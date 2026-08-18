<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
    }

    private function get_seller_id()
    {
        if ($this->ion_auth->is_seller()) {
            return (int)$this->session->userdata('user_id');
        }
        return 0;
    }

    public function todayInvoice($today)
    {
		
		$where = "DATE(date_added) ='$today'";
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select('COUNT(DISTINCT orders.id) as total', false);
            $this->db->from('orders');
            $this->db->join('order_items oi', 'oi.order_id=orders.id', 'left');
            $this->db->where("DATE(orders.date_added) ='$today'");
            $this->db->where('orders.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $row = $this->db->get()->row_array();
            return isset($row['total']) ? (int)$row['total'] : 0;
        }
        $this->db->where($where);
        $this->db->where('orders.is_deleted', 0);
        if ($this->aauth->get_user()->roleid < 5) {
            $this->db->where('created_by', $this->aauth->get_user()->id);
        }
        $this->db->from('orders');
        return $this->db->count_all_results();

    }

    public function todaySales($today)
    {

        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select_sum('oi.sub_total', 'total');
            $this->db->from('order_items oi');
            $this->db->join('orders o', 'o.id=oi.order_id', 'left');
            $this->db->where("DATE(o.date_added) ='$today'");
            $this->db->where('o.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $query = $this->db->get();
            return $query->row()->total;
        }
        $where = "DATE(invoicedate) ='$today'";
        $this->db->select_sum('total');
        $this->db->from('geopos_invoices');
        $this->db->where($where);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function todayInexp($today)
    {
        $seller_id = $this->get_seller_id();
        $this->db->select('SUM(amount) as debit', FALSE);
        $this->db->where("DATE(date_created) ='$today'");
        $this->db->where("txntype = 'purchase'");
        $this->db->from('transactions');
        if ($seller_id) {
            $this->db->join('order_items oi', 'oi.id=transactions.order_item_id', 'left');
            $this->db->where('oi.seller_id', $seller_id);
        }
        $query = $this->db->get();
        return $query->row_array();
    }


	public function todayexpens($today)
    {
        $this->db->select('SUM(total) as debit', FALSE);
        $this->db->where("DATE(invoicedate) ='$today'");
        
             if ($this->aauth->get_user()->roleid != 1) {
            $this->db->where('eid', $this->aauth->get_user()->id);
        }
        $this->db->from('geopos_purchase');
        $query = $this->db->get();
        return $query->row_array();
    } 

	public function todayInexsell($today)
    {
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select('SUM(oi.sub_total) as credit', FALSE);
            $this->db->from('order_items oi');
            $this->db->join('orders o', 'o.id=oi.order_id', 'left');
            $this->db->where("DATE(o.date_added) ='$today'");
            $this->db->where('o.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $query = $this->db->get();
            return $query->row_array();
        }
        $this->db->select('SUM(final_total) as credit', FALSE);
        $this->db->where("DATE(date_added) ='$today'");
        if ($this->aauth->get_user()->roleid != 1) {
            $this->db->where('created_by', $this->aauth->get_user()->id);
        }
        $this->db->where('orders.is_deleted', 0);
        $this->db->from('orders');
        $query = $this->db->get();
        return $query->row_array();
    }
	
	public function thismontsell($month, $year)
    {
        $today = date('Y-m-d');
        $days = date("t", strtotime($today));
        $where = "DATE(date_added) BETWEEN '$year-$month-01' AND '$year-$month-$days'";
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select('SUM(oi.sub_total) as credit, 0 as pamnt', FALSE);
            $this->db->from('order_items oi');
            $this->db->join('orders o', 'o.id=oi.order_id', 'left');
            $this->db->where("DATE(o.date_added) BETWEEN '$year-$month-01' AND '$year-$month-$days'");
            $this->db->where('o.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $query = $this->db->get();
            return $query->row_array();
        }
        $this->db->select('SUM(final_total) as credit, SUM(pamnt) as pamnt', FALSE);
        $this->db->where($where);
        if ($this->aauth->get_user()->roleid != 1) {
            $this->db->where('created_by', $this->aauth->get_user()->id);
        }
        $this->db->where('orders.is_deleted', 0);
        $this->db->from('orders');
        $query = $this->db->get();
        return $query->row_array();
    }



	public function thismontpurchasevlue($month, $year)
{
    $today = date('Y-m-d');
    $days = date("t", strtotime($today));
    $start_date = "$year-$month-01";
    $end_date = "$year-$month-$days";
    
	
    $sqls = "SELECT 
        oi.*, 
        p.article,
        (
            SELECT gpi.price
            FROM geopos_purchase_items gpi
            JOIN geopos_purchase gp ON gp.id = gpi.tid
            WHERE 
                (
                    gpi.product = oi.product_name 
                    OR gpi.pid = oi.product_id
                )
                AND gp.invoicedate <= DATE(o.date_added)
                AND gpi.price > 0
            ORDER BY gp.invoicedate DESC
            LIMIT 1
        ) AS purchase_price
    FROM order_items oi
    LEFT JOIN products p ON p.name = oi.product_name
    JOIN orders o ON o.id = oi.order_id
    WHERE o.is_deleted=0 AND o.date_added BETWEEN '$start_date' AND '$end_date'";
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $sqls .= " AND oi.seller_id = " . (int)$seller_id;
        }



    $queryss = $this->db->query($sqls);
    $resultss = $queryss->result_array();
    $total_purchase_value = 0;

    foreach ($resultss as $row) {
        $con = convert_to_base_unit($row['variant_name']);
        $unit_qty = isset($con['qty']) ? $con['qty'] : 1;
        $unit_purchase_price = $row['purchase_price'] * $unit_qty;
        $puprice = $row['quantity'] * $unit_purchase_price;

        $row['unit_purchase_price'] = $unit_purchase_price;
        $row['total_purchase_price'] = $puprice;

        $total_purchase_value += $puprice;
    }
	
	 return round($total_purchase_value,2);
	
}
	
	
	
	public function thismonthwastage($month, $year)
{
    $seller_id = $this->get_seller_id();
    $today = date('Y-m-d');
    $days = date("t", strtotime($today));
    $start_date = "$year-$month-01";
    $end_date = "$year-$month-$days";
    $join = $seller_id ? " JOIN products ON products.id = product_ledger.product_id AND products.seller_id = " . (int)$seller_id : "";
    $query = $this->db->query("SELECT sum(product_ledger.wastage*product_ledger.purchage_rate) as wastage FROM `product_ledger` $join WHERE ledger_type='Wastage' AND created_date BETWEEN '$start_date' AND '$end_date'
    ");

    $data = $query->row_array();

    return  $data['wastage'];
}

	
	public function thismontpurc($month, $year)
    {
		
        $this->db->select('SUM(total) as credit, SUM(pamnt) as pamnt',  FALSE);
		$today = date('Y-m-d');
		$days=date("t", strtotime($today));
        $where = "DATE(invoicedate) BETWEEN '$year-$month-01' AND '$year-$month-$days'";
		$this->db->where($where);
		
             if ($this->aauth->get_user()->roleid != 1) {
            $this->db->where('eid', $this->aauth->get_user()->id);
        }
        $this->db->from('geopos_purchase');
        $query = $this->db->get();
		
		
        return $query->row_array();
    }

    public function recent_payments()
    {
        $this->db->limit(13);
        $this->db->order_by('id', 'DESC');
              if ($this->aauth->get_user()->id !=1) {
            $this->db->where('eid', $this->aauth->get_user()->id);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $this->db->from('geopos_transactions');
        $query = $this->db->get();
		
		//echo $this->db->last_query();
        return $query->result_array();
    }

    public function stock()
    {
        $seller_id = $this->get_seller_id();
        $whr = '';
        if ($seller_id) {
            $whr = ' AND (products.seller_id=' . (int)$seller_id . ')';
        } elseif ($this->aauth->get_user()->loc) {
         $whr = ' AND (geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ')';
        } elseif (!BDATA) {
         $whr = ' AND (geopos_warehouse.loc=0)';
        }

        $query = $this->db->query("SELECT products.*,categories.name as catname, geopos_warehouse.title FROM products LEFT JOIN geopos_warehouse ON products.warehouse=geopos_warehouse.id LEFT JOIN categories ON  products.category_id=categories.id WHERE (products.stock<=products.total_allowed_quantity) $whr ORDER BY products.updated_date DESC");
        return $query->result_array();
    }



public function getcurrentmonthpay(){
	$seller_id = $this->get_seller_id();
	 /* $start_date = date('Y-m-01 00:00:00');
    $end_date = date('Y-m-d 23:59:59');  */

	$start_date = date('Y-m-01');
    $end_date = date('Y-m-d');
	$seller_where = $seller_id ? " AND eid = " . (int)$seller_id : "";
	$query = $this->db->query("SELECT sum(debit) as totalpay FROM `geopos_transactions` WHERE cat='Purchase' AND created_at >= '".$start_date."' AND created_at <= '".$end_date."'$seller_where; ");

        return $query->row_array();

}


public function get_current_month_reciev()
{
    $seller_id = $this->get_seller_id();
    $this->db->select('SUM(debit) AS total_expense');
    $this->db->from('geopos_transactions');
    $this->db->where('acid', 5);
    $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where('MONTH(date)', date('m'));
    $this->db->where('YEAR(date)', date('Y'));
    if ($seller_id) {
        $this->db->where('eid', $seller_id);
    }

    $query = $this->db->get();
    $result = $query->row();

    return $result->total_expense ? $result->total_expense : 0;
}
    public function todayItems($today)
    {
        $where = "DATE(invoicedate) ='$today'";
        $this->db->select_sum('items');
        $this->db->from('geopos_invoices');
              if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row()->items;
    }

    public function todayProfit($today)
    {
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select_sum('oi.sub_total', 'total_sales');
            $this->db->from('order_items oi');
            $this->db->join('orders o', 'o.id=oi.order_id', 'left');
            $this->db->where("DATE(o.date_added) ='$today'");
            $this->db->where('o.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $sales_row = $this->db->get()->row_array();
            $total_sales = isset($sales_row['total_sales']) ? (float)$sales_row['total_sales'] : 0;

            $sqls = "SELECT 
        oi.*, 
        p.article,
        (
            SELECT gpi.price
            FROM geopos_purchase_items gpi
            JOIN geopos_purchase gp ON gp.id = gpi.tid
            WHERE 
                (
                    gpi.product = oi.product_name 
                    OR gpi.pid = oi.product_id
                )
                AND gp.invoicedate <= DATE(o.date_added)
                AND gpi.price > 0
            ORDER BY gp.invoicedate DESC
            LIMIT 1
        ) AS purchase_price
    FROM order_items oi
    LEFT JOIN products p ON p.name = oi.product_name
    JOIN orders o ON o.id = oi.order_id
    WHERE o.is_deleted=0 AND DATE(o.date_added) = '$today' AND oi.seller_id = " . (int)$seller_id;

            $queryss = $this->db->query($sqls);
            $resultss = $queryss->result_array();
            $total_purchase_value = 0;
            foreach ($resultss as $row) {
                $con = convert_to_base_unit($row['variant_name']);
                $unit_qty = isset($con['qty']) ? $con['qty'] : 1;
                $unit_purchase_price = $row['purchase_price'] * $unit_qty;
                $puprice = $row['quantity'] * $unit_purchase_price;
                $total_purchase_value += $puprice;
            }
            return $total_sales - $total_purchase_value;
        }
        $where = "DATE(geopos_metadata.d_date) ='$today'";
        $this->db->select_sum('geopos_metadata.col1');
        $this->db->from('geopos_metadata');
        $this->db->join('geopos_invoices', 'geopos_metadata.rid=geopos_invoices.id', 'left');
        $this->db->where($where);
        $this->db->where('geopos_metadata.type', 9);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_invoices.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('geopos_invoices.loc', 0);
        }
        $query = $this->db->get();
        return $query->row()->col1;
    }

    public function incomeChart($today, $month, $year)
    {
        $whr = '';
             if ($this->aauth->get_user()->loc) {
         $whr = ' AND (loc=' . $this->aauth->get_user()->loc . ')';
        } elseif (!BDATA) {
         $whr = ' AND (loc=0)';
        }
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $query = $this->db->query("SELECT SUM(t.amount) AS total,t.date_created as date FROM transactions t LEFT JOIN order_items oi ON oi.id=t.order_item_id WHERE ((DATE(t.date_created) BETWEEN DATE('$year-$month-01') AND '$today') AND t.txntype='Sale' AND oi.seller_id=" . (int)$seller_id . ") $whr GROUP BY t.date_created ORDER BY t.date_created DESC");
        } else {
            $query = $this->db->query("SELECT SUM(amount) AS total,date_created as date FROM transactions WHERE ((DATE(date_created) BETWEEN DATE('$year-$month-01') AND '$today') AND txntype='Sale')  $whr GROUP BY date_created ORDER BY date_created DESC");
        }
		
	
		
        return $query->result_array();
    }

    public function expenseChart($today, $month, $year)
    {
        $whr = '';
                   if ($this->aauth->get_user()->loc) {
         $whr = ' AND (loc=' . $this->aauth->get_user()->loc . ')';
        } elseif (!BDATA) {
         $whr = ' AND (loc=0)';
        }
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $query = $this->db->query("SELECT SUM(debit) AS total,date FROM geopos_transactions WHERE ((DATE(date) BETWEEN DATE('$year-$month-01') AND '$today') AND type='Expense' AND eid=" . (int)$seller_id . ")  $whr GROUP BY date ORDER BY date DESC");
        } else {
            $query = $this->db->query("SELECT SUM(debit) AS total,date FROM geopos_transactions WHERE ((DATE(date) BETWEEN DATE('$year-$month-01') AND '$today') AND type='Expense')  $whr GROUP BY date ORDER BY date DESC");
        }
        return $query->result_array();
    }

    public function countmonthlyChart()
    {
        $today = date('Y-m-d');
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $query = $this->db->query("SELECT COUNT(DISTINCT o.id) AS ttlid,SUM(oi.sub_total) AS total,DATE(o.date_added) as date FROM order_items oi LEFT JOIN orders o ON o.id=oi.order_id WHERE (DATE(o.date_added) BETWEEN '$today' - INTERVAL 30 DAY AND '$today') AND o.is_deleted=0 AND oi.seller_id=" . (int)$seller_id . " GROUP BY DATE(o.date_added) ORDER BY date DESC");
            return $query->result_array();
        }
        $whr = '';
        if ($this->aauth->get_user()->loc) {
            $whr = ' AND (loc=' . $this->aauth->get_user()->loc . ')';
        } elseif (!BDATA) {
            $whr = ' AND (loc=0)';
        }
        $query = $this->db->query("SELECT COUNT(id) AS ttlid,SUM(total) AS total,DATE(invoicedate) as date FROM geopos_invoices WHERE (DATE(invoicedate) BETWEEN '$today' - INTERVAL 30 DAY AND '$today')  $whr GROUP BY DATE(invoicedate) ORDER BY date DESC");
        return $query->result_array();
    }


    public function monthlyInvoice($month, $year)
    {
        $today = date('Y-m-d');
		$days=date("t", strtotime($today));
        $where = "DATE(invoicedate) BETWEEN '$year-$month-01' AND '$year-$month-$days'";
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select('COUNT(DISTINCT o.id) as total', false);
            $this->db->from('order_items oi');
            $this->db->join('orders o', 'o.id=oi.order_id', 'left');
            $this->db->where("DATE(o.date_added) BETWEEN '$year-$month-01' AND '$year-$month-$days'");
            $this->db->where('o.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $row = $this->db->get()->row_array();
            return isset($row['total']) ? (int)$row['total'] : 0;
        }
        $this->db->where($where);
        $this->db->from('geopos_invoices');
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        return $this->db->count_all_results();

    }

    public function monthlySales($month, $year)
    {
        $today = date('Y-m-d');
		$days=date("t", strtotime($today));
        $where = "DATE(invoicedate) BETWEEN '$year-$month-01' AND '$year-$month-$days'";
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $this->db->select_sum('oi.sub_total', 'total');
            $this->db->from('order_items oi');
            $this->db->join('orders o', 'o.id=oi.order_id', 'left');
            $this->db->where("DATE(o.date_added) BETWEEN '$year-$month-01' AND '$year-$month-$days'");
            $this->db->where('o.is_deleted', 0);
            $this->db->where('oi.seller_id', $seller_id);
            $query = $this->db->get();
            return $query->row()->total;
        }
        $this->db->select_sum('total');
        $this->db->from('geopos_invoices');
        $this->db->where($where);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }


    public function recentInvoices()
    {
        $whr = '';
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $query = $this->db->query("SELECT i.id,i.date_added,i.total,i.status,i.orderdone_by,c.username,c.image,i.user_id
FROM orders AS i LEFT JOIN order_items oi ON oi.order_id=i.id LEFT JOIN users AS c ON i.user_id=c.id WHERE (i.is_deleted=0 AND oi.seller_id=" . (int)$seller_id . ") GROUP BY i.id ORDER BY i.id DESC LIMIT 10");
            return $query->result_array();
        }
        if ($this->aauth->get_user()->id !=1) {
            $whr = ' WHERE (i.created_by=' . $this->aauth->get_user()->id . ') ';
        } elseif (!BDATA) {
            $whr = ' WHERE (i.loc=0) ';
        }
        $query = $this->db->query("SELECT i.id,i.date_added,i.total,i.status,i.orderdone_by,c.username,c.image,i.user_id
FROM orders AS i LEFT JOIN users AS c ON i.user_id=c.id $whr ORDER BY i.id DESC LIMIT 10");
        return $query->result_array();

    }

        public function recentBuyers()
    {
        $this->db->trans_start();
        $seller_id = $this->get_seller_id();
        if ($seller_id) {
            $query = $this->db->query("SELECT MAX(o.id) AS iid,o.user_id AS csd,SUM(oi.sub_total) AS total,u.id AS cid,MAX(u.image) as picture,MAX(u.username) as name,MAX(o.status) as status FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id LEFT JOIN users u ON u.id=o.user_id WHERE (o.is_deleted=0 AND oi.seller_id=" . (int)$seller_id . ") GROUP BY o.user_id ORDER BY iid DESC LIMIT 10;");
            $result = $query->result_array();
        } else {
            $whr = '';
            if ($this->aauth->get_user()->loc) {
                $whr = ' WHERE (i.loc=' . $this->aauth->get_user()->loc . ') ';
            } elseif (!BDATA) {
                $whr = ' WHERE (i.loc=0) ';
            }
            $query = $this->db->query("SELECT MAX(i.id) AS iid,i.csd,SUM(i.total) AS total, c.cid,MAX(c.picture) as picture ,MAX(c.name) as name,MAX(i.status) as status FROM geopos_invoices AS i LEFT JOIN (SELECT geopos_customers.id AS cid, geopos_customers.picture AS picture, geopos_customers.name AS name FROM geopos_customers) AS c ON c.cid=i.csd $whr GROUP BY i.csd ORDER BY iid DESC LIMIT 10;");
            $result= $query->result_array();
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
{
        return 'sql';
}
        else
        {
            return $result;
        }

    }

    public function tasks($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_todolist');
        $this->db->where('eid', $id);
        $this->db->limit(10);
        $this->db->order_by('DATE(duedate)', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function clockin($id)
    {
        $this->db->select('clock');
        $this->db->where('id', $id);
        $this->db->from('geopos_employees');
        $query = $this->db->get();
        $emp = $query->row_array();
        if (!$emp['clock']) {
            $data = array(
                'clock' => 1,
                'clockin' => time(),
                'clockout' => 0
            );
            $this->db->set($data);
            $this->db->where('id', $id);
            $this->db->update('geopos_employees');
            $this->aauth->applog("[Employee ClockIn]  ID $id", $this->aauth->get_user()->username);
        }
        return true;
    }

    public function clockout($id)
    {

        $this->db->select('clock,clockin');
        $this->db->where('id', $id);
        $this->db->from('geopos_employees');
        $query = $this->db->get();
        $emp = $query->row_array();

        if ($emp['clock']) {

            $data = array(
                'clock' => 0,
                'clockin' => 0,
                'clockout' => time()
            );

            $total_time = time() - $emp['clockin'];


            $this->db->set($data);
            $this->db->where('id', $id);

            $this->db->update('geopos_employees');
            $this->aauth->applog("[Employee ClockOut]  ID $id", $this->aauth->get_user()->username);

            $today = date('Y-m-d');

            $this->db->select('id,adate');
            $this->db->where('emp', $id);
            $this->db->where('DATE(adate)', date('Y-m-d'));
            $this->db->from('geopos_attendance');
            $query = $this->db->get();
            $edate = $query->row_array();
            if ($edate['adate']) {


                $this->db->set('actual_hours', "actual_hours+$total_time", FALSE);
                $this->db->set('tto', date('H:i:s'));
                $this->db->where('id', $edate['id']);
                $this->db->update('geopos_attendance');


            } else {
                $data = array(
                    'emp' => $id,
                    'adate' => date('Y-m-d'),
                    'tfrom' => gmdate("H:i:s", $emp['clockin']),
                    'tto' => date('H:i:s'),
                    'note' => 'Self Attendance',
                    'actual_hours' => $total_time
                );


                $this->db->insert('geopos_attendance', $data);
            }

        }
        return true;
    }




public function warehouse()
{
    $where = '';
    $seller_id = $this->get_seller_id();
    if ($this->aauth->get_user()->loc) {
        $where = ' WHERE c.id=1 AND c.loc=' . $this->aauth->get_user()->loc;
        if (BDATA) $where = ' WHERE c.id=1 AND c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0';
    } elseif (!BDATA) {
        $where = ' WHERE  c.id=1 AND c.loc=0';
    }
    $seller_where = $seller_id ? ' WHERE pr.seller_id=' . (int)$seller_id : '';

    // PHP date range for current month
    $start_date = date('Y-m-01 00:00:00'); // 1st of current month
    $end_date = date('Y-m-d 23:59:59');    // today (current time)

    $query = $this->db->query("
        SELECT 
            c.*, 
            p.pc, 
            p.salessum, 
            p.worthsum, 
            p.qty 
        FROM 
            geopos_warehouse AS c 
        LEFT JOIN (
            SELECT 
                pr.warehouse,
                COUNT(pr.id) AS pc,
                SUM(pr.product_price * IFNULL(pl.balance, 0)) AS salessum,
                SUM(pr.purchase_price * IFNULL(pl.balance, 0)) AS worthsum,
                SUM(IFNULL(pl.balance, 0)) AS qty
            FROM 
                products pr
            LEFT JOIN (
                SELECT 
                    product_id,
                    SUM(purchage_qty) - SUM(sell_qty) - SUM(wastage) AS balance
                FROM 
                    product_ledger
                WHERE 
                    created_date >= '$start_date' AND created_date <= '$end_date'
                GROUP BY product_id
            ) AS pl ON pr.id = pl.product_id
            $seller_where
            GROUP BY pr.warehouse
        ) AS p ON c.id = p.warehouse
        $where
    ");

    $result =  $query->result_array();
	
	foreach($result as $rows){
		
		if($rows['id']==1){
			
			return $rows['worthsum'];
			
		}
		
		
	}
}
}
