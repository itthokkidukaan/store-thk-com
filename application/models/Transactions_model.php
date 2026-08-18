<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions_model extends CI_Model
{
    var $table = 'geopos_transactions';
    var $column_order = array('date', 'acid', 'debit', 'credit', 'payer', 'method','note','cat');
    var $column_search = array('id', 'account', 'payer');
    var $order = array('id' => 'DESC');
    var $opt = '';

    private function _get_datatables_query()
    {
        $is_seller = is_seller_user();
        $this->db->select('geopos_transactions.*,geopos_transactions.id as id');
        $this->db->from($this->table);
        switch ($this->opt) {
            case 'income':
                $this->db->where('type', 'Income');
                break;
            case 'expense':
                $this->db->where('type', 'Expense');
                break;
        }
        if ($is_seller) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }

        $i = 0;

        foreach ($this->column_search as $item) // loop column
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

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
          //  $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		  $this->db->order_by('id', 'DESC');
        } else if (isset($this->order)) {
            $order = $this->order;
           // $this->db->order_by(key($order), $order[key($order)]);
            $this->db->order_by('id', 'DESC');
        }
    } 
	
	
	private function _get_dailypaydatatables_query($from_date = '', $to_date = '')
    {
        $is_seller = is_seller_user();
        $this->db->select('geopos_transactions.*,geopos_transactions.id as id');
        $this->db->from($this->table);
     /*    switch ($this->opt) {
            case 'income':
                $this->db->where('type', 'Income');
                break;
            case 'expense':
                $this->db->where('type', 'Expense');
                break;
        } */
   $this->db->where('acid', 5);
    $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where('debit !=', '0.00');
        if ($is_seller) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
	
	if ($from_date && $to_date) {
        $this->db->where("DATE(created_at) >=", $from_date);
        $this->db->where("DATE(created_at) <=", $to_date);
    }
	
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }

        $i = 0;

        foreach ($this->column_search as $item) // loop column
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

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
          //  $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		  $this->db->order_by('id', 'DESC');
        } else if (isset($this->order)) {
            $order = $this->order;
           // $this->db->order_by(key($order), $order[key($order)]);
            $this->db->order_by('id', 'DESC');
        }
    }

    function get_datatables($opt = 'all')
    {
        $this->opt = $opt;
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
		//echo $this->db->last_query();
        return $query->result();
    } 
	
	
	/* function get_dailypaydatatables($opt = 'all')
    {
        $this->opt = $opt;
        $this->_get_dailypaydatatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    } */
	
	function get_dailypaydatatables($opt = 'all', $from_date = '', $to_date = '')
{
    $this->opt = $opt;
    $this->_get_dailypaydatatables_query($from_date, $to_date);
    if ($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    return $this->db->get()->result();
}


   /*  function count_dailypayfiltered()
    {
        $this->db->from('geopos_transactions');
         $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where('debit !=', '0.00');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }
        $query = $this->db->get(); 
        return $query->num_rows();
    }   */


function count_dailypayfiltered($from_date = '', $to_date = '')
{
    $this->_get_dailypaydatatables_query($from_date, $to_date);
    return $this->db->get()->num_rows();
}

function count_dailypayall($from_date = '', $to_date = '')
{
    $is_seller = is_seller_user();
    $this->db->from('geopos_transactions');
    $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where('debit !=', '0.00');
    if ($is_seller) {
        $this->db->where('eid', (int)$this->session->userdata('user_id'));
    }

    if ($from_date && $to_date) {
        $this->db->where("DATE(created_at) >=", $from_date);
        $this->db->where("DATE(created_at) <=", $to_date);
    }

    if ($this->aauth->get_user()->loc) {
        $this->db->where('loc', $this->aauth->get_user()->loc);
    }

    return $this->db->count_all_results();
}


	function count_filtered()
    {
        $is_seller = is_seller_user();
        $this->db->from('geopos_transactions');
        switch ($this->opt) {
            case 'income':
                $this->db->where('type', 'Income');
                break;
            case 'expense':
                $this->db->where('type', 'Expense');
                break;
        }
        if ($is_seller) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }
        $query = $this->db->get(); 
        return $query->num_rows();
    }

    public function count_all()
    {
        $is_seller = is_seller_user();
        $this->db->from($this->table);
        switch ($this->opt) {
            case 'income':
                $this->db->where('type', 'Income');
                break;
            case 'expense':
                $this->db->where('type', 'Expense');
                break;
        }
        if ($is_seller) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }

        return $this->db->count_all_results();
    }  
	
	
/* 	public function count_dailypayall()
    {
        $this->db->from($this->table);
         $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where('debit !=', '0.00');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        }

        return $this->db->count_all_results();
    } */

    public function categories()
    {
        $this->db->select('*');
        $this->db->from('geopos_trans_cat');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function acc_list()
    {
        $this->db->select('id,acn,holder, account_type, employeeID, lastbal');
        $this->db->from('geopos_accounts');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function addcat($name, $parentcategory)
    {
        $data = array(
            'name' => $name,
            'parent_cat' => $parentcategory
        );

        return $this->db->insert('geopos_trans_cat', $data);
    }

    public function addtrans($payer_id, $payer_name, $pay_acc, $date, $debit, $credit, $pay_type, $pay_cat, $paymethod, $note, $eid, $loc = 0, $ty = 0)
    {

        if ($pay_acc > 0) {

            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $pay_acc);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            $query = $this->db->get();
            $account = $query->row_array();

            if ($account) {
                $data = array(
                    'payerid' => $payer_id,
                    'payer' => $payer_name,
                    'acid' => $pay_acc,
                    'account' => $account['holder'],
                    'date' => $date,
					'created_at' => date('Y-m-d H:i:s'),
                    'debit' => $debit,
                    'credit' => $credit,
                    'type' => $pay_type,
                    'cat' => $pay_cat,
                    'method' => $paymethod,
                    'eid' => $eid,
                    'note' => $note,
                    'ext' => $ty,
                    'loc' => $loc
                );
                $amount = $credit - $debit;
                $this->db->set('lastbal', "lastbal+$amount", FALSE);
                $this->db->where('id', $pay_acc);
                $this->db->update('geopos_accounts');


                $transmydata = array (
                    'transaction_type' => 'transaction',
                    'user_id' => $payer_id,
                    'order_id' => $invocieno,
                    'type' => 'Cash',
                    'txn_id' => '',
                    'amount' => $total,
                    'status' => "success",
                    'txntype' => "Expensess",
                    'message' => "Transection  Successfully",
          );
          $this->db->insert('transactions', $transmydata);
                return $this->db->insert('geopos_transactions', $data);
            }
        }
    } 		

	public function addmytrans($payer_id, $payer_name, $pay_acc, $date, $debit, $credit, $pay_type, $pay_cat, $paymethod, $note, $eid, $loc = 0, $ty = 0, $receivertypes)
    {

        if ($pay_acc > 0) {

            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $pay_acc);
          /*   if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            } */
            $query = $this->db->get();
			
		
            $account = $query->row_array();

            if ($account) {
                $data = array(
                    'payerid' => $payer_id,
                    'payer' => $payer_name,
                    'acid' => $pay_acc,
                    'account' => $account['holder'],
                    'date' => $date,
					'created_at' => date('Y-m-d H:i:s'),
                    'debit' => $debit,
                    'credit' => $credit,
                    'type' => $pay_type,
                    'cat' => $pay_cat,
                    'method' => $paymethod,
                    'eid' => $eid,
                    'note' => $note,
                    'ext' => $ty,
                    'loc' => $loc
                );

				$recedata = array(
                    'payerid' => $pay_acc,
                    'payer' =>  $account['holder'],
                    'acid' => $payer_id,
                    'account' => $payer_name,
                    'date' => $date,
					'created_at' => date('Y-m-d H:i:s'),
                    'debit' =>  $credit,
                    'credit' => $debit,
                    'type' => $pay_type,
                    'cat' => $pay_cat,
                    'method' => $paymethod,
                    'eid' => $eid,
                    'note' => $note,
                    'ext' => $ty,
                    'loc' => $loc
                );
				
			
                $amount = $credit - $debit;
                $this->db->set('lastbal', "lastbal+$amount", FALSE);
                $this->db->where('id', $pay_acc);
                $this->db->update('geopos_accounts');

				$namount = $debit- $credit;
				
				if($receivertypes=='employee'){
						$this->db->set('lastbal', "lastbal+$namount", FALSE);
                $this->db->where('employeeID', $payer_id);
                $this->db->update('geopos_accounts');
				}else{
					
					$this->db->set('lastbal', "lastbal+$namount", FALSE);
                $this->db->where('id', $payer_id);
                $this->db->update('geopos_accounts');
					
				}
				


                $transmydata = array (
                    'transaction_type' => 'transaction',
                    'user_id' => $payer_id,
                    'order_id' => 0,
                    'type' => $paymethod,
                    'txn_id' => '',
                    'amount' => $amount,
                    'status' => "success",
                    'txntype' => "Expensess",
                    'message' => "Transection  Successfully",
          );
          $this->db->insert('transactions', $transmydata);
          $this->db->insert('geopos_transactions', $data);
                return $this->db->insert('geopos_transactions', $recedata);
            }
        }
    }

   public function addtransfer($pay_acc, $pay_acc2, $amount, $eid, $loc = 0)
    {

        if ($pay_acc > 0) {

            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $pay_acc);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            $query = $this->db->get();
            $account = $query->row_array();
            $this->db->select('holder');
            $this->db->from('geopos_accounts');
            $this->db->where('id', $pay_acc2);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            $query = $this->db->get();
            $account2 = $query->row_array();

            if ($account2) {
                $data = array(
                    'payerid' => '',
                    'payer' => '',
                    'acid' => $pay_acc2,
                    'account' => $account2['holder'],
                    'date' => date('Y-m-d'),
					'created_at' => date('Y-m-d H:i:s'),
                    'debit' => 0,
                    'credit' => $amount,
                    'type' => 'Transfer',
                    'cat' => '',
                    'method' => '',
                    'eid' => $eid,
                    'note' => 'Transferred by ' . $account['holder'],
                    'ext' => 9,
                    'loc' => $loc
                );
                $this->db->insert('geopos_transactions', $data);


                $this->db->set('lastbal', "lastbal+$amount", FALSE);
                $this->db->where('id', $pay_acc2);
                $this->db->update('geopos_accounts');
                $datec = date('Y-m-d');

                $data = array(
                    'payerid' => '',
                    'payer' => '',
                    'acid' => $pay_acc,
                    'account' => $account['holder'],
                    'date' => $datec,
					'created_at' => date('Y-m-d H:i:s'),
                    'debit' => $amount,
                    'credit' => 0,
                    'type' => 'Transfer',
                    'cat' => '',
                    'method' => '',
                    'eid' => $eid,
                    'note' => 'Transferred to ' . $account2['holder'],
                    'ext' => 9,
                    'loc' => $loc
                );

                $this->db->set('lastbal', "lastbal-$amount", FALSE);
                $this->db->where('id', $pay_acc);
                $this->db->update('geopos_accounts');

                return $this->db->insert('geopos_transactions', $data);
            }
        }
    } 





    public function delt($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_transactions');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $this->db->where('id', $id);
        $query = $this->db->get();
        $trans = $query->row_array();

        $amt = $trans['credit'] - $trans['debit'];
        $this->db->set('lastbal', "lastbal-$amt", FALSE);
        $this->db->where('id', $trans['acid']);
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $this->db->update('geopos_accounts');

        if ($trans['tid'] > 0 && $trans['ext'] == 0) {
            $crd = $trans['credit'];
            $this->db->set('pamnt', "pamnt-$crd", FALSE);
            $this->db->set('status', "partial");
            $this->db->where('id', $trans['tid']);
            $this->db->update('geopos_invoices');
        }
                if ($trans['tid'] > 0 && $trans['ext'] == 1) {
            $crd = $trans['debit'];
            $this->db->set('pamnt', "pamnt-$crd", FALSE);
            $this->db->set('status', "partial");
            $this->db->where('id', $trans['tid']);
            $this->db->update('geopos_purchase');
        }
        $this->db->delete('geopos_transactions', array('id' => $id));
        $alert = $this->custom->api_config(66);
        if ($alert['key2'] == 1) {
            $this->load->model('communication_model');
            $subject = $trans['payer'] . ' ' . $this->lang->line('DELETED');
            $body = $subject . '<br> ' . $this->lang->line('Credit') . ' ' . $this->lang->line('Amount') . ' ' . $trans['credit'] . '<br> ' . $this->lang->line('Debit') . ' ' . $this->lang->line('Amount') . ' ' . $trans['debit'] . '<br> ID# ' . $trans['id'];
            $out = $this->communication_model->send_corn_email($alert['url'], $alert['url'], $subject, $body, false, '');
        }
        return array('status' => 'Success', 'message' => $this->lang->line('DELETED'));


    }

    public function view($id)
    {

        $this->db->select('*');
        $this->db->from('geopos_transactions');
        $this->db->where('id', $id);

        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function cview($id, $ext = 0)
    {

        if ($ext == 1) {
            $this->db->select('*');
            $this->db->from('geopos_supplier');
            $this->db->where('id', $id);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            $query = $this->db->get();
            return $query->row_array();
        } elseif ($ext == 4) {
            $this->db->select('geopos_employees.*,geopos_users.email');
            $this->db->from('geopos_employees');
            $this->db->join('geopos_users', 'geopos_employees.id = geopos_users.id', 'left');
            $this->db->where('geopos_employees.id', $id);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select('*');
            $this->db->from('geopos_customers');
            $this->db->where('id', $id);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
            $query = $this->db->get();
            return $query->row_array();
        }

    }

    public function cat_details($id)
    {

        $this->db->select('*');
        $this->db->from('geopos_trans_cat');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
       public function cat_details_name($id)
    {

        $this->db->select('*');
        $this->db->from('geopos_trans_cat');
        $this->db->where('name', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function cat_update($id, $cat_name, $parentcategory)
    {

        $data = array(
            'name' => $cat_name,
            'parent_cat' => $parentcategory
        );


        $this->db->set($data);
        $this->db->where('id', $id);

        if ($this->db->update('geopos_trans_cat')) {
            return true;
        } else {
            return false;
        }
    }

    public function check_balance($id)
    {
        $this->db->select('balance');
        $this->db->from('geopos_customers');
        $this->db->where('id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->row_array();
    }



public function get_unique_accounts() {
   return $this->db->distinct()
                    ->select('payerid, payer')
                    ->order_by('payer', 'ASC')
                    ->get('geopos_transactions')
                    ->result();
}

    public function get_locations() {
        return $this->db->select('loc')->group_by('loc')->get('geopos_transactions')->result();
    }

    public function get_filtered_transactions($postData) {
        $draw = intval($postData['draw']);
        $start = intval($postData['start']);
        $length = intval($postData['length']);

        $payerid = $postData['account'];
        $location = $postData['location'];
        $from_date = $postData['from_date'];
        $to_date = $postData['to_date'];

        $this->db->from('geopos_transactions');

        if ($payerid != '') {
            $this->db->where('payerid', $payerid);
        }

        if ($location != '') {
            $this->db->where('loc', $location);
        }

        if ($from_date && $to_date) {
            $this->db->where('date >=', $from_date);
            $this->db->where('date <=', $to_date);
        }

        $this->db->order_by('date', 'ASC');

        $totalRecords = $this->db->count_all_results('', FALSE);
        $this->db->limit($length, $start);
        $query = $this->db->get();

        $records = $query->result();
        $data = [];
        $closing = 0;

        foreach ($records as $row) {
            $closing += ($row->debit - $row->credit);

            $data[] = [
                $row->date,
                $row->loc,
                'Sales',
                'invoice',
                'INV' . $row->tid,
                $row->note ?? '',
                number_format($row->debit, 2),
                number_format($row->credit, 2),
                number_format($closing, 2)
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ];
    }
	
	
	 public function get_expense_statement($account_id, $sdate, $edate)
    {
        $this->db->select('id, date, account, payer, method, note, debit');
        $this->db->from('geopos_transactions');
        $this->db->where('type', 'Expense');
        $this->db->where('cat', 'Expenses'); 

        if ($account_id != 'All') {
            $this->db->where('acid', $account_id);
        }

      if (!empty($sdate) && !empty($edate)) {
   
    $sdate = date('Y-m-d', strtotime(str_replace('/', '-', $sdate)));
    $edate = date('Y-m-d', strtotime(str_replace('/', '-', $edate)));

    $this->db->where('date >=', $sdate);
    $this->db->where('date <=', $edate);
}

    $this->db->where('debit >', 0);
        $this->db->order_by('date', 'ASC');
        return $this->db->get()->result_array();
		 
		
    }


}