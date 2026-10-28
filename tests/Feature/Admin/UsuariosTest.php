<?php

namespace Tests\Feature\Admin;

use App\Enums\Rol;
use App\Models\Participante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function datos(array $sobrescribir = []): array
    {
        return [
            'name' => 'Marta Gómez',
            'email' => 'marta@example.com',
            'rol' => 'administrador',
            'password' => 'clave-segura-1',
            'password_confirmation' => 'clave-segura-1',
            ...$sobrescribir,
        ];
    }

    public function test_the_administrador_can_only_see_participants(): void
    {
        Participante::factory()->create(['nombre' => 'Ana Torres']);
        $administrador = User::factory()->administrador()->create();

        $this->actingAs($administrador)
            ->get(route('admin.participantes.index'))
            ->assertOk()
            ->assertSeeText('Ana Torres')
            ->assertDontSee(route('admin.ruleta.edit'))
            ->assertDontSee(route('admin.usuarios.index'));

        $this->actingAs($administrador)->get(route('admin.ruleta.edit'))->assertForbidden();
        $this->actingAs($administrador)->put(route('admin.ruleta.update'), [])->assertForbidden();
        $this->actingAs($administrador)->get(route('admin.usuarios.index'))->assertForbidden();
        $this->actingAs($administrador)->get(route('admin.usuarios.create'))->assertForbidden();
        $this->actingAs($administrador)->post(route('admin.usuarios.store'), $this->datos())->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'marta@example.com']);
    }

    public function test_the_super_admin_sees_every_panel_link(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())
            ->get(route('admin.participantes.index'))
            ->assertOk()
            ->assertSee(route('admin.ruleta.edit'))
            ->assertSee(route('admin.usuarios.index'));
    }

    public function test_both_roles_can_log_in(): void
    {
        foreach ([User::factory()->superAdmin()->create(), User::factory()->administrador()->create()] as $usuario) {
            $this->post(route('admin.login'), ['email' => $usuario->email, 'password' => 'password'])
                ->assertRedirect(route('admin.participantes.index'));

            $this->assertAuthenticatedAs($usuario);

            $this->post(route('admin.logout'));
        }
    }

    public function test_guests_cannot_reach_the_users_section(): void
    {
        $this->get(route('admin.usuarios.index'))->assertRedirect(route('admin.login'));
    }

    public function test_the_super_admin_lists_the_panel_users(): void
    {
        User::factory()->administrador()->create(['name' => 'Marta Gómez']);
        User::factory()->create(['name' => 'Sin Acceso']);

        $this->actingAs(User::factory()->superAdmin()->create())
            ->get(route('admin.usuarios.index'))
            ->assertOk()
            ->assertSeeText('Marta Gómez')
            ->assertSeeText('Administrador')
            ->assertDontSeeText('Sin Acceso');
    }

    public function test_the_super_admin_can_create_a_user_who_can_then_log_in(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())
            ->post(route('admin.usuarios.store'), $this->datos(['email' => 'MARTA@Example.com']))
            ->assertRedirect(route('admin.usuarios.index'))
            ->assertSessionHas('estado');

        $marta = User::where('email', 'marta@example.com')->firstOrFail();
        $this->assertSame(Rol::Administrador, $marta->rol);
        $this->assertTrue(Hash::check('clave-segura-1', $marta->password));

        $this->post(route('admin.logout'));
        $this->post(route('admin.login'), ['email' => 'marta@example.com', 'password' => 'clave-segura-1'])
            ->assertRedirect(route('admin.participantes.index'));
        $this->assertAuthenticatedAs($marta);
    }

    public function test_user_creation_is_validated(): void
    {
        $super = User::factory()->superAdmin()->create();
        User::factory()->administrador()->create(['email' => 'marta@example.com']);

        $this->actingAs($super)->post(route('admin.usuarios.store'), $this->datos())
            ->assertSessionHasErrors('email');
        $this->actingAs($super)->post(route('admin.usuarios.store'), $this->datos(['email' => 'otra@example.com', 'rol' => 'dios']))
            ->assertSessionHasErrors('rol');
        $this->actingAs($super)->post(route('admin.usuarios.store'), $this->datos(['email' => 'otra@example.com', 'password' => 'corta', 'password_confirmation' => 'corta']))
            ->assertSessionHasErrors('password');
        $this->actingAs($super)->post(route('admin.usuarios.store'), $this->datos(['email' => 'otra@example.com', 'password_confirmation' => 'distinta']))
            ->assertSessionHasErrors('password');
        $this->actingAs($super)->post(route('admin.usuarios.store'), $this->datos(['email' => 'otra@example.com', 'password' => '', 'password_confirmation' => '']))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'otra@example.com']);
    }

    public function test_the_super_admin_can_edit_a_user_and_keep_the_password(): void
    {
        $marta = User::factory()->administrador()->create(['name' => 'Marta', 'email' => 'marta@example.com']);
        $hashAnterior = $marta->password;

        $this->actingAs(User::factory()->superAdmin()->create())
            ->put(route('admin.usuarios.update', $marta), [
                'name' => 'Marta Gómez',
                'email' => 'marta@example.com',
                'rol' => 'super_admin',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.usuarios.index'));

        $marta->refresh();
        $this->assertSame('Marta Gómez', $marta->name);
        $this->assertSame(Rol::SuperAdmin, $marta->rol);
        $this->assertSame($hashAnterior, $marta->password);
    }

    public function test_the_super_admin_can_change_a_password(): void
    {
        $marta = User::factory()->administrador()->create(['email' => 'marta@example.com']);

        $this->actingAs(User::factory()->superAdmin()->create())
            ->put(route('admin.usuarios.update', $marta), $this->datos(['password' => 'nueva-clave-99', 'password_confirmation' => 'nueva-clave-99']))
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertTrue(Hash::check('nueva-clave-99', $marta->refresh()->password));
    }

    public function test_the_last_super_admin_cannot_be_demoted(): void
    {
        $super = User::factory()->superAdmin()->create(['email' => 'super@example.com']);

        $this->actingAs($super)
            ->put(route('admin.usuarios.update', $super), $this->datos(['email' => 'super@example.com', 'rol' => 'administrador']))
            ->assertSessionHasErrors('rol');

        $this->assertSame(Rol::SuperAdmin, $super->refresh()->rol);
    }

    public function test_a_super_admin_can_be_demoted_when_another_one_exists(): void
    {
        $super = User::factory()->superAdmin()->create(['email' => 'super@example.com']);
        $otro = User::factory()->superAdmin()->create();

        $this->actingAs($otro)
            ->put(route('admin.usuarios.update', $super), $this->datos(['email' => 'super@example.com', 'rol' => 'administrador']))
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertSame(Rol::Administrador, $super->refresh()->rol);
    }

    public function test_the_super_admin_can_delete_other_users_but_not_themselves(): void
    {
        $super = User::factory()->superAdmin()->create();
        $marta = User::factory()->administrador()->create();

        $this->actingAs($super)
            ->delete(route('admin.usuarios.destroy', $super))
            ->assertSessionHas('error');
        $this->assertModelExists($super);

        $this->actingAs($super)
            ->delete(route('admin.usuarios.destroy', $marta))
            ->assertRedirect(route('admin.usuarios.index'));
        $this->assertModelMissing($marta);
    }

    public function test_a_user_who_loses_the_role_can_no_longer_enter(): void
    {
        $marta = User::factory()->administrador()->create();
        $this->actingAs($marta)->get(route('admin.participantes.index'))->assertOk();

        $marta->forceFill(['rol' => null])->save();

        $this->actingAs($marta->fresh())->get(route('admin.participantes.index'))->assertForbidden();
    }
}
