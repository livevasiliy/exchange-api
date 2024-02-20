<?php

declare(strict_types=1);

namespace Tests\Feature\app\Http\Controllers\v1;

use Tests\TestCase;

class ExchangeControllerTest extends TestCase
{
    public function test_it_unauthorized_for_rates_request()
    {
        $response = $this->json('GET', '/api/v1/', [
            'method' => 'rates',
            'currency' => 'USD',
        ]);

        $response->assertUnauthorized();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 401,
            'data' => 'No API token provided',
        ]);
    }

    public function test_it_unauthorized_pass_invalid_api_token()
    {
        $response = $this->json('GET', '/api/v1/', [
            'method' => 'rates',
            'currency' => 'USD',
        ], [
            'Authorization' => 'Bearer '.fake()->shuffleString(),
        ]);

        $response->assertUnauthorized();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 401,
            'data' => 'Invalid API token',
        ]);
    }

    public function test_it_failed_pass_invalid_http_method_name()
    {
        $response = $this->json('POST', '/api/v1/', [
            'method' => 'rates',
            'currency' => 'USD',
        ], [
            'Authorization' => 'Bearer '.config('api.token.value'),
        ]);

        $response->assertMethodNotAllowed();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 405,
            'data' => 'No allow HTTP method',
        ]);
    }

    public function test_it_failed_pass_not_exist_currency_name()
    {
        $response = $this->json('GET', '/api/v1/', [
            'method' => 'rates',
            'currency' => 'FOO',
        ], [
            'Authorization' => 'Bearer '.config('api.token.value'),
        ]);

        $response->assertInternalServerError();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 500,
            'data' => 'Not found FOO currency in list of currencies. Check name currency',
        ]);
    }

    public function test_it_successfully_fetch_rates()
    {
        $response = $this->json('GET', '/api/v1/', [
            'method' => 'rates',
            'currency' => 'USD',
        ], [
            'Authorization' => 'Bearer '.config('api.token.value'),
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);
    }

    public function test_it_successfully_convert()
    {
        $response = $this->json('POST', '/api/v1/', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ], [
            'Authorization' => 'Bearer '.config('api.token.value'),
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'status',
            'code',
            'data' => [
                'currency_from',
                'currency_to',
                'value',
                'converted_value',
                'rate_sell',
                'rate_buy',
                'commission_value',
            ],
        ]);
    }

    public function test_it_failed_convert_invalid_http_method()
    {
        $response = $this->json('GET', '/api/v1/', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ], [
            'Authorization' => 'Bearer '.config('api.token.value'),
        ]);

        $response->assertMethodNotAllowed();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);
    }

    public function test_it_unauthorized_for_convert_request()
    {
        $response = $this->json('POST', '/api/v1/', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ]);

        $response->assertUnauthorized();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 401,
            'data' => 'No API token provided',
        ]);
    }

    public function test_it_unauthorized_pass_invalid_api_token_convert_request()
    {
        $response = $this->json('POST', '/api/v1/', [
            'method' => 'convert',
            'currency_from' => 'RUB',
            'currency_to' => 'USD',
            'value' => 100,
        ], [
            'Authorization' => 'Bearer '.fake()->shuffleString(),
        ]);

        $response->assertUnauthorized();

        $response->assertJsonStructure([
            'status',
            'code',
            'data',
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 401,
            'data' => 'Invalid API token',
        ]);
    }

    public function test_it_failed_pass_invalid_params_convert_request()
    {
        $response = $this->json('POST', '/api/v1/', [
            'method' => 'convert',
            'from' => 'RUB',
            'to' => 'USD',
            'value' => -100,
        ], [
            'Authorization' => 'Bearer '.config('api.token.value'),
        ]);

        $response->assertUnprocessable();

        $response->assertJsonStructure([
            'status',
            'code',
            'data' => [
                'errors' => [
                    'currency_from',
                    'currency_to',
                    'value',
                ],
                'message',
            ],
        ]);

        $response->assertExactJson([
            'status' => 'error',
            'code' => 422,
            'data' => [
                'errors' => [
                    'currency_from' => [
                        'The currency from field is required.',
                    ],
                    'currency_to' => [
                        'The currency to field is required.',
                    ],
                    'value' => [
                        'The value field must be greater than or equal to 0.01.',
                    ],
                ],
                'message' => 'The currency from field is required. (and 2 more errors)',
            ],
        ]);
    }
}
