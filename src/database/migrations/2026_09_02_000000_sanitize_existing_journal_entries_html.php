<?php

use App\Support\RichTextSanitizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sanitiza entradas antigas gravadas antes da limpeza server-side.
     */
    public function up(): void
    {
        $sanitizer = new RichTextSanitizer();

        DB::table('journal_entries')
            ->select(['id', 'content', 'feedback'])
            ->chunkById(100, function ($entries) use ($sanitizer): void {
                foreach ($entries as $entry) {
                    $content = $sanitizer->sanitize($entry->content);
                    $feedback = $entry->feedback !== null
                        ? $sanitizer->sanitize($entry->feedback)
                        : null;

                    if ($content === $entry->content && $feedback === $entry->feedback) {
                        continue;
                    }

                    DB::table('journal_entries')
                        ->where('id', $entry->id)
                        ->update([
                            'content' => $content,
                            'feedback' => $feedback,
                        ]);
                }
            });
    }

    /**
     * A sanitizacao remove conteudo inseguro de forma intencional.
     */
    public function down(): void
    {
        //
    }
};
