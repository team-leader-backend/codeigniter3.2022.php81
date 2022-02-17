<?php
   class Nulled extends CI_Model {
		Public function __construct(){ 
		parent::__construct();
            $this->load->database();
		}
       public function index(){
		    return true;
       }
   }