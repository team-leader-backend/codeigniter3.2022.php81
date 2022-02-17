<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['default_controller'] = 'main';

//$route['test'] = 'test/index';
//$route['api/(:any)'] = '$1/index';
//$route['api/(:any)/(:any)'] = '$1/$2';
//$route['api/(:any)/(:any)/(:any)'] = '$1/$2/$3';
//$route['api/(:any)/(:any)/(:any)/(:any)'] = '$1/$2/$3/$4';
//$route['api/(:any)/(:any)/(:any)/(:any)/(:any)'] = '$1/$2/$3/$4/$5';
////
//$route['(:any)'] = 'My404/index';
//$route['(:any)/(:any)'] = 'My404/index';
//$route['(:any)/(:any)/(:any)'] = 'My404/index';

// Имя контроллера до двойного подчеркивания, действие - после двойного подчеркивания
//$route['([a-zA-Z0-9]+)'] = function ($product_type)
//{
//    return rtrim($product_type,'/').'/';
//};
//// Имя контроллера до двойного подчеркивания, действие - после двойного подчеркивания, параметр через слэш
//$route['([a-z0-9]+__[a-z0-9]+)/(:any)'] = function ($product_type, $res2)
//{
//
//    return '' . strtolower($product_type) . '/' . $res2;
//};
//// Имя контроллера до двойного подчеркивания, действие - после двойного подчеркивания, параметр через слэш, второй параметр после второго слэша
//$route['([a-z0-9]+__[a-z0-9]+)/(:any)/(:any)'] = function ($product_type, $res2, $res3)
//{
//
//    return '' . strtolower($product_type) . '/' . $res2 . '/' . $res3;
//};

$route['404_override'] = 'My404';
$route['translate_uri_dashes'] = FALSE;