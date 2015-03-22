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
        $updateParameters = array('username', 'password', 'fullName', 'email', 'phoneNumber', 'country', 'city',
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

        $user->setRole('ROLE_SUPER_ADMIN');

        // Save changes
        $entityManager->persist($user);
        $entityManager->flush();

        ## 4. Display information
        $displayParams = array('userId', 'name', 'email', 'username', 'status', 'role', 'formattedTimestamp');
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
        $requestParameters = array('userId');
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
        $repository = $entityManager->getRepository('NivaWolfCoreBundle:User');

        $user = $repository->find($api->getParameter('userId'));

        if(!$user)
        {
            $response = $api->generateErrorResponse(6);
            return $response;
        }

        // Parse parameters
        $updateParameters = array('name', 'email', 'username', 'status');

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
        $displayParams = array('userId', 'name', 'email', 'username', 'status', 'formattedTimestamp');
        $data = $api->generateData(array($user), $displayParams);

        ## 5. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }



}