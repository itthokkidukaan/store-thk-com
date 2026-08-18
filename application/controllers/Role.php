<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Role extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Role_model', 'role');
        $this->load->model('Permission_model', 'permission');
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
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Role List';
        $data['roles'] = $this->role->list_role();
		//echo '<pre>';print_r($data);die;
		
        $this->load->view('fixed/header', $head);
        $this->load->view('role/list', $data);
        $this->load->view('fixed/footer');
    }


    public function add()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Add Role';
        $data['all_permissions'] = $this->permission->get_all_permission();
         $data['permission_groups'] = $this->permission->getpermissionGroups();
        $this->load->view('fixed/header', $head);
        $this->load->view('role/add', $data);
        $this->load->view('fixed/footer');


    }

    public function submit_role()
    {

        $rolename = $this->input->post('rolename');
		$slug = url_title($rolename, 'dash', TRUE);
        $data = [
            'name'  => $rolename,
            'slug' => $slug,
        ];
        
        $roleId = $this->role->add_role($data);
        $permissions = $this->input->post('permissions');

        if (!empty($permissions)) {
            $data = [];
            foreach ($permissions as $permId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permId
                ];
            }
            $this->db->insert_batch('geopos_role_permissions', $data);
        }
        echo json_encode(array('status' => 'Success', 'message' => 'Role created successfully'));
       
    }

    public function edit()
    {
        $data['id'] = $this->input->get('id');
        $roleId = $data['id'];
        $data['role'] = $this->role->get_role_by_id($roleId);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Edit Role';
        $data['all_permissions'] = $this->permission->get_all_permission();
        $data['permission_groups'] = $this->permission->getpermissionGroups();
        $this->load->view('fixed/header', $head);
        $this->load->view('role/edit', $data);
        $this->load->view('fixed/footer');


    }

    public function update()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $id = $this->input->get('roleId');
        $permissions = $this->input->post('permissions');
        $roleName = $this->input->post('rolename');
        $roleData = ['name' => $roleName];
        $this->role->update_role($id,$roleData);
        $this->role->delete_role_permission_by_roleId($id);
        
        if (!empty($permissions)) {
            $data = [];
            foreach ($permissions as $permId) {
                $data[] = [
                    'role_id' => $id,
                    'permission_id' => $permId
                ];
            }
            $this->db->insert_batch('geopos_role_permissions', $data);
        }
        
        echo json_encode(array('status' => 'Success', 'message' => 'Role updated successfully'));

    } 



}