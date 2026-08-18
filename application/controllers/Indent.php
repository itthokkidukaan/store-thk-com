<?php




defined('BASEPATH') or exit('No direct script access allowed');



class Indent extends CI_Controller

{

    public function __construct()

    {

        parent::__construct();

        $this->load->model('quote_model', 'quote');
$this->load->model('purchase_model', 'purchase');
        $this->load->library("Aauth");

        if (!$this->aauth->is_loggedin()) {

            redirect('/user/', 'refresh');

        }

        if (!$this->aauth->premission(1)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }

        $this->li_a = 'sales';



    }



    //create invoice

    public function create()

    {

        $this->load->model('plugins_model', 'plugins');

        $data['emp'] = $this->plugins->universal_api(69);

        if ($data['emp']['key1']) {

            $this->load->model('employee_model', 'employee');

            $data['employee'] = $this->employee->list_employee();

        }

        $this->load->library("Common");

        $data['taxlist'] = $this->common->taxlist($this->config->item('tax'));

        $this->load->model('customers_model', 'customers');

        $this->load->model('plugins_model', 'plugins');

        $data['exchange'] = $this->plugins->universal_api(5);

        $data['currency'] = $this->quote->currencies();

        $data['customergrouplist'] = $this->customers->group_list();

        $data['lastinvoice'] = $this->quote->lastquote();

        $data['terms'] = $this->quote->billingterms();

        $head['title'] = "New Indent";

        $head['usernm'] = $this->aauth->get_user()->username;

        $data['warehouse'] = $this->quote->warehouses();

        $data['taxdetails'] = $this->common->taxdetail();

        $this->load->view('fixed/header', $head);

        $this->load->view('indent/newindent', $data);

        $this->load->view('fixed/footer');

    }



    //edit invoice

    public function edit()

    {

        $this->load->model('customers_model', 'customers');

        $data['customergrouplist'] = $this->customers->group_list();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

        $data['terms'] = $this->quote->billingterms();

        $data['invoice'] = $this->quote->quote_details($tid);

        $data['products'] = $this->quote->quote_products($tid);

        $data['currency'] = $this->quote->currencies();

        $head['title'] = "Edit indent #" . $data['invoice']['tid'];

        $head['usernm'] = $this->aauth->get_user()->username;

        $data['warehouse'] = $this->quote->warehouses();

        $this->load->model('plugins_model', 'plugins');

        $data['exchange'] = $this->plugins->universal_api(5);

        $this->load->library("Common");

        $data['taxlist'] = $this->common->taxlist_edit($data['invoice']['taxstatus']);

        $this->load->view('fixed/header', $head);

        $this->load->view('indent/edit', $data);

        $this->load->view('fixed/footer');

    }



    //invoices list

    public function index()

    {

        $head['title'] = "Manage indent";

        $data['eid'] = intval($this->input->get('eid'));
		//$data['quotes'] = $this->quote->get_quotes_list();
        $head['usernm'] = $this->aauth->get_user()->username;
		 $limit = 10;
    $start = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

    // Date filter parameters
    $from_date = $this->input->get('from_date');
    $to_date = $this->input->get('to_date');

    // Fetch Data
    $data['quotes'] = $this->quote->get_quotes_list($limit, $start, $from_date, $to_date);
    $total_records = $this->quote->get_quotes_count($from_date, $to_date);

    // Pagination
    $this->load->library('pagination');
    $config['base_url'] = base_url('indent/index');
    $config['total_rows'] = $total_records;
    $config['per_page'] = $limit;
    $config['uri_segment'] = 3;
    $this->pagination->initialize($config);

    $data['pagination'] = $this->pagination->create_links();
    $data['total_records'] = $total_records;
    $data['limit'] = $limit;
    $data['start'] = $start;

        $this->load->view('fixed/header', $head);

        $this->load->view('indent/indent', $data);

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





        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;

        $refer = $this->input->post('refer');

        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);

        $proposal = $this->input->post('propos');

        $total_tax = 0;

        $total_discount = 0;

        $discountFormat = $this->input->post('discountFormat');

        $pterms = $this->input->post('pterms');



        $this->load->model('plugins_model', 'plugins');

        $empl_e = $this->plugins->universal_api(69);

        if ($empl_e['key1']) {

            $emp = $this->input->post('employee');

        } else {

            $emp = $this->aauth->get_user()->id;

        }



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

        //products

        $transok = true;

        //Invoice Data

        $bill_date = datefordatabase($invoicedate);

        $bill_due_date = $invocieduedate;

        $data = array('tid' => $invocieno, 'invoicedate' => $bill_date, 'invoiceduedate' => $bill_date, 'subtotal' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount' => $total_discount, 'tax' => $total_tax, 'total' => $total, 'notes' => $notes, 'csd' => $customer_id, 'eid' => $emp, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms, 'proposal' => $proposal, 'multi' => $currency, 'loc' => $this->aauth->get_user()->loc);

        if ($this->db->insert('geopos_quotes', $data)) {

            $pid = $this->input->post('pid');

            $invocieno = $this->db->insert_id();

            $productlist = array();

            $prodindex = 0;

            $itc = 0;

            $flag = false;

            $product_id = $this->input->post('pid');

            $product_name1 = $this->input->post('product_name', true);

            $product_qty = $this->input->post('product_qty');

            $product_price = $this->input->post('product_price');

            $product_tax = $this->input->post('product_tax');

            $product_discount = $this->input->post('product_discount');

            $product_subtotal = $this->input->post('product_subtotal');

            $ptotal_tax = $this->input->post('taxa');

            $ptotal_disc = $this->input->post('disca');

            $product_des = $this->input->post('product_description', true);

            $product_hsn = $this->input->post('hsn');

            $product_unit = $this->input->post('product_unit');

            foreach ($pid as $key => $value) {



                $total_discount += numberClean(@$ptotal_disc[$key]);

                $total_tax += numberClean($ptotal_tax[$key]);

                $data = array(

                    'tid' => $invocieno,

                    'pid' => $product_id[$key],

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

                    'unit' => $product_unit[$key]

                );

                $flag = true;

                $productlist[$prodindex] = $data;

                $i++;

                $prodindex++;

                $amt = numberClean($product_qty[$key]);

                $itc += $amt;

            }

            if ($prodindex > 0) {

                $this->db->insert_batch('geopos_quotes_items', $productlist);

                $this->db->set(array('discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc), 'tax' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc), 'items' => $itc));

                $this->db->where('id', $invocieno);

                $this->db->update('geopos_quotes');

            } else {

                echo json_encode(array('status' => 'Error', 'message' =>

                    "Please choose product from product list. Go to Item manager section if you have not added the products."));

                $transok = false;

            }



            echo json_encode(array('status' => 'Success', 'message' =>

                "chalan has  been created <a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> View </a> &nbsp; &nbsp;<a href='create' class='btn btn-amber btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span> " . $this->lang->line('Create') . "  </a>"));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>

                $this->lang->line('ERROR')));

            $transok = false;

        }

        if ($transok) {

            $this->db->trans_complete();

        } else {

            $this->db->trans_rollback();

        }

    }





    public function ajax_list()

    {

        $eid = 0;

        if ($this->aauth->premission(9)) {

            $eid = $this->input->post('eid');

        }

        $list = $this->quote->get_datatables($eid);

        $data = array();

        $no = $this->input->post('start');

        foreach ($list as $invoices) {

            $no++;

            $row = array();

            $row[] = $no;

            $row[] = '<a href="' . base_url("chalan/view?id=$invoices->id") . '">&nbsp; ' . $invoices->tid . '</a>';

            $row[] = $invoices->name;

            $row[] = dateformat($invoices->invoicedate);

            $row[] = amountExchange($invoices->total, 0, $this->aauth->get_user()->loc);

            $row[] = '<span class="badge st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';

            $row[] = '<a href="' . base_url("chalan/view?id=$invoices->id") . '" class="btn btn-blue btn-sm"><i class="fa fa-eye"></i></a> &nbsp; <a href="' . base_url("billing/printchalan?id=$invoices->id") . '&d=1" class="btn btn-info btn-sm"  title="Download"><span class="fa fa-download"></span></a>&nbsp;<a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-sm delete-object"><span class="fa fa-trash"></span></a>';

            $data[] = $row;

        }



        $output = array(

            "draw" => $_POST['draw'],

            "recordsTotal" => $this->quote->count_all($eid),

            "recordsFiltered" => $this->quote->count_filtered($eid),

            "data" => $data,

        );

        //output to json format

        echo json_encode($output);



    }



    public function view($date = null)

    {


$data['chalan'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
$data['totalchalan'] = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;


        $this->load->model('accounts_model');

        $data['acclist'] = $this->accounts_model->accountslist();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

        $data['invoice'] = $this->quote->quote_details($tid);

        if (empty($date)) {
            $date = $data['invoice']['invoicedate'] ?? '';
        }

        $data['attach'] = $this->quote->attach($tid);

        $data['employee'] = $this->quote->employee($data['invoice']['eid'] ?? 0);

        $head['title'] = "indent #" . $date;

        $head['usernm'] = $this->aauth->get_user()->username;
		 $data['quote_items'] = $this->quote->get_quote_items_by_date($date);


        $this->load->view('fixed/header', $head);

        if ($data['invoice']) $this->load->view('indent/view', $data);

        $this->load->view('fixed/footer');

    }  


    public function cretequote($date = null)

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
   
      
        $data['warehouse'] = $this->purchase->warehouses();
        $data['taxdetails'] = $this->common->taxdetail();

$data['chalan'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
$data['totalchalan'] = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;


        $this->load->model('accounts_model');

        $data['acclist'] = $this->accounts_model->accountslist();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

        $data['invoice'] = $this->quote->quote_details($tid);

        if (empty($date)) {
            $date = $data['invoice']['invoicedate'] ?? '';
        }


        $data['attach'] = $this->quote->attach($tid);

        $data['employee'] = $this->quote->employee($data['invoice']['eid']);

        $head['title'] = "indent #" . $date;

        $head['usernm'] = $this->aauth->get_user()->username;
		 $data['quote_items'] = $this->quote->get_quote_items_by_date($date);


        $this->load->view('fixed/header', $head);

        if ($data['invoice']) $this->load->view('indent/cretequote', $data);

        $this->load->view('fixed/footer');

    }    


	public function receiveditem($date = null)

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
   
      
        $data['warehouse'] = $this->purchase->warehouses();
        $data['taxdetails'] = $this->common->taxdetail();

$data['chalan'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
$data['totalchalan'] = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;


        $this->load->model('accounts_model');

        $data['acclist'] = $this->accounts_model->accountslist();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

        $data['invoice'] = $this->quote->quote_details($tid);

        if (empty($date)) {
            $date = $data['invoice']['invoicedate'] ?? '';
        }


        $data['attach'] = $this->quote->attach($tid);

        $data['employee'] = $this->quote->employee($data['invoice']['eid']);

        $head['title'] = "indent #" . $date;

        $head['usernm'] = $this->aauth->get_user()->username;
		 $data['quote_items'] = $this->quote->get_quote_items_by_date($date);


        $this->load->view('fixed/header', $head);

        if ($data['invoice']) $this->load->view('indent/receiveditem', $data);

        $this->load->view('fixed/footer');

    }    

	public function printindent($date = null)

    {


$data['chalan'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
$data['totalchalan'] = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;


        $this->load->model('accounts_model');

        $data['acclist'] = $this->accounts_model->accountslist();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

        $data['invoice'] = $this->quote->quote_details($tid);

        if (empty($date)) {
            $date = $data['invoice']['invoicedate'] ?? '';
        }

        $data['products'] = $this->quote->quote_products($tid);

        $data['attach'] = $this->quote->attach($tid);

        $data['employee'] = $this->quote->employee($data['invoice']['eid']);

        $head['title'] = "indent #" . $date;

        $head['usernm'] = $this->aauth->get_user()->username;
		 $data['quote_items'] = $this->quote->get_quote_items_by_date($date);

      /*   $this->load->view('fixed/header', $head);

        if ($data['invoice']) $this->load->view('indent/view', $data);

        $this->load->view('fixed/footer'); */
		
		
		  ini_set('memory_limit', '64M');

                $html = $this->load->view('indent/printview', $data, true);

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
            $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $date . '</div>');

            $pdf->WriteHTML($html);

            if ($this->input->get('d')) {

                $pdf->Output('Indent_#' . $date . '.pdf', 'D');
            } else {
                $pdf->Output('Indent_#' . $date . '.pdf', 'I');
            }


    }  


	public function storeview($date = null)

    {


$data['chalan'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
$data['totalchalan'] = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;


        $this->load->model('accounts_model');

        $data['acclist'] = $this->accounts_model->accountslist();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

  $data['invoice'] = $this->quote->quote_details($tid);

        if (empty($date)) {
            $date = $data['invoice']['invoicedate'] ?? '';
        }

    
	   
// Step 1: Get distinct stores for that date
$this->db->select('DISTINCT(u.id), u.username');
$this->db->from('geopos_quotes q');
$this->db->join('users u', 'q.csd = u.id');
$this->db->where('q.invoicedate', $date);
$this->db->order_by('u.username', 'asc');
$stores = $this->db->get()->result_array();

// Step 2: Get all products + unit for that date
$this->db->select('qi.pid, qi.unit, qi.product, qi.code, u.id as store_id, qi.qty, p.name as productname');
$this->db->from('geopos_quotes q');
$this->db->join('geopos_quotes_items qi', 'q.id = qi.tid');
$this->db->join('users u', 'q.csd = u.id');
$this->db->join('products p', ' qi.pid = p.id ');
$this->db->where('q.invoicedate', $date);
$this->db->order_by('qi.product', 'asc');
$products_data = $this->db->get()->result_array();


$products = [];

foreach ($products_data as $row) {
    $pname = $row['productname'];
	 $converted = convert_to_base_unit($row['unit']);
	// echo  $converted['unit'];
	 
//$unit = preg_replace('/[0-9]+/', '', $row['unit']);
$unit = $converted['unit'];
    if (!isset($products[$pname])) {
        $products[$pname] = [
            'unit' => $unit,
            'stores' => []
        ];
    }

    if (!isset($products[$pname]['stores'][$row['store_id']])) {
        $products[$pname]['stores'][$row['store_id']] = 0;
    }
    $products[$pname]['stores'][$row['store_id']] += $row['qty'] * $converted['qty'];
}

$data['stores'] = $stores;
$data['products'] = $products;



        $head['title'] = "indent #" . $date;

        $head['usernm'] = $this->aauth->get_user()->username;
		// $data['quote_items'] = $this->quote->viewstore_quote_items_by_date($date);

        $this->load->view('fixed/header', $head);

        if ($data['invoice']) $this->load->view('indent/storeview', $data);

        $this->load->view('fixed/footer'); 
		
		 

		

    }


	public function printstoreview($date = null)

    {


$data['chalan'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
$data['totalchalan'] = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;


        $this->load->model('accounts_model');

        $data['acclist'] = $this->accounts_model->accountslist();

        $tid = intval($this->input->get('id'));

        $data['id'] = $tid;

       $data['invoice'] = $this->quote->quote_details($tid);

        if (empty($date)) {
            $date = $data['invoice']['invoicedate'] ?? '';
        }

    
	   
// Step 1: Get distinct stores for that date
$this->db->select('DISTINCT(u.id), u.username');
$this->db->from('geopos_quotes q');
$this->db->join('users u', 'q.csd = u.id');
$this->db->where('q.invoicedate', $date);
$this->db->order_by('u.username', 'asc');
$stores = $this->db->get()->result_array();

// Step 2: Get all products + unit for that date
$this->db->select('qi.pid, qi.unit, qi.product, qi.code, u.id as store_id, qi.qty, p.name as productname');
$this->db->from('geopos_quotes q');
$this->db->join('geopos_quotes_items qi', 'q.id = qi.tid');
$this->db->join('users u', 'q.csd = u.id');
$this->db->join('products p', ' qi.pid = p.id ');
$this->db->where('q.invoicedate', $date);
$this->db->order_by('qi.product', 'asc');
$products_data = $this->db->get()->result_array();
$products = [];
foreach ($products_data as $row) {
    $pname = $row['productname'];
  $converted = convert_to_base_unit($row['unit']);
$unit = $converted['unit'];
$qty = $converted['qty'];

    if (!isset($products[$pname])) {
        $products[$pname] = [
            'unit' => $unit,
            'stores' => []
        ];
    }

    if (!isset($products[$pname]['stores'][$row['store_id']])) {
        $products[$pname]['stores'][$row['store_id']] = 0;
    }
    $products[$pname]['stores'][$row['store_id']] += $row['qty'] * $qty;
}

$data['stores'] = $stores;
$data['products'] = $products;



        $head['title'] = "indent #" . $date;

        $head['usernm'] = $this->aauth->get_user()->username;
		// $data['quote_items'] = $this->quote->viewstore_quote_items_by_date($date);

       /*  $this->load->view('fixed/header', $head);

        if ($data['invoice']) $this->load->view('indent/storeview', $data);

        $this->load->view('fixed/footer'); */
		
		  ini_set('memory_limit', '64M');

                $html = $this->load->view('indent/printstoreview', $data, true);

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
            $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $date . '</div>');

            $pdf->WriteHTML($html);

            if ($this->input->get('d')) {

                $pdf->Output('Indent_#' . $date . '.pdf', 'D');
            } else {
                $pdf->Output('Indent_#' . $date . '.pdf', 'I');
            }

		

    }

/* 
public function store_puquote()
{
    if ($this->input->method() == 'post') {

        $supplier_ids   = $this->input->post('supplier_ids');
        $supplier_phone = $this->input->post('supplier_phone'); // array
        $supplier_name  = $this->input->post('supplier_name');  // array
        $refer          = $this->input->post('refer');
        $productname  = $this->input->post('product_name');
        $pid          = $this->input->post('pid');
        $productunit  = $this->input->post('product_unit');
        $product_qty  = $this->input->post('product_qty');

        foreach ($supplier_ids as $index => $supplier_id) {

            // Store quote in DB
            $order_data = array(
                'supplier_id'   => $supplier_id,
                'invoice_no'    => uniqid('QTN'), // Replace with actual logic if needed
                'reference_no'  => $refer,
                'discount'      => 0,
                'order_date'    => datefordatabase($this->input->post('invoicedate')),
                'created_at'    => date('Y-m-d H:i:s'),
                'due_date'      => datefordatabase($this->input->post('invocieduedate')),
            );

            $order_id = $this->purchase->insert_puquotes($order_data);
            foreach ($pid as $key => $value) {
                $item = array(
                    'order_id'   => $order_id,
                    'item_id'    => $pid[$key],
                    'item_name'  => $productname[$key],
                    'quantity'   => numberClean($product_qty[$key]),
                    'unit'       => $productunit[$key],
                );

                $this->purchase->insert_quoitem($item);
            }

           
            $phone         = $supplier_phone[$index];
            $name          = $supplier_name[$index];
            $mobile_number = "91" . $phone;
            $myquoturl     = base_url("supplierqotation/{$phone}/{$refer}");

            $message = "Dear {$name},\n\n"
                     . "Kindly review our today's purchase requirement by clicking the link below.\n"
                     . "Please fill in the quantity and your best rate for each item and submit the form at your earliest convenience.\n\n"
                     . "{$myquoturl}\n\n"
                     . "Thank you for your cooperation.\n\n"
                     . "Best regards, Thok Ki Dukaan Online Store";

            $data = array(
                'AUTH_KEY'    => 'THOKKIDUKAAN',
                'instance_id' => '427790',
                'message'     => $message,
                'phone'       => $mobile_number
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://wapi.dialtext.com/sendMessage.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                log_message('error', 'cURL Error: ' . curl_error($ch));
            }

            curl_close($ch);
        }

        echo json_encode(['status' => 'success', 'message' => 'Order saved and WhatsApp sent.']);
    } else {
        show_404();
    }
} */



public function store_puquote()
{
    if ($this->input->method() == 'post') {

        $supplier_ids   = $this->input->post('supplier_ids');
        $supplier_phone = $this->input->post('supplier_phone'); // array
        $supplier_name  = $this->input->post('supplier_name');  // array
        $refer          = $this->input->post('refer');
        $productname    = $this->input->post('product_name');
        $pid            = $this->input->post('pid');
        $productunit    = $this->input->post('product_unit');
        $product_qty    = $this->input->post('product_qty');

        foreach ($supplier_ids as $index => $supplier_id) {

            // 🔹 Step 1: Duplicate check
            $this->db->where('supplier_id', $supplier_id);
            $this->db->where('reference_no', $refer);
            $duplicate_check = $this->db->get('seller_quotation')->row_array();

            if ($duplicate_check) {
                // Agar duplicate hai -> Data insert skip karein, WhatsApp bhejein
                $order_id = $duplicate_check['id']; // Existing quotation id
            } else {
                // 🔹 Step 2: Insert new quotation
                $order_data = array(
                    'supplier_id'   => $supplier_id,
                    'invoice_no'    => uniqid('QTN'),
                    'reference_no'  => $refer,
                    'discount'      => 0,
                    'order_date'    => datefordatabase($this->input->post('invoicedate')),
                    'created_at'    => date('Y-m-d H:i:s'),
                    'due_date'      => datefordatabase($this->input->post('invocieduedate')),
                );

                $order_id = $this->purchase->insert_puquotes($order_data);

                // 🔹 Step 3: Allowed products filter
                $allowed_products = $this->db->select('product_id')
                    ->from('supplier_allowed_products')
                    ->where('supplier_id', $supplier_id)
                    ->get()
                    ->result_array();

                $allowed_ids = array_column($allowed_products, 'product_id');

                foreach ($pid as $key => $value) {
                    if (empty($allowed_ids) || in_array($pid[$key], $allowed_ids)) {
                        $item = array(
                            'order_id'   => $order_id,
                            'item_id'    => $pid[$key],
                            'item_name'  => $productname[$key],
                            'quantity'   => numberClean($product_qty[$key]),
                            'unit'       => $productunit[$key],
                        );

                        $this->purchase->insert_quoitem($item);
                    }
                }
            }

            // 🔹 Step 4: Send WhatsApp message (Always send for both new & duplicate)
/*             $phone         = $supplier_phone[$index];
            $name          = $supplier_name[$index];
            $mobile_number = "91" . $phone;
            $myquoturl     = base_url("supplierqotation/{$phone}/{$refer}");

            $message = "Dear {$name},\n\n"
                     . "Kindly review our today's purchase requirement by clicking the link below.\n"
                     . "Please fill in the quantity and your best rate for each item and submit the form at your earliest convenience.\n\n"
                     . "{$myquoturl}\n\n"
                     . "Thank you for your cooperation.\n\n"
                     . "Best regards, Thok Ki Dukaan Online Store";

            $data = array(
                'AUTH_KEY'    => 'THOKKIDUKAAN',
                'instance_id' => '427790',
                'message'     => $message,
                'phone'       => $mobile_number
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://wapi.dialtext.com/sendMessage.php');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                log_message('error', 'cURL Error: ' . curl_error($ch));
            }

            curl_close($ch); */
			
			$phone         = $supplier_phone[$index];
            $name          = $supplier_name[$index];
            
            // Clean phone number (keep only digits) and extract last 10 digits, then prepend 91
            $clean_phone   = preg_replace('/[^0-9]/', '', $phone);
            $mobile_number = "91" . substr($clean_phone, -10);
            
            $myquoturl     = base_url("supplierqotation/{$phone}/{$refer}");

            $message_text = "Dear {$name}, Kindly review our today's purchase requirement by clicking the link below. "
                          . "Please fill in the quantity and your best rate for each item and submit the form.\n\n"
                          . "{$myquoturl}";

            // AiSensy payload
            $data = array(
                "apiKey"          => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY",
                "campaignName"    => "indent send for quotation",
                "destination"     => $mobile_number,
                "userName"        => "Thok ki dukaan",
                "templateParams"  => [$name, $myquoturl], // AiSensy template
                "source"          => "supplier-quotation",
                "media"           => new stdClass(),
                "buttons"         => [],
                "carouselCards"   => [],
                "location"        => new stdClass(),
                "attributes"      => new stdClass(),
                "paramsFallbackValue" => array(
                    "FirstName" => $name
                )
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://backend.aisensy.com/campaign/t1/api/v2");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Required for localhost development
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                log_message('error', 'cURL Error in store_puquote: ' . curl_error($ch));
            } else {
                log_message('info', 'AiSensy WhatsApp API Response in store_puquote: ' . $response);
            }

            curl_close($ch);

        }

        echo json_encode(['status' => 'success', 'message' => 'Order processed and WhatsApp sent.']);
    } else {
        show_404();
    }
}



    public function printquote()

    {



        $tid = intval($this->input->get('id'));



        $data['id'] = $tid;

        $data['title'] = "Quote $tid";

        $data['invoice'] = $this->quote->quote_details($tid);

        $data['products'] = $this->quote->quote_products($tid);

        $data['employee'] = $this->quote->employee($data['invoice']['eid']);

        $data['general'] = array('title' => $this->lang->line('Quote'), 'person' => $this->lang->line('Customer'), 'prefix' => prefix(1), 't_type' => 0);

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



        $file_name = preg_replace('/[^A-Za-z0-9]+/', '-', 'Quote__' . $data['invoice']['name'] . '_' . $data['invoice']['tid']);

        if ($this->input->get('d')) {

            $pdf->Output($file_name . '.pdf', 'D');

        } else {

            $pdf->Output($file_name . '.pdf', 'I');

        }

    }



    public function delete_i()

    {

        $id = $this->input->post('deleteid');

        if ($this->quote->quote_delete($id)) {

            echo json_encode(array('status' => 'Success', 'message' =>

                $this->lang->line('DELETED')));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>

                $this->lang->line('ERROR')));

        }

    }



    public function editaction()

    {



        $customer_id = $this->input->post('customer_id');

        $invocieno_n = $this->input->post('invocieno');

        $invocieno = $this->input->post('iid');

        $invoicedate = $this->input->post('invoicedate');

        $invocieduedate = $this->input->post('invocieduedate');

        $notes = $this->input->post('notes', true);

        $tax = $this->input->post('tax_handle');

        $total_tax = 0;

        $total_discount = 0;

        $discountFormat = $this->input->post('discountFormat');

        $pterms = $this->input->post('pterms');

        $propos = $this->input->post('propos');

        $currency = $this->input->post('mcurrency');

        $ship_taxtype = $this->input->post('ship_taxtype');

        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $this->aauth->get_user()->loc);

        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $this->aauth->get_user()->loc);

        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $this->aauth->get_user()->loc);

        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;

        $refer = $this->input->post('refer', true);

        $total = rev_amountExchange_s($this->input->post('total'), $currency, $this->aauth->get_user()->loc);



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

        $flag = false;

        $transok = true;





        //Product Data

        $pid = $this->input->post('pid');

        $productlist = array();



        $prodindex = 0;



        $this->db->delete('geopos_quotes_items', array('tid' => $invocieno));

        $product_id = $this->input->post('pid');

        $product_name1 = $this->input->post('product_name', true);

        $product_qty = $this->input->post('product_qty');

        $product_price = $this->input->post('product_price');

        $product_tax = $this->input->post('product_tax');

        $product_discount = $this->input->post('product_discount');

        $product_subtotal = $this->input->post('product_subtotal');

        $ptotal_tax = $this->input->post('taxa');

        $ptotal_disc = $this->input->post('disca');

        $product_des = $this->input->post('product_description', true);

        $product_hsn = $this->input->post('hsn');

        $product_unit = $this->input->post('product_unit');



        foreach ($pid as $key => $value) {



            $total_discount += numberClean(@$ptotal_disc[$key]);

            $total_tax += numberClean($ptotal_tax[$key]);



            $data = array(

                'tid' => $invocieno,

                'pid' => $product_id[$key],

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

                'unit' => $product_unit[$key]

            );



            $flag = true;

            $productlist[$prodindex] = $data;

            $i += numberClean($product_qty[$key]);;

            $prodindex++;

        }



        $bill_date = datefordatabase($invoicedate);

        $bill_due_date = datefordatabase($invocieduedate);



        $total_discount = rev_amountExchange_s(amountFormat_general($total_discount), $currency, $this->aauth->get_user()->loc);

        $total_tax = rev_amountExchange_s(amountFormat_general($total_tax), $currency, $this->aauth->get_user()->loc);



        $data = array('invoicedate' => $bill_date, 'invoiceduedate' => $bill_due_date, 'subtotal' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount' => $total_discount, 'tax' => $total_tax, 'total' => $total, 'notes' => $notes, 'csd' => $customer_id, 'items' => $i, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms, 'proposal' => $propos, 'multi' => $currency);

        $this->db->set($data);

        $this->db->where('id', $invocieno);



        if ($flag) {



            if ($this->db->update('geopos_quotes', $data)) {

                $this->db->insert_batch('geopos_quotes_items', $productlist);

				
                echo json_encode(array('status' => 'Success', 'message' =>

                    $this->lang->line('Quote has  been updated') . " <a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='icon-file-text2' aria-hidden='true'></span> View </a> "));

            } else {

                echo json_encode(array('status' => 'Error', 'message' =>

                    $this->lang->line('ERROR')));

                $transok = false;

            }





        } else {

            echo json_encode(array('status' => 'Error', 'message' =>

                "Please add atleast one product in invoice $invocieno"));

            $transok = false;

        }





        if ($transok) {

            $this->db->trans_complete();

        } else {

            $this->db->trans_rollback();

        }

    }





    public function update_status()

    {

        $tid = $this->input->post('tid');

        $status = $this->input->post('status');





        $this->db->set('status', $status);

        $this->db->where('id', $tid);

        $this->db->update('geopos_quotes');



        echo json_encode(array('status' => 'Success', 'message' =>

            $this->lang->line('Quote Status updated') . '', 'pstatus' => $status));

    }



    public function convert()

    {

        $tid = $this->input->post('tid');



 $invoiceId = $this->quote->convert($tid);

        if ($invoiceId) {



            echo json_encode(array('status' => 'Success', 'message' =>

                $this->lang->line('Chalan to invoice conversion'), 'rediurl' => base_url().'invoices/edit?id='.$invoiceId));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>

                $this->lang->line('ERROR')));

        }

    }



    public function convert_po()

    {

        $tid = $this->input->post('tid');

        $person = $this->input->post('customer_id');





        if ($this->quote->convert_po($tid, $person)) {



            echo json_encode(array('status' => 'Success', 'message' =>

                $this->lang->line('chalan to purchase invoice conversion')));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>

                $this->lang->line('ERROR')));

        }

    }



    public function file_handling()

    {

        if ($this->input->get('op')) {

            $name = $this->input->get('name');

            $invoice = $this->input->get('invoice');

            if ($this->quote->meta_delete($invoice, 2, $name)) {

                echo json_encode(array('status' => 'Success'));

            }

        } else {

            $id = $this->input->get('id');

            $this->load->library("Uploadhandler_generic", array(

                'accept_file_types' => '/\.(gif|jpe?g|png|docx|docs|txt|pdf|xls)$/i', 'upload_dir' => FCPATH . 'userfiles/attach/', 'upload_url' => base_url() . 'userfiles/attach/'

            ));

            $files = (string)$this->uploadhandler_generic->filenaam();

            if ($files != '') {

                $fid = rand(100, 9999);

                $this->quote->meta_insert($id, 2, $files);

            }

        }





    }





    // show DataTable list
     public function receiveditemlist(){
        $data['list'] = $this->quote->get_all();
        $this->load->view('indent/list',$data);
    }

    // show form
  /*   public function create(){
        $this->load->view('indent/form');
    } */

    // form submit
/*  public function submitreceiveditem()
{
    $post = $this->input->post();

$employeephone = $post['employee_phone'];
    $master = array(
        'employee_id'   => $post['employee_ids'],
        'employee_name' => $post['employee_name'] ?? '',
        'order_no'      => $post['invocieno'],
        'reference'     => $post['refer'],
        'order_date'    => date('Y-m-d', strtotime($post['invoicedate'])),
        'due_date'      => date('Y-m-d', strtotime($post['invocieduedate'])),
    );

    $master_id = $this->quote->insert_master($master);

    if ($master_id) {
        $count = count($post['product_name']);
        for ($i = 0; $i < $count; $i++) {
            $items = array(
                'master_id'     => $master_id,
                'product_name'  => $post['product_name'][$i],
                'required_qty'  => (float)$post['required_qty'][$i],
                'unit'          => $post['product_unit'][$i],
                'received_qty'  => (float)$post['received_qty'][$i],
                'balance_qty'   => (float)$post['balance_qty'][$i],
                'supplier_id'   => $post['supplier_id'][$i],
                'supplier_name' => $post['supplier'][$i] ?? '',
                'price'         => (float)$post['product_price'][$i],
                'description'   => $post['product_description'][$i],
                'amount'        => ((float)$post['received_qty'][$i] * (float)$post['product_price'][$i])
            );
            $this->quote->insert_item($items);
        }

        echo json_encode([
            'status'  => 'Success',
            'message' => 'Received item saved Successfully'
        ]);
    } else {
        echo json_encode([
            'status'  => 'Error',
            'message' => $this->lang->line('ERROR')
        ]);
    }
} */



public function submitreceiveditem()
{
    $post = $this->input->post();

    $employeephone = $post['employee_phone'];

    $master = array(
        'employee_id'   => $post['employee_ids'],
        'employee_name' => $post['employee_name'] ?? '',
        'order_no'      => $post['invocieno'],
        'reference'     => $post['refer'],
        'order_date'    => date('Y-m-d', strtotime($post['invoicedate'])),
        'due_date'      => date('Y-m-d', strtotime($post['invocieduedate'])),
    );

    $master_id = $this->quote->insert_master($master);

    if ($master_id) {
        $count = count($post['product_name']);
        for ($i = 0; $i < $count; $i++) {
            $items = array(
                'master_id'     => $master_id,
                'product_name'  => $post['product_name'][$i],
                'required_qty'  => (float)$post['required_qty'][$i],
                'unit'          => $post['product_unit'][$i],
                'received_qty'  => (float)$post['received_qty'][$i],
                'balance_qty'   => (float)$post['balance_qty'][$i],
                'supplier_id'   => $post['supplier_id'][$i],
                'supplier_name' => $post['supplier'][$i] ?? '',
                'price'         => (float)$post['product_price'][$i],
                'description'   => $post['product_description'][$i],
                'amount'        => ((float)$post['received_qty'][$i] * (float)$post['product_price'][$i])
            );
            $this->quote->insert_item($items);
        }
		
		$urls= base_url('billing/viewreceiveditem/'.$master_id);

        // ===== WhatsApp API Call (AiSensy) =====
        $apiUrl = "https://backend.aisensy.com/campaign/t1/api/v2";
        $payload = array(
            "apiKey" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY",
            "campaignName" => "receive item send storekeeper",
            "destination" => $employeephone, // dynamic employee phone
            "userName" => $post['employee_name'] ?? 'User',
            "templateParams" => array(
                $urls
            ),
            "source" => "new-landing-page form",
            "media" => new stdClass(),
            "buttons" => array(),
            "carouselCards" => array(),
            "location" => new stdClass(),
            "attributes" => new stdClass(),
            "paramsFallbackValue" => array(
                "FirstName" => $urls
            )
        );

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        curl_close($ch);

        // Optional: log the API response
        log_message('info', 'AiSensy WhatsApp API Response: ' . $response);

        echo json_encode([
            'status'  => 'Success',
            'message' => 'Received item saved & WhatsApp sent successfully'
        ]);
    } else {
        echo json_encode([
            'status'  => 'Error',
            'message' => $this->lang->line('ERROR')
        ]);
    }
}


    // edit
    public function receiveditemedit($id){
        $data['master'] = $this->quote->get_master($id);
        $data['items']  = $this->quote->get_items($id);
		 $head['title'] = "Manage Purchase received";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('indent/receiveditemedit',$data);
		 $this->load->view('fixed/footer');
    }

    // update
    public function receiveditemupdate($id){
        $post = $this->input->post();
        $master = array(
            'employee_name' => $post['cst'],
            'order_no'      => $post['invocieno'],
            'reference'     => $post['refer'],
            'order_date'    => date('Y-m-d',strtotime($post['invoicedate'])),
            'due_date'      => date('Y-m-d',strtotime($post['invocieduedate'])),
        );
        $this->quote->update_master($id,$master);

        // delete old items then insert new
        $this->quote->delete_items($id);

        $count = count($post['product_name']);
        for($i=0;$i<$count;$i++){
            $items = array(
                'master_id'     => $id,
                'product_name'  => $post['product_name'][$i],
                'required_qty'  => $post['required_qty'][$i],
                'unit'          => $post['product_unit'][$i],
                'received_qty'  => $post['received_qty'][$i],
                'balance_qty'   => $post['balance_qty'][$i],
                'supplier_id'   => $post['supplier_id'][$i],
                'supplier_name' => $post['supplier'][$i] ?? '',
                'price'         => $post['product_price'][$i],
                'description'   => $post['product_description'][$i],
                'amount'        => ($post['received_qty'][$i] * $post['product_price'][$i])
            );
            $this->quote->insert_item($items);
        }

        redirect('indent');
    }

    public function receiveditemdelete($id){
        $this->quote->delete_master($id);
        redirect('indent');
    }





}