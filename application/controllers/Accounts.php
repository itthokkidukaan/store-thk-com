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

class Accounts extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        if (!$this->aauth->premission(5)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->load->model('accounts_model', 'accounts');
        $this->li_a = 'accounts';
    }

    public function index()
    {
        $data['accounts'] = $this->accounts->accountslist();
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Accounts';
        $this->load->view('fixed/header', $head);
        $this->load->view('accounts/list', $data);
        $this->load->view('fixed/footer');
    }

    public function view()
    {
        $acid = $this->input->get('id');
        $data['account'] = $this->accounts->details($acid);
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'View Account';
        $this->load->view('fixed/header', $head);
        $this->load->view('accounts/view', $data);
        $this->load->view('fixed/footer');
    }

    public function add()
    {
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->locations_list2();
        $data['categorylist'] = $this->accounts->category_list();
        $head['title'] = 'Add Account';
        $this->load->view('fixed/header', $head);
        $this->load->view('accounts/add', $data);
        $this->load->view('fixed/footer');
    }

    public function addacc()
    {
        $accno = $this->input->post('accno');
        $holder = $this->input->post('holder');
        $intbal = numberClean($this->input->post('intbal'));
        $acode = $this->input->post('acode');
        $lid = $this->input->post('lid');		        if($this->input->post('employeeID')){						$employeeID = $this->input->post('employeeID');		}else{						$employeeID = 0;		}
        $account_type = $this->input->post('account_type');
        $category = $this->input->post('category');

        if ($this->aauth->get_user()->loc) {
            $lid = $this->aauth->get_user()->loc;
        }

        if ($accno) {
            $this->accounts->addnew($accno, $holder, $intbal, $acode, $lid, $account_type, $employeeID, $category);

        }
    }

    public function delete_i()
    {
        $id = $this->input->post('deleteid');
        if ($id) {
            $whr = array('id' => $id);
            if ($this->aauth->get_user()->loc) {
                $whr['loc'] = $this->aauth->get_user()->loc;
            }
            if (function_exists('is_seller_user') && is_seller_user()) {
                $whr['eid'] = (int)$this->session->userdata('user_id');
            }
            $this->db->delete('geopos_accounts', $whr);
            echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('ACC_DELETED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
        }
    }

//view for edit
    public function edit()
    {
        $catid = $this->input->get('id');
        $this->db->select('*');
        $this->db->from('geopos_accounts');
        $this->db->where('id', $catid);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }
        if (function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
        $query = $this->db->get();
        $data['account'] = $query->row_array();
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->locations_list();
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = 'Edit Account';

        $this->load->view('fixed/header', $head);
        $this->load->view('accounts/edit', $data);
        $this->load->view('fixed/footer');

    }

    public function editacc()
    {
        $acid = $this->input->post('acid');
        $accno = $this->input->post('accno');
        $holder = $this->input->post('holder');
        $acode = $this->input->post('acode');
        $lid = $this->input->post('lid');
        $equity = numberClean($this->input->post('balance'));

        if ($this->aauth->get_user()->loc) {
            $lid = $this->aauth->get_user()->loc;
        }
        if ($acid) {
            $this->accounts->edit($acid, $accno, $holder, $acode, $lid, $equity);
        }
    }

   /*  public function balancesheet()
    {


        $head['title'] = "Balance Summary";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['accounts'] = $this->accounts->accountslist();

        $this->load->view('fixed/header', $head);
        $this->load->view('transactions/balance', $data);
        $this->load->view('fixed/footer');

    } */
	
public function balancesheet()
{
    $head['title'] = "Balance Summary";
    $head['usernm'] = $this->aauth->get_user()->username;
    $data['accounts'] = $this->accounts->accountslist();

    $is_seller = function_exists('is_seller_user') && is_seller_user();
    $seller_id = (int)$this->session->userdata('user_id');

    // ---------- Suppliers (LiabilitiesAccounts) ----------
    $this->db->select('geopos_supplier.id, geopos_supplier.name,
                       COALESCE(SUM(geopos_purchase.total - geopos_purchase.pamnt),0) as balance');
    $this->db->from('geopos_supplier');
    $this->db->join('geopos_purchase', 'geopos_supplier.id = geopos_purchase.csd', 'left');
    $this->db->where('geopos_purchase.status !=', 'canceled');
    if ($is_seller) {
        $this->db->where('geopos_purchase.eid', $seller_id);
    }
    $this->db->group_by('geopos_supplier.id');
    $data['suppliers'] = $this->db->get()->result_array();

    // ✅ Reset query builder before next query
    $this->db->reset_query();

    // ---------- Customers (IncomeAccounts) ----------
    // Real income received per customer, from the same transaction ledger
    // that Reports_model::incomestatement()/customincomestatement() use
    // (geopos_transactions, type=Income). "ext=0" marks customer-side entries
    // (as opposed to ext=1 supplier-side entries used for Liabilities).
    // Customer visibility mirrors Customers_model::_get_datatables_query()/get_datatables()
    // exactly, so this section lists the same people as the /customers page:
    // real customers only (users_groups.group_id = 2), seller-scoped by
    // users.assigned_seller, staff-scoped by users.assigned.
    $this->db->select("geopos_transactions.payerid as id,
                       COALESCE(NULLIF(geopos_transactions.payer, ''), users.username, 'Unknown') as name,
                       COALESCE(SUM(geopos_transactions.credit),0) as balance", false);
    $this->db->from('geopos_transactions');
    $this->db->join('users', 'users.id = geopos_transactions.payerid', 'inner');
    $this->db->join('users_groups ug', 'ug.user_id = users.id', 'inner');
    $this->db->where('ug.group_id', 2);
    $this->db->where('geopos_transactions.type', 'Income');
    $this->db->where('geopos_transactions.ext', 0);
    if ($this->aauth->get_user()->loc) {
        $this->db->where('geopos_transactions.loc', $this->aauth->get_user()->loc);
    } elseif (!BDATA) {
        $this->db->where('geopos_transactions.loc', 0);
    }
    if ($is_seller) {
        // Scope by which customer the income belongs to, not by
        // geopos_transactions.eid — eid just records whoever's session created
        // the row and is not a reliable "owner" field.
        $this->db->where('users.assigned_seller', $seller_id);
    } elseif ($this->aauth->get_user()->id != 1) {
        // Non-admin staff see only their own assigned customers (same as /customers)
        $this->db->where('users.assigned', $this->aauth->get_user()->id);
    }
    $this->db->group_by('geopos_transactions.payerid');
    $data['customers'] = $this->db->get()->result_array();
$this->db->reset_query();
    $this->load->view('fixed/header', $head);
    $this->load->view('transactions/balance', $data);
    $this->load->view('fixed/footer');
}




    public function account_stats()
    {

        $this->accounts->account_stats();


    }


}