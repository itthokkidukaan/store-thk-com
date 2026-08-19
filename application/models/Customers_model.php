<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_model extends CI_Model
{

    var $table = 'users';
    var $column_order = array(null, 'users.username as name', 'users.address', 'users.email', 'users.mobile as phone', null);
    var $column_search = array('users.username', 'users.mobile', 'users.address', 'users.city', 'users.email');
    var $trans_column_order = array('date', 'debit', 'credit', 'account', null);
    var $trans_column_search = array('id', 'date');
    var $inv_column_order = array(null, 'id', 'name', 'invoicedate', 'total', 'status', null);
    var $inv_column_search = array('tid', 'name', 'invoicedate', 'total');
    var $order = array('id' => 'desc');
    var $inv_order = array('orders.id' => 'desc');
    var $qto_order = array('geopos_quotes.tid' => 'desc');
    var $notecolumn_order = array(null, 'title', 'cdate', null);
    var $notecolumn_search = array('id', 'title', 'cdate');
    var $pcolumn_order = array('geopos_projects.status', 'geopos_projects.name', 'geopos_projects.edate', 'geopos_projects.worth', null);
    var $pcolumn_search = array('geopos_projects.name', 'geopos_projects.edate', 'geopos_projects.status');
    var $ptcolumn_order = array('status', 'name', 'duedate', 'start', null, null);
    var $ptcolumn_search = array('name', 'edate', 'status');
    var $porder = array('id' => 'desc');


    private function _get_datatables_query($id = '')
    {
        $due = $this->input->post('due');
        if ($due) {

            $this->db->select('users.*,SUM(orders.final_total) AS total,SUM(orders.total_payable) AS pamnt');
            $this->db->from('orders');
            $this->db->where('orders.status!=', 'paid');
            $this->db->join('users', 'users.id = orders.user_id', 'left');
            if ($this->aauth->get_user()->loc) {
                $this->db->where('users.loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('users.loc', 0);
            }
            if ($id != '') {
                $this->db->where('users.gid', $id);
            }
            $this->db->group_by('orders.user_id');
            $this->db->order_by('total', 'desc');

        } else {
            $this->db->select('users.*, sd.store_name as seller_store_name');
            $this->db->from($this->table);
            // Only include actual customers (group_id = 2), not admins/staff/etc.
            $this->db->join('users_groups ug', 'ug.user_id = users.id', 'inner');
            $this->db->join('seller_data sd', 'sd.user_id = users.assigned_seller', 'left');
            $this->db->where('ug.group_id', 2);
            if ($this->aauth->get_user()->loc) {
                $this->db->where('users.loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('users.loc', 0);
            }
            if ($id != '') {
                $this->db->where('users.gid', $id);
            }

        }
         //   $this->db->join('geopos_employees', 'geopos_employees.id = users.assigned', 'left');
        $i = 0;

        foreach ($this->column_search as $item) {
            $search = $this->input->post('search');
            $value  = is_array($search) ? $search['value'] : '';
            if ($value) {
                if ($i === 0) {
                    $this->db->group_start(); // Start OR group
                }
                // Only apply LIKE to raw column names
                $this->db->or_like($item, $value);

                if ($i === count($this->column_search) - 1) {
                    $this->db->group_end(); // End OR group
                }
            }
            $i++;
        }

        $order = $this->input->post('order');
        if ($order) {
            $col = isset($this->column_order[$order['0']['column']]) ? $this->column_order[$order['0']['column']] : null;
            if ($col) {
                $this->db->order_by($col, $order['0']['dir']);
            }
        } else if (isset($this->order)) {
            $ord = $this->order;
            $this->db->order_by(key($ord), $ord[key($ord)]);
        }
    }

    function get_datatables($id = '')
    {
        $is_seller = is_seller_user();
		
	//echo	$this->session->userdata('user_id');
        $this->_get_datatables_query($id);
        if ($is_seller) {
            $this->db->where('users.assigned_seller', (int)$this->session->userdata('user_id'));
        } elseif ($this->aauth->get_user()->id != 1) {
            $this->db->where('users.assigned', $this->aauth->get_user()->id);
        }
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }


  function getUsernameById($id) {
        $this->db->select('username');
        $this->db->from('geopos_employees');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->username;
        } else {
            return 'N/A';
        }
    }
    function count_filtered($id = '')
    {
        $is_seller = is_seller_user();
        $this->_get_datatables_query();
        if ($is_seller) {
            $this->db->where('users.assigned_seller', (int)$this->session->userdata('user_id'));
        } elseif ($this->aauth->get_user()->id != 1) {
            $this->db->where('users.assigned', $this->aauth->get_user()->id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($id = '')
    {
        $is_seller = is_seller_user();
        // Join users_groups to count only real customers (group_id = 2)
        $this->db->from($this->table);
        $this->db->join('users_groups ug', 'ug.user_id = users.id', 'inner');
        $this->db->where('ug.group_id', 2);
        if ($is_seller) {
            $this->db->where('users.assigned_seller', (int)$this->session->userdata('user_id'));
        } elseif ($this->aauth->get_user()->id != 1) {
            $this->db->where('users.assigned', $this->aauth->get_user()->id);
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('users.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('users.loc', 0);
        }
        if ($id != '') {
            $this->db->where('users.gid', $id);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function details($custid,$loc=true)
    {
        $this->db->select('*');
        $this->db->from($this->table);
      //  $this->db->join('users', 'users.cid=geopos_customers.id', 'left');
        $this->db->where('users.id', $custid);
        if($loc) {
            if ($this->aauth->get_user()->loc) {
                $this->db->where('users.loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('users.loc', 0);
            }
        }
        $query = $this->db->get();
        return $query->row_array();
    }
	
	public function getcategorylist(){
		
		
		$this->db->distinct(); // Unique rows laane ke liye
$this->db->select('categories.id, categories.name');
$this->db->from('categories');
$this->db->join('products', 'categories.id = products.category_id');
$this->db->order_by('categories.name', 'ASC'); // Name ke hisaab se sort karein

$query = $this->db->get(); // Query execute karein
$result = $query->result_array(); // Result ko array me convert karein

return $result; // Res
	}

    public function money_details($custid)
    {

        $this->db->select('SUM(debit) AS debit,SUM(credit) AS credit');
        $this->db->from('geopos_transactions');
        $this->db->where('payerid', $custid);
        $this->db->where('ext', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function due_details($custid)
    {

        $this->db->select('SUM(total) AS total,SUM(pamnt) AS pamnt,SUM(discount) AS discount,');
        $this->db->from('orders');
        $this->db->where('user_id', $custid);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function pending_amount($custid)
    {
        $this->db->select('COALESCE(SUM(orders.pending_amount),0) as pending_amount');
        $this->db->from('orders');
        $this->db->where('orders.user_id', $custid);
        $this->db->where('orders.status !=', 'canceled');
        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $this->db->where("orders.id IN (SELECT order_id FROM order_items WHERE seller_id = $seller_id)", NULL, FALSE);
        }
        $query = $this->db->get();
        return $query->row_array();
    }


    public function add($name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $customergroup, $taxid, $name_s, $phone_s, $email_s, $address_s, $city_s, $region_s, $country_s, $postbox_s, $language = '', $create_login = true, $password = '', $docid = '', $custom = '', $discount = 0)
    {
        $is_seller = is_seller_user();
        $this->db->select('email');
        $this->db->from('geopos_customers');
        $this->db->where('email', $email);
        $query = $this->db->get();
        $valid = $query->row_array();
		
		
        if (!$valid['email']) {


            if (!$discount) {
                $this->db->select('disc_rate');
                $this->db->from('geopos_cust_group');
                $this->db->where('id', $customergroup);
                $query = $this->db->get();
                $result = $query->row_array();
                $discount = $result['disc_rate'];
            }


            $data = array(
                'name' => $name,
                'company' => $company,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'city' => $city,
                'region' => $region,
                'country' => $country,
                'postbox' => $postbox,
                'gid' => $customergroup,
                'taxid' => $taxid,
                'name_s' => $name_s,
                'phone_s' => $phone_s,
                'email_s' => $email_s,
                'address_s' => $address_s,
                'city_s' => $city_s,
                'region_s' => $region_s,
                'country_s' => $country_s,
                'postbox_s' => $postbox_s,
                'docid' => $docid,
                'custom1' => $custom,
                 'assigned' => $is_seller ? (int)$this->session->userdata('user_id') : $this->aauth->get_user()->id,
                'discount_c' => $discount
            );


            if ($this->aauth->get_user()->loc) {
                $data['loc'] = $this->aauth->get_user()->loc;
            }

            if ($this->db->insert('geopos_customers', $data)) {
                $cid = $this->db->insert_id();
                $p_string = '';
                $temp_password = '';
                if ($create_login) {

                    if ($password) {
                        $temp_password = $password;
                    } else {
                        $temp_password = rand(200000, 999999);
                    }

                    $pass = password_hash($temp_password, PASSWORD_DEFAULT);
                  /*   $data = array(
                       
                        'status' => 'active',
                        'is_deleted' => 0,
                        'name' => $name,
                        'password' => $pass,
                        'email' => $email,
                        'user_type' => 'Member',
                        'cid' => $cid,
                        'lang' => $language
                    ); */
					
					   $data = array(
            'email' => $email,
            'mobile' => $phone,  
            'password' => $pass, // Password cannot be blank but user_id required for salt, setting bad password for now
            'username' => (!$name) ? '' : $name,
            'created_at' => date("Y-m-d H:i:s"),
			'picture'=>'example.png',
 			'assigned'=> $is_seller ? 0 : $this->aauth->get_user()->id,
 			'assigned_seller'=> $is_seller ? (int)$this->session->userdata('user_id') : 0
        );

                    $this->db->insert('users', $data);
                    $new_user_id = $this->db->insert_id();

                    // Assign customer to group_id=2 so they appear in New Signups & customer list
                    if ($new_user_id) {
                        $this->db->insert('users_groups', [
                            'user_id'  => $new_user_id,
                            'group_id' => 2
                        ]);
                    }

				//	echo $this->db->last_query();
                    $p_string = ' Temporary Password is ' . $temp_password . ' ';
                }
                $this->aauth->applog("[Client Added] $name ID " . $cid, $this->aauth->get_user()->username);
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('ADDED') . $p_string . '&nbsp;<a href="' . base_url('customers/view?id=' . $cid) . '" class="btn btn-info btn-sm"><span class="icon-eye"></span>' . $this->lang->line('View') . '</a>', 'cid' => $cid, 'pass' => $temp_password, 'discount' => amountFormat_general($discount)));

                $this->custom->save_fields_data($cid, 1);

                $this->db->select('other');
                $this->db->from('univarsal_api');
                $this->db->where('id', 64);
                $query = $this->db->get();
                $othe = $query->row_array();

                if ($othe['other']) {
                    $auto_mail = $this->send_mail_auto($email, $name, $temp_password);
                    $this->load->model('communication_model');
                    $attachmenttrue = false;
                    $attachment = '';
                    $this->communication_model->send_corn_email($email, $name, $auto_mail['subject'], $auto_mail['message'], $attachmenttrue, $attachment);
                }

            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                'Duplicate Email'));
        }

    }


    public function edit($id, $name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $customergroup, $taxid, $name_s, $phone_s, $email_s, $address_s, $city_s, $region_s, $country_s, $postbox_s, $docid = '', $custom = '', $whatsappmobile= '', $discount = 0 , $managermobile= '', $paymentterm= '', $repeatorder= '', $document_file= '')
    {
		$language = '';
        $data = array(
            'username' => $name,
            'company' => $company,
            'mobile' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'pincode' => $postbox,
            'gid' => $customergroup,
            'taxid' => $taxid,
            'name_s' => $name_s,
            'phone_s' => $phone_s,
            'email_s' => $email_s,
            'address_s' => $address_s,
            'city' => $city_s,
            'country_s' => $country_s,
            'postbox_s' => $postbox_s,
            'docid' => $docid,
            'custom1' => $custom,
            'whatsapp_mobile' => $whatsappmobile,
            'manager_mobile' => $managermobile,
            'paymentterm' => $paymentterm,
            'repeatorder' => $repeatorder,
            'document_file' => $document_file
        );


        $this->db->set($data);
        $this->db->where('id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }

        if ($this->db->update('users')) {
            $data = array(
                'name' => $name,
                'email' => $email,
                'lang' => $language
            );
          /*   $this->db->set($data);
            $this->db->where('cid', $id);
            $this->db->update('users'); */
            $this->aauth->applog("[Client Updated] $name ID " . $id, $this->aauth->get_user()->username);
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));

            $this->custom->edit_save_fields_data($id, 1);
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function changepassword($id, $password)
    {
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'password' => $pass
        );


        $this->db->set($data);
        $this->db->where('cid', $id);

        if ($this->db->update('users')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function editpicture($id, $pic)
    {
        $this->db->select('picture');
        $this->db->from($this->table);
        $this->db->where('id', $id);

        $query = $this->db->get();
        $result = $query->row_array();


        $data = array(
            'picture' => $pic
        );


        $this->db->set($data);
        $this->db->where('id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        if ($this->db->update('geopos_customers') AND $result['picture'] != 'example.png') {

            unlink(FCPATH . 'userfiles/customers/' . $result['picture']);
            unlink(FCPATH . 'userfiles/customers/thumbnail/' . $result['picture']);
        }


    }

    public function group_list()
    {
        $whr = "";
        if ($this->aauth->get_user()->loc) {
            $whr = "WHERE (geopos_customers.loc=" . $this->aauth->get_user()->loc . " ) ";
            if (BDATA) $whr = "WHERE (geopos_customers.loc=" . $this->aauth->get_user()->loc . " OR geopos_customers.loc=0 ) ";
        } elseif (!BDATA) {
            $whr = "WHERE  geopos_customers.loc=0  ";
        }

        $query = $this->db->query("SELECT c.*,p.pc FROM geopos_cust_group AS c LEFT JOIN ( SELECT gid,COUNT(gid) AS pc FROM geopos_customers $whr GROUP BY gid) AS p ON p.gid=c.id");
        return $query->result_array();
    }

    public function delete($id)
    {


        if ($this->aauth->get_user()->loc) {
            $this->db->delete('geopos_customers', array('id' => $id, 'loc' => $this->aauth->get_user()->loc));

        } elseif (!BDATA) {
            $this->db->delete('geopos_customers', array('id' => $id, 'loc' => 0));
        } else {
            $this->db->delete('geopos_customers', array('id' => $id));
        }

        if ($this->db->affected_rows()) {
            $this->aauth->applog("[Client Deleted]  ID " . $id, $this->aauth->get_user()->username);
            $this->db->delete('users', array('cid' => $id));
            $this->custom->del_fields($id, 1);
            $this->db->delete('geopos_notes', array('fid' => $id, 'rid' => 1));
            //docs
            $this->db->select('filename');
            $this->db->from('geopos_documents');
            $this->db->where('id', $id);
            $query = $this->db->get();
            $result = $query->row_array();
            if ($this->db->delete('geopos_documents', array('fid' => $id, 'rid' => 1))) {
                @unlink(FCPATH . 'userfiles/documents/' . $result['filename']);
                $this->aauth->applog("[Client Doc Deleted]  DocId $id CID " . $id, $this->aauth->get_user()->username);
                //docs

            }
            return true;
        }

    }


    //transtables

    function trans_table($id)
    {
        $this->_get_trans_table_query($id);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }


    private function _get_trans_table_query($id)
    {

        $this->db->from('geopos_transactions');
        $this->db->where('payerid', $id);
        $this->db->where('ext', 0);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $i = 0;
        foreach ($this->trans_column_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->trans_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) // here order processing
        {
            $this->db->order_by($this->trans_column_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function trans_count_filtered($id = '')
    {
        $this->_get_trans_table_query($id);
        $query = $this->db->get();
        if ($id != '') {
            $this->db->where('payerid', $id);
        }
        return $query->num_rows($id = '');
    }

    public function trans_count_all($id = '')
    {
        $this->_get_trans_table_query($id);
        $query = $this->db->get();
        if ($id != '') {
            $this->db->where('payerid', $id);
        }
    }

    private function _inv_datatables_query($id, $tyd = 0)
    {
        $this->db->select('orders.*');
        $this->db->from('orders');
        $this->db->where('orders.user_id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('orders.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('orders.loc', 0);
        }
		
		  if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(orders.date_added) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(orders.date_added) <=', datefordatabase($this->input->post('end_date')));
        }
		
		if ($this->input->post('txttype') && $this->input->post('txttype') !='All') // if datatable send POST for search
        {
           
            $this->db->where('status', $this->input->post('txttype'));
        }

        if ($tyd) $this->db->where('orders.i_class>', 1);
        $this->db->join('users', 'orders.user_id=users.id', 'left');

        $i = 0;

        foreach ($this->inv_column_search as $item) // loop column
        {
            if ($this->input->post('search')['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $this->input->post('search')['value']);
                } else {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }

                if (count($this->inv_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->inv_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->inv_order)) {
            $order = $this->inv_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function inv_datatables($id, $tyd = 0)
    {
        $this->_inv_datatables_query($id, $tyd);

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function inv_count_filtered($id)
    {
        $this->_inv_datatables_query($id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function inv_count_all($id)
    {
        $this->db->from('orders');
        $this->db->where('user_id', $id);
		  if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(orders.date_added) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(orders.date_added) <=', datefordatabase($this->input->post('end_date')));
        }
		
		if ($this->input->post('txttype') && $this->input->post('txttype') !='All') // if datatable send POST for search
        {
           
            $this->db->where('status', $this->input->post('txttype'));
        }
        return $this->db->count_all_results();
    }


    private function _qto_datatables_query($id, $tyd = 0)
    {
        $this->db->select('geopos_quotes.*');
        $this->db->from('geopos_quotes');
        $this->db->where('geopos_quotes.csd', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_quotes.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('geopos_quotes.loc', 0);
        }
        $this->db->join('geopos_customers', 'geopos_quotes.csd=geopos_customers.id', 'left');

        $i = 0;

        foreach ($this->inv_column_search as $item) // loop column
        {
            if ($this->input->post('search')['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $this->input->post('search')['value']);
                } else {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }

                if (count($this->inv_column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->qto_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->qto_order)) {
            $order = $this->qto_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function qto_datatables($id, $tyd = 0)
    {
        $this->_qto_datatables_query($id);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function qto_count_filtered($id)
    {
        $this->_qto_datatables_query($id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function qto_count_all($id)
    {
        $this->db->from('geopos_quotes');
        $this->db->where('csd', $id);
        return $this->db->count_all_results();
    }

    public function group_info($id)
    {

        $this->db->from('geopos_cust_group');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function activity($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_metadata');
        $this->db->where('type', 21);
        $this->db->where('rid', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function recharge($id, $amount)
    {

        $this->db->set('balance', "balance+$amount", FALSE);
        $this->db->where('id', $id);

        $this->db->update('geopos_customers');

        $data = array(
            'type' => 21,
            'rid' => $id,
            'col1' => $amount,
            'col2' => date('Y-m-d H:i:s') . ' Account Recharge by ' . $this->aauth->get_user()->username
        );


        if ($this->db->insert('geopos_metadata', $data)) {
            $this->aauth->applog("[Client Wallet Recharge] Amt-$amount ID " . $id, $this->aauth->get_user()->username);
            return true;
        } else {
            return false;
        }

    }

    private function _project_datatables_query($cday = '')
    {
        $this->db->select("geopos_projects.*,geopos_customers.name AS customer");
        $this->db->from('geopos_projects');
        $this->db->join('geopos_customers', 'geopos_projects.cid = geopos_customers.id', 'left');


        $this->db->where('geopos_projects.cid=', $cday);


        $i = 0;

        foreach ($this->pcolumn_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->pcolumn_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) {
            $this->db->order_by($this->column_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->porder)) {
            $order = $this->porder;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function project_datatables($cday = '')
    {


        $this->_project_datatables_query($cday);

        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    function project_count_filtered($cday = '')
    {
        $this->_project_datatables_query($cday);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function project_count_all($cday = '')
    {
        $this->_project_datatables_query($cday);
        $query = $this->db->get();
        return $query->num_rows();
    }

    //notes

    private function _notes_datatables_query($id)
    {


        $today = date('Y-m-d'); 
        $this->db->select('geopos_notes.*, users.username');
		 $this->db->join('users', 'users.id = geopos_notes.fid', 'left');
        $this->db->from('geopos_notes');
		if($id=='appointment'){
			if($this->input->post('type')=='upcoming'){
			        $this->db->where('upcoming_date >', $today); // Condition
			}else{
				
				  $this->db->where('upcoming_date', $today);
			}
		}else{
			
        $this->db->where('fid', $id);
			
		}
        $this->db->where('ntype', 1);
        $i = 0;

        foreach ($this->notecolumn_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) {
            $this->db->order_by($this->notecolumn_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function notes_datatables($id)
    {
        $this->_notes_datatables_query($id);
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
		//echo $this->db->last_query();
        return $query->result();
    }

    function notes_count_filtered($id)
    {
        $this->_notes_datatables_query($id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function notes_count_all($id)
    {
        $this->_notes_datatables_query($id);
        $query = $this->db->get();
        return $query->num_rows();
    } 


	public function notes_count_alltoday()
    {
	  $today = date('Y-m-d'); 
		 $this->db->where('upcoming_date', $today);
		$this->db->from('geopos_notes');
        $query = $this->db->get();
        return $query->num_rows();
    }


	public function notes_count_allcoming()
    {
	  $today = date('Y-m-d'); 
		$this->db->where('upcoming_date >', $today); 
		$this->db->from('geopos_notes');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function editnote($id, $title, $content, $cid, $upcomingdate)
    {

        $data = array('title' => $title, 'content' => $content, 'last_edit' => date('Y-m-d H:i:s'), 'upcoming_date' => date('Y-m-d', strtotime($upcomingdate)));


        $this->db->set($data);
        $this->db->where('id', $id);
        $this->db->where('fid', $cid);


        if ($this->db->update('geopos_notes')) {
            $this->aauth->applog("[Client Note Edited]  NoteId $id CID " . $cid, $this->aauth->get_user()->username);
            return true;
        } else {
            return false;
        }

    }

    public function note_v($id, $cid)
    {
        $this->db->select('*');
        $this->db->from('geopos_notes');
        $this->db->where('id', $id);
        $this->db->where('fid', $cid);
        $query = $this->db->get();
        return $query->row_array();
    }

    function addnote($title, $content, $cid, $upcomingdate)
    {
        $this->aauth->applog("[Client Note Added]  NoteId $title CID " . $cid, $this->aauth->get_user()->username);
        $data = array('title' => $title, 'content' => $content, 'cdate' => date('Y-m-d'), 'last_edit' => date('Y-m-d H:i:s'), 'upcoming_date' =>  date('Y-m-d', strtotime($upcomingdate)),'cid' => $this->aauth->get_user()->id, 'fid' => $cid, 'rid' => 1, 'ntype' => 1);
        return $this->db->insert('geopos_notes', $data);

    }

    function deletenote($id, $cid)
    {
        $this->aauth->applog("[Client Note Deleted]  NoteId $id CID " . $cid, $this->aauth->get_user()->username);
        return $this->db->delete('geopos_notes', array('id' => $id, 'fid' => $cid, 'rid' => 1));

    }

    //documents list

    var $doccolumn_order = array(null, 'title', 'cdate', null);
    var $doccolumn_search = array('title', 'cdate');

    public function documentlist($cid)
    {
        $this->db->select('*');
        $this->db->from('geopos_documents');
        $this->db->where('fid', $cid);
        $this->db->where('rid', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    function adddocument($title, $filename, $cid)
    {
        $this->aauth->applog("[Client Doc Added]  DocId $title CID " . $cid, $this->aauth->get_user()->username);
        $data = array('title' => $title, 'filename' => $filename, 'cdate' => date('Y-m-d'), 'cid' => $this->aauth->get_user()->id, 'fid' => $cid, 'rid' => 1);
        return $this->db->insert('geopos_documents', $data);

    }

    function deletedocument($id, $cid)
    {
        $this->db->select('filename');
        $this->db->from('geopos_documents');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result = $query->row_array();
        $this->db->trans_start();
        if ($this->db->delete('geopos_documents', array('id' => $id, 'fid' => $cid, 'rid' => 1))) {
            if (@unlink(FCPATH . 'userfiles/documents/' . $result['filename'])) {
                $this->aauth->applog("[Client Doc Deleted]  DocId $id CID " . $cid, $this->aauth->get_user()->username);
                $this->db->trans_complete();
                return true;
            } else {
                $this->db->trans_rollback();
                return false;
            }

        } else {
            return false;
        }
    }


    function document_datatables($cid)
    {
        $this->document_datatables_query($cid);
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    private function document_datatables_query($cid)
    {

        $this->db->from('geopos_documents');
        $this->db->where('fid', $cid);
        $this->db->where('rid', 1);
        $i = 0;

        foreach ($this->doccolumn_search as $item) // loop column
        {
            $search = $this->input->post('search');
            $value = $search['value'];
            if ($value) {

                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->doccolumn_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) {
            $this->db->order_by($this->doccolumn_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function document_count_filtered($cid)
    {
        $this->document_datatables_query($cid);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function document_count_all($cid)
    {
		
        $this->document_datatables_query($cid);
		
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function send_mail_auto($email, $name, $password)
    {
        $this->load->library('parser');
        $this->load->model('templates_model', 'templates');
        $template = $this->templates->template_info(16);

        $data = array(
            'Company' => $this->config->item('ctitle'),
            'NAME' => $name
        );
        $subject = $this->parser->parse_string($template['key1'], $data, TRUE);

        $data = array(
            'Company' => $this->config->item('ctitle'),
            'NAME' => $name,
            'EMAIL' => $email,
            'URL' => base_url() . 'crm',
            'PASSWORD' => $password,
            'CompanyDetails' => '<h6><strong>' . $this->config->item('ctitle') . ',</strong></h6>
<address>' . $this->config->item('address') . '<br>' . $this->config->item('address2') . '</address>
             ' . $this->lang->line('Phone') . ' : ' . $this->config->item('phone') . '<br>  ' . $this->lang->line('Email') . ' : ' . $this->config->item('email'),


        );
        $message = $this->parser->parse_string($template['other'], $data, TRUE);


        return array('subject' => $subject, 'message' => $message);
    }


    public function recipients($ids)
    {

        $this->db->select('id,name,email,phone');
        $this->db->from('geopos_customers');
        $this->db->where_in('id', $ids);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function sales_due($sdate, $edate, $csd, $trans_type, $pay = true, $amount = 0, $acc = 0, $pay_method = '',$note='')
    {
		
		
        if ($pay) {
            $this->db->select_sum('total');
            $this->db->select_sum('pamnt');
            $this->db->from('orders');
            $this->db->where('DATE(date_added) >=', $sdate);
            $this->db->where('DATE(date_added) <=', $edate);
            $this->db->where('user_id', $csd);
           
		 $this->db->where('status', $trans_type);
            if ($this->aauth->get_user()->loc) {
                $this->db->where('loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }

            $query = $this->db->get();
            $result = $query->row_array();
            return $result;
        } else {
            if ($amount) {
                $this->db->select('id,id as tid,final_total as total,pamnt');
                $this->db->from('orders');
                $this->db->where('DATE(date_added) >=', $sdate);
                $this->db->where('DATE(date_added) <=', $edate);
                $this->db->where('user_id', $csd);
          
	 $this->db->where('status', $trans_type);
		
                if ($this->aauth->get_user()->loc) {
                    $this->db->where('loc', $this->aauth->get_user()->loc);
                } elseif (!BDATA) {
                    $this->db->where('loc', 0);
                }

                $query = $this->db->get();
				
                $result = $query->result_array();
				
				
                $amount_custom = $amount;

                foreach ($result as $row) {
                    $note.=' #'.$row['tid'];
                    $due = $row['total'] - $row['pamnt'];
                    if ($amount_custom >= $due) {
                        $this->db->set('status', 'paid');
                      //  $this->db->set('pamnt', "pamnt+$due", FALSE);
						$this->db->set('pamnt', 'pamnt+'.$due, FALSE);
                        $amount_custom = $amount_custom - $due;
                    } elseif ($amount_custom > 0 AND $amount_custom < $due) {
                        $this->db->set('status', 'partial');
                      //  $this->db->set('pamnt', "pamnt+$amount_custom", FALSE);
						$this->db->set('pamnt', 'pamnt+'.$amount_custom, FALSE);
                        $amount_custom = 0;
                    }

                    $this->db->set('payment_method', $pay_method);
                   
					 $this->db->set('paid_date', date('Y-m-d H:i:s'));
                    $this->db->where('id', $row['id']);
					
                    $this->db->update('orders');
				//	echo $this->db->last_query();
                    if ($amount_custom == 0) break;

                }
                  $this->db->select('id,holder');
        $this->db->from('geopos_accounts');
        $this->db->where('id', $acc);
        $query = $this->db->get();
        $account = $query->row_array();

                        $data = array(
            'acid' => $account['id'],
            'account' => $account['holder'],
            'type' => 'Income',
            'cat' => 'Sales',
            'credit' => $amount,
            'payer' => $this->lang->line('Bulk Payment Invoices'),
            'payerid' => $csd,
            'method' => $pay_method,
            'date' => date('Y-m-d'),
            'eid' => $this->aauth->get_user()->id,
            'tid' => 0,
            'note' => $note,
            'loc' => $this->aauth->get_user()->loc
        );

        $this->db->insert('geopos_transactions', $data);
        $tttid = $this->db->insert_id();
		            $this->db->set('lastbal', "lastbal+$amount", FALSE);
                    $this->db->where('id', $account['id']);
                    $this->db->update('geopos_accounts');

            }

        }
    }


}