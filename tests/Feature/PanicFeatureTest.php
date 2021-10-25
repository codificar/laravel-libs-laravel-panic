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
        $requestId = 1989;
        $ledgerId = 6;

        $params = array(
            'request_id' => $requestId,
            'ledger_id' => $ledgerId,
        );

        $response = $this->call('POST', 'lib/panic/save', $params);
        $this->assertIsObject($response);
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
    }

    public function testWillCallSegupRoute()
    {
        $segupLogin = 'ramon@drivesocial.io';
        $segupPassword = '1q2w3e4r5t6y';
        $segupRequestUrl = "http://sistemas.segup.pa.gov.br/alerta/api/posicao";
        $segupVerificationUrl = "http://sistemas.segup.pa.gov.br/alerta/api/usuario/autenticar";

        $params = [
            'segup_login' => $segupLogin,
            'segup_password' => $segupPassword,
            'segup_request_url' => $segupRequestUrl,
            'segup_verification_url' => $segupVerificationUrl
        ];

        $response = $this->call('POST', 'lib/panic/settings/save/segup', $params);
        $this->assertIsObject($response);
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());

        $this->assertDatabaseHas('settings', ['key' => 'segup_login', 'value' => $segupLogin]);
        $this->assertDatabaseHas('settings', ['key' => 'segup_password', 'value' => $segupPassword]);
        $this->assertDatabaseHas('settings', ['key' => 'segup_request_url', 'value' => $segupRequestUrl]);
        $this->assertDatabaseHas('settings', ['key' => 'segup_verification_url', 'value' => $segupVerificationUrl]);
    }

    public function testWillCallGetPanicSegupSettings()
    {
        $response = $this->call('GET', 'lib/panic/settings/segup');
        $this->assertIsObject($response);
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
    }

    public function testWillCallSavePanicButtonSettings()
    {
        $segupPanicButtonEnabledUser = 1;
        $segupPanicButtonEnabledProvider = 1;

        $params = [
            'panic_button_enabled_user' => $segupPanicButtonEnabledUser,
            'panic_button_enabled_provider' => $segupPanicButtonEnabledProvider
        ];

        $response = $this->call('POST', 'lib/panic/settings/save', $params);
        $this->assertIsObject($response);
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());


        $this->assertDatabaseHas('settings', ['key' => 'panic_button_enabled_user', 'value' => $segupPanicButtonEnabledUser]);
        $this->assertDatabaseHas('settings', ['key' => 'panic_button_enabled_user', 'value' => $segupPanicButtonEnabledProvider]);
    }

    public function testWillCallGetPanicButtonSettings()
    {
        $response = $this->call('GET', 'lib/panic/settings');
        $this->assertIsObject($response);
        $this->assertIsString($response->getContent());
        $this->assertJson($response->getContent());
    }
}
