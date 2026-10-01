<?php

namespace OAuth2\ServerBundle\Tests\Controller;

use OAuth2\HttpFoundationBridge\Request;
use OAuth2\ServerBundle\Controller\TokenController;
use OAuth2\ServerBundle\Controller\VerifyController;
use OAuth2\ServerBundle\Tests\ContainerLoader;

class ControllerConstructionTest extends \PHPUnit\Framework\TestCase
{
    public function testTokenControllerCanBeConstructedWithContainerServices()
    {
        $container = ContainerLoader::buildTestContainer();

        $controller = new TokenController(
            $container->get('oauth2.server'),
            $container->get('oauth2.grant_type.client_credentials'),
            $container->get('oauth2.grant_type.authorization_code'),
            $container->get('oauth2.grant_type.refresh_token'),
            $container->get('oauth2.grant_type.user_credentials'),
            new Request(array()),
            $container->get('oauth2.response')
        );

        $this->assertInstanceOf(TokenController::class, $controller);
    }

    public function testVerifyControllerCanBeConstructedWithContainerServices()
    {
        $container = ContainerLoader::buildTestContainer();

        $controller = new VerifyController(
            $container->get('oauth2.server'),
            new Request(array()),
            $container->get('oauth2.response')
        );

        $this->assertInstanceOf(VerifyController::class, $controller);
    }
}
