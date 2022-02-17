<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My404 extends CI_Controller
{
    public function __construct()
    {
    	parent::__construct();
        $this->output->set_status_header('404');
        $data['error_description'] = 'Method not found'; // View name
        $data['error_code'] = 404;
        $this->load->view('index',$data);//loading in my template
    }
    public function index()
    {
    }
}
