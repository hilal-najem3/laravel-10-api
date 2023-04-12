<?php

namespace App\Containers\Agencies\Tests;

use Illuminate\Support\Str;

use App\Containers\Agencies\Helpers\AgencyTypesHelper as Helper;

use App\Containers\Agencies\Models\AgencyType;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

class AgencyTypesControllerTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function createData(): array
    {
        $data = [
            'name' => Str::random(5),
            'description' => Str::random(20)
        ];
        return $data;
    }

    protected function create(): AgencyType
    {
        $data = $this->createData();
        return Helper::create($data);
    }

    /**
     * Test successful get type.
     *
     * @return void
     */
    public function test_get_successful()
    {
        $type = $this->create();
        $content = $this->super_login();
        $token = $content->token;
        $response = $this->json(
            'get',
            '/api/v1/agency_type/get',
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['agency_types']);
        $this->assertEquals($data, Helper::all());

        $response = $this->json(
            'get',
            '/api/v1/agency_type/get/' . $type->id,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['agency_type']);
        $this->assertEquals($data, Helper::id($type->id));
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
            '/api/v1/agency_type/get/' . 63563216532,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(404);
    }

    /**
     * Test successful create type.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $data = $this->createData();
        $content = $this->super_login();
        $token = $content->token;

        $response = $this->json(
            'POST',
            '/api/v1/agency_type/create',
            $data,
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $result = json_encode(json_decode($response->getContent(), true)['agency_type']);
        $agency = json_encode(AgencyType::orderBy('id', 'desc')->first());
        $this->assertEquals($agency, $result);
    }
}