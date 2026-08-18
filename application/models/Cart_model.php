<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Cart_model extends CI_Model
{
    function add_to_cart($data, $check_status = TRUE)
    {
        $data = escape_array($data);
        $product_variant_id = explode(',', $data['product_variant_id']);
        $qty = explode(',', $data['qty']);

        if ($check_status == TRUE) {
            $check_current_stock_status = validate_stock($product_variant_id, $qty);
            if (!empty($check_current_stock_status) && $check_current_stock_status['error'] == true) {
                $check_current_stock_status['csrfName'] = $this->security->get_csrf_token_name();
                $check_current_stock_status['csrfHash'] = $this->security->get_csrf_hash();
                print_r(json_encode($check_current_stock_status));
                return true;
            }
        }

        for ($i = 0; $i < count($product_variant_id); $i++) {
            $cart_data = [
                'user_id' => $data['user_id'],
                'product_variant_id' => $product_variant_id[$i],
                'qty' => $qty[$i],
                'is_saved_for_later' => (isset($data['is_saved_for_later']) && !empty($data['is_saved_for_later']) && $data['is_saved_for_later'] == '1') ? $data['is_saved_for_later'] : '0',
            ];
            if ($qty[$i] == 0) {
                $this->remove_from_cart($cart_data);
            } else {
                if ($this->db->select('*')->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i]])->get('cart')->num_rows() > 0) {
                    $this->db->set($cart_data)->where(['user_id' => $data['user_id'], 'product_variant_id' => $product_variant_id[$i]])->update('cart');
                } else {
                    $this->db->insert('cart', $cart_data);
                }
            }
        }
        return false;
    }

    function remove_from_cart($data)
    {
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $this->db->where('user_id', $data['user_id']);
            if (isset($data['product_variant_id'])) {
                $product_variant_id = explode(',', $data['product_variant_id']);
                $this->db->where_in('product_variant_id', $product_variant_id);
            }
            return $this->db->delete('cart');
        } else {
            return false;
        }
    }

    function get_user_cart($user_id, $is_saved_for_later = 0, $product_variant_id = '')
    {

        $q = $this->db->join('product_variants pv', 'pv.id=c.product_variant_id')
            ->join('products p', 'p.id=pv.product_id')
            ->join('`taxes` tax', 'tax.id = p.tax', 'LEFT')
            ->join('`seller_data` sd', 'sd.user_id = p.seller_id')
            ->where(['c.user_id' => $user_id, 'p.status' => '1', 'pv.status' => 1, 'sd.status' => 1, 'qty !=' => '0', 'is_saved_for_later' => $is_saved_for_later]);
        if (!empty($product_variant_id)) {
            $q->where('c.product_variant_id', $product_variant_id);
        }
        $res =  $q->select('c.*,p.is_prices_inclusive_tax,p.name,p.id,p.image,p.short_description,p.minimum_order_quantity,p.quantity_step_size,p.total_allowed_quantity,pv.price,pv.special_price,pv.id as product_variant_id,tax.percentage as tax_percentage')->order_by('c.id', 'DESC')->get('cart c')->result_array();


        if (!empty($res)) {
            $res = array_map(function ($d) {
                // Get variant prices from database (same as edit product form)
                $variant_price = floatval($d['price']);
                $variant_special_price = floatval($d['special_price']);
                
                // Use special_price if available, otherwise use price
                if ($variant_special_price > 0 && $variant_special_price < $variant_price) {
                    $base_price = $variant_special_price;
                } else {
                    $base_price = $variant_price;
                }
                
                // Apply tax if tax_percentage > 0 (same as edit form logic)
                $percentage = (isset($d['tax_percentage']) && intval($d['tax_percentage']) > 0 && $d['tax_percentage'] != null) ? $d['tax_percentage'] : '0';
                if ((isset($d['is_prices_inclusive_tax']) && $d['is_prices_inclusive_tax'] == 0) || (!isset($d['is_prices_inclusive_tax'])) && $percentage > 0) {
                    $tax_amount = $base_price * ($percentage / 100);
                    $final_price = $base_price + $tax_amount;
                } else {
                    $tax_amount = 0;
                    $final_price = $base_price;
                }
                
                // Set both price and special_price to final_price so frontend shows single price without strikethrough
                $d['price'] = $final_price;
                $d['special_price'] = $final_price;
                $d['minimum_order_quantity'] =  (isset($d['minimum_order_quantity']) && !empty($d['minimum_order_quantity'])) ? $d['minimum_order_quantity'] : 1;
                $d['net_amount'] = $d['special_price'] - $tax_amount;
                $d['tax_percentage'] =  (isset($d['tax_percentage']) && !empty($d['tax_percentage'])) ? $d['tax_percentage'] : '';
                $d['tax_amount'] =  (isset($tax_amount) && !empty($tax_amount)) ? $tax_amount : 0;
                $d['quantity_step_size'] =  (isset($d['quantity_step_size']) && !empty($d['quantity_step_size'])) ? $d['quantity_step_size'] : 1;
                $d['total_allowed_quantity'] =  isset($d['total_allowed_quantity']) && !empty($d['total_allowed_quantity']) ? $d['total_allowed_quantity'] : '';
                $d['product_variants'] = get_variants_values_by_id($d['product_variant_id']);
                return $d;
            }, $res);
        }
        return $res;
    }
}
