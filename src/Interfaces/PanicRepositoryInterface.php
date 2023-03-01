<?php

namespace Codificar\Panic\Interfaces;

interface PanicRepositoryInterface
{

    /**
     * This function inserts the Panic Request on to the Panic table
     * @param int $requestId
     * @param int $ledgerId
     * @param int $adminId
     * @param object $fetchedData
     * @return object $insertedEntry
     */
    public static function insertPanicRequestToTable(
		int $requestId, 
		int $ledgerId, 
		int $adminId,  
		object $fetchedData
	): object;
	
	/**
     * This function deletes a record from the panic table
     * @param int $ledgerId
     * @return string 
     */
    public function deletePanicRecordsFromTable(int $ledgerId): string;


    /**
     * This function provides a way to get the request location and data from the DB.
     * @param int $requestId
     * @return object $requestData
     */
    public static function getRequestLocationData(int $requestId): object;

    /**
     * This function provides a way to get the user and provider data from the DB using the Ledger Id.
     * @param int $ledgerId
     * @return object $partiesData
     */
    public static function getPartiesData(int $requestId): object;

    /**
     * This function provides a way to get the user data From The DB
     * @param int $userId
     * @return object $userData
     */
    public static function getUserData(int $userId): object ;

    /**
     * This function provides a way to get the provider data from the DB.
     * @param int $providerId
     * @return object providerData
     */
    public static function getProviderData(int $providerId): object;

    /**
     * This function provides a way to compare the  emergency ledger contacts that are also users and get their id.
     * @param int $ledgerId
     * @return object $ledgerContactUsers
     */
    public static function getEmergencyContactsUserId(int $ledgerId): object;


    /**
     * This function provides a way to get the admin data to send the panic request.
     * @param null
     * @return object $adminData
     */
    public static function getAdminData(): object;

	/**
     * This function creates the panic history string to be uploaded to the panic table
     * @param object $userData
     * @param object $providerData
     * @param object $requestData
     * @param int $requestId
     * @param int $ledgerId
     * @return string $panicHistory
     */
    public static function createPanicHistory(
		object $userData, 
		object $providerData, 
		object $requestData, 
		int $requestId, 
		int $ledgerId
	): string;

    /**
     * This function fetches the needed data for the panic requests
     * @param int $requestId
     * @return object $fetchedPanicData
     */
    public static function getPanicData(int $requestId): object;


    /**
     * This function creates the panic request body in the JSON format required by the segup api
     * @param object $providerData
     * @param object $requestData
     * @return array $panicRequestBody
     */
    public static function createSegupRequestBody(object $providerData, object $requestData): array;

    /**
     * This function converts a bearing received from the DB to a cardinal direction
     * BROKEN FUNCTION IN THE HELPER
     * @param float $bearing
     * @return string $direction
     */
    public static function getDirectionFromBearing(float $bearing): string;

    /**
     * This function fetches the panic User Button Setting from the DB.
     * @param null
     * @return object $panicUserButtonSetting
     */
    public static function getPanicUserButtonSetting(): object;

    /**
     * This function saves the panic User Button Setting in the DB.
     * @param string $setting
     * @return object $panicProviderButtonSetting
     */
    public static function setPanicUserButtonSetting(string $setting): object;

    /**
     * This function fetches the panic Provider Button Setting from the DB.
     * @param null
     * @return object $panicProviderButtonSetting
     */
    public static function getPanicProviderButtonSetting(): object;
    
	
    /**
     * This function saves the panic User Button Setting in the DB.
     * @param string $setting
     * @return object $panicProviderButtonSetting
     */
    public static function setPanicProviderButtonSetting(string $setting);

    /**
     * This function checks if the admin is using a Security Provider Agency, if so, the value is passed upon validation of the request;
     * @return string|null $securityAgency
     */
    public static function getSecurityProviderAgency();

    /**
     * This function saves the security provider agency into the db;
     * @return string|null $securityAgencyFromRepository
     */
    public static function setSecurityProviderAgency($securityProviderAgency);

    /**
     * This function will try to get a value for the segup login from the DB;
     * @return string|null $segupLogin
     */
    public static function getSegupLogin();


    /**
     * This function saves the segup login into the db;
     * @return string|null $segupLogin
     */
    public static function setSegupLogin($segupLogin);

    /**
     * This function will try to get a value for the segup Password  from the DB;
     * @return string|null $segupPassword
     */
    public static function getSegupPassword();

    /**
     * This function saves the segup password  into the db;
     * @return string|null $segupPassword
     */
    public static function setSegupPassword($segupPassword);

    /**
     * This function will try to get a value for the segup Password  from the DB;
     * @return string|null $segupRequestUrl
     */
    public static function getSegupRequestUrl();

    /**
     * This function saves the segup request url  into the db;
     * @return string|null $segupRequestUrl
     */
    public static function setSegupRequestUrl($segupRequestUrl);

    /**
     * This function will try to get a value for the segup Password  from the DB;
     * @return string|null $segupVerificationUrl
     */
    public static function getSegupVerificationUrl();

    /**
     * This function saves the segup verification url  into the db;
     * @return string|null $segupVerificationUrl
     */
    public static function setSegupVerificationUrl($segupVerificationUrl);

    /**
     * This function sets an admin phone on the Settings Table in the DB;
     * and then returns the phone number 
     * @param string $adminPhone
     * @return string $adminPhone
     */
    public static function setPanicAdminPhone(string $adminPhone): string;

    /**
     * This function fetches the admin phone from the settings table from the DB then returns the number
     * @return string $adminPhone
     */
    public static function getPanicAdminPhone(): string;

    /**
     * This function receives the admin mail from a route then saves it into the settings table.
     * @param string $setting
     * @return object $admin_mail
     */
    public static function setPanicAdminEmail(string $setting): object;

	/**
     * This function fetches the admin mail from the settings table from the DB then returns it
     * @return string $adminMail
     */
    public static function getPanicAdminEmail(): string;

    /**
     * This function fetches the admin id from the settings table from the DB then returns it
     * @return int|null $adminMail
     */
    public static function getPanicAdminId();

    /**
     * This function sets a panic admin Id in the settings table then returns the value
     */
    public static function setPanicAdminId(string $setting): object;

	/**
	 * recupera todos os admins cadastrados para tela de configurações da lib
	 * @return Array
	 */
	public static function getAdmins();

	/**
     * Query to search registers
	 * @param int $requesId
	 * @param int $userLedgerId
	 * @param int $providerLedgerId
     */
    public static function search(
        $requestId = '',
        $userLedgerId = '',
        $providerLedgerId = ''
	);

	/**
     * Fetch and paginate registers
     * @param int $page
     * @param object $filter
     * @return array
     */
    public static function fetch(int $page, object $filter): array;

	/**
	 * Get all messages panic today
	 * @return array
	 */
	public function getAllMessagesPanicToday(): array;

	/**
     * set al messages as read by conversation and/or user
     * @param int $conversationId
     * @param int $messageId
     * @param int $userId - default null
     * 
     * @return void
     */
	public function setMessagesAsSeen(int $conversationId, int $messageId, int $userId = null): void;
}
