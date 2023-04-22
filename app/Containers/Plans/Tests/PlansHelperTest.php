<?php

namespace  App\Containers\Common\Tests;

use Illuminate\Support\Str;

use App\Containers\Plans\Helpers\PlansHelper as Helper;

use App\Containers\Plans\Models\Plan;

use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\CreateFailedException;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

/**
 * This class will test controller and helper for regions
 */
class PlansHelperTest extends TestCase
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
            'abbreviation' => Str::random(5),
            'description' => Str::random(5),
            'price' => 10,
            'currency_id' => 1
        ];
    }

    protected function create(): Plan
    {
        $data = $this->createData();
        $plan = Helper::baseCreate($data);
        return $plan;
    }

    /**
     * Test successful get data.
     *
     * @return void
     */
    public function test_get_successful()
    {
        $plan = $this->create();

        $plans = Plan::all();
        $result = Helper::all();
        $this->assertEquals($result, $plans);

        $result = Helper::id($plan->id);
        $this->assertEquals($result, $plan);
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
        $plan = Helper::baseCreate($data);
        $databaseContactType = Plan::where('name', $data['name'])->first(); // name is unique
        $this->assertEquals($plan, $databaseContactType);
    }

    /**
     * Test fail create data.
     *
     * @return void
     */
    public function test_create_fail()
    {
        $this->expectException(CreateFailedException::class);
        $result = Helper::baseCreate([]);
        $this->assertException($result, 'CreateFailedException');
    }

    /**
     * Test successful update data.
     *
     * @return void
     */
    public function test_update_successful()
    {
        $plan = $this->create();

        $newData = $this->createData();
        $result = Helper::baseUpdate($plan, $newData);

        $plan = Helper::id($plan->id);
        $this->assertEquals($result, $plan);
    }

    /**
     * Test fail update data.
     *
     * @return void
     */
    public function test_update_fail()
    {
        $plan = $this->create();
        $newData = $this->createData();
        $newData['name'] = $plan->name;

        $this->expectException(UpdateFailedException::class);
        $result = Helper::baseUpdate($this->create(), $newData);
        $this->assertException($result, 'UpdateFailedException');
    }
}