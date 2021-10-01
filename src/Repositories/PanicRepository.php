<?php

namespace Codificar\Panic\Repositories;

use Settings;
use User;
use Requests;
use Provider;
use Admin;
use LedgerContact;

class PanicRepository
{
    /**
     * This function provides a way to get the request location and data from the DB.
     * @param int $requestId
     * @return object $requestData
     */
    public static function getRequestData($requestId)
    {
        $requestData =
            Requests::find($requestId)->join('request_location', $requestId, '=', 'request_location.request_id')
            ->select(
                'requests.user_id',
                'requests.provider_id',
                'request_location.updated_at',
                'request_location.latitude',
                'request_location.longitude',
                'request_location.speed',
                'request_location.bearing'
            );

        return $requestData;
    }

    /**
     * This function provides a way to get the user data From The DB
     * @param int $userId
     * @return object $userData
     */
    public static function getUserData($userId)
    {
        $userData = User::find($userId)->select(
            'first_name',
            'last_name',
            'ledger_id'
        );

        return $userData;
    }

    /**
     * This function provides a way to get the provider data from the DB.
     * @param int $providerId
     * @return object providerData
     */
    public static function getProviderData($providerId)
    {
        $providerData = Provider::find($providerId)
            ->select(
                'first_name',
                'last_name',
                'document',
                'car_brand',
                'car_model',
                'car_color',
                'car_number'
            );

        return $providerData;
    }

    /**
     * This function provides a way to get the emergency contacts to send the messages from the DB.
     * @param int $ledgerId
     * @return object $emergencyContacts 
     */
    public static function getEmergencyContacts($ledgerId)
    {
        if (LedgerContact::findById($ledgerId) != null) {
            $emergencyContacts = LedgerContact::find($ledgerId)->select(['email', 'phone']);
            return $emergencyContacts;
        } else {
            return $emergencyContacts = null;
        }
    }

    /**
     * This function provides a way to get the admin data to send the panic request.
     * @param null
     * @return object $adminData
     */
    public static function getAdminData()
    {
        $adminMail = Settings::getAdminEmail();
        $adminPhone = Settings::getAdminPhone();
        $adminId = Admin::find(Auth::guard('web')->user()->id);

        $adminData = (object)[
            $adminPhone,
            $adminMail,
            $adminId
        ];
        return $adminData;
    }
};
