<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixLegacyPasswordHashes extends Command
{
    protected $signature = 'users:fix-legacy-passwords {--default=123456789 : Default password for legacy MD5 accounts}';

    protected $description = 'Re-hash user passwords stored as MD5 or plain text to Bcrypt';

    public function handle(): int
    {
        $defaultPassword = (string) $this->option('default');
        $fixed = 0;

        User::query()->each(function (User $user) use ($defaultPassword, &$fixed) {
            if (preg_match('/^\$2[ayb]\$.{56}$/', $user->password) === 1) {
                return;
            }

            $user->forceFill(['password' => $defaultPassword])->save();
            $fixed++;
            $this->line("Fixed: {$user->email}");
        });

        $this->info("Done. Re-hashed {$fixed} user(s).");

        return self::SUCCESS;
    }
}
