<?php

namespace App\Containers\Agencies\Tests;

use Illuminate\Support\Str;

use App\Containers\Agencies\Exceptions\AgencyTypeDuplicateNameException;
use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;

use App\Containers\Agencies\Helpers\AgencyTypesHelper as Helper;

use App\Containers\Agencies\Models\AgencyType;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

class AgencyTypesHelper extends TestCase
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
     * Test successful all type.
     *
     * @return void
     */
    public function test_all_successful()
    {
        $result = Helper::all();
        $types = AgencyType::all();
        $this->assertEquals($result, $types);
    }

    /**
     * Test successful id type.
     *
     * @return void
     */
    public function test_id_successful()
    {
        $agency = $this->create();
        $result = Helper::id($agency->id);
        $agency = AgencyType::find($agency->id);
        $this->assertEquals($result, $agency);
    }

    /**
     * Test fail id.
     *
     * @return void
     */
    public function test_id_fail()
    {
        $this->expectException(NotFoundException::class);

        $result = Helper::id(53123215);

        $this->assertException($result, 'NotFoundException');
    }

    /**
     * Test successful create type.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $data = $this->createData();
        $result = Helper::create($data);
        $this->assertEquals(isset($result->id), true);
        $id = AgencyType::find($result->id)->id;
        $agency = Helper::id($id);

        $this->assertEquals($result, $agency);
    }

    /**
     * Test fail duplicate name create type.
     *
     * @return void
     */
    public function test_create_duplicate_fail()
    {
        $this->expectException(AgencyTypeDuplicateNameException::class);
        $data = $this->createData();
        $agency = Helper::create($data);

        $result = Helper::create($data);
        $this->assertException($result, 'AgencyTypeDuplicateNameException');
    }

    /**
     * Test fail name create type.
     *
     * @return void
     */
    public function test_create__fail()
    {
        $this->expectException(CreateFailedException::class);
        $result = Helper::create([]);
        $this->assertException($result, 'CreateFailedException');
    }

    /**
     * Test successful update type.
     *
     * @return void
     */
    public function test_update_successful()
    {
        $agency = $this->create();
        $data = $this->createData();
        unset($data['description']);

        $result = Helper::update($agency, $data);
        $this->assertEquals($result, Helper::id($agency->id));
        $this->assertEquals($result->description, '');
        $this->assertEquals($result->name, $data['name']);
    }

    /**
     * Test fail update type duplicate.
     *
     * @return void
     */
    public function test_update_duplicate_fail()
    {
        $this->expectException(AgencyTypeDuplicateNameException::class);
        $agencyType = $this->create();
        $anotherAgencyType = $this->create();

        $data = [
            'name' => $anotherAgencyType->name,
        ];

        $result = Helper::update($agencyType, $data);
        $this->assertException($result, 'AgencyTypeDuplicateNameException');
    }

    /**
     * Test fail update type.
     *
     * @return void
     */
    public function test_update_fail()
    {
        $this->expectException(UpdateFailedException::class);
        $agencyType = $this->create();
        $result = Helper::update($agencyType, []);
        $this->assertException($result, 'UpdateFailedException');
    }
}