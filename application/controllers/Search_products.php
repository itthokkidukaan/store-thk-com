<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Search_products extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        $this->load->model('search_model');
        if (!is_cli()) {
            if (!$this->aauth->is_loggedin()) {
                redirect('/user/', 'refresh');
            }
            if (!$this->aauth->premission(1)) {
                exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
            }
        }
    }

//search product in invoice
    public function search()
    {
		
		$flag ='';
        $wid = (int)$this->input->post('wid', true);
        $current_seller_id = 0;
        if (function_exists('is_seller_user') && is_seller_user()) {
            $current_seller_id = (int)$this->session->userdata('user_id');
        }
     
		
		
		   $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
     
        $search = $this->input->post('name_startsWith', true);
        if ( $search != '') {
            $search = trim($this->input->post('name_startsWith', true));
            $multipleWhere = [
                'p.`id`' => $search, 
                'p.`name`' => $search, 
                'p.`sku`' => $search,
                'p.`barcode`' => $search,
                'product_variants.`sku`' => $search,
                'p.`description`' => $search, 
                'p.`short_description`' => $search, 
                'c.name' => $search
            ];
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
        if ($current_seller_id > 0) {
            $count_res->where("p.seller_id", $current_seller_id);
        }
        if ($wid > 0) {
            $count_res->where("p.warehouse", $wid);
        }
        if (isset($p_status) && $p_status != "") {
            $count_res->where("p.status", $p_status);
        }

   

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();
        $search_res = $this->db->select('product_variants.id AS id,  c.name as category_name,sd.store_name, p.id as pid,  p.rating,p.no_of_ratings ,p.name, p.article, p.type,p.product_price as sellprice,  p.image, p.status, p.purchase_price , product_variants.price , product_variants.special_price, product_variants.stock, tax.percentage as tax_percentage')
            ->join("categories c", "p.category_id=c.id")
            ->join("seller_data sd", "sd.user_id=p.seller_id ", 'left')
            ->join('product_variants', 'product_variants.product_id = p.id')
            ->join('taxes tax', 'tax.id = p.tax', 'left');
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
           
            $search_res->group_Start();
            $search_res->or_where('p.category_id', $category_id);
            $search_res->or_where('c.parent_id', $category_id);
            $search_res->group_End();
        }
        if (isset($seller_id) && $seller_id != "") {
            $search_res->where("p.seller_id", $seller_id);
        }
        if ($current_seller_id > 0) {
            $search_res->where("p.seller_id", $current_seller_id);
        }
        if ($wid > 0) {
            $search_res->where("p.warehouse", $wid);
        }

        if (isset($p_status) && $p_status != "") {
            $search_res->where("p.status", $p_status);
        }
        $out = array();
        
        $search_term = trim($this->input->post('name_startsWith', true));
        if ($search_term != '' && strlen($search_term) >= 6) {
            // 1. Search in product_barcode_info for exact or prefix match on article_no, hpnumber, or print_name using raw SQL
            $sql1 = "SELECT pb.*, p.name as product_name, p.purchase_price, p.type, tax.percentage as tax_percentage, pv.id as variant_id, pv.price as variant_price, pv.special_price, pv.stock as variant_stock, pv.margin_percent, pv.margin_type, pv.disc_percent, pv.purchase_price as variant_purchase_price, pv.packing_price as variant_packing_price
                     FROM product_barcode_info pb
                     JOIN products p ON pb.product_id = p.id
                     LEFT JOIN taxes tax ON tax.id = p.tax
                     LEFT JOIN product_variants pv ON pv.product_id = pb.product_id AND EXISTS(SELECT 1 FROM attribute_values av WHERE av.id = pv.attribute_value_ids AND av.value = pb.UOM)
                     WHERE (pb.article_no = ? OR pb.hpnumber = ? OR pb.print_name = ? OR pb.print_name LIKE ?)";
            $sql1_params = array($search_term, $search_term, $search_term, $search_term . '%');
            if ($current_seller_id > 0) {
                $sql1 .= " AND p.seller_id = ?";
                $sql1_params[] = $current_seller_id;
            }
            if ($wid > 0) {
                $sql1 .= " AND p.warehouse = ?";
                $sql1_params[] = $wid;
            }
            $direct_matches = $this->db->query($sql1, $sql1_params)->result_array();

            if (empty($direct_matches)) {
                // 2. Search in product_variants for exact or prefix match on sku, joining barcode info using raw SQL
                $sql2 = "SELECT pv.id as variant_id, pv.price as variant_price, pv.special_price, pv.stock as variant_stock, pv.margin_percent, pv.margin_type, pv.disc_percent, pv.purchase_price as variant_purchase_price, pv.packing_price as variant_packing_price, pv.sku as variant_sku, p.id as product_id, p.name as product_name, p.purchase_price, p.type, tax.percentage as tax_percentage, av.value as UOM, pb.print_name, pb.article_no, pb.hpnumber
                         FROM product_variants pv
                         JOIN products p ON pv.product_id = p.id
                         LEFT JOIN taxes tax ON tax.id = p.tax
                         LEFT JOIN attribute_values av ON av.id = pv.attribute_value_ids
                         LEFT JOIN product_barcode_info pb ON pb.product_id = pv.product_id AND pb.UOM = av.value
                         WHERE (pv.sku = ? OR pv.sku LIKE ?)";
                $sql2_params = array($search_term, $search_term . '%');
                if ($current_seller_id > 0) {
                    $sql2 .= " AND p.seller_id = ?";
                    $sql2_params[] = $current_seller_id;
                }
                if ($wid > 0) {
                    $sql2 .= " AND p.warehouse = ?";
                    $sql2_params[] = $wid;
                }
                $direct_matches = $this->db->query($sql2, $sql2_params)->result_array();
            }

            if (!empty($direct_matches)) {
                foreach ($direct_matches as $dm) {
                    $uom = !empty($dm['UOM']) ? $dm['UOM'] : 'pc';
                    if (isset($dm['variant_purchase_price']) && $dm['variant_purchase_price'] !== null) {
                        // Per-variant purchase price already represents the cost for this UOM/pack size
                        $puprice = (float)$dm['variant_purchase_price'];
                    } else {
                        $con = convert_to_base_unit($uom);
                        $puprice = $con['qty'] * $dm['purchase_price'];
                    }
                    $pacprice = isset($dm['variant_packing_price']) ? (float)$dm['variant_packing_price'] : 0;
                    $margin = isset($dm['margin_percent']) ? $dm['margin_percent'] : 0;
                    $margintype = isset($dm['margin_type']) ? $dm['margin_type'] : '';

                    if ($margintype == 'Fixed') {
                        $price_with_margin = $puprice + $margin;
                    } elseif ($margintype == 'Percentage') {
                        $price_with_margin = $puprice + ($puprice * $margin / 100);
                    } else {
                        $price_with_margin = $puprice;
                    }
                    $papupri = $puprice + $pacprice;
                    $final_product_price = $price_with_margin + $pacprice;
                    $disc_percent = isset($dm['disc_percent']) ? (float)$dm['disc_percent'] : 0;
                    $dynamic_special_price = ($price_with_margin - ($price_with_margin * $disc_percent / 100)) + $pacprice;

                    $sell_rate = ($dynamic_special_price > 0 && $dynamic_special_price < $final_product_price) ? $dynamic_special_price : $final_product_price;

                    $printname = !empty($dm['print_name']) ? $dm['print_name'] : $dm['product_name'];
                    $article = !empty($dm['article_no']) ? $dm['article_no'] : (!empty($dm['variant_sku']) ? $dm['variant_sku'] : '');
                    $hpnumber = !empty($dm['hpnumber']) ? $dm['hpnumber'] : '';
                    $variant_id = !empty($dm['variant_id']) ? $dm['variant_id'] : 0;
                    $stock = isset($dm['variant_stock']) ? $dm['variant_stock'] : 0;

                    $unit_opt = '<option value="'.$uom.'" varId="'.$variant_id.'" propur="'.$papupri.'" purchase="'.number_format($sell_rate, 2).'" margin="'.$margin.'" disc="'.(isset($dm['disc_percent']) ? $dm['disc_percent'] : 0).'" stock="'.$stock.'" printname="'.$printname.'" productarticle="'.$article.'" hpnumber="'.$hpnumber.'" selected>'.$uom.'</option>';
                    $tax_percentage = isset($dm['tax_percentage']) ? $dm['tax_percentage'] : 0;

                    $out[] = array(
                        $printname . ' (' . $uom . ') [Variant]',
                        number_format($sell_rate, 2),
                        $dm['product_id'],
                        $tax_percentage,
                        '',
                        $stock, 
                        $unit_opt, 
                        '', 
                        $dm['type'],
                        'direct_variant',
                        $printname,
                        $article,
                        $hpnumber,
                        $uom,
                        $variant_id
                    );
                }
            }
        }

        $row_num ='';
        $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        $currency = get_settings('currency');
        $bulkData = array();
       // $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
			
			
			
        foreach ($pro_search_res as $row) {
			
			$this->db->select('DISTINCT(product_variants.attribute_value_ids) as attriId, product_variants.*, attribute_values.value');
            $this->db->where('product_id',$row['pid']);
            $this->db->where('product_variants.status',1);
			$this->db->join ( 'attribute_values', 'attribute_values.id = product_variants.attribute_value_ids' , 'left' );
            $querys = $this->db->get('product_variants');
			
            $results = $querys->result_array();
			
		
		
		
			$unit = '';
			$units_added = array(); // Track unique units to avoid duplicates
			 foreach ($results as $rows) {
				
				
			if (isset($rows['purchase_price']) && $rows['purchase_price'] !== null) {
				// Per-variant purchase price already represents the cost for this UOM/pack size
				$puprice = (float)$rows['purchase_price'];
			} else {
				$con = convert_to_base_unit($rows['value']);
				$puprice = $con['qty'] * $row['purchase_price'];
			}
$pacprice = isset($rows['packing_price']) ? (float)$rows['packing_price'] : 0;
$margin = $rows['margin_percent'];
$margintype = $rows['margin_type'];

if ($margintype == 'Fixed') {
    $price_with_margin = $puprice + $margin;
} elseif ($margintype == 'Percentage') {
    $price_with_margin = $puprice + ($puprice * $margin / 100);
} else {
    // If margin type is unknown, don't apply margin
    $price_with_margin = $puprice;
}
$papupri= $puprice+$pacprice;
$final_product_price = $price_with_margin + $pacprice;
$disc_percent = isset($rows['disc_percent']) ? (float)$rows['disc_percent'] : 0;
$dynamic_special_price = ($price_with_margin - ($price_with_margin * $disc_percent / 100)) + $pacprice;

$sell_rate = ($dynamic_special_price > 0 && $dynamic_special_price < $final_product_price) ? $dynamic_special_price : $final_product_price;

				 
				 // Query product_barcode_info to get the correct print_name, article_no, and hpnumber for this variant
				 $barcode_info = $this->db->get_where('product_barcode_info', [
					 'product_id' => $row['pid'],
					 'UOM' => $rows['value']
				 ])->row_array();

				 if (!empty($barcode_info)) {
					 $article = !empty($barcode_info['article_no']) ? $barcode_info['article_no'] : (!empty($rows['product_article']) ? $rows['product_article'] : $row['article']);
					 $printname = !empty($barcode_info['print_name']) ? $barcode_info['print_name'] : (!empty($rows['print_name']) ? $rows['print_name'] : $row['name']);
					 $hpnumber = !empty($barcode_info['hpnumber']) ? $barcode_info['hpnumber'] : '';
				 } else {
					 $article = !empty($rows['product_article']) ? $rows['product_article'] : $row['article'];
					 $printname = !empty($rows['print_name']) ? $rows['print_name'] : $row['name'];
					 $hpnumber = '';
				 }
				 
				 // Use converted unit name (KG, pc, etc.) instead of raw value (1kg, 2kg, etc.)
				 $display_unit = strtoupper($con['unit']);
				 
				 // Only add unit if this variant hasn't been added yet
				 if (!in_array($rows['id'], $units_added)) {
					 $units_added[] = $rows['id'];
					 $unit .= '<option value="'.$rows['value'].'" varId="'.$rows['id'].'" propur="'.$papupri.'" purchase="'.number_format($sell_rate, 2).'"  margin="'.$rows['margin_percent'].'" disc="'.$rows['disc_percent'].'" stock="'.$rows['stock'].'" printname="'.$printname.'" productarticle="'.$article.'" hpnumber="'.$hpnumber.'">'. $rows['value'].'</option>';
				 }
			 }
			 
            $tax_percentage = isset($row['tax_percentage']) ? $row['tax_percentage'] : 0;
            $name = array($row['name'], '', $row['pid'], $tax_percentage, '', $row['stock'], $unit, $row_num, $row['type']);
            array_push($out, $name);
        }

        echo json_encode($out);


    } 
	
	
	public function myserach()
{
    $search = $this->input->post('name_startsWith', true);
    $offset = 0;
    $limit = 10;

    $this->db->select('p.id as product_id, p.name as product_name, p.product_price, pb.print_name, pb.article_no, pb.UOM, pb.weight');
    $this->db->from('products p');
    $this->db->join('product_barcode_info pb', 'pb.product_id = p.id');

    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('p.name', $search);
        $this->db->or_like('pb.print_name', $search);
        $this->db->or_like('pb.article_no', $search);
        $this->db->group_end();
    }

    $this->db->limit($limit, $offset);
    $query = $this->db->get();
    $results = $query->result_array();

    $data = [];
    foreach ($results as $row) {
        $final_price = round($row['product_price'] * $row['weight'], 2); 

        $data[] = [
            'name' => $row['print_name'] . ' - ' . $row['article_no'],
            'price' => $final_price,
            'uom' => $row['UOM'],
            'article_no' => $row['article_no'],
            'product_id' => $row['product_id']
        ];
    }

    echo json_encode($data);
}


    public function puchase_searchold()
    {
        $result = array();
        $out = array();
        $varia = array();
        $row_num = $this->input->post('row_num', true);
        $name = $this->input->post('name_startsWith', true);
        $wid = $this->input->post('wid', true);
        $qw = '';
        if ($wid > 0) {
            $qw = "(geopos_products.warehouse='$wid' ) AND ";
        }
        $join = '';
     /*    if ($this->aauth->get_user()->loc) {
            $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
            if (BDATA) $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR geopos_warehouse.loc=0) AND '; else $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
        } elseif (!BDATA) {
            $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
            $qw .= '(geopos_warehouse.loc=0) AND ';
        } */
      //  $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';

    
        if ($name) {
            //$query = $this->db->query(" FROM products WHERE UPPER(products.name) LIKE '%" . strtoupper($name) . "%' OR UPPER(products.sku) LIKE '" . strtoupper($name) . "%' LIMIT 6");
            $this->db->select('products.id as pid,products.name,products.sku');
            $this->db->group_start();
            $this->db->like('name',$name);
            $this->db->or_like('sku',$name);
            $this->db->group_end();
            $query = $this->db->get('products');
            $result = $query->result_array();

           // echo $this->db->last_query();
            foreach ($result as $row) {
				
			$this->db->select('products.id as pid,products.name,products.sku');
            $this->db->where('name',$name);
            $querys = $this->db->get('products');
            $results = $querys->result_array();
			
			
                $name = array($row['name'], '0', $row['pid'], '0','', '', '', $row_num);
                array_push($out, $name);
            }

            echo json_encode($out);
        }

    }

    public function csearch()
    {
        $name = $this->input->get('keyword', true);

        if ($name) {
            $this->db->select('users.id as id, username as name, address, city, mobile as phone, email, discount_c');
            $this->db->from('users');

            if (function_exists('is_seller_user') && is_seller_user()) {
                $this->db->where('assigned_seller', (int)$this->session->userdata('user_id'));
            } elseif ($this->aauth->get_user()->roleid != 1) {
                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('loc', $this->aauth->get_user()->loc);
                    if (BDATA) {
                        $this->db->or_where('loc', 0);
                    }
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('loc', 0);
                }
            }

            $search_term = strtoupper(trim($name));
            $this->db->group_start();
            $this->db->like('UPPER(username)', $search_term, 'both', false);
            $this->db->or_like('UPPER(mobile)', $search_term, 'after', false);
            $this->db->group_end();
            $this->db->limit(6);

            $result = $this->db->get()->result_array();
            echo '<ol>';
            $i = 1;
            foreach ($result as $row) {

                echo "<li onClick=\"selectCustomer('" . $row['id'] . "','" . $row['name'] . " ','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "','" . amountFormat_general($row['discount_c']) . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
                $i++;
            }
            echo '</ol>';
        }

    }

    public function csearch_select2()
    {
        $term = trim((string)$this->input->get('term', true));
        $results = array();

        $this->db->select('users.id as id, username as name, address, city, mobile as phone, email, discount_c');
        $this->db->from('users');

        if (function_exists('is_seller_user') && is_seller_user()) {
            $this->db->where('assigned_seller', (int)$this->session->userdata('user_id'));
        } elseif ($this->aauth->get_user()->roleid != 1) {
            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('loc', $this->aauth->get_user()->loc);
                if (BDATA) {
                    $this->db->or_where('loc', 0);
                }
                $this->db->group_end();
            } elseif (!BDATA) {
                $this->db->where('loc', 0);
            }
        }

        if ($term !== '') {
            $search_term = strtoupper($term);
            $this->db->group_start();
            $this->db->like('UPPER(username)', $search_term, 'both', false);
            $this->db->or_like('UPPER(mobile)', $search_term, 'after', false);
            $this->db->or_like('UPPER(email)', $search_term, 'both', false);
            $this->db->group_end();
        }

        $this->db->order_by('username', 'asc');
        $this->db->limit(50);
        $customers = $this->db->get()->result_array();

        foreach ($customers as $customer) {
            $results[] = array(
                'id' => $customer['id'],
                'text' => trim($customer['name'] . ' - ' . $customer['phone']),
                'name' => $customer['name'],
                'address' => $customer['address'],
                'city' => $customer['city'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
                'discount' => amountFormat_general($customer['discount_c'])
            );
        }

        $response = array(
            'results' => $results
        );
        print_r(json_encode($response));
    }

    public function party_search()
    {
        $result = array();
        $out = array();
        $tbl = 'users';
        $name = $this->input->get('keyword', true);

        $ty = $this->input->get('ty', true);
		
		 $whr = '';


        if ($this->aauth->get_user()->loc) {
            $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
            if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
        } elseif (!BDATA) {
            $whr = ' (loc=0) AND ';
        }
        if ($ty){ 
		
		$tbl = 'geopos_supplier';
		 $query = $this->db->query("SELECT id,name,address,city,phone,email FROM $tbl  WHERE $whr (UPPER(name)  LIKE '%" . strtoupper($name) . "%' OR UPPER(phone)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
		}else{
			
			 $query = $this->db->query("SELECT id,username as name,address,city,mobile as phone,email FROM $tbl  WHERE $whr (UPPER(username)  LIKE '%" . strtoupper($name) . "%' OR UPPER(mobile)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
			
		}
       


        if ($name) {
           
            $result = $query->result_array();
            echo '<ol>';
            $i = 1;
            foreach ($result as $row) {

                echo "<li onClick=\"selectCustomer('" . $row['id'] . "','" . $row['name'] . " ','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
                $i++;
            }
            echo '</ol>';
        }

    }
	
	
	public function myparty_search()
{
    $result = array();
    $out = array();
    $tbl = 'users';
    $name = $this->input->get('keyword', true);
    $ty = $this->input->get('ty', true);
    $whr = '';

    if ($this->aauth->get_user()->loc) {
        $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
        if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
    } elseif (!BDATA) {
        $whr = ' (loc=0) AND ';
    }

    if ($ty=='employee') {
       // $tbl = 'geopos_supplier';
       // $query = $this->db->query("SELECT id, name, address, city, phone, email FROM $tbl WHERE $whr (UPPER(name) LIKE '%" . strtoupper($name) . "%' OR UPPER(phone) LIKE '" . strtoupper($name) . "%') LIMIT 6"); 
		
		$tbl = 'geopos_employees';
        $query = $this->db->query("SELECT id, name, address, city, phone,'' as email FROM $tbl WHERE $whr (UPPER(name) LIKE '%" . strtoupper($name) . "%' OR UPPER(phone) LIKE '" . strtoupper($name) . "%') LIMIT 6");
    } else {
		
		if ($ty=='Basic') {
			
			$acctype ="account_type='Basic' ";
		}else{
			
			
			$acctype ="account_type='Expenses' ";
		}
		$tbl = 'geopos_accounts';
        $query = $this->db->query("SELECT id, holder as name, '' as address, '' as city, acn as phone, '' as email FROM $tbl WHERE  $acctype AND (UPPER(holder) LIKE '%" . strtoupper($name) . "%') LIMIT 6");
    }

    if ($name) {
        $result = $query->result_array();
        echo '<ol>';
        foreach ($result as $row) {
            echo "<li onClick=\"selectnewCustomer('" . $row['id'] . "','" . $row['name'] . "','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "')\">";
            echo "<p>" . $row['name'] . " (" . $row['phone'] . ")</p>";
            echo "</li>";
        }
        echo '</ol>';
    }
}


public function myparty_search_transection()
{
    $tbl = 'users';
    $name = $this->input->get('keyword', true);
    $ty = $this->input->get('ty', true);
    $whr = '';

    if ($this->aauth->get_user()->loc) {
        $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
        if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
    } elseif (!BDATA) {
        $whr = ' (loc=0) AND ';
    }

    // ✅ Combine all by default
    $result = [];

    if ($ty == 'employee' || $ty == 'all') {
        $query1 = $this->db->query("
            SELECT id, name, address, city, phone, '' as email, 'employee' as source
            FROM geopos_employees
            WHERE $whr (UPPER(name) LIKE '%" . strtoupper($name) . "%' OR phone LIKE '%" . strtoupper($name) . "%')
        ");
        $result = array_merge($result, $query1->result_array());
    }

    if ($ty == 'Basic' || $ty == 'other' || $ty == 'all' || $ty == '') {
        $accountCondition = '';
        if ($ty == 'Basic') {
            $accountCondition = "account_type='Basic'";
        } elseif ($ty == 'other') {
            $accountCondition = "account_type='Expenses'";
        } else {
            $accountCondition = "(account_type='Basic' OR account_type='Expenses')";
        }

        $query2 = $this->db->query("
            SELECT id, holder as name, '' as address, '' as city, acn as phone, '' as email, 'account' as source
            FROM geopos_accounts
            WHERE $accountCondition AND (UPPER(holder) LIKE '%" . strtoupper($name) . "%')
        ");
        $result = array_merge($result, $query2->result_array());
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}



    public function pos_c_search()
    {
        $result = array();
        $out = array();
        $name = $this->input->get('keyword', true);
        $whr = '';
      /*   if ($this->aauth->get_user()->loc) {
            $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
            if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
        } elseif (!BDATA) {
            $whr = ' (loc=0) AND ';
        } */

        if ($name) {
            $query = $this->db->query("SELECT id,username,mobile FROM users WHERE $whr (UPPER(username)  LIKE '%" . strtoupper($name) . "%' OR UPPER(mobile)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
            $result = $query->result_array();
            echo '<ol>';
            $i = 1;
            foreach ($result as $row) {
                echo "<li onClick=\"PselectCustomer('" . $row['id'] . "','" . $row['username'] . " ')\"><span>$i</span><p>" . $row['username'] . " &nbsp; &nbsp  " . $row['mobile'] . "</p></li>";
                $i++;
            }
            echo '</ol>';
        }

    }


    public function supplier()
    {
        $result = array();
        $out = array();
        $name = $this->input->get('keyword', true);

        $whr = '';
        if ($this->aauth->get_user()->loc) {
            $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
            if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
        } elseif (!BDATA) {
            $whr = ' (loc=0) AND ';
        }
        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
            $whr .= ' eid=' . $seller_id . ' AND ';
        }
        if ($name) {
            $query = $this->db->query("SELECT id,name,address,city,phone,email FROM geopos_supplier WHERE $whr (UPPER(name)  LIKE '%" . strtoupper($name) . "%' OR UPPER(phone)  LIKE '" . strtoupper($name) . "%') LIMIT 6");
            $result = $query->result_array();
            echo '<ol>';
            $i = 1;
            foreach ($result as $row) {
                echo "<li onClick=\"selectSupplier('" . $row['id'] . "','" . $row['name'] . " ','" . $row['address'] . "','" . $row['city'] . "','" . $row['phone'] . "','" . $row['email'] . "')\"><span>$i</span><p>" . $row['name'] . " &nbsp; &nbsp  " . $row['phone'] . "</p></li>";
                $i++;
            }
            echo '</ol>';
        }

    }

public function mysupplier()
{
    $name = $this->input->get('keyword', true);
    $whr = '';

    if ($this->aauth->get_user()->loc) {
        $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
        if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
    } elseif (!BDATA) {
        $whr = ' (loc=0) AND ';
    }

    $seller_whr = '';
    $params = [];
    $related_sellers = array_values(array_filter(array_map('intval', explode(',', (string)$this->input->get('related_sellers', true)))));
    if (!empty($related_sellers) && $this->db->field_exists('eid', 'geopos_supplier')) {
        // Scope to the sellers actually related to the items being converted (e.g. the indent page).
        $placeholders = implode(',', array_fill(0, count($related_sellers), '?'));
        $seller_whr = ' AND eid IN (' . $placeholders . ') ';
        $params = $related_sellers;
    } elseif (function_exists('is_seller_user') && is_seller_user() && $this->db->field_exists('eid', 'geopos_supplier')) {
        $seller_whr = ' AND eid = ? ';
        $params[] = (int)$this->session->userdata('user_id');
    }

    if ($name) {
        $params = array_merge(['%' . strtoupper($name) . '%', strtoupper($name) . '%'], $params);
        $query = $this->db->query("
            SELECT id, name, address, city, phone, email
            FROM geopos_supplier
            WHERE $whr
            (UPPER(name) LIKE ?
            OR UPPER(phone) LIKE ?)
            $seller_whr
            LIMIT 6
        ", $params);
        $result = $query->result_array();

        if (!empty($result)) {
            foreach ($result as $row) {
                $full_address = $row['address'] . ', ' . $row['city'];
                echo '<div class="select-supplier" 
                        data-id="' . $row['id'] . '" 
                        data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '" 
                        data-phone="' . htmlspecialchars($row['phone'], ENT_QUOTES) . '" 
                        data-address="' . htmlspecialchars($full_address, ENT_QUOTES) . '"
                        style="cursor:pointer; padding:8px; border-bottom:1px solid #ddd;">
                        <strong>' . htmlspecialchars($row['name']) . '</strong> - ' . htmlspecialchars($row['phone']) . '
                    </div>';
            }
        } else {
            echo '<div>No suppliers found.</div>';
        }
    }
}

public function myemployee()
{
    $name = $this->input->get('keyword', true);
    $whr = '';

    if ($this->aauth->get_user()->loc) {
        $whr = ' (loc=' . $this->aauth->get_user()->loc . ' OR loc=0) AND ';
        if (!BDATA) $whr = ' (loc=' . $this->aauth->get_user()->loc . ' ) AND ';
    } elseif (!BDATA) {
        $whr = ' (loc=0) AND ';
    }

    if ($name) {
        $query = $this->db->query("
            SELECT id, name, address, city, phone
            FROM geopos_employees
            WHERE 
            (UPPER(name) LIKE '%" . strtoupper($name) . "%'
            OR UPPER(phone) LIKE '" . strtoupper($name) . "%')
            LIMIT 6
        ");
        $result = $query->result_array();

        if (!empty($result)) {
            foreach ($result as $row) {
                $full_address = $row['address'] . ', ' . $row['city'];
                echo '<div class="select-employee"
                        data-id="' . $row['id'] . '"
                        data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '"
                        data-phone="' . htmlspecialchars($row['phone'], ENT_QUOTES) . '"
                        data-address="' . htmlspecialchars($full_address, ENT_QUOTES) . '"
                        style="cursor:pointer; padding:8px; border-bottom:1px solid #ddd;">
                        <strong>' . htmlspecialchars($row['name']) . '</strong> - ' . htmlspecialchars($row['phone'], ENT_QUOTES) . '
                    </div>';
            }
        } else {
            echo '<div>No employees found.</div>';
        }
    }
}


  public function get_suppliers()
    {
        $keyword = $this->input->get('term', true);
        $data = $this->search_model->search_suppliers($keyword);

        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id' => $row['id'],
                'label' => $row['name'] . ' - ' . $row['phone'],
                'value' => $row['name'],
                'phone' => $row['phone'],
                'address' => $row['address'] . ', ' . $row['city']
            ];
        }
        echo json_encode($result);
    }
	

    public function pos_search()
    {

        $out = '';
        $this->load->model('plugins_model', 'plugins');
        $billing_settings = $this->plugins->universal_api(67);
        $name = $this->input->post('name', true);
        $cid = $this->input->post('cid', true);
        $wid = $this->input->post('wid', true);
        $qw = '';
        if ($wid > 0) {
            $qw .= "(geopos_products.warehouse='$wid') AND ";
        }
        if ($billing_settings['key2']) $qw .= "(geopos_products.expiry IS NULL OR DATE (geopos_products.expiry)<" . date('Y-m-d') . ") AND ";
        if ($cid > 0) {
            $qw .= "(geopos_products.pcat='$cid') AND ";
        }
        $join = '';
        if ($this->aauth->get_user()->roleid != 1) {
            if ($this->aauth->get_user()->loc) {
                $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
                if (BDATA) $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR geopos_warehouse.loc=0) AND '; else $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
            } elseif (!BDATA) {
                $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
                $qw .= '(geopos_warehouse.loc=0) AND ';
            }
        }

        $e = '';
        if ($billing_settings['key1'] == 1) {
            $e .= ',geopos_product_serials.serial';
            $join .= 'LEFT JOIN geopos_product_serials ON geopos_product_serials.product_id=geopos_products.pid ';
            $qw .= '(geopos_product_serials.status=0) AND  ';
        }


        $bar = '';
        if (is_numeric($name)) {
            $b = array('-', '-', '-');
            $c = array(3, 4, 11);
            $barcode = $name;
            for ($i = count($c) - 1; $i >= 0; $i--) {
                $barcode = substr_replace($barcode, $b[$i], $c[$i], 0);
            }

            $bar = " OR (geopos_products.barcode LIKE '" . (substr($barcode, 0, -1)) . "%' OR geopos_products.barcode LIKE '" . $name . "%')";
        }
        if ($billing_settings['key1'] == 2) {

            $query = "SELECT geopos_products.*,geopos_product_serials.serial FROM geopos_product_serials  LEFT JOIN geopos_products  ON geopos_products.pid=geopos_product_serials.product_id $join WHERE " . $qw . "geopos_product_serials.serial LIKE '" . strtoupper($name) . "%'  AND (geopos_products.qty>0) LIMIT 16";


        } else {
            $query = "SELECT geopos_products.* $e FROM geopos_products $join WHERE " . $qw . "(UPPER(geopos_products.product_name) LIKE '%" . strtoupper($name) . "%' $bar OR geopos_products.product_code LIKE '" . strtoupper($name) . "%') AND (geopos_products.qty>0) LIMIT 16";

        }


        $query = $this->db->query($query);

        $result = $query->result_array();
        $i = 0;
        echo '<div class="row match-height">';
        foreach ($result as $row) {

            $out .= '    <div class="col-3 border mb-1 "><div class="rounded">
                                 <a   id="posp' . $i . '"  class="select_pos_item btn btn-outline-light-blue round"   data-name="' . $row['product_name'] . '"  data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '"  data-tax="' . amountFormat_general($row['taxrate']) . '"  data-discount="' . amountFormat_general($row['disrate']) . '"   data-pcode="' . $row['product_code'] . '"   data-pid="' . $row['pid'] . '"  data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . @$row['serial'] . '">
                                        <img class="round"
                                             src="' . get_image_url($row['image']) . '"  style="max-height: 100%;max-width: 100%">
                                        <div class="text-xs-center text">
                                       
                                            <small style="white-space: pre-wrap;">' . $row['product_name'] . '</small>

                                            
                                        </div></a>
                                  
                                </div></div>';

            $i++;
            //   if ($i % 4 == 0) $out .= '</div><div class="row">';
        }

        echo $out;

    }

    public function v2_pos_search()
    {

        $out = '';
        $this->load->model('plugins_model', 'plugins');
        $billing_settings = $this->plugins->universal_api(67);
        $name = $this->input->post('name', true);
        $cid = $this->input->post('cid', true);
        $wid = $this->input->post('wid', true);
         $enable_bar = $this->input->post('bar', true);
$flag_p=false;

        $qw = '';

        if ($wid > 0) {
            $qw .= "(geopos_products.warehouse='$wid') AND ";
        }
        if ($billing_settings['key2']) $qw .= "(geopos_products.expiry IS NULL OR DATE (geopos_products.expiry)<" . date('Y-m-d') . ") AND ";
        if ($cid > 0) {
            $qw .= "(geopos_products.pcat='$cid') AND ";
        }
        $join = '';

        if ($this->aauth->get_user()->roleid != 1) {
            if ($this->aauth->get_user()->loc) {
                $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
                if (BDATA) $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR geopos_warehouse.loc=0) AND '; else $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
            } elseif (!BDATA) {
                $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
                $qw .= '(geopos_warehouse.loc=0) AND ';
            }
        }

        $e = '';
        if ($billing_settings['key1'] == 1) {
            $e .= ',geopos_product_serials.serial';
            $join .= 'LEFT JOIN geopos_product_serials ON geopos_product_serials.product_id=geopos_products.pid ';
            $qw .= '(geopos_product_serials.status=0) AND  ';
        }

        $bar = '';
   $p_class='v2_select_pos_item';
        if ($enable_bar=='true' AND is_numeric($name) AND strlen($name)>8) {
$flag_p=true;
            $bar = " (geopos_products.barcode = '" . (substr($name, 0, -1)) . "' OR geopos_products.barcode LIKE '" . $name . "%')";

               $query = "SELECT geopos_products.*  FROM geopos_products $join WHERE " . $qw . "$bar AND (geopos_products.qty>0) ORDER BY geopos_products.product_name LIMIT 6";
               $p_class='v2_select_pos_item_bar';

        } elseif ($enable_bar=='false' OR !$enable_bar ) {
            $flag_p=true;
            if ($billing_settings['key1'] == 2) {

                $query = "SELECT geopos_products.*,geopos_product_serials.serial FROM geopos_product_serials  LEFT JOIN geopos_products  ON geopos_products.pid=geopos_product_serials.product_id $join WHERE " . $qw . "geopos_product_serials.serial LIKE '" . strtoupper($name) . "%'  AND (geopos_products.qty>0) LIMIT 18";

            } else {

                $query = "SELECT geopos_products.* $e FROM geopos_products $join WHERE " . $qw . "(UPPER(geopos_products.product_name) LIKE '%" . strtoupper($name) . "%' $bar OR geopos_products.product_code LIKE '" . strtoupper($name) . "%') AND (geopos_products.qty>0) ORDER BY geopos_products.product_name LIMIT 18";
            }


        }

if($flag_p) {
    $query = $this->db->query($query);
    $result = $query->result_array();
    $i = 0;
    $out = '<div class="row match-height">';
    foreach ($result as $row) {
        if ($bar) $bar = $row['barcode'];
        $out .= '    <div class="col-2 border mb-1"  ><div class=" rounded" >
                                 <a  id="posp' . $i . '"  class="' . $p_class . ' round"   data-name="' . $row['product_name'] . '"  data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '"  data-tax="' . amountFormat_general($row['taxrate']) . '"  data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '"   data-pid="' . $row['pid'] . '"  data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . @$row['serial'] . '" data-bar="' . $bar . '">
                                        <img class="round"
                                             src="' . get_image_url($row['image']) . '"  style="max-height: 100%;max-width: 100%">
                                        <div class="text-center" style="margin-top: 4px;">
                                       
                                            <small style="white-space: pre-wrap;">' . $row['product_name'] . '</small>

                                            
                                        </div></a>
                                  
                                </div></div>';

        $i++;

    }


    $out .= '</div>';

    echo $out;
}


    }

      public function group_pos_search()
    {

        $out = '';
        $this->load->model('plugins_model', 'plugins');
        $billing_settings = $this->plugins->universal_api(67);
        $name = $this->input->post('name', true);
        $cid = $this->input->post('cid', true);
        $wid = $this->input->post('wid', true);


        $qw = '';

        if ($wid > 0) {
            $qw .= "(geopos_product_groups.warehouse='$wid') AND ";
        }

        $join = '';

        if ($this->aauth->get_user()->roleid != 1) {
            if ($this->aauth->get_user()->loc) {
                 $qw .= "(geopos_product_groups.loc='".$this->aauth->get_user()->loc."') AND ";
                $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
                if (BDATA) $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' OR geopos_warehouse.loc=0) AND '; else $qw .= '(geopos_warehouse.loc=' . $this->aauth->get_user()->loc . ' ) AND ';
            } elseif (!BDATA) {
                $join = 'LEFT JOIN geopos_warehouse ON geopos_warehouse.id=geopos_products.warehouse';
                $qw .= '(geopos_warehouse.loc=0) AND ';
            }
        }

        $e = '';
        if ($billing_settings['key1'] == 1) {
            $e .= ',geopos_product_serials.serial';
            $join .= 'LEFT JOIN geopos_product_serials ON geopos_product_serials.product_id=geopos_products.pid ';
            $qw .= '(geopos_product_serials.status=0) AND  ';
        }

        $bar = '';

        if (is_numeric($name)) {
            $b = array('-', '-', '-');
            $c = array(3, 4, 11);
            $barcode = $name;
            for ($i = count($c) - 1; $i >= 0; $i--) {
                $barcode = substr_replace($barcode, $b[$i], $c[$i], 0);
            }
            //    echo(substr($barcode, 0, -1));
            $bar = " OR (geopos_products.barcode LIKE '" . (substr($barcode, 0, -1)) . "%' OR geopos_products.barcode LIKE '" . $name . "%')";
            //  $query = "SELECT geopos_products.* FROM geopos_products $join WHERE " . $qw . " $bar AND (geopos_products.qty>0) LIMIT 16";
        }
        if ($billing_settings['key1'] == 2) {

            $query = "SELECT geopos_products.*,geopos_product_serials.serial FROM geopos_product_serials  LEFT JOIN geopos_products  ON geopos_products.pid=geopos_product_serials.product_id $join WHERE " . $qw . "geopos_product_serials.serial LIKE '" . strtoupper($name) . "%'  AND (geopos_products.qty>0) LIMIT 18";

        } else {
            $query = "SELECT geopos_products.* $e FROM geopos_products $join WHERE " . $qw . "(UPPER(geopos_products.product_name) LIKE '%" . strtoupper($name) . "%' $bar OR geopos_products.product_code LIKE '" . strtoupper($name) . "%') AND (geopos_products.qty>0) ORDER BY geopos_products.product_name LIMIT 18";
        }

        $query = $this->db->query($query);
        $result = $query->result_array();
        $i = 0;
        echo '<div class="row match-height">';
        foreach ($result as $row) {

            $out .= '    <div class="col-2 border mb-1"  ><div class=" rounded" >
                                 <a  id="posp' . $i . '"  class="v2_select_pos_item round"   data-name="' . $row['product_name'] . '"  data-price="' . amountExchange_s($row['product_price'], 0, $this->aauth->get_user()->loc) . '"  data-tax="' . amountFormat_general($row['taxrate']) . '"  data-discount="' . amountFormat_general($row['disrate']) . '" data-pcode="' . $row['product_code'] . '"   data-pid="' . $row['pid'] . '"  data-stock="' . amountFormat_general($row['qty']) . '" data-unit="' . $row['unit'] . '" data-serial="' . @$row['serial'] . '">
                                        <img class="round"
                                             src="' . get_image_url($row['image']) . '"  style="max-height: 100%;max-width: 100%">
                                        <div class="text-center" style="margin-top: 4px;">
                                       
                                            <small style="white-space: pre-wrap;">' . $row['product_name'] . '</small>

                                            
                                        </div></a>
                                  
                                </div></div>';

            $i++;

        }

        echo $out;

    }


   /*  public function puchase_search($flag = NULL, $seller_id = NULL, $p_status = NULL)
    {
        $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
     
        $search = $this->input->post('name_startsWith', true);
        if ( $search != '') {
            $search = trim($this->input->post('name_startsWith', true));
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'p.`description`' => $search, 'p.`short_description`' => $search, 'c.name' => $search];
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

   

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();
        $search_res = $this->db->select('product_variants.id AS id,  c.name as category_name,sd.store_name, p.id as pid,p.rating,p.no_of_ratings,p.name, p.type, p.image, p.status,product_variants.price , product_variants.special_price, product_variants.stock, p.stock as prostock, p.purchase_price as mpurchaseprice')
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
        $out = array();
        $row_num ='';
        $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        $currency = get_settings('currency');
        $bulkData = array();
       // $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($pro_search_res as $row) {
			
			$this->db->select('DISTINCT(product_variants.attribute_value_ids) as attriId, product_variants.*, attribute_values.value');
            $this->db->where('product_id',$row['pid']);
            $this->db->where('product_variants.status',1);
			$this->db->join ( 'attribute_values', 'attribute_values.id = product_variants.attribute_value_ids' , 'left' );
            $querys = $this->db->get('product_variants');
			
		//	echo $this->db->last_query();
            $results = $querys->result_array();
			$unit = '';
			 foreach ($results as $rows) {
				 
				 if($rows['value']==''){
					 
					$values= 'Peace'; 
				 }else{
					 
					$values = $rows['value'];
				 }
				 $unit .= '<option varId="'.$rows['id'].'" purchase="'.$rows['purchase_price'].'" margin="'.$rows['margin_percent'].'" disc="'.$rows['disc_percent'].'" stock="'.$row['prostock'].'" >'.$values.'</option>';
			 }
			 
            $name = array($row['name'], '', $row['pid'], '', $row['mpurchaseprice'], $row['prostock'], $unit, $row_num, $row['type']);
            array_push($out, $name);
        }

        echo json_encode($out);

   
    } */ 
	
	
	private function get_latest_purchase_rate($product_id, $variant_id = 0)
    {
        // Mirrors Products::get_purchase_price_history() (used by productcategory/viewwarehouse)
        // so the Rate shown here matches the "Purchase Price" / "Updated Price" columns there.
        $this->db->select('purchage_rate');
        $this->db->from('product_ledger');
        $this->db->where('product_id', $product_id);
        $this->db->where('purchage_rate !=', 0);
        $this->db->order_by('created_date', 'ASC');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $first_row = $this->db->get()->row_array();
        $first_price = isset($first_row['purchage_rate']) ? (float)$first_row['purchage_rate'] : 0;

        $this->db->select('purchage_rate, ledger_type');
        $this->db->from('product_ledger');
        $this->db->where('product_id', $product_id);
        $this->db->where('purchage_rate !=', 0);
        $this->db->order_by('created_date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last_row = $this->db->get()->row_array();

        $updated_price = 0;
        if (isset($last_row['purchage_rate'])) {
            $latest = (float)$last_row['purchage_rate'];
            if ($last_row['ledger_type'] === 'Price Update' || $latest != $first_price) {
                $updated_price = $latest;
            }
        }

        return $updated_price > 0 ? $updated_price : $first_price;
    }

	public function puchase_search($flag = NULL, $seller_id = NULL, $p_status = NULL)
    {
        $wid = (int)$this->input->post('wid', true);
        if (function_exists('is_seller_user') && is_seller_user()) {
            $seller_id = (int)$this->session->userdata('user_id');
        }
        $settings = get_settings('system_settings', true);
        $low_stock_limit = isset($settings['low_stock_limit']) ? $settings['low_stock_limit'] : 5;
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
     
        $search = $this->input->post('name_startsWith', true);
        if ( $search != '') {
            $search = trim($this->input->post('name_startsWith', true));
            $multipleWhere = ['p.`id`' => $search, 'p.`name`' => $search, 'p.`description`' => $search, 'p.`short_description`' => $search, 'c.name' => $search];
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
        if ($wid > 0) {
            $count_res->where("p.warehouse", $wid);
        }

        if (isset($category_id) && !empty($category_id)) {
            $count_res->group_Start();
            $count_res->or_where('p.category_id', $category_id);
            $count_res->or_where('c.parent_id', $category_id);
            $count_res->group_End();
        }

        $product_count = $count_res->get('products p')->result_array();
        $search_res = $this->db->select('product_variants.id AS id,  c.name as category_name,sd.store_name, p.id as pid,p.rating,p.no_of_ratings,p.name, p.type, p.image, p.status,product_variants.price , product_variants.special_price, product_variants.stock, product_variants.margin_type, p.stock as prostock, p.purchase_price as mpurchaseprice, p.short_description, tax.percentage as tax_percentage')
            ->join("categories c", "p.category_id=c.id")
            ->join("taxes tax", "tax.id = p.tax", "left")
            ->join("seller_data sd", "sd.user_id=p.seller_id ", 'left')
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
        if ($wid > 0) {
            $search_res->where("p.warehouse", $wid);
        }
        $out = array();
        $row_num ='';
        $pro_search_res = $search_res->group_by('pid')->order_by($sort, "DESC")->limit($limit, $offset)->get('products p')->result_array();
        $currency = get_settings('currency');
        $bulkData = array();
       // $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();

        foreach ($pro_search_res as $row) {
			
			$this->db->select('DISTINCT(product_variants.attribute_value_ids) as attriId, product_variants.*, attribute_values.value');
            $this->db->where('product_id',$row['pid']);
            $this->db->where('product_variants.status',1);
			$this->db->join ( 'attribute_values', 'attribute_values.id = product_variants.attribute_value_ids' , 'left' );
            $querys = $this->db->get('product_variants');
			
		//	echo $this->db->last_query();
            $results = $querys->result_array();
			$unit = '';
			$units_added = array(); // Track unique units to avoid duplicates
			 foreach ($results as $rows) {
				 
			/* 	 if($rows['value']==''){
					 
					$values= 'Peace'; 
				 }else{
					 
					$values = $rows['value'];
				 }
				 $unit .= '<option varId="'.$rows['id'].'" purchase="'.$rows['purchase_price'].'" margin="'.$rows['margin_percent'].'" disc="'.$rows['disc_percent'].'" stock="'.$row['prostock'].'" >'.$values.'</option>';
			 */

				if($rows['value']==''){
					 // Only add Peace if not already added
					 if (!in_array('PEACE', $units_added)) {
						 $units_added[] = 'PEACE';
						 $latest_rate = $this->get_latest_purchase_rate($row['pid'], $rows['id']);
						 if ($latest_rate <= 0) {
							 $latest_rate = (float)$row['mpurchaseprice'];
						 }
						 $unit .= '<option value="Peace" varId="'.$rows['id'].'" purchase="'.number_format($latest_rate, 2).'" margintype="'.$rows['margin_type'].'"  margin="'.$rows['margin_percent'].'"  disc="'.$rows['disc_percent'].'" stock="'.$rows['stock'].'" >Peace</option>';
					 }
				 }else{
					 // Keep each pack size (e.g. 1Kg, 2Kg) as its own selectable option
					 $display_unit = $rows['value'];

					 // Only add unit if this variant hasn't been added yet
					 if (!in_array($rows['id'], $units_added)) {
						 $units_added[] = $rows['id'];
						 $latest_rate = $this->get_latest_purchase_rate($row['pid'], $rows['id']);
						 if ($latest_rate <= 0) {
							 $latest_rate = (float)$row['mpurchaseprice'];
						 }
						 $unit .= '<option value="'.$display_unit.'" varId="'.$rows['id'].'" purchase="'.number_format($latest_rate, 2).'" margintype="'.$rows['margin_type'].'" margin="'.$rows['margin_percent'].'" disc="'.$rows['disc_percent'].'" stock="'.$rows['stock'].'" >'. $display_unit.'</option>';
					 }
				 }
			}

            $tax_percentage = isset($row['tax_percentage']) ? $row['tax_percentage'] : 0;
            $description = isset($row['short_description']) ? $row['short_description'] : '';
            $name = array($row['name'], '', $row['pid'], $tax_percentage, '', $description, $unit, $row_num, $row['type']);
            array_push($out, $name);
        }

        echo json_encode($out);

   
    }



    public function puchase_searchf()
    {
        $result = array();
        $out = array();
        $row_num = $this->input->post('row_num', true);
        $name = $this->input->post('name_startsWith', true);
        $wid = $this->input->post('wid', true);
        $qw = '';
        if ($wid > 0) {
            $qw = "(geopos_products.warehouse='$wid' ) AND ";
        }
        $join = '';
   
        if ($name) {
            //$query = $this->db->query(" FROM products WHERE UPPER(products.name) LIKE '%" . strtoupper($name) . "%' OR UPPER(products.sku) LIKE '" . strtoupper($name) . "%' LIMIT 6");
            $this->db->select('products.id as pid,products.name,products.sku');
            $this->db->group_start();
            $this->db->like('name',$name);
            $this->db->or_like('sku',$name);
            $this->db->group_end();
            $query = $this->db->get('products');
            $result = $query->result_array();

           // echo $this->db->last_query();
            foreach ($result as $row) {
                $name = array($row['name'], '0', $row['pid'], '0','', '', '', $row_num);
                array_push($out, $name);
            }

            echo json_encode($out);
        }

    }
 public function productserchnew()
    {
        $keyword = $this->input->get('keyword', true);

        $this->db->select('id, name, sku');
        $this->db->from('products');
        $this->db->like('name', $keyword);
        $this->db->or_like('sku', $keyword);
        $this->db->limit(10);
        $query = $this->db->get();

        $output = '<ul class="list-group">';
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $output .= '<li class="list-group-item" onclick="selectProduct('.$row->id.', \''.htmlspecialchars($row->name, ENT_QUOTES).'\')">'. $row->name .  '</li>';
            }
        } else {
            $output .= '<li class="list-group-item">No product found</li>';
        }
        $output .= '</ul>';

        echo $output;
    }
}
