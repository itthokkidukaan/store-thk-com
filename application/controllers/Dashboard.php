<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
            exit;
        }

        $this->load->model('dashboard_model');
        $this->load->model('accounts_model', 'accounts');
        $this->load->model('Home_model');
        $this->load->model('tools_model');
		 $this->load->model('transactions_model', 'transactions');

    }

    private function get_dashboard_seller_id()
    {
        if ($this->ion_auth->is_seller()) {
            return (int)$this->session->userdata('user_id');
        }

        return 0;
    }

    private function seller_has_any_permissions($user_id)
    {
        $perm_row = $this->db->select('permissions')
            ->from('seller_data')
            ->where('user_id', (int)$user_id)
            ->get()
            ->row_array();

        if (empty($perm_row['permissions'])) {
            return false;
        }

        $decoded = json_decode($perm_row['permissions'], true);

        return is_array($decoded) && !empty($decoded['permission_names']);
    }


    public function index()
    {
       $today = date("Y-m-d");
        $month = date("m");
        $year = date("Y");
        if ($this->aauth->get_user()->roleid == 4) {
            $user_id = $this->session->userdata('id') ?: $this->session->userdata('user_id');
            if (!$this->seller_has_any_permissions($user_id)) {
                redirect('no-access', 'refresh');
                return;
            }
        }
       
        if ($this->aauth->get_user()->roleid < 5) {
            $seller_id = $this->get_dashboard_seller_id();
            $data['todayin'] = $this->dashboard_model->todayInvoice($today);
            $data['todayprofit'] = $this->dashboard_model->todayProfit($today);
            $data['incomechart'] = $this->dashboard_model->incomeChart($today, $month, $year);
            $data['expensechart'] = $this->dashboard_model->expenseChart($today, $month, $year);
            $data['countmonthlychart'] = $this->dashboard_model->countmonthlyChart();
            $data['monthin'] = $this->dashboard_model->monthlyInvoice($month, $year);
            $data['todaysales'] = $this->dashboard_model->todaySales($today);
            $data['monthsales'] = $this->dashboard_model->monthlySales($month, $year);
            $data['todayinexp'] = $this->dashboard_model->todayInexp($today);
                $data['todayinsell'] = $this->dashboard_model->todayInexsell($today);
                $data['thismontsell'] = $this->dashboard_model->thismontsell($month, $year);
                $data['thismontpurc'] = $this->dashboard_model->thismontpurc($month, $year);
                $data['thismontpurcvalue'] = $this->dashboard_model->thismontpurchasevlue($month, $year);
                $data['wastage'] = $this->dashboard_model->thismonthwastage($month, $year);
                $data['currentmpay'] = $this->dashboard_model->getcurrentmonthpay();
                $data['recevedamount'] = $this->dashboard_model->get_current_month_reciev();
                $data['summary'] = get_inventory_summary(null, null, $seller_id);
				
				 // $data['accounts'] = $this->accounts->accountslist();
                $data['todayexpens'] = $this->dashboard_model->todayexpens($today);
            $data['recent_payments'] = $this->dashboard_model->recent_payments();
            $data['tasks'] = $this->dashboard_model->tasks($this->aauth->get_user()->id);
            $data['recent'] = $this->dashboard_model->recentInvoices();
            $data['balance'] = $this->accounts->account_dash();
            $data['recent_buy'] = $this->dashboard_model->recentBuyers();
            $data['goals'] = $this->tools_model->goals(1);
            $data['stock'] = $this->dashboard_model->stock();
			

            $head['usernm'] = $this->aauth->get_user()->username;
			$data['order_counter'] = $this->Home_model->count_new_orders('POS');
			$data['order_online'] = $this->Home_model->count_new_orders('0');
			$data['stock_values'] = get_stock_total_values_live($seller_id);


			$data['total_expense'] = get_total_expense_current_month($seller_id);
            $data['user_counter'] = $this->Home_model->count_new_users();
			$data['accounts'] = $this->transactions->acc_list();
            $data['delivery_boy_counter'] = $this->Home_model->count_delivery_boys();

$start_date = "$year-$month-01";

 $this->db->select('SUM(gsi.qty) AS total_return_qty, SUM(gsi.qty * gsi.price) AS total_return_value', FALSE);
    $this->db->from('geopos_stock_r_items gsi');
    $this->db->join('geopos_stock_r gs', 'gs.id = gsi.tid', 'left');
    if ($seller_id) {
        $this->db->join('products p', 'p.id = gsi.pid', 'left');
        $this->db->where('p.seller_id', $seller_id);
    }
    $this->db->where('gs.status', 'accepted');
    $this->db->where("DATE(gs.invoicedate) BETWEEN '$start_date' AND '$today'");
    $returnStock = $this->db->get()->row();
    $total_return_qty = $returnStock->total_return_qty ?? 0;
    $total_return_value = $returnStock->total_return_value ?? 0;
	
	$data['total_return_qty'] = $total_return_qty;
	$data['total_return_value'] = $total_return_value;
	
            $data['product_counter'] = $this->Home_model->count_products($seller_id);
            $head['title'] = 'Dashboard';
            $this->load->view('fixed/header', $head);
            $this->load->view('dashboard', $data);
            $this->load->view('fixed/footer');
        } else if ($this->aauth->premission(4)) {
            $this->load->model('projects_model', 'projects');
            $head['usernm'] = $this->aauth->get_user()->username;
            $head['title'] = 'Project List';
            $data['totalt'] = $this->projects->project_count_all();

            $this->load->view('fixed/header', $head);
            $this->load->view('projects/index', $data);
            $this->load->view('fixed/footer');
        } else if ($this->aauth->get_user()->roleid == 1) {
            $head['title'] = "Products";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/products');
            $this->load->view('fixed/footer');
        } else {
            $head['title'] = "Manage Invoices";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('invoices/invoices');
            $this->load->view('fixed/footer');
        }
    }
	
	
	
	/* public function filter_report()
{
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    // Format start and end date
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    $user = $this->aauth->get_user();



   // -------- TOTAL SALES --------
    $this->db->select('SUM(total) as total_sales');
    $this->db->from('orders');
    $this->db->where('is_deleted', 0);
	$this->db->where("DATE(date_added) BETWEEN '$start_date' AND '$end_date'");
    $total_sales = $this->db->get()->row()->total_sales ?? 0;

    // -------- TOTAL PURCHASE --------
    $this->db->select('SUM(total) as total_purchase');
    $this->db->from('geopos_purchase');
    $this->db->where('is_deleted', 0);
	$this->db->where("DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'");
    $total_purchase = $this->db->get()->row()->total_purchase ?? 0;
	
	
	
    // Total Expenses
    $this->db->select('SUM(debit) as total_expense');
    $this->db->where("DATE(date) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('payerid', $user->id);
    }
    $this->db->where('type', 'Expense');
    $this->db->from('geopos_transactions');
    $expenses = $this->db->get()->row();

 //this month sell pu value
 

	
	
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



    $queryss = $this->db->query($sqls, array($id));
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
	
	$purchase_data = round($total_purchase_value,2);
	
	
	$loc_where = '';
if ($this->aauth->get_user()->loc) {
    $loc_where = ' WHERE c.loc=' . $this->aauth->get_user()->loc;
    if (BDATA) $loc_where = ' WHERE c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0';
} elseif (!BDATA) {
    $loc_where = ' WHERE c.loc=0';
}

 
   $wheres = ' WHERE  c.id=1';
    if ($this->aauth->get_user()->loc) {
        $where = ' WHERE c.id=1';
   
        if (BDATA) $where = ' WHERE c.id=1 OR c.id=1';
    } elseif (!BDATA) {
        $where = ' WHERE  c.id=1';
    }
	
		
		$query = $this->db->query("SELECT 
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
                    SUM(purchage_qty) - SUM(sell_qty) AS balance
                FROM 
                    product_ledger
                WHERE 
                    created_date >= '$start_date' AND created_date <= '$end_date'
                GROUP BY product_id
            ) AS pl ON pr.id = pl.product_id
            GROUP BY pr.warehouse
        ) AS p ON c.id = p.warehouse
        $wheres");

$stock = $query->row_array();





$query = $this->db->query("SELECT SUM( oi.quantity * ( SELECT gpi.price FROM geopos_purchase_items gpi JOIN geopos_purchase gp ON gp.id = gpi.tid WHERE (gpi.product = oi.product_name OR gpi.pid = oi.product_id) AND gp.invoicedate <= DATE(o.date_added) AND gpi.price > 0 ORDER BY gp.invoicedate DESC LIMIT 1 ) ) AS purchase_price FROM order_items oi LEFT JOIN products p ON p.name = oi.product_name JOIN orders o ON o.id = oi.order_id WHERE o.date_added BETWEEN '$start_date' AND '$end_date'; ");

$summery = $query->row_array();




  $this->db->select('SUM(total) as credit, SUM(pamnt) as pamnt',  FALSE);
		
        $where = "DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'";
		$this->db->where($where);
		
             if ($this->aauth->get_user()->roleid != 1) {
            $this->db->where('eid', $this->aauth->get_user()->id);
        }
        $this->db->from('geopos_purchase');
        $myquery = $this->db->get();
		
		
        $thismontpurc = $myquery->row_array();
		
		
		
		  $this->db->select('SUM(final_total) as credit, SUM(pamnt) as pamnt', FALSE);
		
        $where = "DATE(date_added) BETWEEN '$start_date' AND '$end_date'";
		$this->db->where($where);
		
             if ($this->aauth->get_user()->roleid != 1) {
            $this->db->where('created_by', $this->aauth->get_user()->id);
        }
		 $this->db->where('orders.is_deleted', 0);
        $this->db->from('orders');
        $sellquery = $this->db->get();
        $thismontsell = $sellquery->row_array();
		
		
		
		
		$this->db->select('SUM(debit) AS total_expense');
    $this->db->from('geopos_transactions');
    $this->db->where('acid', 5);
    $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
 $where = "DATE(date) BETWEEN '$start_date' AND '$end_date'";
		$this->db->where($where);
    $query = $this->db->get();
    $result = $query->row();

    $recevedamount= $result->total_expense ? $result->total_expense : 0;
	

    $response = [
        'total_sales' => amountExchange($total_sales, 0, $user->loc),
        'total_purchase' => amountExchange($total_purchase, 0, $user->loc),
        'total_gp' => amountExchange($total_sales - $total_purchase, 0, $user->loc),
        'total_expense' => amountExchange($expenses->total_expense, 0, $user->loc),
        'total_stock' => amountExchange($total_purchase - $purchase_data, 0, $user->loc),
        'purchase_value' => amountExchange($summery['purchase_price'], 0, $user->loc),
        'thispurchase_value' => amountExchange($purchase_data, 0, $user->loc),
        'recevedamount' => amountExchange($recevedamount, 0, $user->loc),
        'total_np' => amountExchange($total_sales - $total_purchase - $expenses->total_expense, 0, $user->loc),
        'totalgp' => amountExchange($total_sales - $summery['purchase_price'], 0, $user->loc),
        'totalduepurchase' => amountExchange($thismontpurc['credit']-$thismontpurc['pamnt'], 0, $user->loc),
        'totalpaidpurchase' => amountExchange($thismontpurc['pamnt'], 0, $user->loc),
        'totalduesales' => amountExchange($thismontsell['credit']-$thismontsell['pamnt'], 0, $user->loc),
      
    ];

    echo json_encode($response);
}
 */
 
 
/*  public function filter_report()
{
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    // Format dates safely
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    $user = $this->aauth->get_user();

    // -------- TOTAL SALES --------
    $this->db->select('SUM(total) as total_sales');
    $this->db->from('orders');
    $this->db->where('is_deleted', 0);
    $this->db->where("DATE(date_added) BETWEEN '$start_date' AND '$end_date'");
    $total_sales = $this->db->get()->row()->total_sales ?? 0;

    // -------- TOTAL PURCHASE --------
    $this->db->select('SUM(total) as total_purchase');
    $this->db->from('geopos_purchase');
    $this->db->where('is_deleted', 0);
    $this->db->where("DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'");
    $total_purchase = $this->db->get()->row()->total_purchase ?? 0;

    // -------- TOTAL EXPENSE --------
    $this->db->select('SUM(debit) as total_expense');
    $this->db->where("DATE(date) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('payerid', $user->id);
    }
    $this->db->where('type', 'Expense');
    $this->db->from('geopos_transactions');
    $expenses = $this->db->get()->row();

    // -------- TOTAL PURCHASE VALUE BASED ON ORDERS --------
    $sqls = "SELECT 
        oi.*, 
        p.article,
        (
            SELECT gpi.price
            FROM geopos_purchase_items gpi
            JOIN geopos_purchase gp ON gp.id = gpi.tid
            WHERE 
                (gpi.product = oi.product_name OR gpi.pid = oi.product_id)
                AND gp.invoicedate <= DATE(o.date_added)
                AND gpi.price > 0
            ORDER BY gp.invoicedate DESC
            LIMIT 1
        ) AS purchase_price
    FROM order_items oi
    LEFT JOIN products p ON p.name = oi.product_name
    JOIN orders o ON o.id = oi.order_id
    WHERE o.is_deleted = 0 
    AND o.date_added BETWEEN '$start_date' AND '$end_date'";

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

    $purchase_data = round($total_purchase_value, 2);

    // -------- TOTAL STOCK --------
    $query = $this->db->query("SELECT 
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
                    SUM(purchage_qty) - SUM(sell_qty) AS balance
                FROM 
                    product_ledger
                WHERE 
                    created_date >= '$start_date' AND created_date <= '$end_date'
                GROUP BY product_id
            ) AS pl ON pr.id = pl.product_id
            GROUP BY pr.warehouse
        ) AS p ON c.id = p.warehouse
        WHERE c.id=1");

    $stock = $query->row_array();

    // -------- PURCHASE SUMMARY --------
    $query = $this->db->query("SELECT 
        SUM(oi.quantity * (
            SELECT gpi.price 
            FROM geopos_purchase_items gpi 
            JOIN geopos_purchase gp ON gp.id = gpi.tid 
            WHERE (gpi.product = oi.product_name OR gpi.pid = oi.product_id) 
            AND gp.invoicedate <= DATE(o.date_added) 
            AND gpi.price > 0 
            ORDER BY gp.invoicedate DESC 
            LIMIT 1
        )) AS purchase_price 
        FROM order_items oi 
        LEFT JOIN products p ON p.name = oi.product_name 
        JOIN orders o ON o.id = oi.order_id 
        WHERE o.date_added BETWEEN '$start_date' AND '$end_date'");

    $summery = $query->row_array();

    // -------- THIS MONTH PURCHASE --------
    $this->db->select('SUM(total) as credit, SUM(pamnt) as pamnt', FALSE);
    $this->db->where("DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('eid', $user->id);
    }
    $this->db->from('geopos_purchase');
    $thismontpurc = $this->db->get()->row_array();

    // -------- THIS MONTH SALES --------
    $this->db->select('SUM(final_total) as credit, SUM(pamnt) as pamnt', FALSE);
    $this->db->where("DATE(date_added) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('created_by', $user->id);
    }
    $this->db->where('orders.is_deleted', 0);
    $this->db->from('orders');
    $thismontsell = $this->db->get()->row_array();

    // -------- RECEIVED AMOUNT (Expense Category ID 5) --------
    $this->db->select('SUM(debit) AS total_expense');
    $this->db->from('geopos_transactions');
    $this->db->where('acid', 5);
    $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where("DATE(date) BETWEEN '$start_date' AND '$end_date'");
    $result = $this->db->get()->row();
    $recevedamount = $result->total_expense ? $result->total_expense : 0;

    // -------- TOTAL WASTAGE --------
    $this->db->select('SUM(qty * purchase_rate) as total_wastage', FALSE);
    $this->db->from('wastage');
    $this->db->where("DATE(created_date) BETWEEN '$start_date' AND '$end_date'");
    $wastage = $this->db->get()->row();
    $total_wastage = $wastage->total_wastage ? $wastage->total_wastage : 0;

    // -------- TOTAL RETURN STOCK --------
    $this->db->select('SUM(gsi.qty) AS total_return_qty, SUM(gsi.qty * gsi.price) AS total_return_value', FALSE);
    $this->db->from('geopos_stock_r_items gsi');
    $this->db->join('geopos_stock_r gs', 'gs.id = gsi.tid', 'left');
    $this->db->where('gs.status', 'accepted');
    $this->db->where("DATE(gs.invoicedate) BETWEEN '$start_date' AND '$end_date'");
    $returnStock = $this->db->get()->row();
    $total_return_qty = $returnStock->total_return_qty ?? 0;
    $total_return_value = $returnStock->total_return_value ?? 0;

    // -------- RESPONSE --------
    $response = [
        'total_sales'         => amountExchange($total_sales, 0, $user->loc),
        'total_purchase'      => amountExchange($total_purchase, 0, $user->loc),
        'total_expense'       => amountExchange($expenses->total_expense, 0, $user->loc),
        'total_stock'         => amountExchange($total_purchase - $purchase_data, 0, $user->loc),
        'purchase_value'      => amountExchange($summery['purchase_price'], 0, $user->loc),
        'consumption_stock'  => amountExchange($purchase_data, 0, $user->loc),
        'recevedamount'       => amountExchange($recevedamount, 0, $user->loc),
        'total_np'            => amountExchange($total_sales - $total_purchase - $expenses->total_expense, 0, $user->loc),
        'total_gp'             => amountExchange($total_sales - $purchase_data, 0, $user->loc),
        'total_due_purchase'    => amountExchange($thismontpurc['credit'] - $thismontpurc['pamnt'], 0, $user->loc),
        'total_paid_purchase'   => amountExchange($thismontpurc['pamnt'], 0, $user->loc),
        'total_wastage'       => amountExchange($total_wastage, 0, $user->loc),
        'total_return_qty'    => $total_return_qty,
        'total_return_stock'  => amountExchange($total_return_value, 0, $user->loc)
    ];

    echo json_encode($response);
} */


public function filter_report()
{
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));
    $user = $this->aauth->get_user();
    $seller_id = $this->get_dashboard_seller_id();

    if (!function_exists('dashboard_summarize_units')) {
        function dashboard_summarize_units($data_array)
        {
            $total_summary = [];

            foreach ($data_array as $item) {
                $unit = strtolower(trim($item['unit']));
                $qty = (float)$item['qty'];
                $converted = convert_to_base_unit($unit);
                $baseunit = $converted['unit'] ?? $unit;
                $conversion_qty = $converted['qty'] ?? 1;
                $final_qty = $qty * $conversion_qty;

                if (!isset($total_summary[$baseunit])) {
                    $total_summary[$baseunit] = 0;
                }

                $total_summary[$baseunit] += $final_qty;
            }

            $summary_texts = [];
            foreach ($total_summary as $unit => $total) {
                $summary_texts[] = number_format($total, 2) . ' ' . $unit;
            }

            return implode(', ', $summary_texts);
        }
    }

    if ($seller_id) {
        $this->db->select('SUM(oi.sub_total) as total_sales');
        $this->db->from('order_items oi');
        $this->db->join('orders o', 'o.id = oi.order_id', 'left');
        $this->db->where('o.is_deleted', 0);
        $this->db->where('oi.seller_id', $seller_id);
        $this->db->where("DATE(o.date_added) BETWEEN '$start_date' AND '$end_date'");
        $total_sales = $this->db->get()->row()->total_sales ?? 0;
    } else {
        $this->db->select('SUM(total) as total_sales');
        $this->db->from('orders');
        $this->db->where('is_deleted', 0);
        $this->db->where("DATE(date_added) BETWEEN '$start_date' AND '$end_date'");
        $total_sales = $this->db->get()->row()->total_sales ?? 0;
    }

    $this->db->select('SUM(total) as total_purchase');
    $this->db->from('geopos_purchase');
    $this->db->where('is_deleted', 0);
    $this->db->where("DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'");
    if ($seller_id || $user->roleid != 1) {
        $this->db->where('eid', $user->id);
    }
    $total_purchase = $this->db->get()->row()->total_purchase ?? 0;

    $this->db->select('SUM(debit) as total_expense');
    $this->db->where("DATE(date) BETWEEN '$start_date' AND '$end_date'");
    if ($seller_id || $user->roleid != 1) {
        $this->db->where('payerid', $user->id);
    }
    $this->db->where('type', 'Expense');
    $this->db->from('geopos_transactions');
    $expenses = $this->db->get()->row();
    $total_expense = $expenses->total_expense ?? 0;

    $sqls = "SELECT 
        oi.*, 
        p.article,
        (
            SELECT gpi.price
            FROM geopos_purchase_items gpi
            JOIN geopos_purchase gp ON gp.id = gpi.tid
            WHERE 
                (gpi.product = oi.product_name OR gpi.pid = oi.product_id)
                AND gp.invoicedate <= DATE(o.date_added)
                AND gpi.price > 0
            ORDER BY gp.invoicedate DESC
            LIMIT 1
        ) AS purchase_price
    FROM order_items oi
    LEFT JOIN products p ON p.name = oi.product_name
    JOIN orders o ON o.id = oi.order_id
    WHERE o.is_deleted = 0 
    AND o.date_added BETWEEN '$start_date' AND '$end_date'";
    if ($seller_id) {
        $sqls .= " AND oi.seller_id = " . (int)$seller_id;
    }

    $queryss = $this->db->query($sqls);
    $resultss = $queryss->result_array();
    $total_purchase_value = 0;

    foreach ($resultss as $row) {
        if (function_exists('convert_to_base_unit')) {
            $con = convert_to_base_unit($row['variant_name']);
            $unit_qty = isset($con['qty']) ? $con['qty'] : 1;
        } else {
            $unit_qty = 1;
        }

        $purchase_price_raw = isset($row['purchase_price']) ? (float)$row['purchase_price'] : 0;
        $unit_purchase_price = $purchase_price_raw * $unit_qty;
        $puprice = ((float)$row['quantity']) * $unit_purchase_price;

        $total_purchase_value += $puprice;
    }

    $purchase_data = round($total_purchase_value, 2);

    $stock_where = $seller_id ? ' WHERE pr.seller_id=' . (int)$seller_id : '';
    $query = $this->db->query("SELECT 
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
                    SUM(purchage_qty) - SUM(sell_qty) AS balance
                FROM 
                    product_ledger
                WHERE 
                    created_date >= '$start_date' AND created_date <= '$end_date'
                GROUP BY product_id
            ) AS pl ON pr.id = pl.product_id
            $stock_where
            GROUP BY pr.warehouse
        ) AS p ON c.id = p.warehouse
        WHERE c.id=1");

    $stock = $query->row_array();

    $summary_sql = "SELECT 
        SUM(oi.quantity * (
            SELECT gpi.price 
            FROM geopos_purchase_items gpi 
            JOIN geopos_purchase gp ON gp.id = gpi.tid 
            WHERE (gpi.product = oi.product_name OR gpi.pid = oi.product_id) 
            AND gp.invoicedate <= DATE(o.date_added) 
            AND gpi.price > 0 
            ORDER BY gp.invoicedate DESC 
            LIMIT 1
        )) AS purchase_price 
        FROM order_items oi 
        LEFT JOIN products p ON p.name = oi.product_name 
        JOIN orders o ON o.id = oi.order_id 
        WHERE o.date_added BETWEEN '$start_date' AND '$end_date'";
    if ($seller_id) {
        $summary_sql .= " AND oi.seller_id = " . (int)$seller_id;
    }
    $summery = $this->db->query($summary_sql)->row_array();

    $this->db->select('SUM(total) as credit, SUM(pamnt) as pamnt', FALSE);
    $this->db->where("DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'");
    if ($seller_id || $user->roleid != 1) {
        $this->db->where('eid', $user->id);
    }
    $this->db->from('geopos_purchase');
    $thismontpurc = $this->db->get()->row_array();

    if ($seller_id) {
        $this->db->select('SUM(oi.sub_total) as credit, 0 as pamnt', FALSE);
        $this->db->from('order_items oi');
        $this->db->join('orders o', 'o.id = oi.order_id', 'left');
        $this->db->where('o.is_deleted', 0);
        $this->db->where('oi.seller_id', $seller_id);
        $this->db->where("DATE(o.date_added) BETWEEN '$start_date' AND '$end_date'");
        $thismontsell = $this->db->get()->row_array();
    } else {
        $this->db->select('SUM(final_total) as credit, SUM(pamnt) as pamnt', FALSE);
        $this->db->where("DATE(date_added) BETWEEN '$start_date' AND '$end_date'");
        if ($user->roleid != 1) {
            $this->db->where('created_by', $user->id);
        }
        $this->db->where('orders.is_deleted', 0);
        $this->db->from('orders');
        $thismontsell = $this->db->get()->row_array();
    }

    $this->db->select('SUM(debit) AS total_expense');
    $this->db->from('geopos_transactions');
    $this->db->where('acid', 5);
    $this->db->where('type', 'Expense');
    $this->db->where('cat', 'Expenses');
    $this->db->where("DATE(date) BETWEEN '$start_date' AND '$end_date'");
    if ($seller_id || $user->roleid != 1) {
        $this->db->where('eid', $user->id);
    }
    $result = $this->db->get()->row();
    $recevedamount = $result->total_expense ?? 0;

    if ($seller_id) {
        $this->db->select('SUM(pl.wastage * pl.purchage_rate) as total_wastage', FALSE);
        $this->db->from('product_ledger pl');
        $this->db->join('products p', 'p.id = pl.product_id', 'left');
        $this->db->where('p.seller_id', $seller_id);
        $this->db->where("pl.created_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'");
        $wastage = $this->db->get()->row();
        $total_wastage = $wastage->total_wastage ?? 0;
    } else {
        $this->db->select('SUM(qty * purchase_rate) as total_wastage', FALSE);
        $this->db->from('wastage');
        $this->db->where("DATE(created_date) BETWEEN '$start_date' AND '$end_date'");
        $wastage = $this->db->get()->row();
        $total_wastage = $wastage->total_wastage ?? 0;
    }

    $this->db->select('SUM(gsi.qty) AS total_return_qty, SUM(gsi.qty * gsi.price) AS total_return_value', FALSE);
    $this->db->from('geopos_stock_r_items gsi');
    $this->db->join('geopos_stock_r gs', 'gs.id = gsi.tid', 'left');
    if ($seller_id) {
        $this->db->join('products p', 'p.id = gsi.pid', 'left');
        $this->db->where('p.seller_id', $seller_id);
    }
    $this->db->where('gs.status', 'accepted');
    $this->db->where("DATE(gs.invoicedate) BETWEEN '$start_date' AND '$end_date'");
    $returnStock = $this->db->get()->row();
    $total_return_qty = $returnStock->total_return_qty ?? 0;
    $total_return_value = $returnStock->total_return_value ?? 0;

    $range_start = date('Y-m-d 00:00:00', strtotime($start_date));
    $range_end = date('Y-m-d 23:59:59', strtotime($end_date));
    $seller_product_join = $seller_id ? " JOIN products p ON p.id = product_ledger.product_id AND p.seller_id = " . (int)$seller_id : "";
    $seller_purchase_join = $seller_id ? " JOIN products p ON p.id = gpi.pid AND p.seller_id = " . (int)$seller_id : "";
    $seller_return_join = $seller_id ? " JOIN products p ON p.id = gsi.pid AND p.seller_id = " . (int)$seller_id : "";

    $stock_units = $this->db->query("
        SELECT product_ledger.unit, SUM(product_ledger.purchage_qty) - SUM(product_ledger.sell_qty) - SUM(product_ledger.wastage) AS qty
        FROM product_ledger
        $seller_product_join
        WHERE product_ledger.created_date BETWEEN '$range_start' AND '$range_end'
        GROUP BY product_ledger.unit
    ")->result_array();

    $consume_units = $this->db->query("
        SELECT unit, SUM(sell_qty) AS qty
        FROM product_ledger
        $seller_product_join
        WHERE created_date BETWEEN '$range_start' AND '$range_end'
        GROUP BY unit
    ")->result_array();


    $purchase_units = $this->db->query("
        SELECT unit, SUM(qty) AS qty
        FROM geopos_purchase_items gpi
        JOIN geopos_purchase gp ON gp.id = gpi.tid
        $seller_purchase_join
        WHERE gp.is_deleted = 0
        AND DATE(gp.invoicedate) BETWEEN '$start_date' AND '$end_date'
        GROUP BY unit
    ")->result_array();

    $return_units = $this->db->query("
        SELECT unit, SUM(qty) AS qty
        FROM geopos_stock_r_items gsi
        JOIN geopos_stock_r gs ON gs.id = gsi.tid
        $seller_return_join
        WHERE gs.status='accepted'
        AND DATE(gs.invoicedate) BETWEEN '$start_date' AND '$end_date'
        GROUP BY unit
    ")->result_array();

    $waste_units = $this->db->query("
        SELECT product_ledger.unit, SUM(product_ledger.wastage) AS qty
        FROM product_ledger
        $seller_product_join
        WHERE created_date BETWEEN '$range_start' AND '$range_end'
        GROUP BY product_ledger.unit
    ")->result_array();

    $sales_units = $this->db->query("
        SELECT product_ledger.unit, SUM(product_ledger.sell_qty) AS qty
        FROM product_ledger
        $seller_product_join
        WHERE created_date BETWEEN '$range_start' AND '$range_end'
        GROUP BY product_ledger.unit
    ")->result_array();

    $stock_summary_text = dashboard_summarize_units($stock_units) . ' available';
    $consume_summary_text = dashboard_summarize_units($consume_units) . ' consumed';
    $purchase_summary_text = dashboard_summarize_units($purchase_units) . ' purchased';
    $return_summary_text = dashboard_summarize_units($return_units) . ' returned';
    $waste_summary_text = dashboard_summarize_units($waste_units) . ' wasted';
    $sales_summary_text = dashboard_summarize_units($sales_units) . ' sold';

    $response = [
        'total_sales'             => amountExchange($total_sales, 0, $user->loc),
        'total_purchase'          => amountExchange($total_purchase, 0, $user->loc),
        'total_expense'           => amountExchange($total_expense, 0, $user->loc),
        'total_stock'             => amountExchange((($stock['worthsum'] ?? 0) - $purchase_data), 0, $user->loc),
        'purchase_value'          => amountExchange($summery['purchase_price'] ?? 0, 0, $user->loc),
        'consumption_stock'       => amountExchange($purchase_data, 0, $user->loc),
        'recevedamount'           => amountExchange($recevedamount, 0, $user->loc),
        'total_np'                => amountExchange(($total_sales - $total_purchase - $total_expense), 0, $user->loc),
        'total_gp'                => amountExchange(($total_sales - $purchase_data), 0, $user->loc),
        'total_due_purchase'      => amountExchange((($thismontpurc['credit'] ?? 0) - ($thismontpurc['pamnt'] ?? 0)), 0, $user->loc),
        'total_paid_purchase'     => amountExchange(($thismontpurc['pamnt'] ?? 0), 0, $user->loc),
        'total_wastage'           => amountExchange($total_wastage, 0, $user->loc),
        'total_return_qty'        => $total_return_qty,
        'total_return_stock'      => amountExchange($total_return_value, 0, $user->loc),
        'total_wastage_percent'   => ($total_sales > 0) ? round(($total_wastage / $total_sales) * 100, 2) : 0,
        'total_return_percent'    => ($total_sales > 0) ? round(($total_return_value / $total_sales) * 100, 2) : 0,

        // Unit summaries
        'total_stock_summary'      => $stock_summary_text,
        'consumption_stock_summary'=> $consume_summary_text,
        'total_purchase_summary'   => $purchase_summary_text,
        'total_return_summary'     => $return_summary_text,
        'total_wastage_summary'    => $waste_summary_text,
        'total_sales_summary'      => $sales_summary_text,
    ];

    echo json_encode($response);
}



public function oldfilter_report()
{
    $start_date = $this->input->post('start_date');
    $end_date = $this->input->post('end_date');

    // Format start and end date
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    $user = $this->aauth->get_user();

    // Total Sales
    $this->db->select('SUM(final_total) as credit');
    $this->db->where("DATE(date_added) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('created_by', $user->id);
    }
    $this->db->where('orders.is_deleted', 0);
    $this->db->from('orders');
    $sales = $this->db->get()->row_array();

    // Total Purchase
    $this->db->select('SUM(total) as credit');
    $this->db->where("DATE(invoicedate) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('eid', $user->id);
    }
    $this->db->from('geopos_purchase');
    $purchase = $this->db->get()->row_array();



    // Total Expenses
    $this->db->select('SUM(debit) as total_expense');
    $this->db->where("DATE(date) BETWEEN '$start_date' AND '$end_date'");
    if ($user->roleid != 1) {
        $this->db->where('payerid', $user->id);
    }
    $this->db->where('type', 'Expense');
    $this->db->from('geopos_transactions');
    $expenses = $this->db->get()->row();

 
	
	
	$loc_where = '';
if ($this->aauth->get_user()->loc) {
    $loc_where = ' WHERE c.loc=' . $this->aauth->get_user()->loc;
    if (BDATA) $loc_where = ' WHERE c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0';
} elseif (!BDATA) {
    $loc_where = ' WHERE c.loc=0';
}

 
   $wheres = ' WHERE  c.id=1';
    if ($this->aauth->get_user()->loc) {
        //$where = ' WHERE c.loc=' . $this->aauth->get_user()->loc;
        $where = ' WHERE c.id=1';
      //  if (BDATA) $where = ' WHERE c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0';
        if (BDATA) $where = ' WHERE c.id=1 OR c.id=1';
    } elseif (!BDATA) {
        $where = ' WHERE  c.id=1';
    }
	
/* $query = $this->db->query("SELECT 
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
            GROUP BY pr.warehouse
        ) AS p ON c.id = p.warehouse
        $wheres"); */
		
		
		$query = $this->db->query("SELECT 
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
                    SUM(purchage_qty) - SUM(sell_qty) AS balance
                FROM 
                    product_ledger
                WHERE 
                    created_date >= '$start_date' AND created_date <= '$end_date'
                GROUP BY product_id
            ) AS pl ON pr.id = pl.product_id
            GROUP BY pr.warehouse
        ) AS p ON c.id = p.warehouse
        $wheres");

$stock = $query->row_array();





$query = $this->db->query("SELECT SUM( oi.quantity * ( SELECT gpi.price FROM geopos_purchase_items gpi JOIN geopos_purchase gp ON gp.id = gpi.tid WHERE (gpi.product = oi.product_name OR gpi.pid = oi.product_id) AND gp.invoicedate <= DATE(o.date_added) AND gpi.price > 0 ORDER BY gp.invoicedate DESC LIMIT 1 ) ) AS purchase_price FROM order_items oi LEFT JOIN products p ON p.name = oi.product_name JOIN orders o ON o.id = oi.order_id WHERE o.date_added BETWEEN '$start_date' AND '$end_date'; ");

$summery = $query->row_array();

//echo $stock['worthsum'];




    $response = [
        'total_sales' => amountExchange($sales['credit'], 0, $user->loc),
        'total_purchase' => amountExchange($purchase['credit'], 0, $user->loc),
        'total_gp' => amountExchange($sales['credit'] - $purchase['credit'], 0, $user->loc),
        'total_expense' => amountExchange($expenses->total_expense, 0, $user->loc),
        'total_stock' => amountExchange($stock['worthsum'], 0, $user->loc),
        'purchase_value' => amountExchange($summery['purchase_price'], 0, $user->loc),
        'total_np' => amountExchange($sales['credit'] - $purchase['credit'] - $expenses->total_expense, 0, $user->loc),
        'totalgp' => amountExchange($sales['credit'] - $summery['purchase_price'], 0, $user->loc)
    ];

    echo json_encode($response);
}


    public function clock_in()
    {
        $id = $this->aauth->get_user()->id;
        if ($this->aauth->auto_attend()) {
            $this->dashboard_model->clockin($id);
        }

        redirect('dashboard');
    }

    public function clock_out()
    {
        $id = $this->aauth->get_user()->id;

        if ($this->aauth->auto_attend()) {
            $this->dashboard_model->clockout($id);
        }


        redirect('dashboard');
    }
}
