<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices_model extends CI_Model
{
    var $table = 'orders';
    var $column_order = array(null, 'orders.id', 'users.username', 'orders.date_added', 'orders.total', 'orders.status','orders.orderdone_by', null);
    var $column_search = array('orders.id', 'users.username', 'orders.date_added', 'orders.total','orders.status','orders.orderdone_by');
    var $order = array('orders.id' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }

    private function _apply_creator_filter($userid)
    {
        if ($userid > 1) {
            if (is_seller_user()) {
                $this->db->group_start();
                $this->db->where('orders.created_by', $userid);
                $this->db->or_where("orders.id IN (SELECT DISTINCT order_id FROM order_items WHERE seller_id = " . (int) $userid . ")", NULL, FALSE);
                $this->db->group_end();
            } else {
                $this->db->where('orders.created_by', $userid);
            }
        }
    }

    public function lastinvoice()
    {
        $this->db->select('id as tid');
        $this->db->from($this->table);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $this->db->where('i_class', 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->tid;
        } else {
            return 1000;
        }
    }


/*     public function invoice_details($id, $eid = '',$p=true)
    {
        $this->db->select('geopos_invoices.*,SUM(geopos_invoices.shipping + geopos_invoices.ship_tax) AS shipping,geopos_customers.*,geopos_invoices.loc as loc,geopos_invoices.id AS iid,geopos_customers.id AS cid,geopos_terms.id AS termid,geopos_terms.title AS termtit,geopos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('geopos_invoices.id', $id);
        if ($eid) {
            $this->db->where('geopos_invoices.eid', $eid);
        }
        if($p) {


            if ($this->aauth->get_user()->loc) {
                $this->db->where('geopos_invoices.loc', $this->aauth->get_user()->loc);
            } elseif (!BDATA) {
                $this->db->where('geopos_invoices.loc', 0);
            }
        }
        $this->db->join('geopos_customers', 'geopos_invoices.csd = geopos_customers.id', 'left');
        $this->db->join('geopos_terms', 'geopos_terms.id = geopos_invoices.term', 'left');
        $query = $this->db->get();
        return $query->row_array();
    } */
	
	    public function invoice_details($id, $eid = '',$loc=null)
    {
        $is_seller = is_seller_user();
        $this->db->select('orders.*, orders.delivery_charge AS shipping,users.*,orders.id AS iid,users.id AS cid,geopos_terms.id AS termid, geopos_terms.title AS termtit,geopos_terms.terms AS terms, "" as eid, "" as tid');
        $this->db->from('orders');
        $this->db->where('orders.id', $id);
       /*  if ($eid) {
            $this->db->where('orders.eid', $eid);
        }
        if (@$this->aauth->get_user()->loc) {
            $this->db->where('orders.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA and !$loc) { $this->db->where('orders.loc', 0); } */
		
		
       /*  if($loc){ $this->db->where('orders.loc', $loc); } */
        if ($is_seller) {
            $this->db->join('order_items oi', 'oi.order_id=orders.id', 'left');
            $this->db->where('oi.seller_id', (int)$this->session->userdata('user_id'));
        }
        $this->db->join('users', 'orders.user_id = users.id', 'left');
        $this->db->join('geopos_terms', 'geopos_terms.id = orders.id', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

  /*   public function invoice_products($id)
    {

        $this->db->select('*');
        $this->db->from('geopos_invoice_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();

    } */
	
	   public function invoice_products($id)
    {
        $is_seller = is_seller_user();


	$sql = "
    SELECT
        oi.*,
        oi.product_name AS product,
        oi.product_id AS pid,
        oi.quantity AS qty,
        oi.sub_total AS subtotal,
        oi.tax_percent AS tax,
        oi.tax_amount AS totaltax,
        oi.variant_name AS unit,
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
            ORDER BY gp.invoicedate DESC
            LIMIT 1
        ) AS purchase_price
    FROM order_items oi
    LEFT JOIN products p ON p.name = oi.product_name
    JOIN orders o ON o.id = oi.order_id
    WHERE oi.order_id = ?";

    $params = array($id);
    if ($is_seller) {
        $sql .= " AND oi.seller_id = ?";
        $params[] = (int)$this->session->userdata('user_id');
    }
$query = $this->db->query($sql, $params);
return $query->result_array();


    }	 



	   public function invoice_productsadmin($id)
    {
        $is_seller = is_seller_user();


$sql = "SELECT
    oi.*,
    oi.product_name AS product,
    oi.product_id AS pid,
    oi.quantity AS qty,
    oi.sub_total AS subtotal,
    oi.tax_percent AS tax,
    oi.tax_amount AS totaltax,
    oi.variant_name AS unit,
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
WHERE oi.order_id = ?";

    $params = array($id);
    if ($is_seller) {
        $sql .= " AND oi.seller_id = ?";
        $params[] = (int)$this->session->userdata('user_id');
    }

$query = $this->db->query($sql, $params);
return $query->result_array();


    }	

/* 	public function invoice_purchase($id)
    {


	$sql = "SELECT sum( oi.quantity* ( SELECT gpi.price FROM geopos_purchase_items gpi JOIN geopos_purchase gp ON gp.id = gpi.tid WHERE ( gpi.product = oi.product_name OR gpi.pid = oi.product_id ) AND gp.invoicedate <= DATE(o.date_added) ORDER BY gp.invoicedate DESC LIMIT 1 )) AS purchase_price FROM order_items oi LEFT JOIN products p ON p.name = oi.product_name JOIN orders o ON o.id = oi.order_id
    WHERE oi.order_id = ?
";

$query = $this->db->query($sql, array($id));
$result = $query->row_array();

return round($result['purchase_price'],2);
    }  */
	
	
	
public function get_id_by_tid($tid_string)
    {
        // sirf number extract karega e.g. CN2348 → 2348
        if ($tid_string === null) {
            $tid_string = '';
        }
        $tid = preg_replace('/\D/', '', (string)$tid_string);

        if ($tid == '') {
            return false; // agar valid number hi nahi mila
        }

        $this->db->select('id');
        $this->db->from('geopos_quotes');
        $this->db->where('tid', (int)$tid);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return false;
        }
    }
	
	
	public function invoice_purchase($id)
{
    $sql = "SELECT 
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
    WHERE oi.order_id = ?";

    $query = $this->db->query($sql, array($id));
    $result = $query->result_array();
    $total_purchase_value = 0;

    foreach ($result as $row) {
        $con = convert_to_base_unit((string)($row['variant_name'] ?? ''));
        $unit_qty = isset($con['qty']) ? $con['qty'] : 1;
        $unit_purchase_price = ((float)($row['purchase_price'] ?? 0)) * $unit_qty;
        $puprice = $row['quantity'] * $unit_purchase_price;

        $row['unit_purchase_price'] = $unit_purchase_price;
        $row['total_purchase_price'] = $puprice;

        $total_purchase_value += $puprice;
    }
	
	return round($total_purchase_value,2);
}

   

        public function items_with_product($id)
    {
        $is_seller = is_seller_user();
        $this->db->select('order_items.*,products.stock AS alert, , products.article');
        $this->db->from('order_items');
        $this->db->where('order_id', $id);
        if ($is_seller) {
            $this->db->where('order_items.seller_id', (int)$this->session->userdata('user_id'));
        }
        $this->db->join('products', 'products.id = order_items.product_id', 'left');
        $query = $this->db->get();
        return $query->result_array();

    }

    public function currencies()
    {

        $this->db->select('*');
        $this->db->from('geopos_currencies');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function currency_d($id, $loc = 0)
    {
        if ($loc) {
            $query = $this->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
            $row = $query->row_array();
            $id = $row['cur'];
        }
        $this->db->select('*');
        $this->db->from('geopos_currencies');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function warehouses()
    {
        $is_seller = function_exists('is_seller_user') && is_seller_user();

        if ($is_seller) {
            $seller_id = (int)$this->session->userdata('user_id');
            $this->db->distinct();
            $this->db->select('geopos_warehouse.*');
            $this->db->from('geopos_warehouse');
            $this->db->join('products', 'products.warehouse = geopos_warehouse.id', 'inner');
            $this->db->where('products.seller_id', $seller_id);
        } else {
            $this->db->select('*');
            $this->db->from('geopos_warehouse');
        }

        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) {
                $this->db->or_where('geopos_warehouse.loc', 0);
            }
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('geopos_warehouse.loc', 0);
        }

        $this->db->order_by('geopos_warehouse.title', 'ASC');

        $query = $this->db->get();

        return $query->result_array();

    }

    public function invoice_transactions($id)
    {
        $is_seller = is_seller_user();
         $this->db->select('*');
        $this->db->from('geopos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 0);
        if ($is_seller) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
        $query = $this->db->get();
        return $query->result_array();

    }

    public function invoice_delete($id, $eid = '', $userId='')
    {
        $this->db->trans_start();
        $this->db->select('id  as tid,total,status');
        $this->db->from('orders');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result  = $query->row_array();
        if ($this->aauth->get_user()->loc) {
            if ($eid) {

              //  $res = $this->db->delete('geopos_invoices', array('id' => $id, 'eid' => $eid, 'loc' => $this->aauth->get_user()->loc));
	$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $userId, 
				'deleted_datetime'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('orders', $data);

            } else {
              //  $res = $this->db->delete('geopos_invoices', array('id' => $id, 'loc' => $this->aauth->get_user()->loc));
				
				$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $userId, 
				'deleted_datetime'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('orders', $data);


            }
        }

        else {
            if (BDATA) {
                if ($eid) {

                  //  $res = $this->db->delete('geopos_invoices', array('id' => $id, 'eid' => $eid));

	$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $userId, 
				'deleted_datetime'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('orders', $data);
                } else {
                  //  $res = $this->db->delete('geopos_invoices', array('id' => $id));
				  	$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $userId, 
				'deleted_datetime'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('orders', $data);
                }
            } else {


                if ($eid) {

                   // $res = $this->db->delete('geopos_invoices', array('id' => $id, 'eid' => $eid, 'loc' => 0));

	$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $userId, 
				'deleted_datetime'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('orders', $data);
                } else {
                  //  $res = $this->db->delete('geopos_invoices', array('id' => $id, 'loc' => 0));
				  	$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $userId, 
				'deleted_datetime'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('orders', $data);
                }
            }
        }

        $affect = $this->db->affected_rows();

        if ($res) {
          /*   if ($result['status'] != 'canceled') {
                $this->db->select('pid,qty');
                $this->db->from('geopos_invoice_items');
                $this->db->where('tid', $id);
                $query = $this->db->get();
                $prevresult = $query->result_array();

                foreach ($prevresult as $prd) {
                    $amt = $prd['qty'];
                    $this->db->set('qty', "qty+$amt", FALSE);
                    $this->db->where('pid', $prd['pid']);
                    $this->db->update('geopos_products');
                }
            } */


            /* if ($affect) $this->db->delete('geopos_invoice_items', array('tid' => $id));

            $data = array('type' => 9, 'rid' => $id);
            $this->db->delete('geopos_metadata', $data);

                        $alert= $this->custom->api_config(66);
            if ($alert['method'] == 1) {
                 $this->load->model('communication_model');
                 $subject= $result['tid'].' '. $this->lang->line('DELETED');
                 $body=$subject.'<br> '. $this->lang->line('Amount').' '. $result['total'].'<br> '. $this->lang->line('Employee').' '. $this->aauth->get_user()->username.'<br> ID# '. $result['tid'];
               $out= $this->communication_model->send_corn_email($alert['url'], $alert['url'], $subject, $body, false, '');
            } */

            if ($this->db->trans_complete()) {
                return true;
            } else {
                return false;
            }
        }

    }


    private function _get_datatables_query($opt = '')
    {
        $this->db->select('orders.id,orders.id as tid,orders.date_added as invoicedate, orders.date_added as invoiceduedate,orders.total,orders.status,orders.pamnt,orders.orderdone_by, users.username as name, emp.name as username,orders.date_added,orders.chalanno, orders.created_by, sd.store_name as seller_store, creator_user.username as creator_username');
       // $this->db->select('orders.id,orders.id,orders.date_added, orders.date_added,orders.total,orders.status, users.username');
        $this->db->from($this->table);
        $this->db->join('geopos_employees as emp', 'orders.created_by=emp.id', 'left');
        $this->db->join('seller_data as sd', 'orders.created_by=sd.user_id', 'left');
        $this->db->join('users as creator_user', 'orders.created_by=creator_user.id', 'left');
     //   $this->db->where('orders.i_class', 0);
        $this->db->where('orders.is_deleted', 0);
      //  $this->db->where('orders.orderdone_by', 'Manual');
     $userid = $this->aauth->get_user()->id;
		$this->_apply_creator_filter($userid);
        if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(orders.date_added) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(orders.date_added) <=', datefordatabase($this->input->post('end_date')));
        }

		if ($this->input->post('txttype') && $this->input->post('txttype') !='All') // if datatable send POST for search
        {

            $this->db->where('orders.status', $this->input->post('txttype'));
        }
        $this->db->join('users', 'orders.user_id=users.id', 'left');

        $search = $this->input->post('search');
        if (!empty($search['value'])) {
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search['value']);
                } else {
                    $this->db->or_like($item, $search['value']);
                }
                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
                $i++;
            }
        }

        if (isset($_POST['order'])) // here order processing
        {
            $col = (int) $_POST['order']['0']['column'];
            if (!empty($this->column_order[$col])) {
                $this->db->order_by($this->column_order[$col], $_POST['order']['0']['dir']);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    } 


	private function _get_datatablestrash_query($opt = '')
    {
        $this->db->select('orders.id,orders.id as tid,orders.date_added as invoicedate, orders.date_added as invoiceduedate,orders.total,orders.status,orders.orderdone_by, users.username as name');
       // $this->db->select('orders.id,orders.id,orders.date_added, orders.date_added,orders.total,orders.status, users.username');
        $this->db->from($this->table);
        $this->db->where('orders.i_class', 0);
        $this->db->where('orders.is_deleted', 1);
      //  $this->db->where('orders.orderdone_by', 'Manual');
     $userid = $this->aauth->get_user()->id;
		
		
		
			 if ($userid > 1) {
			 
			 
			   $this->db->where('orders.created_by', $userid);
		 }
        if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(orders.date_added) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(orders.date_added) <=', datefordatabase($this->input->post('end_date')));
        }
		
		if ($this->input->post('txttype') && $this->input->post('txttype') !='All') // if datatable send POST for search
        {
           
            $this->db->where('orders.status', $this->input->post('txttype'));
        }
        $this->db->join('users', 'orders.user_id=users.id', 'left');

        $i = 0;
        $search = $this->input->post('search');
        $search_value = is_array($search) ? ($search['value'] ?? '') : '';

        foreach ($this->column_search as $item) // loop column
        {
            if ($search_value) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($opt = '')
    {
		
        $this->_get_datatables_query($opt);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
       // $this->db->where('orders.i_class', 0);
     //  echo $this->db->last_query();
        return $query->result();
    }
	
	
    function get_trashdatatables($opt = '')
    {
		
        $this->_get_datatablestrash_query($opt);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $this->db->where('orders.i_class', 0);
      // echo $this->db->last_query();
        return $query->result();
    }
	
	function get_totalsalse(){

        $this->db->from($this->table);
		 $this->db->where('orders.is_deleted', 0);
		$this->_apply_creator_filter($this->aauth->get_user()->id);
		 if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(orders.date_added) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(orders.date_added) <=', datefordatabase($this->input->post('end_date')));
        }
		if ($this->input->post('txttype') && $this->input->post('txttype') !='All') // if datatable send POST for search
        {
           
            $this->db->where('orders.status', $this->input->post('txttype'));
        }
		 $i = 0;
		
        $search = $this->input->post('search');
        $search_value = is_array($search) ? ($search['value'] ?? '') : '';
		  foreach ($this->column_search as $item) // loop column
        {
            if ($search_value) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
		  $this->db->select('SUM(orders.total) AS total');
       // $this->db->select('orders.id,orders.id,orders.date_added, orders.date_added,orders.total,orders.status, users.username');
	    $this->db->join('users', 'orders.user_id=users.id', 'left');
		 $query = $this->db->get();
		 return $query->row_array();
	}


	function get_totalpurchase(){
		
        $this->db->from('geopos_purchase');
		
		$this->db->join('geopos_supplier', 'geopos_purchase.csd=geopos_supplier.id', 'left');
            if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_purchase.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('geopos_purchase.loc', 0); }
		if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(geopos_purchase.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(geopos_purchase.invoicedate) <=', datefordatabase($this->input->post('end_date')));
        }
		
		
		if($this->input->post('supplier') != 'All'){
			 $this->db->where('geopos_purchase.csd', $this->input->post('supplier'));
			
			
		}	
		
		
		if($this->input->post('txttype') != 'All'){
			 $this->db->where('geopos_purchase.status', $this->input->post('txttype'));
			
			
		}
		
		$this->db->where('geopos_purchase.is_deleted', 0);
		
		  $this->db->select('SUM(geopos_purchase.total) AS total');
     
		 $query = $this->db->get();
		 
		// echo $this->db->last_query();
		 return $query->row_array();
	}

    function count_filtered($opt = '')
    {
        $this->_get_datatables_query($opt);


		 $this->db->where('orders.is_deleted', 0);
		 // $this->db->where('orders.orderdone_by', 'Manual');

        $query = $this->db->get();
		// echo $this->db->last_query();
        return $query->num_rows();
    }  


	function count_filteredtrash($opt = '')
    {
        $this->_get_datatablestrash_query($opt);
       

		$userid = $this->aauth->get_user()->id;
		 $this->db->where('orders.is_deleted', 1);
		//  $this->db->where('orders.orderdone_by', 'Manual');
		 	 if ($userid > 1) {
			 
			 
			   $this->db->where('orders.created_by', $userid);
		 }
		 
        $query = $this->db->get();
		// echo $this->db->last_query();
        return $query->num_rows();
    }

    public function count_all($opt = '')
    {
        $this->db->select('orders.id');
        $this->db->from($this->table);
        $this->db->where('orders.i_class', 0);
		 $this->db->where('orders.is_deleted', 0);
		//  $this->db->where('orders.orderdone_by', 'Manual');
         $userid = $this->aauth->get_user()->id;
		$this->_apply_creator_filter($userid);

        /* if ($this->aauth->get_user()->loc) {
            $this->db->where('orders.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('orders.loc', 0); } */
        return $this->db->count_all_results();
    }  


	public function count_alltrash($opt = '')
    {
        $this->db->select('orders.id');
        $this->db->from($this->table);
        $this->db->where('orders.i_class', 0);
		 $this->db->where('orders.is_deleted', 1);
		  //$this->db->where('orders.orderdone_by', 'Manual');
         $userid = $this->aauth->get_user()->id;
		
		
		
			 if ($userid > 1) {
			 
			 
			   $this->db->where('orders.created_by', $userid);
		 }
		
		
		
		
		
        /* if ($this->aauth->get_user()->loc) {
            $this->db->where('orders.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('orders.loc', 0); } */
        return $this->db->count_all_results();
    }


    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('geopos_terms');
        $this->db->where('type', 1);
        $this->db->or_where('type', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function employee($id)
    {
        $this->db->select('geopos_employees.*,geopos_users.roleid');
        $this->db->from('geopos_employees');
        $this->db->where('geopos_employees.id', $id);
        $this->db->join('geopos_users', 'geopos_employees.id = geopos_users.id', 'left');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function meta_insert($id, $type, $meta_data)
    {
        $data = array('type' => $type, 'rid' => $id, 'col1' => $meta_data);
        if ($id) {
            return $this->db->insert('geopos_metadata', $data);
        } else {
            return 0;
        }
    }

    public function attach($id)
    {
        $this->db->select('geopos_metadata.*');
        $this->db->from('geopos_metadata');
        $this->db->where('geopos_metadata.type', 1);
        $this->db->where('geopos_metadata.rid', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function meta_delete($id, $type, $name)
    {
        if (@unlink(FCPATH . 'userfiles/attach/' . $name)) {
            return $this->db->delete('geopos_metadata', array('rid' => $id, 'type' => $type, 'col1' => $name));
        }
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
	
	
	public function get_variation_articles_by_product($product_id) {
  $this->db->where('product_id', $product_id);
  return $this->db->get('product_barcode_info')->result_array();
}
}
