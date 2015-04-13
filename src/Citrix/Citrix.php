<?php
/**
 * Created by PhpStorm.
 * User: EzyVA
 * Date: 3/24/2015
 * Time: 3:36 PM
 */

namespace Citrix;

class Citrix {
    /**
     * Authentication URL
     * @var String
     */
    //private $authorizeUrl = 'https://api.citrixonline.com/oauth/access_token';

    /**
     * API key or Secret Key in Citrix's Developer Portal
     * @var String
     */
    private $apiKey;

    /**
     * Access Token
     * @var String
     */
    private $accessToken;

    /**
     * Organizer Key
     * @var int
     */
    private $organizerKey;

    /**
     * @return String $apiKey
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     *
     * @param String $apiKey
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }
    /**
     * @return String
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param String $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return int
     */
    public function getOrganizerKey()
    {
        return $this->organizerKey;
    }

    /**
     * @param int $organizerKey
     */
    public function setOrganizerKey($organizerKey)
    {
        $this->organizerKey = $organizerKey;
    }
    /**
     * Being here bu passing the api key
     *
     * @param String $apiKey
     */
    public function __construct($apiKey)
    {
        $this->setApiKey($apiKey);
    }

    /**
     * Performs cURL requests (GET, POST, PUT, DELETE)
     *
     * @param $type //GET, POST, PUT, DELETE
     * @param array $args
     * @param array $data
     * @param int $timeout
     * @param bool $verify_ssl
     * @param bool $returnAsAssocArray
     * @return bool|mixed
     */
    protected function sendRequest( $type, $args = array(), $data = array(), $timeout = 30, $verify_ssl = false, $returnAsAssocArray = false ) {

        $accepted_types = ['GET', 'POST'];
        $url_args = implode('/', $args);
        $url = 'https://api.citrixonline.com/G2W/rest/organizers/'.$this->organizerKey.'/'.$url_args;
        $headers = array('Content-Type: application/json', 'Authorization: OAuth oauth_token='.$this->accessToken);

        //check if type is valid
        if ( !in_array( strtoupper($type), $accepted_types ) )
            return false;

         //Prepare Data for POST request
        $json_data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-DSAPI/1.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if ( 'POST' == strtoupper($type) ) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify_ssl);

        $result = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ( $result )
        {
            $http_response = new HttpResponse($http_code);
            return array(
                'http_status' => $http_response->getCode(),
                'http_message' => $http_response->getText(),
                'data' => json_decode($result, $returnAsAssocArray, 512, JSON_BIGINT_AS_STRING)
            );
        }
        else
            return false;
    }

    /**
     * Returns webinars scheduled for the future for the specified organizer and webinars of other organizers where the specified organizer is a co-organizer.
     *
     * @param bool $returnAsAssocArray
     * @return bool|mixed
     */
    public function getUpcomingWebinars( $returnAsAssocArray = false ) {

        $args = array('upcomingWebinars');

        return $this->sendRequest("GET", $args, array(), 30, false, $returnAsAssocArray);
    }

    /**
     * Retrieve information on a specific webinar.
     *
     * @param $webinarKey
     * @param bool $returnAsAssocArray
     * @return bool|mixed
     */
    public function getWebinar($webinarKey, $returnAsAssocArray = false ) {

        if ( empty($webinarKey) )
            return false;

        $args = array(
            'webinars',
            $webinarKey,
        );

        return $this->sendRequest("GET", $args, array(), 30, false, $returnAsAssocArray);
    }

    /**
     * Register an attendee for a scheduled webinar.
     *
     * @param $webinarKey
     * @param array $data
     *      firstName - Required string
     *      lastName - Required string
     *      email - Required string
     *
     * @param bool $resendConfirmation
     * @param bool $returnAsAssocArray
     * @return bool
     */
    public function createRegistrant( $webinarKey, $data = array(), $resendConfirmation = false, $returnAsAssocArray = false ) {
        if ( empty($webinarKey) || empty($data) )
            return false;

        if ( !array_key_exists('firstName', $data) || !array_key_exists('lastName', $data) || !array_key_exists('email', $data) )
            return false;

        if ( empty($data['firstName']) || empty($data['lastName']) || empty($data['email']) )
            return false;

        $args = array(
            'webinars',
            $webinarKey,
            'registrants'
        );

        if ( $resendConfirmation )
            $data['resendConfirmation'] = 'true';
        else
            $data['resendConfirmation'] = 'false';

        return $this->sendRequest("POST", $args, $data, 30, false, $returnAsAssocArray);

    }

    /**
     * Retrieve registration details for all registrants of a specific webinar.
     * Registrant details will not include all fields captured when creating the registrant.
     * To see all data, use the getRegistrant() function
     *
     * @param $webinarKey
     * @param bool $returnAsAssocArray
     * @return bool
     */
    public function getRegistrants( $webinarKey, $returnAsAssocArray = false ) {
        if ( empty($webinarKey) )
            return false;

        $args = array(
            'webinars',
            $webinarKey,
            'registrants'
        );

        return $this->sendRequest("GET", $args, array(), 30, false, $returnAsAssocArray);
    }

    /**
     * Returns all attendees for all sessions of the specified webinar.
     *
     * @param $webinarKey
     * @param bool $returnAsAssocArray
     * @return bool
     */
    public function getAttendees( $webinarKey, $sessionKey = '', $returnAsAssocArray = false ) {
        if ( empty($webinarKey) )
            return false;

        //Session key not specified, try to get the session key of the last webinar session
        if ( empty($sessionKey) ) {
            $sessions = $this->getWebinarSessions( $webinarKey );

            if ( is_array( $sessions )) {
                if ( $sessions['http_status'] == '200' ) {
                    if ( !empty( $sessions['data'] ) ) {

                        //get the last session
                        $sessionKey = $sessions['data'][0]->sessionKey;
                    }
                }
            }
        }

        if ( empty($sessionKey) )
            return false;

        $args = array(
            'webinars',
            $webinarKey,
            'sessions',
            $sessionKey,
            'attendees'
        );

        return $this->sendRequest("GET", $args, array(), 30, false, $returnAsAssocArray);
    }

    public function getWebinarSessions( $webinarKey, $returnAsAssocArray = false ) {
        if ( empty($webinarKey) )
            return false;

        $args = array(
            'webinars',
            $webinarKey,
            'sessions'
        );

        return $this->sendRequest("GET", $args, array(), 30, false, $returnAsAssocArray);

    }
}