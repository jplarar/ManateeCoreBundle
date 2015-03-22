<?php

namespace Manatee\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Manatee\CoreBundle\Entity\PointLog;
use Manatee\CoreBundle\Utility\CorsUtility;
use Manatee\CoreBundle\Utility\ApiUtility;

/**
 * Class ListingController
 * @package Manatee\CoreBundle\Controller
 */
class PointLogController extends Controller
{

    /**
     * Generate new PointLog
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
        $requestParameters = array('listingId');

        $error = $api->validateRequest($requestParameters);
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();
        $pointLog = new PointLog();

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $entityManager->getRepository('ManateeCoreBundle:Listing');
        /** @var \Manatee\CoreBundle\Entity\Listing $listing */
        $listing = $repository->find($api->getParameter('listingId'));

        if ($this->getUser()->getCredits() < $listing->getPrice()) {
            $response = $api->generateErrorResponse(14);
            return $response;
        }

        $pointLog->setUserId($this->getUser());
        $pointLog->setListingId($listing);
        $pointLog->setAmount($listing->getPrice());

        $this->getUser()->payCredits($listing->getPrice());

        $sellingUser = $listing->getUserId();
        $sellingUser->addCredits($listing->getPrice());

        // Save changes
        $entityManager->persist($pointLog);
        $entityManager->flush();

        //TODO: Display data

        ## 4. Return payload
        $response = $api->generateResponse();
        return $response;
    }

}