<?php
//redirect 301
$urlCurrentPath = $_SERVER['REQUEST_URI'];
$urlPath = array(
    '/moscow/companies/rostov-na-donu/gefest' => "http://dvestrahovki.ru/rostov-na-donu/companies/gefest",
    '/krasnodar/gefest' => "http://dvestrahovki.ru/krasnodar/companies/gefest",
    '/moscow/companies/rostov-na-donu/gefest/comments' => "http://dvestrahovki.ru/rostov-na-donu/companies/gefest/comments",
    '/moscow/companies/novosibirsk/nadins' => "http://dvestrahovki.ru/novosibirsk/companies/nadins",
    '/company/ru/moscow/evro-polis' => "http://dvestrahovki.ru/moscow/companies/evro-polis",
    '/vtb' => "http://dvestrahovki.ru/moscow/companies/vtb",
    '/company/ru/moscow/ugsk' => "http://dvestrahovki.ru/moscow/companies/ugsk",
    '/company/ru/nizhny-novgorod/ugsk' => "http://dvestrahovki.ru/nizhny-novgorod/companies/ugsk",
    '/company/ru/rostov-na-donu/zettains' => "http://dvestrahovki.ru/rostov-na-donu/companies/zettains",
    '/company/ru/sankt-peterburg/nig' => "http://dvestrahovki.ru/sankt-peterburg/companies/nig",
    '/company/ru/moscow/vtb' => "http://dvestrahovki.ru/moscow/companies/vtb",
    '/company/ru/krasnodar/gefest' => "http://dvestrahovki.ru/krasnodar/companies/gefest",
    '/company/ru/krasnodar/zhaso' => "http://dvestrahovki.ru/krasnodar/companies/zhaso",
    '/novosibirsk/companies/alfastrah/comments' => "http://dvestrahovki.ru/krasnodar/companies/alfastrah/comments",
    '/moscow/companies/novosibirsk/alfastrah' => "http://dvestrahovki.ru/krasnodar/companies/alfastrah",
    '/moscow/companies/novosibirsk/nadins' => "http://dvestrahovki.ru/novosibirsk/companies/nadins",
    '/sankt-peterburg/liberty' => "http://dvestrahovki.ru/sankt-peterburg/companies/liberty",
    '/moscow/companies/rostov-na-donu' => "http://dvestrahovki.ru/rostov-na-donu/companies",
    '/moscow/companies/Samara/alfastrah/comments' => "http://dvestrahovki.ru/samara/companies/alfastrah/comments",
    '/moscow/companies/Novosibirsk/allianz/comments' => "http://dvestrahovki.ru/novosibirsk/companies/allianz/comments",
);
if (isset($urlPath[$urlCurrentPath])) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$urlPath[$urlCurrentPath]);
    exit();
}
// end redirect 301

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$apcLoader = new ApcClassLoader('sf2', $loader);
$loader->unregister();
$apcLoader->register(true);
*/
umask(0000);

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
