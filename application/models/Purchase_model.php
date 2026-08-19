<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_model extends CI_Model
{
    var $table = 'geopos_purchase';
    var $column_order = array(null, 'geopos_purchase.tid', 'geopos_supplier.name', 'geopos_purchase.invoicedate', 'geopos_purchase.total', 'geopos_purchase.created_at','geopos_purchase.status', null);
    var $column_search = array('geopos_purchase.tid', 'geopos_supplier.name', 'geopos_purchase.invoicedate', 'geopos_purchase.total','geopos_purchase.status');
    var $order = array('geopos_purchase.tid' => 'desc');

    public function __construct()
    {
        parent::__construct();
    }

    public function lastpurchase()
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
        $this->db->select('*');
        $this->db->from('geopos_warehouse');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('loc', 0);
        }
        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $this->db->group_start();
            $this->db->where('id', 1);
            if ($this->db->field_exists('created_by', 'geopos_warehouse')) {
                $this->db->or_where('created_by', $seller_id);
            }
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->result_array();

    }

    public function purchase_details($id)
    {
        $is_seller = is_seller_user();
        $this->db->select('geopos_purchase.*,geopos_purchase.id AS iid,SUM(geopos_purchase.shipping + geopos_purchase.ship_tax) AS shipping,geopos_supplier.*,geopos_supplier.id AS cid,geopos_terms.id AS termid,geopos_terms.title AS termtit,geopos_terms.terms AS terms');
        $this->db->from($this->table);
        $this->db->where('geopos_purchase.id', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_purchase.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('geopos_purchase.loc', 0);
        } elseif (!BDATA) {
            $this->db->where('geopos_purchase.loc', 0);
        }
        if ($is_seller) {
            $this->db->where('geopos_purchase.eid', (int)$this->session->userdata('user_id'));
        }
        $this->db->join('geopos_supplier', 'geopos_purchase.csd = geopos_supplier.id', 'left');
        $this->db->join('geopos_terms', 'geopos_terms.id = geopos_purchase.term', 'left');
        $query = $this->db->get();
        return $query->row_array();

    }

    public function purchase_products($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_purchase_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        return $query->result_array();
    } 
	
	
	public function supplierlist()
    {
        $this->db->select('id, name, phone');
        $this->db->from('geopos_supplier');
        if (function_exists('is_seller_user') && is_seller_user() && $this->db->field_exists('eid', 'geopos_supplier')) {
            $this->db->where('eid', (int)$this->session->userdata('user_id'));
        }
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function purchase_transactions($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_transactions');
        $this->db->where('tid', $id);
        $this->db->where('ext', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function purchase_delete($id)
    {
		
		$data = array( 
				'is_deleted'      => 1 , 
				'deleted_by' => $this->aauth->get_user()->id, 
				'deleted_date'       => date('Y-m-d H:i:s')
			);

	$this->db->where('id', $id);
	$res = $this->db->update('geopos_purchase', $data);
	if($res){
	return true;
	}
       /*  $this->db->trans_start();
        $this->db->select('pid,qty');
        $this->db->from('geopos_purchase_items');
        $this->db->where('tid', $id);
        $query = $this->db->get();
        $prevresult = $query->result_array(); */
     /*    foreach ($prevresult as $prd) {
            $amt = $prd['qty'];
            $this->db->set('qty', "qty-$amt", FALSE);
            $this->db->where('pid', $prd['pid']);
            $this->db->update('geopos_products');
        } */
/*         $whr = array('id' => $id);
        if ($this->aauth->get_user()->loc) {
            $whr = array('id' => $id, 'loc' => $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
               $whr = array('id' => $id, 'loc' =>0);
        }
        $this->db->delete('geopos_purchase', $whr);
        if ($this->db->affected_rows()) $this->db->delete('geopos_purchase_items', array('tid' => $id));
        if ($this->db->trans_complete()) {
            return true;
        } else {
            return false;
        } */
    }


    private function _get_datatables_query()
    {
        $is_seller = is_seller_user();
        $this->db->select('geopos_purchase.id,geopos_purchase.tid,geopos_purchase.invoicedate,geopos_purchase.invoiceduedate,geopos_purchase.total, geopos_purchase.pamnt, geopos_purchase.created_at, geopos_purchase.status,geopos_supplier.name,geopos_purchase.csd');
        $this->db->from($this->table);
        $this->db->join('geopos_supplier', 'geopos_purchase.csd=geopos_supplier.id', 'left');
            if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_purchase.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('geopos_purchase.loc', 0); }
        if ($is_seller) {
            $this->db->where('geopos_purchase.eid', (int)$this->session->userdata('user_id'));
        }
        $date_column = 'invoicedate'; // default

if ($this->input->post('datetype') == 'invoiceduedate') {
    $date_column = 'invoiceduedate';
}

if ($this->input->post('start_date') && $this->input->post('end_date')) {
    $this->db->where('DATE(geopos_purchase.' . $date_column . ') >=', datefordatabase($this->input->post('start_date')));
    $this->db->where('DATE(geopos_purchase.' . $date_column . ') <=', datefordatabase($this->input->post('end_date')));
}
		
		
		if($this->input->post('supplier') != 'All'){
			 $this->db->where('geopos_purchase.csd', $this->input->post('supplier'));
			
			
		}	
		
		
		if($this->input->post('txttype') != 'All'){
			 $this->db->where('geopos_purchase.status', $this->input->post('txttype'));
			
			
		}
		
		$this->db->where('geopos_purchase.is_deleted', 0);
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
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }



function get_totalpurchase(){
        $is_seller = is_seller_user();
        $this->db->from('geopos_purchase');
		
		$this->db->join('geopos_supplier', 'geopos_purchase.csd=geopos_supplier.id', 'left');
            if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_purchase.loc', $this->aauth->get_user()->loc);
        }
        elseif(!BDATA) { $this->db->where('geopos_purchase.loc', 0); }
        if ($is_seller) {
            $this->db->where('geopos_purchase.eid', (int)$this->session->userdata('user_id'));
        }
	$date_column = 'invoicedate'; // default

if ($this->input->post('datetype') == 'invoiceduedate') {
    $date_column = 'invoiceduedate';
}

if ($this->input->post('start_date') && $this->input->post('end_date')) {
    $this->db->where('DATE(geopos_purchase.' . $date_column . ') >=', datefordatabase($this->input->post('start_date')));
    $this->db->where('DATE(geopos_purchase.' . $date_column . ') <=', datefordatabase($this->input->post('end_date')));
}
		
		if($this->input->post('supplier') != 'All'){
			 $this->db->where('geopos_purchase.csd', $this->input->post('supplier'));
			
			
		}	
		
		
		if($this->input->post('txttype') != 'All'){
			 $this->db->where('geopos_purchase.status', $this->input->post('txttype'));
			
			
		}
		
		$this->db->where('geopos_purchase.is_deleted', 0);
		
		  $this->db->select('SUM(geopos_purchase.total) AS total, SUM(geopos_purchase.total- geopos_purchase.pamnt) as balance');
		  
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
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
     
		 $query = $this->db->get();
		 
	
		 return $query->row_array();
	}
    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
		
	//	echo $this->db->last_query();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
	
    public function count_all()
    {
        $this->db->from($this->table);
		$this->db->where('geopos_purchase.is_deleted', 0);
           if ($this->aauth->get_user()->loc) {
            $this->db->where('geopos_purchase.loc', $this->aauth->get_user()->loc);
        }
        if (is_seller_user()) {
            $this->db->where('geopos_purchase.eid', (int)$this->session->userdata('user_id'));
        }
		     $date_column = 'invoicedate'; // default

if ($this->input->post('datetype') == 'invoiceduedate') {
    $date_column = 'invoiceduedate';
}

if ($this->input->post('start_date') && $this->input->post('end_date')) {
    $this->db->where('DATE(geopos_purchase.' . $date_column . ') >=', datefordatabase($this->input->post('start_date')));
    $this->db->where('DATE(geopos_purchase.' . $date_column . ') <=', datefordatabase($this->input->post('end_date')));
}
		if($this->input->post('supplier') != 'All'){
			 $this->db->where('geopos_purchase.csd', $this->input->post('supplier'));
			
			
		}
		if($this->input->post('txttype') != 'All'){
			 $this->db->where('geopos_purchase.status', $this->input->post('txttype'));
			
			
		}
		
        elseif(!BDATA) { $this->db->where('geopos_purchase.loc', 0); }
        return $this->db->count_all_results();
    }


    public function billingterms()
    {
        $this->db->select('id,title');
        $this->db->from('geopos_terms');
        $this->db->where('type', 4);
        $this->db->or_where('type', 0);
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

    public function currency_d($id)
    {
        $this->db->select('*');
        $this->db->from('geopos_currencies');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function employee($id)
    {
        $this->db->select('geopos_employees.name,geopos_employees.sign,geopos_users.roleid');
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
        $this->db->where('geopos_metadata.type', 4);
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
	
	
	
	 public function insert_puquotes($data)
    {
        $this->db->insert('seller_quotation', $data);
		
	//	echo $this->db->last_query();
		
        return $this->db->insert_id();
    }

    public function insert_quoitem($data)
    {
        $this->db->insert('seller_quotation_items', $data);
    }

    public function get_all_orders()
    {
        return $this->db->get('orders')->result_array();
    }
	
	
	
	

    // Custom variables for supplier quotation datatable
    var $sq_table = 'seller_quotation';
    var $sq_column_order = array(null, 'seller_quotation.invoice_no', 'geopos_supplier.name', 'seller_quotation.order_date');
    var $sq_column_search = array('seller_quotation.invoice_no', 'geopos_supplier.name');
    var $sq_order = array('seller_quotation.id' => 'desc');

    // Custom datatable query for supplier quotations
   /*  private function _sq_get_datatables_query()
    {
        $this->db->select("seller_quotation.*, geopos_supplier.name as supplier_name,
            (SELECT COUNT(*) FROM seller_quotation_items WHERE seller_quotation_items.order_id = seller_quotation.id) as total_items,
            (SELECT COUNT(*) FROM seller_quotation_items WHERE seller_quotation_items.order_id = seller_quotation.id AND seller_quotation_items.fill_rate IS NOT NULL) as filled_items,
            CASE 
                WHEN EXISTS (
                    SELECT 1 FROM seller_quotation_items 
                    WHERE seller_quotation_items.order_id = seller_quotation.id 
                    AND seller_quotation_items.fill_rate IS NOT NULL
                )
                THEN 'Partially Filled'
                ELSE 'Pending'
            END as status
        ");
        $this->db->from($this->sq_table);
        $this->db->join('geopos_supplier', 'geopos_supplier.id = seller_quotation.supplier_id', 'left');

        // Date filtering
        if ($this->input->post('from_date') && $this->input->post('to_date')) {
            $this->db->where('seller_quotation.order_date >=', $this->input->post('from_date'));
            $this->db->where('seller_quotation.order_date <=', $this->input->post('to_date'));
        }

        // Search
        $i = 0;
        foreach ($this->sq_column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->sq_column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        // Ordering
        if (isset($_POST['order'])) {
            $this->db->order_by($this->sq_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->sq_order)) {
            $order = $this->sq_order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    } */

  // Get datatables
public function get_supplier_quotation_datatables()
{
    $this->_sq_get_datatables_query();

    if ($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    
    $query = $this->db->get();
    return $query->result();
}


public function get_supplier_challan_datatables()
{
    $this->_sq_get_datatables_query();

    if ($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
    $this->db->where("sq.adm_status", 1);
    $query = $this->db->get();
    return $query->result();
}

private function _sq_get_datatables_query($count_only = false)
{
    if ($count_only) {
        $this->db->select('sq.id');
    } else {
        $this->db->select("sq.*, s.name as supplier_name,
        (SELECT COUNT(*) FROM seller_quotation_items sqi WHERE sqi.order_id = sq.id) as total_item,
        (SELECT COUNT(*) FROM seller_quotation_items sqi WHERE sqi.order_id = sq.id AND sqi.fill_rate IS NOT NULL) as fill_item");
    }
    $this->db->from('seller_quotation sq');
    $this->db->join('geopos_supplier s', 's.id = sq.supplier_id', 'left');

    if (is_seller_user()) {
        $seller_id = (int)$this->session->userdata('user_id');
        $this->db->where("sq.id IN (SELECT sqi.order_id FROM seller_quotation_items sqi INNER JOIN products p ON p.id = sqi.item_id WHERE p.seller_id = $seller_id)", NULL, FALSE);
    }

    // Date filter
    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
        $from = $_POST['from_date'];
        $to = $_POST['to_date'];
        $this->db->where("sq.order_date >=", $from);
        $this->db->where("sq.order_date <=", $to);
    }

    // Search filter
    if (!empty($_POST['search']['value'])) {
        $search = $_POST['search']['value'];
        $this->db->group_start();
        $this->db->like("sq.invoice_no", $search);
        $this->db->or_like("s.name", $search);
        $this->db->or_like("sq.order_date", $search);
        $this->db->group_end();
    }

    if (isset($_POST['order'])) {
        $this->db->order_by($_POST['order']['0']['column'], $_POST['order']['0']['dir']);
    } else {
        $this->db->order_by('sq.id', 'DESC');
    }
}

public function count_filtered_supplier_quotation()
{
    $this->_sq_get_datatables_query(true);
    return $this->db->count_all_results();
}

public function count_filtered_supplier_challan()
{
    $this->_sq_get_datatables_query(true);
	 $this->db->where("sq.adm_status", 1);
    return $this->db->count_all_results();
}

public function count_all_supplier_quotation()
{
    $this->db->from('seller_quotation');
    if (is_seller_user()) {
        $seller_id = (int)$this->session->userdata('user_id');
        $this->db->where("seller_quotation.id IN (SELECT sqi.order_id FROM seller_quotation_items sqi INNER JOIN products p ON p.id = sqi.item_id WHERE p.seller_id = $seller_id)", NULL, FALSE);
    }
    return $this->db->count_all_results();
}

public function count_all_supplier_challan()
{
	 $this->db->where("adm_status", 1);
    $this->db->from('seller_quotation');
    if (is_seller_user()) {
        $seller_id = (int)$this->session->userdata('user_id');
        $this->db->where("seller_quotation.id IN (SELECT sqi.order_id FROM seller_quotation_items sqi INNER JOIN products p ON p.id = sqi.item_id WHERE p.seller_id = $seller_id)", NULL, FALSE);
    }
    return $this->db->count_all_results();
}

public function get_supplier_quotation($id)
{
    $this->db->select('sq.*, s.name as supplier_name');
    $this->db->from('seller_quotation sq');
    $this->db->join('geopos_supplier s', 's.id = sq.supplier_id', 'left');
    $this->db->where('sq.id', $id);
    return $this->db->get()->row();
}

public function get_supplier_quotation_items($id)
{
    $this->db->from('seller_quotation_items');
    $this->db->where('order_id', $id);
    return $this->db->get()->result();
}


public function get_supplier_challan_items($id)
{
    $this->db->from('seller_quotation_items');
    $this->db->where('adm_status', 1);
    $this->db->where('order_id', $id);
    return $this->db->get()->result();
}


 public function get_supplier_by_phone($phone)
    {
        return $this->db->get_where('geopos_supplier', ['phone' => $phone])->row();
    }


public function get_quotation_by_reference($supplier_id, $reference_no)
{
    return $this->db->order_by('id', 'DESC')->get_where('seller_quotation', [
        'supplier_id' => $supplier_id,
        'reference_no' => $reference_no
    ])->row();
}

    public function get_latest_supplier_quotation($supplier_id)
    {
        return $this->db
            ->order_by('id', 'DESC')
            ->get_where('seller_quotation', ['supplier_id' => $supplier_id])
            ->row();
    }

  /*   public function get_supplier_quotation_items($order_id)
    {
        return $this->db->get_where('seller_quotation_items', ['order_id' => $order_id])->result();
    } */

    public function update_supplier_item($item)
    {
        $this->db->where('id', $item['id']);
        $this->db->update('seller_quotation_items', [
            'fill_quantity' => $item['fill_quantity'],
            'fill_rate' => $item['fill_rate'],
            'description' => $item['description'],
            'amount' => (float)$item['fill_quantity'] * (float)$item['fill_rate'],
        ]);
    }
	
	
	public function get_quotations_by_reference($reference_no)
{
    $this->db->select('sq.id, sq.supplier_id, sq.invoice_no, s.name as supplier_name, s.phone ');
    $this->db->from('seller_quotation sq');
	 $this->db->join('geopos_supplier s', 's.id = sq.supplier_id', 'left');
    $this->db->where('reference_no', $reference_no);
    return $this->db->get()->result();
}

public function get_comparison_items($reference_no)
{
    $this->db->select('sq.id as quotation_id, sq.supplier_id, sqi.id as itemid, sqi.item_id as product_id, sqi.unit, sqi.item_name, sqi.quantity, sqi.fill_quantity, sqi.fill_rate, sqi.adm_status');
    $this->db->from('seller_quotation_items sqi');
    $this->db->join('seller_quotation sq', 'sq.id = sqi.order_id');
    $this->db->where('sq.reference_no', $reference_no);
    $result = $this->db->get()->result();

    // Group items by item_name
    $grouped = [];
    foreach ($result as $row) {
        $display_name = $row->item_name;
        if (!empty($row->unit) && stripos($display_name, $row->unit) === false) {
            $display_name .= ' - ' . $row->unit;
        }
        $group_key = $display_name;
        if (!empty($row->product_id) && !empty($row->unit)) {
            $group_key = $row->product_id . '|' . $row->unit . '|' . $display_name;
        } elseif (!empty($row->product_id)) {
            $group_key = $row->product_id . '|' . $display_name;
        }
        $row->display_name = $display_name;
        $grouped[$group_key][$row->supplier_id] = $row;
    }
    return $grouped;
}



  public function get_master($master_id) {
        return $this->db->get_where('received_master', ['id' => $master_id])->row();
    }

    // Get items for master (assumes child items table is received_items_items)
    public function get_items_by_master($master_id) {
        return $this->db->get_where('received_items', ['master_id' => $master_id])->result();
    }

    // Insert seller quotation
    public function insert_seller_quotation($data) {
        $this->db->insert('seller_quotation', $data);
        return $this->db->insert_id();
    }

    // Insert multiple quotation items
    public function insert_seller_quotation_items_batch($items) {
        if (!empty($items)) $this->db->insert_batch('seller_quotation_items', $items);
    }

    // Mark master as converted
    public function mark_master_converted($master_id) {
        $this->db->where('id', $master_id)
                 ->update('received_items', ['converted_to_challan' => 1]);
    }

    // Check if already converted
    public function is_master_converted($master_id) {
        $row = $this->db->select('converted_to_challan')
                        ->get_where('received_items', ['id' => $master_id])
                        ->row();
        return (!empty($row->converted_to_challan));
    }


}
