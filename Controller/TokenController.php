<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\HttpFoundationBridge\Request;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\Server;
use Symfony\Component\Routing\Annotation\Route;

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
        Server $server,
        ClientCredentials $clientCredentialsGrant,
        AuthorizationCode $authorizationCodeGrant,
        RefreshToken $refreshTokenGrant,
        UserCredentials $userCredentialsGrant,
        Request $request,
        Response $response
    ) {
        $this->server = $server;
        $this->clientCredentialsGrant = $clientCredentialsGrant;
        $this->authorizationCodeGrant = $authorizationCodeGrant;
        $this->refreshTokenGrant = $refreshTokenGrant;
        $this->userCredentialsGrant = $userCredentialsGrant;
        $this->request = $request;
        $this->response = $response;
    }

    #[Route('/token', name: '_token')]
    /**
     * This is called by the client app once the client has obtained
     * an authorization code from the Authorize Controller (@see OAuth2\ServerBundle\Controller\AuthorizeController).
     * returns a JSON-encoded Access Token or a JSON object with
     * "error" and "error_description" properties.
     *
     */
    public function tokenAction(): Response
    {
        // Add Grant Types
        $this->server->addGrantType($this->clientCredentialsGrant);
        $this->server->addGrantType($this->authorizationCodeGrant);
        $this->server->addGrantType($this->refreshTokenGrant);
        $this->server->addGrantType($this->userCredentialsGrant);

        return $this->server->handleTokenRequest($this->request, $this->response);
    }
}
