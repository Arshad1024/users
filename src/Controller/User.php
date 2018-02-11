<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Feb-18
 * Time: 10:27 AM
 */

namespace AccountService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Validate;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;






class UserController implements ControllerProviderInterface
{

/* Default function */
/* @params Application $appInstance*/
/* @return */
    public function connect(Application $appInstance)
    {

        $collection = $appInstance['controllers_factory'];
        $collection->get('/{id}','\AccountService\UserController::_getUser')->assert('id','\d+');
        $collection->get('/search/{phrase}','\AccountService\UserController::_searchUser');
        $collection->put('/add','AccountService\UserController::addUser');
        $collection->options('/add',function(){

            return new Response('',200,["Allow"=>"PUT"]);

        });
        return $collection;
    }

    public function addUser(Application $app, Request $request)
    {

        $data = array("firstname"=>$request->get('firstname'),"lastname"=>$request->get('lastname'),
            "email"=>$request->get('email'),"dob"=>$request->get('dob'));

        $filters = new Validate\Collection(array(
            "firstname"=>array(new Validate\NotBlank(),new Validate\Regex("/^[a-zA-Z. ]{2,30}$/")),
            "lastname"=>array(new Validate\NotBlank(),new Validate\Regex("/^[a-zA-Z. ]{2,30}$/")),
            "email"=>array(new Validate\NotBlank(), new Validate\Email()),
            "dob"=>array(new Validate\NotBlank(), new Validate\Date())
        ));

        $errors = $app['validator']->validate($data,$filters);

        if(count($errors) > 0)
        {
            return $app['errorHandler']::processValidationErrors($errors);
        }
        else
        {
            $response = $app['userModal']->addUser($data);
            if($response == 1)
            {
                return new Response(json_encode(["message"=>"success"]),201,['Content-Type'=>'application/json']);
            }
            else if($response == 1062)
            {
                return $app['errorHandler']::processDuplicateEntry();
            }
            else
            {
                return $app['errorHandler']::unavailableService($response);
            }
        }

    }

    public function _getUser(Application $app, $id)
    {



    }

    public function _searchUser(Application $app, $phrase)
    {

    }


}