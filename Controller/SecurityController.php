<?php

namespace Manatee\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Manatee\CoreBundle\Utility\CorsUtility;
use Manatee\CoreBundle\Utility\ApiUtility;
use Manatee\CoreBundle\Utility\GuidUtility;
use Manatee\CoreBundle\Entity\SessionLog;

/**
 * Class SecurityController
 * @package Niva\Wolf\CoreBundle\Controller
 */
class SecurityController extends Controller
{
    /**
     * Method used to
     * @param Request $request
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }

        ## 2. Parse content needed for security
        $api = new ApiUtility($request);

        // Obligatory parameters needed for this operation to succeed.
        $requestParameters = array('_username', '_password');

        $error = $api->validateRequest($requestParameters);

        // Return response
        if($error)
        {
            $response = $api->generateErrorResponse($error);
            return $response;
        }

        $userAgent = $request->headers->get('User-Agent');

        ## 3. Authenticate user
        /** @var \Manatee\CoreBundle\Entity\User $user */
        $user = $this->getDoctrine()->getRepository('ManateeCoreBundle:User')
        ->findOneBy(array('email' => $api->getParameter('_username')));

        if(!$user)
        {
            $response = $api->generateErrorResponse(10);
            return $response;
        }

        // Load security encoder
        $factory = $this->get('security.encoder_factory');

        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $factory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($api->getParameter('_password'), $user->getSalt());

        if($encodedPassword != $user->getPassword())
        {
            $response = $api->generateErrorResponse(11);
            return $response;
        }

        ## 4. Check if there's a session for this username and user agent already.
        /** @var \Manatee\CoreBundle\Entity\SessionLog $sessionLog */
        $sessionLog = $this->getDoctrine()->getRepository('ManateeCoreBundle:SessionLog')
            ->findOneBy(array(
                'userAgent' => $userAgent,
                'userId' => $user->getUserId()));

        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        ## 4.1. Generate API key
        $key = GuidUtility::generate(32);

        if(!$sessionLog)
        {
            $sessionLog = new SessionLog();
            $sessionLog->setUserId($user);
            $sessionLog->setUserAgent($userAgent);

            ## 5.1. Persist to database
            $em->persist($sessionLog);
        }

        $sessionLog->setApiKey($key);

        $em->flush();

        ## 6. Display parameters
        $displayParams = array('apiKey');
        $data = $api->generateData(array($sessionLog), $displayParams);

        ## 7. Return payload
        $response = $api->generateResponse($data);
        return $response;
    }

    public function validAction(Request $request)
    {
        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }

        ## 2. Parse content needed for security
        $api = new ApiUtility($request);

        ## 3. Return payload
        $response = $api->generateResponse();
        return $response;
    }

    public function logoutAction(Request $request)
    {

        ## 1. Initialization
        // Enable CORS in this API
        $response = CorsUtility::createCorsResponse();
        if (CorsUtility::requiresPreFlight($request)) {
            return $response;
        }

        ## 2. Parse content needed for security
        $api = new ApiUtility($request);

        ## 3. Return payload
        /** @var \Manatee\CoreBundle\Entity\User $user */
        $user = $this->getUser();

        /** @var \Niva\Wolf\CoreBundle\Entity\SessionLog $session */
        $session = $this->getDoctrine()->getRepository("ManateeCoreBundle:SessionLog")->findOneBy(array(
            'apiKey' => $request->headers->get('x-authorization')
        ));

        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $em->remove($session);

        $em->flush();

        ## 4. Return payload
        $response = $api->generateResponse();
        return $response;
    }

}
