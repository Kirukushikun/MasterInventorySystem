<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SlackController extends Controller
{
    public static function sendSlackMessage($randomPhrase, $messageTitle, $messageContent, $tableData = null)
    {
        $slackWebhookUrl = env('SLACK_WEBHOOK_URL');

        $tableString = '';

        foreach ($tableData as $key => $outer_value) {
            $tableString .= $outer_value . "\n";
        }

        $payload = [
            "text" => $messageTitle,
            "blocks" => [
                [
                    "type" => "section",    
                    "text" => [
                        "type" => "plain_text",
                        "text" => "$tableString",
                        "emoji" => true
                    ]
                ]
            ]
        ];

        $slackMessageJSON = json_encode($payload);

        $ch = curl_init($slackWebhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $slackMessageJSON);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
