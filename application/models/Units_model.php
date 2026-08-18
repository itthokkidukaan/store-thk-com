<?php
/**
 * Geo POS -  Accounting,  Invoicing  and CRM Application
 * Copyright (c) Rajesh Dukiya. All Rights Reserved
 * ***********************************************************************
 *
 *  Email: support@ultimatekode.com
 *  Website: https://www.ultimatekode.com
 *
 *  ************************************************************************
 *  * This software is furnished under a license and may be used and copied
 *  * only  in  accordance  with  the  terms  of such  license and with the
 *  * inclusion of the above copyright notice.
 *  * If you Purchased from Codecanyon, Please read the full License from
 *  * here- http://codecanyon.net/licenses/standard/
 * ***********************************************************************
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Units_model extends CI_Model
{


    public function units_list()
    {
        $query = $this->db->query("SELECT * FROM geopos_units WHERE type=0 ORDER BY id DESC");
        return $query->result_array();
    }


    public function view($id)
    {

        $this->db->from('geopos_units');
        $this->db->where('id', $id);

        $query = $this->db->get();
        $result = $query->row_array();
        return $result;


    }

    public function create($name, $code)
    {
        $data = array(
            'name' => $name,
            'code' => $code,
            'type' => 0
        );

        if ($this->db->insert('geopos_units', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit($id, $name, $code)
    {
        $data = array(
            'name' => $name,
            'code' => $code,
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('geopos_units')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function variations_list()
    {
        $query = $this->db->query("SELECT attributes.name as atrname, attributes.id as atrid, attribute_set.id as atrsid, attribute_set.name as atrsname FROM attributes inner join attribute_set on attributes.attribute_set_id= attribute_set.id ORDER BY attributes.id DESC");
        return $query->result_array();
    }

    public function create_va($name, $type = 0)
    {
        $data = array(
            'name' => $name,
            'type' => $type
        );

        if ($this->db->insert('geopos_units', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit_va($id, $name)
    {
        $data = array(
            'name' => $name,
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('geopos_units')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function variables_list()
    {
        //   $query = $this->db->query("SELECT * FROM geopos_units WHERE type=2 ORDER BY id DESC");
        //    return $query->result_array();
        $this->db->select('u.id,u.value as variation, u2.name AS name');
        $this->db->join('attributes u2', 'u.attribute_id = u2.id', 'left');
       // $this->db->where('u.type', 2);
        $this->db->order_by('u.value', 'asc');
        $query = $this->db->get('attribute_values u');
        return $query->result_array();
    }

    public function create_vb($name, $var_id)
    {
        $data = array(
            'value' => $name,
            'status' => 1,
            'attribute_id' => $var_id
        );

        if ($this->db->insert('attribute_values', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit_vb($id, $name, $var_id)
    {
        $data = array(
            'value' => $name,
            'attribute_id' => $var_id
        );

        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('attribute_values')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


}