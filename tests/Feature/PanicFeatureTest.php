<?php

use Tests\TestCase;

class PanicFeatureTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testCallPanicStoreRoute()
    {
        $requestId = 1;
        $ledgerId = 3;

        $params = array(
            'request_id' => $requestId,
            'ledger_id' => $ledgerId,
        );

        $response = $this->call('POST', 'lib/panic/save', $params);
        $this->assertIsObject($response);
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
    }
}
