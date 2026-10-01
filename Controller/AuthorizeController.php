<?php

namespace OAuth2\ServerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AuthorizeController
{
    private $server;
    private $request;
    private $response;
    private $scopeStorage;

    public function __construct(
        \OAuth2\Server $server,
        \OAuth2\HttpFoundationBridge\Request $request,
        \OAuth2\HttpFoundationBridge\Response $response,
        \OAuth2\ServerBundle\Storage\Scope $scopeStorage
    ) {
        $this->server = $server;
        $this->request = $request;
        $this->response = $response;
        $this->scopeStorage = $scopeStorage;
    }

    /**
     * @Route("/authorize", name="_authorize_validate")
     * @Method({"GET"})
     * @Template("OAuth2ServerBundle:Authorize:authorize.html.twig")
     */
    public function validateAuthorizeAction()
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

        return array('qs' => $qs, 'scopes' => $scopes);
    }

    /**
     * @Route("/authorize", name="_authorize_handle")
     * @Method({"POST"})
     */
    public function handleAuthorizeAction()
    {
        return $this->server->handleAuthorizeRequest($this->request, $this->response, true);
    }
}
