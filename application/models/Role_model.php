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

class Role_model extends CI_Model
{

    public function list_role()
    {
        $this->db->select('geopos_roles.*,COUNT(geopos_role_permissions.id) as permission_count');
        $this->db->from('geopos_roles');

        $this->db->join('geopos_role_permissions', 'geopos_role_permissions.role_id = geopos_roles.id', 'left');
        $this->db->where('geopos_roles.id !=', '1');
        $this->db->group_by('geopos_roles.id');
        $this->db->order_by('permission_count','DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

     public function get_role_without_super_role()
    {
        $this->db->select('geopos_roles.*,COUNT(geopos_role_permissions.id) as permission_count');
        $this->db->from('geopos_roles');

        $this->db->join('geopos_role_permissions', 'geopos_role_permissions.role_id = geopos_roles.id', 'left');
        $this->db->where('geopos_roles.id !=', '1');
        $this->db->group_by('geopos_roles.id');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function add_role($data) 
    {
        $this->db->insert('geopos_roles', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function update_role($id, $data) 
    {
        return $this->db->update('geopos_roles', $data, ['id' => $id]);
    }

    public function delete_role_permission_by_roleId($roleId) 
    {
        return $this->db->delete('geopos_role_permissions', ['role_id' => $roleId]);
    }
    
    public function get_role_by_id($roleId)
    {
        return $this->db->get_where('geopos_roles', ['id' => $roleId])->row();
    }

    public function roleHasPermissions($role, $permissions)
    {
        $roleId= $role->id;
        $hasPermission = true;
        foreach ($permissions as $permission) {
            $hasPermissionTo = $this->hasPermissionTo($roleId, $permission['name']);
            if (!$hasPermissionTo) {
                $hasPermission = false;
               
                return $hasPermission;
            }
        }
        
        return $hasPermission;
    }

    public function hasPermissionTo($roleId, $permissionName)
    {
        $this->db->select('geopos_permissions.*')
                ->from('geopos_permissions')
                ->join('geopos_role_permissions', 'geopos_permissions.id = geopos_role_permissions.permission_id')
                ->where('geopos_permissions.name', $permissionName)
                ->where('geopos_role_permissions.role_id', $roleId);

        $query = $this->db->get();
        $data = $query->row();
         return $data;
    }

}