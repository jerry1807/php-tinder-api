<?php

namespace Henshall;

use GuzzleHttp\Client;

class TinderApi implements TinderApiInterface
{
    const URL = 'https://api.gotinder.com';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    /**
     * Get user profile.
     *
     * @param string $token
     *
     * @return array
     */
    public function getProfile($token)
    {

        return $this->makeGetRequest($token, '/profile');
    }

    /**
     * Get profiles recommendations.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function getRecommendations($token)
    {

        return $this->makeGetRequest($token, '/v2/recs/core');
    }

    /**
     * Swipe to right.
     *
     * @param string $token
     *
     * @param string $id
     *
     * @return mixed
     */
    public function like($token, $id)
    {

        return $this->makeGetRequest($token, '/like/' . $id);
    }


    /**
     * Swipe left.
     *
     * @param string $token
     *
     * @param string $id
     *
     * @return mixed
     */
    public function pass($token, $id)
    {

        return $this->makeGetRequest($token, '/pass/' . $id);
    }


    /**
     * GETS YOUR OWN METADATA.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function getMetadata($token)
    {

        return $this->makeGetRequest($token, '/v2/meta/');
    }


    /**
     * Updates users location.
     *
     * @param string $token Tinder access token
     * @param array $position array (lat => float, lng => float)
     *
     * @return array
     */
    public function ping($token, array $position)
    {
        $data = $this->makeGetRequest($token, '/user/ping');

        if (array_key_exists('error', $data)) {
            throw new \RuntimeException('You can`t change your location frequently. Please try again later.');
        }

        return $data;
    }


    /**
     * Gets Token based upon users Refresh Token
     *
     * @param string $token Tinder access token
     *
     * @return array
     */
    public function getTokenFromRefreshToken($token)
    {
        $responseArray = $this->makeGetRequest($token, '/v2/auth/login/sms');

        return $responseArray['data'];
    }

    /**
     * Sends SMS message to user to verify their account
     *
     * @param string $phoneNumber Your phone number associated with your tinder account
     *
     * @throws
     *
     * @return object
     */
    public function validateCode($phoneNumber, $code)
    {
        $headers = [
            'Authority' => 'api.gotinder.com',
            'Origin' => 'https://tinder.com',
            'X-Recovery-Token' => ' ',
            'X-Auth-Token' => ' ',
            'User-Session-Time-Elapsed' => '109054',
            'X-Supported-Image-Formats' => 'webp,jpeg',
            'Content-Type' => 'application/json',
            'User-Session-Id' => 'null',
            'Accept' => 'application/json',
            'Platform' => 'web',
            'Sec-Fetch-Site' => 'cross-site',
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
            'Sec-Fetch-Mode' => 'cors',
            'Referer' => 'https://tinder.com/',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'en-US,en;q=0.9,fr;q=0.8',
        ];

        $data = ['otp_code' => $code, 'phone_number' => $phoneNumber, 'is_update' => false];

        try {
            $response = $this->client->post(self::URL . '/v2/auth/sms/validate?auth_type=sms&locale=en', [
                'json' => $data,
                'headers' => $headers
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $responseArray = json_decode($response->getBody()->getContents(), true);

        return $responseArray['data'];
    }

    /**
     * Sends SMS message to user to verify their account
     *
     * @param string $phoneNumber Your phone number associated with your tinder account
     *
     * @throws
     *
     * @return string
     */


    public function requestCode($phoneNumber)
    {
        $number_plus = $phoneNumber;
        $number = ltrim($number_plus, '+');
        $number = preg_replace("/[^0-9]/", "", $number );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.gotinder.com/v3/auth/login?locale=en');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, chr(10).chr(13).chr(10).chr(11).$number);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Authority: api.gotinder.com';
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'X-Supported-Image-Formats: webp,jpeg';
        $headers[] = 'Funnel-Session-Id: bb041b36638ca491';
        $headers[] = 'Persistent-Device-Id: 7dd5826b-8433-4543-bb4b-c9937e8c4205';
        $headers[] = 'Tinder-Version: 2.43.0';
        $headers[] = 'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1';
        $headers[] = 'Content-Type: application/x-google-protobuf';
        $headers[] = 'User-Session-Id: null';
        $headers[] = 'Accept: application/json';
        $headers[] = 'App-Session-Time-Elapsed: 14852';
        $headers[] = 'X-Auth-Token: ';
        $headers[] = 'User-Session-Time-Elapsed: null';
        $headers[] = 'Platform: web';
        $headers[] = 'App-Session-Id: 93c554f5-f39c-4cf7-845c-4989cb321e1b';
        $headers[] = 'App-Version: 1024300';
        $headers[] = 'Origin: https://tinder.com';
        $headers[] = 'Sec-Fetch-Site: cross-site';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Referer: https://tinder.com/';
        $headers[] = 'Accept-Language: en-US,en;q=0.9,fr;q=0.8';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $result = preg_replace("/[^0-9]/", "", $result );
        return $result;
    }



//    public function requestCode($phoneNumber)
//    {
//        $number_plus = $phoneNumber;
//        $number = ltrim($number_plus, '+');
//        $number = preg_replace("/[^0-9]/", "", $number);
//
//        $headers = [
//            "Authority" => " api.gotinder.com",
//            "Pragma" => " no-cache",
//            "Cache-Control" => " no-cache",
//            "X-Supported-Image-Formats" => " webp,jpeg",
//            "Funnel-Session-Id" => " bb041b36638ca491",
//            "Persistent-Device-Id" => " 7dd5826b-8433-4543-bb4b-c9937e8c4205",
//            "Tinder-Version" => " 2.43.0",
//            "User-Agent" => " Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1",
//            "Content-Type" => " application/x-google-protobuf",
//            "User-Session-Id" => " null",
//            "Accept" => " application/json",
//            "App-Session-Time-Elapsed" => " 14852",
//            "X-Auth-Token" => " ",
//            "User-Session-Time-Elapsed" => " null",
//            "Platform" => " web",
//            "App-Session-Id" => " 93c554f5-f39c-4cf7-845c-4989cb321e1b",
//            "App-Version" => " 1024300",
//            "Origin" => " https",
//            "Sec-Fetch-Site" => " cross-site",
//            "Sec-Fetch-Mode" => " cors",
//            "Sec-Fetch-Dest" => " empty",
//            "Referer" => " https",
//            "Accept-Language" => " en-US,en;q=0.9,fr;q=0.8"
//        ];
//
//        try {
//            $response = $this->client->post(self::URL . '/v3/auth/login?locale=en', [
//                'body' => chr(10) . chr(13) . chr(10) . chr(11) . $number,
//                'headers' => $headers
//            ]);
//        } catch (\Exception $e) {
//            return $e->getMessage();
//        }
//
//        return $response->getBody()->getContents();
//    }

    /**
     * @param string $token
     * @param string $profileId
     *
     * @return mixed
     */
    public function getUser($token, $profileId)
    {

        return $this->makeGetRequest($token, '/user/' . $profileId);
    }


    /**
     * Get matches profiles.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function getMatches($token)
    {

        return $this->makeGetRequest($token, '/v2/matches?count=50');
    }

    /**
     * Get certain matched profile by id.
     *
     * @param string $token
     *
     * @param string $matchId
     *
     * @return mixed
     */
    public function getCertainMatch($token, $matchId)
    {

        return $this->makeGetRequest($token, "/matches/{$matchId}");
    }

    /**
     * Get common connection of a user
     *
     * @param string $token
     *
     * @param string $userId
     *
     * @return mixed
     */
    public function getCommonConnections($token, $userId)
    {

        return $this->makeGetRequest($token, "/user/{$userId}/common_connections");
    }

    /**
     * Get Spotify settings
     *
     * @param string $token
     *
     * @return mixed
     */
    public function getSpotifySettings($token)
    {

        return $this->makeGetRequest($token, '/v2/profile/spotify');
    }

    /**
     * Send Message to that id
     *
     * @param string $token
     *
     * @param string $userId
     *
     * @param string $message
     *
     * @throws
     *
     * @return array
     */
    public function sendMessage($token, $userId, $message)
    {
        //$userId = '5f44702123434afe8d';
        $response = $this->client->post(self::URL . "/user/matches/{$userId}/", [
            'json' => ['message' => $message],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Common method to make some get requests
     *
     * @param string $token
     *
     * @param string $url
     *
     * @throws
     *
     * @return array
     */
    private function makeGetRequest($token, $url)
    {
        $response = $this->client->get(self::URL . $url, [
            'headers' => [
                'X-Auth-Token' => $token,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

}
