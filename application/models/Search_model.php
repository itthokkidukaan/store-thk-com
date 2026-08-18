<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends CI_Model
{

    public function autoSearch($name)
    {


        $query = $this->db->query("SELECT pid,product_name,product_price FROM geopos_products WHERE UPPER(product_name) LIKE '" . strtoupper($name) . "%'");

        $result = $query->result_array();

        return $result;
    }			public function getvariation($pdID){				$this->db->select('DISTINCT(product_variants.attribute_value_ids) as attriId, product_variants.*, attribute_values.value');            $this->db->where('product_id',$pdID);			$this->db->join ( 'attribute_values', 'attribute_values.id = product_variants.attribute_value_ids' , 'left' );            $querys = $this->db->get('product_variants');			}



public function search_suppliers($keyword)
    {
        $this->db->select('id, name, phone, address, city');
        $this->db->from('geopos_supplier');
        $this->db->like('name', $keyword);
        $this->db->or_like('phone', $keyword);
        $this->db->limit(10);
        return $this->db->get()->result_array();
    }


}

