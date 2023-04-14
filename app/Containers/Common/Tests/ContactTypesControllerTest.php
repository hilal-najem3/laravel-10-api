<?php

namespace  App\Containers\Common\Tests;

use Illuminate\Support\Str;

use App\Containers\Common\Helpers\ContactTypesHelper as Helper;
use App\Containers\Common\Models\ContactType;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

/**
 * This class will test controller and helper for regions
 */
class ContactTypesControllerTest extends TestCase
{
    use TestsFacilitator;

    protected function createData(): array
    {
        return [
            'name' => Str::random(5),
            'allow_duplicates' => random_int(0, 1)
        ];
    }

    protected function create(): ContactType
    {
        $data = $this->createData();
        $contactType = Helper::create($data);
        return $contactType;
    }

    /**
     * Test successful get data.
     *
     * @return void
     */
    public function test_get_successful()
    {
        $content = $this->super_login();
        $token = $content->token;
        $response = $this->json(
            'get',
            '/api/v1/contact_types/get',
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['contact_types']);
        $this->assertEquals($data, Helper::all());

        $response = $this->json(
            'get',
            '/api/v1/contact_types/get/' . 1,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['contact_type']);
        $this->assertEquals($data, Helper::id(1));
    }

    /**
     * Test fail get data.
     *
     * @return void
     */
    public function test_get_fail()
    {
        $content = $this->super_login();
        $token = $content->token;

        $response = $this->json(
            'get',
            '/api/v1/contact_types/get/' . 8456223,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(404);
    }

    /**
     * Test successful create data.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $content = $this->super_login();
        $token = $content->token;

        $data = $this->createData();
        $response = $this->json(
            'POST',
            '/api/v1/contact_types/create',
            $data,
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(200);
        $data = json_encode(json_decode($response->getContent(), true)['contact_type']);
        $this->assertEquals($data, ContactType::orderBy('id', 'DESC')->first());
    }

    /**
     * Test fail create data.
     *
     * @return void
     */
    public function test_create_fail()
    {
        $content = $this->super_login();
        $token = $content->token;

        $data = [];
        $response = $this->json(
            'POST',
            '/api/v1/contact_types/create',
            $data,
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $response->assertStatus(422);
    }
}