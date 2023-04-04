<?php

namespace  App\Containers\Common\Tests;

use App\Containers\Common\Helpers\ContactHelper as Helper;
use App\Containers\Common\Models\Contact;
use App\Containers\Common\Models\ContactType;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

/**
 * This class will test controller and helper for regions
 */
class ContactsTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * Test successful get all types.
     *
     * @return void
     */
    public function test_types_successful()
    {
        $types = ContactType::all();
        $result = Helper::types();
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
        $types = ContactType::where('name', trim($name))->first();
        $result = Helper::type($name);
        $this->assertEquals($types, $result);
    }

    /**
     * Test successful createContact.
     *
     * @return void
     */
    public function test_createContact_successful()
    {
        $user = $this->createUser()['user'];
        $type = ContactType::where('name', 'email')->first();
        $email = 'email@example.com';

        $data = [
            'value' => $email,
            'type_id' => $type->id
        ];
        $userId = $user->id;
        $targetTag = 'users';

        $result = Helper::createContact($data, $targetTag, $userId);

        $lastInsertId = Contact::orderBy('created_at', 'desc')->first()->id;
        $contact = Helper::id($lastInsertId);

        $this->assertEquals($contact, $result);
    }
}