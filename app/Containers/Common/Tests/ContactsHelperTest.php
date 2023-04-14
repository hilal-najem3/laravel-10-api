<?php

namespace  App\Containers\Common\Tests;

use App\Containers\Common\Helpers\ContactHelper as Helper;
use App\Containers\Common\Models\Contact;
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
class ContactsHelperTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function createContact()
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
        $result = Helper::create($data, $targetTag, $userId);

        return $result;
    }

    /**
     * Test successful get_contact_id.
     *
     * @return void
     */
    public function test_get_contact_id_successful()
    {
        $contact = $this->createContact();
        $result = Helper::id($contact->id);
        $contact = Contact::find($contact->id)->load(['users', 'type']);
        $this->assertEquals($contact, $result);
    }

    /**
     * Test fail get_contact_id.
     *
     * @return void
     */
    public function test_get_contact_id_fail()
    {
        $this->expectException(NotFoundException::class);
        $result = Helper::id(84524521); // not found id
        $this->assertException($result, 'NotFoundException');
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
        $result = Helper::create($data, $targetTag, $userId);

        $contactId = $user->contact()->get()->first()->id;
        $contact = Helper::id($contactId);

        $this->assertEquals($contact, $result);
    }

    /**
     * Test fail createContact.
     *
     * @return void
     */
    public function test_createContact_fail()
    {
        $this->expectException(CreateFailedException::class);

        $user = $this->createUser()['user'];
        $type = ContactType::where('name', 'email')->first();
        $email = 'email@example.com';

        $data = [
            'value' => $email,
            'type_id' => 8456121 // Add a wrong typ id
        ];
        $userId = $user->id;
        $targetTag = 'users';

        $result = Helper::create($data, $targetTag, $userId);

        $this->assertException($result, 'CreateFailedException');
    }

    /**
     * Test successful updateContact.
     *
     * @return void
     */
    public function test_updateContact_successful()
    {
        $contact = $this->createContact();

        $data = [
            'value' => 'some new value',
            'type_id' => 2 // a new type id
        ];
        $newUser = $this->createUser()['user'];

        $result = Helper::update($contact, $data, 'users', $newUser->id);

        $contact = Helper::id($contact->id);
        $this->assertEquals($contact, $result);
        $attachedUsers = $contact->users()->get();
        $this->assertEquals(1, count($attachedUsers));
        $this->assertEquals($newUser->id, $attachedUsers->first()->id);
    }

    /**
     * Test fail updateContact.
     *
     * @return void
     */
    public function test_updateContact_fail()
    {
        $this->expectException(UpdateFailedException::class);
        $contact = $this->createContact();

        $data = [
            'value' => 'some new value',
            'type_id' => 8451632 // wrong type id
        ];
        $newUser = $this->createUser()['user'];

        $result = Helper::update($contact, $data, 'users', $newUser->id);
        $this->assertException($result, 'UpdateFailedException');
    }

    /**
     * Test successful deleteContact.
     *
     * @return void
     */
    public function test_deleteContact_successful()
    {
        $contact = $this->createContact();
        $result = Helper::delete($contact->id);
        $this->assertEquals(true, $result);
    }

    /**
     * Test fail deleteContact.
     *
     * @return void
     */
    public function test_deleteContact_fail()
    {
        $this->expectException(DeleteFailedException::class);

        $result = Helper::delete(84585412);
        $this->assertEquals(true, $result);

        $this->assertException($result, 'DeleteFailedException');
    }

    /**
     * Test successful restoreContact.
     *
     * @return void
     */
    public function test_restoreContact_successful()
    {
        $contact = $this->createContact();
        $id = $contact->id;
        $delete = Helper::delete($id);
        $this->assertEquals(true, $delete);

        $result = Helper::restore($id);
        $contact = Helper::id($id);
        $this->assertEquals($contact, $result);
    }

    /**
     * Test fail restoreContact.
     *
     * @return void
     */
    public function test_restoreContact_fail()
    {
        $this->expectException(UpdateFailedException::class);

        $result = Helper::restore(84585412);

        $this->assertException($result, 'UpdateFailedException');
    }
}