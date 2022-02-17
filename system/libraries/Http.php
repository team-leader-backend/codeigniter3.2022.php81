<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CI_Http extends CI_Input {
    public function jsonpost(){
        $input_array = json_decode(file_get_contents('php://input'), true);
        return $input_array;
    }
}
