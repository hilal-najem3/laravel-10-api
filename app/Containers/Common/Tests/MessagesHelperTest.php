<?php

namespace  App\Helpers\Tests;

use App\Containers\Common\Helpers\MessagesHelper as Helper;
use Tests\TestCase;

/**
 * This class will test response helper
 */
class MessagesHelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }
    
    /**
     * Test successful getMessagesFinalArray.
     *
     * @return void
     */
    public function test_getMessagesFinalArray_successful()
    {
        $messages = [
            'Key1' => [
                'Key2' => [
                    'Key3' => [
                        'Key4' => 'Value4'
                    ],
                    'BB' => 'Value5'
                ],
                'AA' => 'BB'
            ]
        ];
        
        $msgKey = 'Key1.Key2.Key3.Key4.Value';
        $keysArray = explode('.', $msgKey);
        $result = Helper::getMessagesFinalArray($keysArray, $messages);
        $this->assertEquals([
            'Key4' => 'Value4'
        ], $result);

        $msgKey = 'Key1.Key2.BB';
        $keysArray = explode('.', $msgKey);
        $result = Helper::getMessagesFinalArray($keysArray, $messages);
        $this->assertEquals($result, [
            'Key3' => [
                'Key4' => 'Value4'
            ],
            'BB' => 'Value5'
        ]);
    }
}