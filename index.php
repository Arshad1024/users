<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Feb-18
 * Time: 8:58 AM
 */

require_once __DIR__.'/vendor/autoload.php';


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app = new Silex\Application();

# change this in config.php
$app['debug'] = debug_mode;



# Injecting user model to access DB
$app['userModal'] = function($app){
    return new AccountService\UserModel($app);
};


# Injecting error handling class
$app['errorHandler'] = function($app){
    return new AccountService\ErrorHandler($app);
};

# Checks for IP address, if defined in config.php, it will return 403
$app->before(function(Request $request){

    if(whiteListedIps !== "")
    {
        $requestMatch = new \Symfony\Component\HttpFoundation\RequestMatcher('/',null,null,explode(',',whiteListedIps));
        if(!$requestMatch->matches($request))
        {
            return new Response('<h1>Forbidden</h1><p>You do not have permissions to access this resource</p>',403);
        }
    }
});


# registering some services here
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider);
$app->register(new Silex\Provider\ValidatorServiceProvider());


# define db credentials in config.php
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => DB_host,
    'dbname'   => DB_name,
    'user'     => DB_username,
    'password' => DB_password,
);


# base url can be anything
$app->get('/',function(){
    return "Valid Request : /user/";
});


# mounting controller for /user paths
$app->mount('/user',new \AccountService\UserController());


# adding CORS etc at the end
$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods','PUT,GET,POST');
   });


# let it run :)
$app->run();



