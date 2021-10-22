<?php
//functions for getting/setting the admin data
//include this in the settings model of the project that the lib is being implemented to

//add this to the application settings model function that gets all configs for the app
'panic_button_enabled_user'						=> (bool) self::getPanicButtonEnabledUser(),
'panic_button_enabled_provider'				=> (bool) self::getPanicButtonEnabledProvider(),

public static function getPanicButtonEnabledUser()
{
    $settings = self::where('key', 'panic_button_enabled_user')->first();

    if ($settings->value = 'true') {
        return true;
    } else return false;
}

public static function getPanicButtonEnabledProvider()
{
    $settings = self::where('key', 'panic_button_enabled_provider')->first();

    if ($settings->value = 'true') {
        return true;
    } else return false;
}

public static function savePanicButtonEnabledUser($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'panic_button_enabled_user', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}

public static function savePanicButtonEnabledProvider($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'panic_button_enabled_provider', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}

function getAdminEmail()
{
    $settings = self::where('key', 'admin_email_address')->first();

    if ($settings)
        return $settings->value;
    else
        return 'admin@localhost';
}


function getAdminPhone()
{
    $settings = self::where('key', 'admin_phone_number')->first();

    if ($settings) {
        return $settings->value;
    } else return 'adminDefaultPhone';
}

function saveAdminPhoneForAlert($setting)
{
    try {
        $settings = Settings::updateOrCreate(
            ['key' => 'admin_phone_number', 'value' => $setting]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}

//functions to set and get the security provider agency of the system


function getSecurityProviderAgency()
{
    $settings = self::where('key', 'security_provider_agency')->first();

    if ($settings) {
        return $settings->value;
    } else return 'Security Provider Agency Is Not Needed';
}

function saveSecurityProviderAgency($setting)
{
    try {
        $settings = Settings::updateOrCreate(
            ['key' => 'security_provider_agency', 'value' => $setting]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}


//function to set and get the segup related settings


function getSegupTokenExpirationTimestamp()
{
    $settings = self::where('key', 'segup_token_expiration_timestamp')->first();

    if ($settings) {
        return $settings->value;
    } else return "No Timestamp";
}

function getSegupToken()
{
    $settings = self::where('key', 'segup_token')->first();

    if ($settings) {
        return $settings->value;
    } else return false;
}

function getSegupLogin()
{
    $settings = self::where('key', 'segup_login')->first();

    if ($settings) {
        return $settings->value;
    } else return false;
}

function getSegupPassword()
{
    $settings = self::where('key', 'segup_password')->first();

    if ($settings) {
        return $settings->value;
    } else return false;
}

function getSegupVerificationUrl()
{
    $settings = self::where('key', 'segup_verification_url')->first();

    if ($settings) {
        return $settings->value;
    } else return false;
}

function getSegupRequestUrl()
{
    $settings = self::where('key', 'segup_request_url')->first();

    if ($settings) {
        return $settings->value;
    } else return false;
}

function saveSegupLogin($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'segup_login', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}

function saveSegupPassword($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'segup_password', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}

function saveSegupVerificationUrl($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'segup_verification_url', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}

function saveSegupRequestUrl($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'segup_request_url', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return false;
    }
}


function saveSegupTokenExpirationTimestamp()
{
    try {
        $settings = Settings::firstOrNew(
            [ //create a timestamp with carbon format, then add 24h to it and save it
                'key' => 'segup_token_expiration_timestamp', 'value' => Carbon::today()
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return $e;
    }
}

function saveSegupToken($setting)
{
    try {
        $settings = Settings::firstOrNew(
            [
                'key' => 'segup_token', 'value' => $setting,
            ]
        );
        return $settings;
    } catch (\Exception $e) {
        return "KeyNot Saved" . $e;
    }
}

function getProviderTimeout()
{
    $settings = self::where('key', 'provider_timeout')->first();

    if ($settings)
        return $settings->value;
    else
        return 60;
}
