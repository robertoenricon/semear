<?php

namespace Database\Seeders;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminName = trim((string) config('admin.name', ''));

        if ($adminName === '') {
            throw new RuntimeException('ADMIN_NAME precisa estar definido antes de executar o seeder.');
        }

        $admin = User::firstOrNew([
            'name' => $adminName,
        ]);

        $shouldSetPassword = ! $admin->exists || (bool) config('admin.reset_password');

        if ($shouldSetPassword) {
            $admin->password = $this->safeAdminPassword();
        }

        $admin->save();

        JournalEntry::query()
            ->whereNull('user_id')
            ->update(['user_id' => $admin->id]);
    }

    private function safeAdminPassword(): string
    {
        $password = trim((string) config('admin.password', ''));

        if ($password === '') {
            throw new RuntimeException('ADMIN_PASSWORD precisa estar definido antes de criar ou redefinir o administrador.');
        }

        $unsafePasswords = [
            'admin',
            'alterar',
            'alterar_senha_forte',
            'change-me-now',
            'pass',
            'password',
            'root',
            'secret',
        ];

        if (strlen($password) < 12 || in_array(strtolower($password), $unsafePasswords, true)) {
            throw new RuntimeException('ADMIN_PASSWORD precisa ter pelo menos 12 caracteres e nao pode ser uma senha padrao.');
        }

        return $password;
    }
}
