<?php
/**
 * @link https://desma.particles.co.ke/
 * @copyright Copyright (c) 2020 Particles
 * @license https://desma.particles.co.ke/license/
 */
namespace Desma\Messaging;

const SMS_URL = "https://2l4np.api.infobip.com/sms/2/text/advanced";

/**
 * Sms is the base class for the SMS gateway
 * 
 * @author Henry Ohanga <ohanga.henry@gmail.com>
 * @since 1.0
 */
class Sms {
    private $_username, $_password;

    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }

    /**
     * Submits outbound SMS to the external sms gateway and returns status
     * For example
     * 
     * ```
     * $sms->send("Test message", "254701033089");
     * ```
     * 
     * @param string $text the text to be sent out
     * @param string|array $recipients the recipient phone number(s) 
     * @return object the status of the outbound message
     */
    public function send($text, $recipients) {
        
        $headers = [
            "Accept" => "application/json", 
            "Authorization" => $this->getAuthorization(),
            "Content-Type" => "application/json"
        ];
        $body = \Unirest\Request\Body::json($this->getPayload($text, $recipients));

        $resp = \Unirest\Request::post(SMS_URL, $headers, $body);
        
        return $resp;
    }

    private function getAuthorization() {
        return "Basic " . base64_encode($this->_username . ":" . $this->_password);
    }

    private function getPayload($sms, $phone) {
        return [
            "messages" => [
                [
                    "destinations" => [
                        ["to" => $phone],
                    ],
                    "text" => $sms
                ]
            ]
        ];
    }

}