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
class CategoryController extends Controller
{
    /**
     * List Categories and subCategories
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

        $error = $api->validateRequest();
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();
        /* @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $entityManager->getRepository('ManateeCoreBundle:Category');
        $query = $repository->createQueryBuilder('c')
            ->where('c.parent IS NOT NULL')
            ->getQuery();

        $parents = $query->getResult();

        $data = array();
        $displayParams = array('categoryId', 'name', 'imageUrl');
        /** @var \Manatee\CoreBundle\Entity\Category $parent */
        foreach ($parents as $parent) {
            $row = array();
            foreach ($displayParams as $p) {
                $func = 'get' . ucfirst($p);
                $row[$p] = $parent->$func();
            }
            foreach ($parent->getSubcategories() as $children) {
                $row2 = array();
                foreach ($displayParams as $p) {
                    $func = 'get' . ucfirst($p);
                    $row2[$p] = $children->$func();
                }
                $row['children']=$row2;
            }
            $data[] = $row;
        }

        ## 4. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

}