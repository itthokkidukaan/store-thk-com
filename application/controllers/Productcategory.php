<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Productcategory extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model', 'products_cat');
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
        $data['cat'] = $this->products_cat->category_lists();
        $head['title'] = "Product Categories";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/category', $data);
        $this->load->view('fixed/footer');
    }

    public function warehouse()
    {
        $data['cat'] = $this->products_cat->warehouse();
		
		
        $head['title'] = "Product Warehouse";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/warehouse', $data);
        $this->load->view('fixed/footer');
		
    }  

	public function wastage()
    {
        $data['cat'] = $this->products_cat->warehouse();
		
		
        $head['title'] = "wastage Product";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/wastage', $data);
        $this->load->view('fixed/footer');
		
    }
	
	
	
	public function wastage_list()
{
    $this->load->model('categories_model', 'pcat');
    $list = $this->pcat->get_wastage_datatables();
    $data = array();
    $no = $_POST['start'];

    foreach ($list as $w) {
        $no++;
        $row = array();
        $row['sr_no'] = $no;
        $row['product_name'] = $w->product_name;
        $row['qty'] = $w->qty . ' ' . $w->unit;
        $row['sell_value'] = number_format($w->sell_rate * $w->qty, 2);
        $row['purchase_value'] = number_format($w->purchase_rate * $w->qty, 2);
        $row['wastage_date'] = date('d-m-Y', strtotime($w->created_date));
        $row['wastage_reson'] = $w->wastage_reson;
        $data[] = $row;
    }

    $output = array(
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $this->pcat->count_all_wastage(),
        "recordsFiltered" => $this->pcat->count_filtered_wastage(),
        "data" => $data,
    );
    echo json_encode($output);
}

	
	
	
	 public function update_stock() {
        // Start transaction
        $this->db->trans_start();

        // Step 1: Set variables separately (MariaDB/MySQL restriction workaround)
        $this->db->query("SET @prev_close_stock = 0");
        $this->db->query("SET @current_product = NULL");

        // Step 2: Update open_stock and close_stock in product_ledger
    /*     $query1 = "
            UPDATE product_ledger pl 
            JOIN (
                SELECT 
                    id, 
                    product_id, 
                    ledger_type, 
                    sell_qty, 
                    purchage_qty, 
                    created_date,
                    -- Open Stock Calculation
                    CASE 
                        WHEN @current_product = product_id THEN @prev_close_stock 
                        ELSE (@prev_close_stock := 0) 
                    END AS open_stock,
                    -- Close Stock Calculation
                    @prev_close_stock := CASE 
                        WHEN ledger_type = 'Sell' THEN @prev_close_stock - sell_qty 
                        WHEN ledger_type = 'Restock' THEN @prev_close_stock + purchage_qty 
                        WHEN ledger_type = 'purchage' THEN @prev_close_stock + purchage_qty 
                        ELSE @prev_close_stock 
                    END AS close_stock,
                    @current_product := product_id 
                FROM product_ledger 
                ORDER BY product_id, created_date ASC, id ASC
            ) AS temp ON pl.id = temp.id 
            SET pl.open_stock = temp.open_stock, 
                pl.close_stock = temp.close_stock;
        ";
        $this->db->query($query1); */

        // Step 3: Update stock in products table
        $query2 = "
            UPDATE products p
            JOIN (
                -- Get latest close_stock for each product_id
                SELECT pl.product_id, pl.close_stock
                FROM product_ledger pl
                INNER JOIN (
                    SELECT product_id, MAX(created_date) AS latest_date
                    FROM product_ledger
                    GROUP BY product_id
                ) latest ON pl.product_id = latest.product_id AND pl.created_date = latest.latest_date
            ) latest_stock ON p.id = latest_stock.product_id
            SET p.stock = latest_stock.close_stock;
        ";
        $this->db->query($query2);

        // Complete transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo "Stock update failed!";
        } else {
           // echo "Stock updated successfully!";
        }
    }



    public function view()
    {
        $data['id'] = $this->input->get('id');
        $data['sub'] = $this->input->get('sub');
        $data['cat'] = $this->products_cat->category_sub_stock($data['id']);
        $head['title'] = "View Product Category";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/category_view', $data);
        $this->load->view('fixed/footer');
    }

    public function viewwarehouse()
    {
        $data['cat'] = $this->products_cat->warehouse();
        $head['title'] = "View Product Warehouses";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/warehouse_view', $data);
        $this->load->view('fixed/footer');
		//$this->update_stock();
    }

    public function add()
    {
        $data['cat'] = $this->products_cat->category_list();
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->locations_list2();
        $head['title'] = "Add Product Category";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/category_add', $data);
        $this->load->view('fixed/footer');
    }

    public function add_sub()
    {
        $data['cat'] = $this->products_cat->category_list();
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->locations_list2();
        $head['title'] = "Add Product Category";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/category_add_sub', $data);
        $this->load->view('fixed/footer');
    }

    public function addwarehouse()
    {
        if ($this->input->post()) {
            $cat_name = $this->input->post('product_catname');
            $cat_desc = $this->input->post('product_catdesc');
            $lid = $this->input->post('lid');
            if ($this->aauth->get_user()->loc) {
                if ($lid == 0 or $this->aauth->get_user()->loc == $lid) {

                } else {
                    exit();
                }
            }

            if ($cat_name) {

                $this->products_cat->addwarehouse($cat_name, $cat_desc, $lid);
            }
        } else {
            $this->load->model('locations_model');
            $data['locations'] = $this->locations_model->locations_list2();
            $data['cat'] = $this->products_cat->category_list();
            $head['title'] = "Add Product Warehouse";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/warehouse_add', $data);
            $this->load->view('fixed/footer');
        }
    }

    public function addcat()
    {
        $cat_name = $this->input->post('product_catname', true);
        $cat_desc = $this->input->post('product_catdesc', true);
        $cat_type = $this->input->post('cat_type', true);
        $cat_rel = $this->input->post('cat_rel', true);
        if ($cat_name) {
            $this->products_cat->addnew($cat_name, $cat_desc, $cat_type, $cat_rel);
        }
    }


    public function delete_i()
    {
        if ($this->aauth->premission(11)) {
            $id = intval($this->input->post('deleteid'));
            if ($id) {

                $query = $this->db->query("DELETE geopos_movers FROM geopos_movers LEFT JOIN geopos_products ON  geopos_movers.rid1=geopos_products.pid LEFT JOIN geopos_product_cat ON  geopos_products.pcat=geopos_product_cat.id WHERE geopos_product_cat.id='$id' AND  geopos_movers.d_type='1'");

                $this->db->delete('geopos_products', array('pcat' => $id));
                $this->db->delete('geopos_product_cat', array('id' => $id));
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Product Category with products')));
            } else {
                echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }
    }

    public function delete_i_sub()
    {
        if ($this->aauth->premission(11)) {
            $id = intval($this->input->post('deleteid'));
            if ($id) {

                $query = $this->db->query("DELETE geopos_movers FROM geopos_movers LEFT JOIN geopos_products ON  geopos_movers.rid1=geopos_products.pid LEFT JOIN geopos_product_cat ON  geopos_products.sub_id=geopos_product_cat.id WHERE geopos_product_cat.id='$id' AND  geopos_movers.d_type='1'");

                $this->db->delete('geopos_products', array('sub_id' => $id));
                $this->db->delete('geopos_product_cat', array('id' => $id));
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Product Category with products')));
            } else {
                echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function delete_warehouse()
    {
        if ($this->aauth->premission(11)) {
            $id = $this->input->post('deleteid');
            if ($id) {
                $this->db->delete('geopos_products', array('warehouse' => $id));
                $this->db->delete('geopos_warehouse', array('id' => $id));
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('Product Warehouse with products')));
            } else {
                echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }
    }

//view for edit
    public function edit()
    {
        $catid = $this->input->get('id');
        $this->db->select('*');
        $this->db->from('categories');
        $this->db->where('id', $catid);
        $query = $this->db->get();
        $data['productcat'] = $query->row_array();
        $data['cat'] = $this->products_cat->category_list();

        $head['title'] = "Edit Product Category";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/product-cat-edit', $data);
        $this->load->view('fixed/footer');

    }

    public function editwarehouse()
    {
        if ($this->input->post()) {
            $cid = $this->input->post('catid');
            $cat_name = $this->input->post('product_cat_name', true);
            $cat_desc = $this->input->post('product_cat_desc', true);
            $lid = $this->input->post('lid');

            if ($this->aauth->get_user()->loc) {
                if ($lid == 0 or $this->aauth->get_user()->loc == $lid) {

                } else {
                    exit();
                }
            }


            if ($cat_name) {

                $this->products_cat->editwarehouse($cid, $cat_name, $cat_desc, $lid);
            }
        } else {
            $catid = $this->input->get('id');
            $this->db->select('*');
            $this->db->from('geopos_warehouse');
            $this->db->where('id', $catid);
            $query = $this->db->get();
            $data['warehouse'] = $query->row_array();
            $this->load->model('locations_model');
            $data['locations'] = $this->locations_model->locations_list2();
            $head['title'] = "Edit Product Warehouse";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/product-warehouse-edit', $data);
            $this->load->view('fixed/footer');
        }

    }

    public function editcat()
    {
        $cid = $this->input->post('catid');
        $product_cat_name = $this->input->post('product_cat_name');
        $product_cat_desc = $this->input->post('product_cat_desc');
        $cat_type = $this->input->post('cat_type', true);
        $cat_rel = $this->input->post('cat_rel', true);
        $old_cat_type = $this->input->post('old_cat_type', true);
        if ($cid) {
            $this->products_cat->edit($cid, $product_cat_name, $product_cat_desc, $cat_type, $cat_rel, $old_cat_type);
        }
    }


    public function report_product()
    {
        $pid = intval($this->input->post('id'));

        $r_type = intval($this->input->post('r_type'));
        $s_date = datefordatabase($this->input->post('s_date'));
        $e_date = datefordatabase($this->input->post('e_date'));
        $sub_date = $this->input->post('sub');
        $filter = 'pcat';
        if ($sub_date) $filter = 'sub_id';

        if ($pid && $r_type) {
            $qj = '';
            $wr = '';
            if ($this->aauth->get_user()->loc) {
                $qj = "LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id";

                $wr = " AND geopos_warehouse.loc='" . $this->aauth->get_user()->loc . "'";
            }


            switch ($r_type) {
                case 1 :
                    $query = $this->db->query("SELECT geopos_invoices.tid,geopos_invoice_items.qty,geopos_invoice_items.price,geopos_invoices.invoicedate FROM geopos_invoice_items LEFT JOIN geopos_invoices ON geopos_invoices.id=geopos_invoice_items.tid LEFT JOIN geopos_products ON geopos_products.pid=geopos_invoice_items.pid  LEFT JOIN geopos_product_cat ON geopos_product_cat.id=geopos_products.$filter  $qj WHERE geopos_invoices.status!='canceled' AND (DATE(geopos_invoices.invoicedate) BETWEEN DATE('$s_date') AND DATE('$e_date')) AND geopos_products.$filter='$pid' $wr");
                    $result = $query->result_array();
                    break;

                case 2 :
                   $query = $this->db->query("
    SELECT 
        geopos_purchase.tid,
        geopos_purchase_items.qty,
        geopos_purchase_items.price,
        geopos_purchase.invoicedate
    FROM geopos_purchase_items
    LEFT JOIN geopos_purchase 
        ON geopos_purchase.id = geopos_purchase_items.tid
    LEFT JOIN products 
        ON products.id = geopos_purchase_items.pid
    LEFT JOIN categories 
        ON categories.id = products.$filter
    WHERE geopos_purchase.status != 'canceled'
        AND (DATE(geopos_purchase.invoicedate) BETWEEN DATE('$s_date') AND DATE('$e_date'))
        AND products.$filter = '$pid'
");
$result = $query->result_array();

echo $this->db->last_query();
exit();
                    break;

                case 3 :
                    $query = $this->db->query("SELECT geopos_movers.rid2 AS qty, DATE(geopos_movers.d_time) AS  invoicedate,geopos_movers.note,geopos_products.product_price AS price,geopos_products.product_name   FROM geopos_movers LEFT JOIN geopos_products ON geopos_products.pid=geopos_movers.rid1  WHERE geopos_movers.d_type='1' AND geopos_products.$filter='$pid'  AND (DATE(geopos_movers.d_time) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;
            }
            $this->db->select('*');
            $this->db->from('geopos_product_cat');
            $this->db->where('id', $pid);
            $query = $this->db->get();
            $product = $query->row_array();

            $html = $this->load->view('products/cat_statementpdf-ltr', array('report' => $result, 'product' => $product, 'r_type' => $r_type), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($pid . 'report.pdf', 'I');
        } else {
            $pid = intval($this->input->get('id'));
            $sub = $this->input->get('sub');
            $this->db->select('*');
            $this->db->from('geopos_product_cat');
            $this->db->where('id', $pid);
            $query = $this->db->get();
            $product = $query->row_array();

            $head['title'] = "Product Sales";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/cat_statement', array('id' => $pid, 'product' => $product, 'sub' => $sub));
            $this->load->view('fixed/footer');
        }
    }

   /*  public function warehouse_report()
    {
        $pid = intval($this->input->post('id'));

        $r_type = intval($this->input->post('r_type'));
        $s_date = datefordatabase($this->input->post('s_date'));
        $e_date = datefordatabase($this->input->post('e_date'));
        $reportwise = intval($this->input->post('reportwise'));
$unit_summary = [];
$total_amount = 0;
        if ($pid && $r_type) {
            $qj = '';
            $wr = '';
            if ($this->aauth->get_user()->loc) {
                $qj = "LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id";

                $wr = " AND geopos_warehouse.loc='" . $this->aauth->get_user()->loc . "'";
            }

            switch ($r_type) {
                case 1 :
          


				
    if ($reportwise == 1) { // 📅 Date Wise
        $query = $this->db->query("
            SELECT 
                DATE(orders.date_added) as date,
                order_items.product_name,
                SUM(order_items.quantity) as qty,
                SUM(order_items.sub_total) as sub_total,order_items.variant_name as unit
            FROM order_items
            LEFT JOIN orders ON orders.id = order_items.order_id
            LEFT JOIN products ON products.id = order_items.product_id
            $qj
            WHERE orders.is_deleted != '1'
                AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                AND products.warehouse = '$pid'
                $wr
            GROUP BY DATE(orders.date_added), order_items.product_name
            ORDER BY DATE(orders.date_added), order_items.product_name
        ");
    } elseif ($reportwise == 2) { // 📦 Product Wise
        $query = $this->db->query("
            SELECT 
                order_items.product_name,
                SUM(order_items.quantity) as qty,
                SUM(order_items.sub_total) as sub_total, order_items.variant_name as unit
            FROM order_items
            LEFT JOIN orders ON orders.id = order_items.order_id
            LEFT JOIN products ON products.id = order_items.product_id
            $qj
            WHERE orders.is_deleted != '1'
                AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                AND products.warehouse = '$pid'
                $wr
            GROUP BY order_items.product_name
            ORDER BY order_items.product_name
        ");
    } else {
      
        $query = $this->db->query("SELECT order_items.* FROM order_items LIMIT 0"); 
    }
    $result = $query->result_array();


foreach ($result as $row) {
    $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit']); // Or 'unit' if already separate
    $qty = (float) $row['qty'];
    $subtotal = (float) $row['sub_total'];

    if (!isset($unit_summary[$unit])) {
        $unit_summary[$unit] = 0;
    }

    $unit_summary[$unit] += $qty;
    $total_amount += $subtotal;
}
                    break;

                case 2 :
                
					
					 if ($reportwise == 1) {
						 
						  $query = $this->db->query("
            SELECT 
                DATE(geopos_purchase.invoicedate) as date,
                products.name as product_name,
                geopos_purchase_items.unit as unit,
                SUM(geopos_purchase_items.qty) as qty,
                SUM(geopos_purchase_items.price * geopos_purchase_items.qty) as sub_total
            FROM geopos_purchase_items
            LEFT JOIN geopos_purchase ON geopos_purchase.id = geopos_purchase_items.tid
            LEFT JOIN products ON products.id = geopos_purchase_items.pid
            WHERE geopos_purchase.status != 'canceled'
                AND DATE(geopos_purchase.invoicedate) BETWEEN '$s_date' AND '$e_date'
            GROUP BY DATE(geopos_purchase.invoicedate), products.name, geopos_purchase_items.unit
            ORDER BY DATE(geopos_purchase.invoicedate), products.name
        ");
						
					 }elseif($reportwise == 2){
						 
						  $query = $this->db->query("
            SELECT 
                products.name as product_name,
                geopos_purchase_items.unit as unit,
                SUM(geopos_purchase_items.qty) as qty,
                SUM(geopos_purchase_items.price * geopos_purchase_items.qty) as sub_total
            FROM geopos_purchase_items
            LEFT JOIN geopos_purchase ON geopos_purchase.id = geopos_purchase_items.tid
            LEFT JOIN products ON products.id = geopos_purchase_items.pid
            WHERE geopos_purchase.status != 'canceled'
                AND DATE(geopos_purchase.invoicedate) BETWEEN '$s_date' AND '$e_date'
            GROUP BY products.name, geopos_purchase_items.unit
            ORDER BY products.name
        ");
						 
					 }
			 $result = $query->result_array();



$unit_summary = [];  // unit => qty
$total_amount = 0;

foreach ($result as $row) {
    $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit']);
    $qty = (float) $row['qty'];
    $sub_total = (float) $row['sub_total'];

    // Add to unit summary
    if (!isset($unit_summary[$unit])) {
        $unit_summary[$unit] = 0;
    }
    $unit_summary[$unit] += $qty;

    $total_amount += $sub_total;
}
			 
				
                    break;

                case 3 :
                    $query = $this->db->query("SELECT geopos_movers.rid2 AS qty, DATE(geopos_movers.d_time) AS  invoicedate,geopos_movers.note,geopos_products.product_price AS price,geopos_products.product_name  FROM geopos_movers LEFT JOIN geopos_products ON geopos_products.pid=geopos_movers.rid1  WHERE geopos_movers.d_type='1' AND geopos_products.warehouse='$pid'  AND (DATE(geopos_movers.d_time) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;
            }


            $this->db->select('*');
            $this->db->from('geopos_warehouse');
            $this->db->where('id', $pid);
            $query = $this->db->get();
            $product = $query->row_array();

            $html = $this->load->view('products/ware_statementpdf-ltr', array('report' => $result, 'product' => $product, 'r_type' => $r_type,'reportwise' => $reportwise,'unit_summary' => $unit_summary,
    'total_amount' => $total_amount), true);
            ini_set('memory_limit', '64M');


            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($pid . 'report.pdf', 'I');
        } else {
            $pid = intval($this->input->get('id'));
            $this->db->select('*');
            $this->db->from('geopos_warehouse');
            $this->db->where('id', $pid);
            $query = $this->db->get();
            $product = $query->row_array();

            $head['title'] = "Product Sales";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/ware_statement', array('id' => $pid, 'product' => $product));
            $this->load->view('fixed/footer');
        }
    } */



public function warehouse_report_old()
{
    $pid = intval($this->input->post('id'));
    $r_type = intval($this->input->post('r_type'));
    $reportwise = intval($this->input->post('reportwise'));
    $s_date = datefordatabase($this->input->post('s_date'));
    $e_date = datefordatabase($this->input->post('e_date'));
    $party_id = intval($this->input->post('party_id'));
    $party_name = "";

    if ($reportwise == 3) {
        $customer = $this->products_cat->get_customer($party_id);
        $party_name = $customer['name'] . ' (' . $customer['mobile'] . ')';
    } else if ($reportwise == 4) {
        $supplier = $this->products_cat->get_supplier($party_id);
        $party_name = $supplier['name'] . ' (' . $supplier['mobile'] . ')';
    }

    $unit_summary = [];
    $total_amount = 0;

    if ($pid && $r_type) {
        $qj = '';
        $wr = '';
        if ($this->aauth->get_user()->loc) {
            $qj = "LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id";
            $wr = " AND geopos_warehouse.loc='" . $this->aauth->get_user()->loc . "'";
        }

        switch ($r_type) {
            case 1: // Sales
                if ($reportwise == 1) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(orders.date_added) as date,
                            order_items.product_name,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            order_items.variant_name as unit
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $wr
                        GROUP BY DATE(orders.date_added), order_items.product_name
                        ORDER BY DATE(orders.date_added), order_items.product_name
                    ");
                } elseif ($reportwise == 2) {
                    $query = $this->db->query("
                        SELECT 
                            order_items.product_name,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            order_items.variant_name as unit
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $wr
                        GROUP BY order_items.product_name
                        ORDER BY order_items.product_name
                    ");
                } elseif ($reportwise == 3 && $party_id) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(orders.date_added) as date,
                            order_items.product_name,
                            order_items.variant_name as unit,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            orders.id as tid
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND orders.user_id = '$party_id'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $wr
                        GROUP BY orders.id, order_items.product_name
                        ORDER BY orders.date_added
                    ");
                } else {
                    $query = $this->db->query("SELECT order_items.* FROM order_items LIMIT 0");
                }
                break;

            case 2: // Purchase
                if ($reportwise == 1) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(p.invoicedate) as date,
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total,
                            p.id as tid
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                        GROUP BY DATE(p.invoicedate), pr.name, pi.unit
                        ORDER BY p.invoicedate
                    ");
                } elseif ($reportwise == 2) {
                    $query = $this->db->query("
                        SELECT 
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                        GROUP BY pr.name, pi.unit
                        ORDER BY pr.name
                    ");
                } elseif ($reportwise == 4 && $party_id) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(p.invoicedate) as date,
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total,
                            p.id as tid
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND p.csd = '$party_id'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                        GROUP BY p.id, pr.name
                        ORDER BY p.invoicedate
                    ");
                } else {
                    $query = $this->db->query("SELECT * FROM geopos_purchase_items LIMIT 0");
                }
                break;

            case 3: // Stock Transfer
                $query = $this->db->query("
                    SELECT 
                        geopos_movers.rid2 AS qty,
                        DATE(geopos_movers.d_time) AS invoicedate,
                        geopos_movers.note,
                        geopos_products.product_price AS price,
                        geopos_products.product_name
                    FROM geopos_movers
                    LEFT JOIN geopos_products ON geopos_products.pid = geopos_movers.rid1
                    WHERE geopos_movers.d_type = '1'
                        AND geopos_products.warehouse = '$pid'
                        AND DATE(geopos_movers.d_time) BETWEEN DATE('$s_date') AND DATE('$e_date')
                ");
                break;

           case 4: // Stock Summary
    if ($reportwise == 2) { // Product Wise
    $sql = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    pl.unit AS unit,

    -- Opening Stock (all transactions before start date)
    COALESCE(SUM(
        CASE 
            WHEN pl.created_date < ? THEN
                CASE 
                    WHEN pl.ledger_type IN ('purchage','Restock') THEN pl.purchage_qty
                    WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                    WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                    ELSE 0
                END
            ELSE 0
        END
    ), 0) AS opening_stock,

    -- Stock In (between start and end date)
    COALESCE(SUM(
        CASE 
            WHEN pl.created_date BETWEEN ? AND ? 
                 AND pl.ledger_type IN ('purchage','Restock') THEN pl.purchage_qty
            ELSE 0
        END
    ),0) AS stock_in,

    -- Stock Out (between start and end date)
    COALESCE(SUM(
        CASE 
            WHEN pl.created_date BETWEEN ? AND ? 
                 AND pl.ledger_type = 'Sell' THEN pl.sell_qty
            ELSE 0
        END
    ),0) AS stock_out,

    -- Waste (between start and end date)
    COALESCE(SUM(
        CASE 
            WHEN pl.created_date BETWEEN ? AND ? 
                 AND pl.ledger_type = 'Wastage' THEN pl.wastage
            ELSE 0
        END
    ),0) AS waste,

    -- Closing Stock = net of all transactions up to end date (inclusive)
    COALESCE(SUM(
        CASE 
            WHEN pl.created_date <= ? THEN
                CASE 
                    WHEN pl.ledger_type IN ('purchage','Restock') THEN pl.purchage_qty
                    WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                    WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                    ELSE 0
                END
            ELSE 0
        END
    ),0) AS closing_stock

FROM products p
LEFT JOIN product_ledger pl ON pl.product_id = p.id
GROUP BY p.id, p.name
ORDER BY p.name
";

// IMPORTANT: binding order must match the ? order in the SQL above
$binds = [
    $s_date,   // opening_stock: pl.created_date < ?
    $s_date, $e_date, // stock_in: BETWEEN ? AND ?
    $s_date, $e_date, // stock_out
    $s_date, $e_date, // waste
    $e_date           // closing_stock: pl.created_date <= ?
];

$query = $this->db->query($sql, $binds);
$result = $query->result_array();

    } elseif ($reportwise == 1) { // Date Wise
         $query = $this->db->query("
        SELECT 
            DATE(pl.created_date) AS date,
            p.id AS product_id,
            p.name AS product_name,
            p.purchase_unit AS unit,

            -- Opening stock (before the day)
            COALESCE(SUM(
                CASE 
                    WHEN pl.created_date < DATE(pl.created_date) THEN 
                        CASE 
                            WHEN pl.ledger_type = 'purchage' THEN pl.purchage_qty
                            WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                            WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                            ELSE 0
                        END
                    ELSE 0
                END
            ), 0) AS opening_stock,

            -- Stock In
            COALESCE(SUM(CASE WHEN pl.ledger_type = 'purchage' THEN pl.purchage_qty ELSE 0 END), 0) AS stock_in,

            -- Stock Out
            COALESCE(SUM(CASE WHEN pl.ledger_type = 'Sell' THEN pl.sell_qty ELSE 0 END), 0) AS stock_out,

            -- Waste
            COALESCE(SUM(CASE WHEN pl.ledger_type = 'Wastage' THEN pl.wastage ELSE 0 END), 0) AS waste,

            -- Closing stock
            COALESCE(SUM(
                CASE 
                    WHEN pl.ledger_type = 'purchage' THEN pl.purchage_qty
                    WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                    WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                    ELSE 0
                END
            ), 0) AS closing_stock

        FROM products p
        LEFT JOIN product_ledger pl 
            ON pl.product_id = p.id
            AND DATE(pl.created_date) BETWEEN '$s_date' AND '$e_date'

        GROUP BY DATE(pl.created_date), p.id, p.name, p.purchase_unit
        HAVING opening_stock <> 0 OR stock_in <> 0 OR stock_out <> 0 OR waste <> 0
        ORDER BY DATE(pl.created_date), p.name


        ");

        $result = $query->result_array();

    } else {
        $query = $this->db->query("SELECT * FROM products WHERE 1=0");
    }
    break;


            default:
                $query = $this->db->query("SELECT * FROM order_items LIMIT 0");
        }

        $result = $query->result_array();

        if ($r_type == 4) {
            foreach ($result as $row) {
                $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit'] ?? '');
                if (!isset($unit_summary[$unit])) {
                    $unit_summary[$unit] = 0;
                }
                $unit_summary[$unit] += (float) ($row['closing_stock'] ?? 0);
            }
            $total_amount = 0;
        } else {
            foreach ($result as $row) {
                $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit'] ?? '');
                $qty = (float) ($row['qty'] ?? 0);
                $sub_total = (float) ($row['sub_total'] ?? 0);

                if (!isset($unit_summary[$unit])) {
                    $unit_summary[$unit] = 0;
                }
                $unit_summary[$unit] += $qty;
                $total_amount += $sub_total;
            }
        }

        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $product = $query->row_array();
		  $head['title'] = "Product Report";
		 // $this->load->view('fixed/header', $head);
		$this->load->view('products/ware_statementpdf-ltr', [
            'report' => $result,
            'product' => $product,
            'r_type' => $r_type,
            'reportwise' => $reportwise,
            'unit_summary' => $unit_summary,
            'total_amount' => $total_amount,
            'party_name' => $party_name,
        ]);
	//	$this->load->view('fixed/footer');

      /*   $html = $this->load->view('products/ware_statementpdf-ltr', [
            'report' => $result,
            'product' => $product,
            'r_type' => $r_type,
            'reportwise' => $reportwise,
            'unit_summary' => $unit_summary,
            'total_amount' => $total_amount,
            'party_name' => $party_name,
        ], true); */

       /*  ini_set('memory_limit', '640M');
        $this->load->library('pdf');
        $pdf = $this->pdf->load();
        $pdf->WriteHTML($html);
		
        $pdf->Output($pid . '_report.pdf', 'I'); */
    } else {
        $pid = intval($this->input->get('id'));
        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $product = $query->row_array();

        $head['title'] = "Product Sales";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/ware_statement', ['id' => $pid, 'product' => $product]);
        $this->load->view('fixed/footer');
    }
}





public function warehouse_report()
{
    $pid = intval($this->input->post('id'));
    $r_type = intval($this->input->post('r_type'));
    $reportwise = intval($this->input->post('reportwise'));
      $s_date = datefordatabase($this->input->post('s_date'));
     $e_date = datefordatabase($this->input->post('e_date')); 
  

  

    $party_id = intval($this->input->post('party_id'));
    $product_id = intval($this->input->post('product_id')); // ✅ product filter
    $party_name = "";

    if ($reportwise == 3) {
        $customer = $this->products_cat->get_customer($party_id);
        $party_name = $customer['name'] . ' (' . $customer['mobile'] . ')';
    } else if ($reportwise == 4) {
        $supplier = $this->products_cat->get_supplier($party_id);
        $party_name = $supplier['name'] . ' (' . $supplier['mobile'] . ')';
    }

    $unit_summary = [];
    $total_amount = 0;

    if ($pid && $r_type) {
        $qj = '';
        $wr = '';
        $product_condition = '';

        if ($this->aauth->get_user()->loc) {
            $qj = "LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id";
            $wr = " AND geopos_warehouse.loc='" . $this->aauth->get_user()->loc . "'";
        }

        switch ($r_type) {
             case 1: // Sales
                if ($product_id) {
                    $product_condition = " AND products.id = '$product_id' ";
                }

                if ($reportwise == 1) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(orders.date_added) as date,
                            order_items.product_name,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            order_items.variant_name as unit
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $product_condition
                            $wr
                        GROUP BY DATE(orders.date_added), order_items.product_name
                        ORDER BY DATE(orders.date_added), order_items.product_name
                    ");
                } elseif ($reportwise == 2) {
                    $query = $this->db->query("
                        SELECT 
                            order_items.product_name,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            order_items.variant_name as unit
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $product_condition
                            $wr
                        GROUP BY order_items.product_name
                        ORDER BY order_items.product_name
                    ");
                } elseif ($reportwise == 3 && $party_id) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(orders.date_added) as date,
                            order_items.product_name,
                            order_items.variant_name as unit,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            orders.id as tid
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND orders.user_id = '$party_id'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $product_condition
                            $wr
                        GROUP BY orders.id, order_items.product_name
                        ORDER BY orders.date_added
                    ");
                } else {
                    $query = $this->db->query("SELECT order_items.* FROM order_items LIMIT 0");
                }
				
				        $result = $query->result_array();   // ✅ add this
                break;

            case 2: // Purchase
                if ($product_id) {
                    $product_condition = " AND pr.id = '$product_id' ";
                }

                if ($reportwise == 1) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(p.invoicedate) as date,
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total,
                            p.id as tid
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                            $product_condition
                        GROUP BY DATE(p.invoicedate), pr.name, pi.unit
                        ORDER BY p.invoicedate
                    ");
                } elseif ($reportwise == 2) {
                    $query = $this->db->query("
                        SELECT 
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                            $product_condition
                        GROUP BY pr.name, pi.unit
                        ORDER BY pr.name
                    ");
					
                } elseif ($reportwise == 4 && $party_id) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(p.invoicedate) as date,
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total,
                            p.id as tid
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND p.csd = '$party_id'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                            $product_condition
                        GROUP BY p.id, pr.name
                        ORDER BY p.invoicedate
                    ");
                } else {
                    $query = $this->db->query("SELECT * FROM geopos_purchase_items LIMIT 0");
                }
				
				        $result = $query->result_array();   // ✅ add this
                break;

            case 3: // Stock Transfer
                $product_condition = $product_id ? " AND geopos_products.pid = '$product_id' " : "";
                $query = $this->db->query("
                    SELECT 
                        geopos_movers.rid2 AS qty,
                        DATE(geopos_movers.d_time) AS invoicedate,
                        geopos_movers.note,
                        geopos_products.product_price AS price,
                        geopos_products.product_name
                    FROM geopos_movers
                    LEFT JOIN geopos_products ON geopos_products.pid = geopos_movers.rid1
                    WHERE geopos_movers.d_type = '1'
                        AND geopos_products.warehouse = '$pid'
                        $product_condition
                        AND DATE(geopos_movers.d_time) BETWEEN DATE('$s_date') AND DATE('$e_date')
                ");
				        $result = $query->result_array();   // ✅ add this
                break;

         case 4: // Stock Report (opening, stock in, stock out, waste, closing)
		 
		   $s_date = date('Y-m-d', strtotime($s_date));
$e_date = date('Y-m-d', strtotime($e_date));
    if ($reportwise == 2) { // ✅ Product wise
        $this->db->select('id, name, purchase_unit as unit');
        $this->db->from('products');
        if ($product_id) {
            $this->db->where('id', $product_id);
        }
        $products = $this->db->get()->result();

        $result = [];
        foreach ($products as $p) {
            $pid2 = $p->id;

            // Previous stock till before start date
            $this->db->select('*');
            $this->db->from('product_ledger');
            $this->db->where('product_id', $pid2);
            $this->db->where('created_date <', $s_date . ' 00:00:00');
            $prev_entries = $this->db->get()->result();

            $opening = 0;
            if (!empty($prev_entries)) {
                foreach ($prev_entries as $entry) {
                    $opening += $entry->purchage_qty;
                    $opening -= $entry->sell_qty;
                    $opening -= isset($entry->wastage) ? $entry->wastage : 0;
                }
            }

            // Entries within date range
            $this->db->select('*');
            $this->db->from('product_ledger');
            $this->db->where('product_id', $pid2);
           $this->db->where('created_date >=', $s_date . ' 00:00:00');
			$this->db->where('created_date <=', $e_date . ' 23:59:59');
            $entries = $this->db->get()->result();
      // echo $this->db->last_query();
            $stock_in = 0;
            $stock_out = 0;
            $waste = 0;
            $units = '';
            foreach ($entries as $e) {
                $stock_in += $e->purchage_qty;
                $stock_out += $e->sell_qty;
                $waste += isset($e->wastage) ? $e->wastage : 0;
                $units = $e->unit;
            }

            $closing = $opening + $stock_in - $stock_out - $waste;

            // ✅ केवल वही products दिखाओ जिनमें activity हुई हो
            if ($stock_in != 0 || $stock_out != 0 || $waste != 0 ) {
                $result[] = [
                    'product_id'   => $pid2,
                    'product_name' => $p->name,
                    'unit'         => preg_replace('/[^a-zA-Z]/', '', $units),
                    'opening_stock'=> number_format($opening, 2, '.', ''),
                    'stock_in'     => number_format($stock_in, 2, '.', ''),
                    'stock_out'    => number_format($stock_out, 2, '.', ''),
                    'waste'        => number_format($waste, 2, '.', ''),
                    'closing_stock'=> number_format($closing, 2, '.', '')
                ];
            }
        }

    } elseif ($reportwise == 1) { // ✅ Date wise
        $this->db->select('id, name, purchase_unit as unit');
        $this->db->from('products');
        if ($product_id) {
            $this->db->where('id', $product_id);
        }
        $products = $this->db->get()->result();

        $result = [];
        foreach ($products as $p) {
            $pid2 = $p->id;

            // हर दिन का report निकाले
            $period = new DatePeriod(
                new DateTime($s_date),
                new DateInterval('P1D'),
                (new DateTime($e_date))->modify('+1 day')
            );

            $running_opening = 0;

            // Opening till start date
            $this->db->select('*');
            $this->db->from('product_ledger');
            $this->db->where('product_id', $pid2);
            $this->db->where('created_date <', $s_date . ' 00:00:00');
            $prev_entries = $this->db->get()->result();
            foreach ($prev_entries as $entry) {
                $running_opening += $entry->purchage_qty;
                $running_opening -= $entry->sell_qty;
                $running_opening -= isset($entry->wastage) ? $entry->wastage : 0;
            }

            foreach ($period as $date) {
                $day = $date->format('Y-m-d');

                $this->db->select('*');
                $this->db->from('product_ledger');
                $this->db->where('product_id', $pid2);
                $this->db->where('DATE(created_date)', $day);
                $entries = $this->db->get()->result();

                $stock_in = 0;
                $stock_out = 0;
                $waste = 0;
                $units = '';
                foreach ($entries as $e) {
                    $stock_in += $e->purchage_qty;
                    $stock_out += $e->sell_qty;
                    $waste += isset($e->wastage) ? $e->wastage : 0;
                    $units = $e->unit;
                }

                $closing = $running_opening + $stock_in - $stock_out - $waste;

                // ✅ केवल वही दिन दिखाओ जिनमें activity हुई हो
                if ($stock_in != 0 || $stock_out != 0 || $waste != 0 ) {
                    $result[] = [
                        'date'         => $day,
                        'product_id'   => $pid2,
                        'product_name' => $p->name,
                        'unit'         => preg_replace('/[^a-zA-Z]/', '', $units),
                        'opening_stock'=> number_format($running_opening, 2, '.', ''),
                        'stock_in'     => number_format($stock_in, 2, '.', ''),
                        'stock_out'    => number_format($stock_out, 2, '.', ''),
                        'waste'        => number_format($waste, 2, '.', ''),
                        'closing_stock'=> number_format($closing, 2, '.', '')
                    ];
                }

                $running_opening = $closing;
            }
        }

    } else {
        $result = [];
    }
    break;


            default:
                $query = $this->db->query("SELECT * FROM order_items LIMIT 0");
                $result = $query->result_array();
        }

        // Unit summary & total_amount calculation
        if ($r_type == 4) {
            foreach ($result as $row) {
                $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit'] ?? '');
                if (!isset($unit_summary[$unit])) {
                    $unit_summary[$unit] = 0;
                }
                $unit_summary[$unit] += (float) ($row['closing_stock'] ?? 0);
            }
            $total_amount = 0;
        } else {
            foreach ($result as $row) {
                $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit'] ?? '');
                $qty = (float) ($row['qty'] ?? 0);
                $sub_total = (float) ($row['sub_total'] ?? 0);

                if (!isset($unit_summary[$unit])) {
                    $unit_summary[$unit] = 0;
                }
                $unit_summary[$unit] += $qty;
                $total_amount += $sub_total;
            }
        }

        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $product = $query->row_array();

        $head['title'] = "Product Report";
        $this->load->view('products/ware_statementpdf-ltr', [
            'report' => $result,
            'product' => $product,
            'r_type' => $r_type,
            'reportwise' => $reportwise,
            'unit_summary' => $unit_summary,
            'total_amount' => $total_amount,
            'party_name' => $party_name,
        ]);
    } else {
        $pid = intval($this->input->get('id'));
        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $product = $query->row_array();

        $head['title'] = "Product Sales";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/ware_statement', ['id' => $pid, 'product' => $product]);
        $this->load->view('fixed/footer');
    }
}




public function warehouse_report_oldnotwork()
{
    $pid = intval($this->input->post('id'));
    $r_type = intval($this->input->post('r_type'));
    $reportwise = intval($this->input->post('reportwise'));
    $s_date = datefordatabase($this->input->post('s_date'));
    $e_date = datefordatabase($this->input->post('e_date'));
    $party_id = intval($this->input->post('party_id'));
    $product_id = intval($this->input->post('product_id')); // ✅ नया प्रोडक्ट फिल्टर
    $party_name = "";

    if ($reportwise == 3) {
        $customer = $this->products_cat->get_customer($party_id);
        $party_name = $customer['name'] . ' (' . $customer['mobile'] . ')';
    } else if ($reportwise == 4) {
        $supplier = $this->products_cat->get_supplier($party_id);
        $party_name = $supplier['name'] . ' (' . $supplier['mobile'] . ')';
    }

    $unit_summary = [];
    $total_amount = 0;

    if ($pid && $r_type) {
        $qj = '';
        $wr = '';
        $product_condition = ''; // ✅ product filter condition

        if ($this->aauth->get_user()->loc) {
            $qj = "LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id";
            $wr = " AND geopos_warehouse.loc='" . $this->aauth->get_user()->loc . "'";
        }

        switch ($r_type) {
            case 1: // Sales
                if ($product_id) {
                    $product_condition = " AND products.id = '$product_id' ";
                }

                if ($reportwise == 1) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(orders.date_added) as date,
                            order_items.product_name,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            order_items.variant_name as unit
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $product_condition
                            $wr
                        GROUP BY DATE(orders.date_added), order_items.product_name
                        ORDER BY DATE(orders.date_added), order_items.product_name
                    ");
                } elseif ($reportwise == 2) {
                    $query = $this->db->query("
                        SELECT 
                            order_items.product_name,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            order_items.variant_name as unit
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $product_condition
                            $wr
                        GROUP BY order_items.product_name
                        ORDER BY order_items.product_name
                    ");
                } elseif ($reportwise == 3 && $party_id) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(orders.date_added) as date,
                            order_items.product_name,
                            order_items.variant_name as unit,
                            SUM(order_items.quantity) as qty,
                            SUM(order_items.sub_total) as sub_total,
                            orders.id as tid
                        FROM order_items
                        LEFT JOIN orders ON orders.id = order_items.order_id
                        LEFT JOIN products ON products.id = order_items.product_id
                        $qj
                        WHERE orders.is_deleted != '1'
                            AND orders.user_id = '$party_id'
                            AND DATE(orders.date_added) BETWEEN DATE('$s_date') AND DATE('$e_date')
                            AND products.warehouse = '$pid'
                            $product_condition
                            $wr
                        GROUP BY orders.id, order_items.product_name
                        ORDER BY orders.date_added
                    ");
                } else {
                    $query = $this->db->query("SELECT order_items.* FROM order_items LIMIT 0");
                }
                break;

            case 2: // Purchase
                if ($product_id) {
                    $product_condition = " AND pr.id = '$product_id' ";
                }

                if ($reportwise == 1) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(p.invoicedate) as date,
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total,
                            p.id as tid
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                            $product_condition
                        GROUP BY DATE(p.invoicedate), pr.name, pi.unit
                        ORDER BY p.invoicedate
                    ");
                } elseif ($reportwise == 2) {
                    $query = $this->db->query("
                        SELECT 
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                            $product_condition
                        GROUP BY pr.name, pi.unit
                        ORDER BY pr.name
                    ");
                } elseif ($reportwise == 4 && $party_id) {
                    $query = $this->db->query("
                        SELECT 
                            DATE(p.invoicedate) as date,
                            pr.name as product_name,
                            pi.unit as unit,
                            SUM(pi.qty) as qty,
                            SUM(pi.qty * pi.price) as sub_total,
                            p.id as tid
                        FROM geopos_purchase_items pi
                        LEFT JOIN geopos_purchase p ON p.id = pi.tid
                        LEFT JOIN products pr ON pr.id = pi.pid
                        WHERE p.status != 'canceled'
                            AND p.csd = '$party_id'
                            AND DATE(p.invoicedate) BETWEEN '$s_date' AND '$e_date'
                            $product_condition
                        GROUP BY p.id, pr.name
                        ORDER BY p.invoicedate
                    ");
                } else {
                    $query = $this->db->query("SELECT * FROM geopos_purchase_items LIMIT 0");
                }
                break;

            case 3: // Stock Transfer
                $product_condition = $product_id ? " AND geopos_products.pid = '$product_id' " : "";
                $query = $this->db->query("
                    SELECT 
                        geopos_movers.rid2 AS qty,
                        DATE(geopos_movers.d_time) AS invoicedate,
                        geopos_movers.note,
                        geopos_products.product_price AS price,
                        geopos_products.product_name
                    FROM geopos_movers
                    LEFT JOIN geopos_products ON geopos_products.pid = geopos_movers.rid1
                    WHERE geopos_movers.d_type = '1'
                        AND geopos_products.warehouse = '$pid'
                        $product_condition
                        AND DATE(geopos_movers.d_time) BETWEEN DATE('$s_date') AND DATE('$e_date')
                ");
                break;

            case 4:
                if ($reportwise == 2) {
                    $product_condition = $product_id ? " AND p.id = '$product_id' " : "";

                    $sql = "SELECT 
                        p.id AS product_id,
                        p.name AS product_name,
                        pl.unit AS unit,

                        COALESCE(SUM(
                            CASE 
                                WHEN pl.created_date < ? THEN
                                    CASE 
                                        WHEN pl.ledger_type IN ('purchage','Restock') THEN pl.purchage_qty
                                        WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                                        WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                                        ELSE 0
                                    END
                                ELSE 0
                            END
                        ), 0) AS opening_stock,

                        COALESCE(SUM(
                            CASE 
                                WHEN pl.created_date BETWEEN ? AND ? 
                                     AND pl.ledger_type IN ('purchage','Restock') THEN pl.purchage_qty
                                ELSE 0
                            END
                        ),0) AS stock_in,

                        COALESCE(SUM(
                            CASE 
                                WHEN pl.created_date BETWEEN ? AND ? 
                                     AND pl.ledger_type = 'Sell' THEN pl.sell_qty
                                ELSE 0
                            END
                        ),0) AS stock_out,

                        COALESCE(SUM(
                            CASE 
                                WHEN pl.created_date BETWEEN ? AND ? 
                                     AND pl.ledger_type = 'Wastage' THEN pl.wastage
                                ELSE 0
                            END
                        ),0) AS waste,

                        COALESCE(SUM(
                            CASE 
                                WHEN pl.created_date <= ? THEN
                                    CASE 
                                        WHEN pl.ledger_type IN ('purchage','Restock') THEN pl.purchage_qty
                                        WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                                        WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                                        ELSE 0
                                    END
                                ELSE 0
                            END
                        ),0) AS closing_stock

                    FROM products p
                    LEFT JOIN product_ledger pl ON pl.product_id = p.id
                    WHERE 1=1 $product_condition
                    GROUP BY p.id, p.name
                    ORDER BY p.name";

                    $binds = [$s_date,$s_date,$e_date,$s_date,$e_date,$s_date,$e_date,$e_date];
                    $query = $this->db->query($sql, $binds);
					
				
                } elseif ($reportwise == 1) { // Date Wise
                    $product_condition = $product_id ? " AND p.id = '$product_id' " : "";

                    $query = $this->db->query("
                        SELECT 
                            DATE(pl.created_date) AS date,
                            p.id AS product_id,
                            p.name AS product_name,
                            p.purchase_unit AS unit,

                            COALESCE(SUM(
                                CASE 
                                    WHEN pl.created_date < DATE(pl.created_date) THEN 
                                        CASE 
                                            WHEN pl.ledger_type = 'purchage' THEN pl.purchage_qty
                                            WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                                            WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                                            ELSE 0
                                        END
                                    ELSE 0
                                END
                            ), 0) AS opening_stock,

                            COALESCE(SUM(CASE WHEN pl.ledger_type = 'purchage' THEN pl.purchage_qty ELSE 0 END), 0) AS stock_in,
                            COALESCE(SUM(CASE WHEN pl.ledger_type = 'Sell' THEN pl.sell_qty ELSE 0 END), 0) AS stock_out,
                            COALESCE(SUM(CASE WHEN pl.ledger_type = 'Wastage' THEN pl.wastage ELSE 0 END), 0) AS waste,

                            COALESCE(SUM(
                                CASE 
                                    WHEN pl.ledger_type = 'purchage' THEN pl.purchage_qty
                                    WHEN pl.ledger_type = 'Sell' THEN -pl.sell_qty
                                    WHEN pl.ledger_type = 'Wastage' THEN -pl.wastage
                                    ELSE 0
                                END
                            ), 0) AS closing_stock

                        FROM products p
                        LEFT JOIN product_ledger pl 
                            ON pl.product_id = p.id
                            AND DATE(pl.created_date) BETWEEN '$s_date' AND '$e_date'
                        WHERE 1=1 $product_condition
                        GROUP BY DATE(pl.created_date), p.id, p.name, p.purchase_unit
                        HAVING opening_stock <> 0 OR stock_in <> 0 OR stock_out <> 0 OR waste <> 0
                        ORDER BY DATE(pl.created_date), p.name
                    ");
                } else {
                    $query = $this->db->query("SELECT * FROM products WHERE 1=0");
                }
                break;

            default:
                $query = $this->db->query("SELECT * FROM order_items LIMIT 0");
        }

        $result = $query->result_array();

        if ($r_type == 4) {
            foreach ($result as $row) {
                $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit'] ?? '');
                if (!isset($unit_summary[$unit])) {
                    $unit_summary[$unit] = 0;
                }
                $unit_summary[$unit] += (float) ($row['closing_stock'] ?? 0);
            }
            $total_amount = 0;
        } else {
            foreach ($result as $row) {
                $unit = preg_replace('/[^a-zA-Z]/', '', $row['unit'] ?? '');
                $qty = (float) ($row['qty'] ?? 0);
                $sub_total = (float) ($row['sub_total'] ?? 0);

                if (!isset($unit_summary[$unit])) {
                    $unit_summary[$unit] = 0;
                }
                $unit_summary[$unit] += $qty;
                $total_amount += $sub_total;
            }
        }

        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $product = $query->row_array();

        $head['title'] = "Product Report";
        $this->load->view('products/ware_statementpdf-ltr', [
            'report' => $result,
            'product' => $product,
            'r_type' => $r_type,
            'reportwise' => $reportwise,
            'unit_summary' => $unit_summary,
            'total_amount' => $total_amount,
            'party_name' => $party_name,
        ]);
    } else {
        $pid = intval($this->input->get('id'));
        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $pid);
        $query = $this->db->get();
        $product = $query->row_array();

        $head['title'] = "Product Sales";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/ware_statement', ['id' => $pid, 'product' => $product]);
        $this->load->view('fixed/footer');
    }
}



public function stock_report($s_date, $e_date, $product_id = null) {
    $data = [];

    $this->db->select('id, name');
    $this->db->from('products');
    if ($product_id) {
        $this->db->where('id', $product_id);
    }
    $products = $this->db->get()->result();

    foreach ($products as $p) {
        $pid = $p->id;

        // Previous stock till before start date
        $this->db->select('*');
        $this->db->from('product_ledger');
        $this->db->where('product_id', $pid);
        $this->db->where('created_date <', $s_date . ' 00:00:00');
        $prev_entries = $this->db->get()->result();

        $opening = 0;
        if (!empty($prev_entries)) {
            foreach ($prev_entries as $entry) {
                $opening += $entry->purchage_qty;
                $opening -= $entry->sell_qty;
                $opening -= isset($entry->wastage) ? $entry->wastage : 0;
            }
        }

        // Stock in, out, wastage within date range
        $this->db->select('*');
        $this->db->from('product_ledger');
        $this->db->where('product_id', $pid);
        $this->db->where('created_date >=', $s_date . ' 00:00:00');
        $this->db->where('created_date <=', $e_date . ' 23:59:59');
        $entries = $this->db->get()->result();

        $stock_in = 0;
        $stock_out = 0;
        $waste = 0;

        foreach ($entries as $e) {
            $stock_in += $e->purchage_qty;
            $stock_out += $e->sell_qty;
            $waste += isset($e->wastage) ? $e->wastage : 0;
        }

        $closing = $opening + $stock_in - $stock_out - $waste;

        $data[] = [
            'product_id'   => $pid,
            'product_name' => $p->name,
            'unit'         => isset($entries[0]->unit) ? $entries[0]->unit : (isset($prev_entries[0]->unit) ? $prev_entries[0]->unit : ''),
            'opening'      => $opening,
            'stock_in'     => $stock_in,
            'stock_out'    => $stock_out,
            'waste'        => $waste,
            'closing'      => $closing
        ];
    }

    return $data;
}



}