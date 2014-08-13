<?php
namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultController implements ControllerProviderInterface
{

    public function connect(Application $app) {
        $authController = $app['controllers_factory'];
        $authController->match('/', array($this, 'index'))->bind('main_page');
        return $authController;
    }

    public function index(Application $app, Request $request) {
        return $app['twig']->render('default/index.twig', array());
    }

}
