<?php

namespace Codificar\Panic\Repositories;

use Illuminate\Support\Carbon;
use Requests;
use Settings;
use User;
use Admin;
use Codificar\Panic\Models\Messages;
use Ledger;
use LedgerContact;
use Provider;
use RequestLocation;
use stdClass;
use Codificar\Panic\Models\Panic;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class PanicRepository
{
    /**
     * This function inserts the Panic Request on to the Panic table
     * @param int $requestId
     * @param int $ledgerId
     * @param int $adminId
     * @param object $fetchedData
     * @return object $insertedEntry
     */
    // change this function to the repository pattern
    public static function insertPanicRequestToTable(int $requestId, int $ledgerId, int $adminId,  object $fetchedData): object 
    {
        $insertedEntry = new stdClass();

        try {
            $requestHistory = self::createPanicHistory($fetchedData->userData, $fetchedData->providerData, $fetchedData->requestData, $requestId, $ledgerId);
            $panic = new Panic();
            $panic->ledger_id = $ledgerId;
            $panic->request_id = $requestId;
            $panic->admin_id = $adminId;
            $panic->history = $requestHistory;
            $panic->save();
            $insertedEntry = $panic;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        return $insertedEntry;
    }

    /**
     * This function deletes a record from the panic table
     * @param int $ledgerId
     * @return string 
     */
    public function deletePanicRecordsFromTable(int $ledgerId): string
    {
        $stringToReturn = "";
        try {
            Panic::where('ledger_id', $ledgerId)->delete();
            $stringToReturn = trans('panic.successful_delete');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
        return $stringToReturn;
    }

    /**
     * This function provides a way to get the request location and data from the DB.
     * @param int $requestId
     * @return object $requestData
     */
    public static function getRequestLocationData(int $requestId): object
    {
        $requestLocationData = new stdClass();
        try {
            $requestLocationData =
                RequestLocation::where('id', $requestId)->firstOrFail([
                    'updated_at',
                    'latitude',
                    'longitude',
                    'speed',
                    'bearing'
                ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        return $requestLocationData;
    }
    /**
     * This function provides a way to get the user and provider data from the DB using the Ledger Id.
     * @param int $ledgerId
     * @return object $partiesData
     */
    public static function getPartiesData($requestId): object
    {
        $partiesData = new stdClass();
        try {
            $partiesData = Requests::where('id', $requestId)->first([
                'user_id',
                'confirmed_provider'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        };

        return $partiesData;
    }

    /**
     * This function provides a way to get the user data From The DB
     * @param int $userId
     * @return object $userData
     */
    public static function getUserData(int $userId): object
    {
        $userData = new stdClass();
        try {
            $userData = User::where('id', $userId)->firstOrFail([
                'id',
                'first_name',
                'last_name',
                'phone'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
        return $userData;
    }

    /**
     * This function provides a way to get the provider data from the DB.
     * @param int $providerId
     * @return object providerData
     */
    public static function getProviderData(int $providerId): object
    {
        $providerData = new stdClass();
        try {
            $providerData = Provider::where('id', $providerId)->firstOrFail([
                'id',
                'first_name',
                'last_name',
                'document',
                'car_brand',
                'car_model',
                'car_color',
                'car_number',
                'latitude',
                'longitude'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
        return $providerData;
    }

    /**
     * This function provides a way to compare the  emergency ledger contacts that are also users and get their id.
     * @param int $ledgerId
     * @return object $ledgerContactUsers
     */
    public static function getEmergencyContactsUserId(int $ledgerId): object
    {
        $ledgerContactsUsers = new stdClass();
        try {
            $ledgerContactsUsers =  LedgerContact::where('ledger_id', $ledgerId)
                ->join('user', 'ledger_contact.email', '=', 'user.email')->get(['user.id']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
        return $ledgerContactsUsers;
    }

    /**
     * This function provides a way to get the admin data to send the panic request.
     * @param null
     * @return object $adminData
     */
    public static function getAdminData(): object
    {
        try {
            $adminMail = Settings::getPanicAdminEmail();
            $adminPhone = Settings::getPanicAdminPhone();
            $adminId = Settings::getPanicAdminId();

            if ($adminId == null) {
                $adminData = (object) array(
                    'adminPhone' => $adminPhone,
                    'adminMail' => $adminMail,
                    'adminId' => '1',
                );
                return $adminData;
            } else {
                $adminData = (object) array(
                    'adminPhone' => $adminPhone,
                    'adminMail' => $adminMail,
                    'adminId' => $adminId,
                );
                return $adminData;
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        };
    }

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
        int $ledgerId): string
    {
        if (get_object_vars($userData)  && get_object_vars($providerData)  && get_object_vars($requestData)) {

            $ledgerData = Ledger::find($ledgerId);

            if($ledgerData->user_id)
                $panicHistory = trans('panic::panic.user') . $userData->first_name . " " . $userData->last_name . trans('panic::panic.id') . 
                $userData->id . trans('panic::panic.emergency_alert', ['request_id' => $requestId]) . $providerData->first_name . " " . 
                $providerData->last_name . trans('panic::panic.id') . $providerData->id . trans('panic::panic.document') . 
                $providerData->document . trans('panic::panic.vehicle') . $providerData->car_brand . " " . $providerData->car_model . " " . 
                $providerData->car_color . " " . $providerData->car_number;
            else
                $panicHistory = trans('panic::panic.provider') . $providerData->first_name . " " . $providerData->last_name . trans('panic::panic.id') . 
                $providerData->id . trans('panic::panic.emergency_alert_provider', ['request_id' => $requestId]) . $userData->first_name . " " . 
                $userData->last_name . trans('panic::panic.id') . $userData->id . trans('panic::panic.document') . 
                $userData->document . trans('panic::panic.vehicle') . $providerData->car_brand . " " . $providerData->car_model . " " . 
                $providerData->car_color . " " . $providerData->car_number;
            return $panicHistory;
        } else return $panicHistory = trans('panic::panic.panic_push_title');
    }

    /**
     * This function fetches the needed data for the panic requests
     * @param int $requestId
     * @return object $fetchedPanicData
     */
    public static function getPanicData(int $requestId): object
    {
        $partiesData = PanicRepository::getPartiesData($requestId);

        if ($partiesData != null && $partiesData->user_id != null && $partiesData->confirmed_provider != null) {
            $requestData = PanicRepository::getRequestLocationData($requestId);
            $userData = PanicRepository::getUserData($partiesData->user_id);
            $providerData = PanicRepository::getProviderData($partiesData->confirmed_provider);
            $adminData = PanicRepository::getAdminData();

            $fetchedPanicData = (object) array(
                'userData' => $userData,
                'providerData' => $providerData,
                'requestData' => $requestData,
                'adminData' => $adminData
            );

            return $fetchedPanicData;
        }
    }
    /**
     * This function creates the panic request body in the JSON format required by the segup api
     * @param object $providerData
     * @param object $requestData
     * @return array $panicRequestBody
     */
    public static function createSegupRequestBody(object $providerData, object $requestData): array
    {
        try {
            if ($requestData->bearing <= 0.0 || $requestData->bearing >= 360) {
                $direction = 'N';
            } else $direction = getDirection($requestData->bearing);

            $panicUpdatedAt = Carbon::parse($requestData->updated_at)->toDateTimeString();

            $segupRequestBody = [
                "iddispositivo" => $providerData->document,
                "identificacao" => $providerData->first_name . " " . $providerData->last_name . " " . $providerData->car_brand . " " . $providerData->car_model . " " . $providerData->car_color . "" . $providerData->car_number,
                "dt_posicao" => $panicUpdatedAt,
                "latitude" => $requestData->latitude,
                "longitude" => $requestData->longitude,
                "velocidade" => $requestData->speed,
                "direcao" => $direction,
                "situacao" => "PANICO"
            ];
            return $segupRequestBody;
        } catch (\Exception $e) {
            return \Log::error($e->getMessage());;
        }
    }


    /**
     * This function converts a bearing received from the DB to a cardinal direction
     * BROKEN FUNCTION IN THE HELPER
     * @param float $bearing
     * @return string $direction
     */
    public static function getDirectionFromBearing(float $bearing): string
    {
        if ($bearing > 337.5 && $bearing < 360 || $bearing < 0 && $bearing > 22.5) {
            return "N";
        }

        $cardinalDirections = array(
            'N' => array(337.5, 22.5),
            'NE' => array(22.5, 67.5),
            'L' => array(67.5, 112.5),
            'SE' => array(112.5, 157.5),
            'S' => array(157.5, 202.5),
            'SO' => array(202.5, 247.5),
            'O' => array(247.5, 292.5),
            'NO' => array(292.5, 337.5)
        );

        foreach ($cardinalDirections as $dir => $angles) {
            if ($bearing >= $angles[0] && $bearing < $angles[1]) {
                $direction = $dir;
                return $direction;
            }
        }
    }


    //
    //Lib Settings Functions
    //

    /**
     * This function fetches the panic User Button Setting from the DB.
     * @param null
     * @return object $panicUserButtonSetting
     */
    public static function getPanicUserButtonSetting(): object
    {
        try {
            $panicButtonUserSettings = Settings::getPanicButtonEnabledUser();
            return (object) array(
                'panic_button_enabled_user' => $panicButtonUserSettings
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        };
    }


    /**
     * This function saves the panic User Button Setting in the DB.
     * @param string $setting
     * @return object $panicProviderButtonSetting
     */
    public static function setPanicUserButtonSetting(string $setting): object
    {
        try {
            $panicButtonUserSettings = Settings::savePanicButtonEnabledUser($setting);
            return $panicButtonUserSettings;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        };
    }

    /**
     * This function fetches the panic Provider Button Setting from the DB.
     * @param null
     * @return object $panicProviderButtonSetting
     */
    public static function getPanicProviderButtonSetting(): object
    {
        try {
            $panicButtonProviderSetting = Settings::getPanicButtonEnabledProvider();
            return (object) array(
                'panic_button_enabled_provider' => $panicButtonProviderSetting
            );
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        };
    }

    /**
     * This function saves the panic User Button Setting in the DB.
     * @param string $setting
     * @return object $panicProviderButtonSetting
     */
    public static function setPanicProviderButtonSetting(string $setting)
    {
        try {
            $panicButtonUserSettings = Settings::savePanicButtonEnabledProvider($setting);
            return $panicButtonUserSettings;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        };
        //https: //app.mobi66.appmobilidadeurbana.com.br/api/v3/application/settings?user_type=user
    }

    /**
     * This function checks if the admin is using a Security Provider Agency, if so, the value is passed upon validation of the request;
     * @return string $securityAgency || null
     */
    public static function getSecurityProviderAgency()
    {
        $securityAgency = Settings::getSecurityProviderAgency();
        if ($securityAgency == "segup") {
            return $securityAgency;
        } else return trans('panic::panic.no_security_agency');
    }

    /**
     * This function saves the security provider agency into the db;
     * @return string $securityAgencyFromRepository || null
     */
    public static function setSecurityProviderAgency($securityProviderAgency)
    {
        try {
            $securityAgencyFromRepository = Settings::saveSecurityProviderAgency($securityProviderAgency);
            return $securityAgencyFromRepository;
        } catch (\Exception $e) {
            return \Log::error($e->getMessage());;
        }
    }

    /**
     * This function will try to get a value for the segup login from the DB;
     * @return string $segupLogin || null
     */
    public static function getSegupLogin()
    {
        $segupLogin = Settings::getSegupLogin();
        if ($segupLogin != null) {
            return $segupLogin;
        } else return trans('panic::no_segup_login');
    }

    /**
     * This function saves the segup login into the db;
     * @return string $segupLogin || null
     */
    public static function setSegupLogin($segupLogin)
    {
        try {
            $segupLoginRepository = Settings::saveSegupLogin($segupLogin);
            return $segupLoginRepository;
        } catch (\Exception $e) {
            return \Log::error($e->getMessage());;
        }
    }

    /**
     * This function will try to get a value for the segup Password  from the DB;
     * @return string $segupPassword || null
     */
    public static function getSegupPassword()
    {
        $segupPassword = Settings::getSegupPassword();
        if ($segupPassword != null) {
            return $segupPassword;
        } else return trans('panic::no_segup_password');
    }

    /**
     * This function saves the segup password  into the db;
     * @return string $segupPassword || null
     */
    public static function setSegupPassword($segupPassword)
    {
        try {
            $segupPasswordRepository = Settings::saveSegupPassword($segupPassword);
            return $segupPasswordRepository;
        } catch (\Exception $e) {
            return \Log::error($e->getMessage());;
        }
    }

    /**
     * This function will try to get a value for the segup Password  from the DB;
     * @return string $segupRequestUrl || null
     */
    public static function getSegupRequestUrl()
    {
        $segupRequestUrl = Settings::getSegupRequestUrl();
        if ($segupRequestUrl != null) {
            return $segupRequestUrl;
        } else return trans('panic::no_segup_url');
    }

    /**
     * This function saves the segup request url  into the db;
     * @return string $segupRequestUrl || null
     */
    public static function setSegupRequestUrl($segupRequestUrl)
    {
        try {
            $segupRequestUrlRepository = Settings::saveSegupRequestUrl($segupRequestUrl);
            return $segupRequestUrlRepository;
        } catch (\Exception $e) {
            return \Log::error($e->getMessage());;
        }
    }

    /**
     * This function will try to get a value for the segup Password  from the DB;
     * @return string $segupVerificationUrl || null
     */
    public static function getSegupVerificationUrl()
    {
        $segupVerificationUrl = Settings::getSegupVerificationUrl();
        if ($segupVerificationUrl != null) {
            return $segupVerificationUrl;
        } else return trans('panic::no_segup_verification_url');
    }

    /**
     * This function saves the segup verification url  into the db;
     * @return string $segupVerificationUrl || null
     */
    public static function setSegupVerificationUrl($segupVerificationUrl)
    {
        try {
            $segupVerificationRepository = Settings::saveSegupVerificationUrl($segupVerificationUrl);
            return $segupVerificationRepository;
        } catch (\Exception $e) {
            return \Log::error($e->getMessage());;
        }
    }

    //create trans no admin phone
    //create trans no admin mail
    //create trans no admin id
    //TODO: create tests for these functions

    /**
     * This function sets an admin phone on the Settings Table in the DB;
     * and then returns the phone number 
     * @param string $adminPhone
     * @return string $adminPhone
     */
    public static function setPanicAdminPhone(string $adminPhone): string 
    {
        try {
            $adminPhone = Settings::savePanicAdminPhone($adminPhone);
            return $adminPhone;
        } catch (\Exception $e) {
            return $e . trans('panic::panic.admin_phone_not_saved');
        }
    }

    /**
     * This function fetches the admin phone from the settings table from the DB then returns the number
     * @return string $adminPhone
     */
    public static function getPanicAdminPhone(): string
    {
        $adminPhone = Settings::getPanicAdminPhone();
        if ($adminPhone != null) {
            return $adminPhone;
        } else return trans('panic::no_admin_phone');
    }

    /**
     * This function receives the admin mail from a route then saves it into the settings table.
     * @param string $setting
     * @return object $admin_mail
     */
    public static function setPanicAdminEmail(string $setting): object
    {
        try {
            $PanicAdminMail = Settings::savePanicAdminEmail($setting);
            return $PanicAdminMail;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    //TODO: change trans
    /**
     * This function fetches the admin mail from the settings table from the DB then returns it
     * @return string $adminMail
     */
    public static function getPanicAdminEmail(): string
    {
        $adminPhone = Settings::getPanicAdminEmail();
        if ($adminPhone != null) {
            return $adminPhone;
        } else return trans('panic::no_admin_phone');
    }

    //TODO: change trans 
    /**
     * This function fetches the admin id from the settings table from the DB then returns it
     * @return int|null $adminMail
     */
    public static function getPanicAdminId()
    {
        $adminId = Settings::getPanicAdminId();
        if ($adminId != null) {
            return $adminId;
        } else return null;
    }

    /**
     * This function sets a panic admin Id in the settings table then returns the value
     * @param string $setting
     * @return object
     */
    public static function setPanicAdminId(string $setting): object
    {
        try {
            $PanicAdminId = Settings::savePanicAdminId($setting);
            return $PanicAdminId;
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

	/**
	 * recupera todos os admins cadastrados para tela de configurações da lib
	 * @return Array
	 */
	public static function getAdmins() {
		return Admin::where('type', 'admin')->where('is_active', 1)->get(['id', 'username']);
	}

	/**
     * Query to search registers
     */
    public static function search(
        $requestId = '',
        $userLedgerId = '',
        $providerLedgerId = ''
    ) {
        $query = Panic::query();
        $query = $query->leftJoin(
            'request as r', 
            'panic.request_id', '=', 'r.id'
        )->leftJoin(
			'user as u',
			'r.user_id', '=', 'u.id'
        )->leftJoin(
			'provider as p',
			'r.current_provider', '=', 'p.id'
        )->leftJoin(
			'ledger as l',
			'panic.ledger_id', '=', 'l.id'
        );

        if ($requestId)
            $query = $query->where('r.id', $requestId);

        if ($userLedgerId)
            $query = $query->where('l.id', $userLedgerId);

        if ($providerLedgerId)
            $query = $query->where('l.id', $providerLedgerId);

		return $query->select(
            'panic.id',
            'request_id',
            'panic.created_at as date',
			'panic.history',
            DB::raw("CONCAT(u.first_name,' ',u.last_name) AS user_name"),
            DB::raw("CONCAT(p.first_name,' ',p.last_name) AS provider_name")
        );
    }

	/**
     * Fetch and paginate registers
     * @param int $page
     * @param object $filter
     * @return array
     */
    public static function fetch(int $page, object $filter): array
    {
        $recordsTotal = Panic::whereNotNull('id')->count();
		
		if (isset($filter->user_id)) {
			$user_ledger = Ledger::where('user_id', $filter->user_id)->first();
		}

		if (isset($filter->provider_id)) {
			$provider_ledger = Ledger::where('provider_id', $filter->provider_id)->first();
		}

		$data = self::search(
            isset($filter->request_id) ? $filter->request_id : null,
            isset($user_ledger->id) ? $user_ledger->id : null,
            isset($provider_ledger->id) ?  $provider_ledger->id : null
        );

        $currentPage = $page;

		Paginator::currentPageResolver(function () use ($currentPage) {
			return $currentPage;
        });

        return [
			'records_total' => $recordsTotal,
			'records_filtered' => $data->count(),
			'panic' => $data->paginate(10)
		];
    }

    /**
     * Get all messages panic todat
     * @return array
     */
    public function getAllMessagesPanicToday(): array
    {
        $query = Panic::select(
            [
                'panic.id', 
                \DB::raw('CONCAT(u.first_name, " ",  u.last_name) as username'), 
                \DB::raw('date_format(panic.created_at, "%d/%m/%Y %h:%m:%s") AS datetime'), 
                'panic.history as message',
                'panic.request_id as request_id'
            ])
            ->leftJoin('request as r', 'panic.request_id', '=', 'r.id')
            ->leftJoin('user as u', 'r.user_id', '=', 'u.id')
            ->where(['panic.is_seen' => 0])            
            ->whereBetween('panic.created_at', [Carbon::today()->toDateTimeString(), Carbon::tomorrow()->toDateTimeString()])
            ->groupBy('id')
            ->orderBy('panic.created_at', 'desc');
        return array(
            'total_unread' => $query->get()->count(),
            'messages' => $query->limit(5)->get()
        );
    }

    /**
     * set al messages as read by conversation and/or user
     * @param int $conversationId
     * @param int $messageId
     * @param int $userId - default null
     * 
     * @return void
     */
    public function setMessagesAsSeen(int $conversationId, int $messageId, int $userId = null): void
    {
        $messages = Messages::where('conversation_id', $conversationId)
			->where('id', '<=', $messageId);
        if($userId) {
			$messages->where('user_id', '<>', $userId);
        }
			$messages->update(['is_seen' => true]);
    }
};
