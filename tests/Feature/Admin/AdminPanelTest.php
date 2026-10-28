<?php

namespace Tests\Feature\Admin;

use App\Models\Participante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_admin_login(): void
    {
        $this->get(route('admin.participantes.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_regular_users_cannot_see_the_participants(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.participantes.index'))
            ->assertForbidden();
    }

    public function test_admin_can_log_in(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('admin.login'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.participantes.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_regular_users_cannot_log_in_to_the_panel(): void
    {
        $user = User::factory()->create();

        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('admin.login'), ['email' => $admin->email, 'password' => 'incorrecta'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_sees_the_registered_participants(): void
    {
        Participante::factory()->create(['nombre' => 'Ana Torres', 'cedula' => '1020304050']);
        Participante::factory()->create(['nombre' => 'Luis Mora', 'cedula' => '9988776655']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.participantes.index'))
            ->assertOk()
            ->assertSeeText('Ana Torres')
            ->assertSeeText('Luis Mora');
    }

    public function test_admin_can_search_participants(): void
    {
        Participante::factory()->create(['nombre' => 'Ana Torres', 'cedula' => '1020304050']);
        Participante::factory()->create(['nombre' => 'Luis Mora', 'cedula' => '9988776655']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.participantes.index', ['q' => '9988']))
            ->assertOk()
            ->assertSeeText('Luis Mora')
            ->assertDontSeeText('Ana Torres');
    }

    public function test_admin_can_log_out(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }
}
