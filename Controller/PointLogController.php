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
        $requestParameters = array('listingId', 'userId');

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
        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $entityManager->getRepository('ManateeCoreBundle:User');
        /** @var \Manatee\CoreBundle\Entity\User $user */
        $user = $repository->find($api->getParameter('userId'));

        //TODO: Check credits before buy

        $pointLog->setUserId($user);
        $pointLog->setListingId($listing);
        $pointLog->setAmount($listing->getPrice());

        $userBalance = $user->getCredits();
        $user->setCredit($userBalance - $listing->getPrice());

        // Save changes
        $entityManager->persist($pointLog);
        $entityManager->flush();

        //TODO: Display data

        ## 4. Return payload
        $response = $api->generateResponse();
        return $response;
    }

}