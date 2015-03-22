<?php

namespace Manatee\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Manatee\CoreBundle\Entity\Review;
use Manatee\CoreBundle\Utility\CorsUtility;
use Manatee\CoreBundle\Utility\ApiUtility;

/**
 * Class ListingController
 * @package Manatee\CoreBundle\Controller
 */
class ReviewController extends Controller
{
    /**
     * List listing reviews
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction(Request $request)
    {

        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }

        ## 2. Validate request
        $api = new ApiUtility($request);

        // Obligatory parameters needed for this operation to succeed.
        $requestParameters = array('listingId');

        $error = $api->validateRequest($requestParameters);

        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Prepare information

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->getDoctrine()->getRepository('ManateeCoreBundle:Review');
        $reviews = $repository->findBy(array(
            'listingId' => $api->getParameter('listingId')
        ));

        if(!is_array($reviews)){
            $reviews = array();
        }

        ## 4. Process info
        $displayParams = array('content', 'rating');
        $data = array();

        /** @var Review $review */
        foreach ($reviews as $review) {
            $row = array();

            // Normal attributes
            foreach ($displayParams as $p) {
                $func = 'get' . ucfirst($p);
                $row[$p] = $review->$func();
            }
            $listingUser = $review->getUserId();
            $row['fullName'] = $listingUser->getFullName();
            $data[] = $row;
        }

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }
    
    /**
     * Generate new Listing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newAction(Request $request)
    {
        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }

        ## 2. Validate request
        $api = new ApiUtility($request);

        // Obligatory parameters needed for this operation to succeed.
        $requestParameters = array('content', 'rating');

        $error = $api->validateRequest($requestParameters);
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();
        $review = new Review();

        // Parse parameters
        $updateParameters = array('content', 'rating');
        foreach ($updateParameters as $p)
        {
            $func = 'set' . ucfirst($p);
            $review->$func($api->getParameter($p));
        }
        $review->setUserId($this->getUser());

        // Save changes
        $entityManager->persist($review);
        $entityManager->flush();

        ## 4. Display information
        $displayParams = array('content', 'rating');
        $data = array();
        $row = array();

        // Normal attributes
        foreach($displayParams as $p)
        {
            $func = 'get' . ucfirst($p);
            $row[$p] = $review->$func();
        }

        $listingUser = $review->getUserId();
        $row['fullName'] = $listingUser->getFullName();

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

}