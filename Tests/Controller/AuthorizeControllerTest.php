<?php

namespace OAuth2\ServerBundle\Tests\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\ServerBundle\Tests\ContainerLoader;
use OAuth2\ServerBundle\Controller\AuthorizeController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AuthorizeControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testOpenIdConfig()
    {
        $container = ContainerLoader::buildTestContainer();

        $clientManager = $container->get('oauth2.client_manager');

        $clientId = 'test-client-' . rand();
        $redirectUri = 'http://brentertainment.com';
        $scope  = 'openid';

        $clientManager->createClient(
          $clientId,
          explode(',', $redirectUri),
          array(),
          explode(',', $scope)
        );

        $request = new Request(array(
            'client_id'     => $clientId,
            'response_type' => 'code',
            'scope'         => 'openid',
            'state'         => 'xyz',
            'foo'           => 'bar',
            'nonce'         => '123',
        ));
        $loader = new FilesystemLoader(__DIR__.'/../../Resources/views');
        $loader->addPath(__DIR__.'/../../Resources/views', 'OAuth2Server');

        $controller = new AuthorizeController(
            $container->get('oauth2.server'),
            $request,
            $container->get('oauth2.response'),
            $container->get('oauth2.storage.scope'),
            new Environment($loader)
        );

        $response = $controller->validateAuthorizeAction();
        $this->assertSame(200, $response->getStatusCode());
        $html = $response->getContent();

        $this->assertStringContainsString('nonce=123', $html, 'optional included param');
        $this->assertStringNotContainsString('foo=bar', $html, 'invalid parameter');
        $this->assertStringNotContainsString('redirect_uri=', $html, 'optional excluded parameter');
    }
}
