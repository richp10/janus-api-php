<?php
    /**
     * Janus API client for PHP
     *
     *
     * @category  janusAPI
     * @package   janusAPI
     * @author    richp10
     * @copyright
     * @license
     * @link      https://github.com/richp10/janus-api-php
     */
    namespace richp10\janusAPI;
    class JanusAPI
    {
        private $_protocol = 'http';
        private $_apiKey = null;
        private $_dcHostname = null;
        function __construct($dcHostname, $apiKey = null, $protocol='http')
        {
            $this->_dcHostname = $dcHostname;
            $this->_apiKey = $apiKey;
            $this->_protocol=$protocol;
        }

        private function _deleteRequest($reqString, $paramArray, $apiUser = 'system')
        {
             return $this->_getRequest($reqString, $paramArray, $apiUser, "DELETE" );
        }
        private function _getRequest($reqString, $paramArray = null, $apiUser = 'system', $HTTPMETHOD = "GET" )
        {
            if ($paramArray == null) {
                $paramArray = array();
            }
            $paramArray['api_key'] = $this->_apiKey;
            $paramArray['api_username'] = $apiUser;
            $ch = curl_init();
            $url = sprintf(
                '%s://%s%s?%s',
                $this->_protocol,
                $this->_dcHostname,
                $reqString,
                http_build_query($paramArray)
            );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $HTTPMETHOD );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $body = curl_exec($ch);
            $rc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $resObj = new \stdClass();
            $resObj->http_code = $rc;
            // Only return valid json
            $json = json_decode($body);
            if (json_last_error() == JSON_ERROR_NONE) {
                $resObj->apiresult = $json;
            } else {
                $resObj->apiresult = $body;
            }
            return $resObj;
        }
        private function _putRequest($reqString, $paramArray, $apiUser = 'system')
        {
            return $this->_putpostRequest($reqString, $paramArray, $apiUser, "PUT" );
        }
        private function _postRequest($reqString, $paramArray, $apiUser = 'system')
        {
            return $this->_putpostRequest($reqString, $paramArray, $apiUser, "POST" );
        }
        private function _putpostRequest($reqString, $paramArray, $apiUser = 'system', $HTTPMETHOD = "POST" )
        {
            $ch = curl_init();
            $url = sprintf(
                '%s://%s%s?api_key=%s&api_username=%s',
                $this->_protocol,
                $this->_dcHostname,
                $reqString,
                $this->_apiKey,
                $apiUser
            );
            curl_setopt($ch, CURLOPT_URL, $url);
            $query = '';
            if (isset($paramArray['group'])) {
                foreach ($paramArray['group'] as $param => $value) {
                    $query .= $param.'='.$value .'&';
                }
            } else {
                foreach ($paramArray as $param => $value) {
                    $query .= $param.'='.$value .'&';
                }
            }
            $query = trim($query, '&');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $HTTPMETHOD );
            $body = curl_exec($ch);
            $rc = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $resObj = new \stdClass();
            $json = json_decode($body);
            if (json_last_error() == JSON_ERROR_NONE) {
                $resObj->apiresult = $json;
            } else {
                $resObj->apiresult = $body;
            }
            $resObj->http_code = $rc;
            return $resObj;
        }


    }
