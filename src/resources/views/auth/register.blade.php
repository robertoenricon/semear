{{-- Tela de cadastro: cria a conta do usuário e inicia a sessão. --}}
<x-guest-layout title="Criar conta">
    <section class="login-card" aria-labelledby="register-title">
        <div class="login-card__brand">
            <span class="login-card__logo">🌱</span>
            <div>
                <h1 id="register-title" class="login-card__title">Crie sua conta</h1>
                <p class="login-card__subtitle">Seus registros ficam vinculados apenas a você.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger login-card__alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="login-form">
            @csrf

            <div>
                <label for="name" class="login-form__label">Usuário</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    class="form-control login-form__input @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    autocomplete="username"
                    required
                    autofocus
                >
                <p class="login-form__hint">De 3 a 255 caracteres. É o usuário usado para entrar.</p>
            </div>

            <x-password-input
                name="password"
                label="Senha"
                autocomplete="new-password"
                hint="Use pelo menos 8 caracteres."
            />

            <x-password-input
                name="password_confirmation"
                label="Confirme a senha"
                autocomplete="new-password"
            />

            <button type="submit" class="semear-save-btn login-form__submit">
                Criar conta
            </button>
        </form>

        <div class="login-card__footer">
            <span class="login-card__footer-text">Já tem uma conta?</span>
            <a href="{{ route('login') }}" class="login-card__secondary-btn">
                Entrar
            </a>
        </div>
    </section>
</x-guest-layout>
