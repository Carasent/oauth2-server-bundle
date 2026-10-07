<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VerifyController
{
    private $server;
    private $request;
    private $response;

    public function __construct(
        Server $server,
        Request $request,
        Response $response
    ) {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
    }

    #[Route('/verify', name: '_verify_token')]
    /**
     * This is called with an access token, details
     * about the access token are then returned.
     * Used for verification purposes.
     *
     */
    public function verifyAction(): JsonResponse|Response
    {
        if (!$this->server->verifyResourceRequest($this->request, $this->response)) {
            return $this->server->getResponse();
        }

        $tokenData = $this->server->getAccessTokenData($this->request, $this->response);

        return new JsonResponse($tokenData);
    }
}
