<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit extends CI_Controller {
    public function index()
    {
        $this->load->library('unit_test');
        $test = 1 + 1;
        $expected_result = 2;
        $test_name = 'Adds one plus one';
        $this->unit->run($test, $expected_result, $test_name,'Тестовый тест');
        $this->unit->run('Foo', 'is_string', 'String test','Это тест строки');
        echo $this->unit->report();
    }
}
