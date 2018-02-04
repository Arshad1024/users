<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Feb-18
 * Time: 9:07 AM
 */

namespace AccountService;

use Silex\Application;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException as exception;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;



class UserModel
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function addUser( $data)
    {
        try
        {
            $response = $this->app['db']->insert('users', $data);

            // dispatch event so other listeners can perform actions
            if($response == 1)
            {
                $dispatcher = new EventDispatcher();
                $event = new GenericEvent('NewUser',['data'=>$data]);
                $dispatcher->dispatch('UserAdded', $event);
            }
            return $response;
        }
        catch (exception $e)
        {
            return $e->getErrorCode();
        }
    }

    public function searchUser(Application $app, $phrase)
    {

    }

    public function getUser(Application $app, $id)
    {

    }
}