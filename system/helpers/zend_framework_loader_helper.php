<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
//
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.BASEPATH.'extensions/');
require_once('Zend/Loader.php');

Zend_Loader::loadClass('Zend_Acl');
Zend_Loader::loadClass('Zend_Acl_Role');
Zend_Loader::loadClass('Zend_Acl_Resource');
//* This source code was highlighted with Source Code Highlighter.