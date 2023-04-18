<?php

namespace App\Containers\Currencies\Tests;

use App\Containers\Currencies\Helpers\CurrenciesHelper as Helper;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

class CurrenciesControllerTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }
    /**
     * Test successful get.
     *
     * @return void
     */
    public function test_get_successful()
    {
        $content = $this->super_login();
        $token = $content->token;
        $response = $this->json(
            'get',
            '/api/v1/currencies/get',
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['currencies']);
        $this->assertEquals($data, Helper::all());

        $response = $this->json(
            'get',
            '/api/v1/currencies/get/61',
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['currency']);
        $this->assertEquals($data, Helper::id(61));
    }

    /**
     * Test fail get type.
     *
     * @return void
     */
    public function test_get_fail()
    {
        $content = $this->super_login();
        $token = $content->token;
        $response = $this->json(
            'get',
            '/api/v1/currencies/get/' . 63563216532,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(404);
    }
}