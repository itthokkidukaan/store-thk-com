<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Permission extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Permission_model', 'permission');
        $this->load->model('Role_model', 'role');
        $this->load->library("Aauth");
        $this->load->helper('url');
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(9)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->li_a = 'emp';

    }

    public function index()
    {
        // /echo 'ok';die;
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Permission List';
        $permissions = [];
        $dataPermissions = $this->permission->getpermissionGroups();
        foreach($dataPermissions as $permission){
            $permissionGroup = $this->permission->getpermissionsByGroupName($permission['name']);
            $permission['count'] = count($permissionGroup);
            $permissions[] = $permission;
        }
        $data['permissions'] = $permissions;
		
        $this->load->view('fixed/header', $head);
        $this->load->view('permission/list', $data);
        $this->load->view('fixed/footer');
    }


    public function add()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Add Permission';
        $data = [];
        
        $this->load->view('fixed/header', $head);
        $this->load->view('permission/add',$data);
        $this->load->view('fixed/footer');


    }

    public function submit_permission()
    {
        $CI =& get_instance();
        $CI->load->database();
        $groupName = $this->input->post('group_name');
		$name = $this->input->post('name');
        $inputData = [
            [
                'group_name'  => $groupName,
                'permissions' => $name,
            ],
        ];
        $role = $this->role->get_role_by_id(1);
        for ($i = 0; $i < count($inputData); $i++) {
            $permissionGroup = $inputData[$i]['group_name'];
            for ($j = 0; $j < count($inputData[$i]['permissions']); $j++) {
                $name = url_title($inputData[$i]['permissions'][$j],'-') ?? $inputData[$i]['permissions'][$j];
                 $CI->db->insert('geopos_permissions', [
                    'name' => $name,
                    'group_name' => $permissionGroup
                ]);

                $permission_id = $CI->db->insert_id();

                $CI->db->insert('geopos_role_permissions', [
                    'role_id' => $role->id,
                    'permission_id' => $permission_id
                ]);
            }
        }
        echo json_encode(array('status' => 'Success', 'message' => 'Permission created successfully'));
        
    }

    public function edit()
    {
        $data['id'] = $this->input->get('id');
        $pid = $data['id'];
        $permission = $this->permission->get_permission_by_id($pid);
        $data['permission'] = $permission;
       
        $data['permissions'] = $this->permission->getpermissionsByGroupName($permission->group_name);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Edit Permission';
        $this->load->view('fixed/header', $head);
        $this->load->view('permission/edit', $data);
        $this->load->view('fixed/footer');


    }

    public function update()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $CI =& get_instance();
        $CI->load->database();
        $id = $this->input->get('pId');
        $inputData = [
            [
                'group_name'  => $this->input->post('group_name'),
                'permissions' => $this->input->post('name'),
            ],
        ];
        
        $getGroupName = $this->permission->getGroupName($id);
        $getGroupNameByIds = $this->permission->getGroupNameByIds($getGroupName);
      
        foreach($getGroupNameByIds as $row){
            $ids[] = $row['id'];
        }

        $this->db->where_in('permission_id', $ids);
        $this->db->delete('geopos_role_permissions');

        $this->db->where_in('id', $ids);
        $this->db->delete('geopos_permissions');

        $role = $this->role->get_role_by_id(1);

        for ($i = 0; $i < count($inputData); $i++) {
            $permissionGroup = $inputData[$i]['group_name'];
            for ($j = 0; $j < count($inputData[$i]['permissions']); $j++) {
                $name = url_title($inputData[$i]['permissions'][$j],'-') ?? $inputData[$i]['permissions'][$j];
                 
                $CI->db->insert('geopos_permissions', [
                    'name' => $name,
                    'group_name' => $permissionGroup
                ]);

                $permission_id = $CI->db->insert_id();

                $CI->db->insert('geopos_role_permissions', [
                    'role_id' => $role->id,
                    'permission_id' => $permission_id
                ]);
            }
        }
        echo json_encode(array('status' => 'Success', 'message' => 'Role updated successfully'));

    } 



}