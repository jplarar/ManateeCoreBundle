<?php

namespace Manatee\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Manatee\CoreBundle\Entity\User;
use Manatee\CoreBundle\Utility\CorsUtility;
use Manatee\CoreBundle\Utility\ApiUtility;

/**
 * Class UserController
 * @package Manatee\CoreBundle\Controller
 */
class UserController extends Controller
{
    /**
     * Generate new User
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
        $requestParameters = array('username', 'password', 'fullName', 'email', 'phoneNumber', 'country', 'city',
            'zipcode', 'skype');

        $error = $api->validateRequest($requestParameters);
        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        // Parse parameters
        $updateParameters = array('username', 'fullName', 'email', 'phoneNumber', 'country', 'city',
            'zipcode', 'skype');
        foreach ($updateParameters as $p)
        {
            $func = 'set' . ucfirst($p);
            $user->$func($api->getParameter($p));
        }

        // Special parameters
        // Password
        $factory = $this->get('security.encoder_factory');
        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $factory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($api->getParameter('password'), $user->getSalt());
        $user->setPassword($encodedPassword);

        // Save changes
        $entityManager->persist($user);
        $entityManager->flush();

        ## 4. Display information
        $displayParams = array('username', 'fullName', 'email', 'phoneNumber', 'country', 'city',
            'zipcode', 'skype');
        $data = $api->generateData(array($user), $displayParams);

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    /**
     * Modify existing User
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
        $error = $api->validateRequest();

        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getUser();

        // Parse parameters
        $updateParameters = array('fullName', 'email', 'phoneNumber', 'country', 'city',
            'zipcode', 'skype');

        foreach ($updateParameters as $p)
        {
            if($api->hasParameter($p)) {
                $func = 'set' . ucfirst($p);
                $user->$func($api->getParameter($p));
            }
        }

        // Special parameters
        if($api->hasParameter('password')){
            // Load security encoder
            $factory = $this->get('security.encoder_factory');
            /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
            $encoder = $factory->getEncoder($user);
            $encodedPassword = $encoder->encodePassword($api->getParameter('password'), $user->getSalt());
            $user->setPassword($encodedPassword);
        }

        // Save changes
        $entityManager->flush();

        ## 4. Display information
        $displayParams = array('username', 'fullName', 'email', 'phoneNumber', 'country', 'city',
            'zipcode', 'skype');
        $data = $api->generateData(array($user), $displayParams);

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    /**
     * Specific User entity
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

        $error = $api->validateRequest();

        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        ## 3. Process information
        $entityManager = $this->getDoctrine()->getManager();

        if ($api->hasParameter('userId')) {
            /* @var \Doctrine\ORM\EntityRepository $repository */
            $repository = $entityManager->getRepository('ManateeCoreBundle:User');
            $user = $repository->find($api->getParameter('userId'));
            if (!$user) {
                $response = $api->generateErrorResponse(6);
                return $response;
            }
            $displayParams = array('username', 'fullName', 'country', 'city', 'zipcode');
        } else {
            $user = $this->getUser();
            $displayParams = array('username', 'fullName', 'email', 'phoneNumber', 'country', 'city',
                'zipcode', 'skype');
        }

        ## 4. Display information
        $data = $api->generateData(array($user), $displayParams);

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

}