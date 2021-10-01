<?php

namespace Codificar\Panic\Models;

use Exception;
use Codificar\Panic\Repositories\PanicRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Settings;

class Panic extends Model
{
    /**
     * This function inserts the Panic Request on to the Panic table
     * @param int $requestId
     * @param int $ledgerId
     * @param object $fetchedData
     * @return object $insertedEntry
     */
    public function insertPanicRequestToTable($requestId, $ledgerId,  $fetchedData)
    {

        $requestHistory = $this->createPanicHistory($fetchedData->userData, $fetchedData->providerData, $fetchedData->requestData);

        $insertedEntry = DB::table('panic')->insert([
            'ledger_id' => $ledgerId,
            'request_id' => $requestId,
            'admin_id' => $fetchedData->adminData->adminId,
            'history' => $requestHistory,
        ]);
        return $insertedEntry;
    }

    /**
     * This function deletes a record from the panic table
     * @param int $ledgerId
     * @return string 
     */
    public function deletePanicRecordsFromTable($ledgerId)
    {
        try {
            DB::table('panic')->where('ledger_id', $ledgerId)->delete();
            return trans('panic.successful_delete');
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return null;
        }
    }


    /**
     * This function creates the panic history string to be uploaded to the panic table
     * @param object $userData
     * @param object $providerData
     * @param object $requestData
     * @return string $panicHistory
     */
    public static function createPanicHistory($userData, $providerData, $requestData)
    {
        $panicHistory = trans('panic.user') . $userData->first_name . $userData->last_name . trans('panic.id') . $requestData->id . trans('panic.emergency_alert') .
            $providerData->first_name . $providerData->last_name . trans('panic.id') . $providerData->id . trans('panic.document')
            . $providerData->document_id . trans('panic.vehicle')
            . $providerData->car_brand . $providerData->car_model . $providerData->car_color . $providerData->car_number;

        return $panicHistory;
    }

    /**
     * This function fetches the needed data for the panic requests
     * @param int $requestId
     * @return object $fetchedPanicData
     */
    public static function getPanicData($requestId, $ledgerId)
    {
        $requestData = PanicRepository::getRequestData($requestId);
        $userData = PanicRepository::getUserData($requestData->user_id);
        $providerData = PanicRepository::getProviderData($requestData->provider_id);
        $adminData = PanicRepository::getAdminData();
        $emergencyContacts = PanicRepository::getEmergencyContacts($ledgerId);

        $fetchedPanicData = (object) [
            $requestData,
            $userData,
            $providerData,
            $adminData,
            $emergencyContacts
        ];
        return $fetchedPanicData;
    }

    /**
     * This function creates the panic request body in the JSON format required by the segup api
     * @param object $providerData
     * @param object $requestData
     * @return string $panicRequestBody
     */
    public static function createSegupRequestBody($providerData, $requestData)
    {
        $direction = getDirection($providerData->bearing);

        $segup_request_body = [
            "iddispositivo" => $providerData->document,
            "identificacao" => $providerData->first_name . " " . $providerData->last_name . " " . $providerData->car_brand . " " . $providerData->car_model . " " . $providerData->car_color . "" . $providerData->car_number,
            "dt_posicao" => $requestData->updated_at,
            "latitude" => $requestData->latitude,
            "longitude" => $requestData->longitude,
            "velocidade" => $requestData->speed,
            "direcao" => $direction,
            "situacao" => "PANICO"
        ];

        return json_encode($segup_request_body);
    }

    /**
     * This function checks if the admin is using a Security Provider Agency, if so, the value is passed upon validation of the request;
     * @param null
     * @return mixed $security_agency or string
     */
    public static function getDirectedToSegup()
    {
        $securityAgency = Settings::getSecurityProviderAgency();
        if ($securityAgency->value == "segup") {
            return $securityAgency->value;
        } else return trans('panic.no_security_agency');
    }

    /**
     * This function sets an admin phone on the Settings Table using the Facade brought by laravel after a Route call
     * and then returns the phone number 
     * @param string $adminPhone
     * @return string $adminPhone
     */
    public static function setAdminPhoneForEmergencies($adminPhone)
    {
        try {
            $adminPhone = Settings::saveAdminPhoneForAlert($adminPhone);
            return trans('panic.admin_phone_saved') . $adminPhone;
        } catch (Exception $e) {
            return trans('panic.admin_phone_not_saved');
        }
    }
}
