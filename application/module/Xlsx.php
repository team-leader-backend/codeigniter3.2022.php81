<?php
   class Xlsx extends CI_Module {
		Public function __construct(){
            parent::__construct();
		}
       public function index(){
		    return true;
       }
       public function parseXLSX($filename){
           if ($xlsx = self::parseFile($filename)) {
               $result = $xlsx->rows();
               return $result;
           } else {
               echo self::parseError();
               die();
           }
       }
       public function tmpTOnomen($nomen,$discount=0,$plata=1,$mydiscount=0){
		    if($plata==0) $platadiscount = 6; else $platadiscount = 0;
		    $this->load->database();
		    foreach($nomen as $n){
		        $art = $n[0]; $num = $n[1]; $discount = (float)$discount; $mydiscount = (float)$mydiscount;
		        $query = $this->db->query("SELECT n.id, n.articul,n.title_device,b.Description as base_ed, n.num_from_pack,n.base_price,NULL AS discount_price, '$num' AS kolvo, n.kpp FROM nomenclature AS n JOIN base_ed AS b ON n.base_ed=b.Ref WHERE n.articul LIKE '$art'");
		        $nomentab = $query->row_array();
		        if($nomentab) {
                    $nomentab['base_price'] = (float)preg_replace('/\s/u', '', preg_replace('/\,/u', '.', preg_replace('/^[0-9],\./u', '', $nomentab['base_price'])));
                    if($nomentab['kpp']=='false') {
                        $nomentab['discount_price'] = (float)number_format($nomentab['base_price'] * (1 - ($discount / 100)) * (1 - ($platadiscount / 100)) * (1 - ($mydiscount / 100)), 2, '.', '');
                    }else{
                        $nomentab['discount_price'] = $nomentab['base_price'];
                    }
                    $nomentab['discount'] = $discount;
                    $nomentab['platadiscount'] = $platadiscount;
                    $nomentab['mydiscount'] = $mydiscount;
                    $nomentab_array[] = $nomentab;
                }
            }
		   return $nomentab_array;
       }
   }