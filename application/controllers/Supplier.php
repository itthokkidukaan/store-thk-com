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

defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('supplier_model', 'supplier');
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(2)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->li_a = 'stock';
    }

    public function index()
    {

        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/clist');
        $this->load->view('fixed/footer');
    }

    public function create()
    {
        $data['customergrouplist'] = $this->supplier->group_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Create Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/create', $data);
        $this->load->view('fixed/footer');
    }

    public function view()
    {
        $custid = $this->input->get('id');
        $data['details'] = $this->supplier->details($custid);
        $data['customergroup'] = $this->supplier->group_info($data['details']['gid']);
        $data['money'] = $this->supplier->money_details($custid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier';
        $this->load->view('fixed/header', $head);
        if ($data['details']['id']) $this->load->view('supplier/view', $data);
        $this->load->view('fixed/footer');
    }

    public function load_list()
    {
        $list = $this->supplier->get_datatables();
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $customers) {
            $no++;

            $row = array();
            $row[] = $no;
            $row[] = '<a href="supplier/view?id=' . $customers->id . '">' . $customers->name . '</a>';
            $row[] = $customers->address . ',' . $customers->city . ',' . $customers->country;
            $row[] = $customers->email;
            $row[] = $customers->phone;
            $row[] = '<a href="supplier/view?id=' . $customers->id . '" class="btn btn-info btn-sm"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> <a href="supplier/edit?id=' . $customers->id . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a> <a href="#" data-object-id="' . $customers->id . '" class="btn btn-danger btn-sm delete-object"><span class="fa fa-trash"></span></a>';


            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier->count_all(),
            "recordsFiltered" => $this->supplier->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }

    //edit section
    public function edit()
    {
        $pid = $this->input->get('id');

        $data['customer'] = $this->supplier->details($pid);
        $data['customergroup'] = $this->supplier->group_info($pid);
        $data['customergrouplist'] = $this->supplier->group_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Edit Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/edit', $data);
        $this->load->view('fixed/footer');

    }

    public function addsupplier()
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

        $this->supplier->add($name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $taxid);

    }

    public function editsupplier()
    {
        $id = $this->input->post('id', true);
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

        if ($id) {
            $this->supplier->edit($id, $name, $company, $phone, $email, $address, $city, $region, $country, $postbox, $taxid);
        }
    }


    public function delete_i()
    {
        $id = $this->input->post('deleteid');

        if ($this->supplier->delete($id)) {
            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
        }
    }

    public function displaypic()
    {
        $id = $this->input->get('id');
        $this->load->library("uploadhandler", array(
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i', 'upload_dir' => FCPATH . 'userfiles/customers/'
        ));
        $img = (string)$this->uploadhandler->filenaam();
        if ($img != '') {
            $this->supplier->editpicture($id, $img);
        }


    }


    public function translist()
    {
        $cid = $this->input->post('cid');
        $list = $this->supplier->trans_table($cid);
        $data = array();
        // $no = $_POST['start'];
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $pid = $prd->id;
            $row[] = $prd->date;
            $row[] = amountExchange($prd->debit, 0, $this->aauth->get_user()->loc);
            $row[] = amountExchange($prd->credit, 0, $this->aauth->get_user()->loc);	
			$row[] = amountExchange(0, 0, $this->aauth->get_user()->loc);
            $row[] = $prd->account;
            $row[] = $prd->payer;
            $row[] = $this->lang->line($prd->method);

            $row[] = '<a href="' . base_url() . 'transactions/view?id=' . $pid . '" class="btn btn-primary btn-xs"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-xs delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier->trans_count_all($cid),
            "recordsFiltered" => $this->supplier->trans_count_filtered($cid),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }
	
	
/* 	 public function ledger_view()
    {
	
$supplier_id = (int) $this->input->get('id');


$query = $this->db->query("
    SELECT invoicedate AS date, 'Purchase' AS type, 0.00 AS debit, total AS credit, NULL AS account, NULL AS method, refer AS reference, notes AS note, id as invoice
    FROM geopos_purchase
    WHERE csd = $supplier_id AND is_deleted = 0

    UNION ALL

    SELECT date, 'Payment' AS type, debit, 0.00 AS credit, account, method, CONCAT('TID#', tid) AS reference, note, '' as invoice
    FROM geopos_transactions
    WHERE payerid = $supplier_id 

    ORDER BY date
");

$data['ledger'] = $query->result_array();

        $data['details'] = $this->supplier->details($supplier_id);
        $data['money'] = $this->supplier->money_details($supplier_id);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier';
        $this->load->view('fixed/header', $head);
       $this->load->view('supplier/ledger_view', $data);
        $this->load->view('fixed/footer');


	} */
	
	
/* 	public function ledger_view()
{
    $supplier_id = (int) $this->input->get('id');
	
	if(!empty($this->input->get('from'))){
		 $from_date = $this->input->get('from');
    $to_date = $this->input->get('to');
	}else{
		
		$from_date = date('Y-04-01');
    $to_date = date('Y-m-d');
		
	}
   

  
  
    $purchase_where = "csd = $supplier_id AND is_deleted = 0";
    $payment_where = "payerid = $supplier_id";

    if (!empty($from_date)) {
        $purchase_where .= " AND invoicedate >= " . $this->db->escape($from_date);
        $payment_where .= " AND date >= " . $this->db->escape($from_date);
    }

    if (!empty($to_date)) {
        $purchase_where .= " AND invoicedate <= " . $this->db->escape($to_date);
        $payment_where .= " AND date <= " . $this->db->escape($to_date);
    }

    $query = $this->db->query("
        SELECT invoicedate AS date, 'Purchase' AS type, 0.00 AS debit, total AS credit, NULL AS account, NULL AS method, CONCAT('POS#', tid) AS reference, notes AS note, id as invoice,'' as customnote
        FROM geopos_purchase
        WHERE $purchase_where

        UNION ALL

        SELECT date, 'Payment' AS type, debit, 0.00 AS credit, account, method, CONCAT('POS#', tid) AS reference, note, '' as invoice, customnote
        FROM geopos_transactions
        WHERE $payment_where

        ORDER BY date
    ");
	
	

    $data['ledger'] = $query->result_array();
    $data['details'] = $this->supplier->details($supplier_id);
    $data['money'] = $this->supplier->money_details($supplier_id);
    $data['from'] = $from_date;
    $data['to'] = $to_date;

    $head['usernm'] = $this->aauth->get_user()->username;
    $head['title'] = 'View Supplier';
    $this->load->view('fixed/header', $head);
    $this->load->view('supplier/ledger_view', $data);
    $this->load->view('fixed/footer');
} */


public function ledger_view()
{
    $supplier_id = (int) $this->input->get('id');
	
    // Date filters
    if (!empty($this->input->get('from'))) {
        $from_date = $this->input->get('from');
        $to_date = $this->input->get('to');
    } else {
        $from_date = date('Y-04-01');
        $to_date = date('Y-m-d');
    }

    // Where conditions
    $purchase_where = "csd = $supplier_id AND is_deleted = 0";
    $payment_where = "payerid = $supplier_id";
    $stockr_where = "csd = $supplier_id AND status = 'accepted'";

    if (!empty($from_date)) {
        $purchase_where .= " AND invoicedate >= " . $this->db->escape($from_date);
        $payment_where .= " AND date >= " . $this->db->escape($from_date);
        $stockr_where .= " AND invoicedate >= " . $this->db->escape($from_date);
    }

    if (!empty($to_date)) {
        $purchase_where .= " AND invoicedate <= " . $this->db->escape($to_date);
        $payment_where .= " AND date <= " . $this->db->escape($to_date);
        $stockr_where .= " AND invoicedate <= " . $this->db->escape($to_date);
    }

    $query = $this->db->query("
        SELECT invoicedate AS date, 
               'Purchase' AS type, 
               0.00 AS debit, 
               total AS credit, 
               NULL AS account, 
               NULL AS method, 
               CONCAT('POS#', tid) AS reference, 
               notes AS note, 
               id AS invoice,
               '' AS customnote
        FROM geopos_purchase
        WHERE $purchase_where

        UNION ALL

        SELECT date, 
               'Payment' AS type, 
               debit, 
               0.00 AS credit, 
               account, 
               method, 
               CONCAT('POS#', tid) AS reference, 
               note, 
               '' AS invoice,
               customnote
        FROM geopos_transactions
        WHERE $payment_where

       

        ORDER BY date
    ");

    // Prepare data for view
    $data['ledger'] = $query->result_array();
    $data['details'] = $this->supplier->details($supplier_id);
    $data['money'] = $this->supplier->money_details($supplier_id);
    $data['from'] = $from_date;
    $data['to'] = $to_date;

    // View headers
    $head['usernm'] = $this->aauth->get_user()->username;
    $head['title'] = 'View Supplier Ledger';
    $this->load->view('fixed/header', $head);
    $this->load->view('supplier/ledger_view', $data);
    $this->load->view('fixed/footer');
}


    public function inv_list()
    {
        $cid = $this->input->post('cid');
        $list = $this->supplier->inv_datatables($cid);
        $data = array();

        $no = $this->input->post('start');

        foreach ($list as $invoices) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $invoices->tid;

            $row[] = $invoices->invoicedate;
            $row[] = amountExchange($invoices->total, 0, $this->aauth->get_user()->loc);
           $row[] = round($invoices->total-$invoices->pamnt,2);
            $row[] = '<span class="st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';
            $row[] = '<a href="' . base_url("purchase/view?id=$invoices->id") . '" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> ' . $this->lang->line('View') . '</a> &nbsp; <a href="' . base_url("purchase/printinvoice?id=$invoices->id") . '&d=1" class="btn btn-info btn-xs"  title="Download"><span class="fa fa-download"></span></a>&nbsp; &nbsp;<a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-xs delete-object"><span class="fa fa-trash"></span></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->supplier->inv_count_all($cid),
            "recordsFiltered" => $this->supplier->inv_count_filtered($cid),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);

    }


    public function transactions()
    {
        $custid = $this->input->get('id');
        $data['details'] = $this->supplier->details($custid);
        $data['money'] = $this->supplier->money_details($custid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/transactions', $data);
        $this->load->view('fixed/footer');
    }

    public function invoices()
    {
        $custid = $this->input->get('id');
        $data['details'] = $this->supplier->details($custid);

        $data['money'] = $this->supplier->money_details($custid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Supplier Invoices';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/invoices', $data);
        $this->load->view('fixed/footer');
    }

    public function bulkpayment()
    {
        if (!$this->aauth->premission(8)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $data['id'] = $this->input->get('id');
        $data['details'] = $this->supplier->details($data['id']);
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->model('accounts_model');
        $data['acclist'] = $this->accounts_model->accountslist((integer)$this->aauth->get_user()->loc);
        $this->session->set_userdata("cid", $data['id']);
        $head['title'] = 'Bulk Payment Invoices';
        $this->load->view('fixed/header', $head);
        $this->load->view('supplier/bulkpayment', $data);
        $this->load->view('fixed/footer');
    }

    public function bulk_post()
    {
        if (!$this->aauth->premission(8)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $csd = $this->input->post('customer', true);
        $sdate = datefordatabase($this->input->post('sdate'));
        $edate = datefordatabase($this->input->post('edate'));
        $trans_type = $this->input->post('trans_type', true);
		
		if($trans_type=='all'){
        $data['details'] = $this->supplier->sales_due_both($sdate, $edate, $csd, $trans_type);
		}else{
			
			 $data['details'] = $this->supplier->sales_due($sdate, $edate, $csd, $trans_type);
		}
        $due = $data['details']['total'] - $data['details']['pamnt'];
        echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Calculated') . ' ' . amountExchange($due), 'due' => amountExchange_s($due)));
    }

    public function bulk_post_payment()
    {
        if (!$this->aauth->premission(8)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $csd = $this->input->post('customer', true);
        $account = $this->input->post('account', true);
        $pay_method = $this->input->post('pmethod', true);
        $amount = numberClean($this->input->post('amount', true));
        $sdate = datefordatabase($this->input->post('sdate_2'));
        $edate = datefordatabase($this->input->post('edate_2'));

        $trans_type = $this->input->post('trans_type_2', true);
        $note = $this->input->post('note', true);
		
		
       // $data['details'] = $this->supplier->sales_due($sdate, $edate, $csd, $trans_type, false, $amount, $account, $pay_method, $note);


if($trans_type=='all'){
        $data['details'] = $this->supplier->sales_due_both($sdate, $edate, $csd, $trans_type, false, $amount, $account, $pay_method, $note);
		}else{
			
			 $data['details'] = $this->supplier->sales_due($sdate, $edate, $csd, $trans_type, false, $amount, $account, $pay_method, $note);
		}
		
        $due = 0;
        echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Paid') . ' ' . amountExchange($amount), 'due' => amountExchange_s($due)));
    }




public function get_products_by_category()
{
    $category_id = $this->input->post('category_id');
    $supplier_id = $this->input->post('supplier_id');

    $this->db->select('p.id, p.name');
    $this->db->from('products p');
    if ($category_id != 'all') {
        $this->db->where('p.category_id', $category_id);
    }

    $products = $this->db->get()->result();

    // Fetch allowed product IDs
    $allowed = $this->db->select('product_id')
        ->from('supplier_allowed_products')
        ->where('supplier_id', $supplier_id)
        ->get()->result_array();

    $allowed_ids = array_column($allowed, 'product_id');

    echo json_encode(['products' => $products, 'allowed' => $allowed_ids]);
}

public function get_product_categories()
{
    $this->db->select('c.id, c.name');
    $this->db->from('categories c');
    $this->db->join('products p', 'p.category_id = c.id');
    $this->db->group_by('c.id');
    $categories = $this->db->get()->result();

    echo json_encode($categories);
}

public function assign_products()
{
    $supplier_id = $this->input->post('supplier_id');
    $product_ids = $this->input->post('product_ids');

    // Remove existing assignments
    $this->db->where('supplier_id', $supplier_id);
    $this->db->delete('supplier_allowed_products');

    // Insert new assignments
    if (!empty($product_ids)) {
        $data = [];
        foreach ($product_ids as $pid) {
            $data[] = ['supplier_id' => $supplier_id, 'product_id' => $pid];
        }
        $this->db->insert_batch('supplier_allowed_products', $data);
    }

    echo json_encode(['status' => 'success']);
}

public function remove_supplier_product()
{
    $supplier_id = $this->input->post('supplier_id');
    $product_id = $this->input->post('product_id');

    $this->db->where(['supplier_id' => $supplier_id, 'product_id' => $product_id])->delete('supplier_allowed_products');

    echo json_encode(['status' => 'removed']);
}

}