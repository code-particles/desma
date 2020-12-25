<?php
/**
 * @link https://desma.particles.co.ke/
 * @copyright Copyright (c) 2020 Particles
 * @license https://desma.particles.co.ke/license/
 */
namespace Desma\Messaging;

use Unirest\Request as ApiRequest;
use Unirest\Request\Body;

/**
 * Sms is the base class for the SMS gateway
 *
 * @author Henry Ohanga <ohanga.henry@gmail.com>
 * @since 1.0
 */
class Sms
{
    const API_BASE_URL = "https://desma-api.herokuapp.com/v1/messaging/sms";

    private $username, $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
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
        $requestBody = Body::Json([
            "text" => $text,
            "to" => $to,
            "options" => $options,
        ]);

        $requestUrl = self::API_BASE_URL . "/send";
        $headers = $this->getRequestHeaders();

        $result = ApiRequest::post($requestUrl, $headers, $requestBody);

        return $result->body;
    }

    /**
     * Gets the account total balance
     *
     * @return object the account balance
     */
    public function getBalance()
    {
        $url = self::API_BASE_URL . "/balance";
        $headers = $this->getRequestHeaders();

        $response = ApiRequest::get($url, $headers);

        return $response->body;
    }

    private function getRequestHeaders()
    {
        $authString = base64_encode($this->username . ":" . $this->password);

        return [
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Basic " . $authString,
        ];
    }
}
