<?php

namespace Manatee\CoreBundle\Utility;

use Symfony\Component\HttpFoundation\Request;

class ApiUtility
{
    /** @var \Symfony\Component\HttpFoundation\Request  */
    protected $request;
    protected $data;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $parameter
     * @return mixed
     */
    public function getParameter($parameter)
    {
        // Check if parameter was sent
        if(!array_key_exists($parameter, $this->data)){
            return false;
        }

        return $this->data[$parameter];
    }

    /**
     * @param string $parameter
     * @return boolean
     */
    public function hasParameter($parameter)
    {

        // Check if parameter was sent
        if(!array_key_exists($parameter, $this->data)){
            return false;
        }

        return true;
    }

    /**
     * @param array $parameters
     * @return int
     */
    public function validateRequest(array $parameters = array())
    {
        // Check request type
        if ($this->request->getMethod() != 'POST') {
            // Error: Invalid HTTP method
            return 1;
        }

        // Check request content-type
        if ($this->request->headers->get('content-type') != 'application/json') {
            // Error: Invalid request content-type
            return 2;
        }

        // Check request body
        if ($this->request->getContent() == '') {
            // Error: Malformed request, empty request body
            return 3;
        }

        ## VALIDATE JSON REQUEST
        // We simply use json_decode to parse the content of the request and
        //    then replace the request data on the $request object.
        //    This is useful if we ever decide to deprecate JSON in favor of
        //    other request method, for example HTTP POST.
        $this->data = json_decode($this->request->getContent(), true);

        if (!is_array($this->data)) {
            // Error: Bad JSON packages
            return 4;
        }

        // if content is needed, then validate it.
        if($parameters)
        {
            // Check for JSON structure
            foreach($parameters as $parameter)
            {
                if(!array_key_exists($parameter, $this->data)){
                    // Error: missing parameters
                    return 5;
                }
            }
        }

        // No error.
        return 0;
    }

    /**
     * @param int $error
     * @param boolean $debug
     * @return \Symfony\Component\HttpFoundation\JsonResponse;

     */
    public function generateErrorResponse($error, $debug = false)
    {
        $json = array();

        // Show received parameters
        if($debug)
        {
            // Request body
            $json["request"] = $this->data;
        }

        // Response body
        $json['response']['error'] = intval($error);
        $json['response']['description'] = self::getErrorDescription($error);

        $response = CorsUtility::createCorsResponse($json);
        return $response;
    }

    /**
     * @param mixed $data
     * @param boolean $debug
     * @return \Symfony\Component\HttpFoundation\JsonResponse;

     */
    public function generateResponse($data = false, $debug = false)
    {
        $json = array();

        // Show received parameters
        if($debug)
        {
            // Request body
            $json["request"] = $this->data;
        }

        // Response body
        $json['response']['error'] = 0;
        $json['response']['description'] = self::getErrorDescription(0);
        if(is_array($data)){
            $json['response']['data'] = $data;
        }

        $response = CorsUtility::createCorsResponse($json);
        return $response;
    }

    /**
     * Generate resulting array from all the objects to be displayed
     *
     * @param mixed $objectArray
     * @param array $displayParams
     * @return array
     */
    public function generateData($objectArray, $displayParams)
    {
        $data = array();

        foreach ($objectArray as $object)
        {
            $row = array();

            // Normal attributes
            foreach($displayParams as $p)
            {
                $func = 'get' . ucfirst($p);
                $row[$p] = $object->$func();
            }

            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get error description based on diagnosed problem
     *
     * @param int $error
     * @return string
     */
    private function getErrorDescription($error)
    {
        $description = 'Unknown error';

        switch($error){
            case 0:
                $description = 'Success';
                break;
            case 1:
                $description = 'Invalid HTTP Method';
                break;
            case 2:
                $description = 'Invalid request content-type';
                break;
            case 3:
                $description = 'Malformed request: Empty request body';
                break;
            case 4:
                $description = 'Malformed request: Bad JSON';
                break;
            case 5:
                $description = 'Malformed request: Missing parameters';
                break;
            case 6:
                $description = 'Object requested was not found in database.';
                break;
            case 7:
                $description = 'Method disabled.';
                break;
            case 8:
                $description = 'Relation was not found in database.';
                break;
            case 9:
                $description = 'File not allowed.';
                break;
            case 10:
                $description = 'Authentication error, username not found in database.';
                break;
            case 11:
                $description = 'Authentication error, please check username and password combination.';
                break;
            case 12:
                $description = 'Access forbidden';
                break;
            case 14:
                $description = 'insufficient credits';
                break;
            case 15:
                $description = 'email already exist';
                break;
            case 16:
                $description = 'need to buy this first';
                break;
            case 99:
                $description = 'Unknown internal error';
                break;
        }

        return $description;
    }
}

