<?php

/**
 * send email notifications to users, admin, providers an admin_painel
 * @param  int  $id
 * @param  String  $type    user type (provider,admin_painel,admin, user:default, ledger_contacts)
 * @param  array   $vars    email content data
 * @param  String  $subject  email subject
 * @param  String  $key     email template key
 * @param  boolean $is_imp
 */
function email_notification($id, $type, $vars, $subject, $key = null, $is_imp = null, $replyTo = null, $emailTo = null, $copyEmails = null)
{
    try {
        $settings = Settings::where('key', 'email_notification')->first();
        $email_notification = $settings->value;

        if ($emailTo == null) {
            if ($type == 'provider') {
                $provider = Provider::find($id);
                $emailTo = $provider->email;
            } elseif ($type == 'user') {
                $user = User::find($id);
                $emailTo = $user->email;
            } elseif ($type == 'admin') {
                $admin = Admin::find($id);
                $emailTo = ($admin ? $admin->username : Settings::getAdminEmail());
            } else {
                $ledger_contacts = LedgerContact::where('ledger_id', $id)->get();
                foreach ($ledger_contacts as $ledger_contact) {
                    $emailTo = $ledger_contact->email;
                    if ($email_notification == 1 || $is_imp == "imp") {

                        if (!$key)
                            $key = 'layout';

                        SendEmailJob::dispatch($key, $vars, $emailTo, $subject, $replyTo, $copyEmails)->onQueue('emails');

                        //Queue::push(new App\Jobs\SendEmailJob($key, $vars, $emailTo, $subject, $replyTo, $copyEmails));
                    }
                }
                return true;
            }
        }

        if ($email_notification == 1 || $is_imp == "imp") {

            if (!$key)
                $key = 'layout';

            SendEmailJob::dispatch($key, $vars, $emailTo, $subject, $replyTo, $copyEmails)->onQueue('emails');
            //Queue::push(new App\Jobs\SendEmailJob($key, $vars, $emailTo, $subject, $replyTo, $copyEmails));
        }
        return true;
    } catch (Exception $e) {
        Log::error($e);
    }
}


/**
 * send sms notifications to users, providers and admins
 * @param  int $id
 * @param  string $type    user type ['provider', 'admin',] | ledger_contacts
 * @param  string $message
 */
function sms_notification($id, $type, $message)
{
    try {
        $sms_service = Settings::where('key', 'sms_service')->first();
        $settings = Settings::where('key', 'sms_notification')->first();
        $sms_notification = ($settings ? $settings->value : 0);
        $sms = null;

        if ($sms_service && $sms_service->value && $sms_notification == 1) {

            if ($type == 'provider') {
                $user = Provider::find($id);
                $phone = $user->phone;
            } elseif ($type == 'admin') {
                $settings = Settings::where('key', 'admin_phone_number')->first();
                $phone = $settings->value;
            } elseif ($type == 'user') {
                $user = User::find($id);
                $phone = $user->phone;
            } else {
                $ledger_contacts = LedgerContact::findById($id);
                foreach ($ledger_contacts as $ledger_contact) {
                    $phone = $ledger_contact->phone;
                    if ($sms_service->value == 'painel_zenvia') {
                        $sms = sendZenviaSms($phone, $message);
                    } else if ($sms_service->value == 'painel_twillo') {
                        $sms = sendTwilloSms($phone, $message);
                    }
                    return true;
                }
            }

            if ($sms_service->value == 'painel_zenvia') {
                $sms = sendZenviaSms($phone, $message);
            } else if ($sms_service->value == 'painel_twillo') {
                $sms = sendTwilloSms($phone, $message);
            }

            if ($sms == null) {
                Log::notice('houve um erro ao enviar sms pelo ' . $sms_service->value);
            }
        }
    } catch (Exception $e) {
        Log::error($e);
    }
}



/**
 * Essa função vai gerar uma notificação para os contatos registrados como usuários/provedores
 * @param int $id
 * @param string $type type[user, provider,ledger_contacts]
 * @param string $title 
 * @param string $message
 * @param string $is_imp
 * @param array $payload
 * @return void
 */
function send_notifications($id, $type, $title, $message, $is_imp = null, $payload = null)
{

    \Queue::push(new FireNotification($id, $type, $title, $message, $is_imp, $payload));
}

function send_ios_push($user_id, $title, $message, $payload = null)
{
    send_android_push($user_id, $title, $message, $payload);
}


function send_android_push($user_id, $title, $message, $payload = null)
{

    $limit = env('PUSH_LIMIT') ? env('PUSH_LIMIT') : 100;

    if (!is_array($user_id)) $user_id = array($user_id);
    $pagination = array_chunk($user_id, $limit);

    $n = count($pagination);

    for ($i = 0; $i < $n; $i++) {
        $androidPush = new PushNotification('fcm');

        $googleApiKey = Settings::getGcmKey();

        if (is_array($message)) {
            $body = $title;
        } else {
            $body = $message;
        }
        $androidPush->setMessage([
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $payload ? $payload : array('without_payload' => 'payload is required')
        ])
            ->setDevicesToken($pagination[$i])
            ->setConfig(['dry_run' => false])
            ->setApiKey($googleApiKey)
            ->send();
    }
}
