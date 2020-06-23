<?php
namespace particles\Desma;

require_once 'vendor/autoload.php';

const SMS_URL = "https://2l4np.api.infobip.com/sms/2/text/advanced";

class Gateway {
    private $username, $password;


    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }


    public function send($sms, $phone) {
        $headers = ["Accept" => "application/json", "Authorization" => $this->getAuthorization()];
        $body = \Unirest\Request\Body::json($this->getPayload($sms, $phone));

        $resp = \Unirest\Request::post(SMS_URL, $headers, $body);
        
        echo $resp;
    }

    private function getAuthorization() {
        return "Basic " . base64_encode($this->username . ":" . $this->password);
    }

    private function getPayload($sms, $phone) {
        return [
            "messages" => [
                "destinations" => [
                    ["to" => $phone],
                ],
                "text" => $sms
            ]
        ];
    }

}