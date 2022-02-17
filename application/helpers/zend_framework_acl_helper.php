<?php
function init_roles() {
    $acl = new Zend_Acl();

    //определяем ресурсы/контроллеры
    $acl->add(new Zend_Acl_Resource('login'));
    $acl->add(new Zend_Acl_Resource('welcome'));
    $acl->add(new Zend_Acl_Resource('logout'));

    // определяем роли
    $acl->addRole(new Zend_Acl_Role('guest'));
    $acl->addRole(new Zend_Acl_Role('member'));

    //определяем доступ
    $acl->allow('guest','login');
    $acl->deny('guest','welcome');
    $acl->deny('guest','logout');

    $acl->deny('member','login');
    $acl->allow('member','welcome');
    $acl->allow('member','logout');

    return $acl;
}
//* This source code was highlighted with Source Code Highlighter.