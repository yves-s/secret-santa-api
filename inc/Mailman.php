<?php
namespace Mailman;
require 'vendor/autoload.php';

use \Mailjet\Resources;

class Mailman
{
    private $apiKey = 'MJ_APIKEY_PUBLIC';
    private $apiSecret = 'MJ_APIKEY_PRIVATE';
    private $fromEmail = 'secretsanta@yslch.de';

    public function __construct()
    {
        $this->mj = new \Mailjet\Client($this->apiKey, $this->apiSecret);
    }

    public function send($wichtelArray, $sender){
        $fromName = ($sender->name) ? $sender->name : "Secret Santa";
        $message = ($sender->message) ? "Message: " . $sender->message : "Der Wichtelmann kommt angeritten";
        $subject = ($sender->subject) ? $sender->subject : "Pssst... dein Wichtel ist...";

        for($i=0; $i<count($wichtelArray); $i++){
            $nachricht = $message . " \n ";
            $nachricht .= "---------------- \n ";
            $nachricht .= "Dein Wichtel ist: " . $wichtelArray[$i]['name'] . " \n ";
            $body = [
                'FromEmail' => $this->fromEmail,
                'FromName' => $fromName,
                'Subject' => $subject,
                'Text-part' => $nachricht,
                'Recipients' => [
                    [
                        'Email' => $wichtelArray[$i]['email']
                    ]
                ]
            ];

            $this->mj->post(Resources::$Email, ['body' => $body]);
        }
    }

}