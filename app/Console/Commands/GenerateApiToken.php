<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateApiToken extends Command
{
    private const MAX_LENGTH_API_TOKEN = 64;

    private const MIN_LENGTH_API_TOKEN = 10;

    protected $signature = 'generate:api-token';

    protected $description = 'Generate a random API token and update .env file';

    public function handle(): int
    {
        $length = $this->ask('Enter the length of the API token (between 10 and 64)', self::MAX_LENGTH_API_TOKEN);

        if ($length < self::MIN_LENGTH_API_TOKEN || $length > self::MAX_LENGTH_API_TOKEN) {
            $this->error('Invalid length. Please enter a value between 10 and 64.');

            return Command::FAILURE;
        }

        $token = Str::random($length);

        // Update .env file
        $envFilePath = base_path('.env');
        $this->updateEnvFile($envFilePath, (int) $length, $token);

        $this->info('API_TOKEN generated and updated in .env file:');
        $this->line('API_TOKEN='.$token);

        return Command::SUCCESS;
    }

    private function updateEnvFile(string $filePath, int $length, string $token)
    {
        File::put($filePath, Str::replace(
            'API_TOKEN_LENGTH='.env('API_TOKEN_LENGTH'),
            "API_TOKEN_LENGTH=$length",
            File::get($filePath)
        ));

        File::put($filePath, Str::replace(
            'API_TOKEN='.env('API_TOKEN'),
            "API_TOKEN=$token",
            File::get($filePath)
        ));

        // Clear the configuration cache
        Artisan::call('config:clear');
    }
}
