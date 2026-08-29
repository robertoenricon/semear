{{-- Tela de acesso ao Semear: autentica o usuário e leva ao cadastro. --}}
<x-guest-layout title="Login">
    <section class="login-card" aria-labelledby="login-title">
        <div class="login-card__brand">
            <span class="login-card__logo">🌱</span>
            <div>
                <h1 id="login-title" class="login-card__title">Acesse o Semear</h1>
                <p class="login-card__subtitle">Entre para continuar seus registros.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert login-card__notice" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger login-card__alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="login-form">
            @csrf

            <div>
                <label for="name" class="login-form__label">Nome</label>
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
            </div>

            <x-password-input name="password" label="Senha" autocomplete="current-password" />

            <label class="login-form__remember">
                <input
                    name="remember"
                    type="checkbox"
                    class="form-check-input"
                    value="1"
                >
                Manter conectado
            </label>

            <button type="submit" class="semear-save-btn login-form__submit">
                Entrar
            </button>
        </form>

        <div class="login-card__footer">
            <span class="login-card__footer-text">Ainda não tem uma conta?</span>
            <a href="{{ route('register') }}" class="login-card__secondary-btn">
                Registre-se
            </a>
        </div>
    </section>
</x-guest-layout>
