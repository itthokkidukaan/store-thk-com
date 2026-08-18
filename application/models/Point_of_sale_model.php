<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Point_of_sale_model extends CI_Model
{
    function get_users($search_term = "")
    {
        $is_seller = function_exists('is_seller_user') && is_seller_user();

        $this->db->select('users.id, users.username, users.mobile, users.email, geopos_customers.name as customer_name');
        $this->db->from('users');
        $this->db->join('geopos_customers', 'RIGHT(users.mobile, 10) = RIGHT(geopos_customers.phone, 10)', 'left');
        $this->db->group_by('users.id');

        if ($is_seller) {
            $this->db->where('users.assigned_seller', (int)$this->session->userdata('user_id'));
        }

        if (!empty($search_term)) {
            $this->db->group_start();
            $this->db->like('users.username', $search_term);
            $this->db->or_like('users.mobile', $search_term);
            $this->db->or_like('users.email', $search_term);
            $this->db->or_like('geopos_customers.name', $search_term);
            $this->db->group_end();
        }

        $this->db->order_by('users.username', 'asc');
        $this->db->limit(20);
        $fetched_records = $this->db->get();
        $users = $fetched_records->result_array();

        $data = array();
        foreach ($users as $user) {
            $name = !empty($user['username']) ? $user['username'] : (!empty($user['customer_name']) ? $user['customer_name'] : '');
            $display_text = trim($name);

            if (!empty($user['mobile'])) {
                $display_text = ($display_text !== '' ? $display_text . ' | ' : '') . $user['mobile'];
            }

            if (!empty($user['email'])) {
                $display_text = ($display_text !== '' ? $display_text . ' | ' : '') . $user['email'];
            }

            $data[] = array(
                "id" => $user['id'],
                "text" => $display_text,
                "number" => $user['mobile'],
                "email" => $user['email'],
                "name" => $name
            );
        }
        return $data;
    }
}
