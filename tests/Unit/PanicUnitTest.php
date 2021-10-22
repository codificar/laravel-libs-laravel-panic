<?php

use Codificar\Panic\Repositories\PanicRepository;
use Tests\TestCase;
use Codificar\Panic\Http\PanicController;

class PanicUnitTest extends TestCase
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

    //start of repo unit tests
    public function testWillGetValidUserData()
    {
        $repository = PanicRepository::getUserData(1);
        $this->assertNotNull($repository);
        $this->assertIsObject($repository);
        $this->objectHasAttribute('id');
        $this->objectHasAttribute('first_name');
        $this->objectHasAttribute('last_name');
    }

    public function testWilNotlGetValidUserData()
    {
        $repository = PanicRepository::getUserData(99999999);
        $this->assertIsObject($repository);
        $this->assertNotNull($repository);
        $this->assertDatabaseMissing('user', ['id' => 99999999]);
    }

    public function testWillGetValidProviderData()
    {
        $repository = PanicRepository::getProviderData(1);
        $this->assertIsObject($repository);
        $this->objectHasAttribute('id');
        $this->objectHasAttribute('first_name');
        $this->objectHasAttribute('last_name');
        $this->objectHasAttribute('document');
        $this->objectHasAttribute('car_brand');
        $this->objectHasAttribute('car_model');
        $this->objectHasAttribute('car_color');
        $this->objectHasAttribute('car_number');
    }

    public function testWillNotGetValidProviderData()
    {
        $repository = PanicRepository::getProviderData(9999999);
        $this->assertObjectNotHasAttribute('car_number', $repository);
        $this->assertNotNull($repository);
        $this->assertDatabaseMissing('provider', ['id' => 99999999]);
    }

    public function testWillGetValidRequestLocationData()
    {
        $repository = PanicRepository::getRequestLocationData(20);
        $this->assertIsObject($repository);
        $this->objectHasAttribute('id', $repository);
        $this->objectHasAttribute('latitude', $repository);
        $this->objectHasAttribute('longitude', $repository);
        $this->objectHasAttribute('speed', $repository);
        $this->objectHasAttribute('updated_at', $repository);
        $this->objectHasAttribute('bearing', $repository);
    }

    public function testWillNotGetValidRequestLocationData()
    {
        $repository = PanicRepository::getRequestLocationData(9999999);
        $this->assertNotNull($repository);
        $this->assertObjectNotHasAttribute('latitude', $repository);
        $this->assertDatabaseMissing('request', ['id' => 99999999]);
    }

    public function testWillGetValidPartiesData()
    {
        $repository = PanicRepository::getPartiesData(3);
        $this->assertDatabaseHas('ledger', ['id' => 3]);
        $this->assertIsObject($repository);
        $this->objectHasAttribute('user_id', $repository);
        $this->objectHasAttribute('provider_id', $repository);
    }

    public function testWillGetInvalidPartiesData()
    {
        $repository = PanicRepository::getPartiesData(9999999);
        $this->assertNull($repository);
        $this->assertEmpty($repository);
        $this->assertDatabaseMissing('ledger', ['id' => 99999999]);
    }

    //TODO: Check about the edge cases from admin
    public function testWillGetValidAdminData()
    {
        $repository = PanicRepository::getAdminData();
        $this->assertDatabaseHas('admin', ['id' => $repository->adminId]);
    }

    public function testWillGetUsersThatAreLedgerContacts()
    {
        $ledgerId = 112;
        //used a copy of the borabrasil db
        $repository = PanicRepository::getEmergencyContactsUserId($ledgerId);
        $this->assertIsObject($repository);
        $this->objectHasAttribute('id', $repository);
    }

    public function testWillSavePanicButtonSettingUser()
    {
        $setting = '1';
        $repository = PanicRepository::setPanicUserButtonSetting($setting);
        $this->assertArrayHasKey('key', $repository);
        $this->assertArrayHasKey('value', $repository);
        $this->assertDatabaseHas('settings', ['key' => $repository->key, 'value' => $repository->value]);
    }

    public function testWillSavePanicButtonSettingProvider()
    {
        $setting = '1';
        $repository = PanicRepository::setPanicProviderButtonSetting($setting);
        $this->assertArrayHasKey('key', $repository);
        $this->assertArrayHasKey('value', $repository);
        $this->assertDatabaseHas('settings', ['key' => $repository->key, 'value' => $repository->value]);
    }

    public function testWillGetPanicButtonUserSetting()
    {
        $repository = PanicRepository::getPanicUserButtonSetting();
        $this->assertIsObject($repository);
        $this->objectHasAttribute('key', $repository);
        $this->objectHasAttribute('value', $repository);
    }

    //end of repo unit tests
    //start of model unit tests
    public function testWillGetAllNeededPanicData()
    {
        $repository = PanicRepository::getPanicData(1, 3);
        $this->assertIsObject($repository);
        $this->objectHasAttribute('partiesData', $repository);
        $this->objectHasAttribute('userData', $repository);
        $this->objectHasAttribute('providerData', $repository);
        $this->objectHasAttribute('requestData', $repository);
        $this->assertObjectHasAttribute('adminData', $repository);
    }

    public function testWillNotGetAllNeededPanicData()
    {

        $repository = PanicRepository::getPanicData(9999999, 99999999);
        $this->assertNull($repository);
        $this->assertIsNotObject($repository);
        $this->assertDatabaseMissing('request', ['id' => 99999999]);
        $this->assertDatabaseMissing('ledger', ['id' => 99999999]);
    }

    public function testWillCreatePanicHistory()
    {
        $ledgerId = 3;
        $fetchedPanicData = PanicRepository::getPanicData(1, 3);
        $createdPanicHistory = PanicRepository::createPanicHistory(
            $fetchedPanicData->userData,
            $fetchedPanicData->providerData,
            $fetchedPanicData->requestData,
            $ledgerId
        );

        $this->assertNotContains($createdPanicHistory, trans('panic::panic.panic_push_title'));
    }

    public function testWillNotCreatePanicHistory()
    {
        $empty = new stdClass();
        $createdPanicHistory = PanicRepository::createPanicHistory(
            $empty,
            $empty,
            $empty,
            0
        );
        $this->assertStringContainsString($createdPanicHistory, trans('panic::panic.panic_push_title'));
        $this->assertNotNull($createdPanicHistory);
    }

    public function testWillTestBearingN()
    {
        $direction = PanicRepository::getDirectionFromBearing(338.0);
        $this->assertIsString($direction);
        $this->assertStringContainsString("N", $direction);
    }

    public function testWillTestBearingNE()
    {
        $direction = PanicRepository::getDirectionFromBearing(26);
        $this->assertIsString($direction);
        $this->assertStringContainsString("NE", $direction);
    }

    public function testWillTestBearingL()
    {
        $direction = PanicRepository::getDirectionFromBearing(68);
        $this->assertStringContainsString("L", $direction);
    }

    public function testWillTestBearingSE()
    {
        $direction = PanicRepository::getDirectionFromBearing(115);
        $this->assertStringContainsString("SE", $direction);
    }

    public function testWillTestBearingS()
    {
        $direction = PanicRepository::getDirectionFromBearing(190);
        $this->assertStringContainsString("S", $direction);
    }

    public function testWillTestBearingSO()
    {
        $direction = PanicRepository::getDirectionFromBearing(212);
        $this->assertStringContainsString("SO", $direction);
    }

    public function testWillTestBearingO()
    {
        $direction = PanicRepository::getDirectionFromBearing(255);
        $this->assertStringContainsString("O", $direction);
    }

    public function testWillTestBearingNO()
    {
        $direction = PanicRepository::getDirectionFromBearing(330);
        $this->assertStringContainsString("NO", $direction);
    }

    public function testWillNotTestBearing()
    {
        $direction = PanicRepository::getDirectionFromBearing(-1);
        $this->assertIsNotString($direction);
    }

    //TODO: NEED MORE EDGE CASES
    public function testWillGetSecurityProviderStatus()
    {
        $setting = 'segup';
        $segupValue = Settings::saveSecurityProviderAgency($setting);
        $segupRefreshing = PanicRepository::getDirectedToSegup();
        $this->assertStringContainsString($segupRefreshing, "segup");
    }

    //TODO: MAKE TEST FOR NEGATIVE CASES
    public function testWillCreateSegupBodyForRequest()
    {
        $fetchedPanicData = PanicRepository::getPanicData(1, 3);
        $createdSegupBody = PanicRepository::createSegupRequestBody(
            $fetchedPanicData->providerData,
            $fetchedPanicData->requestData
        );
        $this->assertIsArray($createdSegupBody);
    }
    //TODO: NEED TO FIND A BETTER WAY TO EXPECT EXCEPTIONS ON THE CODE
    public function testWillNotCreateSegupBodyForRequestOnlyOneFalseValue()
    {
        $empty = new stdClass();
        $fetchedPanicData = PanicRepository::getPanicData(1, 3);
        $createdSegupBody = PanicRepository::createSegupRequestBody(
            $fetchedPanicData->providerData,
            $empty
        );
        $this->assertNull($createdSegupBody);
    }
    //END OF MODEL TESTS
    //START OF CONTROLLER UNIT TESTS


    //TODO: NEED TO TEST MORE EDGE CASES TO BE SAFE
    public function testWillSendMailToAdmin()
    {
        $adminId = 54;
        $requestId = 1;
        $fetchedData =  PanicRepository::getPanicData(1, 3);
        $this->assertIsObject($fetchedData);
        $panicInstance = new PanicController();
        $panicMail = $panicInstance->sendMailForAdmin($adminId, $requestId, $fetchedData);
        $this->assertIsBool($panicMail);
        $this->isTrue($panicMail);
    }

    //TODO: NEED TO FIND FURTHER EDGE CASES FOR TESTING
    public function testWillSendSmsToAdmin()
    {
        $adminId = 54;
        $panicInstance = new PanicController();
        $panicMail = $panicInstance->sendSmsForAdmin($adminId);
        $this->assertIsBool($panicMail);
        $this->isTrue($panicMail);
    }

    //TODO: TEST USER WITH MULTIPLE LEDGER CONTACTS
    public function testWillSendMailToEmergencyContacts()
    {
        $ledgerId = 3;
        $requestId = 1;
        $panicInstance = new PanicController();
        $fetchedData =  PanicRepository::getPanicData(1, 3);
        $this->assertIsObject($fetchedData);
        $panicSms = $panicInstance->sendMailForEmergencyContacts($ledgerId, $fetchedData, $requestId);
        $this->assertIsBool($panicSms);
        $this->isTrue($panicSms);
    }

    public function testWillSendSmsToEmergencyContacts()
    {
        $ledgerId = 112;
        $panicInstance = new PanicController();
        $panicSms = $panicInstance->sendSmsForEmergencyContacts($ledgerId);
        $this->assertIsBool($panicSms);
        $this->isTrue($panicSms);
    }

    public function testWillGetPanicButtonSettings()
    {
        $panicInstance = new PanicController();
        $repository = $panicInstance->getPanicSettings();
        $this->assertIsObject($repository);
        $this->assertArrayHasKey('panic_button_enabled_user', $repository);
        $this->assertArrayHasKey('panic_button_enabled_provider', $repository);
    }
}
