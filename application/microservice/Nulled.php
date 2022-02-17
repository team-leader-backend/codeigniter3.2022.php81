<?php
   class Nulled extends CI_Microservice {
		Public function __construct(){ 
		parent::__construct();
            $this->load->database();
		}
       public function index(){
		    return true;
       }
   }