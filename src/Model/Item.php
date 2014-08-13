<?php
namespace Model;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class Item
{

    protected $_app;
    protected $_db;

    public function __construct(Application $app) {
        $this->_app = $app;
        $this->_db = $app['db'];
    }
}
