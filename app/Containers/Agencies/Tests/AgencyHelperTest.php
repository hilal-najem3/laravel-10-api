<?php

namespace App\Containers\Agencies\Tests;

use App\Helpers\Storage\StoreHelper;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Str;

use App\Containers\Agencies\Exceptions\AgencyDuplicateUserNameException;
use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;

use App\Containers\Agencies\Helpers\AgencyHelper as Helper;

use App\Containers\Agencies\Models\AgencyType;
use App\Containers\Agencies\Models\Agency;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

class AgencyHelperTest extends TestCase
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
     * Test successful all.
     *
     * @return void
     */
    public function test_all_successful()
    {
        $this->create();
        $result = Helper::all();
        $agencies = Agency::with(['type', 'parent_agency', 'logo'])
        ->get()->each(function (Agency $agency) {
            if($agency->logo) {
                $agency->logo->link = StoreHelper::getFileLink($agency->logo->k);
            }
        });;
        $this->assertEquals($result, $agencies);
    }

    /**
     * Test successful id.
     *
     * @return void
     */
    public function test_id_successful()
    {
        $agency = $this->create();
        $result = Helper::id($agency->id);
        $agency = Agency::find($agency->id);
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
     * Test successful create.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $data = $this->createData();
        $result = Helper::create($data);
        $this->assertEquals(isset($result->id), true);
        $id = Agency::find($result->id)->id;
        $agency = Helper::id($id);
        $this->assertEquals($agency->is_main, true);
        $this->assertEquals($result, $agency);

        $data = $this->createData();
        $data['agency_id'] = $agency->id;
        $newAgency = Helper::create($data);
        $this->assertEquals(isset($newAgency->id), true);
        $agency = Helper::id($newAgency->id);
        $this->assertEquals($newAgency, $agency);
        $this->assertEquals($newAgency->agency_id, $agency->agency_id);
        $this->assertEquals($newAgency->is_main, false);
    }

    /**
     * Test fail duplicate name create.
     *
     * @return void
     */
    public function test_create_duplicate_fail()
    {
        $this->expectException(AgencyDuplicateUserNameException::class);
        $data = $this->createData();
        $agency = Helper::create($data);

        $result = Helper::create($data);
        $this->assertException($result, 'AgencyDuplicateUserNameException');
    }

    /**
     * Test fail create.
     *
     * @return void
     */
    public function test_create_fail()
    {
        $this->expectException(CreateFailedException::class);
        $result = Helper::create([]);
        $this->assertException($result, 'CreateFailedException');
    }

    /**
     * Test successful update.
     *
     * @return void
     */
    public function test_update_successful()
    {
        $agency = $this->create();
        $data = $this->createData();
        unset($data['bio']);
        $data['active'] = false;

        $result = Helper::update($agency, $data);
        $this->assertEquals($result, Helper::id($agency->id));
        $this->assertEquals($result->bio, '');
        $this->assertEquals($result->active, false);
        $this->assertEquals($result->name, $data['name']);
    }

    /**
     * Test fail update duplicate.
     *
     * @return void
     */
    public function test_update_duplicate_fail()
    {
        $this->expectException(AgencyDuplicateUserNameException::class);
        $data = $this->createData();
        $agency = Helper::create($data);
        $anotherAgency = $this->create();

        $data['username'] = $anotherAgency->username;

        $result = Helper::update($agency, $data);
        $this->assertException($result, 'AgencyDuplicateUserNameException');
    }

    /**
     * Test fail update.
     *
     * @return void
     */
    public function test_update_fail()
    {
        $this->expectException(UpdateFailedException::class);
        $agency = $this->create();
        $result = Helper::update($agency, []);
        $this->assertException($result, 'UpdateFailedException');
    }

    /**
     * Test update logo photo successful.
     *
     * @return void
     */
    public function test_update_logo_photo_successful()
    {
        $image = File::image('image.png', 400, 100);
        $agency = $this->create();

        // Result should be the newly created image model
        $result = Helper::updateLogo($agency, $image);
        $agency = Helper::id($agency->id);
        $this->assertEquals($agency->logo_id, $result->id);

        $result = Helper::updateLogo($agency, null);
        $agency = Helper::id($agency->id);
        $this->assertEquals($agency->logo_id, null);
    }
}