<?php

namespace  App\Containers\Common\Tests;

use App\Containers\Common\Helpers\RegionsHelper as Helper;
use App\Containers\Common\Models\Region;
use App\Containers\Common\Models\RegionType;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

/**
 * This class will test controller and helper for regions
 */
class RegionsTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * Test successful helper types.
     *
     * @return void
     */
    public function test_types_successful()
    {
        $types = RegionType::all();
        $result = Helper::types();
        $this->assertEquals($types, $result);
    }

    /**
     * Test successful helper allCountries.
     *
     * @return void
     */
    public function test_allCountries_successful()
    {
        $regions = Region::where('type_id', 1)
        ->orderBy('name', 'asc')->get()
        ->each(function(Region $region) {
            $region = $region->load(['states'])->orderBy('name', 'asc');
        });
        $result = Helper::allCountries();
        $this->assertEquals($regions, $result);
    }

    /**
     * Test successful controller all.
     *
     * @return void
     */
    public function test_all_successful()
    {
        $userCreatedWithRaw = $this->createUser();
        $user = $userCreatedWithRaw['user'];
        $content = $this->login(null, $userCreatedWithRaw['userRawData']);
        $response = $this->json(
            'GET',
            '/api/v1/regions',
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $content->token
            ]);

        $response->assertStatus(200)->assertJsonStructure([
            'types',
            'regions'
        ]);
    }
}