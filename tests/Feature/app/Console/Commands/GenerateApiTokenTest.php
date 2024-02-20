<?php

declare(strict_types=1);

namespace Tests\Feature\app\Console\Commands;

use Tests\TestCase;

class GenerateApiTokenTest extends TestCase
{
    public function test_it_generates_api_token_and_updates_env_file()
    {
        $this->artisan('generate:api-token')
            ->expectsQuestion('Enter the length of the API token (between 10 and 64)', 11)
            ->expectsOutput('API_TOKEN generated and updated in .env file:')
            ->assertExitCode(0);

        $this->assertStringContainsString('API_TOKEN=', file_get_contents(base_path('.env')));
    }

    public function test_it_generates_api_token_and_updates_env_file_with_default_value()
    {
        $this->artisan('generate:api-token')
            ->expectsQuestion('Enter the length of the API token (between 10 and 64)', true)
            ->expectsOutput('API_TOKEN generated and updated in .env file:')
            ->assertExitCode(0);

        $this->assertStringContainsString('API_TOKEN=', file_get_contents(base_path('.env')));
    }

    public function test_it_failed_generate_api_token_and_updates_env_file()
    {
        $this->artisan('generate:api-token')
            ->expectsQuestion('Enter the length of the API token (between 10 and 64)', 1)
            ->expectsOutput('Invalid length. Please enter a value between 10 and 64.')
            ->doesntExpectOutput('API_TOKEN generated and updated in .env file:')
            ->assertExitCode(1);
    }
}
