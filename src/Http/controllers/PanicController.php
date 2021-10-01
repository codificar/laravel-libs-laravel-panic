<?php

use App\Http\Controllers\Controller;
use Codificar\Panic\Http\Resources\PanicResource;
use Codificar\Panic\Models\Panic;
use Laravel\Horizon\Listeners\SendNotification;
use Settings;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PanicController extends Controller
{
    /**
     * This store function will deal with preparing the information and passing it to the respective functions that are needed.
     * @api {post} /lib/panic/store 
     * @param object $request
     * @return resource PanicResource
     */
    public function storePanicRequest(Request $request)
    {
        //should the model/repository call be made in this level or just upon the model? 
        $panicModel = new Panic();
        $requestId = $request->request_id;
        $ledgerId = $request->ledger_id;
        $fetchedData = Panic::getPanicData($requestId, $ledgerId);
        //include the fetched data calls into the insert function
        $panicModel->insertPanicRequestToTable($requestId, $ledgerId, $fetchedData);
        //how to guarantee that if the return from the db is true, you get further actions?

        if ($panicModel) {
            $this->sendMailForAdmin($fetchedData->adminData->admin_id, $fetchedData);
            $this->sendSmsForAdmin($fetchedData->adminData->admin_id);
            $this->sendMailForEmergencyContacts($ledgerId, $fetchedData);
            $this->sendSmsForEmergencyContacts($ledgerId);
            $this->sendPushToEmergencyContacts($ledgerId);
            //verify how to create an Event to be listened later
            //$this->createPanicEvent();
        }

        //to include other api calls to third party security agencies include it into the if block below
        if ($request->security_agency == 'segup') {
            $segupRequest = $this->callSegupApi($fetchedData->request_data, $fetchedData->provider_data);
        }

        if ($segupRequest->apiResponse) {
            return new PanicResource($segupRequest->apiResponse);
        } else return new PanicResource($segupRequest);
    }

    /**
     * This function will send an email for the admin registered in the db.
     * @param int $adminId
     * @param object $fetchedData
     * @return bool true || false
     */
    private function sendMailForAdmin($adminId, $fetchedData)
    {
        $type = 'admin';
        $vars = Panic::createPanicHistory($fetchedData->userData, $fetchedData->providerData, $fetchedData->requestData);
        $subject = trans('panic.panic_email_subject');
        try {
            email_notification($adminId, $type, $vars, $subject);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send an sms for the admin registered in the db.
     * @param int $adminId
     * @return bool true || false
     */
    private function sendSmsForAdmin($adminId)
    {
        try {
            $type = 'admin';
            $messsage =  trans('panic.panic_sms_message');
            sms_notification($adminId, $messsage, $type);
            return true;
        } catch (Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send emails to thel ledger contactst registered in the ledger_contacts table.
     * @param int $ledgerId
     * @param object $fetchedData
     * @return bool true || false
     */
    private function sendMailForEmergencyContacts($ledgerId, $fetchedData)
    {
        $type = 'ledger_contacts';
        $vars = Panic::createPanicHistory($fetchedData->userData, $fetchedData->providerData, $fetchedData->requestData);
        $subject = trans('panic.panic_email_subject');
        try {
            email_notification($ledgerId, $type, $vars, $subject);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**
     * This function will send an sms for the ledger contacts registered in the ledger_contacts table.
     * @param int $ledgerId
     * @return bool true || false
     */
    private function sendSmsForEmergencyContacts($ledgerId)
    {
        try {
            $type = 'ledger_contacts';
            $messsage = trans('panic.panic_sms_message');
            sms_notification($ledgerId, $messsage, $type);
            return true;
        } catch (Exception $e) {
            \Log::error($e->getMessage());
        }
    }



    /**
     * This function will send an push notifications if the user has other users in the DB that have the same email.
     * @param int $ledgerId
     * @return void
     */
    private function sendPushToEmergencyContacts($ledgerId)
    {
        $id = $ledgerId;
        $title = trans('panic.panic_push_title');
        $message = trans('panic.sms_message');
        $type = 'ledger_contact';

        try {
            send_notifications($id, $type, $title, $message);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    /**this functin will verify if the SegupToken is valid, if so, it returns it, if not, it creates a new one and saves it
     * @param null
     * @return string $savedToken
     */

    private function verifySegupToken()
    {
        $timestamp = Settings::getSegupTokenTimestamp();
        if ($timestamp != Carbon::today()) {
            $login = Settings::getSegupLogin();
            $password = Settings::getSegupPassword();
            $client = new GuzzleHttp\Client();
            $response = $client->request('POST', 'http://sistemas.segup.pa.gov.br/alerta/api/usuario/autenticar', [
                'form_params' => [
                    'login' => $login,
                    'password' => $password,
                    'no-g-recaptcha' => true,
                    'expire' => 1440
                ]
            ]);
            $token = $response->getBody();
            $savedToken = Settings::saveSegupToken($token->token);
            return $savedToken;
        } else {
            $savedToken = Settings::getSegupToken();
            return $savedToken;
        };
    }

    //see the needed data in the document that was sent with the task, then format the data to send to the api
    /**
     * This function will call upon the segup API to send the panic request to the respective securtiy agency provider
     * @param object $fetchedData
     * @return object $apiResponse
     */
    private function callSegupApi($fetchedData)
    {
        $token = $this->verifySegupToken();
        $client = new GuzzleHttp\Client();
        $body = Panic::createSegupRequestBody($fetchedData->providerData, $fetchedData->requestData);
        $response = $client->post('http://sistemas.segup.pa.gov.br/alerta/api/posicao', [
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ],
            'body' => [
                $body
            ]
        ]);

        $apiResponse = $response->getBody();
        return $apiResponse;
    }

    //define the parameters to this function
    private function createPanicEvent($request)
    {
    }
}

//Disparar evento de pânico (posteriormente será escutado e alertado ao admin)
