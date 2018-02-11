<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 03-Feb-18
 * Time: 8:35 AM
 */

namespace AccountService;

use Symfony\Component\HttpFoundation\Response;
use Silex\Application;


class ErrorHandler
{

    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public static function processValidationErrors($errors,$code=400)
    {
        $response = array();
        $errorFields = array();

        foreach($errors as $error)
        {
           $response[] = "Validation error in ".str_ireplace(['[',']'],'',$error->getPropertyPath());
            $errorFields[] = str_ireplace(['[',']'],'',$error->getPropertyPath());
        }

        return new Response(json_encode(["message"=>$response]),$code,['Content-Type'=>'application/json','X-Status-Reason'=>'Validation Failed','X-Validation-Errors'=>$errorFields]);
    }

    public static function processDuplicateEntry()
    {
        return new Response(json_encode(['message'=>'Duplicate Entry']),409,['Content-Type'=>'application/json','X-Status-Reason'=>'Duplicate Entry in Email or Phone']);
    }

    public static function unavailableService($code)
    {
        if(debug_mode == false)
        {
            unset($code);
        }
        return new Response(json_encode(['message'=>'Please Try Again Later']),503,['Content-Type'=>'application/json','X-Status-Reason'=>'Could not create Record :'.$code]);

    }
}