<?php
   class Middleware_category extends CI_Middleware {
		Public function __construct(){ 
		parent::__construct();
            $this->load->database();
		}
       public function index(){
		    return true;
       }
       public function full_filter($filter){
            if(isset($filter['color'])){
                // Фильтруем по цвету
                $color = $filter['color'];
                $result = $this->db->query("SELECT n.title, n.articul FROM parameter_product as pp JOIN nomenclature AS n ON n.articul=pp.articul  WHERE pp.title_values LIKE '$color'")->result_array();
            }
           if(isset($filter['country'])){
               $country = $filter['country'];
               $this->db->select('articul');
               $result = $this->db->query("SELECT n.title, n.articul FROM parameter_product as pp JOIN nomenclature AS n ON n.articul=pp.articul  WHERE pp.title_values LIKE '$country'")->result_array();
           }
           return $result;
       }
   }