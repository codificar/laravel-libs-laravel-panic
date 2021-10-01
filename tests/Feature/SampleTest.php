<?php

namespace Codificar\Panic\Tests\Feature;

use Codificar\Panic\Repositories\PanicRepository;

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = 1;
        $response2 = 1;

        $this->assertEquals($response2, $response);
    }

    public function testBasicTest2()
    {
        $response = PanicRepository::getProviderData(1);
        $this->assertIsObject($response);
    }

    public function TestingRoute()
    {
        $response = $this->get('/');
        $response = $this->call("POST", '/lib/panic/save', [
            'ledger_id' => 1,
            "request_id" => 1,
        ]);
        $this->assertIsResource($response);
    }
}
