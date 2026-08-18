<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Quote_model extends CI_Model
{
    var $table = 'geopos_quotes';
    var $column_order = array(null, 'geopos_quotes.tid', 'users.username', 'geopos_quotes.invoicedate', 'geopos_quotes.total', 'geopos_quotes.status', null);
    var $column_search = array('geopos_quotes.tid', 'users.username', 'geopos_quotes.invoicedate', 'geopos_quotes.total','geopos_quotes.status',);
    var $order = array('geopos_quotes.tid' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }

    public function lastquote()
    {
        $this->db->select('tid');
        $this->db->from($this->table);
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->tid;
        } else {
            return 1000;
        }
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
        $query = $this->db->get();
        return $query->result_array();

    }

    public function quote_details($id)
    {

        $this->db->select('geopos_quotes.*,geopos_quotes.id AS iid,SUM(geopos_quotes.shipping + geopos_quotes.ship_tax) AS shipping,users.*,geopos_quotes.loc as loc,users.id AS cid,geopos_terms.id AS termid,geopos_terms.title AS termtit,geopos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('geopos_quotes.id', $id);
         if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_quotes.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->where('geopos_quotes.loc', 0);
        }
        if (function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('geopos_quotes.eid', (int)$this->session->userdata('user_id'));
        }
        $this->db->join('users', 'geopos_quotes.csd = users.id', 'left');//$this->db->join('users', 'geopos_quotes.csd = users.id', 'left');
        $this->db->join('geopos_terms', 'geopos_terms.id = geopos_quotes.term', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

    public function quote_products($id)
    {

        $this->db->select('geopos_quotes_items.*, products.product_price as sellprice, products.article, products.seller_id, sd.store_name as seller_name, pv.sku AS variant_sku, pv.price AS variant_price');
        $this->db->from('geopos_quotes_items');
        $this->db->join('products', 'products.id = geopos_quotes_items.pid', 'left');
        $this->db->join('seller_data sd', 'sd.user_id = products.seller_id', 'left');
        $this->db->join('attribute_values av', 'av.value = geopos_quotes_items.unit', 'left');
        $this->db->join('product_variants pv', 'pv.product_id = geopos_quotes_items.pid AND FIND_IN_SET(av.id, pv.attribute_value_ids) > 0', 'left');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();

    } 
	
	public function report_products($ids = [])
{
    if(!is_array($ids)) {
        $ids = [$ids];
    }

    $this->db->select('geopos_quotes_items.*, products.product_price as sellprice, products.article');
    $this->db->from('geopos_quotes_items');
    $this->db->join('products', 'products.id = geopos_quotes_items.pid', 'left');

    if(!empty($ids)) {
        $this->db->where_in('tid', $ids);
    }

    $query = $this->db->get();
	
	
	
    return $query->result_array();
	
	
	
}

	
	
	public function productslist($catid)
    {

        $this->db->select('products.*, categories.name as catname');
        $this->db->from('products' );
		$this->db->join('categories', 'categories.id=products.category_id', 'left');
		  if ($catid !== 'All') {
      
        $catidArray = explode(',', $catid);

        
        $this->db->where_in('category_id', $catidArray);
    }
		
		      //  $this->db->group_by('categories.id'); 
		$this->db->order_by("categories.name", "asc");
		$this->db->order_by("products.name", "asc");
        $query = $this->db->get();
	//	echo $this->db->last_query();
        return $query->result_array();

    }


    public function quote_delete($id)
    {
        $this->db->trans_start();
        $where = array('id' => $id);
        if (function_exists('is_seller_user') && is_seller_user()) {
            $where['eid'] = (int)$this->session->userdata('user_id');
        }
          if ($this->aauth->get_user()->loc) {
                $where['loc'] = $this->aauth->get_user()->loc;
                $res = $this->db->delete('geopos_quotes', $where);
        }
        else {
            if (BDATA) {
                    $res = $this->db->delete('geopos_quotes', $where);

            } else {
                    $where['loc'] = 0;
                    $res = $this->db->delete('geopos_quotes', $where);
            }
        }
        if ($this->db->affected_rows()) $this->db->delete('geopos_quotes_items', array('tid' => $id));
        if ($this->db->trans_complete()) {
            return true;
        } else {
            return false;
        }
    }


    private function _get_datatables_query($eid)
    {
        $is_seller = function_exists('is_seller_user') && is_seller_user();

        $this->db->select('geopos_quotes.id,geopos_quotes.tid,geopos_quotes.invoicedate,geopos_quotes.invoiceduedate,geopos_quotes.total,geopos_quotes.status,users.username as name, emp.username as employee, geopos_quotes.eid, sd.store_name as seller_store');
        $this->db->from($this->table);



        if ($is_seller) {
            $this->db->where('geopos_quotes.eid', (int)$this->session->userdata('user_id'));
        } elseif ($eid) {
            $this->db->where('geopos_quotes.eid', $eid);
        }
		
		
		
                if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_quotes.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('geopos_quotes.loc', 0); }
                        if ($this->input->post('start_date') && $this->input->post('end_date')) // if datatable send POST for search
        {
            $this->db->where('DATE(geopos_quotes.invoicedate) >=', datefordatabase($this->input->post('start_date')));
            $this->db->where('DATE(geopos_quotes.invoicedate) <=', datefordatabase($this->input->post('end_date')));
        }

        $this->db->join('users', 'geopos_quotes.csd=users.id', 'left');
        $this->db->join('users as emp', 'geopos_quotes.eid=emp.id', 'left');
        $this->db->join('seller_data as sd', 'geopos_quotes.eid=sd.user_id', 'left');

        $i = 0;
        $search = $this->input->post('search');
        $search_value = '';
        if (is_array($search)) {
            $search_value = isset($search['value']) ? trim((string)$search['value']) : '';
        }

        foreach ($this->column_search as $item) // loop column
        {
            if ($search_value !== '') // if datatable send POST for search
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

        if (isset($_POST['order'][0]['column'])) // here order processing
        {
            $col_index = (int)$_POST['order'][0]['column'];
            $dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
            $col_name = isset($this->column_order[$col_index]) ? $this->column_order[$col_index] : null;
            if ($col_name) {
                $this->db->order_by($col_name, $dir);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($eid)
    {
        $this->_get_datatables_query($eid);
        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        if ($length !== -1) {
            $this->db->limit($length, $start);
        }
        if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_quotes.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('geopos_quotes.loc', 0); }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($eid)
    {
        $this->_get_datatables_query($eid);
    if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_quotes.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('geopos_quotes.loc', 0); }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($eid)
    {
        $is_seller = function_exists('is_seller_user') && is_seller_user();

        $this->db->select('geopos_quotes.id');
        $this->db->from($this->table);
         if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_quotes.loc', $this->aauth->get_user()->loc);
        }  elseif(!BDATA) { $this->db->where('geopos_quotes.loc', 0); }
        if ($is_seller) {
            $this->db->where('geopos_quotes.eid', (int)$this->session->userdata('user_id'));
        } elseif ($eid) {
            $this->db->where('geopos_quotes.eid', $eid);
        }
        return $this->db->count_all_results();
    }


    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('geopos_terms');
        $this->db->where('type', 2);
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

    public function convert($id)
    {

        $invoice = $this->quote_details($id);
        if (empty($invoice)) {
            return false;
        }

        $products = $this->quote_products($id);

        $this->db->trans_start();
        $this->db->select('tid');
        $this->db->from('geopos_invoices');
        $this->db->where('i_class', 0);
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        /* if ($query->num_rows() > 0) {
            $iid = $query->row()->tid + 1;
        } else {
            $iid = 1000;
        } */
        $productlist = array();
        $prodindex = 0;
        if($invoice['loc']==$this->aauth->get_user()->loc) {
            $data = array( 'date_added' =>  date('Y-m-d H:i:s'), 'invoiceduedate' => $invoice['invoicedate'], 'total_payable' => $invoice['invoicedate'], 'shipping' => $invoice['shipping'], 'discount' => $invoice['discount'], 'tax_amount' => $invoice['tax'], 'total' => $invoice['subtotal'], 'total_payable'=>$invoice['subtotal'], 'final_total' =>$invoice['subtotal'], 'notes' => $invoice['notes'], 'user_id' => $invoice['csd'], 'created_by' => $invoice['eid'], 'items' => $invoice['items'], 'taxstatus' => $invoice['taxstatus'], 'discstatus' => $invoice['discstatus'], 'format_discount' => $invoice['format_discount'], 'chalanno' => 'CN'.$invoice['tid'], 'term' => $invoice['term'], 'loc' => $invoice['loc'], 'status'=>'due', 'orderdone_by'=>'Manual', 'refer'=> $invoice['refer']);
            $this->db->insert('orders', $data);
            $iid = $this->db->insert_id();		
			$status =  'received';
            foreach ($products as $row) {
                $amt = $row['qty'];
                $data = array(
                    'order_id' => $iid,                    
					'user_id' => $invoice['csd'],
                    'product_id' => $row['pid'],                 
					'product_variant_id' => $row['pid'],
					'product_article' => $row['product_article'],
                    'product_name' => $row['product'],
                    'code' => $row['code'],
                    'quantity' => $amt,
                    'price' => $row['sellprice'],
                    'tax_amount' => $row['tax'],
                    'discount' => $row['discount'],
                    'sub_total' => $row['subtotal'],
                    'status' =>json_encode(array(array($status, date("d-m-Y h:i:sa")))),
                    'variant_name' => $row['unit'],
                    'seller_id' => (int)$row['seller_id']
                );
                $productlist[$prodindex] = $data;
                $prodindex++;
				
				
				
				
					 $oldunit = get_stock_by_product_id($row['pid']);
			$stock = $oldunit - $row['qty'];
			
						$legerdata= array(
				
			
				'order_id' => $iid,
				'product_id' => $row['pid'],
				'product_variants' => $row['pid'],
				 'product_name' =>$row['product'],
				'seller_id' => (int)$row['seller_id'],
				'customer_id' =>  $invoice['csd'],
				'sell_qty' => numberClean($row['qty']),
				'purchage_qty' => '',
				'sell_amount' => $row['subtotal'],
				'purchage_amount' => '0.00',
				'open_stock' => $oldunit,
				'close_stock' => $stock,
				'created_date' => date('Y-m-d H:i:s'),
				'created_by' => $this->aauth->get_user()->id,
				'purchage_rate' => '0.00',
				'unit' => $row['unit'],
				'sell_rate' => $row['sellprice'],
				'ledger_type'=> 'Sell'
				
				
				);
				
				$this->db->insert('product_ledger', $legerdata);
				
                $this->db->set('stock', "stock-$amt", FALSE);
                $this->db->where('id', $row['pid']);
                $this->db->update('products');
            }


            $this->db->insert_batch('order_items', $productlist);


            //profit calculation
          /*   $t_profit = 0;
            $this->db->select('geopos_invoice_items.pid, geopos_invoice_items.price, geopos_invoice_items.qty, geopos_products.fproduct_price');
            $this->db->from('geopos_invoice_items');
            $this->db->join('geopos_products', 'geopos_products.pid = geopos_invoice_items.pid', 'left');
            $this->db->where('geopos_invoice_items.tid', $iid);
            $query = $this->db->get();
            $pids = $query->result_array();
            foreach ($pids as $profit) {
                $t_cost = $profit['fproduct_price'] * $profit['qty'];
                $s_cost = $profit['price'] * $profit['qty'];
                $t_profit += $s_cost - $t_cost;
            }
            $data = array('type' => 9, 'rid' => $iid, 'col1' => rev_amountExchange_s($t_profit, $invoice['multi'], $this->aauth->get_user()->loc), 'd_date' => $invoice['invoicedate']);

            $this->db->insert('geopos_metadata', $data); */

            if ($this->db->trans_complete()) {
                $this->db->set('status', 'accepted');
                $this->db->set('convert_to_sell', $iid);
                $this->db->where('id', $id);
                $this->db->update('geopos_quotes');
                return $iid;
            } else {
                return false;
            }
        }else{

                return false;

        }

    }

     public function convert_po($id,$person)
    {

        $invoice = $this->quote_details($id);
        if (empty($invoice)) {
            return false;
        }
        $products = $this->quote_products($id);
        $this->db->trans_start();
        $this->db->select('tid');
        $this->db->from('geopos_purchase');
        $this->db->order_by('tid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $iid = $query->row()->tid + 1;
        } else {
            $iid = 1000;
        }
        $productlist = array();
        $prodindex = 0;
        if($invoice['loc']==$this->aauth->get_user()->loc) {
            $data = array('tid' => $iid, 'invoicedate' => $invoice['invoicedate'], 'invoiceduedate' => $invoice['invoicedate'], 'subtotal' => $invoice['invoicedate'], 'shipping' => $invoice['shipping'], 'discount' => $invoice['discount'], 'tax' => $invoice['tax'], 'total' => 0.00, 'notes' => $invoice['notes'], 'csd' => $person, 'eid' => $invoice['eid'], 'items' => $invoice['items'], 'taxstatus' => $invoice['taxstatus'], 'discstatus' => $invoice['discstatus'], 'format_discount' => $invoice['format_discount'], 'refer' => $invoice['refer'], 'term' => $invoice['term'],'multi' => $invoice['multi'], 'loc' => $invoice['loc'],'created_at' =>date('Y-m-d H:i:s'));
            $this->db->insert('geopos_purchase', $data);
            $iid = $this->db->insert_id();
            foreach ($products as $row) {
                $amt = $row['qty'];
                $data = array(
                    'tid' => $iid,
                    'pid' => $row['pid'],
                    'product' => $row['product'],
                    'code' => $row['code'],
                    'qty' => $amt,
                    'price' => 0.00,
                    'tax' => $row['tax'],
                    'discount' => $row['discount'],
                    'subtotal' => 0.00,
                    'totaltax' => $row['totaltax'],
                    'totaldiscount' => $row['totaldiscount'],
                    'product_des' => $row['product_des'],
                    'unit' => $row['unit']
                );
                $productlist[$prodindex] = $data;
                $prodindex++;
                $this->db->set('qty', "qty+$amt", FALSE);
                $this->db->where('pid', $row['pid']);
                $this->db->update('geopos_products');
            }


            $this->db->insert_batch('geopos_purchase_items', $productlist);




            if ($this->db->trans_complete()) {
                $this->db->set('status', 'accepted');
                $this->db->set('convert_purchase', $iid);
                $this->db->where('id', $id);
                $this->db->update('geopos_quotes');
                return  $iid;
            } else {
                return false;
            }
        }else{

                return false;

        }

    }

    public function currencies()
    {

        $this->db->select('*');
        $this->db->from('geopos_currencies');

        $query = $this->db->get();
        return $query->result_array();

    }

    public function currency_d($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_currencies');
        $this->db->where('id', $id);
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
        $this->db->where('geopos_metadata.type', 2);
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




/*   public function get_quotes_list() {
        $query = "SELECT 
                    MIN(tid) AS tid_start, 
                    MAX(tid) AS tid_end, 
                    invoicedate, 
                    SUM(total) AS total 
                  FROM geopos_quotes 
                  GROUP BY invoicedate 
                  ORDER BY invoicedate ASC";
                  
        return $this->db->query($query)->result_array();
    } */
	
	public function get_quotes_list($limit, $start, $from_date = null, $to_date = null) {
    $is_seller = function_exists('is_seller_user') && is_seller_user();
    $seller_id = (int)$this->session->userdata('user_id');

    $query = "SELECT
                MIN(tid) AS tid_start,
                MAX(tid) AS tid_end,
				COUNT(tid) AS totalinvoice,
                invoicedate,
                SUM(total) AS total
              FROM geopos_quotes";

    // अगर filter दिया गया है तो उसे apply करें
    if ($from_date && $to_date) {
        $query .= " WHERE invoicedate BETWEEN '$from_date' AND '$to_date'";
    } else {
        $query .= " WHERE MONTH(invoicedate) = MONTH(CURRENT_DATE())";
    }

    if ($is_seller) {
        $query .= " AND eid = $seller_id";
    }

    $query .= " GROUP BY invoicedate ORDER BY invoicedate DESC LIMIT $start, $limit";

    return $this->db->query($query)->result();

}

public function get_quotes_count($from_date = null, $to_date = null) {
    $is_seller = function_exists('is_seller_user') && is_seller_user();
    $seller_id = (int)$this->session->userdata('user_id');

    $query = "SELECT COUNT(*) AS total FROM (
                SELECT invoicedate FROM geopos_quotes";

    if ($from_date && $to_date) {
        $query .= " WHERE invoicedate BETWEEN '$from_date' AND '$to_date'";
    } else {
        $query .= " WHERE MONTH(invoicedate) = MONTH(CURRENT_DATE())";
    }

    if ($is_seller) {
        $query .= " AND eid = $seller_id";
    }

    $query .= " GROUP BY invoicedate) AS subquery";

    $result = $this->db->query($query)->row();


    return $result->total;
}

    // Get quote items by date with total quantity aggregation
  /*   public function get_quote_items_by_date($date) {
     
        $query="SELECT qi.pid,
    qi.product, p.name as producname,
    qi.unit, 
    qi.product_article AS article, 
    SUM(qi.qty) AS total_qty,
    COALESCE((
        SELECT SUM(pl.purchage_qty) - SUM(pl.sell_qty)
        FROM product_ledger pl
        WHERE pl.product_id = qi.pid
    ), 0) AS available_stock,
    GREATEST(0, SUM(qi.qty) - COALESCE((
        SELECT SUM(pl.purchage_qty) - SUM(pl.sell_qty)
        FROM product_ledger pl
        WHERE pl.product_id = qi.pid
    ), 0)) AS need_qty,

    (SUM(qi.qty) * p.purchase_price) AS total_purchase,

    (GREATEST(0, SUM(qi.qty) - COALESCE((
        SELECT SUM(pl.purchage_qty) - SUM(pl.sell_qty)
        FROM product_ledger pl
        WHERE pl.product_id = qi.pid
    ), 0)) * p.purchase_price) AS need_purchase

FROM geopos_quotes_items qi
JOIN geopos_quotes q ON qi.tid = q.id
JOIN products p ON qi.pid = p.id 
WHERE q.invoicedate = ?
GROUP BY qi.product, qi.unit, qi.product_article, qi.pid, p.purchase_price;
";
        return $this->db->query($query, [$date])->result_array();
	
		
    }  */
	
	
	public function get_quote_items_by_date($date) {
    $this->load->helper('string');
    $this->db->select('qi.pid, qi.unit, qi.product, qi.code, u.id as store_id, qi.qty, p.name as productname,p.article');
    $this->db->from('geopos_quotes q');
    $this->db->join('geopos_quotes_items qi', 'q.id = qi.tid');
    $this->db->join('users u', 'q.csd = u.id');
    $this->db->join('products p', 'qi.pid = p.id');
    $this->db->where('q.invoicedate', $date);
    $this->db->order_by('qi.product', 'asc');
    $results = $this->db->get()->result_array();

    $product_summary = [];

    foreach ($results as $row) {
        $converted = convert_to_base_unit($row['unit']);
        $conversion_qty = $converted['qty'];
        $base_unit = $converted['unit'];

        $total_qty_in_base = $row['qty'] * $conversion_qty;

        $key = $row['pid'];

        if (!isset($product_summary[$key])) {
            $product_summary[$key] = [
                'pid' => $row['pid'],
                'product' => $row['product'],
                'productname' => $row['productname'],
                'article' => $row['article'],
                'unit' => $base_unit,
                'original_unit' => $base_unit,
                'total_qty' => 0
            ];
        }
        $product_summary[$key]['total_qty'] += $total_qty_in_base;
    }
    $final = [];
	
	foreach ($product_summary as $key => $product) {
    $latest_purchase = $this->get_latest_purchase_info($product['pid'], $date);
    $available = $this->get_total_livebalance($product['pid']);

    $total_qty = round($product['total_qty'], 2);
    $need_qty = max(0, $total_qty - $available);
    $last_price = $latest_purchase['price'] ?? 0;

    // Find the variant ID (varid)
    $varid = '';
    if (!empty($product['original_unit'])) {
        $this->db->select('product_variants.id');
        $this->db->from('product_variants');
        $this->db->join('attribute_values', 'attribute_values.id = product_variants.attribute_value_ids', 'left');
        $this->db->where('product_variants.product_id', $product['pid']);
        $this->db->where('attribute_values.value', $product['original_unit']);
        $var_query = $this->db->get()->row_array();
        if (!empty($var_query)) {
            $varid = $var_query['id'];
        }
    }

    $final[] = [
        'pid' => $product['pid'],
        'product' => $product['product'],
        'article' => $product['article'],
        'productname' => $product['productname'],
        'unit' => $product['unit'],
        'original_unit' => $product['original_unit'],
        'total_qty' => $total_qty,
        'available_stock' => $available,
        'need_qty' => $need_qty,
        'total_purchase' => round($last_price * $total_qty, 2),
        'need_purchase' => round($last_price * $need_qty, 2),
        'last_purchase_rate' => $last_price,
        'last_supplier' => $latest_purchase['supplier_name'] ?? null,
        'varid' => $varid
    ];
}

    return $final;
}



public function get_latest_purchase_info($pid, $date) {
    $this->db->select('pi.price, s.name as supplier_name');
    $this->db->from('geopos_purchase_items pi');
    $this->db->join('geopos_purchase p', 'pi.tid = p.id');
    $this->db->join('geopos_supplier s', 'p.csd = s.id');
    $this->db->where('pi.pid', $pid);
    $this->db->where('p.invoicedate <=', $date);
    $this->db->where('p.is_deleted', 0); // Optional: skip deleted purchases
    $this->db->order_by('p.invoicedate', 'DESC');
    $this->db->limit(1);
    
    $query = $this->db->get();
    return $query->row_array(); // returns ['price' => ..., 'supplier_name' => ...]
}




public function get_total_livebalance($product_id, $from_date = null, $to_date = null)
{
    if (empty($from_date) || empty($to_date)) {
        $from_date = date('Y-m-01'); // 1st of current month
        $to_date = date('Y-m-d');    // today's date
    }

    // === STEP 1: Get filtered entries
    $this->db->select('*');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $product_id);
    $this->db->where('created_date >=', $from_date . ' 00:00:00');
    $this->db->where('created_date <=', $to_date . ' 23:59:59');
    $this->db->order_by('created_date', 'ASC');
    $entries = $this->db->get()->result();

    $prev_balance = 0;
    $current_balance = 0;
    $total_purchage_qty = 0;
    $total_wastage_qty = 0;
    $total_sell_qty = 0;
    $total_purchage_amount = 0;
    $total_sell_amount = 0;

    $ledger_has_stock_movement = false;
    if (!empty($entries)) {
        foreach ($entries as $entry) {
            if ((float)$entry->purchage_qty > 0 || (float)$entry->sell_qty > 0 || (float)$entry->wastage > 0) {
                $ledger_has_stock_movement = true;
                break;
            }
        }
    }

    if (empty($entries) || !$ledger_has_stock_movement) {
        $prod = $this->db->select('type, stock')->from('products')->where('id', $product_id)->get()->row();
        if ($prod) {
            if ($prod->type === 'variable_product') {
                $variant_stock_sum = $this->db->select('SUM(stock) as total_stock')->from('product_variants')->where(['product_id' => $product_id, 'status' => 1])->get()->row();
                $final_closing_balance = $variant_stock_sum ? (float)$variant_stock_sum->total_stock : 0.00;
            } else {
                $final_closing_balance = (float)$prod->stock;
            }
        } else {
            $final_closing_balance = 0.00;
        }
    } else {
        foreach ($entries as $entry) {
            $total_purchage_qty += $entry->purchage_qty;
            $total_sell_qty += $entry->sell_qty;
            $total_wastage_qty += $entry->wastage;
            $total_purchage_amount += $entry->purchage_amount;
            $total_sell_amount += $entry->sell_amount;
            $current_balance += ($entry->purchage_qty - $entry->sell_qty);
        }

        $final_closing_balance = $current_balance - $total_wastage_qty;
    }

   /*  return [
        'previous_balance' => $prev_balance, // Not calculated in this case, since no data before range
        'period_balance' => $current_balance,
        'total_balance' => $final_closing_balance,
        'avg_rate' => $avg_rate,
        'stock_value' => $stock_value,
        'total_purchage_qty' => $total_purchage_qty,
        'total_sell_qty' => $total_sell_qty,
        'total_purchage_amount' => $total_purchage_amount,
        'total_sell_amount' => $total_sell_amount,
    ]; */
	
	return $final_closing_balance;
}
	public function viewstore_quote_items_by_date($date) {
		$this->db->reset_query(); 
     $this->db->select('q.id as quote_id, q.tid, q.csd, u.username as store_name, qi.product, qi.code, qi.qty');
    $this->db->from('geopos_quotes q');
    $this->db->join('geopos_quotes_items qi', 'q.id = qi.tid');
    $this->db->join('users u', 'q.csd = u.id');
    $this->db->where('q.invoicedate', $date);
    $this->db->order_by('q.id, qi.id');
	

    print_r( $this->db->get());
        
      //  return  $query;
		 
		
    }





    public function insert_master($data){
        $this->db->insert('received_master',$data);
        return $this->db->insert_id();
    }

    public function insert_item($data){
        return $this->db->insert('received_items',$data);
    }

    public function get_all(){
        $this->db->select('m.*, COUNT(i.id) as total_items');
        $this->db->from('received_master m');
        $this->db->join('received_items i','i.master_id=m.id','left');
        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $this->db->where("m.id IN (SELECT ri.master_id FROM received_items ri INNER JOIN products p ON p.id = ri.product_id WHERE p.seller_id = $seller_id)", NULL, FALSE);
        }
        $this->db->group_by('m.id');
        return $this->db->get()->result();
    }

    public function get_master($id){
        return $this->db->get_where('received_master',['id'=>$id])->row();
    }

    public function get_items($id){
        return $this->db->get_where('received_items',['master_id'=>$id])->result();
    }

    public function update_master($id,$data){
        $this->db->where('id',$id);
        return $this->db->update('received_master',$data);
    }

    public function delete_items($master_id){
        $this->db->where('master_id',$master_id)->delete('received_items');
    }

    public function delete_master($id){
        $this->db->where('id',$id)->delete('received_master');
    }


}
