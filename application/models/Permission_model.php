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

class Permission_model extends CI_Model
{

    public function get_all_permission()
    {
        $this->db->select('geopos_permissions.*');
        $this->db->from('geopos_permissions');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getpermissionGroups()
    {
        $this->db->select('id, group_name as name');
        $this->db->from('geopos_permissions');
        $this->db->group_by('geopos_permissions.group_name');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getpermissionsByGroupName($group_name)
    {
        $this->db->select('id, name');
        $this->db->from('geopos_permissions');
        $this->db->where('group_name',$group_name);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_permission_by_id($pid)
    {
        $this->db->select('*');
        $this->db->from('geopos_permissions');
        $this->db->where('id', $pid);
        $this->db->group_by('group_name');
        $query = $this->db->get();
        $result = $query->row();
        return $result;
    }

    public function getGroupName($pid)
    {
        $this->db->select('group_name');
        $this->db->from('geopos_permissions');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $result = $query->row();
        return $groupName = $result ? $result->group_name : null;
    }

    public function getGroupNameByIds($group_name)
    {
        $this->db->select('id');
        $this->db->from('geopos_permissions');
        $this->db->where('group_name', $group_name);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

}