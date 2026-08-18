<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Billing_model extends CI_Model
{

    public function paynow($tid, $amount, $note, $pmethod, $loc = false,$bill_date=null,$account_d=0)
    {
        $account = array('id' => false, 'holder' => '');
        if ($loc) {
            $this->db->select('geopos_accounts.id,geopos_accounts.holder,');
            $this->db->from('geopos_locations');
            $this->db->where('geopos_locations.id', $loc);
            $this->db->join('geopos_accounts', 'geopos_locations.ext = geopos_accounts.id', 'left');
            $query = $this->db->get();
            $account = $query->row_array();
        }
        if (empty($account) || !$account['id']) {
            $this->db->select('geopos_accounts.id,geopos_accounts.holder,');
            $this->db->from('univarsal_api');
            $this->db->where('univarsal_api.id', 54);

            $this->db->join('geopos_accounts', 'univarsal_api.key1 = geopos_accounts.id', 'left');

            $query = $this->db->get();
            $account = $query->row_array();
        }

        if ($account_d>0) {
           $this->db->select('*');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $account_d);
            $query = $this->db->get();
            $account = $query->row_array();
        }
        
        if (empty($account) || !$account['id']) {
            $this->db->select('id, holder');
            $this->db->from('geopos_accounts');
            $this->db->limit(1);
            $query = $this->db->get();
            $account = $query->row_array();
        }

        $this->db->select('orders.*,users.username as name,users.id AS cid');
        $this->db->from('orders');
        $this->db->where('orders.id', $tid);
        $this->db->join('users', 'orders.user_id = users.id', 'left');

        $query = $this->db->get();
        $invoice = $query->row_array();

        if(!$bill_date) $bill_date = date('Y-m-d');


        $data = array(
            'acid' => $account['id'],
            'account' => $account['holder'],
            'type' => 'Income',
            'cat' => 'Sales',
            'credit' => $amount,
            'payer' => $invoice['name'],
            'payerid' => $invoice['cid'],
            'method' => $pmethod,
            'date' => $bill_date,
            'eid' => ($this->aauth->get_user() ? $this->aauth->get_user()->id : 0),
            'tid' => $tid,
            'note' => $note,
            'loc' => 0
        );
        $this->db->trans_start();
        $this->db->insert('geopos_transactions', $data);
        $trans = $this->db->insert_id();


        $totalrm = $invoice['total'] - $invoice['pamnt'];

        if ($totalrm > $amount) {
            $this->db->set('payment_method', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);

            $this->db->set('status', 'partial');
            $this->db->set('paid_date', date('Y-m-d H:i:s'));
            $this->db->where('id', $tid);
            $this->db->update('orders');


            //account update
            $this->db->set('lastbal', "lastbal+$amount", FALSE);
            $this->db->where('id', $account['id']);
            $this->db->update('geopos_accounts');

        } else {
            $this->db->set('payment_method', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);
            $this->db->set('status', 'paid');
            $this->db->set('paid_date', date('Y-m-d H:i:s'));
            $this->db->where('id', $tid);
            $this->db->update('orders');
            //acount update
            $this->db->set('lastbal', "lastbal+$amount", FALSE);
            $this->db->where('id', $account['id']);
            $this->db->update('geopos_accounts');

        }
		
		//echo $this->db->last_query();
        /*         $dual = $this->custom->api_config(65);
      if ($dual['key1']) {

            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $account['id']);
            $query = $this->db->get();
            $account = $query->row_array();

            $data['credit'] = 0;
            $data['debit'] = $amount;
              $data['type'] = 'Expense';
            $data['acid'] = $account['id'];
            $data['account'] = $account['holder'];
            $data['note'] = 'Debit ' . $data['note'];

            $this->db->insert('geopos_transactions', $data);

            //account update
            $this->db->set('lastbal', "lastbal-$amount", FALSE);
            $this->db->where('id', $account['id']);
            $this->db->update('geopos_accounts');
      } */
        $this->aauth->applog("[Payment Invoice $tid]  Transaction-$trans - $amount ", $this->aauth->get_user()->username);
        if ($this->db->trans_complete()) {
            return true;
        } else {
            return false;
        }
    }

    public function gateway($id)
    {

        $this->db->from('geopos_gateways');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }


    public function gateway_list($enable = '')
    {

        $this->db->from('geopos_gateways');
        if ($enable == 'Yes') {
            $this->db->where('enable', 'Yes');
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function bank_accounts($enable = '')
    {

        $this->db->from('geopos_bank_ac');
        if ($enable == 'Yes') {
            $this->db->where('enable', 'Yes');
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function bank_account_info($id)
    {

        $this->db->from('geopos_bank_ac');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }


    public function gateway_update($gid, $currency, $key1, $key2, $enable, $devmode, $p_fee)
    {
        $data = array(
            'key1' => $key1,
            'key2' => $key2,
            'enable' => $enable,
            'dev_mode' => $devmode,
            'currency' => $currency,
            'surcharge' => $p_fee
        );


        $this->db->set($data);
        $this->db->where('id', $gid);

        if ($this->db->update('geopos_gateways')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function online_pay_settings()
    {

        $this->db->select('univarsal_api.key1 AS default_acid,univarsal_api.key2 AS currency_code,univarsal_api.url AS enable,univarsal_api.method AS bank, geopos_accounts.*');
        $this->db->from('univarsal_api');
        $this->db->where('univarsal_api.id', 54);

        $this->db->join('geopos_accounts', 'univarsal_api.key1 = geopos_accounts.id', 'left');

        $query = $this->db->get();
        return $query->row_array();
    }


    public function payment_settings($id, $enable, $bank)
    {
        $data = array(
            'key1' => $id,
            'url' => $enable,
            'method' => $bank
        );


        $this->db->set($data);
        $this->db->where('id', 54);

        if ($this->db->update('univarsal_api')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


    public function bank_ac_add($name, $acn, $code, $enable, $bank, $branch, $address)
    {
        $data = array(
            'name' => $name,
            'acn' => $acn,
            'code' => $code,
            'enable' => $enable,
            'note' => $bank,
            'branch' => $branch,
            'address' => $address,
        );


        if ($this->db->insert('geopos_bank_ac', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


    public function bank_ac_update($gid, $name, $acn, $code, $enable, $bank, $branch, $address)
    {
        $data = array(
            'name' => $name,
            'acn' => $acn,
            'code' => $code,
            'enable' => $enable,
            'note' => $bank,
            'branch' => $branch,
            'address' => $address,
        );


        $this->db->set($data);
        $this->db->where('id', $gid);

        if ($this->db->update('geopos_bank_ac')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function add_currency($code, $symbol, $spos, $rate, $decimal, $thous_sep, $deci_sep)
    {
        $data = array(
            'code' => $code,
            'symbol' => $symbol,
            'rate' => $rate,
            'thous' => $thous_sep,
            'dpoint' => $deci_sep,
            'decim' => $decimal,
            'cpos' => $spos
        );


        if ($this->db->insert('geopos_currencies', $data)) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit_currency($gid, $code, $symbol, $spos, $rate, $decimal, $thous_sep, $deci_sep)
    {
        $data = array(
            'code' => $code,
            'symbol' => $symbol,
            'rate' => $rate,
            'thous' => $thous_sep,
            'dpoint' => $deci_sep,
            'decim' => $decimal,
            'cpos' => $spos
        );
        $this->db->set($data);
        $this->db->where('id', $gid);
        if ($this->db->update('geopos_currencies')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


    public function exchange($currency, $key1, $key2, $enable, $reverse = 0)
    {
        $data = array(
            'key1' => $key1,
            'key2' => $key2,
            'url' => $currency,
            'other' => $reverse,
            'active' => $enable
        );

        $this->db->set($data);
        $this->db->where('id', 5);

        if ($this->db->update('univarsal_api')) {
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }


    public function recharge_done($id, $amount)
    {

        $this->db->set('balance', "balance+$amount", FALSE);
        $this->db->where('id', $id);

        $this->db->update('geopos_customers');

        $data = array(
            'type' => 21,
            'rid' => $id,
            'col1' => $amount,
            'col2' => date('Y-m-d H:i:s') . ' Account Recharge'
        );


        if ($this->db->insert('geopos_metadata', $data)) {
            $this->aauth->applog("[Wallet Payment $id]  Amt - $amount ", @$this->aauth->get_user()->username);
            return true;
        } else {
            return false;
        }

    }

    public function pos_paynow($tid, $amount, $note, $pmethod)
    {

        $this->db->select('geopos_accounts.id,geopos_accounts.holder,');
        $this->db->from('univarsal_api');
        $this->db->where('univarsal_api.id', 54);
        $this->db->join('geopos_accounts', 'univarsal_api.key1 = geopos_accounts.id', 'left');

        $query = $this->db->get();
        $account = $query->row_array();

        $this->db->select('geopos_invoices.*,geopos_customers.name,geopos_customers.id AS cid');
        $this->db->from('geopos_invoices');
        $this->db->where('geopos_invoices.id', $tid);
        $this->db->join('geopos_customers', 'geopos_invoices.csd = geopos_customers.id', 'left');

        $query = $this->db->get();
        $invoice = $query->row_array();

        // print_r($invoice);


        $data = array(
            'acid' => $account['id'],
            'account' => $account['holder'],
            'type' => 'Income',
            'cat' => 'Sales',
            'credit' => $amount,
            'payer' => $invoice['name'],
            'payerid' => $invoice['csd'],
            'method' => $pmethod,
            'date' => date('Y-m-d'),
            'eid' => $invoice['eid'],
            'tid' => $tid,
            'note' => $note,
            'loc' => $invoice['loc']
        );
        $this->db->trans_start();
        $this->db->insert('geopos_transactions', $data);
        $trans = $this->db->insert_id();


        $totalrm = $invoice['total'] - $invoice['pamnt'];

        if ($totalrm > $amount) {
            $this->db->set('pmethod', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);

            $this->db->set('status', 'partial');
            $this->db->where('id', $tid);
            $this->db->update('geopos_invoices');


            //account update
            $this->db->set('lastbal', "lastbal+$amount", FALSE);
            $this->db->where('id', $account['id']);
            $this->db->update('geopos_accounts');

        } else {
            $this->db->set('pmethod', $pmethod);
            $this->db->set('pamnt', "pamnt+$amount", FALSE);
            $this->db->set('status', 'paid');
            $this->db->where('id', $tid);
            $this->db->update('geopos_invoices');
            //acount update
            $this->db->set('lastbal', "lastbal+$amount", FALSE);
            $this->db->where('id', $account['id']);
            $this->db->update('geopos_accounts');

        }
        $this->aauth->applog("[Payment Invoice $tid]  Transaction-$trans - $amount ", $this->aauth->get_user()->username);
        if ($this->db->trans_complete()) {
            return true;
        } else {
            return false;
        }
    }

    public function token($in=0,$type=1)
    {
        if($type==1){
             $data = array(
            'type' => 71,
            'rid' => $in,
            'col1' => 1,
            'col2' => 2,
            'd_date' => date('Y-m-d')
        );
        $this->db->insert('geopos_metadata', $data);
        return true;
        }elseif($type==2){
            $this->db->from('geopos_metadata');
            $this->db->where('type', 71);
            $this->db->where('rid', $in);
           $query = $this->db->get();
           return $query->row_array();
        }else{
              $this->db->delete('geopos_metadata', array('type' => 71,'rid'=> $in));
        }
    }


}