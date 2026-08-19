<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Home_model extends CI_Model
{

    public function count_new_orders($type = '')
    {
        $is_seller = $this->ion_auth->is_seller();
        $res = $this->db->select('count(o.id) as counter');

		   $userid = $this->aauth->get_user()->id;
		
		
		$this->db->where('o.orderdone_by', $type);
        if ($is_seller) {
            $this->db->join('order_items oi', 'oi.order_id=o.id', 'left');
            $this->db->where('oi.seller_id', $userid);
            $this->db->select('count(DISTINCT o.id) as counter', false);
        } else if ($userid > 1) {
            $this->db->where('o.created_by', $userid);
        }
		 
        $res = $this->db->get('`orders` o')->result_array();
        // print_r($this->db->last_query());
        return $res[0]['counter'];
    }

    public function count_orders_by_status($status)
    {
        $is_seller = $this->ion_auth->is_seller();
        $res = $this->db->select('count(id) as counter');
        $this->db->where('active_status', $status);
		   $userid = $this->aauth->get_user()->id;
		
		
        if ($is_seller) {
            $this->db->join('order_items oi', 'oi.order_id=o.id', 'left');
            $this->db->where('oi.seller_id', $userid);
            $this->db->select('count(DISTINCT o.id) as counter', false);
        } else if ($userid > 1) {
            $this->db->where('o.created_by', $userid);
        }
        $res = $this->db->get('`orders` o')->result_array();
        return $res[0]['counter'];
    }

    public function count_new_users()
    {
		
        if ($this->ion_auth->is_seller()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $res = $this->db->select('count(DISTINCT u.id) as counter')
                ->join('users_groups ug', ' ug.`user_id` = u.`id` ')
                ->where('ug.group_id=2')
                ->where('u.assigned_seller', $seller_id)
                ->get('`users u`')->result_array();
        } else if ($this->aauth->get_user()->roleid != 1) {
            //$this->db->where('eid', $this->aauth->get_user()->id);
            $res = $this->db->select('count(u.id) as counter')->join('users_groups ug', ' ug.`user_id` = u.`id` ')
                ->where('ug.group_id=2 AND u.assigned='.$this->aauth->get_user()->id)
                ->get('`users u`')->result_array();
        }else{
			
			 $res = $this->db->select('count(u.id) as counter')->join('users_groups ug', ' ug.`user_id` = u.`id` ')
            ->where('ug.group_id=2')
            ->get('`users u`')->result_array();
			
		}
		
		
       
        return $res[0]['counter'];
    }

    public function count_delivery_boys()
    {
        if ($this->ion_auth->is_seller()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $res = $this->db->select('COUNT(DISTINCT oi.delivery_boy_id) as counter', false)
                ->from('order_items oi')
                ->where('oi.seller_id', $seller_id)
                ->where('oi.delivery_boy_id IS NOT NULL', null, false)
                ->where('oi.delivery_boy_id >', 0)
                ->get()->result_array();
            return $res[0]['counter'];
        }

        $res = $this->db->select('count(u.id) as counter')->where('ug.group_id', '3')->join('users_groups ug', 'ug.user_id=u.id')
            ->get('`users` u')->result_array();
        return $res[0]['counter'];
    }

    public function count_products($seller_id = "")
    {
        $res = $this->db->select('count(id) as counter ');
        if (!empty($seller_id) && $seller_id != '') {
            $res->where('seller_id=' . $seller_id);
        }
        $count = $res->get('`products`')->result_array();
        return $count[0]['counter'];
    }

    public function count_products_stock_low_status($seller_id = "")
    {
        $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;
        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')->join('product_variants', 'product_variants.product_id = p.id');
        $where = "p.stock_type is  NOT NULL";

        $count_res->where($where);
        $count_res->group_Start();
        $count_res->where('p.stock  <=', $low_stock_limit);
        $count_res->where('p.availability  =', '1');
        $count_res->or_where('product_variants.stock  <=', $low_stock_limit);
        $count_res->where('product_variants.availability  =', '1');
        $count_res->group_End();
        if (!empty($seller_id) && $seller_id != '') {
            $count_res->where('p.seller_id  =', $seller_id);
        }
        $product_count = $count_res->get('products p')->result_array();
        return $product_count[0]['total'];
    }

    public function count_products_availability_status($seller_id = "")
    {
        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')->join('product_variants', 'product_variants.product_id = p.id');
        $where = "p.stock_type is  NOT NULL";
        $count_res->where($where);
        $count_res->group_Start();
        $count_res->where('p.stock ', '0');
        $count_res->where('p.availability ', '0');
        $count_res->or_where('product_variants.stock ', '0');
        $count_res->where('product_variants.availability', '0');
        $count_res->group_End();
        if (!empty($seller_id) && $seller_id != '') {
            $count_res->where('p.seller_id  =', $seller_id);
        }
        $product_count = $count_res->get('products p')->result_array();

        return  $product_count[0]['total'];
    }

    public function approved_seller()
    {

        $query_approved_seller = $this->db->select('*')->where('status', '1')->get('seller_data');

        $approved_seller = $query_approved_seller->result_array();

        return $approved_seller;
    }


    public function count_approved_seller()
    {

        $query_approved_seller = $this->db->select('*')->where('status', '1')->get('seller_data');

        $count_approved_seller = $query_approved_seller->num_rows();

        return $count_approved_seller;
    }

    public function not_approved_seller()
    {

        $query_not_approved_seller = $this->db->select('*')->where('status', '2')->get('seller_data');

        $not_approved_seller = $query_not_approved_seller->result_array();

        return $not_approved_seller;
    }


    public function count_not_approved_seller()
    {

        $query_not_approved_seller = $this->db->select('*')->where('status', '2')->get('seller_data');

        $count_not_approved_seller = $query_not_approved_seller->num_rows();

        return $count_not_approved_seller;
    }

    public function deactive_seller()
    {

        $query_deactive_seller = $this->db->select('*')->where('status', '0')->get('seller_data');

        $deactive_seller = $query_deactive_seller->result_array();

        return $deactive_seller;
    }


    public function count_deactive_seller()
    {

        $query_deactive_seller = $this->db->select('*')->where('status', '')->get('seller_data');

        $count_deactive_seller = $query_deactive_seller->num_rows();

        return $count_deactive_seller;
    }

    public function total_earnings($type = "admin")
    {
        $select = "";
        if ($type == "admin") {
            $select = "SUM(admin_commission_amount) as total ";
        }
        if ($type == "seller") {
            $select = "SUM(seller_commission_amount) as total ";
        }
        if ($type == "overall") {
            $select = "SUM(sub_total) as total ";
        }
        $count_res = $this->db->select($select);
        $where = "is_credited=1";
        $count_res->where($where);

        $product_count = $count_res->get('order_items')->result_array();
        return $product_count[0]['total'];
    }
}
