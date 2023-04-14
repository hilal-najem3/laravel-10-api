<?php

namespace  App\Containers\Common\Tests;

use App\Containers\Common\Helpers\ContactTypesHelper as Helper;
use App\Containers\Common\Models\ContactType;

use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\CreateFailedException;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

/**
 * This class will test controller and helper for regions
 */
class ContactsTypesHelperTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * Test successful get all.
     *
     * @return void
     */
    public function test_all_successful()
    {
        $types = ContactType::all();
        $result = Helper::all();
        $this->assertEquals($types, $result);
    }

    /**
     * Test successful get type by name.
     *
     * @return void
     */
    public function test_type_successful()
    {
        $name = 'email';
        $type = ContactType::where('name', trim($name))->first();
        $result = Helper::typeName($name);
        $this->assertEquals($type, $result);
    }

    /**
     * Test successful get type by id.
     *
     * @return void
     */
    public function test_id_successful()
    {
        $type = ContactType::first();
        $result = Helper::id($type->id);
        $this->assertEquals($type, $result);
    }
    
}