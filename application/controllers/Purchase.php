<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchase_model', 'purchase');
        $this->load->model('invoices_model', 'invocies');
      $this->load->model('transactions_model', 'transactions');
        
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        if (!$this->aauth->premission(2)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->li_a = 'stock';
        //exit('Under Dev Mode');


    }

    //create invoice
    public function create()
    {
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist($this->config->item('tax'));
        $data['unitlist'] = $this->common->unitlist();
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $data['currency'] = $this->purchase->currencies();
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
           $data['sellers'] = $this->db->select(' u.username as seller_name,u.id as seller_id, u.mobile, sd.category_ids,sd.id as seller_data_id  ')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->join('seller_data sd', ' sd.user_id = u.id ')
            ->where(['ug.group_id' => '4'])->where(['u.active' => '1'])
            ->get('users u')->result_array();
		
        $data['lastinvoice'] = $this->purchase->lastpurchase();
        $data['terms'] = $this->purchase->billingterms();
        $head['title'] = "New Purchase";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->purchase->warehouses();
        $data['taxdetails'] = $this->common->taxdetail();
		 $data['accounts'] = $this->transactions->acc_list();
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase/newinvoice', $data);
        $this->load->view('fixed/footer');
    } 

	public function challancreate($quotation_id = null)
    {
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist($this->config->item('tax'));
        $data['unitlist'] = $this->common->unitlist();
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $data['currency'] = $this->purchase->currencies();
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
           $data['sellers'] = $this->db->select(' u.username as seller_name,u.id as seller_id, u.mobile, sd.category_ids,sd.id as seller_data_id  ')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->join('seller_data sd', ' sd.user_id = u.id ')
            ->where(['ug.group_id' => '4'])->where(['u.active' => '1'])
            ->get('users u')->result_array();
		
        $data['lastinvoice'] = $this->purchase->lastpurchase();
        $data['terms'] = $this->purchase->billingterms();
		$data['quotation_items'] = [];

/* if ($quotation_id) {
    $data['quotation_items'] = $this->db->select('item_name, quantity, unit, fill_rate, description, amount')
        ->from('seller_quotation_items')
        ->where(['order_id' => $quotation_id, 'adm_status' => 1])
        ->get()
        ->result_array();
} */

if ($quotation_id) {
    // Fetch quotation
    $quotation = $this->db->get_where('seller_quotation', ['id' => $quotation_id])->row_array();
    $data['quotation'] = $quotation;

    // Fetch quotation items
    $data['quotation_items'] = $this->db->select('item_id, item_name, quantity, unit, fill_rate, description, amount')
        ->from('seller_quotation_items')
        ->where(['order_id' => $quotation_id, 'adm_status' => 1])
        ->get()
        ->result_array();

    // Fetch supplier details
    if (!empty($quotation['supplier_id'])) {
        $data['supplier_details'] = $this->db->get_where('geopos_supplier', ['id' => $quotation['supplier_id']])->row_array();
    } else {
        $data['supplier_details'] = [];
    }
}
		
        $head['title'] = "New Purchase Challan";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->purchase->warehouses();
        $data['taxdetails'] = $this->common->taxdetail();
		 $data['accounts'] = $this->transactions->acc_list();
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase/challancreate', $data);
        $this->load->view('fixed/footer');
    }



public function challan(){
	
	  $head['title'] = "Manage Purchase Challan";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
		 $data['supplierlist'] = $this->purchase->supplierlist();
        $this->load->view('purchase/challan', $data);
        $this->load->view('fixed/footer');
	
}


public function received(){
	
		 $this->load->model('quote_model', 'quote');
	  $head['title'] = "Manage Purchase received";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
		// $data['supplierlist'] = $this->purchase->supplierlist();
		  $data['list'] = $this->quote->get_all();
        $this->load->view('purchase/received', $data);
        $this->load->view('fixed/footer');
	
}


public function createreceiveditems(){
	
		 $this->load->model('quote_model', 'quote');
	  $head['title'] = "Manage Purchase received";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
		// $data['supplierlist'] = $this->purchase->supplierlist();
		 // $data['list'] = $this->quote->get_all();
        $this->load->view('purchase/createreceiveditems', $data);
        $this->load->view('fixed/footer');
	
}


public function receiveditemsave()
{
    $this->load->database();
    $this->load->helper('security');

    $employee_name = $this->input->post('employee_ids', true);
    $employee_name = $this->input->post('employee_name', true);
    $order_no      = $this->input->post('invocieno', true);
    $reference     = $this->input->post('refer', true);
    $order_date    = datefordatabase($this->input->post('invoicedate', true));
    $due_date      = datefordatabase($this->input->post('invocieduedate', true));

    $this->db->trans_start();

    // Master insert
    $master_data = [
        'employee_name' => $employee_name,
        'employee_name' => $employee_name,
        'order_no'      => $order_no,
        'reference'     => $reference,
        'order_date'    => $order_date,
        'due_date'      => $due_date,
    ];
    $this->db->insert('received_master', $master_data);
    $master_id = $this->db->insert_id();

    // Items insert
    $product_names   = $this->input->post('product_name');
    $required_qtys   = $this->input->post('required_qty');
    $units           = $this->input->post('product_unit');
    $received_qtys   = $this->input->post('received_qty');
    $balance_qtys    = $this->input->post('balance_qty');
    $supplier_ids    = $this->input->post('supplier_id');
    $supplier_names  = $this->input->post('supplier');
    $prices          = $this->input->post('product_price');
    $descriptions    = $this->input->post('product_description');
    $amounts         = $this->input->post('product_subtotal');

    if (!empty($product_names)) {
        foreach ($product_names as $key => $name) {
            if (trim($name) == '') continue;

            $item_data = [
                'master_id'     => $master_id,
                'product_name'  => $name,
                'required_qty'  => $required_qtys[$key],
                'unit'          => $units[$key],
                'received_qty'  => $received_qtys[$key],
                'balance_qty'   => $balance_qtys[$key],
                'supplier_id'   => $supplier_ids[$key],
                'supplier_name' => $supplier_names[$key],
                'price'         => $prices[$key],
                'description'   => $descriptions[$key],
                'amount'        => $amounts[$key],
            ];
            $this->db->insert('received_items', $item_data);
        }
    }

    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
        echo json_encode(['status' => 'Error', 'message' => 'Failed to save received items!']);
    } else {
        echo json_encode(['status' => 'Success', 'message' => 'Received item save successfully!', 'pstatus' => true]);
    }
}


public function update_adm_status() {
    $items = $this->input->post('items');

    if (empty($items) || !is_array($items)) {
        echo json_encode(['status' => 'error', 'message' => 'No items received']);
        return;
    }

    $this->load->database();

    // 🔹 Step 1: Update status + collect suppliers
    $suppliers = []; // supplier_id => [ 'phone' => xxx, 'references' => [] ]
 
    foreach ($items as $item) {
        $item_id      = intval($item['item_id']);
        $order_id     = intval($item['order_id']);
        $supplier_id  = intval($item['supplier_id']);
        $phone        = preg_replace('/\D/', '', $item['phone']); // only digits
        $suppliername = $item['suppliername'];
        $reference_no = $item['reference_no'];
		

        // Update item
        $this->db->where('order_id', $order_id);
        $this->db->where('id', $item_id);
        $this->db->update('seller_quotation_items', ['adm_status' => 1]);

        // Update order
        $this->db->where('id', $order_id);
        $this->db->update('seller_quotation', ['adm_status' => 1]);

        // Group by supplier
        if (!isset($suppliers[$supplier_id])) {
            $suppliers[$supplier_id] = [
                'phone'      => $phone,
                'name'       => $suppliername,
                'references' => $reference_no
            ];
        }
       // $suppliers[$supplier_id]['references'][] = $order_id;
    }

    foreach ($suppliers as $supplier) {
        $phone      = "91" . ltrim($supplier['phone'], '0'); 
        $sphone      = $supplier['phone']; 
        $references = $reference_no; // e.g. 2378_2382
        $url        = base_url("supplierqotation/challan/{$sphone}/{$references}");

        $payload = [
            "apiKey"    => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY",
            "campaignName" => "supplier purchase challan confirm",
            "destination"  => $phone,
            "userName"     => "Thok ki dukaan",
            "templateParams" => [
                $supplier['name'],
                $url
            ],
            "source"    => "new-landing-page form",
            "media"     => new \stdClass(),
            "buttons"   => [],
            "carouselCards" => [],
            "location"  => new \stdClass(),
            "attributes"=> new \stdClass(),
            "paramsFallbackValue" => ["FirstName" => $supplier['name']]
        ];

        // Send to AiSensy API
        $ch = curl_init("https://backend.aisensy.com/campaign/t1/api/v2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        // Log or debug response if needed
        // log_message('info', "WhatsApp sent to {$phone}: " . $response);
    }

    echo json_encode(['status' => 'success', 'message' => 'Status updated & WhatsApp sent']);
}

 



/* public function update_adm_status() {
    $items = $this->input->post('items');

    if (empty($items) || !is_array($items)) {
        echo json_encode(['status' => 'error', 'message' => 'No items received']);
        return;
    }

    $this->load->database();

    $suppliers = [];

    foreach ($items as $item) {
        $item_id      = intval($item['item_id']);
        $order_id     = intval($item['order_id']);
        $supplier_id  = intval($item['supplier_id']);
        $phone        = preg_replace('/\D/', '', $item['phone']);
        $suppliername = $item['suppliername'];
        $reference_no = $item['reference_no'];

        $this->db->where('order_id', $order_id);
        $this->db->where('id', $item_id);
        $this->db->update('seller_quotation_items', ['adm_status' => 1]);

        $this->db->where('id', $order_id);
        $this->db->update('seller_quotation', ['adm_status' => 1]);

        if (!isset($suppliers[$supplier_id])) {
            $suppliers[$supplier_id] = [
                'phone'      => $phone,
                'name'       => $suppliername,
                'references' => $reference_no,
                'order_id'   => $order_id   // 👈 Added for employee URL
            ];
        }
    }

    foreach ($suppliers as $supplier) {
        $phone      = "91" . ltrim($supplier['phone'], '0'); 
        $sphone     = $supplier['phone']; 
        $references = $supplier['references']; 
        $order_id   = $supplier['order_id']; 
        $url        = base_url("supplierqotation/challan/{$sphone}/{$references}");
        $emplurl    = base_url("indent/receiveditemedit/{$order_id}");

        // 🔹 Supplier WhatsApp
        $payload = [
            "apiKey"    => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY",
            "campaignName" => "supplier purchase challan confirm",
            "destination"  => $phone,
            "userName"     => "Thok ki dukaan",
            "templateParams" => [
                $supplier['name'],
                $url
            ],
            "source"    => "new-landing-page form",
            "media"     => new \stdClass(),
            "buttons"   => [],
            "carouselCards" => [],
            "location"  => new \stdClass(),
            "attributes"=> new \stdClass(),
            "paramsFallbackValue" => ["FirstName" => $supplier['name']]
        ];

        $ch = curl_init("https://backend.aisensy.com/campaign/t1/api/v2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        // 🔹 Abhishek Rana WhatsApp (fixed number)
      
    }

    echo json_encode(['status' => 'success', 'message' => 'Status updated & WhatsApp sent']);
} */


    //edit invoice
    public function edit()
    {

        $tid = $this->input->get('id');
        $data['id'] = $tid;
        $data['title'] = "Purchase Order $tid";
        $this->load->model('customers_model', 'customers');
        $data['customergrouplist'] = $this->customers->group_list();
        $data['terms'] = $this->purchase->billingterms();
        $data['invoice'] = $this->purchase->purchase_details($tid);
        $data['products'] = $this->purchase->purchase_products($tid);;
        $head['title'] = "Edit Invoice #$tid";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->purchase->warehouses();
        $data['currency'] = $this->purchase->currencies();
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist_edit($data['invoice']['taxstatus']);
        $this->load->view('fixed/header', $head);
        $this->load->view('purchase/edit', $data);
        $this->load->view('fixed/footer');

    }

    //invoices list
    public function index()
    {
        $head['title'] = "Manage Purchase Orders";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
		 $data['supplierlist'] = $this->purchase->supplierlist();
        $this->load->view('purchase/invoices', $data);
        $this->load->view('fixed/footer');
    } 


	public function sellerquote()
    {
        $head['title'] = "Manage Purchase Orders";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
		 $data['supplierlist'] = $this->purchase->supplierlist();
        $this->load->view('indent/sellerquote', $data);
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
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
        $ship_taxtype = $this->input->post('ship_taxtype');
        if ($ship_taxtype == 'incl') @$shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);
        $total_tax = 0;
        $total_discount = 0;
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms');
        $i = 0;
        if ($discountFormat == '0') {
            $discstatus = 0;
        } else {
            $discstatus = 1;
        }

        if ($customer_id == 0) {
            echo json_encode(array('status' => 'Error', 'message' =>
                "Please add a new supplier or search from a previous added!"));
            exit;
        }
        $this->db->trans_start();
        //products
        $transok = true;
        //Invoice Data
        $bill_date = datefordatabase($invoicedate);
        $bill_due_date = datefordatabase($invocieduedate);
        $data = array('tid' => $invocieno, 'invoicedate' => $bill_date, 'invoiceduedate' => $bill_due_date, 'subtotal' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'total' => $total, 'notes' => $notes, 'csd' => $customer_id, 'eid' => $this->aauth->get_user()->id, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms, 'loc' => $this->aauth->get_user()->loc, 'multi' => $currency,'created_at' =>date('Y-m-d H:i:s'));


        if ($this->db->insert('geopos_purchase', $data)) {
            $invocieno = $this->db->insert_id();

            $pid = $this->input->post('pid');
            $productlist = array();
            $prodindex = 0;
            $itc = 0;
            $flag = false;
            $product_id = $this->input->post('pid');
            $varid = $this->input->post('varid');
            $margin = $this->input->post('margin');
            $margintype = $this->input->post('margintype');
            $prodicount = $this->input->post('prodisc');
            $product_name1 = $this->input->post('product_name', true);
            $productUnit = $this->input->post('product_unit');
            $producttype = $this->input->post('producttype');
            $product_qty = $this->input->post('product_qty');
            $product_price = $this->input->post('product_price');
            $product_tax = $this->input->post('product_tax');
            $product_discount = $this->input->post('product_discount');
            $product_subtotal = $this->input->post('product_subtotal');
            $ptotal_tax = $this->input->post('taxa');
            $ptotal_disc = $this->input->post('disca');
            $product_des = $this->input->post('product_description', true);
            $product_unit = $this->input->post('unit');
            $product_hsn = $this->input->post('hsn');


            foreach ($pid as $key => $value) {
                $total_discount += numberClean(@$ptotal_disc[$key]);
                $total_tax += numberClean($ptotal_tax[$key]);


                $data = array(
                    'tid' => $invocieno,
                    'pid' => $product_id[$key],
                    'product_variants' => $varid[$key],
                    'product' => $product_name1[$key],
                    'code' => $product_hsn[$key],
                    'qty' => numberClean($product_qty[$key]),
                    'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                    'tax' => numberClean($product_tax[$key]),
                    'discount' => numberClean($product_discount[$key]),
                    'subtotal' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                    'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
                    'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
                    'product_des' => $product_des[$key],
                    'unit' => $productUnit[$key]
                );


				$amt = numberClean($product_qty[$key]);
                $oldunit = get_stock_by_product_id($product_id[$key]);
				$stock =  $oldunit+ $amt;
				
				$legerdata = array(
				
			
				'order_id' => $invocieno,
				'product_id' => $product_id[$key],
				'product_variants' => $varid[$key],
				 'product_name' => $product_name1[$key],
				'seller_id' => $customer_id, 
				'customer_id' => '0',
				'sell_qty' => '0.00',
				'purchage_qty' => numberClean($product_qty[$key]),
				'sell_amount' => '0.00',
				'purchage_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
				'open_stock' => $oldunit,
				'close_stock' => $stock,
				'created_date' => date('Y-m-d H:i:s'),
				'created_by' => $this->aauth->get_user()->id,
				'purchage_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
				'unit' => $productUnit[$key],
				'sell_rate' => '0.00',
				'ledger_type'=> 'purchage'
				
				
				);
				
				$this->db->insert('product_ledger', $legerdata);
                $flag = true;
                $productlist[$prodindex] = $data;
                $i++;
                $prodindex++;
                
                if ($product_id[$key] > 0) {
                  
							if( $margintype[$key]=="Fixed"){
								
								 $marginamount = $margin[$key];
								
							}else{
								
								 $marginamount = ((float)$margin[$key] / 100) * (float)$product_price[$key];
							}
						
						 
						 $discamount = ((float)$prodicount[$key] / 100) * (float)$product_price[$key];
						
						
						$spprice=  ((float)$product_price[$key] + (float)$marginamount) - (float)$discamount;
						$price= (float)$product_price[$key]+(float)$marginamount;
						
						
						
						$data['stock_summary'] = $this->get_product_stock_summary($product_id[$key]);
						
						
						$this->db->set('purchase_price', $product_price[$key]);
						$this->db->set('fproduct_price', $product_price[$key]);
						$this->db->set('product_price', $price);
                        $this->db->set('stock', $stock);
                        $this->db->set('updated_date', date('Y-m-d H:i:s'));
                        $this->db->where('id', $product_id[$key]);
                        $this->db->update('products');
				
				
				
				$this->db->query("SELECT SUM(sell_qty) as 'totalsell', sum(purchage_qty) as 'totalpurchage', SUM(purchage_qty - sell_qty) as 'balanceqty' FROM `product_ledger` WHERE product_id='1752'; ");
					
                    $itc += $amt;
                }

            }


          //  if (isset($res) && !empty($res)) {
              
                $transmydata = array (
                    'transaction_type' => 'transaction',
                    'user_id' => $this->aauth->get_user()->id,
                    'order_id' => $invocieno,
                    'type' => 'Cash',
                    'txn_id' => $refer,
                    'amount' => $total,
                    'status' => "success",
                    'txntype' => "purchase",
                    'message' => "purchase  Successfully",
          );

       //  echo json_encode($transmydata);
             //   $this->transaction_model->add_transaction($trans_data);

             $this->db->insert('transactions', $transmydata);
               // echo $this->db->last_query();
                
          //  }
            
            if ($prodindex > 0) {
              $this->db->insert_batch('geopos_purchase_items', $productlist);
                $this->db->set(array('discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc), 'tax' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc), 'items' => $itc));
                $this->db->where('id', $invocieno);
                $this->db->update('geopos_purchase'); 





            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    "Please choose product from product list. Go to Item manager section if you have not added the products."));
                $transok = false;
            }


            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Purchase order success') . "<a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span>" . $this->lang->line('View') . " </a>"));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
            $transok = false;
        }


        if ($transok) {
            $this->db->trans_complete();
        } else {
            $this->db->trans_rollback();
        }



    }
	
	
	
	
	 public function get_product_stock_summary($product_id) {
        $this->db->select("SUM(sell_qty) as totalsell, SUM(purchage_qty) as totalpurchage, SUM(purchage_qty - sell_qty) as balanceqty");
        $this->db->from("product_ledger");
        $this->db->where("product_id", $product_id);
        $query = $this->db->get();
        return $query->row_array(); // Single row result
    }
	
	


    public function ajax_list()
    {

        $list = $this->purchase->get_datatables();
        $data = array();

        $no = $this->input->post('start');

        foreach ($list as $invoices) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $invoices->tid;
            $row[] = '<a href="'.base_url("supplier/view?id=$invoices->csd").'" target="blank">'.$invoices->name.'</a>';
            $row[] = date('d-m-Y', strtotime($invoices->invoicedate));
            $row[] = dateformat($invoices->invoiceduedate);
            $row[] = round($invoices->total,2);
            $row[] = round($invoices->total-$invoices->pamnt,2);
            $row[] = '<span class="st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';
            $row[] = date('d-m-Y h:i A', strtotime($invoices->created_at));
            $row[] = '<a href="' . base_url("purchase/view?id=$invoices->id") . '" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> ' . $this->lang->line('View') . '</a> &nbsp; <a href="' . base_url("purchase/printinvoice?id=$invoices->id") . '&d=1" class="btn btn-info btn-xs"  title="Download"><span class="fa fa-download"></span></a>&nbsp; &nbsp;<a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-xs delete-object purchase"><span class="fa fa-trash"></span></a>&nbsp; &nbsp;<a href="' . base_url("supplier/bulkpayment?id=$invoices->csd") . '" target="_blank" class="btn btn-blue btn-xs pay-object purchase">Pay now</span></a>';

            $data[] = $row;
        }
		$totalsale = $this->purchase->get_totalpurchase();
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->purchase->count_all(),
            "recordsFiltered" => $this->purchase->count_filtered(),
			"totalsale" => round($totalsale['total'], 2),
			"totalbal" => round($totalsale['balance'], 2),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);

    }

    public function view()
    {
        $this->load->model('accounts_model');
        $data['acclist'] = $this->accounts_model->accountslist((integer)$this->aauth->get_user()->loc);
        $tid = intval($this->input->get('id'));
        $data['id'] = $tid;
        $head['title'] = "Purchase $tid";
        $data['invoice'] = $this->purchase->purchase_details($tid);
        $data['products'] = $this->purchase->purchase_products($tid);
        $data['activity'] = $this->purchase->purchase_transactions($tid);
        $data['attach'] = $this->purchase->attach($tid);
        $data['employee'] = $this->purchase->employee($data['invoice']['eid']);
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        if ($data['invoice']['tid']) $this->load->view('purchase/view', $data);
        $this->load->view('fixed/footer');

    }


    public function printinvoice()
    {

        $tid = $this->input->get('id');

        $data['id'] = $tid;
        $data['title'] = "Purchase $tid";
        $data['invoice'] = $this->purchase->purchase_details($tid);
        $data['products'] = $this->purchase->purchase_products($tid);
        $data['employee'] = $this->purchase->employee($data['invoice']['eid']);
        $data['invoice']['multi'] = 0;

        $data['general'] = array('title' => $this->lang->line('Purchase Order'), 'person' => $this->lang->line('Supplier'), 'prefix' => prefix(2), 't_type' => 0);


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

        if ($this->input->get('d')) {

            $pdf->Output('Purchase_#' . $data['invoice']['tid'] . '.pdf', 'D');
        } else {
            $pdf->Output('Purchase_#' . $data['invoice']['tid'] . '.pdf', 'I');
        }


    }

    public function delete_i()
    {
        $id = $this->input->post('deleteid');

        if ($this->purchase->purchase_delete($id)) {
			
			
			
			 $previous_items = $this->db->where('tid', $id)->get('geopos_purchase_items')->result();
		  
	
    foreach ($previous_items as $item) {
		
	
		if($item->pid==0){
		
		 $openstock = get_stock_by_product_name($item->product);
			$nstock = $openstock + $item->qty;
		}else{
			 $openstock = get_stock_by_product_id($item->pid);
			$nstock = $openstock + $item->qty;
				
		}
			
        $this->db->set('stock', 'stock+' . $item->qty, FALSE)
                 ->where('id', $item->pid)
                 ->update('products');

        $ledger_data = [
		
		 'order_id' => $id,
                'product_id' => $item->pid,
                'product_variants' => $item->pid,
                'product_name' => $item->product,
                'seller_id' => '',
                'customer_id' => '',
                'sell_qty' => $item->qty,
                'purchage_qty' => '0.00',
                'sell_amount' => '0.00',
                'purchage_amount' => '0.00',
                'open_stock' => $openstock,
                'close_stock' => $nstock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => '0.00',
                'unit' => $item->unit,
                'sell_rate' => '0.00',
                'ledger_type' => 'Purchase Order Deleted'
				
            
        ];
        $this->db->insert('product_ledger', $ledger_data);
		
		
    }
	
	
            echo json_encode(array('status' => 'Success', 'message' =>
                "Purchase Order #$id has been deleted successfully!"));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>
                "There is an error! Purchase has not deleted."));
        }

    }

   public function editaction()
{
    $currency = $this->input->post('mcurrency');
    $customer_id = $this->input->post('customer_id');
    $invocieno = $this->input->post('iid');
    $invoicedate = $this->input->post('invoicedate');
    $invocieduedate = $this->input->post('invocieduedate');
    $notes = $this->input->post('notes', true);
    $tax = $this->input->post('tax_handle');
    $refer = $this->input->post('refer', true);
    $pterms = $this->input->post('pterms');
    $ship_taxtype = $this->input->post('ship_taxtype');
    $discountFormat = $this->input->post('discountFormat');

    $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);
    $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);
    $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);
    $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);

    if ($ship_taxtype == 'incl') $shipping -= $shipping_tax;

    $discstatus = ($discountFormat == '0') ? 0 : 1;
    if ($customer_id == 0) {
        echo json_encode(['status' => 'Error', 'message' => "Please add or select a supplier!"]);
        exit();
    }

    $this->db->trans_start();
    $flag = false;
    $transok = true;
    $total_tax = 0;
    $total_discount = 0;
    $itc = 0;

    // Inputs
    $pid = $this->input->post('pid');
    $product_name = $this->input->post('product_name', true);
    $product_qty = $this->input->post('product_qty');
    $old_product_qty = $this->input->post('old_product_qty');
    $product_price = $this->input->post('product_price');
    $product_tax = $this->input->post('product_tax');
    $product_discount = $this->input->post('product_discount');
    $product_subtotal = $this->input->post('product_subtotal');
    $ptotal_tax = $this->input->post('taxa');
    $ptotal_disc = $this->input->post('disca');
    $product_des = $this->input->post('product_description', true);
    $product_unit = $this->input->post('unit');
    $product_hsn = $this->input->post('hsn');
    $productUnit = $this->input->post('product_unit');
    $producttype = $this->input->post('producttype');
    $varid = $this->input->post('varid');
    $margin = $this->input->post('margin');
    $prodicount = $this->input->post('prodisc');

    // Old Items: Restock
    $previous_items = $this->db->where('tid', $invocieno)->get('geopos_purchase_items')->result();
    foreach ($previous_items as $item) {
        $index = array_search($item->pid, $pid);
        if ($index !== false) {
            $new_qty = floatval(numberClean($product_qty[$index]));
            $old_qty = floatval(numberClean($old_product_qty[$index] ?? 0));

            if ($new_qty != $old_qty) {
                $openstock = get_stock_by_product_id($item->pid);
                $nstock = $openstock - $item->qty;

                $this->db->set('stock', $nstock, false)->where('id', $item->pid)->update('products');

                $ledger = [
                    'order_id' => $invocieno,
                    'product_id' => $item->pid,
                    'product_variants' => $item->pid,
                    'product_name' => $item->product,
                    'seller_id' => $customer_id,
                    'customer_id' => '0',
                    'sell_qty' => $item->qty,
                    'purchage_qty' => '',
                    'sell_amount' => '0.00',
                    'purchage_amount' => '0.00',
                    'open_stock' => $openstock,
                    'close_stock' => $nstock,
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $this->aauth->get_user()->id,
                    'purchage_rate' => '0.00',
                    'unit' => $item->unit,
                    'sell_rate' => '0.00',
                    'ledger_type' => 'Restock'
                ];
                $this->db->insert('product_ledger', $ledger);
            }
        }
    }

    // Delete old items
    $this->db->delete('geopos_purchase_items', ['tid' => $invocieno]);

    // New Items Insert
    $productlist = [];
    foreach ($pid as $key => $val) {
        $new_qty = floatval(numberClean($product_qty[$key]));
        $old_qty = floatval(numberClean($old_product_qty[$key] ?? 0));
        $amt = $new_qty;
        $delta_qty = $new_qty - $old_qty;

        $price = floatval($product_price[$key]);
        $margin_amt = (floatval($margin[$key]) / 100) * $price;
        $disc_amt = (floatval($prodicount[$key]) / 100) * $price;
        $spprice = ($price + $margin_amt) - $disc_amt;
        $final_price = $price + $margin_amt;

        $total_tax += numberClean($ptotal_tax[$key]);
        $total_discount += numberClean($ptotal_disc[$key]);

        $productlist[] = [
            'tid' => $invocieno,
            'pid' => $pid[$key],
            'product_variants' => $varid[$key],
            'product' => $product_name[$key],
            'code' => $product_hsn[$key],
            'qty' => $new_qty,
            'price' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
            'tax' => numberClean($product_tax[$key]),
            'discount' => numberClean($product_discount[$key]),
            'subtotal' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
            'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $this->aauth->get_user()->loc),
            'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $this->aauth->get_user()->loc),
            'product_des' => $product_des[$key],
            'unit' => $product_unit[$key]
        ];

        if ($delta_qty != 0) {
            $oldstock = get_stock_by_product_id($pid[$key]);
            $newstock = $oldstock + $new_qty;

            $ledgerdata = [
                'order_id' => $invocieno,
                'product_id' => $pid[$key],
                'product_variants' => $varid[$key],
                'product_name' => $product_name[$key],
                'seller_id' => $customer_id,
                'customer_id' => '0',
                'sell_qty' => '0.00',
                'purchage_qty' => $amt,
                'sell_amount' => '0.00',
                'purchage_amount' => rev_amountExchange_s($product_subtotal[$key], $currency, $this->aauth->get_user()->loc),
                'open_stock' => $oldstock,
                'close_stock' => $newstock,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $this->aauth->get_user()->id,
                'purchage_rate' => rev_amountExchange_s($product_price[$key], $currency, $this->aauth->get_user()->loc),
                'unit' => $productUnit[$key],
                'sell_rate' => '0.00',
                'ledger_type' => 'purchage'
            ];
            $this->db->insert('product_ledger', $ledgerdata);

            $this->db->set('stock', "stock+$new_qty", false)
                     ->set('purchase_price', $product_price[$key])
                     ->where('id', $pid[$key])
                     ->update('products');
        }

        $flag = true;
        $itc += $amt;
    }

    // Purchase Update
    $data = [
        'invoicedate' => datefordatabase($invoicedate),
        'invoiceduedate' => datefordatabase($invocieduedate),
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'ship_tax' => $shipping_tax,
        'ship_tax_type' => $ship_taxtype,
        'discount' => rev_amountExchange_s($total_discount, $currency, $this->aauth->get_user()->loc),
        'tax' => rev_amountExchange_s($total_tax, $currency, $this->aauth->get_user()->loc),
        'total' => $total,
        'notes' => $notes,
        'csd' => $customer_id,
        'items' => $itc,
        'taxstatus' => $tax,
        'discstatus' => $discstatus,
        'format_discount' => $discountFormat,
        'refer' => $refer,
        'term' => $pterms,
        'multi' => $currency,
		'updated_date' => date('Y-m-d H:i:s')
    ];

    $this->db->where('id', $invocieno)->update('geopos_purchase', $data);
    $this->db->insert_batch('geopos_purchase_items', $productlist);

    // Update Variant Pricing if set
    if ($this->input->post('update_stock') == 'yes' && $this->input->post('restock')) {
        foreach ($this->input->post('restock') as $key => $val) {
            $prid = explode('-', $val)[0];
            if ($prid > 0) {
                $price = floatval($product_price[$key]);
                $margin_amt = (floatval($margin[$key]) / 100) * $price;
                $disc_amt = (floatval($prodicount[$key]) / 100) * $price;
                $spprice = ($price + $margin_amt) - $disc_amt;
                $final_price = $price + $margin_amt;

                $this->db->set('purchase_price', $price)
                         ->set('special_price', $spprice)
                         ->set('price', $final_price)
                         ->where('id', $varid[$key])
                         ->update('product_variants');

                $this->db->set('purchase_price', $price)
                         ->set('fproduct_price', $price)
                         ->set('product_price', $final_price)
                         ->set('updated_date', date('Y-m-d H:i:s'))
                         ->where('id', $pid[$key])
                         ->update('products');
            }
        }
    }

    if ($transok) {
        $this->db->trans_complete();
        echo json_encode(['status' => 'Success', 'message' => "Purchase order updated successfully! <a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='fa fa-eye'></span> View</a>"]);
    } else {
        $this->db->trans_rollback();
        echo json_encode(['status' => 'Error', 'message' => "Something went wrong!"]);
    }
}


    public function update_status()
    {
        $tid = $this->input->post('tid');
        $status = $this->input->post('status');


        $this->db->set('status', $status);
        $this->db->where('id', $tid);
        $this->db->update('geopos_purchase');

        echo json_encode(array('status' => 'Success', 'message' =>
            'Purchase Order Status updated successfully!', 'pstatus' => $status));
    }

    public function file_handling()
    {
        if ($this->input->get('op')) {
            $name = $this->input->get('name');
            $invoice = $this->input->get('invoice');
            if ($this->purchase->meta_delete($invoice, 4, $name)) {
                echo json_encode(array('status' => 'Success'));
            }
        } else {
            $id = $this->input->get('id');
            $this->load->library("Uploadhandler_generic", array(
                'accept_file_types' => '/\.(gif|jpe?g|png|docx|docs|txt|pdf|xls)$/i', 'upload_dir' => FCPATH . 'userfiles/attach/', 'upload_url' => base_url() . 'userfiles/attach/'
            ));
            $files = (string)$this->uploadhandler_generic->filenaam();
            if ($files != '') {

                $this->purchase->meta_insert($id, 4, $files);
            }
        }
    }
	
        
	
public function supplierquotelist()
{
    $this->load->model('purchase_model');

    $list = $this->purchase_model->get_supplier_quotation_datatables();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $quotation) {
        $no++;
        $row = array();
        $row[] = $no;
       $row[] = '<a href="' . base_url('purchase/viewsupplierquote/' . $quotation->id) . '">SQ#' . $quotation->id . '</a>';
        $row[] = $quotation->supplier_name;
       $row[] = date('d-m-Y', strtotime($quotation->order_date));
        $row[] = $quotation->total_item;
        $row[] = $quotation->fill_item;

        if ($quotation->total_item == $quotation->fill_item) {
            $status = '<span class="badge badge-success">Filled</span>';
        } else {
            $status = '<span class="badge badge-warning">Pending</span>';
        }

        $row[] = $status;

	$row[] = '<a href="' . base_url('purchase/comparesupplierquote/' . $quotation->id) . '"  class="btn btn-blue btn-xs ">Compare</span></a>';
        $data[] = $row;
		
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->purchase_model->count_all_supplier_quotation(),
        "recordsFiltered" => $this->purchase_model->count_filtered_supplier_quotation(),
        "data" => $data,
    );
    echo json_encode($output);
}

public function supplierchallanlist()
{
    $this->load->model('purchase_model');

    $list = $this->purchase_model->get_supplier_challan_datatables();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $quotation) {
        $no++;
        $row = array();
        $row[] = $no;
       $row[] = '<a href="' . base_url('purchase/viewsupplierquote/' . $quotation->id) . '">SQ#' . $quotation->id . '</a>';
        $row[] = $quotation->supplier_name;
       $row[] = date('d-m-Y', strtotime($quotation->order_date));
        $row[] = $quotation->total_item;
        $row[] = $quotation->fill_item;

        if ($quotation->total_item == $quotation->fill_item) {
            $status = '<span class="badge badge-success">Filled</span>';
        } else {
            $status = '<span class="badge badge-warning">Pending</span>';
        }

        $row[] = $status;

	$row[] = '<a href="' . base_url('purchase/challancreate/' . $quotation->id) . '"  class="btn btn-blue btn-xs ">Convert To Purchase</span></a>';
        $data[] = $row;
		
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->purchase_model->count_all_supplier_challan(),
        "recordsFiltered" => $this->purchase_model->count_filtered_supplier_challan(),
        "data" => $data,
    );
    echo json_encode($output);
}



/* public function viewsupplierquote($id){
	
	
	//echo $id;
	 $head['title'] = "Supplier Quotation Details";
        $head['usernm'] = $this->aauth->get_user()->username;
	   $this->load->view('fixed/header', $head);
        $this->load->view('indent/viewsupplierquote', $data);
        $this->load->view('fixed/footer');
	
} */

public function viewsupplierquote($id)
{
    $this->load->model('purchase_model');

    $quotation = $this->purchase_model->get_supplier_quotation($id);
    $items = $this->purchase_model->get_supplier_quotation_items($id);

    if (!$quotation) {
        show_error('Quotation not found', 404);
    }

    $data['quotation'] = $quotation;
    $data['items'] = $items;

    $head['title'] = "Supplier Quotation View";
    $this->load->view('fixed/header', $head);
    $this->load->view('indent/viewsupplierquote', $data);
    $this->load->view('fixed/footer');
}


public function comparesupplierquote($id)
{
    $this->load->model('purchase_model');

    // Get current quotation
    $quotation = $this->purchase_model->get_supplier_quotation($id);
    if (!$quotation) {
        show_error('Quotation not found', 404);
    }

    // Get all quotations with the same reference_no
    $quotations = $this->purchase_model->get_quotations_by_reference($quotation->reference_no);
    $items_grouped = $this->purchase_model->get_comparison_items($quotation->reference_no);

    $data['quotations'] = $quotations;
    $data['items_grouped'] = $items_grouped;
    $data['reference_no'] = $quotation->reference_no;

    $head['title'] = "Supplier Quotation Compare";
    $this->load->view('fixed/header', $head);
    $this->load->view('indent/comparesupplierquote', $data);
    $this->load->view('fixed/footer');
}



public function update_received_item()
{
    $id = $this->input->post('id');
    $data = [
        'product_name'  => $this->input->post('product_name'),
        'required_qty'  => $this->input->post('required_qty'),
        'unit'          => $this->input->post('unit'),
        'received_qty'  => $this->input->post('received_qty'),
        'balance_qty'   => $this->input->post('balance_qty'),
        'supplier_id'   => $this->input->post('supplier_id'),
        'supplier_name' => $this->input->post('supplier'),
        'price'         => $this->input->post('price'),
        'description'   => $this->input->post('description'),
        'amount'        => $this->input->post('amount'),
    ];

    $this->db->where('id', $id);
    $this->db->update('received_items', $data);

echo $this->db->last_query();
    echo json_encode(['status' => 'success', 'message' => 'Item updated successfully!']);
}


public function insert_received_items($master_id)
{
    $products = $this->input->post('product_name');
    if (!empty($products)) {
        foreach ($products as $key => $pname) {
            if (trim($pname) == '') continue;

            $data = [
                'master_id'     => $master_id,
                'product_name'  => $pname,
                'required_qty'  => $this->input->post('required_qty')[$key],
                'unit'          => $this->input->post('product_unit')[$key],
                'received_qty'  => $this->input->post('received_qty')[$key],
                'balance_qty'   => $this->input->post('balance_qty')[$key],
                'supplier_id'   => $this->input->post('supplier_id')[$key],
                'supplier_name' => $this->input->post('supplier')[$key],
                'price'         => $this->input->post('product_price')[$key],
                'description'   => $this->input->post('product_description')[$key],
                'amount'        => $this->input->post('product_subtotal')[$key],
            ];
            $this->db->insert('received_items', $data);
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'New items inserted successfully!']);
}

public function convert_to_challan($master_id = null) {
	
	
        header('Content-Type: application/json');

        if (!$master_id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing master ID']);
            return;
        }

        if ($this->purchase->is_master_converted($master_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Already converted to challan']);
            return;
        }

        $master = $this->purchase->get_master($master_id);
        if (!$master) {
            echo json_encode(['status' => 'error', 'message' => 'Master record not found']);
            return;
        }

        $items = $this->purchase->get_items_by_master($master_id);
        if (empty($items)) {
            echo json_encode(['status' => 'error', 'message' => 'No items to convert']);
            return;
        }

        // Group items by supplier
        $grouped = [];
        foreach ($items as $it) {
            $sid = $it->supplier_id ?? 0;
            if (!isset($grouped[$sid])) $grouped[$sid] = [];
            $grouped[$sid][] = $it;
        }

        $this->db->trans_begin();

        try {
            foreach ($grouped as $supplier_id => $supplier_items) {
                $invoice_no = 'SQ-' . date('Ymd') . '-' . mt_rand(1000,9999);
                $reference_no = $master->reference ?? 'REF-' . $master_id;

                $quotation_data = [
                    'supplier_id' => $supplier_id,
                    'invoice_no' => $invoice_no,
                    'reference_no' => $reference_no,
                    'order_date' => date('Y-m-d'),
                    'due_date' => null,
                    'tax' => null,
                    'discount' => null,
                    'note' => 'Auto-generated from received ID ' . $master_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'adm_status' => 1
                ];

                $quote_id = $this->purchase->insert_seller_quotation($quotation_data);

                $items_batch = [];
                foreach ($supplier_items as $si) {
                    $items_batch[] = [
                        'order_id' => $quote_id,
                        'item_id' => $si->product_id ?? 0,
                        'item_name' => $si->product_name ?? '',
                        'quantity' => $si->required_qty ?? 0,
                        'unit' => $si->unit ?? '',
                        'fill_quantity' => $si->received_qty ?? 0,
                        'fill_rate' => $si->price ?? 0,
                        'description' => $si->description ?? '',
                        'amount' => $si->amount ?? (($si->received_qty ?? 0) * ($si->price ?? 0)),
                        'adm_status' => 1
                    ];
                }

                $this->purchase->insert_seller_quotation_items_batch($items_batch);
            }

            $this->purchase->mark_master_converted($master_id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => 'Transaction failed']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['status' => 'success', 'message' => 'Converted to purchase challan successfully']);
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


 public function update_product_price()
    {
        $items = $this->input->post('items');

        if (!empty($items) && is_array($items)) {
            $this->db->trans_begin();

            try {
                foreach ($items as $item) {
                    $item_id = isset($item['item_id']) ? (int)$item['item_id'] : 0;
                    $rate = isset($item['rate']) ? (float)$item['rate'] : 0;

                    if ($item_id <= 0 || $rate <= 0) {
                        continue;
                    }

                    $sqi = $this->db->get_where('seller_quotation_items', ['id' => $item_id])->row();
                    if ($sqi && !empty($sqi->item_id)) {
                        // 🔹 Set adm_status to 0 for other quotation items of the same product & reference
                        $sq = $this->db->get_where('seller_quotation', ['id' => $sqi->order_id])->row();
                        if ($sq) {
                            $ref_no = $sq->reference_no;
                            if (!empty($sqi->item_id)) {
                                $this->db->query("
                                    UPDATE seller_quotation_items sqi
                                    JOIN seller_quotation sq ON sq.id = sqi.order_id
                                    SET sqi.adm_status = 0
                                    WHERE sq.reference_no = ? AND sqi.item_id = ? AND sqi.unit = ?
                                ", [$ref_no, $sqi->item_id, $sqi->unit]);
                            } else {
                                $this->db->query("
                                    UPDATE seller_quotation_items sqi
                                    JOIN seller_quotation sq ON sq.id = sqi.order_id
                                    SET sqi.adm_status = 0
                                    WHERE sq.reference_no = ? AND sqi.item_name = ?
                                ", [$ref_no, $sqi->item_name]);
                            }
                        }

                        // 🔹 Set current item's adm_status to 1
                        $this->db->where('id', $item_id);
                        $this->db->update('seller_quotation_items', ['adm_status' => 1]);

                        $product = $this->db->get_where('products', ['id' => $sqi->item_id])->row_array();

                        if (empty($product)) {
                            continue;
                        }

                        $updated_date = date('Y-m-d H:i:s');
                        $current_stock = isset($product['stock']) ? (float)$product['stock'] : 0;

                        $this->db->query("SET @disable_triggers = 1;");
                        $this->db->where('id', (int)$sqi->item_id);
                        $this->db->update('products', [
                            'purchase_price' => $rate,
                            'fproduct_price' => $rate,
                            'updated_date' => $updated_date
                        ]);
                        $this->db->query("SET @disable_triggers = NULL;");

                        $variant_id_for_ledger = 0;
                        $unit = !empty($sqi->unit) ? $sqi->unit : '';

                        $ledger_data = [
                            'order_id' => !empty($sqi->order_id) ? (int)$sqi->order_id : 0,
                            'product_id' => (int)$sqi->item_id,
                            'product_variants' => $variant_id_for_ledger,
                            'product_name' => $product['name'],
                            'unit' => $unit,
                            'seller_id' => 0,
                            'customer_id' => 0,
                            'sell_qty' => 0,
                            'purchage_qty' => 0,
                            'wastage' => 0,
                            'sell_amount' => 0,
                            'purchage_amount' => 0,
                            'open_stock' => $current_stock,
                            'close_stock' => $current_stock,
                            'created_date' => $updated_date,
                            'created_by' => (int)$this->aauth->get_user()->id,
                            'purchage_rate' => $rate,
                            'sell_rate' => 0,
                            'ledger_type' => 'Price Update'
                        ];

                        $this->db->insert('product_ledger', $ledger_data);
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update prices']);
                } else {
                    $this->db->trans_commit();
                    echo json_encode(['status' => 'success', 'message' => 'Prices updated successfully']);
                }
            } catch (Exception $e) {
                $this->db->trans_rollback();
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No items received']);
        }
    }


}
