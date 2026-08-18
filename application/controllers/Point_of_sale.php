<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Point_of_sale extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(['form_validation', 'upload', 'pagination']);
        $this->load->helper(['url', 'language', 'file']);
		$this->load->library("Aauth");
     /*    $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter'), $this->config->item('error_end_delimiter')); */
        $this->load->model(['point_of_sale_model', 'customer_model', 'transaction_model', 'order_model', 'cart_model', 'ion_auth_model']);
		//$this->load->model('ion_auth_model');

		$this->_cache_user_in_group = &$this->ion_auth_model->_cache_user_in_group;
       /*  if (!has_permissions('read', 'media')) {
            $this->session->set_flashdata('authorize_flag', PERMISSION_ERROR_MSG);
            redirect('admin/home', 'refresh');
        } */
    }
    public function index()
    {
        
            $this->data['main_page'] = VIEW . 'point-of-sale';
            $settings = get_settings('system_settings', true);
            $this->data['title'] = 'Point of Sale | ' . $settings['app_name'];
            $this->data['meta_description'] = 'Point of Sale |' . $settings['app_name'];
            $this->data['categories'] = $this->category_model->get_categories();
            $this->data['csrfName'] = $this->security->get_csrf_token_name();
            $this->data['csrfHash'] = $this->security->get_csrf_hash();
            $this->load->view('admin/template', $this->data);
       
    }
    public function get_products()
    {
        $max_limit = 25;
        $category_id = (isset($_GET['category_id']) && !empty($_GET['category_id']) && is_numeric($_GET['category_id'])) ? $_GET['category_id'] : "";
        $limit = (isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] <= $max_limit) ? $_GET['limit'] : $max_limit;
        $offset = (isset($_GET['offset']) && !empty($_GET['offset']) && is_numeric($_GET['offset'])) ? $_GET['offset'] : 0;
        $sort = (isset($_GET['sort']) && !empty($_GET['sort'])) ? $_GET['sort'] : 'id';
        $order = (isset($_GET['order']) && !empty($_GET['order'])) ? $_GET['order'] : 'desc';
        $filter['search'] = (isset($_GET['search']) && !empty($_GET['search'])) ? $_GET['search'] : '';

        $products =  $this->data['products'] = fetch_product("", $filter, "", $category_id, $limit, $offset, $sort, $order);
        $response['error'] = (!empty($products)) ? false : true;
        $response['message'] = (!empty($products)) ? "Products fetched successfully" : "No products found";
        $response['products'] = (!empty($products)) ? $products : [];
        print_r(json_encode($response));
    }

    public function get_users()
    {
        $search = $this->input->get('search');
        $response = $this->point_of_sale_model->get_users($search);
        echo json_encode($response);
    }
    public function register_user()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
		
        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|min_length[5]|numeric|is_unique[users.mobile]', array('is_unique' => ' The mobile number is already registered . Please login'));
       // $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->response['csrfName'] = $this->security->get_csrf_token_name();
        $this->response['csrfHash'] = $this->security->get_csrf_hash();
        if ($this->form_validation->run() == false) {
            $this->response['error'] = true;
            $this->response['message'] = strip_tags(validation_errors());
            $this->response['data'] = array();
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
        } else {
            $identity_column = 'mobile';
            $mobile = $this->input->post('mobile');
            $password = 'THK123456';
            $identity =  $mobile;
            $additional_data = [
                'username' => $this->input->post('name'),
                'active' => 1
            ];
            if (function_exists('is_seller_user') && is_seller_user()) {
                $additional_data['assigned'] = 0;
                $additional_data['assigned_seller'] = (int)$this->session->userdata('user_id');
            } else {
                $additional_data['assigned'] = (int)$this->aauth->get_user()->id;
                $additional_data['assigned_seller'] = 0;
            }
            $email = $mobile . '@thokkidukaan.com';
            if ($this->input->post('email') && trim($this->input->post('email')) != '') {
                $email = $this->input->post('email');
            }
            $res = $this->register($identity, $password, $email, $additional_data, ['2']);
            $update_data = ['active' => 1];
            if (function_exists('is_seller_user') && is_seller_user()) {
                $update_data['assigned'] = 0;
                $update_data['assigned_seller'] = (int)$this->session->userdata('user_id');
            } else {
                $update_data['assigned'] = (int)$this->aauth->get_user()->id;
                $update_data['assigned_seller'] = 0;
            }
            update_details($update_data, [$identity_column => $identity], 'users');
            $data = $this->db->select('u.id,u.username,u.mobile')->where([$identity_column => $identity])->get('users u')->result_array();
            $this->response['error'] = (!empty($data)) ? false : true;
            $this->response['message'] = (!empty($data)) ? "Registered Successfully" : "Not Registered";
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = (!empty($data)) ? $data : [];
        }
        print_r(json_encode($this->response));
    }
	
	public function register($identity, $password, $email, $additional_data = [], $group_ids = [])
	{
		$this->ion_auth_model->trigger_events('pre_account_creation');

		$email_activation = $this->config->item('email_activation', 'ion_auth');

		$id = $this->ion_auth_model->register($identity, $password, $email, $additional_data, $group_ids);

		//if (!$email_activation) {
			if ($id !== FALSE) {
				//$this->set_message('account_creation_successful');
				//$this->ion_auth_model->trigger_events(['post_account_creation', 'post_account_creation_successful']);
				//echo "Account Successfully Created";
				return $id;
			} else {
				$this->set_error('account_creation_unsuccessful');
				$this->ion_auth_model->trigger_events(['post_account_creation', 'post_account_creation_unsuccessful']);
				return FALSE;
			}
		
	}

public function deactivate($id = NULL)
	{
		//$this->trigger_events('deactivate');

		/* if (!isset($id)) {
			$this->set_error('deactivate_unsuccessful');
			return FALSE;
		} else if ($this->logged_in() && $this->user()->row()->id == $id) {
			$this->set_error('deactivate_current_user_unsuccessful');
			return FALSE;
		} */

		return $this->ion_auth_model->deactivate($id);
	}
	
    public function place_order()
    {
        try {
        if (!isset($_POST['data']) || empty($_POST['data'])) {
            $this->response['error'] = true;
            $this->response['message'] = "Pass the data";
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }

        $post_data = json_decode($_POST['data'], true);
        if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
            $this->response['error'] = true;
            $this->response['message'] = "Please select the customer!";
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }
        
            if (isset($post_data) && !empty($post_data)) {
                for ($i = 0; $i < count($post_data); $i++) {
                    if (!isset($post_data[$i]['variant_id']) || empty($post_data[$i]['variant_id'])) {
                        $this->response['error'] = true;
                        $this->response['message'] = "The variant ID field is required";
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                        return false;
                    }

                    if (!isset($post_data[$i]['quantity']) || empty($post_data[$i]['quantity'])) {
                        $this->response['error'] = true;
                        $this->response['message'] = "Please enter valid quantity for " . $post_data[$i]['title'];
                        $this->response['csrfName'] = $this->security->get_csrf_token_name();
                        $this->response['csrfHash'] = $this->security->get_csrf_hash();
                        $this->response['data'] = array();
                        print_r(json_encode($this->response));
                        return false;
                    }
                }
            } else {
                $this->response['error'] = true;
                $this->response['message'] = "Pass the data";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            // creating arr for place order
            $product_variant_id = array_column($post_data, "variant_id");
            $quantity = array_column($post_data, "quantity");
            $user_id = $_POST['user_id'];

            $place_order_data = array();
            $place_order_data['product_variant_id'] = implode(",", $product_variant_id);
            $place_order_data['quantity'] = implode(",", $quantity);
            $place_order_data['user_id'] = $user_id;
            $user_mobile = fetch_details("users", ['id' => $user_id], "mobile");
            $place_order_data['mobile'] = !empty($user_mobile[0]['mobile']) ? $user_mobile[0]['mobile'] : '';
            $place_order_data['is_wallet_used'] = 0;
            $place_order_data['delivery_charge'] = 0;
            $place_order_data['is_delivery_charge_returnable'] = 0;
            $place_order_data['wallet_balance_used'] = 0;
            
            $p_amount_submitted = isset($_POST['p_amount']) && trim($_POST['p_amount']) !== '';
            $place_order_data['p_amount'] = $p_amount_submitted ? $_POST['p_amount'] : 0;
            $totalamunt = $p_amount_submitted ? (float)$_POST['p_amount'] : 0;

            $payment_method_name = (isset($_POST['payment_method_name']) && !empty($_POST['payment_method_name'])) ? $this->input->post('payment_method_name', true) : NULL;
            $place_order_data['payment_method'] = (isset($_POST['payment_method']) && !empty($_POST['payment_method']) && $_POST['payment_method'] != "other") ? $this->input->post('payment_method', true) : $payment_method_name;
            $txn_id = (isset($_POST['txn_id']) && !empty($_POST['txn_id'])) ? $this->input->post('txn_id', true) : NULL;
			
           /*  $check_current_stock_status = validate_stock($product_variant_id, $quantity);
            if ($check_current_stock_status['error'] == true) {
                $this->response['error'] = true;
                $this->response['message'] = $check_current_stock_status['message'];
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            } */
            if (isset($_POST['payment_method']) && !empty($_POST['payment_method']) && $_POST['payment_method'] == "other" && empty($_POST['payment_method_name'])) {
                $this->response['error'] = true;
                $this->response['message'] = "Please enter payment method name";
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            for ($i = 0; $i < count($post_data); $i++) {
                $data = array(
                    'user_id' => $user_id,
                    'product_variant_id' => implode(",", $product_variant_id),
                    'qty' => implode(",", $quantity),
                );
                if ($this->cart_model->add_to_cart($data)) {
                    $this->response['error'] = true;
                    $this->response['message'] = "Item are Not Added";
                    $this->response['data'] = array();
                    print_r(json_encode($this->response));
                    return false;
                }
            }
            $cart = get_cart_total($user_id, false, '0', "", true);
            if (empty($cart)) {
                $this->response['error'] = true;
                $this->response['message'] = "Your Cart is empty.";
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            $final_total = $cart['overall_amount'];
            if (!$p_amount_submitted) {
                $totalamunt = (float)$final_total;
                $place_order_data['p_amount'] = $totalamunt;
            }
            $place_order_data['final_total'] = $final_total;
            $res = $this->order_model->place_order($place_order_data, $this->aauth->get_user()->id);
            if (empty($res) || empty($res['order_id'])) {
                $this->response['error'] = true;
                $this->response['message'] = "Unable to place the order.";
                $this->response['csrfName'] = $this->security->get_csrf_token_name();
                $this->response['csrfHash'] = $this->security->get_csrf_hash();
                $this->response['data'] = array();
                print_r(json_encode($this->response));
                return false;
            }
            if (isset($res) && !empty($res)) {
              
                $trans_data = [
                    'transaction_type' => 'transaction',
                    'user_id' => $user_id,
                    'order_id' => $res['order_id'],
                    'type' => strtolower($place_order_data['payment_method']),
                    'txn_id' => $txn_id,
                    'amount' => $final_total,
                    'status' => "success",
                    'txntype' => "Sales",
                    'message' => "Order Delivered Successfully",
                ];
                $this->transaction_model->add_transaction($trans_data);
            }
            $data['order_id'] = $res['order_id'];
            $invocieno = $res['order_id'];
			
			
			$accId = (int)$this->input->post('p_account');
            if ($accId <= 0) {
                $this->db->select('id');
                $this->db->from('geopos_accounts');
                $this->db->group_start();
                $this->db->where('employeeID', $this->aauth->get_user()->id);
                $this->db->or_where('employeeID', 1);
                $this->db->group_end();
                $this->db->order_by('employeeID', 'desc');
                $this->db->order_by('id', 'asc');
                $this->db->limit(1);
                $account_query = $this->db->get()->row_array();
                $accId = !empty($account_query['id']) ? (int)$account_query['id'] : 0;
            }
            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $accId);
            $query = $this->db->get();
            $account_d = $query->row_array();
			
			
         /*    $t_data = array(
            'type' => 'Income',
            'cat' => 'Sales',
            'payerid' => $customer_id,
            'method' => $pmethod,
            'date' => $bill_date,
            'eid' =>$emp,
            'tid' => $invocieno,
            'loc' =>$this->aauth->get_user()->loc
        );

            $dual = $this->custom->api_config(65);
            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $dual['key2']);
            $query = $this->db->get();
            $account_d = $query->row_array();
            $t_data['credit'] = $final_total;
           $t_data['debit'] = $final_total;
           $t_data['type'] = 'Sell';
            $t_data['acid'] = $dual['key2'];
            $t_data['account'] = $account_d['holder'];
            $t_data['note'] = 'Credit ' . $tnote;

            $this->db->insert('geopos_transactions', $t_data); 
        
            $this->db->set('lastbal', "lastbal+$final_total", FALSE);
            $this->db->where('id', $dual['key2']);
            $this->db->update('geopos_accounts'); */
		$pmethod = $this->input->post('payment_method', true);
        if (empty($pmethod)) {
            $pmethod = $this->input->post('payment_method_name', true);
        }
        $pamnt = $totalamunt;
         $this->load->model('billing_model', 'billing');
                $tnote = '#' . $invocieno . '-' . $pmethod;
                switch ($pmethod) {
                    case 'Cash' :
                        $r_amt1 = $pamnt;
                        $r_amt2 = 0;
                        $r_amt3 = 0;
                        break;
                    case 'Card Swipe':
                        $r_amt1 = 0;
                        $r_amt2 = $pamnt;
                        $r_amt3 = 0;
                        break;
                    case 'Bank' :
                        $r_amt1 = 0;
                        $r_amt2 = 0;
                        $r_amt3 = $pamnt;
                        break;
					case 'Due' :
                        $r_amt1 = 0;
                        $r_amt2 = 0;
                        $r_amt3 = $pamnt;
                        break;
					case 'UPI' :
                        $r_amt1 = 0;
                        $r_amt2 = 0;
                        $r_amt3 = $pamnt;
                        break;
                }
			 $bill_date= date("Y-m-d");
                if ($totalamunt > 0) {
				    $this->billing->paynow($invocieno, $totalamunt, $tnote, $pmethod, $this->aauth->get_user()->loc, $bill_date, $accId);
                }

	//	if ($pamnt > 0) $this->billing->paynow($invocieno, $pamnt, $tnote, $pmethod, $this->aauth->get_user()->loc, $bill_date, $account);
             //profit calculation
       $t_profit = 0;
        $this->db->select('order_items.product_variant_id, order_items.price, order_items.quantity, product_variants.purchase_price');
        $this->db->from('order_items');
        $this->db->join('product_variants', 'product_variants.id = order_items.product_variant_id', 'left');
        $this->db->where('order_items.order_id', $res['order_id']);
        $query = $this->db->get();
  
        $pids = $query->result_array();
        foreach ($pids as $profit) {
            $t_cost = $profit['purchase_price'] * $profit['quantity'];
            $s_cost = $profit['price'] * $profit['quantity'];

            $t_profit += $s_cost - $t_cost;
        }
       
        $data = array('type' => 9, 'rid' => $res['order_id'], 'col1' => $t_profit, 'd_date' => $bill_date);

        $this->db->insert('geopos_metadata', $data); 

            $this->response['error'] = false;
            $this->response['message'] = "Order Delivered Successfully.";
            $this->response['data'] = $res;
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            print_r(json_encode($this->response));
            return false;
        } catch (\Throwable $e) {
            log_message('error', 'POS place_order failed: ' . $e->getMessage());
            $this->response['error'] = true;
            $this->response['message'] = 'Unable to complete payment right now.';
            $this->response['csrfName'] = $this->security->get_csrf_token_name();
            $this->response['csrfHash'] = $this->security->get_csrf_hash();
            $this->response['data'] = array();
            print_r(json_encode($this->response));
            return false;
        }
    }
}
