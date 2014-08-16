<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider;

$app = new Silex\Application();
$app['debug'] = true;
require __DIR__ . '/../config/config.php';

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../src/views',
));

$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

$app->register(new Provider\DoctrineServiceProvider(),
    array('db.options' =>
        array(
            'driver' => $app["db.driver"],
            'host' => $app["db.host"],
            'dbname' => $app["db.dbname"],
            'user' => $app["db.user"],
            'password' => $app["db.password"],
            'charset' => $app["db.charset"]
        )
    )
);
$app->register(new Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured_area' => array(
            'pattern' => '^.*$',
            'anonymous' => true,
            'remember_me' => array(),
            'form' => array(
                'login_path' => '/user/login',
                'check_path' => '/user/login_check',
                'default_target_path'=> '/user',
                // 'failure_path'=> '/user/login',
            ),
            'logout' => array(
                'logout_path' => '/user/logout',
                'target'=> '/user/login'
            ),
            'users' => $app->share(function($app) {
                return $app['user.manager'];
            }),
        ),
    ),
    'security.access_rules' => array(
        array('^/users/register$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/auth.+$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        // array('^/.+$', 'ROLE_ADMIN'),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
));


// Note: As of this writing, RememberMeServiceProvider must be registered *after* SecurityServiceProvider or SecurityServiceProvider
// throws 'InvalidArgumentException' with message 'Identifier "security.remember_me.service.secured_area" is not defined.'
$app->register(new Provider\RememberMeServiceProvider());

// These services are only required if you use the optional SimpleUser controller provider for form-based authentication.
$app->register(new Provider\ServiceControllerServiceProvider());

// Register the SimpleUser service provider.
$app->register($u = new SimpleUser\UserServiceProvider());

// Optionally mount the SimpleUser controller provider.
$app->mount('/user', $u);


$app['user.controller']->setLayoutTemplate('layout.twig');

$app->mount('/', new Controller\DefaultController());

$app->run();

?>
