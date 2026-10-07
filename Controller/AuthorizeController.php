<?php

namespace OAuth2\ServerBundle\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\HttpFoundationBridge\Response;
use OAuth2\Server;
use OAuth2\ServerBundle\Storage\Scope;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AuthorizeController
{
    private $server;
    private $request;
    private $response;
    private $scopeStorage;
    private $twig;

    public function __construct(
        Server $server,
        Request $request,
        Response $response,
        Scope $scopeStorage,
        Environment $twig
    ) {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
        $this->scopeStorage = $scopeStorage;
        $this->twig = $twig;
    }

    #[Route('/authorize', name: '_authorize_validate', methods: ['GET'])]
    public function validateAuthorizeAction(): SymfonyResponse|Response
    {
        if (!$this->server->validateAuthorizeRequest($this->request, $this->response)) {
            return $this->server->getResponse();
        }

        // Get descriptions for scopes if available
        $scopes = array();
        foreach (explode(' ', $this->request->query->get('scope')) as $scope) {
            $scopes[] = $this->scopeStorage->getDescriptionForScope($scope);
        }

        $qs = array_intersect_key(
            $this->request->query->all(),
            array_flip(explode(' ', 'response_type client_id redirect_uri scope state nonce'))
        );

        return new SymfonyResponse($this->twig->render('@OAuth2Server/Authorize/authorize.html.twig', [
            'qs' => $qs,
            'scopes' => $scopes,
        ]));
    }

    #[Route('/authorize', name: '_authorize_handle', methods: ['POST'])]
    public function handleAuthorizeAction(): Response
    {
        return $this->server->handleAuthorizeRequest($this->request, $this->response, true);
    }
}
