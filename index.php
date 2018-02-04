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
$app['debug'] = debug_mode;


$app['userModal'] = function($app){
    return new AccountService\UserModel($app);
};

$app['errorHandler'] = function($app){
    return new AccountService\ErrorHandler($app);
};



$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider);
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => DB_host,
    'dbname'   => DB_name,
    'user'     => DB_username,
    'password' => DB_password,
);

$app->get('/',function(){

    return "Valid Request : /user/";


});
$app->mount('/user',new \AccountService\UserController());


$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods','PUT,GET,POST');
   });

$app->run();



