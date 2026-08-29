<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Gerencia o cadastro de novos usuários do Semear.
 *
 * Cada conta criada é independente: os registros lançados pelo usuário
 * ficam vinculados ao seu identificador e não são visíveis para os demais.
 */
class RegisteredUserController extends Controller
{
    /** Quantidade máxima de cadastros permitidos por IP na janela de tempo. */
    private const MAX_ATTEMPTS = 5;

    /** Duração, em segundos, da janela de bloqueio de novos cadastros. */
    private const DECAY_SECONDS = 3600;

    /**
     * Exibe o formulário de criação de conta.
     *
     * @return View Tela de cadastro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Cria a conta com o nome e a senha informados e autentica o usuário.
     *
     * O nome é único no sistema, pois é usado como identificador de login.
     * Após o cadastro, a sessão é iniciada e o usuário segue direto para o app.
     *
     * @param  Request $request Requisição com nome, senha e confirmação.
     * @return RedirectResponse  Redirecionamento para a tela principal.
     *
     * @throws ValidationException Quando os dados são inválidos ou o limite
     *                             de cadastros por IP é atingido.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:users,name'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Informe um nome.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'name.unique' => 'Este nome já está em uso. Escolha outro.',
            'password.required' => 'Informe uma senha.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ]);

        RateLimiter::hit($this->throttleKey($request), self::DECAY_SECONDS);

        $user = User::create([
            'name' => $data['name'],
            'password' => $data['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('semear');
    }

    /**
     * Bloqueia o cadastro quando há muitas tentativas vindas do mesmo IP.
     *
     * @param  Request $request Requisição de cadastro.
     *
     * @throws ValidationException Quando o limite de tentativas é atingido.
     */
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'name' => "Muitas contas criadas a partir deste dispositivo. Tente novamente em {$seconds} segundos.",
        ]);
    }

    /**
     * Monta a chave usada para contar as tentativas de cadastro por IP.
     *
     * @param  Request $request Requisição de cadastro.
     * @return string           Chave do limitador de tentativas.
     */
    private function throttleKey(Request $request): string
    {
        return 'register|'.$request->ip();
    }
}
