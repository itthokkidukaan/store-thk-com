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

class Quote extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('quote_model', 'quote');
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
        $head['title'] = "New Quote";
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->quote->warehouses();
        $data['is_seller_quote'] = function_exists('is_seller_user') && is_seller_user();
        $data['quote_customers'] = $this->get_quote_customers();
        $data['taxdetails'] = $this->common->taxdetail();
        $this->load->view('fixed/header', $head);
        $this->load->view('quotes/newquote', $data);
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
        $head['title'] = "Edit Quote #" . $data['invoice']['tid'];
        $head['usernm'] = $this->aauth->get_user()->username;
        $data['warehouse'] = $this->quote->warehouses();
        $data['is_seller_quote'] = function_exists('is_seller_user') && is_seller_user();
        $data['quote_customers'] = $this->get_quote_customers();
        $this->load->model('plugins_model', 'plugins');
        $data['exchange'] = $this->plugins->universal_api(5);
        $this->load->library("Common");
        $data['taxlist'] = $this->common->taxlist_edit($data['invoice']['taxstatus']);
        $this->load->view('fixed/header', $head);
        $this->load->view('quotes/edit', $data);
        $this->load->view('fixed/footer');
    }

    private function get_quote_customers()
    {
        $this->db->select('id, username as name, address, city, mobile as phone, email, discount_c');
        $this->db->from('users');

        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) {
                $this->db->or_where('loc', 0);
            }
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }

        if (function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('assigned', (int)$this->session->userdata('user_id'));
        }

        $this->db->order_by('username', 'asc');
        $customers = $this->db->get()->result_array();

        foreach ($customers as &$customer) {
            $customer['discount'] = amountFormat_general($customer['discount_c']);
        }
        unset($customer);

        return $customers;
    }

    //invoices list
    public function index()
    {
        $head['title'] = "Manage Quote";
        $data['eid'] = intval($this->input->get('eid'));
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('quotes/quotes', $data);
        $this->load->view('fixed/footer');
    }

    //action
    public function action()
    {
        $user = $this->aauth->get_user();
        $loc = $user ? (int)$user->loc : 0;
        $currency = $this->input->post('mcurrency');
        $customer_id = $this->input->post('customer_id');
        $invocieno = $this->input->post('invocieno');
        $invoicedate = $this->input->post('invoicedate');
        if (!$invoicedate) $invoicedate = date('Y-m-d');
        $invocieduedate = $this->input->post('invocieduedate');
        if (!$invocieduedate) $invocieduedate = date('Y-m-d');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $loc);
        $ship_taxtype = $this->input->post('ship_taxtype');


        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer');
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $loc);
        $proposal = $this->input->post('propos');
        $total_tax = 0;
        $total_discount = 0;
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms');

        if (function_exists('is_seller_user') && is_seller_user()) {
            $emp = (int)$this->session->userdata('user_id');
        } else {
            $this->load->model('plugins_model', 'plugins');
            $empl_e = $this->plugins->universal_api(69);
            if ($empl_e['key1']) {
                $emp = $this->input->post('employee');
            } else {
                $emp = $user ? $user->id : 0;
            }
        }
        if (!$emp) $emp = 0;

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
        $bill_due_date = datefordatabase($invocieduedate);

        $this->db->select('tid');
        $this->db->from('geopos_quotes');
        $this->db->where('tid', $invocieno);
        $query = $this->db->get();
        if (@$query->row()->tid) {
            $this->db->select('tid');
            $this->db->from('geopos_quotes');
            $this->db->order_by('tid', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            $invocieno = $query->row()->tid + 1;
        }

        $data = array('tid' => $invocieno, 'invoicedate' => $bill_date, 'invoiceduedate' => $bill_due_date, 'subtotal' => $subtotal, 'shipping' => $shipping, 'ship_tax' => $shipping_tax, 'ship_tax_type' => $ship_taxtype, 'discount' => $total_discount, 'tax' => $total_tax, 'total' => $total, 'notes' => $notes, 'csd' => $customer_id, 'eid' => $emp, 'taxstatus' => $tax, 'discstatus' => $discstatus, 'format_discount' => $discountFormat, 'refer' => $refer, 'term' => $pterms, 'proposal' => $proposal, 'multi' => $currency, 'loc' => $loc);
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
            $product_unit = $this->input->post('unit');
            foreach ($pid as $key => $value) {

                $total_discount += numberClean(@$ptotal_disc[$key]);
                $total_tax += numberClean($ptotal_tax[$key]);
                $data = array(
                    'tid' => $invocieno,
                    'pid' => $product_id[$key],
                    'product' => $product_name1[$key],
                    'code' => $product_hsn[$key],
                    'qty' => numberClean($product_qty[$key]),
                    'price' => rev_amountExchange_s($product_price[$key], $currency, $loc),
                    'tax' => numberClean($product_tax[$key]),
                    'discount' => numberClean($product_discount[$key]),
                    'subtotal' => rev_amountExchange_s($product_subtotal[$key], $currency, $loc),
                    'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $loc),
                    'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $loc),
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
                $this->db->set(array('discount' => rev_amountExchange_s(amountFormat_general($total_discount), $currency, $loc), 'tax' => rev_amountExchange_s(amountFormat_general($total_tax), $currency, $loc), 'items' => $itc));
                $this->db->where('id', $invocieno);
                $this->db->update('geopos_quotes');
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    "Please choose product from product list. Go to Item manager section if you have not added the products."));
                $transok = false;
            }

            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('Quote has  been created') . " <a href='view?id=$invocieno' class='btn btn-info btn-lg'><span class='fa fa-eye' aria-hidden='true'></span> View </a> &nbsp; &nbsp;<a href='create' class='btn btn-amber btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span> " . $this->lang->line('Create') . "  </a>"));
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
        if (function_exists('is_seller_user') && is_seller_user()) {
            $eid = (int)$this->session->userdata('user_id');
        } elseif ($this->aauth->premission(9)) {
            $eid = (int)$this->input->post('eid');
        } else {
            $eid = (int)$this->aauth->get_user()->id;
        }
        $list = $this->quote->get_datatables($eid);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $invoices) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="' . base_url("quote/view?id=$invoices->id") . '">&nbsp; ' . $invoices->tid . '</a>';
            $row[] = $invoices->name;
            $row[] = dateformat($invoices->invoicedate);
            $row[] = amountExchange($invoices->total, 0, $this->aauth->get_user()->loc);
            $row[] = '<span class="badge st-' . $invoices->status . '">' . $this->lang->line(ucwords($invoices->status)) . '</span>';
            $row[] = '<a href="' . base_url("quote/view?id=$invoices->id") . '" class="btn btn-blue btn-sm"><i class="fa fa-eye"></i></a> &nbsp; <a href="' . base_url("quote/printquote?id=$invoices->id") . '&d=1" class="btn btn-info btn-sm"  title="Download"><span class="fa fa-download"></span></a>&nbsp;<a href="#" data-object-id="' . $invoices->id . '" class="btn btn-danger btn-sm delete-object"><span class="fa fa-trash"></span></a>';
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

    public function view()
    {
        $tid = intval($this->input->get('id'));
        $data['id'] = $tid;
        $data['invoice'] = $this->quote->quote_details($tid);
        $data['products'] = $this->quote->quote_products($tid);
        $data['attach'] = $this->quote->attach($tid);
        $data['employee'] = $this->quote->employee($data['invoice']['eid']);
        $head['title'] = "Quote #" . $data['invoice']['tid'];
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        if ($data['invoice']) $this->load->view('quotes/view', $data);
        $this->load->view('fixed/footer');
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

        $user = $this->aauth->get_user();
        $loc = $user ? (int)$user->loc : 0;
        $customer_id = $this->input->post('customer_id');
        $invocieno_n = $this->input->post('invocieno');
        $invocieno = $this->input->post('iid');
        $invoicedate = $this->input->post('invoicedate');
        if (!$invoicedate) $invoicedate = date('Y-m-d');
        $invocieduedate = $this->input->post('invocieduedate');
        if (!$invocieduedate) $invocieduedate = date('Y-m-d');
        $notes = $this->input->post('notes', true);
        $tax = $this->input->post('tax_handle');
        $total_tax = 0;
        $total_discount = 0;
        $discountFormat = $this->input->post('discountFormat');
        $pterms = $this->input->post('pterms');
        $propos = $this->input->post('propos');
        $currency = $this->input->post('mcurrency');
        $ship_taxtype = $this->input->post('ship_taxtype');
        $subtotal = rev_amountExchange_s($this->input->post('subtotal'), $currency, $loc);
        $shipping = rev_amountExchange_s($this->input->post('shipping'), $currency, $loc);
        $shipping_tax = rev_amountExchange_s($this->input->post('ship_tax'), $currency, $loc);
        if ($ship_taxtype == 'incl') $shipping = $shipping - $shipping_tax;
        $refer = $this->input->post('refer', true);
        $total = rev_amountExchange_s($this->input->post('total'), $currency, $loc);

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
        $product_unit = $this->input->post('unit');

        foreach ($pid as $key => $value) {

            $total_discount += numberClean(@$ptotal_disc[$key]);
            $total_tax += numberClean($ptotal_tax[$key]);

            $data = array(
                'tid' => $invocieno,
                'pid' => $product_id[$key],
                'product' => $product_name1[$key],
                'code' => $product_hsn[$key],
                'qty' => numberClean($product_qty[$key]),
                'price' => rev_amountExchange_s($product_price[$key], $currency, $loc),
                'tax' => numberClean($product_tax[$key]),
                'discount' => numberClean($product_discount[$key]),
                'subtotal' => rev_amountExchange_s($product_subtotal[$key], $currency, $loc),
                'totaltax' => rev_amountExchange_s($ptotal_tax[$key], $currency, $loc),
                'totaldiscount' => rev_amountExchange_s($ptotal_disc[$key], $currency, $loc),
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

        $total_discount = rev_amountExchange_s(amountFormat_general($total_discount), $currency, $loc);
        $total_tax = rev_amountExchange_s(amountFormat_general($total_tax), $currency, $loc);

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

        if (function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
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
                $this->lang->line('Quote to invoice conversion'), 'rediurl' => base_url() . 'invoices/edit?id=' . $invoiceId));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }
    }

    public function convert_po()
    {
        $tid = $this->input->post('tid');
        $person = $this->input->post('customer_id');

        $invoiceId = $this->quote->convert_po($tid, $person);

        if ($invoiceId) {

            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('Quote to invoice conversion'), 'rediurl' => base_url() . 'purchase/edit?id=' . $invoiceId));
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


}
