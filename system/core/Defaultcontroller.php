<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Defaultcontroller
{
    function auth_d() {
        header('Location: /authentication');
    }
}