<?php

namespace App\Containers\Currencies\Tests;

use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;

use App\Containers\Currencies\Helpers\CurrenciesHelper as Helper;

use App\Containers\Currencies\Models\Currency;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

class CurrenciesHelperTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * Test successful all.
     *
     * @return void
     */
    public function test_all_successful()
    {
        $result = Helper::all();
        $currencies = Currency::all();
        $this->assertEquals($result, $currencies);
    }

    /**
     * Test successful id.
     *
     * @return void
     */
    public function test_id_successful()
    {
        $result = Helper::id(61);
        $currency = Currency::find(61);
        $this->assertEquals($result, $currency);
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
}