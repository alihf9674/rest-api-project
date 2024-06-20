<?php
#cach constants
define('CACHE_ENABLED',0);
define('CACHE_DIR',__DIR__.'/cache/');

#jwt constants
define('JWT_KEY','aliHtf7learnfjs$n1554fndcn#hvv*mss');
define('JWT_ALG','HS256');


include_once "App/iran.php";
include_once "vendor/autoload.php";


spl_autoload_register(function ($class) {
    $class_file = __DIR__ . "/" . $class . ".php";
    if (!(file_exists($class_file) && is_readable($class_file)))
        die("$class not found");
    include_once $class_file;
});
// use \App\Services\CityService;
// use \App\Utilities\Response;

// new CityService;
// Response::respond([1,23],200);