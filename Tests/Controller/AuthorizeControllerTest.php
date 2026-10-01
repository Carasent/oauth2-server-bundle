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
        $controller = new AuthorizeController(
            $container->get('oauth2.server'),
            $request,
            $container->get('oauth2.response'),
            $container->get('oauth2.storage.scope')
        );

        $params = $controller->validateAuthorizeAction();

        $this->assertArrayHasKey('nonce', $params['qs'], 'optional included param');
        $this->assertArrayNotHasKey('foo', $params['qs'], 'invalid included param');
        $this->assertArrayNotHasKey('redirect_uri', $params['qs'], 'optional excluded param');

        $loader = new FilesystemLoader(__DIR__.'/../../Resources/views');
        $twig = new Environment($loader);
        $template = $twig->load('Authorize/authorize.html.twig');
        $html = $template->render($params);

        $this->assertStringContainsString(htmlentities(http_build_query($params['qs'])), $html);
    }
}
