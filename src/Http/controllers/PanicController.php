<?php

namespace Codificar\Panic\Http;

use App\Http\Controllers\Controller;
use Settings;
use Carbon\Carbon;
use Codificar\Panic\Models\Panic;
use Codificar\Panic\Http\Resources\PanicDeletedResource;
use Codificar\Panic\Http\Resources\PanicSuccessfulResource;
use Codificar\Panic\Http\Resources\PanicOnlyCreatedResource;
use Codificar\Panic\Http\Requests\PanicSettingAdminRequest;
use Codificar\Panic\Http\Requests\PanicSettingStoreRequest;
use Codificar\Panic\Http\Requests\PanicSettingSegupRequest;
use Codificar\Panic\Http\Requests\PanicStoreRequest;
use Codificar\Panic\Http\Resources\PanicButtonGettingResource;
use Codificar\Panic\Http\Resources\PanicButtonSettingResource;
use Codificar\Panic\Http\Resources\PanicGettingSegupResource;
use Codificar\Panic\Http\Resources\PanicSettingSegupResource;
use Codificar\Panic\Http\Resources\PanicSettingAdminResource;
use Codificar\Panic\Http\Resources\PanicGettingAdminResource;
use Codificar\Panic\Http\Resources\IndexResource;
use Codificar\Panic\Repositories\PanicRepository;


class PanicController extends Controller
{

    /**
     * @api {get} /lib/panic/
     * @return resource indexResource
     */
    public function index()
    {
        return new IndexResource(Panic::all());
    }


    /**
     * This store function will deal with preparing the information and passing it to the respective functions that are needed.
     * @api {post} /lib/panic/store 
     * @param object $request->ledger_id
     * @param object $request->request_id
     * @return resource PanicResource
     */
    //TODO: include the type param into the req to differentiate the types of alert, then create one type of histories for each type of alert
    public function storePanicRequest(PanicStoreRequest $request)
    {
        $requestId = $request->request_id;
        $ledgerId = $request->ledger_id;
        $fetchedData = PanicRepository::getPanicData($requestId);
        $adminId = $fetchedData->adminData->adminId;
        $panicModel = PanicRepository::insertPanicRequestToTable($requestId, $ledgerId, $adminId, $fetchedData);
        if (get_object_vars($panicModel)) {
            $this->sendMailForAdmin($fetchedData->adminData->adminId, $requestId, $fetchedData);
            $this->sendSmsForAdmin($fetchedData->adminData->adminId);
            $this->sendMailForEmergencyContacts($ledgerId, $fetchedData, $requestId);
            $this->sendSmsForEmergencyContacts($ledgerId);
            $this->sendPushToEmergencyContacts($ledgerId);
        }
        $security_agency = PanicRepository::getSecurityProviderAgency();
        //to include other api calls to third party security agencies include it into the if block below
        //you need to create the functions that will call that 3rd party api then include the resource in the next 
        if ($security_agency == 'segup') {
            $securityProviderApiCall = $this->callSegupApi($fetchedData->providerData, $fetchedData->requestData);
            if ($securityProviderApiCall->idposicao) {
                return new PanicSuccessfulResource($panicModel);
            } else {
                return new PanicOnlyCreatedResource($panicModel);
            }
        }
    }
    /**
     * @apí {post} /lib/panic/delete
     * @param int $request->ledger_id;
     * @return resource PanicDeletedResource
     */
    public function deletePanicRequest(\Request $request)
    {
        $resource = "";
        $repository = new PanicRepository();
        $resource = $repository->deletePanicRecordsFromTable($request->ledger_id);
        return new PanicDeletedResource($resource);
    }


    /**
     * This function will save the panic button settings on the database
     * @api {post} /lib/panic/setting
     * @param object $request->panic_button_enabled_user
     * @param object $request->panic_button_enabled_provider
     * @return resource PanicButtonSettingResource
     */
    public function savePanicButtonSettings(PanicSettingStoreRequest $request)
    {
        $panicButtonUserSetting = $request->panic_button_enabled_user;
        $panicButtonProviderSetting = $request->panic_button_enabled_provider;

        if ($panicButtonUserSetting && $panicButtonProviderSetting == null) {
            $panicRepository = PanicRepository::setPanicUserButtonSetting($panicButtonUserSetting);
            return new PanicButtonSettingResource($panicRepository);
        } elseif ($panicButtonProviderSetting && $panicButtonUserSetting == null) {
            $panicRepository = PanicRepository::setPanicProviderButtonSetting($panicButtonProviderSetting);
            return new PanicButtonSettingResource($panicRepository);
        } elseif ($panicButtonProviderSetting && $panicButtonUserSetting) {
            $panicRepositoryUser = PanicRepository::setPanicUserButtonSetting($panicButtonUserSetting);
            $panicRepositoryProvider = PanicRepository::setPanicProviderButtonSetting($panicButtonProviderSetting);
            $panicRepositorySettings = (object)  array(
                'panic_button_enabled_user' => $panicRepositoryUser->value,
                'panic_button_enabled_provider' => $panicRepositoryProvider->value
            );
            return new PanicButtonSettingResource($panicRepositorySettings);
        }
    }

    /**
     * This function will save the provided fields in the setttings table to provide the data needed for the api to work
     * @api {get}/lib/panic/settings/segup
     * @param object $request->security_provider_agency
     * @param object $request->segup_login
     * @param object $request->segup_password
     * @param object $request->segup_request_url
     * @param object $request->segup_response_url
     * @return resource PanicSettingSegupResource
     */
    public function savePanicSegupSettings(PanicSettingSegupRequest $request)
    {
        $panicSecurityProviderAgency = $request->security_provider_agency;
        $panicSegupLogin = $request->segup_login;
        $panicSegupPassword = $request->segup_password;
        $panicSegupRequestUrl = $request->segup_request_url;
        $panicSegupVerificationUrl = $request->segup_verification_url;

        if ($panicSecurityProviderAgency == 'segup') {
            $securityProviderAgency = PanicRepository::setSecurityProviderAgency($panicSecurityProviderAgency);
            $segupLogin = PanicRepository::setSegupLogin($panicSegupLogin);
            $segupPassword = PanicRepository::setSegupPassword($panicSegupPassword);
            $segupRequestUrl = PanicRepository::setSegupRequestUrl($panicSegupRequestUrl);
            $segupVerificationUrl = PanicRepository::setSegupVerificationUrl($panicSegupVerificationUrl);

            $panicSegupSettings = (object) array(
                'security_provider_agency' => $securityProviderAgency,
                'segup_login' => $segupLogin,
                'segup_password' => $segupPassword,
                'segup_request_url' => $segupRequestUrl,
                'segup_verification_url' => $segupVerificationUrl
            );
            return new PanicSettingSegupResource($panicSegupSettings);
        } elseif (get_object_vars($request) == null) {
            $failedRequest = false;
            return new PanicSettingSegupResource($failedRequest);
        }
    }

    /**
     * This function will return the panic button settings that are needed from the database
     * @api {get}/lib/panic/settings/
     * @return resource PanicButtonGettingResource
     */
    public static function getPanicButtonSettings()
    {
        $panicButtonUserSetting = PanicRepository::getPanicUserButtonSetting();
        $panicButtonProviderSetting = PanicRepository::getPanicProviderButtonSetting();
        $panicRepositorySettings = (object)  array(
            'panic_button_enabled_user' => $panicButtonUserSetting,
            'panic_button_enabled_provider' => $panicButtonProviderSetting,
        );
        return new PanicButtonGettingResource($panicRepositorySettings);
    }

    /**
     * This function will return the panic segup settings that are needed from the database
     * @api {get}/lib/panic/settings/segup
     * @return resource PanicSettingSegupResource
     */
    public static function getPanicSegupSettings()
    {
        $panicSegupLogin = PanicRepository::getSegupLogin();
        $panicSegupPassword = PanicRepository::getSegupPassword();
        $panicSegupRequestUrl = PanicRepository::getSegupRequestUrl();
        $panicSegupVerificationUrl = PanicRepository::getSegupVerificationUrl();
        $panicSecurityProviderAgency = PanicRepository::getSecurityProviderAgency();
        $panicRepositorySettings = (object) array(
            'segup_login' => $panicSegupLogin,
            'segup_password' => $panicSegupPassword,
            'segup_request_url' => $panicSegupRequestUrl,
            'segup_verification_url' => $panicSegupVerificationUrl,
            'security_provider_agency' => $panicSecurityProviderAgency
        );
        return new PanicGettingSegupResource($panicRepositorySettings);
    }

    /**
     * @api {post} /lib/panic/settings/save/admin
     * @param $request->panic_admin_id
     * @param $request->panic_admin_phone_number
     * @param $request->panic_admin_email
     * @return resource PanicSettingAdminResource
     */
    public static function savePanicAdminSettings(PanicSettingAdminRequest $request)
    {
        $panicAdminId = $request->panic_admin_id;
        $panicAdminPhone = $request->panic_admin_phone_number;
        $panicAdminEmail = $request->panic_admin_email;

        $adminMail = PanicRepository::setPanicAdminEmail($panicAdminEmail);
        $adminPhone = PanicRepository::setPanicAdminPhone($panicAdminPhone);
        $adminId = PanicRepository::setPanicAdminId($panicAdminId);

        $panicAdminSettings = (object) array(
            'panic_admin_mail' => $adminMail->value,
            'panic_admin_phone_number' => $adminPhone->value,
            'panic_admin_id' => $adminId->value,
        );

        return new PanicSettingAdminResource($panicAdminSettings);
    }

    /**
     * This function will return the panic segup settings that are needed from the database
     * @api {get}/lib/panic/settings/admin
     * @return resource PanicGettingAdminResource
     */
    public static function getPanicAdminSettings()
    {
        $adminMail = PanicRepository::getPanicAdminEmail();
        $adminPhone = PanicRepository::getPanicAdminPhone();
        $adminId = PanicRepository::getPanicAdminId();

        $panicAdminSettings = (object) array(
            'panic_admin_mail' => $adminMail,
            'panic_admin_phone_number' => $adminPhone,
            'panic_admin_id' => $adminId,
        );

        return new PanicGettingAdminResource($panicAdminSettings);
    }

    /**
     * This function will send an email for the admin registered in the db.
     * @param int $adminId
     * @param int $requestId
     * @param object $fetchedData
     * @return bool true || false
     */
    public function sendMailForAdmin($adminId, $requestId, $fetchedData)
    {
        $type = 'admin';
        $vars = PanicRepository::createPanicHistory($fetchedData->userData, $fetchedData->providerData, $fetchedData->requestData, $requestId);
        $subject = trans('panic.panic_email_subject');
        try {
            $emailNotification = email_notification($adminId, $type, $vars, $subject);
            return $emailNotification;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send an sms for the admin registered in the db.
     * @param int $adminId
     * @return bool true || false
     */
    public function sendSmsForAdmin($adminId)
    {
        try {
            $type = 'admin';
            $messsage =  trans('panic.panic_sms_message');
            sms_notification($adminId, $messsage, $type);
            return true;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send emails to thel ledger contactst registered in the ledger_contacts table.
     * @param int $ledgerId
     * @param object $fetchedData
     * @param int $requestId
     * @return bool true || false
     */
    public function sendMailForEmergencyContacts($ledgerId, $fetchedData, $requestId)
    {
        $type = 'ledger_contacts';
        $vars = PanicRepository::createPanicHistory($fetchedData->userData, $fetchedData->providerData, $fetchedData->requestData, $requestId);
        $subject = trans('panic::panic.panic_email_subject');
        $is_imp = 1;

        try {
            $emailForEmergencyContacts = email_notification($ledgerId, $type, $vars, $subject, $is_imp);
            return $emailForEmergencyContacts;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send an sms for the ledger contacts registered in the ledger_contacts table.
     * @param int $ledgerId
     * @return bool true || false
     */
    public function sendSmsForEmergencyContacts($ledgerId)
    {
        try {
            $type = 'ledger_contacts';
            $messsage = trans('panic.panic_sms_message');
            $smsNotification = sms_notification($ledgerId, $messsage, $type);
            return $smsNotification;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send an push notifications if the user has other users in the DB that have the same email.
     * @param int $ledgerId
     * @return void
     */
    public static function sendPushToEmergencyContacts($ledgerId)
    {
        $id = $ledgerId;
        $title = trans('panic.panic_push_title');
        $message = trans('panic.sms_message');
        $type = 'ledger_contact';

        try {
            send_notifications($id, $type, $title, $message);
            return trans('Panic::panic.push_was_successful');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return trans("panic::panic.push_request_error");
        }
    }

    /**this function will verify if the SegupToken is valid, if so, it returns it, if not, it creates a new one and saves it
     * @param null
     * @return string $savedToken
     */
    public static function verifySegupToken()
    {
        $timestamp = Settings::getSegupTokenExpirationTimestamp();
        if ($timestamp = 'No Timestamp' || $timestamp != Carbon::today()) {
            $url = Settings::getSegupVerificationUrl();
            $login = Settings::getSegupLogin();
            $password = Settings::getSegupPassword();
            $encodedRequestBody = [
                'login' => $login,
                'password' => $password,
                'no-g-recaptcha' => true,
                'expire' => 1000
            ];
            $postFieldsUnencoded = http_build_query($encodedRequestBody);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFieldsUnencoded,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));

            $response = curl_exec($curl);
            $decodedResponse = json_decode($response);
            curl_close($curl);
            $timestamp = Settings::saveSegupTokenExpirationTimestamp();
            $savedToken = Settings::saveSegupToken($decodedResponse->token);
            return $savedToken->value;
        } else {
            $savedToken = Settings::getSegupToken();
            return $savedToken;
        };
    }

    //see the needed data in the document that was sent with the task, then format the data to send to the api

    /**
     * This function will call upon the segup API to send the panic request to the respective securtiy agency provider
     * @param object $providerData
     * @param object $requestData
     * @return object $apiResponse
     */
    public function callSegupApi($providerData, $requestData)
    {
        $token = $this->verifySegupToken();
        $url = Settings::getSegupRequestUrl();
        $createdRequestBody = PanicRepository::createSegupRequestBody($providerData, $requestData);
        $unencodedRequestBody = http_build_query($createdRequestBody);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $unencodedRequestBody,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));
        $response = curl_exec($curl);
        $decodedResponse = json_decode($response);
        curl_close($curl);
        return $decodedResponse;
    }
}
