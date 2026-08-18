<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products_model extends CI_Model
{
    var $table = 'products';
    var $column_order = array(null, 'products.id', 'products.name', 'products.stock', 'products.sku', 'categories.name', 'products.product_price', 'products.purchase_price', null); //set column field database for datatable orderable
    var $column_search = array('products.name', 'products.sku', 'categories.name'); //set column field database for datatable searchable
    var $order = array('products.name' => 'ASC'); // default order

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

   /*  private function _get_datatables_query($id = '', $w = '', $sub = '')
    {
        $this->db->select('products.*,categories.name AS c_title,seller_data.store_name as title');
        $this->db->from($this->table);
        $this->db->join('seller_data', 'seller_data.id = products.seller_id', 'left');
      //  $this->db->join('product_variants', 'product_variants.product_id = products.id');
        if ($sub) {
            $this->db->join('categories', 'categories.id = products.category_id');

            if ($this->input->post('group') != 'yes') $this->db->where('products.merge', 0);
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
                if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('geopos_warehouse.loc', 0);
            }

            $this->db->where("products.category_id =$id");

        } else {
            $this->db->join('categories', 'categories.id = products.category_id');

            if ($w) {

                if ($id > 0) {
                    $this->db->where("warehouse = $id");
                    // $this->db->where('products.sub_id', 0);
                }
                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('geopos_warehouse.loc', 0);
                }

            } else {

                if ($this->input->post('group') != 'yes') $this->db->where('products.merge', 0);
                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
                    if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('geopos_warehouse.loc', 0);
                }
                if ($id > 0) {
                    $this->db->where("categories.id = $id");
                    $this->db->where('products.sub_id', 0);
                }
            }
        }

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
			if($this->input->post('search')){
				 $search = $this->input->post('search');
            $value = $search['value'];
			}else{
				
				 $value = $this->input->get('search');;
			}
           
            if ($value) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        $search = $this->input->post('order');
        if ($search) // here order processing
        {
            $this->db->order_by($this->column_order[$search['0']['column']], $search['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($id = '', $w = '', $sub = '')
    {
        if ($id > 0) {
            $this->_get_datatables_query($id, $w, $sub);
        } else {
            $this->_get_datatables_query();
        }
        if ($this->input->post('length') != -1)
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($id, $w = '', $sub = '')
    {
        if ($id > 0) {
            $this->_get_datatables_query($id, $w, $sub);
        } else {
            $this->_get_datatables_query();
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse');
        if ($this->aauth->get_user()->loc) {

            $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
        } elseif (!BDATA) {
            $this->db->where('geopos_warehouse.loc', 0);
        }
        return $this->db->count_all_results();
    }
 */
 
 
private function _get_datatables_query($id = '', $w = '', $sub = '')
{
    $is_seller = is_seller_user();
    $this->db->select('products.*, categories.name AS c_title, seller_data.store_name as title');
    $this->db->from($this->table);
    $this->db->join('seller_data', 'seller_data.user_id = products.seller_id', 'left');
    $this->db->join('categories', 'categories.id = products.category_id');

    if ($sub) {
        $this->db->where("products.category_id = $id");
    } else {
        if ($w) {
            if ($id > 0) {
                $this->db->where("products.warehouse = $id");
            }
        } else {
            if ($id > 0) {
                $this->db->where("products.category_id = $id");
                $this->db->where('products.sub_id', 0);
            }
        }
    }

    if ($is_seller) {
        $seller_id = (int)$this->session->userdata('user_id');
        $this->db->where("products.seller_id", $seller_id);
    }

    // 🔹 Search Query
    if ($this->input->post('search')) {
        $search = $this->input->post('search');
        $value = $search['value'];
        if (!empty($value)) {
            $this->db->group_start();
            $this->db->like('products.name', $value);
            $this->db->or_like('products.sku', $value);
            $this->db->or_like('products.stock', $value);
            $this->db->group_end();
        }
    }

    // ✅ Fix: Sorting for Numeric Columns
    $search = $this->input->post('order');
    if ($search) {
        $columnIndex = (int) $search[0]['column'];  // Convert to Integer
        $sortDir = $search[0]['dir'];

        // 🔹 Define Columns for Sorting
        $sortableColumns = [
            2 => 'products.stock',
            5 => 'products.product_price',
            6 => 'products.purchase_price',
        ];

        if (isset($sortableColumns[$columnIndex])) {
            $this->db->order_by($sortableColumns[$columnIndex], $sortDir);
        } else {
           // $this->db->order_by('products.id', 'DESC');  // Default Sorting
            $this->db->order_by('products.updated_date', 'DESC');  // Default Sorting
        }
    } else {
      //  $this->db->order_by('products.id', 'DESC'); // Default Sort
        $this->db->order_by('products.updated_date', 'DESC'); // Default Sort
    }
}





public function get_datatables($id = '', $w = '', $sub = '')
{
    $this->_get_datatables_query($id, $w, $sub);
    if ($this->input->post('length') != -1)
        $this->db->limit($this->input->post('length'), $this->input->post('start'));

    $query = $this->db->get();
	//echo $this->db->last_query();
    return $query->result();
}

public function count_filtered($id = '', $w = '', $sub = '')
{
    $this->_get_datatables_query($id, $w, $sub);
    return $this->db->count_all_results();
}

public function count_all($id = '', $w = '', $sub = '')
{
    $is_seller = is_seller_user();
    $this->db->from($this->table);
    if ($sub) {
        $this->db->where("products.category_id = $id");
    } else {
        if ($w) {
            if ($id > 0) {
                $this->db->where("products.warehouse = $id");
            }
        } else {
            if ($id > 0) {
                $this->db->where("products.category_id = $id");
                $this->db->where('products.sub_id', 0);
            }
        }
    }

    if ($is_seller) {
        $seller_id = (int)$this->session->userdata('user_id');
        $this->db->where("products.seller_id", $seller_id);
    }
    return $this->db->count_all_results();
}



    public function addnew($catid, $warehouse, $name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $v_type, $v_stock, $v_alert, $wdate, $code_type, $w_type = '', $w_stock = '', $w_alert = '', $sub_cat = '', $b_id = '', $serial = '')
    {
        $ware_valid = $this->valid_warehouse($warehouse);
        if (!$sub_cat) $sub_cat = 0;
        if (!$b_id) $b_id = 0;
        $datetime1 = new DateTime(date('Y-m-d'));

        $datetime2 = new DateTime($wdate);

        $difference = $datetime1->diff($datetime2);
        if (!$difference->d > 0) {
            $wdate = null;
        }

        $result = false; // Initialize result

        if ($this->aauth->get_user()->loc) {
            if ($ware_valid['loc'] == $this->aauth->get_user()->loc OR $ware_valid['loc'] == '0' OR $warehouse == 0) {
                if (strlen($barcode) > 5 AND is_numeric($barcode)) {
                    $data = array(
                        'pcat' => $catid,
                        'warehouse' => $warehouse,
                        'name' => $name,
                        'product_code' => $product_code,
                        'product_price' => $product_price,
                        'fproduct_price' => $factoryprice,
                        'taxrate' => $taxrate,
                        'disrate' => $disrate,
                        'qty' => $product_qty,
                        'product_des' => $product_desc,
                        'alert' => $product_qty_alert,
                        'unit' => $unit,
                        'image' => $image,
                        'barcode' => $barcode,
                        'expiry' => $wdate,
                        'code_type' => $code_type,
                        'sub_id' => $sub_cat,
                        'b_id' => $b_id
                    );

                } else {

                    $barcode = rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9);

                    $data = array(
                        'pcat' => $catid,
                        'warehouse' => $warehouse,
                        'name' => $name,
                        'product_code' => $product_code,
                        'product_price' => $product_price,
                        'fproduct_price' => $factoryprice,
                        'taxrate' => $taxrate,
                        'disrate' => $disrate,
                        'qty' => $product_qty,
                        'product_des' => $product_desc,
                        'alert' => $product_qty_alert,
                        'unit' => $unit,
                        'image' => $image,
                        'barcode' => $barcode,
                        'expiry' => $wdate,
                        'code_type' => 'EAN13',
                        'sub_id' => $sub_cat,
                        'b_id' => $b_id
                    );
                }
                $this->db->trans_start();
                if ($this->db->insert('products', $data)) {
                    $pid = $this->db->insert_id();
                    $this->movers(1, $pid, $product_qty, 0, 'Stock Initialized');
                    $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    $result = true; // Set result to true on success
                } else {
                    $result = false; // Set result to false on failure
                }
                if ($serial) {
                    $serial_group = array();
                    foreach ($serial as $key => $value) {
                         if($value) $serial_group[] = array('product_id' => $pid, 'serial' => $value);
                    }
                    if (!empty($serial_group)) { // Added check for empty serial_group
                        $this->db->insert_batch('geopos_product_serials', $serial_group);
                    }
                }
                if ($v_type) {
                    foreach ($v_type as $key => $value) {
                        if ($v_type[$key] && numberClean($v_stock[$key]) > 0.00) {
                            $this->db->select('u.id,u.name,u2.name AS variation');
                            $this->db->join('geopos_units u2', 'u.rid = u2.id', 'left');
                            $this->db->where('u.id', $v_type[$key]);
                            $query = $this->db->get('geopos_units u');
                            $r_n = $query->row_array();
                            $data['name'] = $name . '-' . $r_n['variation'] . '-' . $r_n['name'];
                            $data['qty'] = numberClean($v_stock[$key]);
                            $data['alert'] = numberClean($v_alert[$key]);
                            $data['merge'] = 1;
                            $data['sub'] = $pid;
                            $data['vb'] = $v_type[$key];
                            $this->db->insert('products', $data);
                            $pidv = $this->db->insert_id();
                            $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                            $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                        }
                    }
                }
                if ($w_type) {
                    foreach ($w_type as $key => $value) {
                        if ($w_type[$key] && numberClean($w_stock[$key]) > 0.00 && $w_type[$key] != $warehouse) {
                            $data['name'] = $name;
                            $data['warehouse'] = $w_type[$key];
                            $data['qty'] = numberClean($w_stock[$key]);
                            $data['alert'] = numberClean($w_alert[$key]);
                            $data['merge'] = 2;
                            $data['sub'] = $pid;
                            $data['vb'] = $w_type[$key];
                            $this->db->insert('products', $data);
                            $pidv = $this->db->insert_id();
                            $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                            $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                        }
                    }
                }
                $this->db->trans_complete();
                return $this->db->trans_status(); // Return transaction status
            } else {
                // If warehouse location is not valid, set result to false
                $this->db->trans_complete(); // Ensure transaction is completed even on early exit
                return false;
            }
        } else {
            if (strlen($barcode) > 5 AND is_numeric($barcode)) {
                $data = array(
                    'pcat' => $catid,
                    'warehouse' => $warehouse,
                    'name' => $name,
                    'product_code' => $product_code,
                    'product_price' => $product_price,
                    'fproduct_price' => $factoryprice,
                    'taxrate' => $taxrate,
                    'disrate' => $disrate,
                    'qty' => $product_qty,
                    'product_des' => $product_desc,
                    'alert' => $product_qty_alert,
                    'unit' => $unit,
                    'image' => $image,
                    'barcode' => $barcode,
                    'expiry' => $wdate,
                    'code_type' => $code_type,
                    'sub_id' => $sub_cat,
                    'b_id' => $b_id
                );
            } else {
                $barcode = rand(100, 999) . rand(0, 9) . rand(1000000, 9999999) . rand(0, 9);
                $data = array(
                    'pcat' => $catid,
                    'warehouse' => $warehouse,
                    'name' => $name,
                    'product_code' => $product_code,
                    'product_price' => $product_price,
                    'fproduct_price' => $factoryprice,
                    'taxrate' => $taxrate,
                    'disrate' => $disrate,
                    'qty' => $product_qty,
                    'product_des' => $product_desc,
                    'alert' => $product_qty_alert,
                    'unit' => $unit,
                    'image' => $image,
                    'barcode' => $barcode,
                    'expiry' => $wdate,
                    'code_type' => 'EAN13',
                    'sub_id' => $sub_cat,
                    'b_id' => $b_id
                );
            }
            $this->db->trans_start();
            if ($this->db->insert('products', $data)) {
                $pid = $this->db->insert_id();
                $this->movers(1, $pid, $product_qty, 0, 'Stock Initialized');
                $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                $result = true; // Set result to true on success
            } else {
                $result = false; // Set result to false on failure
            }
            if ($serial) {
                $serial_group = array();
                foreach ($serial as $key => $value) {
                     if($value)  $serial_group[] = array('product_id' => $pid, 'serial' => $value);
                }
                if (!empty($serial_group)) { // Added check for empty serial_group
                    $this->db->insert_batch('geopos_product_serials', $serial_group);
                }
            }
            if ($v_type) {
                foreach ($v_type as $key => $value) {
                    if ($v_type[$key] && numberClean($v_stock[$key]) > 0.00) {
                        $this->db->select('u.id,u.name,u2.name AS variation');
                        $this->db->join('geopos_units u2', 'u.rid = u2.id', 'left');
                        $this->db->where('u.id', $v_type[$key]);

                        $query = $this->db->get('geopos_units u');
                        $r_n = $query->row_array();
                        $data['name'] = $name . '-' . $r_n['variation'] . '-' . $r_n['name'];
                        $data['qty'] = numberClean($v_stock[$key]);
                        $data['alert'] = numberClean($v_alert[$key]);
                        $data['merge'] = 1;
                        $data['sub'] = $pid;
                        $data['vb'] = $v_type[$key];
                        $this->db->insert('products', $data);
                        $pidv = $this->db->insert_id();
                        $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                        $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    }
                }
            }
            if ($w_type) {
                foreach ($w_type as $key => $value) {
                    if ($w_type[$key] && numberClean($w_stock[$key]) > 0.00 && $w_type[$key] != $warehouse) {

                        $data['name'] = $name;
                        $data['warehouse'] = $w_type[$key];
                        $data['qty'] = numberClean($w_stock[$key]);
                        $data['alert'] = numberClean($w_alert[$key]);
                        $data['merge'] = 2;
                        $data['sub'] = $pid;
                        $data['vb'] = $w_type[$key];
                        $this->db->insert('products', $data);
                        $pidv = $this->db->insert_id();
                        $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                        $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    }
                }
            }
            $this->custom->save_fields_data($pid, 4);
            $this->db->trans_complete();
            return $this->db->trans_status(); // Return transaction status
        }
    }

    public function edit($pid, $catid, $warehouse, $name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $code_type, $sub_cat = '', $b_id = '', $vari = null, $serial = null)
    {
        $this->db->select('qty');
        $this->db->from('products');
        $this->db->where('pid', $pid);
        $query = $this->db->get();
        $r_n = $query->row_array();
        $ware_valid = $this->valid_warehouse($warehouse);
        $this->db->trans_start();
        if ($this->aauth->get_user()->loc) {
            if ($ware_valid['loc'] == $this->aauth->get_user()->loc OR $ware_valid['loc'] == '0' OR $warehouse == 0) {
                $data = array(
                    'pcat' => $catid,
                    'warehouse' => $warehouse,
                    'name' => $name,
                    'product_code' => $product_code,
                    'product_price' => $product_price,
                    'fproduct_price' => $factoryprice,
                    'taxrate' => $taxrate,
                    'disrate' => $disrate,
                    'qty' => $product_qty,
                    'product_des' => $product_desc,
                    'alert' => $product_qty_alert,
                    'unit' => $unit,
                    'image' => $image,
                    'barcode' => $barcode,
                    'code_type' => $code_type,
                    'sub_id' => $sub_cat,
                    'b_id' => $b_id
                );

                $this->db->set($data);
                $this->db->where('pid', $pid);

                if ($this->db->update('products')) {
                    if ($r_n['qty'] != $product_qty) {
                        $m_product_qty = $product_qty - $r_n['qty'];
                        $this->movers(1, $pid, $m_product_qty, 0, 'Stock Changes');
                    }
                    $this->aauth->applog("[Update Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                    echo json_encode(array('status' => 'Success', 'message' =>
                        $this->lang->line('UPDATED') . " <a href='" . base_url('products/edit?id=' . $pid) . "' class='btn btn-blue btn-lg'><span class='fa fa-eye' aria-hidden='true'></span>  </a> <a href='" . base_url('products') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>"));
                } else {
                    echo json_encode(array('status' => 'Error', 'message' =>
                        $this->lang->line('ERROR')));
                }
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        } else {
            $data = array(
                'pcat' => $catid,
                'warehouse' => $warehouse,
                'name' => $name,
                'product_code' => $product_code,
                'product_price' => $product_price,
                'fproduct_price' => $factoryprice,
                'taxrate' => $taxrate,
                'disrate' => $disrate,
                'qty' => $product_qty,
                'product_des' => $product_desc,
                'alert' => $product_qty_alert,
                'unit' => $unit,
                'image' => $image,
                'barcode' => $barcode,
                'code_type' => $code_type,
                'sub_id' => $sub_cat,
                'b_id' => $b_id
            );
            $this->db->set($data);
            $this->db->where('pid', $pid);
            if ($this->db->update('products')) {
                if ($r_n['qty'] != $product_qty) {
                    $m_product_qty = $product_qty - $r_n['qty'];
                    $this->movers(1, $pid, $m_product_qty, 0, 'Stock Changes');
                }
                $this->aauth->applog("[Update Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                echo json_encode(array('status' => 'Success', 'message' =>
                    $this->lang->line('UPDATED') . " <a href='" . base_url('products/edit?id=' . $pid) . "' class='btn btn-blue btn-lg'><span class='fa fa-eye' aria-hidden='true'></span>  </a> <a href='" . base_url('products') . "' class='btn btn-grey-blue btn-lg'><span class='fa fa-list-alt' aria-hidden='true'></span>  </a>"));
            } else {
                echo json_encode(array('status' => 'Error', 'message' =>
                    $this->lang->line('ERROR')));
            }
        }

        if (isset($serial['old'])) {
            $this->db->delete('geopos_product_serials', array('product_id' => $pid,'status'=>0));
            $serial_group = array();
            foreach ($serial['old'] as $key => $value) {
                if($value) $serial_group[] = array('product_id' => $pid, 'serial' => $value);
            }
            $this->db->insert_batch('geopos_product_serials', $serial_group);
        }
                if (isset($serial['new'])) {
            $serial_group = array();
            foreach ($serial['new'] as $key => $value) {
                 if($value)  $serial_group[] = array('product_id' => $pid, 'serial' => $value,'status'=>0);
            }

            $this->db->insert_batch('geopos_product_serials', $serial_group);
        }
        $this->custom->edit_save_fields_data($pid, 4);


        $v_type = @$vari['v_type'];
        $v_stock = @$vari['v_stock'];
        $v_alert = @$vari['v_alert'];
        $w_type = @$vari['w_type'];
        $w_stock = @$vari['w_stock'];
        $w_alert = @$vari['w_alert'];

        if (isset($v_type)) {
            foreach ($v_type as $key => $value) {
                if ($v_type[$key] && numberClean($v_stock[$key]) > 0.00) {
                    $this->db->select('u.id,u.name,u2.name AS variation');
                    $this->db->join('geopos_units u2', 'u.rid = u2.id', 'left');
                    $this->db->where('u.id', $v_type[$key]);
                    $query = $this->db->get('geopos_units u');
                    $r_n = $query->row_array();
                    $data['name'] = $name . '-' . $r_n['variation'] . '-' . $r_n['name'];
                    $data['qty'] = numberClean($v_stock[$key]);
                    $data['alert'] = numberClean($v_alert[$key]);
                    $data['merge'] = 1;
                    $data['sub'] = $pid;
                    $data['vb'] = $v_type[$key];
                    $this->db->insert('products', $data);
                    $pidv = $this->db->insert_id();
                    $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                    $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                }
            }
        }
        if (isset($w_type)) {
            foreach ($w_type as $key => $value) {
                if ($w_type[$key] && numberClean($w_stock[$key]) > 0.00 && $w_type[$key] != $warehouse) {
                    $data['name'] = $name;
                    $data['warehouse'] = $w_type[$key];
                    $data['qty'] = numberClean($w_stock[$key]);
                    $data['alert'] = numberClean($w_alert[$key]);
                    $data['merge'] = 2;
                    $data['sub'] = $pid;
                    $data['vb'] = $w_type[$key];
                    $this->db->insert('products', $data);
                    $pidv = $this->db->insert_id();
                    $this->movers(1, $pidv, $data['qty'], 0, 'Stock Initialized');
                    $this->aauth->applog("[New Product] -$name  -Qty-$product_qty ID " . $pid, $this->aauth->get_user()->username);
                }
            }
        }
        $this->db->trans_complete();

    }

    public function prd_stats()
    {

        $whr = '';
        if ($this->aauth->get_user()->loc) {
            $whr = ' LEFT JOIN  geopos_warehouse on geopos_warehouse.id = products.warehouse WHERE geopos_warehouse.loc=' . $this->aauth->get_user()->loc;
            if (BDATA) $whr = ' LEFT JOIN  geopos_warehouse on geopos_warehouse.id = products.warehouse WHERE geopos_warehouse.loc=0 OR geopos_warehouse.loc=' . $this->aauth->get_user()->loc;
        } elseif (!BDATA) {
            $whr = ' LEFT JOIN  geopos_warehouse on geopos_warehouse.id = products.warehouse WHERE geopos_warehouse.loc=0';
        }
        $query = $this->db->query("SELECT
COUNT(IF( products.qty > 0, products.qty, NULL)) AS instock,
COUNT(IF( products.qty <= 0, products.qty, NULL)) AS outofstock,
COUNT(products.qty) AS total
FROM products $whr");
        echo json_encode($query->result_array());
    }

    public function products_list($id, $term = '')
    {
        $this->db->select('products.*');
        $this->db->from('products');
      //  $this->db->where('products.warehouse', $id);
        if ($this->aauth->get_user()->loc) {
            $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse');
            $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
        } elseif (!BDATA) {
            $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse');
            $this->db->where('geopos_warehouse.loc', 0);
        }
        if ($term) {
            $this->db->where("products.name LIKE '%$term%'");
            $this->db->or_where("products.article LIKE '$term%'");
        }
        $query = $this->db->get();
		
		//echo $this->db->last_query();
        return $query->result_array();

    }


    public function units()
    {
        $this->db->select('*');
        $this->db->from('geopos_units');
        $this->db->where('type', 0);
        $query = $this->db->get();
        return $query->result_array();

    }

    public function serials($pid)
    {
        $this->db->select('*');
        $this->db->from('geopos_product_serials');
        $this->db->where('product_id', $pid);

        $query = $this->db->get();
        return $query->result_array();


    }

    public function transfer($from_warehouse, $products_l, $to_warehouse, $qty)
    {
        $updateArray = array();
        $move = false;
        $qtyArray = explode(',', $qty);
        $this->db->select('title');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $to_warehouse);
        $query = $this->db->get();
        $to_warehouse_name = $query->row_array()['title'];

        $i = 0;
        foreach ($products_l as $row) {
            $qty = 0;
            if (array_key_exists($i, $qtyArray)) $qty = $qtyArray[$i];

            $this->db->select('*');
            $this->db->from('products');
            $this->db->where('pid', $row);
            $query = $this->db->get();
            $pr = $query->row_array();
            $pr2 = $pr;
            $c_qty = $pr['qty'];
            if ($c_qty - $qty < 0) {

            } elseif ($c_qty - $qty == 0) {


                if ($pr['merge'] == 2) {

                    $this->db->select('pid,name');
                    $this->db->from('products');
                    $this->db->where('pid', $pr['sub']);
                    $this->db->where('warehouse', $to_warehouse);
                    $query = $this->db->get();
                    $pr = $query->row_array();

                } else {
                    $this->db->select('pid,name');
                    $this->db->from('products');
                    $this->db->where('merge', 2);
                    $this->db->where('sub', $row);
                    $this->db->where('warehouse', $to_warehouse);
                    $query = $this->db->get();
                    $pr = $query->row_array();
                }


                $c_pid = $pr['pid'];
                $name = $pr['name'];

                if ($c_pid) {

                    $this->db->set('qty', "qty+$qty", FALSE);
                    $this->db->where('pid', $c_pid);
                    $this->db->update('products');
                    $this->aauth->applog("[Product Transfer] -$name  -Qty-$qty ID " . $c_pid, $this->aauth->get_user()->username);
                    $this->db->delete('products', array('pid' => $row));
                    $this->db->delete('geopos_movers', array('d_type' => 1, 'rid1' => $row));

                } else {
                    $updateArray[] = array(
                        'pid' => $row,
                        'warehouse' => $to_warehouse
                    );
                    $move = true;
                    $name = $pr2['name'];
                    $this->db->delete('geopos_movers', array('d_type' => 1, 'rid1' => $row));

                    $this->movers(1, $row, $qty, 0, 'Stock Transferred & Initialized W- ' . $to_warehouse_name);
                    $this->aauth->applog("[Product Transfer] -$name  -Qty-$qty W- $to_warehouse_name PID " . $pr2['pid'], $this->aauth->get_user()->username);
                }


            } else {
                $data['name'] = $pr['name'];
                $data['pcat'] = $pr['pcat'];
                $data['warehouse'] = $to_warehouse;
                $data['name'] = $pr['name'];
                $data['product_code'] = $pr['product_code'];
                $data['product_price'] = $pr['product_price'];
                $data['fproduct_price'] = $pr['fproduct_price'];
                $data['taxrate'] = $pr['taxrate'];
                $data['disrate'] = $pr['disrate'];
                $data['qty'] = $qty;
                $data['product_des'] = $pr['product_des'];
                $data['alert'] = $pr['alert'];
                $data['	unit'] = $pr['unit'];
                $data['image'] = $pr['image'];
                $data['barcode'] = $pr['barcode'];
                $data['merge'] = 2;
                $data['sub'] = $row;
                $data['vb'] = $to_warehouse;
                if ($pr['merge'] == 2) {
                    $this->db->select('pid,name');
                    $this->db->from('products');
                    $this->db->where('pid', $pr['sub']);
                    $this->db->where('warehouse', $to_warehouse);
                    $query = $this->db->get();
                    $pr = $query->row_array();
                } else {
                    $this->db->select('pid,name');
                    $this->db->from('products');
                    $this->db->where('merge', 2);
                    $this->db->where('sub', $row);
                    $this->db->where('warehouse', $to_warehouse);
                    $query = $this->db->get();
                    $pr = $query->row_array();
                }


                $c_pid = $pr['pid'];
                $name = $pr2['name'];

                if ($c_pid) {

                    $this->db->set('qty', "qty+$qty", FALSE);
                    $this->db->where('pid', $c_pid);
                    $this->db->update('products');

                    $this->movers(1, $c_pid, $qty, 0, 'Stock Transferred W ' . $to_warehouse_name);
                    $this->aauth->applog("[Product Transfer] -$name  -Qty-$qty W $to_warehouse_name  ID " . $c_pid, $this->aauth->get_user()->username);


                } else {
                    $this->db->insert('products', $data);
                    $pid = $this->db->insert_id();
                    $this->movers(1, $pid, $qty, 0, 'Stock Transferred & Initialized W ' . $to_warehouse_name);
                    $this->aauth->applog("[Product Transfer] -$name  -Qty-$qty  W $to_warehouse_name ID " . $pr2['pid'], $this->aauth->get_user()->username);

                }

                $this->db->set('qty', "qty-$qty", FALSE);
                $this->db->where('pid', $row);
                $this->db->update('products');
                $this->movers(1, $row, -$qty, 0, 'Stock Transferred WID ' . $to_warehouse_name);
            }


            $i++;
        }

        if ($move) {
            $this->db->update_batch('products', $updateArray, 'pid');
        }

        echo json_encode(array('status' => 'Success', 'message' =>
            $this->lang->line('UPDATED')));


    }

    public function meta_delete($name)
    {
        if (@unlink(FCPATH . 'userfiles/product/' . $name)) {
            return true;
        }
    }

    public function valid_warehouse($warehouse)
    {
        $this->db->select('id,loc');
        $this->db->from('geopos_warehouse');
        $this->db->where('id', $warehouse);
        $query = $this->db->get();
        $row = $query->row_array();
        return $row;
    }


    public function movers($type = 0, $rid1 = 0, $rid2 = 0, $rid3 = 0, $note = '')
    {
        $data = array(
            'd_type' => $type,
            'rid1' => $rid1,
            'rid2' => $rid2,
            'rid3' => $rid3,
            'note' => $note
        );
        $this->db->insert('geopos_movers', $data);
    }
	
	
	
	public function get_product_details($flag = NULL, $seller_id = NULL, $p_status = NULL)
    {
        $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;
        if (is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
        }
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            if ($_GET['sort'] == 'id') {
                $sort = "product_variants.id";
            } else {
                $sort = $_GET['sort'];
            }
        if (isset($_GET['order']))
            $order = $_GET['order'];

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = trim($_GET['search']);
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'p.`description`' => $search, 'p.`short_description`' => $search, 'c.name' => $search];
        }

        if (isset($_GET['category_id']) || isset($_GET['search'])) {
            if (isset($_GET['search']) and $_GET['search'] != '') {
                $multipleWhere['p.`category_id`'] = $search;
            }

            if (isset($_GET['category_id']) and $_GET['category_id'] != '') {
                $category_id = $_GET['category_id'];
            }
        }

        $count_res = $this->db->select(' COUNT( distinct(p.id)) as `total` ')->join(" categories c", "p.category_id=c.id ")->join('product_variants', 'product_variants.product_id = p.id');

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $count_res->group_Start();
            $count_res->or_like($multipleWhere);
            $count_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $count_res->where($where);
        }
        if ($flag == 'low') {
            $count_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $count_res->where($where);
            $count_res->where('p.stock <=', $low_stock_limit);
            $count_res->where('p.availability  =', '1');
            $count_res->or_where('product_variants.stock <=', $low_stock_limit);
            $count_res->where('product_variants.availability  =', '1');
            $count_res->group_End();
        }

        if (isset($seller_id) && $seller_id != "") {
            $count_res->where("p.seller_id", $seller_id);
        }
        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", $p_status);
        }

        if ($flag == 'sold') {
            $count_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $count_res->where($where);
            $count_res->where('p.stock ', '0');
            $count_res->where('p.availability ', '0');
            $count_res->or_where('product_variants.stock ', '0');
            $count_res->where('product_variants.availability ', '0');
            $count_res->group_End();
        }

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();

        foreach ($product_count as $row) {
            $total = $row['total'];
        }
        $search_res = $this->db->select('product_variants.id AS id,c.name as category_name,sd.store_name, p.id as pid,p.rating,p.no_of_ratings,p.name, p.type, p.image, p.status,product_variants.price , product_variants.special_price, product_variants.stock')
            ->join("categories c", "p.category_id=c.id")
            ->join("seller_data sd", "sd.user_id=p.seller_id ")
            ->join('product_variants', 'product_variants.product_id = p.id');
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_res->group_Start();
            $search_res->or_like($multipleWhere);
            $search_res->group_End();
        }

        if (isset($where) && !empty($where)) {
            $search_res->where($where);
        }

        if ($flag != null && $flag == 'low') {

            $search_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $search_res->where($where);
            $search_res->where('p.stock <=', $low_stock_limit);
            $search_res->where('p.availability  =', '1');
            $search_res->or_where('product_variants.stock <=', $low_stock_limit);
            $search_res->where('product_variants.availability  =', '1');
            $search_res->group_End();
        }
        if ($flag != null && $flag == 'sold') {
            $search_res->group_Start();
            $where = "p.stock_type is  NOT NULL";
            $search_res->where($where);
            $search_res->where('p.stock ', '0');
            $search_res->where('p.availability ', '0');
            $search_res->or_where('product_variants.stock ', '0');
            $search_res->where('product_variants.availability ', '0');

            $search_res->group_End();
        }

        if (isset($category_id) && !empty($category_id)) {
            //category select where
            $search_res->group_Start();
            $search_res->or_where('p.category_id', $category_id);
            $search_res->or_where('c.parent_id', $category_id);
            $search_res->group_End();
        }
        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("p.seller_id", $seller_id);
        }

        if (isset($p_status) && $p_status != "") {
            $search_res->where("p.status", $p_status);
        }
        $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        $currency = get_settings('currency');
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($pro_search_res as $row) {
            $row = output_escaping($row);
           // $operate = "<a href='view-product?edit_id=" . $row['pid'] . "'  class='btn btn-primary btn-xs mr-1 mb-1' title='View'><i class='fa fa-eye'></i></a>";
            $operate = " <a href='" . base_url('products/edit/' . $row['pid']) . "' data-id=" . $row['pid'] . " class='btn btn-success btn-xs mr-1 mb-1' title='Edit' ><i class='fa fa-pen'></i></a>";
             $rate_product="<a href='product_rate_change?edit_id=" . $row['pid'] . "' data-id=" . $row['pid'] . " class='btn btn-success btn-xs mr-1 mb-1' title='Edit' >Rate Change</a>";
            if ($row['status'] == '2') {
                $tempRow['status'] = '<a class="badge badge-danger text-white" >Not-Approved</a>';
                if (is_seller_user()) {
                    $operate .= '<a class="btn btn-secondary mr-1 mb-1 btn-xs" data-table="products" href="javascript:void(0)" title="Not-Approved" ><i class="fa fa-ban"></i></a>';
                } else {
                    $operate .= '<a class="btn btn-secondary mr-1 mb-1 btn-xs update_active_status" data-table="products" href="javascript:void(0)" title="Approve" data-id="' . $row['pid'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-ban"></i></a>';
                }
            }
            if ($row['status'] == '1') {
                $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
                $operate .= '<a class="btn btn-warning btn-xs update_active_status mr-1 mb-1" data-table="products" title="Deactivate" href="javascript:void(0)" data-id="' . $row['pid'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-toggle-on"></i></a>';
            } else  if ($row['status'] == '0') {

                $tempRow['status'] = '<a class="badge badge-danger text-white" >Inactive</a>';
                $operate .= '<a class="btn btn-secondary mr-1 mb-1 btn-xs update_active_status" data-table="products" href="javascript:void(0)" title="Active" data-id="' . $row['pid'] . '" data-status="' . $row['status'] . '" ><i class="fa fa-toggle-off"></i></a>';
            }
            $operate .= ' <a href="javascript:void(0)" id="delete-product" data-id=' . $row['pid'] . ' class="btn btn-danger mr-1 mb-1 btn-xs"><i class="fa fa-trash"></i></a>';
            $operate .= " <a href='javascript:void(0)' data-id=" . $row['pid'] . " data-toggle='modal' data-target='#product-rating-modal' class='btn btn-success btn-xs mr-1 mb-1' title='View Ratings' ><i class='fa fa-star'></i></a>";
            $operate .= " <a href='javascript:void(0)' data-id=" . $row['pid'] . " data-toggle='modal' data-target='#product-faqs-modal' class='btn btn-info btn-xs mr-1 mb-1' title='View FAQs' ><i class='fas fa-question-circle'></i></a>";

            $attr_values  =  get_variants_values_by_pid($row['pid']);
            $tempRow['id'] = $row['pid'];
            $tempRow['varaint_id'] = $row['id'];
            $tempRow['name'] = $row['name'] . '<br><small>' . ucwords(str_replace('_', ' ', $row['type'])) . '</small><br><small> By </small><b>' . $row['store_name'] . '</b>';
            $tempRow['type'] = $row['type'];
            $tempRow['category_name'] = $row['category_name'];
            // $tempRow['price'] =  ($row['special_price'] == null || $row['special_price'] == '0') ? $currency . $row['price'] : $currency . $row['special_price'];
            $tempRow['price'] = $row['price'];
            $tempRow['special_price'] = $row['special_price'];
            $tempRow['stock'] = $row['stock'];
            $variations = '';
            foreach ($attr_values as $variants) {
                if (isset($attr_values[0]['attr_name'])) {

                    if (!empty($variations)) {
                        $variations .= '---------------------<br>';
                    }

                    $attr_name = explode(',', $variants['attr_name']);
                    $varaint_values = explode(',', $variants['variant_values']);
                    for ($i = 0; $i < count($attr_name); $i++) {
                        $variations .= '<b>' . $attr_name[$i] . '</b> : ' . $varaint_values[$i] . '<br>';
                    }
                }
            }

            $tempRow['variations'] = (!empty($variations)) ? $variations : '-';
            $row['image'] = get_image_url($row['image'], 'thumb', 'sm');
            $tempRow['image'] = '<div class="mx-auto product-image"><a href=' . $row['image'] . ' data-toggle="lightbox" data-gallery="gallery">
        <img src=' . $row['image'] . ' class="img-fluid rounded"></a></div>';

            $tempRow['rating'] = '<input type="text" class="kv-fa rating-loading" value="' . $row['rating'] . '" data-size="xs" title="" readonly> <span> (' . $row['rating'] . '/' . $row['no_of_ratings'] . ') </span>';

            $tempRow['operate'] = $operate;
             $tempRow['rate_product'] = $rate_product;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        print_r(json_encode($bulkData));
    }



    // Fetch product details from the `products` table
    public function get_product($product_id)
    {
        $this->db->where('id', $product_id);
        return $this->db->get('products')->row_array();
    }

    // Get the first variant with status 1
    public function get_first_variant($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        return $this->db->get('product_variants')->row_array();
    }

    // Get ALL active variants with status 1
    public function get_all_active_variants($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'ASC');
        return $this->db->get('product_variants')->result_array();
    }

    // Extract unit from `attribute_values`
    public function get_unit_from_attribute($attribute_value_ids)
    {
        if (empty($attribute_value_ids)) {
            return '';
        }

        $ids = explode(',', $attribute_value_ids);
        $this->db->select('value');
        $this->db->where_in('id', $ids);
        $query = $this->db->get('attribute_values');
        $attribute_values = $query->result_array();

        if (!empty($attribute_values)) {
            foreach ($attribute_values as $attribute) {
                // Remove numbers and extract only the unit
                $unit = preg_replace('/\d/', '', $attribute['value']);
                $unit = trim($unit); // Remove extra spaces
                if (!empty($unit)) {
                    return $unit; // Return the first valid unit
                }
            }
        }

        return '';
    }

    // Update products table
    public function update_product($product_id, $data)
    {
        $this->db->where('id', $product_id);
        $result = $this->db->update('products', $data);
        // Log for debugging
        if (isset($data['stock'])) {
            error_log("Products_model::update_product - Product ID: $product_id, Stock being set: " . $data['stock'] . ", Update result: " . ($result ? 'success' : 'failed'));
        }
        return $result;
    }

    // Update product_variants table
    public function update_variant($variant_id, $data)
    {
        $this->db->where('id', $variant_id);
        $this->db->update('product_variants', $data);
    }

    // Insert into product_ledger table
    public function insert_ledger($data)
    {
        $this->db->insert('product_ledger', $data);
    }




public function get_sell_report($product_id, $from_date = '', $to_date = '')
{
    $this->db->select('*');
    $this->db->from('product_ledger pl');
   // $this->db->join('orders o', 'o.id = pl.order_id', 'left');
    $this->db->where('pl.product_id', $product_id);
    $this->db->where_in('pl.ledger_type', ['Sell', 'purchage']);
   // $this->db->where('o.is_deleted', 0);

    // Default to current month if no date is provided
    if (empty($from_date) && empty($to_date)) {
        $from_date = date('Y-m-01'); // first day of current month
        $to_date = date('Y-m-t');    // last day of current month
    }

    if (!empty($from_date)) {
        $this->db->where('DATE(pl.created_date) >=', $from_date);
    }
    if (!empty($to_date)) {
        $this->db->where('DATE(pl.created_date) <=', $to_date);
    }

    $this->db->order_by('pl.created_date', 'ASC');
    $query = $this->db->get();

    return $query->result();
}

/* public function get_opening_balance($product_id, $from_date)
{
    $this->db->select('close_stock');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $product_id);
    $this->db->where('DATE(created_date) <', $from_date);
    $this->db->order_by('created_date', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return (float)$query->row()->close_stock;
    } else {
        return 0;
    }
}
 */

public function get_combined_ledger($product_id, $from_date = '', $to_date = '')
{
    $this->load->database();

    $union_query = "
        (
            SELECT 
                o.id AS order_id,
                oi.product_id,
                DATE(o.date_added) AS created_date,
                'Sell' AS ledger_type,
                oi.quantity AS qty,
                0 AS purchage_qty
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE oi.product_id = ? 
                AND o.is_deleted = 0
    ";

    if (!empty($from_date)) {
        $union_query .= " AND DATE(o.date_added) >= '{$from_date}'";
    }
    if (!empty($to_date)) {
        $union_query .= " AND DATE(o.date_added) <= '{$to_date}'";
    }

    $union_query .= ")
        
        UNION ALL
        
        (
            SELECT 
                gp.id AS order_id,
                gpi.pid,
                DATE(gp.created_at) AS created_date,
                'Purchase' AS ledger_type,
                0 AS qty,
                gpi.qty AS purchage_qty
            FROM geopos_purchase_items gpi
            JOIN geopos_purchase gp ON gp.id = gpi.tid
            WHERE gpi.pid = ?
    ";

    if (!empty($from_date)) {
        $union_query .= " AND DATE(gp.created_at) >= '{$from_date}'";
    }
    if (!empty($to_date)) {
        $union_query .= " AND DATE(gp.created_at) <= '{$to_date}'";
    }

    $union_query .= ") ORDER BY created_date ASC";

    $query = $this->db->query($union_query, [$product_id, $product_id]);
    return $query->result();
}



public function get_opening_balance($product_id, $from_date)
{
    $sell_query = "
        SELECT SUM(oi.quantity) as total_sell
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE oi.product_id = ?
            AND DATE(o.date_added) < ?
            AND o.is_deleted = 0
    ";
    $purchase_query = "
        SELECT SUM(gpi.qty) as total_purchase
        FROM geopos_purchase_items gpi
        JOIN geopos_purchase gp ON gp.id = gpi.tid
        WHERE gpi.pid = ?
            AND DATE(gp.created_at) < ?
    ";

    $sell_result = $this->db->query($sell_query, array($product_id, $from_date))->row();
    $purchase_result = $this->db->query($purchase_query, array($product_id, $from_date))->row();

    $sell = (isset($sell_result->total_sell) && $sell_result->total_sell !== null) ? $sell_result->total_sell : 0;
    $purchase = (isset($purchase_result->total_purchase) && $purchase_result->total_purchase !== null) ? $purchase_result->total_purchase : 0;

    return (float)($purchase - $sell);
}





public function get_total_livebalance($product_id, $from_date = null, $to_date = null)
{
    if (empty($from_date) || empty($to_date)) {
        $from_date = date('Y-m-01'); // 1st of current month
        $to_date = date('Y-m-d');    // today's date
    }

    // === STEP 1: Get filtered entries from product_ledger
    $this->db->select('*');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $product_id);
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
        $prod = $this->db->select('type, stock, purchase_price')->from('products')->where('id', $product_id)->get()->row();
        if ($prod) {
            if ($prod->type === 'variable_product') {
                $variant_stock_sum = $this->db->select('SUM(stock) as total_stock')->from('product_variants')->where(['product_id' => $product_id, 'status' => 1])->get()->row();
                $final_closing_balance = $variant_stock_sum ? (float)$variant_stock_sum->total_stock : 0.00;
            } else {
                $final_closing_balance = (float)$prod->stock;
            }
            $avg_rate = (float)$prod->purchase_price;
        } else {
            $final_closing_balance = 0.00;
            $avg_rate = 0.00;
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
        $avg_rate = ($total_purchage_qty > 0) ? $total_purchage_amount / $total_purchage_qty : 0;
    }

    $stock_value = $final_closing_balance * $avg_rate;

    // === STEP 2: Calculate Return Stock (only accepted)
    $this->db->select('SUM(gsi.qty) AS total_return_qty, SUM(gsi.qty * gsi.price) AS total_return_value', FALSE);
    $this->db->from('geopos_stock_r_items gsi');
    $this->db->join('geopos_stock_r gs', 'gs.id = gsi.tid', 'left');
    $this->db->where('gs.status', 'accepted');
    $this->db->where('gsi.pid', $product_id);
    $this->db->where("DATE(gs.invoicedate) BETWEEN '$from_date' AND '$to_date'");
    $returnStock = $this->db->get()->row();

    $total_return_qty = (isset($returnStock->total_return_qty) && $returnStock->total_return_qty !== null) ? $returnStock->total_return_qty : 0;
    $total_return_value = (isset($returnStock->total_return_value) && $returnStock->total_return_value !== null) ? $returnStock->total_return_value : 0;

    // === STEP 3: Adjust closing balance if return stock should be added back
    // (If returns mean product comes back into stock, add the quantity)
    $final_closing_balance += $total_return_qty;
    $stock_value = $final_closing_balance * $avg_rate;

    // === STEP 4: Return combined data
    return [
        'previous_balance'      => $prev_balance,
        'period_balance'        => $current_balance,
        'total_balance'         => $final_closing_balance,
        'avg_rate'              => $avg_rate,
        'stock_value'           => $stock_value,
        'total_purchage_qty'    => $total_purchage_qty,
        'total_sell_qty'        => $total_sell_qty,
        'total_purchage_amount' => $total_purchage_amount,
        'total_sell_amount'     => $total_sell_amount,
        'total_wastage_qty'     => $total_wastage_qty,
        'total_return_qty'      => $total_return_qty,
        'total_return_value'    => $total_return_value
    ];
}




public function get_total_balance($product_id, $from_date = null, $to_date = null)
{
    if (empty($from_date) || empty($to_date)) {
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
    }

    // === STEP 1: Get all entries ordered
    $this->db->select('*');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $product_id);
    $this->db->order_by('created_date', 'ASC');
    $entries = $this->db->get()->result();

    $prev_balance = 0;
    $current_balance = 0;
    $total_purchage_qty = 0;
    $total_wastage_qty = 0;
    $total_sell_qty = 0;
    $total_purchage_amount = 0;
    $total_sell_amount = 0;

    $is_reset_found = false;

    foreach ($entries as $entry) {
        $date = $entry->created_date;

       /*  if ($entry->ledger_type == 'Updated by Admin') {
            // If it's before from_date, consider it reset point
            if ($date < $from_date . ' 00:00:00') {
                $prev_balance = $entry->close_stock;
                $is_reset_found = true;
                continue;
            }
        
        } */
/* 
        if ($date < $from_date . ' 00:00:00') {
           // $prev_balance += ($entry->purchage_qty - $entry->sell_qty);
        } elseif ($date >= $from_date . ' 00:00:00' && $date <= $to_date . ' 23:59:59') { */
            $total_purchage_qty += $entry->purchage_qty;
            $total_sell_qty += $entry->sell_qty;
            $total_wastage_qty += $entry->wastage;
            $total_purchage_amount += $entry->purchage_amount;
            $total_sell_amount += $entry->sell_amount;
            $current_balance += ($entry->purchage_qty - $entry->sell_qty);
       // }
    }

    $final_closing_balance = $current_balance - $total_wastage_qty;

    $avg_rate = ($total_purchage_qty > 0) ? $total_purchage_amount / $total_purchage_qty : 0;
    $stock_value = $final_closing_balance * $avg_rate;

    return [
        'previous_balance' => $prev_balance,
        'period_balance' => $current_balance,
        'total_balance' => $final_closing_balance,
        'avg_rate' => $avg_rate,
        'stock_value' => $stock_value,
        'total_purchage_qty' => $total_purchage_qty,
        'total_sell_qty' => $total_sell_qty,
        'total_purchage_amount' => $total_purchage_amount,
        'total_sell_amount' => $total_sell_amount,
    ];
}



}
