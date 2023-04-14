<?php

namespace  App\Containers\Common\Tests;

use Illuminate\Support\Str;

use App\Containers\Common\Helpers\ContactTypesHelper as Helper;
use App\Containers\Common\Models\ContactType;

use App\Containers\Common\Exceptions\ContactTypeDuplicateNameException;
use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\CreateFailedException;

use App\Helpers\Tests\TestsFacilitator;
use PHPUnit\TextUI\Help;
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
        $types = ContactType::all();
        $result = Helper::all();
        $this->assertEquals($types, $result);

        $name = 'email';
        $type = ContactType::where('name', trim($name))->first();
        $result = Helper::typeName($name);
        $this->assertEquals($type, $result);

        $type = ContactType::first();
        $result = Helper::id($type->id);
        $this->assertEquals($type, $result);
    }

    /**
     * Test fail get type by name.
     *
     * @return void
     */
    public function test_type_fail()
    {
        $this->expectException(NotFoundException::class);
        $name = 'wrong_name';
        $result = Helper::typeName($name);
        $this->assertException($result, 'NotFoundException');

    }

    /**
     * Test fail get type by id.
     *
     * @return void
     */
    public function test_id_fail()
    {
        $this->expectException(NotFoundException::class);
        $result = Helper::id(89465132);
        $this->assertException($result, 'NotFoundException');
    }

    /**
     * Test successful create data.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $data = $this->createData();
        $contactType = Helper::create($data);
        $databaseContactType = ContactType::where('name', $data['name'])->first();
        $this->assertEquals($contactType, $databaseContactType);
    }

    /**
     * Test fail create data.
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
     * Test fail create data.
     *
     * @return void
     */
    public function test_create_duplicate_name_fail()
    {
        $this->expectException(ContactTypeDuplicateNameException::class);
        $contactType = $this->create();
        $data = $this->createData();
        $data['name'] = $contactType->name;
        $result = Helper::create($data);
        $this->assertException($result, 'ContactTypeDuplicateNameException');
    }

    /**
     * Test successful update data.
     *
     * @return void
     */
    public function test_update_contact_type_successful()
    {
        $contactType = $this->create();
        $data = $this->createData();

        $result = Helper::update($contactType, $data);
        $databaseContactType = ContactType::where('name', $data['name'])->first();
        $this->assertEquals($result, $databaseContactType);
        
    }

    /**
     * Test fail update data.
     *
     * @return void
     */
    public function test_update_fail()
    {
        $this->expectException(UpdateFailedException::class);
        $result = Helper::update($this->create(), []);
        $this->assertException($result, 'UpdateFailedException');
    }

    /**
     * Test fail update data.
     *
     * @return void
     */
    public function test_update_duplicate_fail()
    {
        $this->expectException(ContactTypeDuplicateNameException::class);
        $contactType = $this->create();
        $contactType2 = $this->create();
        $data = [
            'name' => $contactType2->name
        ];
        $result = Helper::update($contactType, $data);
        $this->assertException($result, 'ContactTypeDuplicateNameException');
    }
    
}