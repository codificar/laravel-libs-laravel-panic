<?php

use Illuminate\Database\Migrations\Migration;

class RunPanicRequestEmailSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        EmailTemplate::updateOrCreate(['key' => 'panic_request'], [
            'key' => 'panic_request',
            'subject' => '',
            'copy_emails' => Settings::getPanicAdminEmail(),
            'from' => Settings::getPanicAdminEmail()
        ]);
    
        $template = EmailTemplate::where('key', 'panic_request')->first();
    
        //vars : url do logo da empresa, frase , invite url , register user trans
        if ($template) {
        $template->content = trim('
                <div style="width: 100%;">
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;">
                <img style="width: 100%; max-width: 320px; height: 170px;" src="{{ $vars["logo"] }}" alt="Logo da empresa" />
                </div>
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;">
                <p style="font-size: 20px; font-family: " Roboto", sans-serif;">{{ $vars["panicAlert"] }}</p>
                </div>
                <div style="margin: 0 auto; max-width: 640px; padding: 15px; text-align: center;"><h3 style="padding: 11px 16px; color: #fff; background-color: #ff3f05; text-decoration: none; border-radius: 5px;"> <span style="font-size: 20px; letter-spacing: 0.5px; font-weight: bold; font-family: " Roboto", sans-serif;">{{ $vars["panicText"] }}</span> </a></div>
            </div>
                ');
        $template->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if($secret = EmailTemplate::where('key','panic_request')->first())
            $secret->delete();
    }
}
