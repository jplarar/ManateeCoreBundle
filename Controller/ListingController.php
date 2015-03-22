<?php

namespace Manatee\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Manatee\CoreBundle\Entity\Listing;
use Manatee\CoreBundle\Utility\CorsUtility;
use Manatee\CoreBundle\Utility\ApiUtility;

/**
 * Class ListingController
 * @package Manatee\CoreBundle\Controller
 */
class ListingController extends Controller
{
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
        $requestParameters = array('categoryId', 'name', 'content', 'area',
            'schedule', 'price');

        $error = $api->validateRequest($requestParameters);
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();
        $listing = new Listing();

        // Parse parameters
        $updateParameters = array('name', 'content', 'area', 'schedule', 'price');
        foreach ($updateParameters as $p)
        {
            $func = 'set' . ucfirst($p);
            $listing->$func($api->getParameter($p));
        }
        $listing->setUserId($this->getUser());

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $entityManager->getRepository('ManateeCoreBundle:Category');
        /** @var \Manatee\CoreBundle\Entity\Category $category */
        $category = $repository->find($api->getParameter('categoryId'));
        $listing->setCategoryId($category);

        // Save changes
        $entityManager->persist($listing);
        $entityManager->flush();

        ## 4. Display information
        $displayParams = array('name', 'content', 'area', 'schedule', 'price');
        $data = $api->generateData(array($listing), $displayParams);

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    /**
     * Modify existing Listing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editAction(Request $request)
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

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $entityManager->getRepository('ManateeCoreBundle:Listing');
        /** @var Listing $listing */
        $listing = $repository->find($api->getParameter('listingId'));

        // Parse parameters
        $updateParameters = array('name', 'content', 'area', 'schedule', 'price');

        foreach ($updateParameters as $p)
        {
            if($api->hasParameter($p)) {
                $func = 'set' . ucfirst($p);
                $listing->$func($api->getParameter($p));
            }
        }

        if ($api->hasParameter('categoryId')) {
            /* @var \Doctrine\ORM\EntityRepository $repository */
            $repository = $entityManager->getRepository('ManateeCoreBundle:Category');
            /** @var \Manatee\CoreBundle\Entity\Category $category */
            $category = $repository->find($api->getParameter('categoryId'));
            $listing->setCategoryId($category);
        }

        // Save changes
        $entityManager->flush();

        ## 4. Display information
        $displayParams = array('listingId', 'name', 'content', 'area', 'schedule', 'price');
        $data = $api->generateData(array($listing), $displayParams);

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    /**
     * Specific Listing entity without user data
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function viewAction(Request $request)
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

        /* @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $entityManager->getRepository('ManateeCoreBundle:Listing');
        /** @var \Manatee\CoreBundle\Entity\Listing $listing */
        $listing = $repository->find($api->getParameter('listingId'));

        if (!$listing) {
            $response = $api->generateErrorResponse(6);
            return $response;
        }

        ## 4. Display information
        $displayParams = array('name', 'content', 'area', 'schedule', 'price');
        $data = array();

        $row = array();

        // Normal attributes
        foreach($displayParams as $p)
        {
            $func = 'get' . ucfirst($p);
            $row[$p] = $listing->$func();
        }

        // Execute the main query
        $sql = <<<ENDSQL
SELECT
	u.phoneNumber, u.email, u.skype
FROM Users as u
INNER JOIN Listings as l ON l.userId = u.userId
INNER JOIN PointLogs as pl ON pl.listingId = l.listingId
WHERE pl.userId = :userId
ENDSQL;

        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->bindValue('userId', $this->getUser()->getUserId());
        $stmt->execute();
        $user = $stmt->fetchAll();

        $listingUser = $listing->getUserId();
        $row['userId'] = $listingUser->getUserId();
        $row['fullName'] = $listingUser->getFullName();
        $row['country'] = $listingUser->getCountry();
        $row['city'] = $listingUser->getCity();
        $row['zipcode'] = $listingUser->getZipcode();

        if ($user){
            $row['phoneNumber'] = $user[0]['phoneNumber'];
            $row['email'] = $user[0]['email'];
            $row['skype'] = $user[0]['skype'];
        }

        $data[] = $row;

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    /**
     * Search Listing by keywords
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchAction(Request $request)
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
        $requestParameters = array('q');

        $error = $api->validateRequest($requestParameters);

        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->getDoctrine()->getRepository('ManateeCoreBundle:Listing');

        // Create Query builder object
        $qb = $repository->createQueryBuilder('l');
        $qb
            ->innerJoin('l.categoryId', 'c')
            ->where('l.name LIKE :search OR l.content LIKE :search OR c.name LIKE :search')
            ->setParameter('search', '%' . $api->getParameter('q') . '%')
            ->orderBy('l.timestamp', 'ASC');

        $listings = $qb->getQuery()->getResult();

        ## 4. Process info
        $displayParams = array('listingId', 'name', 'content', 'area',
            'schedule', 'price', 'formattedTimestamp', 'categoryId');

        $data = array();

        /** @var Listing $listing */
        foreach ($listings as $listing)
        {
            $row = array();

            // Normal attributes
            foreach($displayParams as $p)
            {
                $func = 'get' . ucfirst($p);
                $row[$p] = $listing->$func();
            }
            $row['subcategoryId'] = $listing->getCategoryId()->getCategoryId();
            $row['categoryId'] =$listing->getCategoryId()->getParent()->getCategoryId();
            $data[] = $row;
        }

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

}