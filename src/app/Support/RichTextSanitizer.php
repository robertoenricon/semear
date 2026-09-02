<?php

namespace App\Support;

class RichTextSanitizer
{
    private const ALLOWED_TAGS = '<p><div><br><ul><ol><li><strong><b><em><i><u>';

    /**
     * Sanitiza o HTML simples produzido pelo editor rico.
     */
    public function sanitize(?string $html): string
    {
        $clean = preg_replace(
            '/<\s*(script|style|iframe|object|embed|svg|math|template)\b[^>]*>.*?<\s*\/\s*\1\s*>/is',
            '',
            $html ?? '',
        ) ?? '';

        $clean = strip_tags($clean, self::ALLOWED_TAGS);

        $clean = preg_replace_callback('/<\/?([a-z][a-z0-9]*)\b[^>]*>/i', function (array $matches): string {
            $tag = strtolower($matches[1]);
            $closing = str_starts_with($matches[0], '</') ? '/' : '';

            if ($tag === 'b') {
                $tag = 'strong';
            }

            if ($tag === 'i') {
                $tag = 'em';
            }

            if ($tag === 'br') {
                return '<br>';
            }

            return "<{$closing}{$tag}>";
        }, $clean) ?? '';

        return trim($clean);
    }
}
