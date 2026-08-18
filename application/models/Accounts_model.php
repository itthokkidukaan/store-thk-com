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

defined('BASEPATH') OR exit('No direct script access allowed');


class Accounts_model extends CI_Model
{
    var $table = 'geopos_accounts';

    public function __construct()
    {
        parent::__construct();
    }

    public function accountslist($l=true,$lid=0)
    {
        // 'eid' (account owner) is a newer column: some environments haven't been migrated to add
        // it to geopos_accounts yet, so only join/filter on it when it's actually there. Without
        // this guard, the join/select below throws "Unknown column" and fatals the caller.
        $has_eid = $this->db->field_exists('eid', $this->table);
        if ($has_eid) {
            $this->db->select('geopos_accounts.*, users.username AS owner_name');
            $this->db->join('users', 'users.id = geopos_accounts.eid', 'left');
        } else {
            $this->db->select('geopos_accounts.*');
        }
        $this->db->from($this->table);
        if ($l) {
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('geopos_accounts.loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('geopos_accounts.loc', 0);
                $this->db->group_end();
            } else {
                if (!BDATA) $this->db->where('geopos_accounts.loc', 0);
            }
    } else {
            $this->db->where('geopos_accounts.loc', $lid);
        }
        if ($has_eid && function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('geopos_accounts.eid', (int)$this->session->userdata('user_id'));
        }

        $query = $this->db->get();
        return $query->result_array();
    }
	
	
	public function category_list()
    {
        $this->db->select('*');
        $this->db->from('geopos_trans_cat');
     

        $query = $this->db->get();
        return $query->result_array();
    }

    public function details($acid)
    {

        $has_eid = $this->db->field_exists('eid', 'geopos_accounts');
        if ($has_eid) {
            $this->db->select('geopos_accounts.*, users.username AS owner_name');
            $this->db->join('users', 'users.id = geopos_accounts.eid', 'left');
        } else {
            $this->db->select('geopos_accounts.*');
        }
        $this->db->from('geopos_accounts');
        $this->db->where('geopos_accounts.id', $acid);
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('geopos_accounts.loc', $this->aauth->get_user()->loc);
            if(BDATA)  $this->db->or_where('geopos_accounts.loc', 0);
            $this->db->group_end();
        }
        if ($has_eid && function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('geopos_accounts.eid', (int)$this->session->userdata('user_id'));
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function addnew($accno, $holder, $intbal, $acode, $lid,$account_type, $employeeID, $category)
    {
        $data = array(
            'acn' => $accno,
            'holder' => $holder,
            'adate' => date('Y-m-d H:i:s'),
            'lastbal' => $intbal,
            'code' => $acode,
            'loc' => $lid,
            'account_type'=>$account_type,
            'category'=>$category,
        );
        if ($this->db->field_exists('eid', 'geopos_accounts')) {
            $data['eid'] = (int)$this->aauth->get_user()->id;
        }

        if ($this->db->insert('geopos_accounts', $data)) {
            $this->aauth->applog("[Account Created] $accno - $intbal ID " . $this->db->insert_id(), $this->aauth->get_user()->username);
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED'). "  <a href='".base_url('accounts')."' class='btn btn-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a> <a href='add' class='btn btn-info btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span>  </a>"));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit($acid, $accno, $holder, $acode, $lid, $account_equity='')
    {
        if($account_equity){
               $data = array(
            'acn' => $accno,
            'holder' => $holder,
            'code' => $acode,
            'loc' => $lid,
            'lastbal'=>$account_equity
        );
        }
        else{
               $data = array(
            'acn' => $accno,
            'holder' => $holder,
            'code' => $acode,
            'loc' => $lid
        );
        }

        $this->db->set($data);
        $this->db->where('id', $acid);
         if ($this->aauth->get_user()->loc) {
           $this->db->where('loc', $this->aauth->get_user()->loc);
         }
        if ($this->db->field_exists('eid', 'geopos_accounts') && function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }

        if ($this->db->update('geopos_accounts')) {
            $this->aauth->applog("[Account Edited] $accno - ID " . $acid, $this->aauth->get_user()->username);
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function account_stats()
    {
        $conds = array();
        if ($this->aauth->get_user()->loc) {
            $locwhr = 'loc=' . $this->aauth->get_user()->loc;
            if (BDATA) $locwhr .= ' OR loc=0';
            $conds[] = '(' . $locwhr . ')';
        }
        if ($this->db->field_exists('eid', 'geopos_accounts') && function_exists('is_seller_user') && is_seller_user()) {
            $conds[] = 'eid=' . (int)$this->session->userdata('user_id');
        }
        $whr = $conds ? ' WHERE ' . implode(' AND ', $conds) : '';

        $query = $this->db->query("SELECT SUM(lastbal) AS balance,COUNT(id) AS count_a FROM geopos_accounts $whr");

        $result = $query->row_array();
        echo json_encode(array(0 => array('balance' => amountExchange($result['balance'], 0, $this->aauth->get_user()->loc), 'count_a' => $result['count_a'])));

    }  	public function account_dash()
    {
        $conds = array();
        if ($this->aauth->get_user()->loc) {
            $locwhr = 'loc=' . $this->aauth->get_user()->loc;
            if (BDATA) $locwhr .= ' OR loc=0';
            $conds[] = '(' . $locwhr . ')';
        }
        if ($this->db->field_exists('eid', 'geopos_accounts') && function_exists('is_seller_user') && is_seller_user()) {
            $conds[] = 'eid=' . (int)$this->session->userdata('user_id');
        }
        $whr = $conds ? ' WHERE ' . implode(' AND ', $conds) : '';

        $query = $this->db->query("SELECT SUM(lastbal) AS balance,COUNT(id) AS count_a FROM geopos_accounts $whr");

        $result = $query->row_array();return $result['balance'];
      //  echo json_encode(array(0 => array('balance' => amountExchange($result['balance'], 0, $this->aauth->get_user()->loc), 'count_a' => $result['count_a'])));

    }

}