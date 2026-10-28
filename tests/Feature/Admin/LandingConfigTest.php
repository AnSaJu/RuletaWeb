<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\LandingPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingConfigTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function datos(array $sobrescribir = []): array
    {
        return [
            'meta_titulo' => 'Gran sorteo',
            'nombre_principal' => 'Gran Sorteo SAS',
            'insignia' => '🎁 Premios todo el mes',
            'titulo' => 'Gana con *nuestra* ruleta',
            'descripcion' => 'Regístrate y gira.',
            'beneficios' => "Premios reales\nSin costo",
            'formulario_titulo' => 'Inscríbete',
            'formulario_subtitulo' => 'Solo toma un minuto',
            'boton_texto' => 'Entrar al sorteo',
            'color_fondo' => '#FFFFFF',
            'color_texto' => '#111111',
            'color_acento_1' => '#ff0000',
            'color_acento_2' => '#0000ff',
            ...$sobrescribir,
        ];
    }

    private function superAdmin(): User
    {
        return User::factory()->superAdmin()->create();
    }

    public function test_the_landing_uses_the_default_content_until_it_is_edited(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Regístrate y')
            ->assertSee('Quiero participar')
            ->assertSee('--lp-fondo: #020617', false);
    }

    public function test_only_the_super_admin_can_open_the_editor(): void
    {
        $this->get(route('admin.landing.edit'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->administrador()->create())
            ->get(route('admin.landing.edit'))
            ->assertForbidden();

        $this->actingAs($this->superAdmin())
            ->get(route('admin.landing.edit'))
            ->assertOk()
            ->assertSee('Quiero participar');
    }

    public function test_only_the_super_admin_can_change_or_restore_the_landing(): void
    {
        $administrador = User::factory()->administrador()->create();

        $this->put(route('admin.landing.update'), $this->datos())->assertRedirect(route('admin.login'));
        $this->actingAs($administrador)->put(route('admin.landing.update'), $this->datos())->assertForbidden();
        $this->actingAs($administrador)->delete(route('admin.landing.destroy'))->assertForbidden();

        $this->assertSame('Quiero participar', LandingPage::valores()['boton_texto']);
    }

    public function test_the_super_admin_can_edit_texts_and_colors(): void
    {
        $this->actingAs($this->superAdmin())
            ->put(route('admin.landing.update'), $this->datos())
            ->assertRedirect(route('admin.landing.edit'))
            ->assertSessionHas('guardado');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Gran sorteo</title>', false)
            ->assertSeeText('Gran Sorteo SAS')
            ->assertSeeText('🎁 Premios todo el mes')
            ->assertSeeText('Gana con')
            ->assertSee('<span class="tema-texto-degradado">nuestra</span>', false)
            ->assertSeeText('Regístrate y gira.')
            ->assertSeeText('Premios reales')
            ->assertSeeText('Sin costo')
            ->assertSeeText('Inscríbete')
            ->assertSeeText('Entrar al sorteo')
            ->assertSee('--lp-fondo: #ffffff', false)
            ->assertSee('--lp-acento-1: #ff0000', false)
            ->assertDontSee('Quiero participar');
    }

    public function test_the_roulette_page_uses_the_colors_too(): void
    {
        $this->actingAs($this->superAdmin())->put(route('admin.landing.update'), $this->datos());
        auth()->logout();

        $this->post(route('participantes.store'), [
            'nombre' => 'Ana Torres',
            'cedula' => '1020304050',
            'direccion' => 'Calle 1 # 2-3',
            'celular' => '3001234567',
        ]);

        $this->get(route('ruleta.show'))
            ->assertOk()
            ->assertSee('--lp-fondo: #ffffff', false)
            ->assertSee('--lp-acento-2: #0000ff', false);
    }

    public function test_optional_fields_can_be_left_empty_and_are_hidden(): void
    {
        $this->actingAs($this->superAdmin())
            ->put(route('admin.landing.update'), $this->datos([
                'insignia' => null,
                'descripcion' => null,
                'beneficios' => null,
                'formulario_subtitulo' => null,
            ]))
            ->assertSessionHasNoErrors();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('tema-insignia')
            ->assertDontSee('<ul', false)
            ->assertSeeText('Gana con');
    }

    public function test_the_text_entered_by_the_admin_is_escaped(): void
    {
        $this->actingAs($this->superAdmin())
            ->put(route('admin.landing.update'), $this->datos(['titulo' => '<script>alert(1)</script> *hola*']));

        $this->get(route('home'))
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_invalid_data_is_rejected_and_nothing_changes(): void
    {
        $admin = $this->superAdmin();

        foreach ([
            ['titulo' => ''],
            ['boton_texto' => ''],
            ['meta_titulo' => ''],
            ['titulo' => str_repeat('a', 121)],
            ['descripcion' => str_repeat('a', 301)],
            ['color_fondo' => 'rojo'],
            ['color_texto' => '#fff'],
            ['color_acento_1' => 'url(javascript:alert(1))'],
            ['color_acento_2' => ''],
            ['beneficios' => "1\n2\n3\n4\n5\n6\n7"],
            ['beneficios' => str_repeat('a', 101)],
        ] as $invalido) {
            $campo = array_key_first($invalido);

            $this->actingAs($admin)
                ->put(route('admin.landing.update'), $this->datos($invalido))
                ->assertSessionHasErrors($campo);
        }

        $this->assertSame('Quiero participar', LandingPage::valores()['boton_texto']);
    }

    public function test_the_original_design_can_be_restored(): void
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos());
        $this->assertSame('Entrar al sorteo', LandingPage::valores()['boton_texto']);

        $this->actingAs($admin)
            ->delete(route('admin.landing.destroy'))
            ->assertRedirect(route('admin.landing.edit'));

        $this->assertSame(LandingPage::predeterminados(), LandingPage::valores());
    }

    public function test_the_landing_shows_the_main_name_above_the_badge(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, 'tema-insignia'),
            strpos($html, 'tracking-tight sm:text-3xl">'.config('app.name')),
        );
    }

    public function test_the_brand_block_is_hidden_without_name_and_logo(): void
    {
        $this->actingAs($this->superAdmin())
            ->put(route('admin.landing.update'), $this->datos(['nombre_principal' => null]));

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Gran Sorteo SAS')
            ->assertDontSee('sm:text-3xl', false)
            ->assertDontSee('max-w-[10rem]', false);
    }

    public function test_a_logo_can_be_uploaded_and_is_shown_above_the_badge(): void
    {
        Storage::fake(LandingPage::DISCO);
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('logo.png', 300, 120)]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.landing.edit'));

        $ruta = LandingPage::valores()['logo'];
        $this->assertStringStartsWith('logos/', $ruta);
        Storage::disk(LandingPage::DISCO)->assertExists($ruta);

        $html = $this->get(route('home'))->assertOk()->getContent();
        $this->assertStringContainsString('uploads/'.$ruta, $html);
        $this->assertLessThan(strpos($html, 'tema-insignia'), strpos($html, 'uploads/'.$ruta));

        $this->actingAs($admin)->get(route('admin.landing.edit'))
            ->assertOk()
            ->assertSee('uploads/'.$ruta);
    }

    public function test_saving_without_a_file_keeps_the_current_logo(): void
    {
        Storage::fake(LandingPage::DISCO);
        $admin = $this->superAdmin();

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('logo.png')]));
        $ruta = LandingPage::valores()['logo'];

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['titulo' => 'Otro título']));

        $this->assertSame($ruta, LandingPage::valores()['logo']);
        Storage::disk(LandingPage::DISCO)->assertExists($ruta);
    }

    public function test_a_new_logo_replaces_and_deletes_the_old_one(): void
    {
        Storage::fake(LandingPage::DISCO);
        $admin = $this->superAdmin();

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('uno.png')]));
        $anterior = LandingPage::valores()['logo'];

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('dos.jpg')]));
        $nuevo = LandingPage::valores()['logo'];

        $this->assertNotSame($anterior, $nuevo);
        Storage::disk(LandingPage::DISCO)->assertMissing($anterior);
        Storage::disk(LandingPage::DISCO)->assertExists($nuevo);
    }

    public function test_the_logo_can_be_removed(): void
    {
        Storage::fake(LandingPage::DISCO);
        $admin = $this->superAdmin();

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('logo.png')]));
        $ruta = LandingPage::valores()['logo'];

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['quitar_logo' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertSame('', LandingPage::valores()['logo']);
        Storage::disk(LandingPage::DISCO)->assertMissing($ruta);
        $this->get(route('home'))->assertDontSee('max-w-[10rem]', false);
    }

    public function test_only_png_jpg_and_webp_images_up_to_two_megabytes_are_accepted(): void
    {
        Storage::fake(LandingPage::DISCO);
        $admin = $this->superAdmin();

        foreach ([
            UploadedFile::fake()->create('logo.svg', 5, 'image/svg+xml'),
            UploadedFile::fake()->create('logo.pdf', 5, 'application/pdf'),
            UploadedFile::fake()->create('logo.php', 5, 'application/x-php'),
            UploadedFile::fake()->image('enorme.png')->size(3000),
        ] as $archivo) {
            $this->actingAs($admin)
                ->put(route('admin.landing.update'), $this->datos(['logo' => $archivo]))
                ->assertSessionHasErrors('logo');
        }

        $this->assertSame('', LandingPage::valores()['logo']);
        $this->assertSame([], Storage::disk(LandingPage::DISCO)->allFiles());
    }

    public function test_restoring_the_original_design_deletes_the_logo_file(): void
    {
        Storage::fake(LandingPage::DISCO);
        $admin = $this->superAdmin();

        $this->actingAs($admin)->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('logo.png')]));
        $ruta = LandingPage::valores()['logo'];

        $this->actingAs($admin)->delete(route('admin.landing.destroy'));

        Storage::disk(LandingPage::DISCO)->assertMissing($ruta);
        $this->assertSame('', LandingPage::valores()['logo']);
    }

    public function test_only_the_super_admin_can_upload_a_logo(): void
    {
        Storage::fake(LandingPage::DISCO);

        $this->actingAs(User::factory()->administrador()->create())
            ->put(route('admin.landing.update'), $this->datos(['logo' => UploadedFile::fake()->image('logo.png')]))
            ->assertForbidden();

        $this->assertSame([], Storage::disk(LandingPage::DISCO)->allFiles());
    }

    public function test_line_breaks_from_the_browser_are_stored_as_lf(): void
    {
        $this->actingAs($this->superAdmin())
            ->put(route('admin.landing.update'), $this->datos(['beneficios' => "Uno\r\nDos"]));

        $this->assertSame("Uno\nDos", LandingPage::valores()['beneficios']);
    }

    public function test_the_title_segments_mark_the_highlighted_words(): void
    {
        $this->assertSame([
            ['texto' => 'Regístrate y ', 'resaltado' => false],
            ['texto' => 'participa', 'resaltado' => true],
            ['texto' => ' ya', 'resaltado' => false],
        ], LandingPage::segmentosDelTitulo('Regístrate y *participa* ya'));

        $this->assertSame([['texto' => 'Sin resaltar', 'resaltado' => false]], LandingPage::segmentosDelTitulo('Sin resaltar'));
    }

    public function test_the_button_text_color_follows_the_accent_brightness(): void
    {
        $this->assertSame('#0f172a', LandingPage::colorTextoBoton('#fbbf24', '#d946ef'));
        $this->assertSame('#ffffff', LandingPage::colorTextoBoton('#1e3a8a', '#4c1d95'));
    }
}
