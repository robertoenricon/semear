{{--
    Estrutura base das telas públicas (login e cadastro).

    Centraliza o cabeçalho HTML, os metadados de PWA e o script que alterna
    a visibilidade dos campos de senha, evitando duplicação entre as telas.

    @param string $title Título exibido na aba do navegador.
--}}
@props(['title' => 'Semear'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <!-- Cor da barra de status no mobile: combina com o fundo escuro do app
             para eliminar a faixa branca no topo em iOS/Android. -->
        <meta name="theme-color" content="#0a1014">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <!-- Favicon: emoji 🌱 desenhado via SVG inline (sem arquivo de imagem) -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>%F0%9F%8C%B1</text></svg>">

        <!-- PWA: manifesto e icone do app (instalacao na tela inicial) -->
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
        <meta name="apple-mobile-web-app-title" content="Semear">

        <title>{{ $title }} | {{ config('app.name', 'Terapia') }}</title>

        @vite(['resources/css/app.css'])
    </head>
    <body>
        <main class="login-page">
            {{ $slot }}
        </main>

        {{-- Alterna a visibilidade dos campos de senha ao clicar no botão de olho. --}}
        <script>
            (function () {
                document.querySelectorAll('[data-password-toggle]').forEach(function (toggle) {
                    const input = document.getElementById(toggle.dataset.passwordToggle);

                    if (!input) {
                        return;
                    }

                    toggle.addEventListener('click', function () {
                        const show = input.type === 'password';
                        input.type = show ? 'text' : 'password';
                        toggle.classList.toggle('login-form__toggle--visible', show);
                        toggle.setAttribute('aria-pressed', String(show));
                        toggle.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
                    });
                });
            })();
        </script>
    </body>
</html>
