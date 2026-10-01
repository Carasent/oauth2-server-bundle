<?php

namespace OAuth2\ServerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TokenController
{
    private $server;
    private $clientCredentialsGrant;
    private $authorizationCodeGrant;
    private $refreshTokenGrant;
    private $userCredentialsGrant;
    private $request;
    private $response;

    public function __construct(
        \OAuth2\Server $server,
        \OAuth2\GrantType\ClientCredentials $clientCredentialsGrant,
        \OAuth2\GrantType\AuthorizationCode $authorizationCodeGrant,
        \OAuth2\GrantType\RefreshToken $refreshTokenGrant,
        \OAuth2\GrantType\UserCredentials $userCredentialsGrant,
        \OAuth2\HttpFoundationBridge\Request $request,
        \OAuth2\HttpFoundationBridge\Response $response
    ) {
        $this->server = $server;
        $this->clientCredentialsGrant = $clientCredentialsGrant;
        $this->authorizationCodeGrant = $authorizationCodeGrant;
        $this->refreshTokenGrant = $refreshTokenGrant;
        $this->userCredentialsGrant = $userCredentialsGrant;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * This is called by the client app once the client has obtained
     * an authorization code from the Authorize Controller (@see OAuth2\ServerBundle\Controller\AuthorizeController).
     * returns a JSON-encoded Access Token or a JSON object with
     * "error" and "error_description" properties.
     *
     * @Route("/token", name="_token")
     */
    public function tokenAction()
    {
        // Add Grant Types
        $this->server->addGrantType($this->clientCredentialsGrant);
        $this->server->addGrantType($this->authorizationCodeGrant);
        $this->server->addGrantType($this->refreshTokenGrant);
        $this->server->addGrantType($this->userCredentialsGrant);

        return $this->server->handleTokenRequest($this->request, $this->response);
    }
}
