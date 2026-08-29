<?php

namespace Tests\Feature;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Verifica o cadastro de novos usuários e o isolamento dos seus registros.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // As telas usam assets compilados pelo Vite, ausentes no ambiente de teste.
        $this->withoutVite();

        RateLimiter::clear('register|127.0.0.1');
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Criar conta');
    }

    public function test_login_screen_shows_link_to_registration(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Registre-se')
            ->assertSee(route('register'), false);
    }

    public function test_new_user_can_register_and_is_authenticated(): void
    {
        $this->post('/register', [
            'name' => 'maria',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ])
            ->assertRedirect('/semear');

        $user = User::where('name', 'maria')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('senha-segura', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_requires_unique_name(): void
    {
        User::factory()->create(['name' => 'maria']);

        $this->from('/register')->post('/register', [
            'name' => 'maria',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('name');

        $this->assertGuest();
        $this->assertSame(1, User::where('name', 'maria')->count());
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'maria',
            'password' => 'senha-segura',
            'password_confirmation' => 'outra-senha',
        ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['name' => 'maria']);
    }

    public function test_registration_requires_password_with_at_least_eight_characters(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'maria',
            'password' => 'curta',
            'password_confirmation' => 'curta',
        ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_access_registration_screen(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/register')
            ->assertRedirect('/dashboard');
    }

    public function test_entries_created_after_registration_belong_to_the_new_user(): void
    {
        $other = User::factory()->create();
        JournalEntry::factory()->create([
            'user_id' => $other->id,
            'entry_date' => '2026-06-10',
        ]);

        $this->post('/register', [
            'name' => 'maria',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
        ]);

        $newUser = User::where('name', 'maria')->firstOrFail();

        $this->postJson('/api/journal-entries', [
            'entry_date' => '2026-06-11',
            'category' => 'terapia',
            'content' => 'Primeiro registro.',
        ])->assertCreated();

        $this->assertDatabaseHas('journal_entries', [
            'user_id' => $newUser->id,
            'entry_date' => '2026-06-11 00:00:00',
        ]);

        // A listagem devolve apenas os registros do próprio usuário.
        $this->getJson('/api/journal-entries')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.user_id', $newUser->id);
    }
}
