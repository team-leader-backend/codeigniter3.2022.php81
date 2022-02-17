<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        // This autocreate class model from name controller
        $classname = mb_strtolower(get_class());
        $docroot = $_SERVER['DOCUMENT_ROOT'];
        if(file_exists($docroot.'/application/models/model_'.$classname)) {
        $this->load->model('model_'.$classname);
        $classname = mb_strtolower(get_class());
        $this->load->middleware('middleware_'.$classname);
        }else{
            $nulled = file_get_contents($docroot.'/application/models/Nulled.php');
            $name = ucfirst('model_'.$classname);
            $newfile = str_replace('Nulled', $name, $nulled);
            file_put_contents($docroot.'/application/models/'.$name.'.php', $newfile);
        }
    }

    public function index()
    {
        echo 'Main controller';
    }
}
