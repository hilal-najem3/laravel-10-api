<?php

namespace App\Containers\Agencies\Tests;

use Illuminate\Support\Str;

use App\Containers\Agencies\Helpers\AgencyHelper as Helper;

use App\Containers\Agencies\Models\AgencyType;
use App\Containers\Agencies\Models\Agency;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

class AgencyControllerTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function createData(): array
    {
        if(AgencyType::all()->count() == 0) {
            AgencyType::create([
                'name' => Str::random(5)
            ]);
        }
        $data = [
            'name' => Str::random(5),
            'username' => Str::random(20),
            'bio' => Str::random(20),
            'type_id' => 1
        ];
        return $data;
    }

    protected function create(): Agency
    {
        $data = $this->createData();
        return Helper::create($data);
    }

    /**
     * Test successful get.
     *
     * @return void
     */
    public function test_get_successful()
    {
        $agency = $this->create();
        $content = $this->super_login();
        $token = $content->token;
        $response = $this->json(
            'get',
            '/api/v1/agency/get',
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['agencies']);
        $this->assertEquals($data, Helper::all());

        $response = $this->json(
            'get',
            '/api/v1/agency/get/' . $agency->id,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['agency']);
        $this->assertEquals($data, Helper::full($agency->id));
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
            '/api/v1/agency/get/' . 63563216532,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(404);
    }

    /**
     * Test successful create.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $parentAgency = $this->create();
        $agencyData = $this->createData();
        $agencyData['agency_id'] = $parentAgency->id;
        $content = $this->super_login();
        $token = $content->token;
        $response = $this->json(
            'POST',
            '/api/v1/agency/create',
            $agencyData,
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['agency']);
        $agency = json_encode(Agency::orderBy('id', 'desc')->first());
        $this->assertEquals($data, $agency);
    }

    /**
     * Test update.
     *
     * @return void
     */
    public function test_update()
    {
        $content = $this->super_login();
        $token = $content->token;

        $oldAgency = $this->create();
        $agency = $this->create();

        $agencyData = $this->createData();
        $response = $this->json(
            'PUT',
            '/api/v1/agency/update/' . $agency->id,
            $agencyData,
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);

        $agencyData = $this->createData();
        $agencyData['username'] = $oldAgency->username;
        $response = $this->json(
            'PUT',
            '/api/v1/agency/update/' . $agency->id,
            $agencyData,
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(400);
    }
}