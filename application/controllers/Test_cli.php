<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test_cli extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            exit('CLI only');
        }
        $this->load->database();
        $this->load->model('product_model');
        $this->load->helper('function_helper');
    }

    public function test()
    {
        $id = 1836;
        $variants = get_variants_values_by_pid($id);
        echo "=== VARIANTS FOR PRODUCT $id ===\n";
        print_r($variants);
    }
}
