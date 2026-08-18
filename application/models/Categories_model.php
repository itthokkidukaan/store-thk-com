<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Categories_model extends CI_Model
{

    public function category_list($type = 0, $rel = 0)
    {
        $query = $this->db->query("SELECT id, name FROM categories WHERE parent_id='$rel'

ORDER BY id DESC");
        return $query->result_array();
    }

    public function warehouse_list()
    {
        $where = '';


        if (!BDATA) $where = "WHERE  (loc=0) ";
        if ($this->aauth->get_user()->loc) {
            $where = "WHERE  (loc=" . $this->aauth->get_user()->loc . " ) ";
            if (BDATA) $where = "WHERE  (loc=" . $this->aauth->get_user()->loc . " OR geopos_warehouse.loc=0) ";
        }


        $query = $this->db->query("SELECT id,title
FROM geopos_warehouse $where 

ORDER BY id DESC");
        return $query->result_array();
    }

    public function category_stock()
    {
        $whr = '';
        if (!BDATA) $whr = "WHERE  (geopos_warehouse.loc=0) ";
        if ($this->aauth->get_user()->loc) {
            $whr = "WHERE  (geopos_warehouse.loc=" . $this->aauth->get_user()->loc . " ) ";
            if (BDATA) $whr = "WHERE  (geopos_warehouse.loc=" . $this->aauth->get_user()->loc . " OR geopos_warehouse.loc=0) ";
        }

        $query = $this->db->query("SELECT c.*,p.pc,p.salessum,p.worthsum,p.qty FROM geopos_product_cat AS c LEFT JOIN ( SELECT geopos_products.pcat,COUNT(geopos_products.pid) AS pc,SUM(geopos_products.product_price*geopos_products.qty) AS salessum, SUM(geopos_products.fproduct_price*geopos_products.qty) AS worthsum,SUM(geopos_products.qty) AS qty FROM geopos_products LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id  $whr GROUP BY geopos_products.pcat ) AS p ON c.id=p.pcat WHERE c.c_type=0");
        return $query->result_array();
    }


    public function category_lists(){


        

         $query = $this->db->query("SELECT c.*,pc.name as parrentname FROM categories AS c  LEFT JOIN categories as pc  ON pc.parent_id=c.id  ORDER BY c.id DESC");
         return $query->result_array();
    }
    
    public function category_sub_stock($id = 0)
    {
        $whr = '';
        if (!BDATA) $whr = "WHERE  (geopos_warehouse.loc=0) ";
        if ($this->aauth->get_user()->loc) {
            $whr = "WHERE  (geopos_warehouse.loc=" . $this->aauth->get_user()->loc . " ) ";
            if (BDATA) $whr = "WHERE  (geopos_warehouse.loc=" . $this->aauth->get_user()->loc . " OR geopos_warehouse.loc=0) ";
        }

        $whr2 = '';

        $query = $this->db->query("SELECT c.*,p.pc,p.salessum,p.worthsum,p.qty,p.sub_id FROM geopos_product_cat AS c LEFT JOIN ( SELECT geopos_products.sub_id,COUNT(geopos_products.pid) AS pc,SUM(geopos_products.product_price*geopos_products.qty) AS salessum, SUM(geopos_products.fproduct_price*geopos_products.qty) AS worthsum,SUM(geopos_products.qty) AS qty FROM geopos_products LEFT JOIN geopos_warehouse ON geopos_products.warehouse=geopos_warehouse.id  $whr GROUP BY geopos_products.sub_id ) AS p ON c.id=p.sub_id WHERE c.c_type=1 AND c.rel_id='$id'");
        return $query->result_array();
    }

  /*   public function warehouse()
    {
        $where = '';
        if ($this->aauth->get_user()->loc) {
            $where = ' WHERE c.loc=' . $this->aauth->get_user()->loc;

            if (BDATA) $where = ' WHERE c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0';
        } elseif (!BDATA) {
            $where = ' WHERE  c.loc=0';
        }
        $query = $this->db->query("SELECT c.*,p.pc,p.salessum,p.worthsum, p.qty FROM geopos_warehouse AS c LEFT JOIN ( SELECT warehouse,COUNT(id) AS pc,SUM(product_price*stock) AS salessum, SUM(fproduct_price*stock) AS worthsum,SUM(stock) AS qty FROM  products GROUP BY warehouse ) AS p ON c.id=p.warehouse  $where");
        return $query->result_array();
    } */ 


	public function warehouse()
{
    $is_seller = is_seller_user();
    $where = '';
    if ($this->aauth->get_user()->loc) {
        $where = ' WHERE c.loc=' . $this->aauth->get_user()->loc;
        if (BDATA) $where = ' WHERE c.loc=' . $this->aauth->get_user()->loc . ' OR c.loc=0';
    } elseif (!BDATA) {
        $where = ' WHERE  c.loc=0';
    }

    // PHP date range for current month
    $start_date = date('Y-m-01 00:00:00'); // 1st of current month
    $end_date = date('Y-m-d 23:59:59');    // today (current time)

    $seller_where = '';
    if ($is_seller) {
        $seller_id = (int)$this->session->userdata('user_id');
        $seller_where = " WHERE pr.seller_id = $seller_id ";
    }

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

    return $query->result_array();
}


    public function cat_ware($id, $loc = 0)
    {
        $qj = '';
        if ($loc) $qj = "AND w.loc='$loc'";
        $query = $this->db->query("SELECT c.id AS cid, w.id AS wid,c.title AS catt,w.title AS watt FROM geopos_products AS p LEFT JOIN geopos_product_cat AS c ON p.pcat=c.id LEFT JOIN geopos_warehouse AS w ON p.warehouse=w.id WHERE
p.pid='$id' $qj ");
        return $query->row_array();
    }


    public function addnew($cat_name, $cat_desc, $cat_type = 0, $cat_rel = 0)
    {
        if (!$cat_type) $cat_type = 0;
        if (!$cat_rel) $cat_rel = 0;
        $data = array(
            'title' => $cat_name,
            'extra' => $cat_desc,
            'c_type' => $cat_type,
            'rel_id' => $cat_rel
        );

        if ($cat_type) {
            $url = "<a href='" . base_url('productcategory/add_sub') . "' class='btn btn-blue btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span>  </a> <a href='" . base_url('productcategory/view?id=' . $cat_rel) . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>";
        } else {
            $url = "<a href='" . base_url('productcategory/add') . "' class='btn btn-blue btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span>  </a> <a href='" . base_url('productcategory') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>";
        }

        if ($this->db->insert('geopos_product_cat', $data)) {
            $this->aauth->applog("[Category Created] $cat_name ID " . $this->db->insert_id(), $this->aauth->get_user()->username);
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED') . " $url"));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function addwarehouse($cat_name, $cat_desc, $lid)
    {
        $data = array(
            'title' => $cat_name,
            'extra' => $cat_desc,
            'loc' => $lid
        );

        if ($this->db->insert('geopos_warehouse', $data)) {
            $this->aauth->applog("[WareHouse Created] $cat_name ID " . $this->db->insert_id(), $this->aauth->get_user()->username);
               $url = "<a href='" . base_url('productcategory/addwarehouse') . "' class='btn btn-blue btn-lg'><span class='fa fa-plus-circle' aria-hidden='true'></span>  </a> <a href='" . base_url('productcategory/warehouse') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>";
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('ADDED') . $url));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function edit($catid, $product_cat_name, $product_cat_desc, $cat_type, $cat_rel, $old_cat_type)
    {
         if (!$cat_rel) $cat_rel = 0;
        $data = array(
            'title' => $product_cat_name,
            'extra' => $product_cat_desc,
            'c_type' => $cat_type,
            'rel_id' => $cat_rel
        );
        $this->db->set($data);
        $this->db->where('id', $catid);
        if ($this->db->update('geopos_product_cat')) {
            if ($cat_type != $old_cat_type && $cat_type && $cat_type) {
                $data = array('pcat' => $cat_rel);
                $this->db->set($data);
                $this->db->where('sub_id', $catid);
                $this->db->update('geopos_products');
            }
            $this->aauth->applog("[Category Edited] $product_cat_name ID " . $catid, $this->aauth->get_user()->username);
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function editwarehouse($catid, $product_cat_name, $product_cat_desc, $lid)
    {
        $data = array(
            'title' => $product_cat_name,
            'extra' => $product_cat_desc,
            'loc' => $lid
        );


        $this->db->set($data);
        $this->db->where('id', $catid);

        if ($this->db->update('geopos_warehouse')) {
            $this->aauth->applog("[Warehouse Edited] $product_cat_name ID " . $catid, $this->aauth->get_user()->username);
            echo json_encode(array('status' => 'Success', 'message' =>
                $this->lang->line('UPDATED')));
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }

    }

    public function sub_cat($id = 0)
    {
        $this->db->select('*');
        $this->db->from('geopos_product_cat');
        $this->db->where('rel_id', $id);
        $this->db->where('c_type', 1);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

       public function sub_cat_curr($id = 0)
    {
        $this->db->select('*');
        $this->db->from('geopos_product_cat');
        $this->db->where('id', $id);
        $this->db->where('c_type', 1);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function sub_cat_list($id = 0)
    {
        $this->db->select('*');
        $this->db->from('geopos_product_cat');
        $this->db->where('rel_id', $id);
        $this->db->where('c_type', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
  public function get_customer($id) {
        $this->db->select('username AS name, mobile');
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows()) {
		
            return $query->row_array();  // ['name' => ..., 'mobile' => ...]
        } else {
            return ['name' => 'Unknown', 'mobile' => ''];
        }
		
		 
    }
	
	
	 public function get_supplier($id) {
        $this->db->select('name, phone AS mobile');
        $this->db->from('geopos_supplier');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows()) {
            return $query->row_array();  // ['name' => ..., 'mobile' => ...]
        } else {
            return ['name' => 'Unknown', 'mobile' => ''];
        }
    }






    var $table = 'wastage';
    var $column_order = array(null, 'product_name', 'qty', 'sell_rate', 'purchase_rate', 'created_date', 'wastage_reson');
    var $column_search = array('product_name', 'wastage_reson');
    var $order = array('created_date' => 'desc');

    private function _get_wastage_query()
    {
        $this->db->from($this->table);

        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $this->db->where("product_id IN (SELECT id FROM products WHERE seller_id = $seller_id)", NULL, FALSE);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if($_POST['search']['value']) {
                if($i===0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if(count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if(isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_wastage_datatables()
    {
        $this->_get_wastage_query();
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_wastage()
    {
        $this->_get_wastage_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_wastage()
    {
        $this->db->from($this->table);
        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $this->db->where("product_id IN (SELECT id FROM products WHERE seller_id = $seller_id)", NULL, FALSE);
        }
        return $this->db->count_all_results();
    }


}