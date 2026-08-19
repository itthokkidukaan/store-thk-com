<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class Invoices extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model', 'invocies');
         $this->load->model('plugins_model', 'plugins');
        $this->load->library("Aauth");

        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(1)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }

        if ($this->aauth->get_user() && $this->aauth->get_user()->roleid == 2) {
            $this->limited = $this->aauth->get_user()->id;
        } else {
            $this->limited = '';
        }
        $this->load->library("Custom");
        $this->li_a = 'sales';

    }

    //create invoice
    public function create()
    {

         $data['emp'] = $this->plugins->universal_api(69);
        if ($data['emp']['key1']) {
            $this->load->model('employee_model', 'employee');
            $data['employee'] = $this->employee->list_employee();
        }

        $this->load->library("Common");
        $data['custom_fields_c'] = $this->custom->add_fields(1);

        $this->load->model('customers_model', 'customers');
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $data['customergrouplist'] = $this->customers->group_list();
        $data['lastinvoice'] = $this->invocies->lastinvoice();
        $data['warehouse'] = $this->invocies->warehouses();
        $data['terms'] = $this->invocies->billingterms();
        $data['currency'] = $this->invocies->currencies();
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist($this->config->item('tax'));
        $head['title'] = "New Invoice";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['taxdetails'] = $this->common->taxdetail();
        $data['custom_fields'] = $this->custom->add_fields(2);
        $this->load->view('fixed/header', $head);
        $this->load->view('invoices/newinvoice', $data);
        $this->load->view('fixed/footer');
    }

    //edit invoice
    public function edit()
    {
        $tid = intval($this->input->get('id'));
        if (!$this->aauth->premission(13)) {
            redirect('invoices/view?id=' . $tid, 'refresh');
        }
        $data['id'] = $tid;
        $data['title'] = "Edit Invoice $tid";
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
        $data['terms'] = $this->invocies->billingterms();
        $data['currency'] = $this->invocies->currencies();
        $data['invoice'] = $this->invocies->invoice_details($tid, $this->limited);
        if ($data['invoice']['id']) $data['products'] = $this->invocies->items_with_product($tid);
        $head['title'] = "Edit Invoice #$tid";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->invocies->warehouses();
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist_edit($data['invoice']['taxstatus']);

         $this->load->library("Common");
          $data['custom_fields_c'] = $this->custom->add_fields(1);
        $data['custom_fields'] = $this->custom->add_fields(2);
        $data['custom_fields'] = $this->custom->view_edit_fields($tid, 2);




        $this->load->view('fixed/header', $head);
        if ($data['invoice']['id']) $this->load->view('invoices/edit', $data);
        $this->load->view('fixed/footer');

    }

    //invoices list
    public function index()
    {
		  

            $this->load->model('employee_model', 'employee');

            $data['employee'] = $this->employee->list_employee();

        
        $head['title'] = "Manage Invoices";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('invoices/invoices', $data);
        $this->load->view('fixed/footer');
    } 


	public function trashinvoices()
    {
        $head['title'] = "Manage Invoices";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('invoices/trashinvoices');
        $this->load->view('fixed/footer');
    }

    //action
    public function action()
    {
        $currency = $this->input->post('mcurrency');
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('invocieno');
        $invoicedate = $this->input->post('invoicedate');
        $invocieduedate = $this->input->post('invocieduedate');
        $customerphone = $this->input->post('customerphone');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $ship_taxtype = $this->input->post('ship_taxtype');
        $disc_val = numberClean($this->input->post('disc_val'));
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
        $project = $this->input->post('prjid');
        $total_tax = 0;
        $total_discount = rev_amountExchange_s($this->input->post('after_disc'), $currency, $this->aauth->get_user()->loc);
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms', true);
        $i = 0;
        if ($discountFormat == '0') {
            $discstatus = 0;
        } else {
            $discstatus = 1;
        }
        if ($customer_id == 0) {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('Please add a new client')));
            exit;
        }

        $this->load->model('plugins_model', 'plugins');
        $empl_e = $this->plugins->universal_api(69);
        if ($empl_e['key1']) {
           
            $emp = $this->aauth->get_user()->id;
        } else {
            $emp = $this->aauth->get_user()->id;
        }
		
	
		

        $transok = true;
        $st_c = 0;
        $this->load->library("Common");
        $this->db->trans_start();
        //Invoice Data
        $bill_date = datefordatabase($invoicedate);
        $bill_due_date = datefordatabase($invocieduedate);

        $this->db->select('id');
        $this->db->from('orders');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $this->db->where('id', $invocieno);
        $this->db->where('i_class', 0);
        $query = $this->db->get();
        if(@$query->row()->id){
            $this->db->select('id');
            $this->db->from('orders');
            $this->db->order_by('id', 'DESC');
            $this->db->limit(1);
            $this->db->where('i_class', 0);
            $query = $this->db->get();
            $invocieno=$query->row()->id+1;
        }

        $data = array('id' => $invocieno, 'date_added' => date('Y-m-d H:i:s'), 'mobile' =>$customerphone, 'invoiceduedate' => $bill_due_date, 'final_total' => $subtotal, 'total_payable' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount_rate' => $disc_val, 'total' => $total, 'notes' => $notes, 'user_id' => $customer_id, 'created_by' => $emp, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms,  'loc' => $this->aauth->get_user()->loc, 'orderdone_by' => 'Manual','status' => 'due');
        $invocieno2 = $invocieno;
	
        if ($this->db->insert('orders', $data)) {
            $invocieno = $this->db->insert_id();
            //products
            $pid = $this->input->post('pid');
            $productlist = array();
            $prodindex = 0;
            $itc = 0;
            $product_id = $this->input->post('pid');
            $product_name1 = $this->input->post('product_name', true);
            $product_qty = $this->input->post('product_qty');
            $product_price = $this->input->post('product_price');
            $product_article = $this->input->post('product_article');
            $product_tax = $this->input->post('product_tax');
            $product_discount = $this->input->post('product_discount');
            $product_subtotal = $this->input->post('product_subtotal');
            $ptotal_tax = $this->input->post('taxa');
            $ptotal_disc = $this->input->post('disca');
            $product_des = $this->input->post('product_description', true);
            $product_unit = $this->input->post('product_unit');
            $product_hsn = $this->input->post('hsn', true);
            $product_alert = $this->input->post('alert');
            $product_serial = $this->input->post('serial');
            foreach ($pid as $key => $value) {
				
				
			$product_variant = $this->db->select('tax.percentage as tax_percentage, tax.title as tax_name, p.seller_id, p.name as product_name, p.is_prices_inclusive_tax')
    ->join('categories c', 'p.category_id = c.id', 'left')
    ->join('taxes tax', 'tax.id = p.tax', 'left')
    ->where_in('p.id', $product_id[$key])
    ->get('products p')
    ->row_array();



 $converted = convert_to_base_unit($product_unit[$key]);


        $prqty = $converted['qty'] * $product_qty[$key];
		
		$baseunit = $converted['unit'];
$seller_id = isset($product_variant['seller_id']) ? $product_variant['seller_id'] : 0;

			 
			 
                $total_discount += numberClean(@$ptotal_disc[$key]);
                $total_tax += numberClean($ptotal_tax[$key]);
                $data = array(
                    'order_id' => $invocieno,
                    'product_id' => $product_id[$key],
                    'seller_id' => $seller_id,
                    'product_name' => $product_name1[$key],
                    'product_variant_id' => $product_id[$key],
                    'product_article' => $product_article[$key],
                    'code' => $product_hsn[$key],
                    'quantity' => numberClean($product_qty[$key]),
                    'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'discounted_price' => numberClean($product_discount[$key]),
                    'sub_total' => rev_amountExchange_s($product_qty[$key] * $product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'tax_amount' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                    'discount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                    'date_added' => date('Y-m-d H:i:s'),
					'status' =>  json_encode(array(array('received', date("d-m-Y h:i:sa")))),
                    'variant_name' => $product_unit[$key],
					'active_status' => 'received',
                );

                $productlist[$prodindex] = $data;
                $i++;
                $prodindex++;
                $amt = numberClean($product_qty[$key]);
           
				
				 $oldunit = get_stock_by_product_id($product_id[$key]);
			$stock = $oldunit - $prqty;
			
						$legerdata= array(
				
			
				'order_id' => $invocieno,
				'product_id' => $product_id[$key],
				'product_variants' => $product_id[$key],
				'product_name' =>$product_name1[$key],
				'seller_id' => $seller_id, 
				'customer_id' =>  $customer_id,
				'sell_qty' => numberClean($prqty),
				'purchage_qty' => '',
				'sell_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
				'purchage_amount' => '0.00',
				'open_stock' => $oldunit,
				'close_stock' => $stock,
				'created_date' => date('Y-m-d H:i:s'),
				'created_by' => $this->aauth->get_user()->id,
				'purchage_rate' => '0.00',
				'unit' => $baseunit,
				'sell_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
				'ledger_type'=> 'Sell'
				
				
				);
				
				$this->db->insert('product_ledger', $legerdata);
				
				
                $itc += $amt;
			$this->get_productstock($product_id[$key], $prqty);
            }
          /*   if (count($product_serial) > 0) {
                $this->db->set('status', 1);
                $this->db->where_in('serial', $product_serial);
                $this->db->update('geopos_product_serials');
            } */
            if ($prodindex > 0) {
            $this->db->insert_batch('order_items', $productlist);
				
	//	echo $this->db->last_query();
				
				
                $this->db->set(array('discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc), 'tax_amount' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc), 'items' => $itc));
                $this->db->where('id', $invocieno);
                $this->db->update('orders');
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    "Please choose product from product list. Go to Item manager section if you have not added the products."));
                $transok = false;
            }
            $tnote = '#' . $invocieno . '-' ;
          $d_trans = $this->plugins->universal_api(69);
        if ($d_trans['key2']) {
            $t_data = array(
            'type' => 'Income',
            'cat' => 'Sales',
            'payerid' => $customer_id,
            'method' => 'Auto',
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
            $t_data['credit'] = 0;
           $t_data['debit'] = $total;
           $t_data['type'] = 'Expense';
            $t_data['acid'] = $dual['key2'];
            $t_data['account'] = $account_d['holder'];
            $t_data['note'] = 'Debit ' . $tnote;

            $this->db->insert('geopos_transactions', $t_data);
            //account update
            $this->db->set('lastbal', "lastbal-$total", FALSE);
            $this->db->where('id', $dual['key2']);
            $this->db->update('geopos_accounts');

        }
            if ($transok) {
                $validtoken = hash_hmac('ripemd160', $invocieno, $this->config->item('encryption_key'));
                $link = base_url('billing/view?id=' . $invocieno . '&token=' . $validtoken);
                echo json_encode(array('status' => 'Success', 'message' =>
                    $this->lang->line('Invoice Success') . " <a href='view?id=$invocieno' class='btn btn-primary btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> " . $this->lang->line('View') . "  </a> &nbsp; &nbsp;<a href='printinvoice?id=$invocieno' class='btn btn-blue btn-lg' target='_blank'><span class='fa fa-print' aria-hidden='true'></span> " . $this->lang->line('Print') . "  </a> &nbsp; &nbsp; <a href='$link' class='btn btn-purple btn-lg'><span class='fa fa-globe' aria-hidden='true'></span> " . $this->lang->line('Public View') . " </a> &nbsp; &nbsp; <a href='create' class='btn btn-warning btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span></a>"));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                "Invalid Entry!"));
            $transok = false;
        }
        if ($transok) {
            if ($this->aauth->premission(4) and $project > 0) {
                $data = array('pid' => $project, 'meta_key' => 11, 'meta_data' => $invocieno, 'value' => '0');
                $this->db->insert('geopos_project_meta', $data);
            }
            $this->db->trans_complete();
        } else {
            $this->db->trans_rollback();
        }
        if ($transok) {
            $this->db->from('univarsal_api');
            $this->db->where('univarsal_api.id', 56);
            $query = $this->db->get();
            $auto = $query->row_array();
            if ($auto['key1'] == 1) {
                $this->db->select('name,email');
                $this->db->from('geopos_customers');
                $this->db->where('id', $customer_id);
                $query = $this->db->get();
                $customer = $query->row_array();
                $this->load->model('communication_model');
                $invoice_mail = $this->send_invoice_auto($invocieno, $invocieno2, $bill_date, $total, $currency);
                $attachmenttrue = false;
                $attachment = '';
                $this->communication_model->send_corn_email($customer['email'], $customer['name'], $invoice_mail['subject'], $invoice_mail['message'], $attachmenttrue, $attachment);
            }
            if ($auto['key2'] == 1) {
                $this->db->select('name,phone');
                $this->db->from('geopos_customers');
                $this->db->where('id', $customer_id);
                $query = $this->db->get();
                $customer = $query->row_array();
                $this->load->model('plugins_model', 'plugins');

                $invoice_sms = $this->send_sms_auto($invocieno, $invocieno2, $bill_date, $total, $currency);
                $mobile = $customer['phone'];
                $text_message = $invoice_sms['message'];
                $this->load->model('sms_model', 'sms');
                $this->sms->send_sms($mobile, $text_message, false);
            }

            //profit calculation
            $t_profit = 0;
            $this->db->select('geopos_invoice_items.pid, geopos_invoice_items.price, geopos_invoice_items.qty, geopos_products.fproduct_price');
            $this->db->from('geopos_invoice_items');
            $this->db->join('geopos_products', 'geopos_products.pid = geopos_invoice_items.pid', 'left');
            $this->db->where('geopos_invoice_items.tid', $invocieno);
            $query = $this->db->get();
            $pids = $query->result_array();
            foreach ($pids as $profit) {
                $t_cost = $profit['fproduct_price'] * $profit['qty'];
                $s_cost = $profit['price'] * $profit['qty'];
                $t_profit += $s_cost - $t_cost;
            }
            $data = array('type' => 9, 'rid' => $invocieno, 'col1' => $t_profit, 'd_date' => $bill_date);

            $this->db->insert('geopos_metadata', $data);

            $this->custom->save_fields_data($invocieno, 2);

        }

    }


	/* public function convertaction()
    {
        $currency = $this->input->post('mcurrency');
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('invocieno');
        $invoicedate = $this->input->post('invoicedate');
        $invocieduedate = $this->input->post('invocieduedate');
        $customerphone = $this->input->post('customerphone');
        $challan_number = $this->input->post('challan_number');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $ship_taxtype = $this->input->post('ship_taxtype');
        $disc_val = numberClean($this->input->post('disc_val'));
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer', true);
        $chalan = $this->input->post('chalan', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
        $project = $this->input->post('prjid');
        $total_tax = 0;
        $total_discount = rev_amountExchange_s($this->input->post('after_disc'), $currency, $this->aauth->get_user()->loc);
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms', true);
        $i = 0;
        if ($discountFormat == '0') {
            $discstatus = 0;
        } else {
            $discstatus = 1;
        }
        if ($customer_id == 0) {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('Please add a new client')));
            exit;
        }

        $this->load->model('plugins_model', 'plugins');
        $empl_e = $this->plugins->universal_api(69);
        if ($empl_e['key1']) {
           
            $emp = $this->aauth->get_user()->id;
        } else {
            $emp = $this->aauth->get_user()->id;
        }
		
	
		

        $transok = true;
        $st_c = 0;
        $this->load->library("Common");
        $this->db->trans_start();
        //Invoice Data
        $bill_date = datefordatabase($invoicedate);
        $bill_due_date = datefordatabase($invocieduedate);

        $this->db->select('id');
        $this->db->from('orders');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $this->db->where('id', $invocieno);
        $this->db->where('i_class', 0);
        $query = $this->db->get();
        if(@$query->row()->id){
            $this->db->select('id');
            $this->db->from('orders');
            $this->db->order_by('id', 'DESC');
            $this->db->limit(1);
            $this->db->where('i_class', 0);
            $query = $this->db->get();
            $invocieno=$query->row()->id+1;
        }

        $data = array('id' => $invocieno, 'date_added' => date('Y-m-d H:i:s'), 'mobile' =>$customerphone, 'invoiceduedate' => $bill_due_date, 'final_total' => $subtotal, 'total_payable' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount_rate' => $disc_val, 'total' => $total, 'notes' => $notes, 'user_id' => $customer_id, 'created_by' => $emp, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms,  'loc' => $this->aauth->get_user()->loc, 'orderdone_by' => 'Manual','status' => 'due', 'chalanno' =>$chalan);
        $invocieno2 = $invocieno;
	
        if ($this->db->insert('orders', $data)) {
            $invocieno = $this->db->insert_id();
            //products
            $pid = $this->input->post('pid');
            $productlist = array();
            $prodindex = 0;
            $itc = 0;
            $quoteItemid = $this->input->post('quoteItemid');
            $product_id = $this->input->post('pid');
            $product_name1 = $this->input->post('product_name', true);
            $product_qty = $this->input->post('product_qty');
            $product_price = $this->input->post('product_price');
            $product_article = $this->input->post('product_article');
            $product_tax = $this->input->post('product_tax');
            $product_discount = $this->input->post('product_discount');
            $product_subtotal = $this->input->post('product_subtotal');
            $ptotal_tax = $this->input->post('taxa');
            $ptotal_disc = $this->input->post('disca');
            $product_des = $this->input->post('product_description', true);
            $product_unit = $this->input->post('product_unit');
            $product_hsn = $this->input->post('hsn', true);
            $product_alert = $this->input->post('alert');
            $product_serial = $this->input->post('serial');
            foreach ($pid as $key => $value) {
				
				
			$product_variant = $this->db->select('tax.percentage as tax_percentage, tax.title as tax_name, p.seller_id, p.name as product_name, p.is_prices_inclusive_tax')
    ->join('categories c', 'p.category_id = c.id', 'left')
    ->join('taxes tax', 'tax.id = p.tax', 'left')
    ->where_in('p.id', $product_id[$key])
    ->get('products p')
    ->row_array();



 $converted = convert_to_base_unit($product_unit[$key]);


        $prqty = $converted['qty'] * $product_qty[$key];
		
		$baseunit = $converted['unit'];
$seller_id = isset($product_variant['seller_id']) ? $product_variant['seller_id'] : 0;

			 
			 
                $total_discount += numberClean(@$ptotal_disc[$key]);
                $total_tax += numberClean($ptotal_tax[$key]);
                $data = array(
                    'order_id' => $invocieno,
                    'product_id' => $product_id[$key],
                    'seller_id' => '0',
                    'product_name' => $product_name1[$key],
                    'product_variant_id' => $product_id[$key],
                    'product_article' => $product_article[$key],
                    'code' => $product_hsn[$key],
                    'quantity' => numberClean($product_qty[$key]),
                    'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'discounted_price' => numberClean($product_discount[$key]),
                    'sub_total' => rev_amountExchange_s($product_qty[$key] * $product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'tax_amount' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                    'discount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                    'date_added' => date('Y-m-d H:i:s'),
					'status' =>  json_encode(array(array('received', date("d-m-Y h:i:sa")))),
                    'variant_name' => $product_unit[$key],
					'active_status' => 'received',
                );

                $productlist[$prodindex] = $data;
                $i++;
                $prodindex++;
                $amt = numberClean($product_qty[$key]);
           
				
				 $oldunit = get_stock_by_product_id($product_id[$key]);
			$stock = $oldunit - $prqty;
			
						$legerdata= array(
				
			
				'order_id' => $invocieno,
				'product_id' => $product_id[$key],
				'product_variants' => $product_id[$key],
				'product_name' =>$product_name1[$key],
				'seller_id' => '0', 
				'customer_id' =>  $customer_id,
				'sell_qty' => numberClean($prqty),
				'purchage_qty' => '',
				'sell_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
				'purchage_amount' => '0.00',
				'open_stock' => $oldunit,
				'close_stock' => $stock,
				'created_date' => date('Y-m-d H:i:s'),
				'created_by' => $this->aauth->get_user()->id,
				'purchage_rate' => '0.00',
				'unit' => $baseunit,
				'sell_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
				'ledger_type'=> 'Sell'
				
				
				);
				
				$this->db->insert('product_ledger', $legerdata);
				
				
				
				$this->update_received_and_dispatch($challan_number, $quoteItemid[$key], $product_qty[$key]);
                $itc += $amt;
			$this->get_productstock($product_id[$key], $prqty);
            }
        
            if ($prodindex > 0) {
            $this->db->insert_batch('order_items', $productlist);
				
	//	echo $this->db->last_query();
				
				
                $this->db->set(array('discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc), 'tax_amount' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc), 'items' => $itc));
                $this->db->where('id', $invocieno);
                $this->db->update('orders');
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    "Please choose product from product list. Go to Item manager section if you have not added the products."));
                $transok = false;
            }
            $tnote = '#' . $invocieno . '-' ;
          $d_trans = $this->plugins->universal_api(69);
        if ($d_trans['key2']) {
            $t_data = array(
            'type' => 'Income',
            'cat' => 'Sales',
            'payerid' => $customer_id,
            'method' => 'Auto',
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
            $t_data['credit'] = 0;
           $t_data['debit'] = $total;
           $t_data['type'] = 'Expense';
            $t_data['acid'] = $dual['key2'];
            $t_data['account'] = $account_d['holder'];
            $t_data['note'] = 'Debit ' . $tnote;

            $this->db->insert('geopos_transactions', $t_data);
            //account update
            $this->db->set('lastbal', "lastbal-$total", FALSE);
            $this->db->where('id', $dual['key2']);
            $this->db->update('geopos_accounts');

        }
            if ($transok) {
				
				   $this->db->set('status', 'accepted');
                $this->db->set('convert_to_sell', $invocieno);
                $this->db->where('id', $challan_number);
                $this->db->update('geopos_quotes');
				
				
                $validtoken = hash_hmac('ripemd160', $invocieno, $this->config->item('encryption_key'));
                $link = base_url('billing/view?id=' . $invocieno . '&token=' . $validtoken);
                echo json_encode(array('status' => 'Success', 'message' =>
                    " Challan to invoice converted <a href='".base_url()."invoices/view?id=$invocieno' class='btn btn-primary btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> " . $this->lang->line('View') . "  </a> &nbsp; &nbsp;<a href='".base_url()."invoices/printinvoice?id=$invocieno' class='btn btn-blue btn-lg' target='_blank'><span class='fa fa-print' aria-hidden='true'></span> " . $this->lang->line('Print') . "  </a> &nbsp; &nbsp; <a href='$link' class='btn btn-purple btn-lg'><span class='fa fa-globe' aria-hidden='true'></span> " . $this->lang->line('Public View') . " </a> &nbsp; &nbsp; <a href='create' class='btn btn-warning btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span></a>"));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                "Invalid Entry!"));
            $transok = false;
        }
        if ($transok) {
            if ($this->aauth->premission(4) and $project > 0) {
                $data = array('pid' => $project, 'meta_key' => 11, 'meta_data' => $invocieno, 'value' => '0');
                $this->db->insert('geopos_project_meta', $data);
            }
            $this->db->trans_complete();
        } else {
            $this->db->trans_rollback();
        }
        if ($transok) {
            $this->db->from('univarsal_api');
            $this->db->where('univarsal_api.id', 56);
            $query = $this->db->get();
            $auto = $query->row_array();
            if ($auto['key1'] == 1) {
                $this->db->select('name,email');
                $this->db->from('geopos_customers');
                $this->db->where('id', $customer_id);
                $query = $this->db->get();
                $customer = $query->row_array();
                $this->load->model('communication_model');
                $invoice_mail = $this->send_invoice_auto($invocieno, $invocieno2, $bill_date, $total, $currency);
                $attachmenttrue = false;
                $attachment = '';
                $this->communication_model->send_corn_email($customer['email'], $customer['name'], $invoice_mail['subject'], $invoice_mail['message'], $attachmenttrue, $attachment);
            }
            if ($auto['key2'] == 1) {
                $this->db->select('name,phone');
                $this->db->from('geopos_customers');
                $this->db->where('id', $customer_id);
                $query = $this->db->get();
                $customer = $query->row_array();
                $this->load->model('plugins_model', 'plugins');

                $invoice_sms = $this->send_sms_auto($invocieno, $invocieno2, $bill_date, $total, $currency);
                $mobile = $customer['phone'];
                $text_message = $invoice_sms['message'];
                $this->load->model('sms_model', 'sms');
                $this->sms->send_sms($mobile, $text_message, false);
            }

            //profit calculation
            $t_profit = 0;
            $this->db->select('geopos_invoice_items.pid, geopos_invoice_items.price, geopos_invoice_items.qty, geopos_products.fproduct_price');
            $this->db->from('geopos_invoice_items');
            $this->db->join('geopos_products', 'geopos_products.pid = geopos_invoice_items.pid', 'left');
            $this->db->where('geopos_invoice_items.tid', $invocieno);
            $query = $this->db->get();
            $pids = $query->result_array();
            foreach ($pids as $profit) {
                $t_cost = $profit['fproduct_price'] * $profit['qty'];
                $s_cost = $profit['price'] * $profit['qty'];
                $t_profit += $s_cost - $t_cost;
            }
            $data = array('type' => 9, 'rid' => $invocieno, 'col1' => $t_profit, 'd_date' => $bill_date);

            $this->db->insert('geopos_metadata', $data);

            $this->custom->save_fields_data($invocieno, 2);

        }

    } */


public function convertaction()
{
    $currency = $this->input->post('mcurrency');
    $customer_id = $this->input->post('customer_id');
    $invocieno = $this->input->post('invocieno');
    $invoicedate = $this->input->post('invoicedate');
    $invocieduedate = $this->input->post('invocieduedate');
    $customerphone = $this->input->post('customerphone');
    $challan_number = $this->input->post('challan_number'); // challan number
    $notes = $this->input->post('notes', true);
    $tax = $this->input->post('tax_handle');
    $ship_taxtype = $this->input->post('ship_taxtype');
    $disc_val = numberClean($this->input->post('disc_val'));
    $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
    $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
    $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
    if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
    $refer = $this->input->post('refer', true);
    $chalan = $this->input->post('chalan', true);
    $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
    $project = $this->input->post('prjid');
    $total_tax = 0;
    $total_discount = rev_amountExchange_s($this->input->post('after_disc'), $currency, $this->aauth->get_user()->loc);
    $discountFormat = $this->input->post('discountFormat');
    $pterms = $this->input->post('pterms', true);
    $i = 0;
    $discstatus = ($discountFormat == '0') ? 0 : 1;

    if ($customer_id == 0) {
        echo json_encode(array('status' => 'Error', 'message' =>
            $this->lang->line('Please add a new client')));
        exit;
    }

    $this->load->model('plugins_model', 'plugins');
    $empl_e = $this->plugins->universal_api(69);
    $emp = $this->aauth->get_user()->id;

    $transok = true;
    $this->load->library("Common");
    $this->db->trans_start();

    $bill_date = datefordatabase($invoicedate);
    $bill_due_date = datefordatabase($invocieduedate);

    // 🔴 Check if challan already converted
    $this->db->select('id');
    $this->db->from('orders');
    $this->db->where('chalanno', $challan_number);
    $checkChallan = $this->db->get();
    if ($checkChallan->num_rows() > 0) {
        echo json_encode(array('status' => 'Error', 'message' =>
            "This Challan ($challan_number) is already converted to Invoice."));
        $this->db->trans_rollback();
        return;
    }

    // Invoice number handling
    $this->db->select('id');
    $this->db->from('orders');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $this->db->where('id', $invocieno);
    $this->db->where('i_class', 0);
    $query = $this->db->get();
    if (@$query->row()->id) {
        $this->db->select('id');
        $this->db->from('orders');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $this->db->where('i_class', 0);
        $query = $this->db->get();
        $invocieno = $query->row()->id + 1;
    }

    $data = array(
        'id' => $invocieno,
        'date_added' => date('Y-m-d H:i:s'),
        'mobile' => $customerphone,
        'invoiceduedate' => $bill_due_date,
        'final_total' => $subtotal,
        'total_payable' => $subtotal,
        'shipping' => $shipping,
        'ship_tax' => $shipping_tax,
        'ship_tax_type' => $ship_taxtype,
        'discount_rate' => $disc_val,
        'total' => $total,
        'notes' => $notes,
        'user_id' => $customer_id,
        'created_by' => $emp,
        'taxstatus' => $tax,
        'discstatus' => $discstatus,
        'format_discount' => $discountFormat,
        'refer' => $refer,
        'term' => $pterms,
        'loc' => $this->aauth->get_user()->loc,
        'orderdone_by' => 'Manual',
        'status' => 'due',
        'chalanno' => $challan_number // challan save
    );
    $invocieno2 = $invocieno;

    if ($this->db->insert('orders', $data)) {
        $invocieno = $this->db->insert_id();

        // -------------------------------
        // Products insert (order_items)
        // -------------------------------
        $pid = $this->input->post('pid');
        $productlist = array();
        $prodindex = 0;
        $itc = 0;
        $quoteItemid = $this->input->post('quoteItemid');
        $product_id = $this->input->post('pid');
        $product_name1 = $this->input->post('product_name', true);
        $product_qty = $this->input->post('product_qty');
        $product_price = $this->input->post('product_price');
        $product_article = $this->input->post('product_article');
        $product_tax = $this->input->post('product_tax');
        $product_discount = $this->input->post('product_discount');
        $product_subtotal = $this->input->post('product_subtotal');
        $ptotal_tax = $this->input->post('taxa');
        $ptotal_disc = $this->input->post('disca');
        $product_des = $this->input->post('product_description', true);
        $product_unit = $this->input->post('product_unit');
        $product_hsn = $this->input->post('hsn', true);
        $product_alert = $this->input->post('alert');
        $product_serial = $this->input->post('serial');

        foreach ($pid as $key => $value) {
            $product_variant = $this->db->select('tax.percentage as tax_percentage, tax.title as tax_name, p.seller_id, p.name as product_name, p.is_prices_inclusive_tax')
                ->join('categories c', 'p.category_id = c.id', 'left')
                ->join('taxes tax', 'tax.id = p.tax', 'left')
                ->where_in('p.id', $product_id[$key])
                ->get('products p')
                ->row_array();

            $converted = convert_to_base_unit($product_unit[$key]);
            $prqty = $converted['qty'] * $product_qty[$key];
            $baseunit = $converted['unit'];
            $seller_id = isset($product_variant['seller_id']) ? $product_variant['seller_id'] : 0;

            $total_discount += numberClean(@$ptotal_disc[$key]);
            $total_tax += numberClean($ptotal_tax[$key]);

            $data = array(
                'order_id' => $invocieno,
                'product_id' => $product_id[$key],
                'seller_id' => $seller_id,
                'product_name' => $product_name1[$key],
                'product_variant_id' => $product_id[$key],
                'product_article' => $product_article[$key],
                'code' => $product_hsn[$key],
                'quantity' => numberClean($product_qty[$key]),
                'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'discounted_price' => numberClean($product_discount[$key]),
                'sub_total' => rev_amountExchange_s($product_qty[$key] * $product_price[$key], $currency, $this->aauth->get_user()->loc),
                'tax_amount' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                'discount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                'date_added' => date('Y-m-d H:i:s'),
                'status' => json_encode(array(array('received', date("d-m-Y h:i:sa")))),
                'variant_name' => $product_unit[$key],
                'active_status' => 'received',
            );

            $productlist[$prodindex] = $data;
            $i++;
            $prodindex++;
            $amt = numberClean($product_qty[$key]);

            $oldunit = get_stock_by_product_id($product_id[$key]);
            $stock = $oldunit - $prqty;

            $legerdata = array(
                'order_id' => $invocieno,
                'product_id' => $product_id[$key],
                'product_variants' => $product_id[$key],
                'product_name' => $product_name1[$key],
                'seller_id' => $seller_id,
                'customer_id' => $customer_id,
                'sell_qty' => numberClean($prqty),
                'purchage_qty' => '',
                'sell_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                'purchage_amount' => '0.00',
                'open_stock' => $oldunit,
                'close_stock' => $stock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => '0.00',
                'unit' => $baseunit,
                'sell_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'ledger_type' => 'Sell'
            );
            $this->db->insert('product_ledger', $legerdata);

            $this->update_received_and_dispatch($challan_number, $quoteItemid[$key], $product_qty[$key]);
            $itc += $amt;
            $this->get_productstock($product_id[$key], $prqty);
        }

        if ($prodindex > 0) {
            $this->db->insert_batch('order_items', $productlist);

            $this->db->set(array(
                'discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc),
                'tax_amount' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc),
                'items' => $itc
            ));
            $this->db->where('id', $invocieno);
            $this->db->update('orders');
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                "Please choose product from product list. Go to Item manager section if you have not added the products."));
            $transok = false;
        }

        // ✅ challan update in quotes
        $this->db->set('status', 'accepted');
        $this->db->set('convert_to_sell', $invocieno);
        $this->db->where('id', $challan_number);
        $this->db->update('geopos_quotes');

        $validtoken = hash_hmac('ripemd160', $invocieno, $this->config->item('encryption_key'));
        $link = base_url('billing/view?id=' . $invocieno . '&token=' . $validtoken);
        echo json_encode(array('status' => 'Success', 'message' =>
            "Challan to invoice converted <a href='" . base_url() . "invoices/view?id=$invocieno' class='btn btn-primary btn-lg'><span class='fa fa-eye'></span> View </a> &nbsp;<a href='" . base_url() . "invoices/printinvoice?id=$invocieno' class='btn btn-blue btn-lg' target='_blank'><span class='fa fa-print'></span> Print </a> &nbsp;<a href='$link' class='btn btn-purple btn-lg'><span class='fa fa-globe'></span> Public View </a>"));
    } else {
        echo json_encode(array('status' => 'Error', 'message' =>
            "Invalid Entry!"));
        $transok = false;
    }

    if ($transok) {
        $this->db->trans_complete();
    } else {
        $this->db->trans_rollback();
    }
}




 public function update_received_and_dispatch($tid, $id, $received_qty) {
        // Get existing row
        $this->db->where('tid', $tid);
        $this->db->where('id', $id);
        $row = $this->db->get('geopos_quotes_items')->row();

        if ($row) {
            $qty = (float)$row->qty;

            // Calculate received percentage
            $received_per = ($qty > 0) ? ($received_qty / $qty) * 100 : 0;

            // Dispatch quantity and percentage
            $dispatch_qty = $qty; // Agar hamesha full dispatch hai
            $dispatch_per = ($qty > 0) ? ($dispatch_qty / $qty) * 100 : 0;

            // Update record
            $data = [
                'received_qty' => $received_qty,
                'received_per' => round($received_per, 2),
                'dispatch_qty' => $dispatch_qty,
                'dispatch_per' => round($dispatch_per, 2)
            ];

            $this->db->where('tid', $tid);
            $this->db->where('id', $id);
            return $this->db->update('geopos_quotes_items', $data);
        }

        return false; // Row not found
    }
	

public function get_productstock($product_id, $quantity_to_deduct){
	
	
	$this->db->trans_start();



// Get current stock
$this->db->select('stock');
$this->db->where('id', $product_id);
$query = $this->db->get('products');
$product = $query->row();

if ($product) {
    $current_stock = $product->stock;

   // if ($current_stock >= $quantity_to_deduct) {
        $new_stock = $current_stock - $quantity_to_deduct;
        $this->db->where('id', $product_id);
        $this->db->update('products', ['stock' => $new_stock]);
   /*  } else {
        echo "Insufficient stock!";
        $this->db->trans_complete();
        return;
    } */
} else {
    echo "Product not found!";
    $this->db->trans_complete();
    return;
}

$this->db->trans_complete();

return $product->stock;




}

    public function ajax_list()
    {
        sync_online_order_stock_ledger();
        $list = $this->invocies->get_datatables($this->limited);
        $data = array();
        $no = $this->input->post('start');
	
        foreach ($list as $invoices) {
			
			
			
            $no++;
			
			if($invoices->orderdone_by=='POS'){
				
				$acturl ='pos_invoices';
			}else{
				 
				$acturl ='invoices';
			}
			
			$challanid = (!empty($invoices->chalanno)) ? $this->invocies->get_id_by_tid($invoices->chalanno) : false;
			
			$chlan = ($invoices->chalanno != '')? ' | <a href="' . base_url("chalan/view?id={$challanid}") . '" target="_blank">' . $invoices->chalanno . '</a>': '';

			$totalpurchase = $this->invocies->invoice_purchase($invoices->id);
			$postag = ($invoices->orderdone_by=='POS') ? ' <span class="badge badge-info">POS</span>' : '';
			$tidprefix = ($invoices->orderdone_by=='POS') ? 'POS' : 'THKD';
            $row = array();
            $row[] = $no;

            $row[] = '<a href="' . base_url($acturl."/view?id=$invoices->id") . '">'.$tidprefix.'#' . $invoices->tid . '</a>'.$postag.$chlan;
            $row[] = $invoices->name;
            $row[] = date("d-m-Y h:i A", strtotime($invoices->date_added));
            $row[] = $invoices->total;
			if($this->aauth->get_user()->roleid==1){
            $row[] = $totalpurchase;
            $row[] = round($invoices->total-$totalpurchase, 2);
           // $row[] =  sprintf("%.2f",(($invoices->total - $totalpurchase) / $totalpurchase) * 100) .' %';
			$row[] = sprintf("%.2f", ($totalpurchase > 0 ? (($invoices->total - $totalpurchase) / $totalpurchase) * 100 : 0)) . ' %';
			}
			if ($invoices->created_by == 0) {
				$row[] = 'Online';
			} else if (!empty($invoices->seller_store)) {
				$row[] = $invoices->seller_store;
			} else {
				$row[] = !empty($invoices->username) ? $invoices->username : 'Admin';
			}
			if ($invoices->orderdone_by == 'POS') {
				$pamnt = (float) $invoices->pamnt;
				$total = (float) $invoices->total;
				if ($pamnt <= 0) {
					$display_status = 'due';
				} elseif ($pamnt >= $total) {
					$display_status = 'paid';
				} else {
					$display_status = 'partial';
				}
			} else {
				$display_status = $invoices->status;
			}
            $row[] = '<span class="st-' . $display_status . '">' . $this->lang->line(ucwords($display_status)).': '.dateformat($invoices->invoiceduedate) . '</span>';
            $row[] = '<a href="' . base_url($acturl."/view?id=$invoices->id") . '" class="btn btn-success btn-sm" title="View"><i class="fa fa-eye"></i></a>&nbsp;<a href="' . base_url($acturl."/printinvoice?id=$invoices->id") . '&d=1" class="btn btn-info btn-sm"  title="Download"><span class="fa fa-download"></span></a> <a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-sm delete-object"><span class="fa fa-trash"></span></a>';
            $data[] = $row;
        }
		$totalsale = $this->invocies->get_totalsalse();
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->invocies->count_all($this->limited),
            "recordsFiltered" => $this->invocies->count_filtered($this->limited),
            "totalsale" => round((float) ($totalsale['total'] ?? 0), 2),
            "data" => $data,
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    } 


	public function ajaxtrash_list()
    {
        $list = $this->invocies->get_trashdatatables($this->limited);
        $data = array();
        $no = $this->input->post('start');
	
        foreach ($list as $invoices) {
			
			
			
            $no++;
			
			if($invoices->orderdone_by=='POS'){
				
				$acturl ='pos_invoices';
			}else{
				
				$acturl ='invoices';
			}
            $row = array();
            $row[] = $no;

            $row[] = '<a href="' . base_url($acturl."/view?id=$invoices->id") . '">THKD # &nbsp; ' . $invoices->tid . '</a>';
            $row[] = $invoices->name;
            $row[] = dateformat($invoices->invoicedate);
            $row[] = $invoices->total;
            $row[] = '<span class="st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';
            $row[] = ' <a href="#" onclick="restoreinvoice('.$invoices->id.')" class="btn btn-danger btn-sm restore-object">Restore</a>';
            $data[] = $row;
        }
		//$totalsale = $this->invocies->get_totalsalse();
		$totalsale = 0;
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->invocies->count_alltrash($this->limited),
            "recordsFiltered" => $this->invocies->count_filteredtrash($this->limited),
            "totalsale" => round((float) $totalsale, 2),
            "data" => $data,
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
	
public function restoreinvoice()
{
    $id = $this->input->post('id');

    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    $data = [
        'is_deleted' => 0,
        'deleted_by' => NULL
    ];

    $this->db->where('id', $id);
    $update = $this->db->update('orders', $data);

    if ($update) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update order']);
    }
}


    public function view()
    {
        $tid = $this->input->get('id');
        $header_loaded = false;
        try {
            $this->load->model('accounts_model');
            $data['acclist'] = $this->accounts_model->accountslist((integer)$this->aauth->get_user()->loc);
            $data['invoice'] = $this->invocies->invoice_details($tid, $this->limited);
            $data['attach'] = $this->invocies->attach($tid);
            $data['c_custom_fields'] = $this->custom->view_fields_data($data['invoice']['cid'] ?? 0, 1);
            $head['usernm'] = $this->aauth->get_user()->username;
            $head['title'] = "Invoice " . ($data['invoice']['tid'] ?? $tid);
            $this->load->view('fixed/header', $head);
            $header_loaded = true;
            $data['products'] = $this->invocies->invoice_products($tid);


            if (!empty($data['invoice']['id'])) $data['activity'] = $this->invocies->invoice_transactions($tid);
            $data['employee'] = $this->invocies->employee($data['invoice']['eid'] ?? 0);
            $data['custom_fields'] = $this->custom->view_fields_data($tid, 2);
            if (!empty($data['invoice']['id'])) {
                $data['invoice']['id'] = $tid;
                $this->load->view('invoices/view', $data);
            } else {
                $this->output->append_output('<div class="content-body"><div class="alert alert-warning m-2">Invoice not found.</div></div>');
            }
            $this->load->view('fixed/footer');
        } catch (\Throwable $e) {
            // Never surface a raw 500 for this page: log the real cause and show a friendly
            // message, with the full error visible to the master admin (id 1) on-screen so it
            // can be diagnosed without needing server log/DB access. Note: $this->load->view()
            // only buffers into CI's output object (flushed at the very end of the request), so
            // we must use append_output() here too rather than echo, or this would render before
            // the header instead of after it.
            error_log('Invoices::view failed for id=' . $tid . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $user = $this->aauth->get_user();
            if (!$header_loaded) {
                $this->load->view('fixed/header', array('usernm' => $user ? $user->username : '', 'title' => 'Invoice'));
            }
            if ($user && (int)$user->id === 1) {
                $message = '<strong>Error loading invoice #' . htmlspecialchars($tid) . ':</strong> ' . htmlspecialchars($e->getMessage()) . ' (' . htmlspecialchars(basename($e->getFile())) . ':' . $e->getLine() . ')';
            } else {
                $message = 'This invoice could not be displayed right now. Please try again later.';
            }
            $this->output->append_output('<div class="content-body"><div class="alert alert-danger m-2">' . $message . '</div></div>');
            $this->load->view('fixed/footer');
        }
    }

    public function printinvoice()
    {

        $tid = $this->input->get('id');
        $data['id'] = $tid;
        $data['invoice'] = $this->invocies->invoice_details($tid, $this->limited);
        if ($data['invoice']['id']) $data['products'] = $this->invocies->invoice_products($tid);
        if ($data['invoice']['id']) $data['employee'] = $this->invocies->employee($data['invoice']['eid']);
        if ($data['invoice']['i_class'] == 1) {
            $pref = prefix(7);
        } else {
            $pref = $this->config->item('prefix');
        }
        if (CUSTOM) $data['c_custom_fields'] = $this->custom->view_fields_data($data['invoice']['cid'], 1, 1);
        $data['general'] = array('title' => $this->lang->line('Invoice'), 'person' => $this->lang->line('Customer'), 'prefix' => $pref, 't_type' => 0);
        ini_set('memory_limit', '64M');
        if ($data['invoice']['taxstatus'] == 'cgst' || $data['invoice']['taxstatus'] == 'igst') {
            $html = $this->load->view('print_files/invoice-a4-gst_v' . INVV, $data, true);
        } else {
            $html = $this->load->view('print_files/invoice-a4_v' . INVV, $data, true);
        }
        //PDF Rendering
        $this->load->library('pdf');
        if (INVV == 1) {
            $header = $this->load->view('print_files/invoice-header_v' . INVV, $data, true);
            $pdf = $this->pdf->load_split(array('margin_top' => 40));
            $pdf->SetHTMLHeader($header);
        }
        if (INVV == 2) {
            $pdf = $this->pdf->load_split(array('margin_top' => 5));
        }
        $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $data['invoice']['tid'] . '</div>');
        $pdf->WriteHTML($html);
        $file_name = preg_replace('/[^A-Za-z0-9]+/', '-', 'Invoice__' . $data['invoice']['name'] . '_' . $data['invoice']['tid']);
        if ($this->input->get('d')) {
            $pdf->Output($file_name . '.pdf', 'D');
        } else {
            $pdf->Output($file_name . '.pdf', 'I');
        }
    }

    public function delete_i()
    {
        if ($this->aauth->premission(11)) {
            $id = $this->input->post('deleteid');

            if ($this->invocies->invoice_delete($id, $this->limited, $this->aauth->get_user()->id)) {
				
					
		  $previous_items = $this->db->where('order_id', $id)->get('order_items')->result();
		  
	

    foreach ($previous_items as $item) {
		
	
		if($item->product_id==0){
		
		 $openstock = get_stock_by_product_name($item->product_name);
			$nstock = $openstock + $item->quantity;
		}else{
			 $openstock = get_stock_by_product_id($item->product_id);
			$nstock = $openstock + $item->quantity;
				
		}
			
        $this->db->set('stock', 'stock+' . $item->quantity, FALSE)
                 ->where('id', $item->product_id)
                 ->update('products');

        $ledger_data = [
		
		 'order_id' => $id,
                'product_id' => $item->product_id,
                'product_variants' => $item->product_id,
                'product_name' => $item->product_name,
                'seller_id' => '0',
                'customer_id' => $item->user_id,
                'sell_qty' => '0',
                'purchage_qty' => $item->quantity,
                'sell_amount' => '0.00',
                'purchage_amount' => '0.00',
                'open_stock' => $openstock,
                'close_stock' => $nstock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => '0.00',
                'unit' => $item->variant_name,
                'sell_rate' => '0.00',
                'ledger_type' => 'Invoice Deleted'
				
            
        ];
        $this->db->insert('product_ledger', $ledger_data);
		
		
    }
                echo json_encode(array('status' => 'Success', 'message' =>
                    $this->lang->line('DELETED')));
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

   /*  public function editaction()
    {
       
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('invocieno');
        $iid = $this->input->post('id');
        $invoicedate = $this->input->post('invoicedate');
        $invocieduedate = $this->input->post('invocieduedate');
        $mobile = $this->input->post('mobile');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $ship_taxtype = $this->input->post('ship_taxtype');
        $total_tax = 0;
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms');
        $currency = $this->input->post('mcurrency');
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
        $disc_val = numberClean($this->input->post('disc_val'));
        $total_discount = rev_amountExchange_s($this->input->post('after_disc'), $currency, $this->aauth->get_user()->loc);
        $i = 0;
    
        if ($discountFormat == '0') {
            $discstatus = 0;
        } else {
            $discstatus = 1;
        }

        if ($customer_id == 0) {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('Please add a new client')));
            exit;
        }
        $this->db->trans_start();
        $transok = true;
          $st_c = 0;
           $this->load->library("Common");

        $bill_date = datefordatabase($invoicedate);
        $bill_due_date = datefordatabase($invocieduedate);
        $data = array('date_added' => $bill_date, 'mobile' => $mobile, 'invoiceduedate' => $bill_due_date, 'final_total' => $subtotal, 'total_payable' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount_rate' => $disc_val, 'total' => $total, 'notes' => $notes, 'user_id' => $customer_id, 'created_by' =>$this->aauth->get_user()->id, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms,  'loc' => $this->aauth->get_user()->loc, 'orderdone_by' => 'Manual','status' => 'due');
        $this->db->set($data);
        $this->db->where('id', $iid);


        if ($this->db->update('orders', $data)) {
            //Product Data
            $pid = $this->input->post('pid');
            $productlist = array();
            $prodindex = 0;
            $itc = 0;
            $this->db->delete('order_items', array('order_id' => $iid));
            $product_id = $this->input->post('pid');
            $product_name1 = $this->input->post('product_name', true);
            $product_qty = $this->input->post('product_qty');
            $old_product_qty = $this->input->post('old_product_qty');
            $product_price = $this->input->post('product_price');
            $product_tax = $this->input->post('product_tax');
            $product_discount = $this->input->post('product_discount');
            $product_subtotal = $this->input->post('product_subtotal');
            $ptotal_tax = $this->input->post('taxa');
            $ptotal_disc = $this->input->post('disca');
            $product_des = 'NA';
            $product_unit = $this->input->post('product_unit');
            $product_hsn = $this->input->post('hsn');
            $product_serial = $this->input->post('serial');
            $product_alert = $this->input->post('alert');

            foreach ($pid as $key => $value) {

$product_variant = $this->db->select('tax.percentage as tax_percentage, tax.title as tax_name, p.seller_id, p.name as product_name, p.is_prices_inclusive_tax')
    ->join('categories c', 'p.category_id = c.id', 'left')
    ->join('taxes tax', 'tax.id = p.tax', 'left')
    ->where_in('p.id', $product_id[$key])
    ->get('products p')
    ->row_array();

$seller_id = isset($product_variant['seller_id']) ? $product_variant['seller_id'] : null;

                $total_discount += numberClean(@$ptotal_disc[$key]);
                $total_tax += numberClean($ptotal_tax[$key]);

                 $data = array(
                    'order_id' => $iid,
                    'product_id' => $product_id[$key],
                    'seller_id' => $seller_id,
                    'user_id' => $customer_id,
                    'product_name' => $product_name1[$key],
                    'product_variant_id' => $product_id[$key],
                    'code' => $product_hsn[$key],
                    'quantity' => numberClean($product_qty[$key]),
                    'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'discounted_price' => numberClean($product_discount[$key]),
                    'sub_total' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                    'tax_amount' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                    'discount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                    'date_added' => date('Y-m-d H:i:s'),
					'status' =>  json_encode(array(array('received', date("d-m-Y h:i:sa")))),
                    'variant_name' => $product_unit[$key],
					'active_status' => 'received',
                );
                $productlist[$prodindex] = $data;
                $i++;
                $prodindex++;

                $amt = numberClean(@$product_qty[$key]) - numberClean(@$old_product_qty[$key]);
                if ($product_id[$key] > 0 and $amt) {
                    $this->db->set('stock', "stock-$amt", FALSE);
                    $this->db->where('id', $product_id[$key]);
                    $this->db->update('products');

                
                }
                $itc += $amt;
            }

            if ($prodindex > 0) {
                $this->db->insert_batch('order_items', $productlist);
				
			
         if($transok)

			 echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Invoice has  been updated') . " <a href='view?id=$iid' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> " . $this->lang->line('View') . " </a> "));
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
                $transok = false;
            }

            if ($this->input->post('restock')) {
                foreach ($this->input->post('restock') as $key => $value) {
                    $myArray = explode('-', $value);
                    $prid = $myArray[0];
                    $dqty = numberClean($myArray[1]);
                    if ($prid > 0) {
                        $this->db->set('stock', "stock+$dqty", FALSE);
                        $this->db->where('id', $prid);
                        $this->db->update('products');
                    }
                }
            }
        } else {
                if($transok)   echo json_encode(array('status' => 'Error', 'message' =>
                "Please add at least one product in invoice"));
            $transok = false;

        }


    
        $this->db->trans_complete();
    } */


 public function editaction()
    {
       
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('invocieno');
        $iid = $this->input->post('id');
        $invoicedate = $this->input->post('invoicedate');
        $invocieduedate = $this->input->post('invocieduedate');
        $mobile = $this->input->post('mobile');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $ship_taxtype = $this->input->post('ship_taxtype');
        $total_tax = 0;
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms');
        $currency = $this->input->post('mcurrency');
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
        $disc_val = numberClean($this->input->post('disc_val'));
        $total_discount = rev_amountExchange_s($this->input->post('after_disc'), $currency, $this->aauth->get_user()->loc);
		
		
		
        $i = 0;
    
        if ($discountFormat == '0') {
            $discstatus = 0;
        } else {
            $discstatus = 1;
        }

        if ($customer_id == 0) {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('Please add a new client')));
            exit;
        }
        $this->db->trans_start();
	
	
	   $previous_items = $this->db->where('order_id', $iid)->get('order_items')->result_array();
    
    // Create an associative array of previous items for easy lookup
    $prev_item_map = [];
    foreach ($previous_items as $item) {
        $prev_item_map[$item['product_id']] = $item;
    }
    $pid = $this->input->post('pid');
    $product_qty = $this->input->post('product_qty'); 
    $old_product_qty = $this->input->post('old_product_qty'); // Old quantity from form

    foreach ($previous_items as $item) {
        $product_id = $item['product_id'];
        if (isset($prev_item_map[$product_id])) {
			
			 $converted = convert_to_base_unit($item['variant_name']);


        $prqty = $converted['qty'] * $item['quantity'];
		
		$baseunit = $converted['unit'];
		
		
		/* $converted = convert_to_base_unit($product_unit[$key]);
            $prqty = $converted['qty'] * $product_qty[$key]; */
		
            $old_qty = $item['quantity'];
            $new_qty = isset($product_qty[array_search($product_id, $pid)]) ? $product_qty[array_search($product_id, $pid)] : 0;



            if ($old_qty != $new_qty) {
				
                $openstock = get_stock_by_product_id($product_id);
                $nstock = $openstock + $prqty;
                $this->db->set('stock', 'stock+' . $prqty, FALSE)
                         ->where('id', $product_id)
                         ->update('products');

                // Add restock entry to ledger
                $ledger_data = [
                    'order_id' => $iid,
                    'product_id' => $product_id,
                    'product_variants' => $product_id,
                    'product_name' => $item['product_name'],
                    'seller_id' => '0',
                    'customer_id' => $item['user_id'],
                    'sell_qty' => '0',
                    'purchage_qty' => $old_qty,
                    'sell_amount' => '0.00',
                    'purchage_amount' => '0.00',
                    'open_stock' => $openstock,
                    'close_stock' => $nstock,
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $this->aauth->get_user()->id,
                    'purchage_rate' => '0.00',
                    'unit' => $baseunit,
                    'sell_rate' => '0.00',
                    'ledger_type' => 'Restock'
                ];
                $this->db->insert('product_ledger', $ledger_data);
            }
        }
    }
        $transok = true;
          $st_c = 0;
           $this->load->library("Common");

        $bill_date = datefordatabase($invoicedate);
        $bill_due_date = datefordatabase($invocieduedate);
        $data = array( 'mobile' => $mobile, 'date_added' => $bill_date, 'updated_at' => date('Y-m-d H:i:s'), 'invoiceduedate' => $bill_due_date, 'final_total' => $subtotal, 'total_payable' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount_rate' => $disc_val, 'total' => $total, 'notes' => $notes, 'user_id' => $customer_id,  'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms,  'loc' => $this->aauth->get_user()->loc, 'orderdone_by' => 'Manual','status' => 'due');
        $this->db->set($data);
        $this->db->where('id', $iid);


        if ($this->db->update('orders', $data)) {
            //Product Data
            $pid = $this->input->post('pid');
            $productlist = array();
            $prodindex = 0;
            $itc = 0;
            $this->db->delete('order_items', array('order_id' => $iid));
            $product_id = $this->input->post('pid');
            $product_name1 = $this->input->post('product_name', true);
            $product_qty = $this->input->post('product_qty');
            $old_product_qty = $this->input->post('old_product_qty');
            $product_price = $this->input->post('product_price');
            $product_tax = $this->input->post('product_tax');
            $product_discount = $this->input->post('product_discount');
            $product_subtotal = $this->input->post('product_subtotal');
           // $product_subtotal =  ($product_qty[$key] * $product_price[$key]);
            $ptotal_tax = $this->input->post('taxa');
            $ptotal_disc = $this->input->post('disca');
            $product_des = 'NA';
            $product_unit = $this->input->post('product_unit');
            $product_hsn = $this->input->post('hsn');
            $product_serial = $this->input->post('serial');
            $product_alert = $this->input->post('alert');

            foreach ($pid as $key => $value) {

$product_variant = $this->db->select('tax.percentage as tax_percentage, tax.title as tax_name, p.seller_id, p.name as product_name, p.is_prices_inclusive_tax')
    ->join('categories c', 'p.category_id = c.id', 'left')
    ->join('taxes tax', 'tax.id = p.tax', 'left')
    ->where_in('p.id', $product_id[$key])
    ->get('products p')
    ->row_array();

$seller_id = isset($product_variant['seller_id']) ? $product_variant['seller_id'] : null;

                $total_discount += numberClean(@$ptotal_disc[$key]);
                $total_tax += numberClean($ptotal_tax[$key]);
				
		
			$new_qty = $product_qty[$key];
			 $oldunit = get_stock_by_product_id($product_id[$key]);
			 $stock = $oldunit - $product_qty[$key];
			
	
                 $data = array(
                    'order_id' => $iid,
                    'product_id' => $product_id[$key],
                    'seller_id' => $seller_id,
                    'user_id' => $customer_id,
                    'product_name' => $product_name1[$key],
                    'product_variant_id' => $product_id[$key],
                    'code' => $product_hsn[$key],
                    'quantity' => numberClean($product_qty[$key]),
                    'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'discounted_price' => numberClean($product_discount[$key]),
                    'sub_total' => rev_amountExchange_s($product_qty[$key] * $product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'tax_amount' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                    'discount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                    'date_added' => date('Y-m-d H:i:s'),
					'status' =>  json_encode(array(array('received', date("d-m-Y h:i:sa")))),
                    'variant_name' => $product_unit[$key],
					'active_status' => 'received',
                );
                $productlist[$prodindex] = $data;
                $i++;
                $prodindex++;

if($old_product_qty[$key] !== $new_qty){
      $nledger_data = [
                'order_id' => $iid,
                'product_id' => $value,
                'product_variants' => $value,
                'product_name' => $product_name1[$key],
                'seller_id' => '0',
                'customer_id' => $customer_id,
                'sell_qty' => $prqty,
                'purchage_qty' => '',
                'sell_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                'purchage_amount' => '0.00',
                'open_stock' => $oldunit,
                'close_stock' => $stock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => '0.00',
                'unit' => $baseunit,
                'sell_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'ledger_type' => 'Sell'
            ];
            $this->db->insert('product_ledger', $nledger_data);
}
                $amt = numberClean(@$product_qty[$key]) - numberClean(@$old_product_qty[$key]);
                if ($product_id[$key] > 0 and $amt) {
                   // $this->db->set('stock', "stock-$amt", FALSE);
                    $this->db->set('stock', $stock, FALSE);
                    $this->db->where('id', $product_id[$key]);
                    $this->db->update('products');

                
                }
                $itc += $amt;
            }

            if ($prodindex > 0) {
                $this->db->insert_batch('order_items', $productlist);
				
			
         if($transok)

			 echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Invoice has  been updated') . " <a href='view?id=$iid' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> " . $this->lang->line('View') . " </a> "));
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
                $transok = false;
            }

        } else {
                if($transok)   echo json_encode(array('status' => 'Error', 'message' =>
                "Please add at least one product in invoice"));
            $transok = false;

        }


    
        $this->db->trans_complete();
    } 

public function editactionold()
{
    $customer_id = $this->input->post('customer_id');
    $invocieno = $this->input->post('invocieno');
    $iid = $this->input->post('id');
    $invoicedate = $this->input->post('invoicedate');
    $invocieduedate = $this->input->post('invocieduedate');
    $mobile = $this->input->post('mobile');
    $notes = $this->input->post('notes', true);
    $tax = $this->input->post('tax_handle');
    $ship_taxtype = $this->input->post('ship_taxtype');
    $currency = $this->input->post('mcurrency');

    $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
    $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
    $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);

    if ($ship_taxtype == 'incl') {
        $shipping = $shipping - $shipping_tax;
    }

    $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
    $disc_val = numberClean($this->input->post('disc_val'));
    $total_discount = rev_amountExchange_s($this->input->post('after_disc'), $currency, $this->aauth->get_user()->loc);
    $pterms = $this->input->post('pterms');
    $refer = $this->input->post('refer', true);


     $bill_date = datefordatabase($invoicedate);
    $bill_due_date = datefordatabase($invocieduedate);

    if ($customer_id == 0) {
        echo json_encode(['status' => 'Error', 'message' => 'Please add a new client']);
        exit;
    }

    $this->db->trans_start();

    // Restore stock for previous order items
    $previous_items = $this->db->where('order_id', $iid)->get('order_items')->result();
    foreach ($previous_items as $item) {
		
		
		
		 $converted_value = convert_to_base_unit($item->variant_name);


        $prqty = $converted_value * $item->quantity;

		if($item->product_id==0){
		
		 $openstock = get_stock_by_product_name($item->product_name);
			$nstock = $openstock + $prqty;
		}else{
			 $openstock = get_stock_by_product_id($item->product_id);
			$nstock = $openstock + $prqty;
			
		}
			
        $this->db->set('stock', 'stock+' . $prqty, FALSE)
                 ->where('id', $item->product_id)
                 ->update('products');
        // Add ledger entry for stock return
        $ledger_data = [
		
		 'order_id' => $iid,
                'product_id' => $item->product_id,
                'product_variants' => $item->product_id,
                'product_name' => $item->product_name,
                'seller_id' => '0',
                'customer_id' => $item->user_id,
                'sell_qty' => '0',
                'purchage_qty' => $prqty,
                'sell_amount' => rev_amountExchange_s($item->sub_total, $currency, $this->aauth->get_user()->loc),
                'purchage_amount' => '0.00',
                'open_stock' => $openstock,
                'close_stock' => $nstock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => '0.00',
                'unit' => $item->variant_name,
                'sell_rate' => '0.00',
                'ledger_type' => 'Restock'
				
            
        ];
        $this->db->insert('product_ledger', $ledger_data);
		
		
    }

    // Delete previous order items
    $this->db->delete('order_items', ['order_id' => $iid]);

    // Update order details
    $data = [
        'date_added' => $bill_date,
        'mobile' => $mobile,
        'invoiceduedate' => $bill_due_date,
        'final_total' => $subtotal,
        'total_payable' => $subtotal,
        'shipping' => $shipping,
        'ship_tax' => $shipping_tax,
        'ship_tax_type' => $ship_taxtype,
        'discount_rate' => $disc_val,
        'total' => $total,
        'notes' => $notes,
        'user_id' => $customer_id,
        'created_by' => $this->aauth->get_user()->id,
        'taxstatus' => $tax,
        'refer' => $refer,
        'term' => $pterms,
        'loc' => $this->aauth->get_user()->loc,
        'orderdone_by' => 'Manual',
        'status' => 'due'
    ];
    $this->db->where('id', $iid)->update('orders', $data);

    // Insert new order items
    $product_id = $this->input->post('pid');
    $product_name1 = $this->input->post('product_name', true);
    $product_article = $this->input->post('product_article', true);
    $product_qty = $this->input->post('product_qty');
    $product_price = $this->input->post('product_price');
    $product_subtotal = $this->input->post('product_subtotal');
    $product_unit = $this->input->post('product_unit');

    foreach ($product_id as $key => $value) {
        if ($value > 0) {
            // Get current stock before update
            $product = $this->db->where('id', $value)->get('products')->row();
            $oldunit = $product->stock;
            $new_qty = numberClean($product_qty[$key]);
            $stock = $oldunit - $new_qty; // New stock after update

            $data = [
                'order_id' => $iid,
                'product_id' => $value,
                'user_id' => $customer_id,
                'product_name' => $product_name1[$key],
                'product_article' => $product_article[$key],
                'quantity' => $new_qty,
                'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'sub_total' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                'date_added' => date('Y-m-d H:i:s'),
                'variant_name' => $product_unit[$key],
                'active_status' => 'received'
            ];
        $this->db->insert('order_items', $data);

            // Update stock
            $this->db->set('stock', 'stock-' . $new_qty, FALSE)
                     ->where('id', $value)
                     ->update('products');

            // Insert ledger entry
            $nledger_data = [
                'order_id' => $iid,
                'product_id' => $value,
                'product_variants' => $value,
                'product_name' => $product_name1[$key],
                'seller_id' => '0',
                'customer_id' => $customer_id,
                'sell_qty' => $new_qty,
                'purchage_qty' => '',
                'sell_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                'purchage_amount' => '0.00',
                'open_stock' => $oldunit,
                'close_stock' => $stock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => '0.00',
                'unit' => $product_unit[$key],
                'sell_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'ledger_type' => 'Sell'
            ];
            $this->db->insert('product_ledger', $nledger_data);
        }
    }

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(['status' => 'Error', 'message' => 'ERROR']);
    } else {
        $this->db->trans_commit();
        echo json_encode(['status' => 'Success', 'message' => "Invoice has been updated <a href='view?id=$iid' class='btn btn-info btn-lg'><span class='fa fa-eye'></span> View</a>"]);
    }
}



    public function update_status()
    {
        $tid = $this->input->post('tid');
        $status = $this->input->post('status');
        $this->db->set('status', $status);
        $this->db->where('id', $tid);
        $this->db->update('orders');

        echo json_encode(array('status' => 'Success', 'message' =>
            $this->lang->line('UPDATED'), 'pstatus' => $status));
    }


    public function addcustomer()
    {
        $name = $this->input->post('name', true);
        $company = $this->input->post('company', true);
        $phone = $this->input->post('phone', true);
        $email = $this->input->post('email', true);
        $address = $this->input->post('address', true);
        $city = $this->input->post('city', true);
        $region = $this->input->post('region', true);
        $country = $this->input->post('country', true);
        $postbox = $this->input->post('postbox', true);
        $taxid = $this->input->post('taxid', true);
        $customergroup = $this->input->post('customergroup');
        $name_s = $this->input->post('name_s', true);
        $phone_s = $this->input->post('phone_s', true);
        $email_s = $this->input->post('email_s', true);
        $address_s = $this->input->post('address_s', true);
        $city_s = $this->input->post('city_s', true);
        $region_s = $this->input->post('region_s', true);
        $country_s = $this->input->post('country_s', true);
        $postbox_s = $this->input->post('postbox_s', true);

        $this->load->model('customers_model', 'customers');
        $this->customers->add($name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $customergroup, $taxid, $name_s, $phone_s, $email_s, $address_s, $city_s, $region_s, $country_s, $postbox_s);

    }

    public function file_handling()
    {
        if ($this->input->get('op')) {
            $name = $this->input->get('name');
            $invoice = $this->input->get('invoice');
            if ($this->invocies->meta_delete($invoice, 1, $name)) {
                echo json_encode(array('status' => 'Success'));
            }
        } else {
            $id = $this->input->get('id');
            $this->load->library("Uploadhandler_generic", array(
                'accept_file_types' => '/\.(gif|jpe?g|png|docx|docs|txt|pdf|xls)$/i', 'upload_dir' => FCPATH . 'userfiles/attach/', 'upload_url' => base_url() . 'userfiles/attach/'
            ));
            $files = (string)$this->uploadhandler_generic->filenaam();
            if ($files != '') {

                $this->invocies->meta_insert($id, 1, $files);
            }
        }


    }

    public function delivery()
    {

        $tid = $this->input->get('id');

        $data['id'] = $tid;
        $data['title'] = "Invoice $tid";
        $data['invoice'] = $this->invocies->invoice_details($tid, $this->limited);
        if ($data['invoice']['id']) $data['products'] = $this->invocies->invoice_products($tid);
        if ($data['invoice']['id']) $data['employee'] = $this->invocies->employee($data['invoice']['eid']);

        ini_set('memory_limit', '64M');

        $html = $this->load->view('invoices/del_note', $data, true);

        //PDF Rendering
        $this->load->library('pdf');

        $pdf = $this->pdf->load();

        $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $tid . '</div>');

        $pdf->WriteHTML($html);

        if ($this->input->get('d')) {

            $pdf->Output('DO_#' . $data['invoice']['tid'] . '.pdf', 'D');
        } else {
            $pdf->Output('DO_#' . $data['invoice']['tid'] . '.pdf', 'I');
        }


    }

    public function proforma()
    {

        $tid = $this->input->get('id');

        $data['id'] = $tid;
        $data['title'] = "Invoice $tid";
        $data['invoice'] = $this->invocies->invoice_details($tid, $this->limited);
        if ($data['invoice']['id']) $data['products'] = $this->invocies->invoice_products($tid);
        if ($data['invoice']['id']) $data['employee'] = $this->invocies->employee($data['invoice']['eid']);
        ini_set('memory_limit', '64M');
        $html = $this->load->view('invoices/proforma', $data, true);
        //PDF Rendering
        $this->load->library('pdf');
        $pdf = $this->pdf->load();
        $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $tid . '</div>');
        $pdf->WriteHTML($html);
        if ($this->input->get('d')) {
            $pdf->Output('Proforma_#' . $data['invoice']['tid'] . '.pdf', 'D');
        } else {
            $pdf->Output('Proforma_#' . $data['invoice']['tid'] . '.pdf', 'I');
        }


    }


    public function send_invoice_auto($invocieno, $invocieno2, $idate, $total, $multi)
    {
        $this->load->library('parser');
        $this->load->model('templates_model', 'templates');
        $template = $this->templates->template_info(6);

        $data = array(
            'Company' => $this->config->item('ctitle'),
            'BillNumber' => $invocieno2
        );
        $subject = $this->parser->parse_string($template['key1'], $data, TRUE);
        $validtoken = hash_hmac('ripemd160', $invocieno, $this->config->item('encryption_key'));
        $link = base_url('billing/view?id=' . $invocieno . '&token=' . $validtoken);


        $data = array(
            'Company' => $this->config->item('ctitle'),
            'BillNumber' => $invocieno2,
            'URL' => "<a href='$link'>$link</a>",
            'CompanyDetails' => '<h6><strong>' . $this->config->item('ctitle') . ',</strong></h6>
<address>' . $this->config->item('address') . '<br>' . $this->config->item('address2') . '</address>
             ' . $this->lang->line('Phone') . ' : ' . $this->config->item('phone') . '<br>  ' . $this->lang->line('Email') . ' : ' . $this->config->item('email'),
            'DueDate' => dateformat($idate),
            'Amount' => amountExchange($total, $multi)
        );
        $message = $this->parser->parse_string($template['other'], $data, TRUE);
        return array('subject' => $subject, 'message' => $message);
    }

    public function send_sms_auto($invocieno, $invocieno2, $idate, $total, $multi)
    {
        $this->load->library('parser');
        $this->load->model('templates_model', 'templates');
        $template = $this->templates->template_info(30);
        $validtoken = hash_hmac('ripemd160', $invocieno, $this->config->item('encryption_key'));
        $link = base_url('billing/view?id=' . $invocieno . '&token=' . $validtoken);
        $this->load->model('plugins_model', 'plugins');
        $sms_service = $this->plugins->universal_api(1);
        if ($sms_service['active']) {
            $this->load->library("Shortenurl");
            $this->shortenurl->setkey($sms_service['key1']);
            $link = $this->shortenurl->shorten($link);
        }
        $data = array(
            'BillNumber' => $invocieno2,
            'URL' => $link,
            'DueDate' => dateformat($idate),
            'Amount' => amountExchange($total, $multi)
        );
        $message = $this->parser->parse_string($template['other'], $data, TRUE);
        return array('message' => $message);
    }

    public function view_payslip()
    {
        $id = $this->input->get('id');
        $inv = $this->input->get('inv');
        $data['invoice'] = $this->invocies->invoice_details($inv, $this->limited);
        if (!$data['invoice']['id']) exit('Limited Permissions!');

        $this->load->model('transactions_model', 'transactions');
        $head['title'] = "View Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;

        $data['trans'] = $this->transactions->view($id);

        if ($data['trans']['payerid'] > 0) {
            $data['cdata'] = $this->transactions->cview($data['trans']['payerid'], $data['trans']['ext']);
        } else {
            $data['cdata'] = array('address' => 'Not Registered', 'city' => '', 'phone' => '', 'email' => '');
        }
        ini_set('memory_limit', '64M');

        $html = $this->load->view('transactions/view-print-customer', $data, true);

        //PDF Rendering
        $this->load->library('pdf');

        $pdf = $this->pdf->load_en();

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;"><tr><td width="33%"></td><td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td><td width="33%" style="text-align: right; ">#' . $id . '</td></tr></table>');

        $pdf->WriteHTML($html);

        if ($this->input->get('d')) {

            $pdf->Output('Trans_#' . $id . '.pdf', 'D');
        } else {
            $pdf->Output('Trans_#' . $id . '.pdf', 'I');
        }


    }
	
	
	public function get_variation_articles() {
  $product_id = $this->input->post('product_id');
  $articles = $this->invocies->get_variation_articles_by_product($product_id);
  echo json_encode($articles);
}


}
