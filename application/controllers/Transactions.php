<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transactions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        $this->load->library("ion_auth");
        $this->load->model('invoices_model');
		  $this->load->model('accounts_model', 'accounts');
        $this->load->model('transactions_model', 'transactions');
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $this->load->library("Custom");
        $this->li_a = 'accounts';
    }

     public function index()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $head['title'] = "Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/index');
        $this->load->view('fixed/footer');

    } 

                                 


 public function oldindex() {
        $data['accounts'] = $this->transactions->get_unique_accounts();
        $data['locations'] = $this->transactions->get_locations();
		 $head['title'] = "Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
		$this->load->view('fixed/header', $head);
        $this->load->view('transactions/report_view', $data);
		 $this->load->view('fixed/footer');
    }

    public function fetch_transactions() {
        $postData = $this->input->post();
        $data = $this->transactions->get_filtered_transactions($postData);
        echo json_encode($data);
    }

    public function add()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $data['dual'] = $this->custom->api_config(65);

        $data['cat'] = $this->transactions->categories();
        $data['accounts'] = $this->transactions->acc_list();
        $head['title'] = "Add Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/create', $data);
        $this->load->view('fixed/footer');

    }

    public function transfer()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }

        $data['cat'] = $this->transactions->categories();
        $data['accounts'] = $this->transactions->acc_list();
        $head['title'] = "New Transfer";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/transfer', $data);
        $this->load->view('fixed/footer');

    }

    public function payinvoice()
    {

        if (!$this->aauth->premission(1)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $amount2 = 0;
        $tid = $this->input->post('tid');
        $amount = rev_amountExchange_s($this->input->post('amount', true), 0, $this->aauth->get_user()->loc);
        $paydate = $this->input->post('paydate', true);
        $note = $this->input->post('shortnote', true);
        $pmethod = $this->input->post('pmethod', true);
        $acid = $this->input->post('account', true);
        $cid = $this->input->post('cid', true);
        $cname = $this->input->post('cname', true);
        $paydate = datefordatabase($paydate);

        $this->db->select('holder');
        $this->db->from('geopos_accounts');
        $this->db->where('id', $acid);
        $query = $this->db->get();
        $account = $query->row_array();

        if ($pmethod == 'Balance') {

            $customer = $this->transactions->check_balance($cid);
            if (rev_amountExchange_s($customer['balance'], 0, $this->aauth->get_user()->loc) >= $amount) {

                $this->db->set('balance', "balance-$amount", FALSE);
                $this->db->where('id', $cid);
                $this->db->update('users');
            } else {

                $amount = rev_amountExchange_s($customer['balance'], 0, $this->aauth->get_user()->loc);
                $this->db->set('balance', 0, FALSE);
                $this->db->where('id', $cid);
                $this->db->update('users');
            }
        }

        $data = array(
            'acid' => $acid,
            'account' => $account['holder'],
            'type' => 'Income',
            'cat' => 'Sales',
            'credit' => $amount,
            'payer' => $cname,
            'payerid' => $cid,
            'method' => $pmethod,
            'date' => $paydate,
            'created_at' => date('Y-m-d H:i:s'),
            'eid' => $this->aauth->get_user()->id,
            'tid' => $tid,
            'note' => $note,
            'loc' => $this->aauth->get_user()->loc
        );

        $this->db->insert('geopos_transactions', $data);
        $tttid = $this->db->insert_id();

        $this->db->select('total,pamnt');
        $this->db->from('orders');
        $this->db->where('id', $tid);
        $query = $this->db->get();
        $invresult = $query->row();

       $totalrm = $invresult->total - $invresult->pamnt;      

        if ($totalrm > $amount) {
            $this->db->set('payment_method', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);

            $this->db->set('status', 'partial');
            $this->db->set('paid_date', date('Y-m-d H:i:s'));
            $this->db->where('id', $tid);
            $this->db->update('orders');


            //account update
            $this->db->set('lastbal', "lastbal+$amount", FALSE);
            $this->db->where('id', $acid);
            $this->db->update('geopos_accounts');
            $paid_amount = $invresult->pamnt + $amount;
            $status = 'Partial';
            $totalrm = $totalrm - $amount;
        } else {
            if ($totalrm < $amount) {
                $diff = $totalrm - $amount;
                $diff = abs($diff);
                $amount2 = $amount;
                $amount = $totalrm;
                $this->db->set('balance', "balance+$diff", FALSE);
                $this->db->where('id', $cid);
                $this->db->update('users');
                $this->db->set('credit', "credit-$diff", FALSE);
                $this->db->where('id', $tttid);
                $this->db->update('geopos_transactions');

            }
            $this->db->set('payment_method', $pmethod);
            $this->db->set('pamnt', "pamnt+$totalrm", FALSE);
            $this->db->set('status', 'paid');
            $this->db->set('paid_date', date('Y-m-d H:i:s'));
            $this->db->where('id', $tid);
            $this->db->update('orders');
			

            //account update
            $this->db->set('lastbal', "lastbal+$totalrm", FALSE);
            $this->db->where('id', $acid);
            $this->db->update('geopos_accounts');
            $totalrm = 0;
            $status = 'Paid';
        }
        $amount += $amount2;

        $activitym = "<tr><td>" . '<a href="' . base_url('invoices') . '/view_payslip?id=' . $tttid . '&inv=' . $tid . '" class="btn btn-blue btn-sm"><span class="fa fa-print" aria-hidden="true"></span></a> ' . substr($paydate, 0, 10) . "</td><td>$pmethod</td><td>" . amountExchange_s($amount, 0, $this->aauth->get_user()->loc) . "</td><td>$note</td></tr>";
        $dual = $this->custom->api_config(65);
        if ($dual['key1']) {

            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $dual['key2']);
            $query = $this->db->get();
            $account = $query->row_array();

            $data['credit'] = 0;
            $data['debit'] = $amount;
            $data['type'] = 'Expense';
            $data['acid'] = $dual['key2'];
            $data['account'] = $account['holder'];
            $data['note'] = 'Debit ' . $data['note'];

            $this->db->insert('geopos_transactions', $data);

            //account update
            $this->db->set('lastbal', "lastbal-$amount", FALSE);
            $this->db->where('id', $dual['key2']);
            $this->db->update('geopos_accounts');
        }
        echo json_encode(array('status' => 'Success', 'message' =>
            $this->lang->line('Transaction has been added'), 'pstatus' => $this->lang->line($status), 'activity' => $activitym, 'amt' => $totalrm, 'ttlpaid' => amountExchange_s($amount, 0, $this->aauth->get_user()->loc)));

                $alert = $this->custom->api_config(66);
        if ($alert['key1'] == 1) {
            $this->load->model('communication_model');
            $subject = $cname . ' ' . $this->lang->line('Transaction has been');
            $body = $subject . '<br> ' . $this->lang->line('Credit') . ' ' . $this->lang->line('Amount') . ' ' . $amount . '<br> ' . $this->lang->line('Debit') . ' ' . $this->lang->line('Amount') . ' 0  <br> ID# ' . $tttid;
            $out = $this->communication_model->send_corn_email($alert['url'], $alert['url'], $subject, $body, false, '');
        }
    }

    public function paypurchase()
    {

        if (!$this->aauth->premission(2)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }

        $tid = $this->input->post('tid', true);
        $amount = $this->input->post('amount', true);
        $paydate = $this->input->post('paydate', true);
        $note = $this->input->post('shortnote', true);
        $pmethod = $this->input->post('pmethod', true);
        $acid = $this->input->post('account', true);
        $cid = $this->input->post('cid', true);
        $cname = $this->input->post('cname', true);
        $paydate = datefordatabase($paydate);
        $this->db->select('holder');
        $this->db->from('geopos_accounts');
        $this->db->where('id', $acid);
        $query = $this->db->get();
        $account = $query->row_array();
        $data = array(
            'acid' => $acid,
            'account' => $account['holder'],
            'type' => 'Expense',
            'cat' => 'Purchase',
            'debit' => $amount,
            'payer' => $cname,
            'payerid' => $cid,
            'method' => $pmethod,
            'date' => $paydate,
			'created_at' => date('Y-m-d H:i:s'),
            'eid' => $this->aauth->get_user()->id,
            'tid' => $tid,
            'note' => $note,
            'ext' => 1,
            'loc' => $this->aauth->get_user()->loc
        );
        $this->db->insert('geopos_transactions', $data);
        $this->db->insert_id();
        $this->db->select('total,csd,pamnt');
        $this->db->from('geopos_purchase');
        $this->db->where('id', $tid);
        $query = $this->db->get();
        $invresult = $query->row();
        $totalrm = $invresult->total - $invresult->pamnt;
        if ($totalrm > $amount) {
            $this->db->set('pmethod', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);
            $this->db->set('status', 'partial');
            $this->db->where('id', $tid);
            $this->db->update('geopos_purchase');
            //account update
            $this->db->set('lastbal', "lastbal-$amount", FALSE);
            $this->db->where('id', $acid);
            $this->db->update('geopos_accounts');
            $paid_amount = $invresult->pamnt + $amount;
            $status = 'Partial';
            $totalrm = $totalrm - $amount;
        } else {
            $this->db->set('pmethod', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);
            $this->db->set('status', 'paid');
            $this->db->where('id', $tid);
            $this->db->update('geopos_purchase');
            //acount update
            $this->db->set('lastbal', "lastbal-$amount", FALSE);
            $this->db->where('id', $acid);
            $this->db->update('geopos_accounts');
            $totalrm = 0;
            $status = 'Paid';
            $paid_amount = $amount;
        }

        $dual = $this->custom->api_config(65);
        if ($dual['key1']) {

            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $dual['url']);
            $query = $this->db->get();
            $account = $query->row_array();

            $data['debit'] = 0;
            $data['credit'] = $amount;
            $data['type'] = 'Income';
            $data['acid'] = $dual['url'];
            $data['account'] = $account['holder'];
            $data['note'] = 'Credit ' . $data['note'];

            $this->db->insert('geopos_transactions', $data);

            //account update
            $this->db->set('lastbal', "lastbal+$amount", FALSE);
            $this->db->where('id', $dual['url']);
            $this->db->update('geopos_accounts');
        }
        $activitym = "<tr><td>" . substr($paydate, 0, 10) . "</td><td>$pmethod</td><td>$amount</td><td>$note</td></tr>";


        echo json_encode(array('status' => 'Success', 'message' =>
            $this->lang->line('Transaction has been added'), 'pstatus' => $this->lang->line($status), 'activity' => $activitym, 'amt' => $totalrm, 'ttlpaid' => $paid_amount));
    }


    public function cancelinvoice()
    {
        if (!$this->aauth->premission(1)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }


        $tid = intval($this->input->post('tid'));


        $this->db->set('pamnt', "0.00", FALSE);
        $this->db->set('total', "0.00", FALSE);
        $this->db->set('items', 0);
        $this->db->set('status', 'canceled');
        $this->db->where('id', $tid);
        $this->db->update('geopos_invoices');
        //reverse
        $this->db->select('credit,debit,acid');
        $this->db->from('geopos_transactions');
        $this->db->where('tid', $tid);
        $query = $this->db->get();
        $revresult = $query->result_array();
        foreach ($revresult as $trans) {
            $amt = $trans['credit'] - $trans['debit'];
            $this->db->set('lastbal', "lastbal-$amt", FALSE);
            $this->db->where('id', $trans['acid']);
            $this->db->update('geopos_accounts');
        }
        $this->db->select('pid,qty');
        $this->db->from('geopos_invoice_items');
        $this->db->where('tid', $tid);
        $query = $this->db->get();
        $prevresult = $query->result_array();
        foreach ($prevresult as $prd) {
            $amt = $prd['qty'];
            $this->db->set('qty', "qty+$amt", FALSE);
            $this->db->where('pid', $prd['pid']);
            $this->db->update('geopos_products');
        }
        $this->db->delete('geopos_transactions', array('tid' => $tid));
        $data = array('type' => 9, 'rid' => $tid);
        $this->db->delete('geopos_metadata', $data);
        echo json_encode(array('status' => 'Success', 'message' =>
            $this->lang->line('Invoice canceled')));
    }


    public function cancelpurchase()
    {
        if (!$this->aauth->premission(2)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $tid = intval($this->input->post('tid'));
        $this->db->set('pamnt', "0.00", FALSE);
        $this->db->set('status', 'canceled');
        $this->db->where('id', $tid);
        $this->db->update('geopos_purchase');
        //reverse
        $this->db->select('debit,credit,acid');
        $this->db->from('geopos_transactions');
        $this->db->where('tid', $tid);
        $this->db->where('ext', 1);
        $query = $this->db->get();
        $revresult = $query->result_array();
        foreach ($revresult as $trans) {
            $amt = $trans['debit'] - $trans['credit'];
            $this->db->set('lastbal', "lastbal+$amt", FALSE);
            $this->db->where('id', $trans['acid']);
            $this->db->update('geopos_accounts');
        }
        $this->db->select('pid,qty');
        $this->db->from('geopos_purchase_items');
        $this->db->where('tid', $tid);
        $query = $this->db->get();
        $prevresult = $query->result_array();
        foreach ($prevresult as $prd) {
            $amt = $prd['qty'];
            $this->db->set('qty', "qty-$amt", FALSE);
            $this->db->where('pid', $prd['pid']);
            $this->db->update('geopos_products');
        }
        $this->db->delete('geopos_transactions', array('tid' => $tid, 'ext' => 1));
        echo json_encode(array('status' => 'Success', 'message' =>
            $this->lang->line('Purchase canceled!')));
    }

    public function translist()
    {
        if (!$this->aauth->premission(5)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $ttype = $this->input->get('type');
        $list = $this->transactions->get_datatables($ttype);
        $data = array();
        // $no = $_POST['start'];
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $pid = $prd->id;
         //   $row[] = dateformat($prd->date);
		 if ($prd->created_at !== '0000-00-00 00:00:00') {
    $row[] = date('d-m-Y h:i A', strtotime($prd->created_at));
} else {
    $row[] = dateformat($prd->date);
}
            $row[] = $prd->account;
            $row[] = $prd->note;
            $row[] = amountExchange($prd->debit, 0, $this->aauth->get_user()->loc);
            $row[] = amountExchange($prd->credit, 0, $this->aauth->get_user()->loc);
            $row[] = $prd->payer;
            $row[] = $this->lang->line($prd->method);
            $row[] = '<a href="' . base_url() . 'transactions/view?id=' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-eye"></span>  ' . $this->lang->line('View') . '</a> <a href="' . base_url() . 'transactions/print_t?id=' . $pid . '" class="btn btn-info btn-sm"  title="Print"><span class="fa fa-print"></span></a>&nbsp; &nbsp;<a  href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm delete-object"><span class="fa fa-trash"></span></a>';
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->transactions->count_all(),
            "recordsFiltered" => $this->transactions->count_filtered(),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    } 


/* 	public function dailypaytranslist()
    {
        if (!$this->aauth->premission(5)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $ttype = $this->input->get('type');
        $list = $this->transactions->get_dailypaydatatables($ttype);
        $data = array();
        // $no = $_POST['start'];
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $pid = $prd->id;
         //   $row[] = dateformat($prd->date);
		 if ($prd->created_at !== '0000-00-00 00:00:00') {
    $row[] = date('d-m-Y h:i A', strtotime($prd->created_at));
} else {
    $row[] = dateformat($prd->date);
}
            $row[] = $prd->payer;
            $row[] = amountExchange($prd->debit, 0, $this->aauth->get_user()->loc);
            $row[] = $prd->note;
          
            $row[] = '<a href="' . base_url() . 'transactions/view?id=' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-eye"></span>  ' . $this->lang->line('View') . '</a> <a href="' . base_url() . 'transactions/print_t?id=' . $pid . '" class="btn btn-info btn-sm"  title="Print"><span class="fa fa-print"></span></a>';
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->transactions->count_dailypayall(),
            "recordsFiltered" => $this->transactions->count_dailypayfiltered(),
            "data" => $data,
        );
      
        echo json_encode($output);
    } */


public function dailypaytranslist()
{
    if (!$this->aauth->premission(5)) {
        exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
    }

    $ttype = $this->input->get('type');
    $from_date = $this->input->post('from_date');
    $to_date = $this->input->post('to_date');

    $list = $this->transactions->get_dailypaydatatables($ttype, $from_date, $to_date);
    $data = [];
    $no = $this->input->post('start');

    $total_amount = 0;

    foreach ($list as $prd) {
        $no++;
        $row = [];

        $row[] = ($prd->created_at !== '0000-00-00 00:00:00') ? date('d-m-Y h:i A', strtotime($prd->created_at)) : dateformat($prd->date);
        $row[] = $prd->payer;
        $row[] = amountExchange($prd->debit, 0, $this->aauth->get_user()->loc);
        $row[] = $prd->note;
        $row[] = '<a href="' . base_url() . 'transactions/view?id=' . $prd->id . '" class="btn btn-primary btn-sm"><span class="fa fa-eye"></span> View</a>';

        $total_amount += (float) $prd->debit;

        $data[] = $row;
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->transactions->count_dailypayall($from_date, $to_date),
        "recordsFiltered" => $this->transactions->count_dailypayfiltered($from_date, $to_date),
        "data" => $data,
        "total_amount" => $total_amount
    );

    echo json_encode($output);
}


    // Category
    public function categories()
    {
        $this->li_a = 'misc_settings';
        /* if ($this->aauth->get_user()->roleid < 5) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        } */

        $data['catlist'] = $this->transactions->categories();
        $head['title'] = "Category";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/cat', $data);
        $this->load->view('fixed/footer');
    }

    public function createcat()
    {
       /*  if ($this->aauth->get_user()->roleid < 5) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        } */

        $head['title'] = "Category";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/cat_create');
        $this->load->view('fixed/footer');
    }

    public function editcat()
    {

       /*  if ($this->aauth->get_user()->roleid < 5) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        } */

        $head['title'] = "Category";
        $head['usernm'] = $this->aauth->get_user()->username;

        $id = $this->input->get('id');

        $data['cat'] = $this->transactions->cat_details($id);

        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/trans-cat-edit', $data);
        $this->load->view('fixed/footer');

    }

    public function save_createcat()
    {

      /*   if ($this->aauth->get_user()->roleid < 5) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        } */

        $name = $this->input->post('catname');
        $parentcategory = $this->input->post('parent_category');

        if ($this->transactions->addcat($name, $parentcategory)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function editcatsave()
    {
       /*  if ($this->aauth->get_user()->roleid < 5) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        } */

        $id = $this->input->post('catid');
        $name = $this->input->post('cat_name');
         $parentcategory = $this->input->post('parent_category');

        if ($this->transactions->cat_update($id, $name, $parentcategory)) {

            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));

        } else {

            echo json_encode(array('status' => 'Error', 'message' =>
                'Error!'));
        }


    }

    public function delete_cat()
    {
       /*  if ($this->aauth->get_user()->roleid < 5) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
 */
        $id = $this->input->post('deleteid');
        if ($id) {
            $this->db->delete('geopos_trans_cat', array('id' => $id));
            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => 'Error!'));
        }
    }

 /*  public function save_trans()
{
    if (!$this->aauth->premission(5)) {
        exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
    }

    $dual = $this->custom->api_config(65);

    $pay_acc = $this->input->post('pay_acc', true);
    $payer_ids = $this->input->post('payer_id', true);
    $payer_types = $this->input->post('ty_p', true);
    $payer_names = $this->input->post('payer_name', true);
    $amounts = $this->input->post('amount', true);
    $pay_types = $this->input->post('pay_type', true);
    $pay_cats = $this->input->post('pay_cat', true);
    $paymethod = $this->input->post('paymethod', true);
    $note = $this->input->post('note', true);
    $date = $this->input->post('date', true);

    $date = datefordatabase($date);

    // Loop through the transactions and process them
    $results = [];
    foreach ($payer_ids as $key => $payer_id) {
        $payer_name = isset($payer_names[$key]) ? $payer_names[$key] : '';
        $payer_ty = isset($payer_types[$key]) ? $payer_types[$key] : '';
        $amount = isset($amounts[$key]) ? numberClean($amounts[$key]) : 0;
        $pay_type = isset($pay_types[$key]) ? $pay_types[$key] : '';
        $pay_cat = isset($pay_cats[$key]) ? $pay_cats[$key] : '';

        $credit = 0;
        $debit = 0;

        if ($pay_type == 'Income') {
            $credit = $amount;
        } elseif ($pay_type == 'Expense') {
            $debit = $amount;
        }
		
		
		

        if ($amount > 0) {
            $success = $this->transactions->addmytrans(
                $payer_id,
                $payer_name,
                $pay_acc,
                $date,
                $debit,
                $credit,
                $pay_type,
                $pay_cat,
                $paymethod,
                $note,
                $this->aauth->get_user()->id,
                $this->aauth->get_user()->loc,
                $payer_ty
            );

            if ($success) {
                $lid = $this->db->insert_id();
                $results[] = [
                    'status' => 'Success',
                    'message' => $this->lang->line('Transaction has been') . "  <a href='" . base_url() . "transactions/add' class='btn btn-blue '><span class='fa fa-plus-circle' aria-hidden='true'></span> " . $this->lang->line('New') . "  </a> <a href='" . base_url() . 'transactions/view?id=' . $lid . "' class='btn btn-primary btn-xs'><span class='fa fa-eye'></span>  " . $this->lang->line('View') . "</a> <a href='" . base_url() . "transactions' class='btn btn-pink '><span class='fa fa-list-alt aria-hidden='true'></span></a>"
                ];
            } else {
                $results[] = ['status' => 'Error', 'message' => 'Error inserting transaction.'];
            } 
        } else {
            $results[] = ['status' => 'Error', 'message' => 'Amount must be greater than zero.'];
        }
    }

   
    echo json_encode($results);

    $alert = $this->custom->api_config(66);
    if ($alert['key1'] == 1) {
        $this->load->model('communication_model');
        foreach ($results as $result) {
            if ($result['status'] == 'Success') {
                $subject = $result['message'];
                $body = $subject . '<br> ' . $this->lang->line('Credit') . ' ' . $credit . '<br> ' . $this->lang->line('Debit') . ' ' . $debit;
                $this->communication_model->send_corn_email($alert['url'], $alert['url'], $subject, $body, false, '');
            }
        }
    }
} */



public function save_trans()
{
    if (!$this->aauth->premission(5)) {
        exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
    }

    $dual = $this->custom->api_config(65);

    $pay_acc = $this->input->post('pay_acc', true);
    $payer_ids = $this->input->post('payer_id', true);
    $payer_types = $this->input->post('ty_p', true);
    $payer_names = $this->input->post('payer_names', true);
    $amounts = $this->input->post('amount', true);
    $pay_types = $this->input->post('pay_type', true);
    $pay_cats = $this->input->post('pay_cat', true);
    $paymethod = $this->input->post('paymethod', true);
    $note = $this->input->post('notes', true);
    //$date = $this->input->post('date', true);
	$date = $this->input->post('date', true) ?: date('Y-m-d H:i:s');
    $receivertype = $this->input->post('receiver_type', true);
 
    $date = datefordatabase($date);
    foreach ($payer_ids as $key => $payer_id) {
        $payer_name = isset($payer_names[$key]) ? $payer_names[$key] : '';
        $payer_ty = isset($payer_types[$key]) ? $payer_types[$key] : '';
        $amount = isset($amounts[$key]) ? numberClean($amounts[$key]) : 0;
        $pay_type = isset($pay_types[$key]) ? $pay_types[$key] : '';
        $pay_cat = isset($pay_cats[$key]) ? $pay_cats[$key] : '';
        $receivertypes = isset($receivertype[$key]) ? $receivertype[$key] : '';
        $notes = isset($note[$key]) ? $note[$key] : '';

        $credit = 0;
        $debit = 0;

        if ($pay_type == 'Income') {
            $credit = $amount;
        } elseif ($pay_type == 'Expense') {
            $debit = $amount;
        }

        if ($amount > 0) {
            $success = $this->transactions->addmytrans(
                $payer_id,
                $payer_name,
                $pay_acc,
                $date,
                $debit,
                $credit,
                $pay_type,
                $pay_cat,
                $paymethod,
                $notes,
                $this->aauth->get_user()->id,
                $this->aauth->get_user()->loc,
                $payer_ty,
				$receivertypes
            );

            if ($success) {
                $lid = $this->db->insert_id();
                // Return a single success response
               /*  echo json_encode([
                    [
                        'status' => 'Success',
                        'message' => sprintf(
                            "The transaction has been successfully added! <a href='%stransactions/add' class='btn btn-blue '><span class='fa fa-plus-circle' aria-hidden='true'></span> New </a> <a href='%stransactions/view?id=%d' class='btn btn-primary btn-xs'><span class='fa fa-eye'></span> View</a> <a href='%stransactions' class='btn btn-pink '><span class='fa fa-list-alt' aria-hidden='true'></span></a>",
                            base_url(),
                            base_url(),
                            $lid,
                            base_url()
                        )
                    ]
                ]); */
				
				 echo json_encode(array('status' => 'Success', 'message' =>
                    $this->lang->line('Transaction has been') . "  <a href='" . base_url() . "transactions/add' class='btn btn-blue '><span class='fa fa-plus-circle' aria-hidden='true'></span> " . $this->lang->line('New') . "  </a> <a href='" . base_url() . 'transactions/view?id=' . $lid . "' class='btn btn-primary btn-xs'><span class='fa fa-eye'></span>  " . $this->lang->line('View') . "</a> <a href='" . base_url() . "transactions' class='btn btn-pink '><span class='fa fa-list-alt aria-hidden='true'></span></a>"));
                return; // Stop processing further transactions
            }
        } else {
            echo json_encode([
                ['status' => 'Error', 'message' => 'Amount must be greater than zero.']
            ]);
            return; // Stop processing further transactions
        }
    }

    // If no success was achieved, return an error
    echo json_encode([
        ['status' => 'Error', 'message' => 'No valid transaction was processed.']
    ]);
}


    public function save_transfer()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }

        $pay_acc = $this->input->post('pay_acc');
        $pay_acc2 = $this->input->post('pay_acc2');
        $amount = (float)$this->input->post('amount', true);

        if ($amount > 0) {
            if ($this->transactions->addtransfer($pay_acc, $pay_acc2, $amount, $this->aauth->get_user()->id, $this->aauth->get_user()->loc)) {
                echo json_encode(array('status' => 'Success', 'message' =>
                    "Transfer has been successfully done! <a href='" . base_url() . "transactions/transfer' class='btn btn-indigo btn-sm'><span class='icon-plus-circle' aria-hidden='true'></span> " . $this->lang->line('New') . "  </a> <a href='" . base_url() . "accounts' class='btn btn-indigo btn-sm'><span class='icon-list-ul' aria-hidden='true'></span></a>"));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                'Error!'));
        }


    }


    public function delete_i()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }

        $id = $this->input->post('deleteid');
        if ($id) {


            echo json_encode($this->transactions->delt($id));
            $alert = $this->custom->api_config(66);

        } else {
            echo json_encode(array('status' => 'Error', 'message' => 'Error!'));
        }
    }

    public function income()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $head['title'] = "Income Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/income');
        $this->load->view('fixed/footer');

    } 


	public function dailypayment()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $head['title'] = "Daily Payment Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/dailypayment');
        $this->load->view('fixed/footer');

    }

    public function expense()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $head['title'] = "Expense Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/expense');
        $this->load->view('fixed/footer');

    }

    public function view()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $head['title'] = "View Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
		$data['accounts'] = $this->accounts->accountslist();
			  
       
        $this->load->view('fixed/header', $head);
      
			$this->load->view('transactions/view', $data);
        $this->load->view('fixed/footer');

    }



    public function viewstatement()
    {
        $account_id = $this->input->post('trans_type');
        $sdate = $this->input->post('sdate');
        $edate = $this->input->post('edate');
         $head['title'] = "View Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['title'] = 'Expense Statement Report';
        $data['records'] = $this->transactions->get_expense_statement($account_id, $sdate, $edate);
        $data['sdate'] = $sdate;
        $data['edate'] = $edate;
        $data['account_id'] = $account_id;
        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/statement_view', $data);
		 $this->load->view('fixed/footer');
    }


    public function export_pdf($account_id, $sdate, $edate)
    {
        $data['records'] = $this->transactions->get_expense_statement($account_id, $sdate, $edate);
        $data['title'] = 'Expense Statement PDF';
        $data['sdate'] = $sdate;
        $data['edate'] = $edate;

        $html = $this->load->view('transactions/pdf_view', $data, TRUE);

        // PDF generation
        $this->load->library('pdf');
        $this->pdf->loadHtml($html);
        $this->pdf->render();
        $this->pdf->stream("expense_statement.pdf", array("Attachment" => 1));
    }


    public function export_excel($account_id, $sdate, $edate)
    {
        $records = $this->transactions->get_expense_statement($account_id, $sdate, $edate);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=expense_statement.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "Date\tAccount\tPayer\tMethod\tNote\tDebit\n";
        foreach ($records as $row) {
            echo "{$row['date']}\t{$row['account']}\t{$row['payer']}\t{$row['method']}\t{$row['note']}\t{$row['debit']}\n";
        }
    }


	
    public function print_t()
    {
        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $head['title'] = "View Transaction";
        $head['usernm'] = $this->aauth->get_user()->username;
        $id = $this->input->get('id');
        $data['trans'] = $this->transactions->view($id);
        if ($data['trans']['payerid'] > 0) {
            $data['cdata'] = $this->transactions->cview($data['trans']['payerid'], $data['trans']['ext']);
        } else {
            $data['cdata'] = array('address' => 'Not Registered', 'city' => '', 'phone' => '', 'email' => '');
        }


        ini_set('memory_limit', '64M');

        $html = $this->load->view('transactions/view-print', $data, true);

        //PDF Rendering
        $this->load->library('pdf');

        $pdf = $this->pdf->load_en();

        $pdf->SetHTMLFooter('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;"><tr><td width="33%"></td><td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td><td width="33%" style="text-align: right; ">#' . $id . '</td></tr></table>');

        if ($data['trans']['id']) $pdf->WriteHTML($html);

        if ($this->input->get('d')) {

            $pdf->Output('Trans_#' . $id . '.pdf', 'D');
        } else {
            $pdf->Output('Trans_#' . $id . '.pdf', 'I');
        }


    }


}