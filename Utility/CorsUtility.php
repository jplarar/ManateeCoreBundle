<?php

namespace Manatee\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CorsUtility
{
    /**
     * Create a valid CORS JSON Response
     *
     * @return JsonResponse
     */
    public static function createCorsResponse($data=null)
    {
        $response = new JsonResponse();
        // TODO : Allow only BMA domains
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'content-type, x-authorization');
        $response->headers->set('Access-Control-Max-Age', '1728000');

        if ($data) $response->setData($data);

        return $response;
    }

    /**
     * Check if a request requires a CORS Pre-Flight response
     *
     * @param Request $request
     * @return bool
     */
    public static function requiresPreFlight(Request $request)
    {
        // We are making this very verbose in case we need to create more tests in the future
        if ($request->getMethod() == 'OPTIONS') {
            return true;
        } else {
            return false;
        }
    }
}