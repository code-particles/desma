<?php
/**
 * @link https://desma.particles.co.ke/
 * @copyright Copyright (c) 2020 Particles
 * @license https://desma.particles.co.ke/license/
 */
namespace Desma\Messaging;

const API_BASE_URL = "https://2l4np.api.infobip.com";

/**
 * Sms is the base class for the SMS gateway
 * 
 * @author Henry Ohanga <ohanga.henry@gmail.com>
 * @since 1.0
 */
class Sms {
    private $_username, $_password;
    private $_headers;

    public function __construct($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;

        $this->_headers = [
            "Accept" => "application/json", 
            "Content-Type" => "application/json",
            "Authorization" => "Basic " . base64_encode($this->_username . ":" . $this->_password),
        ];;
    }

    /**
     * Submits a single outbound SMS to the external sms gateway for sending 
     * to one or more recipients. 
     * Phone numbers must be in E64 format but without the plus prefix
     * 
     * For example
     * 
     * ```
     * $sms->send("Test message", "254701033089");
     * $sms->send("Test message", ["254701033089", "254700559109"]);
     * $sms->send("Test flash message", "254701033089", ["flash" => true])
     * $sms->send("Test flash message", "254701033089", ["flash" => true, "notifyUrl" => ""])
     * ```
     * 
     * @param string $text the text to be sent out
     * @param string|array $to the recipient phone number(s)
     * @param array $options including optional provision for flash, intermediateReport, 
     * notifyUrl, notifyContentType, callbackData, validityPeriod
     * @return object the status of the outbound message
     */
    public function send($text, $to, $options = []) {
        
        $body = \Unirest\Request\Body::json($this->getSmsPayload($text, $to, $options));

        $resp = \Unirest\Request::post(API_BASE_URL . '/sms/2/text/advanced', $this->_headers, $body);
        
        return $resp;
    }

    /**
     * Gets the account total balance 
     * 
     * @return object the account balance 
     */
    public function getBalance() {
        $resp =  \Unirest\Request::get(API_BASE_URL . '/account/1/total-balance', $this->_headers);

        return $resp;
    }

    private function getSmsPayload($sms, $to, $options = []) {
       
        return [
            "messages" => [
                array_merge(
                [
                    "destinations" => [
                        ["to" => $to],
                    ],
                    "text" => $sms
                ], $options)
            ]
        ];
    }

}