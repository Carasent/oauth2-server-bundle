<?php

namespace OAuth2\ServerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class VerifyController
{
    private $server;
    private $request;
    private $response;

    public function __construct(
        \OAuth2\Server $server,
        \OAuth2\HttpFoundationBridge\Request $request,
        \OAuth2\HttpFoundationBridge\Response $response
    ) {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * This is called with an access token, details
     * about the access token are then returned.
     * Used for verification purposes.
     *
     * @Route("/verify", name="_verify_token")
     */
    public function verifyAction()
    {
        if (!$this->server->verifyResourceRequest($this->request, $this->response)) {
            return $this->server->getResponse();
        }

        $tokenData = $this->server->getAccessTokenData($this->request, $this->response);

        return new JsonResponse($tokenData);
    }
}
