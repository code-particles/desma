<?php
/**
 * @link https://desma.particles.co.ke/
 * @copyright Copyright (c) 2020 Particles
 * @license https://desma.particles.co.ke/license/
 */
namespace Desma\Messaging;

use BadMethodCallException;
use Unirest\Request;
use Unirest\Request\Body;

const API_BASE_URL = "http://164.52.197.71:6005/api/v2";

/**
 * Sms is the base class for the SMS gateway
 *
 * @author Henry Ohanga <ohanga.henry@gmail.com>
 * @since 1.0
 */
class Sms
{
    private $_apiKey, $_clientId;
    private $_headers;

    public function __construct($apiKey, $clientId)
    {
        $this->_apiKey = $apiKey;
        $this->_clientId = $clientId;

        $this->_headers = [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];
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
     * $sms->send("Test flash message", "254701033089", ["isFlash" => true])
     * $sms->send("Test flash message", "254701033089", ["isFlash" => true, "notifyUrl" => ""])
     * ```
     *
     * @param string $text the text to be sent out
     * @param string|array $to the recipient phone number(s)
     * @param array $options including optional provision for isFlash, isUnicode,
     * sendAt
     *
     * @return object the status of the outbound message
     */
    public function send($text, $to, $options = [])
    {
        $body = Body::Json($this->getSmsPayload($text, $to, $options));

        // print($body);

        $requestUrl = API_BASE_URL . "/SendSMS";

        $result = Request::post($requestUrl, $this->_headers, $body);

        return $result->body;
    }

    /**
     * Gets the account total balance
     *
     * @return object the account balance
     */
    public function getBalance()
    {
        $params = [
            "ApiKey" => $this->_apiKey,
            "ClientId" => $this->_clientId,
        ];

        $requestUrl =
            API_BASE_URL . "/Balance?" . http_build_query($params, "&amp");

        $resp = \Unirest\Request::get($requestUrl, $this->_headers);

        return $resp->body;
    }

    protected function getSmsPayload($sms, $to, $options = [])
    {
        if (is_array($to)) {
            $to = join(",", $to);
        }

        if (is_null($sms) || is_null($to) || !strlen($to)) {
            throw new BadMethodCallException(
                "sms and to properties must be set for you to send the sms"
            );
        }

        $payload = [
            "SenderId" => isset($options["senderId"])
                ? $options["senderId"]
                : "Particles",
            "Is_Unicode" => isset($options["isUnicode"])
                ? $options["isUnicode"]
                : false,
            "Is_Flash" => isset($options["isFlash"])
                ? $options["isFlash"]
                : false,
            "SchedTime" => isset($options["sendAt"])
                ? $options["sendAt"]
                : false,
            "Message" => $sms,
            "MobileNumbers" => $to,
            "ApiKey" => $this->_apiKey,
            "ClientId" => $this->_clientId,
        ];

        foreach ($payload as $key => $value) {
            if (!$value) {
                unset($payload[$key]);
            }
        }

        return $payload;
    }
}
