<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sellers extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library([ 'form_validation', 'upload']);
        $this->load->library("Aauth");
        $this->load->helper(['url', 'language', 'file']);
        $this->load->model('Seller_model');
        $this->load->model('category_model');
        $this->data = [];

       /*  if (!has_permissions('read', 'seller')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        } */
    }

    public function index()
    {
     
          /*   $this->data['main_page'] = TABLES . 'manage-seller';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Seller Management | ' . $settings['app_name'];
            $this->data['meta_description'] = ' Seller Management  | ' . $settings['app_name']; */
          //  $this->load->view('admin/template', $this->data);
          $head['title'] = "Seller Management";
        $head['usernm'] = $this->aauth->get_user()->username;
          $this->load->view('fixed/header', $head);
          $this->load->view('seller/manage-seller');
          $this->load->view('fixed/footer');
       
    }

    public function manage_seller()
    {
        $edit_id = $this->input->get('edit_id', true);

        $head['title'] = "Seller Management";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);

        if (!empty($edit_id)) {
            $this->load->config('eshop');
            $system_modules = $this->config->item('system_modules');
            $this->load->model('Permission_model', 'permission');

            $seller = $this->db->select('u.id as user_id, u.username, u.email, u.mobile, u.address, sd.store_name, sd.permissions')
                ->join('seller_data sd', 'sd.user_id = u.id', 'left')
                ->where('u.id', $edit_id)
                ->get('users u')
                ->row_array();

            if (empty($seller)) {
                redirect('manage-seller', 'refresh');
                return;
            }

            $seller['permissions'] = !empty($seller['permissions'])
                ? json_decode($seller['permissions'], true)
                : [];
            $seller_permission_names = [];
            if (is_array($seller['permissions'])) {
                if (isset($seller['permissions']['permission_names']) && is_array($seller['permissions']['permission_names'])) {
                    $seller_permission_names = $seller['permissions']['permission_names'];
                } elseif (isset($seller['permissions'][0])) {
                    $seller_permission_names = $seller['permissions'];
                }
            }

            $this->load->view('seller/edit-seller', [
                'seller' => $seller,
                'system_modules' => $system_modules,
                'all_permissions' => $this->permission->get_all_permission(),
                'permission_groups' => $this->permission->getpermissionGroups(),
                'seller_permission_names' => $seller_permission_names
            ]);
        } else {
            $this->load->view('seller/manage-seller');
        }

        $this->load->view('fixed/footer');
    }

    public function apply_role_permissions()
    {
        if (print_msg(!has_permissions('update', 'seller'), PERMISSION_ERROR_MSG, 'seller')) {
            return true;
        }

        $seller_id = (int)$this->input->get('seller_id', true);
        $role_id = (int)$this->input->get('role_id', true);
        if ($seller_id <= 0) {
            redirect('manage-seller', 'refresh');
            return;
        }
        if ($role_id <= 0) {
            $role_id = 3;
        }

        $permission_names = $this->db->select('gp.name')
            ->from('geopos_role_permissions grp')
            ->join('geopos_permissions gp', 'gp.id = grp.permission_id')
            ->where('grp.role_id', $role_id)
            ->get()
            ->result_array();
        $permission_names = array_column($permission_names, 'name');

        $this->load->config('eshop');
        $system_modules = $this->config->item('system_modules');
        $modules = $this->build_modules_from_permissions($permission_names, $system_modules);

        $existing = $this->db->select('permissions')
            ->where('user_id', $seller_id)
            ->get('seller_data')
            ->row_array();
        $current = [];
        if (!empty($existing['permissions'])) {
            $decoded = json_decode($existing['permissions'], true);
            if (is_array($decoded)) {
                $current = $decoded;
            }
        }
        $current['permission_names'] = $permission_names;
        $current['modules'] = $modules;

        $this->db->set('permissions', json_encode($current))
            ->where('user_id', $seller_id)
            ->update('seller_data');

        $this->session->set_flashdata('message', 'Permissions copied from role.');
        redirect('manage-seller?edit_id=' . $seller_id, 'refresh');
    }

    private function build_modules_from_permissions($permission_names, $system_modules)
    {
        $modules = [];
        foreach ($system_modules as $module => $actions) {
            foreach ($actions as $action) {
                $modules[$module][$action] = 0;
            }
        }

        $explicit_map = [
            'sales-view' => ['orders', 'read'],
            'new-invoice' => ['orders', 'create'],
            'pos-invoice-view' => ['orders', 'read'],
            'pos-invoice-create' => ['orders', 'create'],
            'trash-invoice' => ['orders', 'delete'],
            'order-view' => ['orders', 'read'],
            'order-create' => ['orders', 'create'],
            'order-update' => ['orders', 'update'],
            'order-delete' => ['orders', 'delete'],
            'product-view' => ['product', 'read'],
            'product-create' => ['product', 'create'],
            'product-update' => ['product', 'update'],
            'product-delete' => ['product', 'delete'],
            'category-view' => ['categories', 'read'],
            'category-create' => ['categories', 'create'],
            'category-update' => ['categories', 'update'],
            'category-delete' => ['categories', 'delete'],
            'media-view' => ['media', 'read'],
            'media-create' => ['media', 'create'],
            'media-update' => ['media', 'update'],
            'media-delete' => ['media', 'delete'],
            'client-view' => ['customers', 'read'],
            'client-create' => ['customers', 'create'],
            'client-edit' => ['customers', 'update'],
            'client-delete' => ['customers', 'delete'],
            'customer-view' => ['customers', 'read'],
            'customer-create' => ['customers', 'create'],
            'customer-update' => ['customers', 'update'],
            'customer-delete' => ['customers', 'delete'],
            'customer-record-view' => ['customers', 'read'],
            'return-request-view' => ['return_request', 'read'],
            'return-request-update' => ['return_request', 'update'],
            'delivery-boy-view' => ['delivery_boy', 'read'],
            'delivery-boy-create' => ['delivery_boy', 'create'],
            'delivery-boy-update' => ['delivery_boy', 'update'],
            'delivery-boy-delete' => ['delivery_boy', 'delete'],
            'fund-transfer-view' => ['fund_transfer', 'read'],
            'fund-transfer-create' => ['fund_transfer', 'create'],
            'send-notification-view' => ['send_notification', 'read'],
            'send-notification-create' => ['send_notification', 'create'],
            'notification-setting-view' => ['notification_setting', 'read'],
            'notification-setting-update' => ['notification_setting', 'update'],
            'client-api-keys-view' => ['client_api_keys', 'read'],
            'client-api-keys-create' => ['client_api_keys', 'create'],
            'client-api-keys-update' => ['client_api_keys', 'update'],
            'client-api-keys-delete' => ['client_api_keys', 'delete'],
            'area-view' => ['area', 'read'],
            'area-create' => ['area', 'create'],
            'area-update' => ['area', 'update'],
            'area-delete' => ['area', 'delete'],
            'city-view' => ['city', 'read'],
            'city-create' => ['city', 'create'],
            'city-update' => ['city', 'update'],
            'city-delete' => ['city', 'delete'],
            'faq-view' => ['faq', 'read'],
            'faq-create' => ['faq', 'create'],
            'faq-update' => ['faq', 'update'],
            'faq-delete' => ['faq', 'delete'],
            'promo-code-view' => ['promo_code', 'read'],
            'promo-code-create' => ['promo_code', 'create'],
            'promo-code-update' => ['promo_code', 'update'],
            'promo-code-delete' => ['promo_code', 'delete'],
            'tax-view' => ['tax', 'read'],
            'tax-create' => ['tax', 'create'],
            'tax-update' => ['tax', 'update'],
            'tax-delete' => ['tax', 'delete'],
            'attribute-view' => ['attribute', 'read'],
            'attribute-create' => ['attribute', 'create'],
            'attribute-update' => ['attribute', 'update'],
            'attribute-delete' => ['attribute', 'delete'],
            'attribute-set-view' => ['attribute_set', 'read'],
            'attribute-set-create' => ['attribute_set', 'create'],
            'attribute-set-update' => ['attribute_set', 'update'],
            'attribute-set-delete' => ['attribute_set', 'delete'],
            'attribute-value-view' => ['attribute_value', 'read'],
            'attribute-value-create' => ['attribute_value', 'create'],
            'attribute-value-update' => ['attribute_value', 'update'],
            'attribute-value-delete' => ['attribute_value', 'delete'],
            'support-ticket-view' => ['support_tickets', 'read'],
            'support-ticket-create' => ['support_tickets', 'create'],
            'support-ticket-update' => ['support_tickets', 'update'],
            'support-ticket-delete' => ['support_tickets', 'delete'],
        ];

        foreach ($permission_names as $perm) {
            if (isset($explicit_map[$perm])) {
                [$m, $a] = $explicit_map[$perm];
                if (isset($modules[$m][$a])) {
                    $modules[$m][$a] = 1;
                }
                continue;
            }
            $parts = explode('-', $perm);
            if (count($parts) < 2) {
                continue;
            }
            $action_raw = array_pop($parts);
            $action_map = ['view' => 'read', 'read' => 'read', 'create' => 'create', 'update' => 'update', 'delete' => 'delete'];
            if (!isset($action_map[$action_raw])) {
                continue;
            }
            $module_raw = implode('-', $parts);
            $module = str_replace('-', '_', $module_raw);
            if (!isset($modules[$module]) && isset($modules[$module . 's'])) {
                $module = $module . 's';
            }
            $action = $action_map[$action_raw];
            if (isset($modules[$module][$action])) {
                $modules[$module][$action] = 1;
            }
        }

        return $modules;
    }

    public function update_permissions()
    {
        $seller_id = $this->input->post('seller_id', true);
        if (empty($seller_id) || !is_numeric($seller_id)) {
            redirect('manage-seller', 'refresh');
            return;
        }

        $existing = $this->db->select('permissions')
            ->where('user_id', (int)$seller_id)
            ->get('seller_data')
            ->row_array();
        $existing_permissions = [];
        if (!empty($existing) && !empty($existing['permissions'])) {
            $decoded = json_decode($existing['permissions'], true);
            if (is_array($decoded)) {
                $existing_permissions = $decoded;
            }
        }

        $permissions = $existing_permissions;
        $permissions['require_products_approval'] = $this->input->post('require_products_approval') ? 1 : 0;
        $permissions['customer_privacy'] = $this->input->post('customer_privacy') ? 1 : 0;
        $permissions['view_order_otp'] = $this->input->post('view_order_otp') ? 1 : 0;
        $permissions['assign_delivery_boy'] = $this->input->post('assign_delivery_boy') ? 1 : 0;

        $permission_ids = $this->input->post('permissions');
        $permission_names = [];
        if (is_array($permission_ids) && !empty($permission_ids)) {
            $permission_names = $this->db->select('name')
                ->from('geopos_permissions')
                ->where_in('id', $permission_ids)
                ->get()
                ->result_array();
            $permission_names = array_column($permission_names, 'name');
        }
        $permissions['permission_names'] = $permission_names;

        // Sync module read/create/update/delete from selected permission names
        $this->load->config('eshop');
        $system_modules = $this->config->item('system_modules');
        $modules = $this->build_modules_from_permissions($permission_names, $system_modules);
        $permissions['modules'] = $modules;

        $this->db->set('permissions', json_encode($permissions));
        $this->db->where('user_id', (int)$seller_id);
        $this->db->update('seller_data');

        redirect('manage-seller?edit_id=' . (int)$seller_id, 'refresh');
    }

    public function view_sellers()
    {
        
            return $this->Seller_model->get_sellers_list();
       
    }

    public function remove_sellers()
    {
        

            if (print_msg(!has_permissions('delete', 'seller'), PERMISSION_ERROR_MSG, 'seller', false)) {
                return true;
            }

            if (!isset($_GET['id']) && empty($_GET['id'])) {
                $this->response['error'] = true;
                $this->response['message'] = 'Seller id is required';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            $all_status = [0, 1, 2, 7];
            $status = $this->input->get('status', true);
            $id = $this->input->get('id', true);
            if (!in_array($status, $all_status)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Invalid status';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            if ($status == 2) {
                $this->response['error'] = true;
                $this->response['message'] = 'First approve this Seller from edit seller.';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            $status = ($status == 7) ? 1 : (($status == 1) ? 7 : 1);

            if (update_details(['status' => $status], ['user_id' => $id], 'seller_data') == TRUE) {
                $this->response['error'] = false;
                $this->response['message'] = 'Seller removed succesfully';
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
       
    }

    public function delete_sellers()
    {
        
            if (print_msg(!has_permissions('delete', 'seller'), PERMISSION_ERROR_MSG, 'seller', false)) {
                return true;
            }

            if (!isset($_GET['id']) && empty($_GET['id'])) {
                $this->response['error'] = true;
                $this->response['message'] = 'Seller id is required';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            $id = $this->input->get('id', true);
            $delete = array(
                "media" => 0,
                "payment_requests" => 0,
                "products" => 0,
                "product_attributes" => 0,
                "order_items" => 0,
                "orders" => 0,
                "order_bank_transfer" => 0,
                "seller_commission" => 0,
                "seller_data" => 0,
            );

            $seller_media = fetch_details('seller_data', ['user_id' => $id], 'id,logo,national_identity_card,address_proof');

            if (!empty($seller_media)) {
                unlink(FCPATH . $seller_media[0]['logo']);
                unlink(FCPATH . $seller_media[0]['national_identity_card']);
                unlink(FCPATH . $seller_media[0]['address_proof']);
            }

            if (update_details(['seller_id' => 0], ['seller_id' => $id], 'media')) {
                $delete['media'] = 1;
            }

            /* check for retur requesst if seller's product have */
            $return_req = $this->db->where(['p.seller_id' => $id])->join('products p', 'p.id=rr.product_id')->get('return_requests rr')->result_array();
            if (!empty($return_req)) {
                $this->response['error'] = true;
                $this->response['message'] = 'Seller could not be deleted.Either found some order items which has return request.Finalize those before deleting it';
                print_r(json_encode($this->response));
                return;
                exit();
            }
            $pr_ids = fetch_details("products", ['seller_id' => $id], "id");
            if (delete_details(['seller_id' => $id], 'products')) {
                $delete['products'] = 1;
            }
            foreach ($pr_ids as $row) {
                if (delete_details(['product_id' => $row['id']], 'product_attributes')) {
                    $delete['product_attributes'] = 1;
                }
            }

            /* check order items */
            $order_items = fetch_details('order_items', ['seller_id' => $id], 'id,order_id');
            if (delete_details(['seller_id' => $id], 'order_items')) {
                $delete['order_items'] = 1;
            }
            if (!empty($order_items)) {
                $res_order_id = array_values(array_unique(array_column($order_items, "order_id")));
                for ($i = 0; $i < count($res_order_id); $i++) {
                    $orders = $this->db->where('oi.seller_id != ' . $id . ' and oi.order_id=' . $res_order_id[$i])->join('orders o', 'o.id=oi.order_id', 'right')->get('order_items oi')->result_array();
                    if (empty($orders)) {
                        // delete orders
                        if (delete_details(['seller_id' => $id], 'order_items')) {
                            $delete['order_items'] = 1;
                        }
                        if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                            $delete['orders'] = 1;
                        }
                        if (delete_details(['order_id' => $res_order_id[$i]], 'order_bank_transfer')) {
                            $delete['order_bank_transfer'] = 1;
                        }
                    }
                }
            } else {
                $delete['order_items'] = 1;
                $delete['orders'] = 1;
                $delete['order_bank_transfer'] = 1;
            }
            if (!empty($res_order_id)) {

                if (delete_details(['id' => $res_order_id[$i]], 'orders')) {
                    $delete['orders'] = 1;
                }
            } else {
                $delete['orders'] = 1;
            }

            if (delete_details(['seller_id' => $id], 'seller_commission')) {
                $delete['seller_commission'] = 1;
            }
            if (delete_details(['user_id' => $id], 'seller_data')) {
                $delete['seller_data'] = 1;
            }

            $deleted = FALSE;
            if (isset($delete['seller_data']) && !empty($delete['seller_data']) && isset($delete['seller_commission']) && !empty($delete['seller_commission'])) {
                $deleted = TRUE;
            }
            if (update_details(['group_id' => '2'], ['user_id' => $id, 'group_id' => 4], 'users_groups') == TRUE && $deleted == TRUE) {
                $this->response['error'] = false;
                $this->response['message'] = 'Seller deleted from seller succesfully';
                print_r(json_encode($this->response));
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Something Went Wrong';
                print_r(json_encode($this->response));
            }
       
    }


    public function add_seller()
    {
     
            if (isset($_POST['edit_seller'])) {
                if (print_msg(!has_permissions('update', 'seller'), PERMISSION_ERROR_MSG, 'seller')) {
                    return true;
                }
            } else {
                if (print_msg(!has_permissions('create', 'seller'), PERMISSION_ERROR_MSG, 'seller')) {
                    return true;
                }
            }
            $user = $this->ion_auth->user()->row();
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('email', 'Mail', 'trim|required');
            if (!isset($_POST['edit_seller'])) {
                $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|numeric|min_length[5]|edit_unique[users.mobile.' . $user->id . ']');
                $this->form_validation->set_rules('password', 'Password', 'trim|required');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|required|matches[password]');
            }
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('store_name', 'Store Name', 'trim|required');
            $this->form_validation->set_rules('tax_name', 'Tax Name', 'trim|required');
            $this->form_validation->set_rules('tax_number', 'Tax Number', 'trim|required');
            $this->form_validation->set_rules('status', 'Status', 'trim|required');
            if (!isset($_POST['edit_seller'])) {
                if (isset($_POST['global_commission']) && empty($_POST['global_commission'])) {
                    $this->form_validation->set_rules('commission_data', 'Category Commission data or Global Commission is missing', 'trim|required');
                }
            }

            if (!isset($_POST['edit_seller'])) {
                $this->form_validation->set_rules('store_logo', 'Store Logo', 'trim');
                $this->form_validation->set_rules('national_identity_card', 'National Identity Card', 'trim');
                $this->form_validation->set_rules('address_proof', 'Address Proof', 'trim');
            }

            if (!$this->form_validation->run()) {

                $this->response['error'] = true;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['message'] = validation_errors();
                print_r(json_encode($this->response));
            } else {

                // process images of seller

                if (!file_exists(FCPATH . SELLER_DOCUMENTS_PATH)) {
                    mkdir(FCPATH . SELLER_DOCUMENTS_PATH, 0777);
                }

                //process store logo
                $temp_array_logo = $store_logo_doc = array();
                $logo_files = $_FILES;
                $store_logo_error = "";
                $config = [
                    'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                    'allowed_types' => 'jpg|png|jpeg|gif',
                    'max_size' => 8000,
                ];
                if (isset($logo_files['store_logo']) && !empty($logo_files['store_logo']['name']) && isset($logo_files['store_logo']['name'])) {
                    $other_img = $this->upload;
                    $other_img->initialize($config);

                    if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_store_logo']) && !empty($_POST['old_store_logo'])) {
                        $old_logo = explode('/', $this->input->post('old_store_logo', true));
                        delete_images(SELLER_DOCUMENTS_PATH, $old_logo[2]);
                    }

                    if (!empty($logo_files['store_logo']['name'])) {

                        $_FILES['temp_image']['name'] = $logo_files['store_logo']['name'];
                        $_FILES['temp_image']['type'] = $logo_files['store_logo']['type'];
                        $_FILES['temp_image']['tmp_name'] = $logo_files['store_logo']['tmp_name'];
                        $_FILES['temp_image']['error'] = $logo_files['store_logo']['error'];
                        $_FILES['temp_image']['size'] = $logo_files['store_logo']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $store_logo_error = 'Images :' . $store_logo_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array_logo = $other_img->data();
                            resize_review_images($temp_array_logo, FCPATH . SELLER_DOCUMENTS_PATH);
                            $store_logo_doc  = SELLER_DOCUMENTS_PATH . $temp_array_logo['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $logo_files['store_logo']['name'];
                        $_FILES['temp_image']['type'] = $logo_files['store_logo']['type'];
                        $_FILES['temp_image']['tmp_name'] = $logo_files['store_logo']['tmp_name'];
                        $_FILES['temp_image']['error'] = $logo_files['store_logo']['error'];
                        $_FILES['temp_image']['size'] = $logo_files['store_logo']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $store_logo_error = $other_img->display_errors();
                        }
                    }
                    //Deleting Uploaded Images if any overall error occured
                    if ($store_logo_error != NULL || !$this->form_validation->run()) {
                        if (isset($store_logo_doc) && !empty($store_logo_doc || !$this->form_validation->run())) {
                            foreach ($store_logo_doc as $key => $val) {
                                unlink(FCPATH . SELLER_DOCUMENTS_PATH . $store_logo_doc[$key]);
                            }
                        }
                    }
                }

                if ($store_logo_error != NULL) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] =  $store_logo_error;
                    print_r(json_encode($this->response));
                    return;
                }

                //process national_identity_card
                $temp_array_id_card = $id_card_doc = array();
                $id_card_files = $_FILES;
                $id_card_error = "";
                $config = [
                    'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                    'allowed_types' => 'jpg|png|jpeg|gif',
                    'max_size' => 8000,
                ];
                if (isset($id_card_files['national_identity_card']) &&  !empty($id_card_files['national_identity_card']['name']) && isset($id_card_files['national_identity_card']['name'])) {
                    $other_img = $this->upload;
                    $other_img->initialize($config);

                    if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_national_identity_card']) && !empty($_POST['old_national_identity_card'])) {
                        $old_national_identity_card = explode('/', $this->input->post('old_national_identity_card', true));
                        delete_images(SELLER_DOCUMENTS_PATH, $old_national_identity_card[2]);
                    }

                    if (!empty($id_card_files['national_identity_card']['name'])) {

                        $_FILES['temp_image']['name'] = $id_card_files['national_identity_card']['name'];
                        $_FILES['temp_image']['type'] = $id_card_files['national_identity_card']['type'];
                        $_FILES['temp_image']['tmp_name'] = $id_card_files['national_identity_card']['tmp_name'];
                        $_FILES['temp_image']['error'] = $id_card_files['national_identity_card']['error'];
                        $_FILES['temp_image']['size'] = $id_card_files['national_identity_card']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $id_card_error = 'Images :' . $id_card_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array_id_card = $other_img->data();
                            resize_review_images($temp_array_id_card, FCPATH . SELLER_DOCUMENTS_PATH);
                            $id_card_doc  = SELLER_DOCUMENTS_PATH . $temp_array_id_card['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $id_card_files['national_identity_card']['name'];
                        $_FILES['temp_image']['type'] = $id_card_files['national_identity_card']['type'];
                        $_FILES['temp_image']['tmp_name'] = $id_card_files['national_identity_card']['tmp_name'];
                        $_FILES['temp_image']['error'] = $id_card_files['national_identity_card']['error'];
                        $_FILES['temp_image']['size'] = $id_card_files['national_identity_card']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $id_card_error = $other_img->display_errors();
                        }
                    }
                    //Deleting Uploaded Images if any overall error occured
                    if ($id_card_error != NULL || !$this->form_validation->run()) {
                        if (isset($id_card_doc) && !empty($id_card_doc || !$this->form_validation->run())) {
                            foreach ($id_card_doc as $key => $val) {
                                unlink(FCPATH . SELLER_DOCUMENTS_PATH . $id_card_doc[$key]);
                            }
                        }
                    }
                }

                if ($id_card_error != NULL) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] =  $id_card_error;
                    print_r(json_encode($this->response));
                    return;
                }

                //process address_proof
                $temp_array_proof = $proof_doc = array();
                $proof_files = $_FILES;
                $proof_error = "";
                $config = [
                    'upload_path' =>  FCPATH . SELLER_DOCUMENTS_PATH,
                    'allowed_types' => 'jpg|png|jpeg|gif',
                    'max_size' => 8000,
                ];
                if (isset($proof_files['address_proof']) && !empty($proof_files['address_proof']['name']) && isset($proof_files['address_proof']['name'])) {
                    $other_img = $this->upload;
                    $other_img->initialize($config);

                    if (isset($_POST['edit_seller']) && !empty($_POST['edit_seller']) && isset($_POST['old_address_proof']) && !empty($_POST['old_address_proof'])) {
                        $old_address_proof = explode('/', $this->input->post('old_address_proof', true));
                        delete_images(SELLER_DOCUMENTS_PATH, $old_address_proof[2]);
                    }

                    if (!empty($proof_files['address_proof']['name'])) {

                        $_FILES['temp_image']['name'] = $proof_files['address_proof']['name'];
                        $_FILES['temp_image']['type'] = $proof_files['address_proof']['type'];
                        $_FILES['temp_image']['tmp_name'] = $proof_files['address_proof']['tmp_name'];
                        $_FILES['temp_image']['error'] = $proof_files['address_proof']['error'];
                        $_FILES['temp_image']['size'] = $proof_files['address_proof']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $proof_error = 'Images :' . $proof_error . ' ' . $other_img->display_errors();
                        } else {
                            $temp_array_proof = $other_img->data();
                            resize_review_images($temp_array_proof, FCPATH . SELLER_DOCUMENTS_PATH);
                            $proof_doc  = SELLER_DOCUMENTS_PATH . $temp_array_proof['file_name'];
                        }
                    } else {
                        $_FILES['temp_image']['name'] = $proof_files['address_proof']['name'];
                        $_FILES['temp_image']['type'] = $proof_files['address_proof']['type'];
                        $_FILES['temp_image']['tmp_name'] = $proof_files['address_proof']['tmp_name'];
                        $_FILES['temp_image']['error'] = $proof_files['address_proof']['error'];
                        $_FILES['temp_image']['size'] = $proof_files['address_proof']['size'];
                        if (!$other_img->do_upload('temp_image')) {
                            $proof_error = $other_img->display_errors();
                        }
                    }
                    //Deleting Uploaded Images if any overall error occured
                    if ($proof_error != NULL || !$this->form_validation->run()) {
                        if (isset($proof_doc) && !empty($proof_doc || !$this->form_validation->run())) {
                            foreach ($proof_doc as $key => $val) {
                                unlink(FCPATH . SELLER_DOCUMENTS_PATH . $proof_doc[$key]);
                            }
                        }
                    }
                }

                if ($proof_error != NULL) {
                    $this->response['error'] = true;
                    $this->response['csrfName'] = $this->security->get_csrf_token_name();
                    $this->response['csrfHash'] = $this->security->get_csrf_hash();
                    $this->response['message'] =  $proof_error;
                    print_r(json_encode($this->response));
                    return;
                }

                $categories = "";
                // process categories
                if (isset($_POST['commission_data']) && !empty($_POST['commission_data'])) {

                    $commission_data = json_decode($this->input->post('commission_data'), true);
                    if (!is_array($commission_data['category_id'])) {
                        $categories = $commission_data['category_id'];
                    } else {
                        if (count($commission_data['category_id']) >= 2) {
                            $categories = implode(",", array_unique($commission_data['category_id']));
                        }
                    }
                }

                // process permissions of sellers
                $permmissions = array();
                $permmissions['require_products_approval'] = (isset($_POST['require_products_approval'])) ? 1 : 0;
                $permmissions['customer_privacy'] = (isset($_POST['customer_privacy'])) ? 1 : 0;
                $permmissions['view_order_otp'] = (isset($_POST['view_order_otp'])) ? 1 : 0;
                $permmissions['assign_delivery_boy'] = (isset($_POST['assign_delivery_boy'])) ? 1 : 0;

                if (isset($_POST['edit_seller'])) {
                    if (empty($_POST['commission_data'])) {
                        $category_ids = fetch_details("seller_data", ['id' => $this->input->post('edit_seller_data_id', true)], "category_ids");
                        $categories = $category_ids[0]['category_ids'];
                    }
                    $seller_data = array(
                        'user_id' => $this->input->post('edit_seller', true),
                        'edit_seller_data_id' => $this->input->post('edit_seller_data_id', true),
                        'address_proof' => (!empty($proof_doc)) ? $proof_doc : $this->input->post('old_address_proof', true),
                        'national_identity_card' => (!empty($id_card_doc)) ? $id_card_doc : $this->input->post('old_national_identity_card', true),
                        'store_logo' => (!empty($store_logo_doc)) ? $store_logo_doc : $this->input->post('old_store_logo', true),
                        'status' => $this->input->post('status', true),
                        'pan_number' => $this->input->post('pan_number', true),
                        'tax_number' => $this->input->post('tax_number', true),
                        'tax_name' => $this->input->post('tax_name', true),
                        'bank_name' => $this->input->post('bank_name', true),
                        'bank_code' => $this->input->post('bank_code', true),
                        'account_name' => $this->input->post('account_name', true),
                        'account_number' => $this->input->post('account_number', true),
                        'store_description' => $this->input->post('store_description', true),
                        'store_url' => $this->input->post('store_url', true),
                        'store_name' => $this->input->post('store_name', true),
                        'global_commission' => (isset($_POST['global_commission']) && !empty($_POST['global_commission'])) ? $this->input->post('global_commission', true) : 0,
                        'categories' => $categories,
                        'permissions' => $permmissions,
                        'slug' => create_unique_slug($this->input->post('store_name', true), 'seller_data')
                    );
                    $seller_profile = array(
                        'name' => $this->input->post('name', true),
                        'email' => $this->input->post('email', true),
                        'mobile' => $this->input->post('mobile', true),
                        'password' => $this->input->post('password', true),
                        'address' => $this->input->post('address', true),
                        'latitude' => $this->input->post('latitude', true),
                        'longitude' => $this->input->post('longitude', true)
                    );

                    $com_data = array();
                    if (isset($_POST['commission_data']) && !empty($_POST['commission_data'])) {
                        $commission_data = json_decode($this->input->post('commission_data'), true);
                        if (is_array($commission_data['category_id'])) {
                            if (count($commission_data['category_id']) >= 2) {
                                $cat_array = array_unique($commission_data['category_id']);
                                foreach ($commission_data['commission'] as $key => $val) {
                                    if (!array_key_exists($key, $cat_array)) unset($commission_data['commission'][$key]);
                                }
                                $cat_array = array_values($cat_array);
                                $com_array = array_values($commission_data['commission']);

                                for ($i = 0; $i < count($cat_array); $i++) {
                                    $tmp['seller_id'] = $this->input->post('edit_seller', true);
                                    $tmp['category_id'] = $cat_array[$i];
                                    $tmp['commission'] = $com_array[$i];
                                    $com_data[] = $tmp;
                                }
                            } else {
                                $com_data[0] = array(
                                    "seller_id" => $this->input->post('edit_seller', true),
                                    "category_id" => $commission_data['category_id'],
                                    "commission" => $commission_data['commission'],
                                );
                            }
                        } else {
                            $com_data[0] = array(
                                "seller_id" => $this->input->post('edit_seller', true),
                                "category_id" => $commission_data['category_id'],
                                "commission" => $commission_data['commission'],
                            );
                        }
                    }

                    if ($this->Seller_model->add_seller($seller_data, $seller_profile, $com_data)) {
                        $this->response['error'] = false;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $message = 'Seller Update Successfully';
                        $this->response['message'] = $message;
                        print_r(json_encode($this->response));
                    } else {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['message'] = "Seller data was not updated";
                        print_r(json_encode($this->response));
                    }
                } else {

                    if (!$this->form_validation->is_unique($_POST['mobile'], 'users.mobile') || !$this->form_validation->is_unique($_POST['email'], 'users.email')) {
                        $response["error"]   = true;
                        $response["message"] = "Email or mobile already exists !";
                        $response['csrfName'] = $this->security->get_csrf_token_name();
                        $response['csrfHash'] = $this->security->get_csrf_hash();
                        $response["data"] = array();
                        echo json_encode($response);
                        return false;
                    }

                    $identity_column = $this->config->item('identity', 'ion_auth');
                    $email = strtolower($this->input->post('email'));
                    $mobile = $this->input->post('mobile');
                    $identity = ($identity_column == 'mobile') ? $mobile : $email;
                    $password = $this->input->post('password');

                    $additional_data = [
                        'username' => $this->input->post('name', true),
                        'address' => $this->input->post('address', true),
                        'latitude' => $this->input->post('latitude', true),
                        'longitude' => $this->input->post('longitude', true),
                    ];

                    $this->ion_auth->register($identity, $password, $email, $additional_data, ['4']);
                    if (update_details(['active' => 1], [$identity_column => $identity], 'users')) {
                        $user_id = fetch_details('users', ['mobile' => $mobile], 'id');
                        $com_data = array();
                        if (isset($_POST['commission_data']) && !empty($_POST['commission_data'])) {

                            $commission_data = json_decode($this->input->post('commission_data'), true);

                            if (is_array($commission_data['category_id'])) {
                                if (count($commission_data['category_id']) >= 2) {
                                    $cat_array = array_unique($commission_data['category_id']);
                                    foreach ($commission_data['commission'] as $key => $val) {
                                        if (!array_key_exists($key, $cat_array)) unset($commission_data['commission'][$key]);
                                    }
                                    $cat_array = array_values($cat_array);
                                    $com_array = array_values($commission_data['commission']);

                                    for ($i = 0; $i < count($cat_array); $i++) {
                                        $tmp['seller_id'] = $user_id[0]['id'];
                                        $tmp['category_id'] = $cat_array[$i];
                                        $tmp['commission'] = $com_array[$i];
                                        $com_data[] = $tmp;
                                    }
                                } else {
                                    $com_data[0] = array(
                                        "seller_id" => $user_id[0]['id'],
                                        "category_id" => $commission_data['category_id'],
                                        "commission" => $commission_data['commission'],
                                    );
                                }
                            } else {
                                $com_data[0] = array(
                                    "seller_id" => $user_id[0]['id'],
                                    "category_id" => $commission_data['category_id'],
                                    "commission" => $commission_data['commission'],
                                );
                            }
                        } else {
                            $category_ids = fetch_details('categories', null,  'id');
                            $categories = implode(",", array_column($category_ids, "id"));
                        }

                        $data = array(
                            'user_id' => $user_id[0]['id'],
                            'address_proof' => (!empty($proof_doc)) ? $proof_doc : null,
                            'national_identity_card' => (!empty($id_card_doc)) ? $id_card_doc : null,
                            'store_logo' => (!empty($store_logo_doc)) ? $store_logo_doc : null,
                            'status' => $this->input->post('status', true),
                            'pan_number' => $this->input->post('pan_number', true),
                            'tax_number' => $this->input->post('tax_number', true),
                            'tax_name' => $this->input->post('tax_name', true),
                            'bank_name' => $this->input->post('bank_name', true),
                            'bank_code' => $this->input->post('bank_code', true),
                            'account_name' => $this->input->post('account_name', true),
                            'account_number' => $this->input->post('account_number', true),
                            'store_description' => $this->input->post('store_description', true),
                            'store_url' => $this->input->post('store_url', true),
                            'store_name' => $this->input->post('store_name', true),
                            'global_commission' => (isset($_POST['global_commission']) && !empty($_POST['global_commission'])) ? $this->input->post('global_commission', true) : 0,                            'categories' => $categories,
                            'permissions' => $permmissions,
                            'categories' => $categories,
                            'slug' => create_unique_slug($this->input->post('store_name', true), 'seller_data')
                        );
                        $insert_id = $this->Seller_model->add_seller($data, [], $com_data);
                        if (!empty($insert_id)) {
                            $this->response['error'] = false;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = 'Seller Added Successfully';
                            print_r(json_encode($this->response));
                        } else {
                            $this->response['error'] = true;
                            $this->response['csrfName'] = $this->security->get_csrf_token_name();
                            $this->response['csrfHash'] = $this->security->get_csrf_hash();
                            $this->response['message'] = "Seller data was not added";
                            print_r(json_encode($this->response));
                        }
                    } else {
                        $this->response['error'] = true;
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $message = (isset($_POST['edit_seller'])) ? 'Seller not Updated' : 'Seller not Added.';
                        $this->response['message'] = $message;
                        print_r(json_encode($this->response));
                    }
                }
            }
        
    }

    public function get_seller_commission_data()
    {
        
            $result = array();
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $id = $this->input->post('id', true);
                $result = $this->Seller_model->get_seller_commission_data($id);
                if (empty($result)) {
                    $result = $this->category_model->get_categories();
                }
            } else {
                $result = fetch_details('categories', "",  'id,name');
            }
            if (empty($result)) {
                $this->response['error'] = true;
                $this->response['message'] = "No category & commission data found for seller.";
                $this->response['data'] = [];
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            } else {
                $this->response['error'] = false;
                $this->response['data'] = $result;
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }
       
    }

    public function create_slug()
    {
        
            $tmpRow = $update_batch = array();
            $sellers = fetch_details('seller_data', 'slug IS NULL', 'id,store_name');
            if (!empty($sellers)) {
                foreach ($sellers as $row) {
                    $tmpRow['id'] = $row['id'];
                    $tmpRow['slug'] = create_unique_slug($row['store_name'], 'seller_data');
                    $this->Seller_model->create_slug($tmpRow);
                }
                $this->response['error'] = false;
                $this->response['message'] = "Slug Created Successfully.";
                $this->response['data'] = [];
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            } else {
                $this->response['error'] = true;
                $this->response['message'] = 'Already Created No need to create again.';
                $this->response['data'] = [];
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($this->response));
                return false;
            }
     
    }

    public function top_seller()
    {
        $this->Seller_model->top_sellers();
    }

    public function approved_sellers()
    {
        $this->Seller_model->approved_sellers();
    }
    public function not_approved_sellers()
    {
        $this->Seller_model->not_approved_sellers();
    }
    public function deactive_sellers()
    {
        $this->Seller_model->deactive_sellers();
    }
}
