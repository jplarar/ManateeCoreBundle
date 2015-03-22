<?php

namespace Manatee\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Manatee\CoreBundle\Utility\CorsUtility;
use Manatee\CoreBundle\Utility\ApiUtility;

/**
 * Class ListingController
 * @package Manatee\CoreBundle\Controller
 */
class LeaderboardController extends Controller
{
    /**
     * Generate new Listing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listingsAction(Request $request)
    {
        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }

        ## 2. Validate request
        $api = new ApiUtility($request);

        $error = $api->validateRequest();
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $sql = <<<ENDSQL
SELECT
	COUNT(*) as buys, l.listingId, l.name
FROM Listings as l
INNER JOIN PointLogs as pl ON pl.listingId = l.listingId
WHERE pl.listingId IS NOT NULL
GROUP BY l.listingId
LIMIT 10
ENDSQL;
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll();

        ## 4. Display information
        $data = array();
        $data[] = $topUsers;

        ## 3. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    /**
     * Modify existing Listing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function usersAction(Request $request)
    {
        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }
        
        ## 2. Validate request
        $api = new ApiUtility($request);

        $error = $api->validateRequest();
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $sql = <<<ENDSQL
SELECT
	COUNT(*) buys, u.userId, u.fullName
FROM Users as u
INNER JOIN PointLogs as pl ON pl.userId = u.userId
WHERE pl.listingId IS NOT NULL
GROUP BY u.userId
LIMIT 10
ENDSQL;
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $topUsers = $stmt->fetchAll();

        ## 4. Display information
        $data = array();
        $data[] = $topUsers;

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

}