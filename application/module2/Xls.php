<?php
   class Xls extends CI_Module2 {
		Public function __construct(){
            parent::__construct();
		}
       public function index(){
		    return true;
       }
       public function parseXLS($filename){
           if ($xls = self::parseFile($filename)) {
               $result = $xls->rows();
               return $result;
           } else {
               echo self::parseError();
               die();
           }
       }
   }