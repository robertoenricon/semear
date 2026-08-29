{{--
    Campo de senha com botão de "olho" para mostrar ou ocultar o texto.

    @param string $name         Nome do campo enviado no formulário.
    @param string $label        Rótulo exibido acima do campo.
    @param string $autocomplete Valor do atributo autocomplete do navegador.
    @param bool   $autofocus    Define se o campo recebe o foco ao abrir a tela.
    @param string $hint         Texto auxiliar exibido abaixo do campo.
--}}
@props([
    'name' => 'password',
    'label' => 'Senha',
    'autocomplete' => 'current-password',
    'autofocus' => false,
    'hint' => null,
])
<div>
    <label for="{{ $name }}" class="login-form__label">{{ $label }}</label>
    <div class="login-form__password">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="password"
            class="form-control login-form__input @error($name) is-invalid @enderror"
            autocomplete="{{ $autocomplete }}"
            required
            @if ($autofocus) autofocus @endif
        >
        <button
            type="button"
            class="login-form__toggle"
            data-password-toggle="{{ $name }}"
            aria-label="Mostrar senha"
            aria-pressed="false"
        >
            {{-- Ícone de olho aberto (senha oculta). --}}
            <svg class="login-form__eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            {{-- Ícone de olho cortado (senha visível). --}}
            <svg class="login-form__eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3.5 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                <line x1="2" y1="2" x2="22" y2="22"/>
            </svg>
        </button>
    </div>

    @if ($hint)
        <p class="login-form__hint">{{ $hint }}</p>
    @endif
</div>
