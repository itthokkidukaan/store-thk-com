<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        if (!$this->aauth->premission(2)) {

            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');

        }
        $this->load->database();
        $this->load->model('products_model', 'products');
        $this->load->model('categories_model');
        $this->load->model(['product_model', 'category_model', 'rating_model']);
       
        $this->load->library("Custom");
        $this->li_a = 'stock';

    }

    public function index()
    {
        $head['title'] = "Products";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/products');
        $this->load->view('fixed/footer');
    }

    public function cat()
    {
        $head['title'] = "Product Categories";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/cat_productlist');
        $this->load->view('fixed/footer');

    }


    public function add()
    {
      /*   $data['cat'] = $this->categories_model->category_list();
        $data['units'] = $this->products->units();
        $data['warehouse'] = $this->categories_model->warehouse_list();
        $data['custom_fields'] = $this->custom->add_fields(4);
        $this->load->model('units_model', 'units');
        $data['variables'] = $this->units->variables_list();
        $head['title'] = "Add Product";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/product-add', $data);
        $this->load->view('fixed/footer'); */
        $data['cat'] = $this->categories_model->category_list();
        $data['units'] = $this->products->units();
        $data['warehouse'] = $this->categories_model->warehouse_list();
        $data['custom_fields'] = $this->custom->add_fields(4);
        $this->load->model('units_model', 'units');
        $data['variables'] = $this->units->variables_list();
        $head['title'] = "Add Product";
        $head['usernm'] = $this->aauth->get_user()->username;

        $data['taxes'] = fetch_details('taxes', null, '*');
        $data['countries'] = fetch_details('countries', null, 'name,id');
        $data['sellers'] = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id  ')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->join('seller_data sd', ' sd.user_id = u.id ')
            ->where(['ug.group_id' => '4'])
            ->get('users u')->result_array();
    /*     if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
            $data['title'] = 'Update Product | ' . $settings['app_name'];
            $data['meta_description'] = 'Update Product | ' . $settings['app_name'];
            $product_details = fetch_details('products', ['id' => $_GET['edit_id']], '*');
            $countries = fetch_details('countries', ['name' => $product_details[0]['made_in']], 'name');
            if (!empty($product_details)) {
                $data['product_details'] = $product_details;
                $data['product_variants'] = get_variants_values_by_pid($_GET['edit_id']);
                $product_attributes = fetch_details('product_attributes', ['product_id' => $_GET['edit_id']]);
                if (!empty($product_attributes) && !empty($product_details)) {
                    $data['product_attributes'] = $product_attributes;
                }
            }
    } */
	
            $attributes = $this->db->select('attr_val.id,attr.name as attr_name ,attr_set.name as attr_set_name,attr_val.value')
                ->join('attributes attr', 'attr.id=attr_val.attribute_id')
                ->join('attribute_set attr_set', 'attr_set.id=attr.attribute_set_id')
                ->where(['attr.status' => 1, 'attr_set.status' => 1])
                ->get('attribute_values attr_val')->result_array();

            $attributes_refind = array();

            for ($i = 0; $i < count($attributes); $i++) {
                if (!array_key_exists($attributes[$i]['attr_set_name'], $attributes_refind)) {
                    $attributes_refind[$attributes[$i]['attr_set_name']] = array();
                    for ($j = 0; $j < count($attributes); $j++) {
                        if ($attributes[$i]['attr_set_name'] == $attributes[$j]['attr_set_name']) {
                            if (!array_key_exists($attributes[$j]['attr_name'], $attributes_refind[$attributes[$i]['attr_set_name']])) {
                                $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array();
                            }
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['id'] = $attributes[$j]['id'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['text'] = $attributes[$j]['value'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['data-values'] = $attributes[$j]['value'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array_values($attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']]);
                        }
                    }
                }
            }
            $data['categories'] = $this->category_model->get_categories();
            $data['attributes_refind'] = $attributes_refind;
			
		
		$this->load->view('fixed/header', $head);
        $this->load->view('products/product-add', $data);
        $this->load->view('fixed/footer'); 
}


    public function product_list()
    {
        $catid = $this->input->get('id');
        $sub = $this->input->get('sub');

        if ($catid > 0) {
            $list = $this->products->get_datatables($catid, '', $sub);
        } else {
            $list = $this->products->get_datatables();
        }
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $row[] = $no;
            $id = $prd->id;
            $row[] = '<a href="#" data-object-id="' . $id . '" class="view-object"><span class="avatar-lg align-baseline"><img src="' . base_url() . $prd->image . '"  height="100px"></span>&nbsp;' . $prd->name . '</a>';
            $row[] = +$prd->stock;
            $row[] = $prd->sku;
            $row[] = $prd->c_title;
            $row[] = $prd->title;
            $row[] = amountExchange($prd->special_price, 0, $this->aauth->get_user()->loc);
            $row[] = '<a href="#" data-object-id="' . $id . '" class="btn btn-success  btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> 
<div class="btn-group">
                                    <button type="button" class="btn btn-indigo dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i>  ' . $this->lang->line('Print') . '</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="' . base_url() . 'products/barcode?id=' . $id. '" target="_blank"> ' . $this->lang->line('BarCode') . '</a><div class="dropdown-divider"></div> <a class="dropdown-item" href="' . base_url() . 'products/posbarcode?id=' . $id . '" target="_blank"> ' . $this->lang->line('BarCode') . ' - Compact</a> <div class="dropdown-divider"></div>
                                             <a class="dropdown-item" href="' . base_url() . 'products/label?id=' . $id . '" target="_blank"> ' . $this->lang->line('Product') . ' Label</a><div class="dropdown-divider"></div>
                                         <a class="dropdown-item" href="' . base_url() . 'products/poslabel?id=' . $id . '" target="_blank"> Label - Compact</a></div></div><a class="btn btn-pink  btn-sm" href="' . base_url() . 'products/report_product?id=' . $id. '" target="_blank"> <span class="fa fa-pie-chart"></span> ' . $this->lang->line('Reports') . '</a> <div class="btn-group">
                                    <button type="button" class="btn btn btn-primary dropdown-toggle   btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-cog"></i>  </button>
                                    <div class="dropdown-menu">
&nbsp;<a href="' . base_url() . 'products/edit?id=' . $id . '"  class="btn btn-purple btn-sm"><span class="fa fa-edit"></span>' . $this->lang->line('Edit') . '</a><div class="dropdown-divider"></div>&nbsp;<a href="#" data-object-id="' . $id . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span>' . $this->lang->line('Delete') . '</a>
                                    </div>
                                </div>';
            $data[] = $row;
        }
        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->products->count_all($catid, '', $sub),
            "recordsFiltered" => $this->products->count_filtered($catid, '', $sub),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }


public function newprolist(){
   // get_product_details
if( $this->session->userdata('usertype')==1){
   $seller_id =  (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $this->input->get('seller_id', true) : NULL;
   $status =  (isset($_GET['status']) && $_GET['status'] != "") ? $this->input->get('status', true) : NULL;
   if (isset($_GET['flag']) && !empty($_GET['flag'])) {
       return $this->products->get_product_details($_GET['flag'], $seller_id, $status);
   }
   return $this->products->get_product_details(null, $seller_id, $status);
}else{

   // $seller_id =  $this->session->userdata('user_id');
    $seller_id =  (isset($_GET['seller_id']) && !empty($_GET['seller_id'])) ? $this->input->get('seller_id', true) : NULL;
    $status =  (isset($_GET['status']) && $_GET['status'] != "") ? $this->input->get('status', true) : NULL;
    if (isset($_GET['flag']) && !empty($_GET['flag'])) {
        return $this->products->get_product_details($_GET['flag'], $seller_id, $status);
    }
    return $this->products->get_product_details(null, $seller_id, $status);
}
}

    public function addproduct()
    {
        $product_name = $this->input->post('product_name', true);
        $catid = $this->input->post('product_cat');
        $warehouse = $this->input->post('product_warehouse');
        $product_code = $this->input->post('product_code');
        $product_price = numberClean($this->input->post('product_price'));
        $factoryprice = numberClean($this->input->post('fproduct_price'));
        $taxrate = numberClean($this->input->post('product_tax', true));
        $disrate = numberClean($this->input->post('product_disc', true));
        $product_qty = numberClean($this->input->post('product_qty', true));
        $product_qty_alert = numberClean($this->input->post('product_qty_alert'));
        $product_desc = $this->input->post('product_desc', true);
        $image = $this->input->post('image');
        $unit = $this->input->post('unit', true);
        $barcode = $this->input->post('barcode');
        $v_type = $this->input->post('v_type');
        $v_stock = $this->input->post('v_stock');
        $v_alert = $this->input->post('v_alert');
        $w_type = $this->input->post('w_type');
        $w_stock = $this->input->post('w_stock');
        $w_alert = $this->input->post('w_alert');
        $wdate = datefordatabase($this->input->post('wdate'));
        $code_type = $this->input->post('code_type');
        $sub_cat = $this->input->post('sub_cat');
        $brand = $this->input->post('brand');
        $serial = $this->input->post('product_serial');
        if ($catid) {
            $this->products->addnew($catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $v_type, $v_stock, $v_alert, $wdate, $code_type, $w_type, $w_stock, $w_alert, $sub_cat, $brand, $serial);
        }
    }

    public function delete_i()
    {
        if ($this->aauth->premission(11)) {
            $id = $this->input->post('deleteid');
            if ($id) {
                $this->db->delete('geopos_products', array('pid' => $id));
                $this->db->delete('geopos_products', array('sub' => $id, 'merge' => 1));
                $this->db->delete('geopos_movers', array('d_type' => 1, 'rid1' => $id));
                $this->db->set('merge', 0);
                $this->db->where('sub', $id);
                $this->db->update('geopos_products');
                echo json_encode(array('status' => 'Success', 'message' => $this->lang->line('DELETED')));
            } else {
                echo json_encode(array('status' => 'Error', 'message' => $this->lang->line('ERROR')));
            }
        } else {
            echo json_encode(array('status' => 'Error', 'message' =>
                $this->lang->line('ERROR')));
        }
    }

    public function edit($id='')
    {
		
		
		$data['cat'] = $this->categories_model->category_list();
        $data['units'] = $this->products->units();
        $data['warehouse'] = $this->categories_model->warehouse_list();
        $data['custom_fields'] = $this->custom->add_fields(4);
        $this->load->model('units_model', 'units');
        $data['variables'] = $this->units->variables_list();
		$data['livestock'] = $this->getlivestock($id);
		
        $head['title'] = "Add Product";
        $head['usernm'] = $this->aauth->get_user()->username;
    $data['barcode_data'] = $this->db->get_where('product_barcode_info', ['product_id' => $id])->result_array();
        $data['taxes'] = fetch_details('taxes', null, '*');
        $data['countries'] = fetch_details('countries', null, 'name,id');
        $data['sellers'] = $this->db->select(' u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id  ')
            ->join('users_groups ug', ' ug.user_id = u.id ')
            ->join('seller_data sd', ' sd.user_id = u.id ')
            ->where(['ug.group_id' => '4'])
            ->get('users u')->result_array();
       // if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
            $data['title'] = 'Update Product | ';
            $data['meta_description'] = 'Update Product | ';
            $product_details = fetch_details('products', ['id' => $id], '*');
            $countries = fetch_details('countries', ['name' => $product_details[0]['made_in']], 'name');
            if (!empty($product_details)) {
                $data['product_details'] = $product_details;
                $data['product_variants'] = get_variants_values_by_pid($id);
				
			//	print_r($data['product_variants']);
                $product_attributes = fetch_details('product_attributes', ['product_id' => $id]);
                if (!empty($product_attributes) && !empty($product_details)) {
                    $data['product_attributes'] = $product_attributes;
                }
            }
			
			    $attributes = $this->db->select('attr_val.id,attr.name as attr_name ,attr_set.name as attr_set_name,attr_val.value')
                ->join('attributes attr', 'attr.id=attr_val.attribute_id')
                ->join('attribute_set attr_set', 'attr_set.id=attr.attribute_set_id')
                ->where(['attr.status' => 1, 'attr_set.status' => 1])
                ->get('attribute_values attr_val')->result_array();

            $attributes_refind = array();

            for ($i = 0; $i < count($attributes); $i++) {
                if (!array_key_exists($attributes[$i]['attr_set_name'], $attributes_refind)) {
                    $attributes_refind[$attributes[$i]['attr_set_name']] = array();
                    for ($j = 0; $j < count($attributes); $j++) {
                        if ($attributes[$i]['attr_set_name'] == $attributes[$j]['attr_set_name']) {
                            if (!array_key_exists($attributes[$j]['attr_name'], $attributes_refind[$attributes[$i]['attr_set_name']])) {
                                $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array();
                            }
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['id'] = $attributes[$j]['id'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['text'] = $attributes[$j]['value'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']][$j]['data-values'] = $attributes[$j]['value'];
                            $attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']] = array_values($attributes_refind[$attributes[$i]['attr_set_name']][$attributes[$j]['attr_name']]);
                        }
                    }
                }
            }
			
			
			
			$product_variants = $this->db->query("
    SELECT id, product_id, print_name, product_article, attribute_value_ids 
    FROM product_variants 
    WHERE status =1 AND product_id = ?", [$id])->result_array();


$attr_values = [];
if (!empty($product_variants)) {
    $all_attr_ids = [];
    foreach ($product_variants as $pv) {
        if (!empty($pv['attribute_value_ids'])) {
            $ids = explode(',', $pv['attribute_value_ids']);
            $all_attr_ids = array_merge($all_attr_ids, $ids);
        }
    }
    $all_attr_ids = array_unique($all_attr_ids);
    if (!empty($all_attr_ids)) {
        $in = implode(",", array_map('intval', $all_attr_ids));
        $attr_values = $this->db->query("
            SELECT id, value 
            FROM attribute_values 
            WHERE id IN ($in) AND status=1
        ")->result_array();
    }
}


            $data['attr_values'] = $attr_values;
            $data['categories'] = $this->category_model->get_categories();
            $data['attributes_refind'] = $attributes_refind;
   // }

    $this->load->view('fixed/header', $head);
        $this->load->view('products/product-add', $data);
        $this->load->view('fixed/footer'); 
		
     

    }

    public function editproduct()
    {
        if (!$this->aauth->premission(14)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $pid = $this->input->post('pid');
        $product_name = $this->input->post('product_name', true);
        $catid = $this->input->post('product_cat');
        $warehouse = $this->input->post('product_warehouse');
        $product_code = $this->input->post('product_code');
        $product_price = numberClean($this->input->post('product_price'));
        $factoryprice = numberClean($this->input->post('fproduct_price'));
        $taxrate = numberClean($this->input->post('product_tax'));
        $disrate = numberClean($this->input->post('product_disc'));
        $product_qty = numberClean($this->input->post('product_qty'));
        $product_qty_alert = numberClean($this->input->post('product_qty_alert'));
        $product_desc = $this->input->post('product_desc', true);
        $image = $this->input->post('image');
        $unit = $this->input->post('unit');
        $barcode = $this->input->post('barcode');
        $code_type = $this->input->post('code_type');
        $sub_cat = $this->input->post('sub_cat');
        if (!$sub_cat) $sub_cat = 0;
        $brand = $this->input->post('brand');
        $vari = array();
        $vari['v_type'] = $this->input->post('v_type');
        $vari['v_stock'] = $this->input->post('v_stock');
        $vari['v_alert'] = $this->input->post('v_alert');
        $vari['w_type'] = $this->input->post('w_type');
        $vari['w_stock'] = $this->input->post('w_stock');
        $vari['w_alert'] = $this->input->post('w_alert');
        $serial = array();
        $serial['new'] = $this->input->post('product_serial');
        $serial['old'] = $this->input->post('product_serial_e');
        if ($pid) {
            $this->products->edit($pid, $catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $code_type, $sub_cat, $brand, $vari, $serial);
        }
    }


    /* public function warehouseproduct_list()
    {
        $catid = $this->input->get('id');
        $list = $this->products->get_datatables($catid, true);
        $data = array();
        $no = $this->input->post('start');
        foreach ($list as $prd) {
            $no++;
            $row = array();
            $row[] = $no;
            $pid = $prd->id;
            $row[] = $prd->name;
            $row[] = $prd->stock;
            $row[] = $prd->sku;
            $row[] = $prd->c_title;
            $row[] = amountExchange($prd->product_price, 0, $this->aauth->get_user()->loc);
            $row[] = amountExchange($prd->purchase_price, 0, $this->aauth->get_user()->loc);
           $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . '</a> 
          <a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a> 
          <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a> 
          <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $prd->stock . ')" data-object-id="' . $pid . '" class="btn btn-success btn-sm  update-object">Updated</a> 
          <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $prd->stock . ')" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  update-object">Unupdated</a>';

            $data[] = $row;
        }
  

		$output = array(
           
            "total" => $this->products->count_all($catid, true),
            "rows" => $data,
        );
        echo json_encode($output);
    } */

/* public function warehouseproduct_list()
{
    $catid = $this->input->get('id');
    $list = $this->products->get_datatables($catid);
    $data = array();
    $no = $this->input->post('start');
$today = date('Y-m-d');
    foreach ($list as $prd) {
        $no++;
		$pid = $prd->id;
		$livestock = $this->getlivestock($prd->id);
		
		$livestockval = $livestock * $prd->purchase_price;
        $row = array();
        $row[] = $no;
        $row[] = $prd->name;
		
		if($livestock==0){
			   $row[] = "";
			
		}else{
			
			
        $row[] = $livestock;
		}
		if($livestockval==0){
			$row[] ="";
			
		}else{
			
        $row[] = number_format($livestock * $prd->purchase_price, 2);
			
		}
        $row[] = $prd->sku;
        $row[] = $prd->c_title;
        $row[] = number_format($prd->product_price, 2); 
        $row[] = number_format($this->getlastbillprice($prd->id), 2);
        $row[] = number_format($prd->purchase_price, 2);
         if (!empty($prd->updated_date) && date('Y-m-d', strtotime($prd->updated_date)) === $today) {
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>  
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>   
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-success btn-sm  update-object">Updated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        } else {
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>   
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>  
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  update-object">Unupdated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        }
        $data[] = $row;
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->products->count_all($catid),
        "recordsFiltered" => $this->products->count_filtered($catid),
        "data" => $data
    );
    echo json_encode($output);
} */


public function warehouseproduct_list()
{
    $catid = $this->input->get('id');
    $list = $this->products->get_datatables($catid);
    $data = array();
    $no = $this->input->post('start');
    $today = date('Y-m-d');
	 $from_date = date('Y-m-01');
$start_date = $this->input->post('start_date');
$end_date   = $this->input->post('end_date');

    foreach ($list as $prd) {
        $pid = $prd->id;
		if ($start_date && $end_date) {
        $livestock = $this->getlivestock($prd->id, $start_date, $end_date);
}else{
	
	
	   $livestock = $this->getlivestock($prd->id, $from_date, $today);
}
        // Skip product if livestock is exactly 0
        if ($livestock == 0) {
            continue;
        }

        $livestockval = $livestock * $prd->purchase_price;
        $no++;
        $row = array();
        $row[] = $no;

        // If livestock is negative, show name and value with minus sign
        $row[] = $prd->name;
        $row[] = $livestock;

        // Show value only if not zero (already filtered above)
        $row[] = number_format($livestockval, 2);

        $row[] = $prd->sku;
        $row[] = $prd->c_title;
        $row[] = number_format($prd->product_price, 2); 
        $row[] = number_format($this->getlastbillprice($prd->id), 2);
        $row[] = number_format($prd->purchase_price, 2);

        if (!empty($prd->updated_date) && date('Y-m-d', strtotime($prd->updated_date)) === $today) {
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>  
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>   
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-success btn-sm  update-object">Updated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        } else {
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>   
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>  
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  update-object">Unupdated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        }

        $data[] = $row;
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->products->count_all($catid),
        "recordsFiltered" => $this->products->count_filtered($catid),
        "data" => $data
    );
    echo json_encode($output);
}

public function getlastbillprice($product_id){
	
		$this->db->select('sell_rate');
        $this->db->from('product_ledger');
        $this->db->where('product_id', $product_id);
        $this->db->where('ledger_type', 'Sell');
		$this->db->order_by('id', 'DESC');
		$this->db->limit('1');
        $latdata = $this->db->get()->row_array();
		

        return $latdata['sell_rate'];

	
}
  public function get_current_stock($product_id) {
	   $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
        $this->db->select('product_id, product_name, SUM(purchage_qty) - SUM(sell_qty) AS current_stock');
        $this->db->from('product_ledger');
        $this->db->where('product_id', $product_id);
        $this->db->group_by('product_id');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->current_stock;
        } else {
            return 0; // No entry found, stock is zero
        }
    }


    public function prd_stats()
    {
       // $this->products->prd_stats();
    }

    public function stock_transfer_products()
    {
        $wid = $this->input->get('wid');
        $customer = $this->input->post('product');
        $terms = @$customer['term'];
        $result = $this->products->products_list($wid, $terms);
	
        echo json_encode($result);
    }  

	public function products_printlist()
    {
        $wid = $this->input->get('wid');
        $customer = $this->input->post('product');
        $terms = @$customer['term'];
        $result = $this->products->productsprint_list($wid, $terms);
	
        echo json_encode($result);
    }

    public function sub_cat()
    {
        $wid = $this->input->get('id');
        $string = $this->input->post('product');


        if(isset($string['term'])) $this->db->like('title', $string['term']);
        $this->db->from('geopos_product_cat');
        $this->db->where('rel_id', $wid);
        $this->db->where('c_type', 1);
        $query = $this->db->get();
        $result = $query->result_array();


        echo json_encode($result);
    }

    public function stock_transfer()
    {
        if ($this->input->post()) {
            $products_l = $this->input->post('products_l');
            $from_warehouse = $this->input->post('from_warehouse');
            $to_warehouse = $this->input->post('to_warehouse');
            $qty = $this->input->post('products_qty');
            $this->products->transfer($from_warehouse, $products_l, $to_warehouse, $qty);
        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Stock Transfer";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/stock_transfer', $data);
            $this->load->view('fixed/footer');
        }
    }


    public function file_handling()
    {
        if ($this->input->get('op')) {
            $name = $this->input->get('name');
            if ($this->products->meta_delete($name)) {
                echo json_encode(array('status' => 'Success'));
            }
        } else {
            $id = $this->input->get('id');
            $this->load->library("Uploadhandler_generic", array(
                'accept_file_types' => '/\.(gif|jpe?g|png)$/i', 'upload_dir' => FCPATH . 'userfiles/product/', 'upload_url' => base_url() . 'userfile/product/'
            ));
        }
    }

    public function barcode()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,barcode,code_type');
            $this->db->from('geopos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();
            $data['name'] = $resultz['product_name'];
            $data['code'] = $resultz['barcode'];
            $data['ctype'] = $resultz['code_type'];
            $html = $this->load->view('barcode/view', $data, true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($data['name'] . '_barcode.pdf', 'I');

        }
    }

    public function posbarcode()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,barcode,code_type');
            $this->db->from('geopos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();
            $data['name'] = $resultz['product_name'];
            $data['code'] = $resultz['barcode'];
            $data['ctype'] = $resultz['code_type'];
            $html = $this->load->view('barcode/posbarcode', $data, true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_thermal();
            $pdf->WriteHTML($html);
            $pdf->Output($data['name'] . '_barcode.pdf', 'I');

        }
    }

    public function view_over()
    {
		
		$this->db->trans_start();
       $pid = $this->input->post('id');
        $this->db->select('products.*,geopos_warehouse.title');
        $this->db->from('products');
        $this->db->where('products.id', $pid);
        $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse');
        if ($this->aauth->get_user()->loc) {
            $this->db->group_start();
            $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
            if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
            $this->db->group_end();
        } elseif (!BDATA) {
            $this->db->where('geopos_warehouse.loc', 0);
        }

        $query = $this->db->get();
        $data['product'] = $query->row_array();
		$this->db->trans_complete();

		
		 $this->db->select('product_ledger.*, s.name as sellername, c.username as customername');
        $this->db->join('users c', 'c.id = product_ledger.customer_id', 'left');
        $this->db->join('geopos_supplier s', 's.id = product_ledger.seller_id', 'left');
		$this->db->where('product_ledger.product_id', $pid);
        $query = $this->db->get('product_ledger');
		
        $data['daily_stock'] = $query->result();
        $data['pid'] = $pid;


        $this->load->view('products/view-over', $data);


    }

/* public function filter_over() {
    $from_date = $this->input->post('from_date');
    $to_date = $this->input->post('to_date');
    $pid = $this->input->post('pid');

    if (empty($from_date) || empty($to_date)) {
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
    }

    // Previous entries before from_date
    $this->db->select('*');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $pid);
    $this->db->where('created_date <', $from_date . ' 00:00:00');
    $this->db->order_by('created_date', 'ASC');
    $prev_entries = $this->db->get()->result();
    $prev_sell_amount = 0;
    $prev_purchage_qty = 0;
    $prev_purchage_amount = 0;
    $prev_sell_qty = 0;
    $prev_close_stock = 0;
    $prev_wastage_qty = 0;
    $prev_last_date = '';

    if (!empty($prev_entries)) {
        foreach ($prev_entries as $entry) {
            $prev_purchage_qty += $entry->purchage_qty;
            $prev_sell_amount += $entry->sell_amount;
            $prev_purchage_amount += $entry->purchage_amount;
            $prev_sell_qty += $entry->sell_qty;
			        $prev_wastage_qty += isset($entry->wastage) ? $entry->wastage : 0;
			
        }

        $last_entry = end($prev_entries);
        $prev_close_stock = $last_entry->close_stock;
        $prev_unit = $last_entry->unit;
        $prev_last_date = $last_entry->created_date;
    }
	
	
	 $prev_purchage_rate = 0;
    $this->db->select('purchage_rate');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $pid);
    $this->db->where('created_date <', $from_date . ' 00:00:00');
    $this->db->where('purchage_rate !=', 0);
    $this->db->order_by('created_date', 'DESC');
    $this->db->limit(1);
    $rate_query = $this->db->get();

    if ($rate_query->num_rows() > 0) {
        $prev_purchage_rate = $rate_query->row()->purchage_rate;
    }
	
	

    // Filtered ledger entries
    $this->db->select('product_ledger.*, s.name as sellername, c.username as customername');
    $this->db->from('product_ledger');
    $this->db->join('users c', 'c.id = product_ledger.customer_id', 'left');
    $this->db->join('geopos_supplier s', 's.id = product_ledger.seller_id', 'left');
    $this->db->where('product_ledger.created_date >=', $from_date . ' 00:00:00');
    $this->db->where('product_ledger.created_date <=', $to_date . ' 23:59:59');
    $this->db->where('product_ledger.product_id', $pid);
    $this->db->order_by('product_ledger.created_date', 'ASC');
    $daily_stock = $this->db->get()->result();

    // Purchase price for valuation
    $data['purchaseprice'] = $this->get_purchase_price($pid);
    $prev_close_stock = $prev_purchage_qty - $prev_sell_qty - $prev_wastage_qty;
    $data['prev_sell_amount'] = $prev_sell_amount;
    $data['prev_purchage_amount'] = $prev_purchage_amount;
    //$data['prev_close_stock'] = $prev_close_stock;
    $data['prev_close_stock'] = $prev_close_stock;
    $data['prev_last_date'] = $prev_last_date;
    $data['prev_sell_qty'] = $prev_sell_qty;
    $data['prev_purchage_qty'] = $prev_purchage_qty;
    $data['daily_stock'] = $daily_stock;
    $data['prev_unit'] = $prev_unit;
    $data['prev_purchage_rate'] = $prev_purchage_rate;
    $data['prev_stock_value'] = $prev_purchage_rate* $prev_close_stock;

    $this->load->view('products/filter_over', $data);
}
 */


public function filter_over() {
    $from_date = $this->input->post('from_date');
    $to_date = $this->input->post('to_date');
    $pid = $this->input->post('pid');

    if (empty($from_date) || empty($to_date)) {
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
    }

    // Previous entries before from_date
    $this->db->select('*');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $pid);
    $this->db->where('created_date <', $from_date . ' 00:00:00');
    $this->db->order_by('created_date', 'ASC');
    $prev_entries = $this->db->get()->result();
    $prev_sell_amount = 0;
    $prev_purchage_qty = 0;
    $prev_purchage_amount = 0;
    $prev_sell_qty = 0;
    $prev_close_stock = 0;
    $prev_wastage_qty = 0;
    $prev_last_date = '';
    $prev_unit = '';

    if (!empty($prev_entries)) {
        foreach ($prev_entries as $entry) {
            $prev_purchage_qty += $entry->purchage_qty;
            $prev_sell_amount += $entry->sell_amount;
            $prev_purchage_amount += $entry->purchage_amount;
            $prev_sell_qty += $entry->sell_qty;
            $prev_wastage_qty += isset($entry->wastage) ? $entry->wastage : 0;
        }

        $last_entry = end($prev_entries);
        $prev_close_stock = $last_entry->close_stock;
        $prev_unit = $last_entry->unit;
        $prev_last_date = $last_entry->created_date;
    }

    $prev_purchage_rate = 0;
    $this->db->select('purchage_rate');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $pid);
    $this->db->where('created_date <', $from_date . ' 00:00:00');
    $this->db->where('purchage_rate !=', 0);
    $this->db->order_by('created_date', 'DESC');
    $this->db->limit(1);
    $rate_query = $this->db->get();

    if ($rate_query->num_rows() > 0) {
        $prev_purchage_rate = $rate_query->row()->purchage_rate;
    }

    // Filtered ledger entries
    $this->db->select('product_ledger.*, s.name as sellername, c.username as customername');
    $this->db->from('product_ledger');
    $this->db->join('users c', 'c.id = product_ledger.customer_id', 'left');
    $this->db->join('geopos_supplier s', 's.id = product_ledger.seller_id', 'left');
    $this->db->where('product_ledger.created_date >=', $from_date . ' 00:00:00');
    $this->db->where('product_ledger.created_date <=', $to_date . ' 23:59:59');
    $this->db->where('product_ledger.product_id', $pid);
    $this->db->order_by('product_ledger.created_date', 'ASC');
    $daily_stock = $this->db->get()->result();

    // Purchase price for valuation
    $data['purchaseprice'] = $this->get_purchase_price($pid);

    // recompute prev_close_stock (you already had similar)
    $prev_close_stock = $prev_purchage_qty - $prev_sell_qty - $prev_wastage_qty;

    // --- Compute accepted return stock within date range for this product ---
    // We sum qty and sum(qty * price) for valuation.
    $this->db->select('SUM(ri.qty) AS total_return_qty, SUM(ri.qty * ri.price) AS total_return_value');
    $this->db->from('geopos_stock_r_items ri');
    $this->db->join('geopos_stock_r r', 'r.id = ri.tid', 'inner');
    $this->db->where('ri.pid', $pid);
    $this->db->where('r.status', 'accepted');
    // r.invoicedate is DATE type, use between from_date and to_date
    $this->db->where('r.invoicedate >=', $from_date);
    $this->db->where('r.invoicedate <=', $to_date);
    $return_q = $this->db->get()->row();

    $total_return_qty = 0;
    $total_return_value = 0;
    if ($return_q) {
        $total_return_qty = (float)$return_q->total_return_qty;
        $total_return_value = (float)$return_q->total_return_value;
    }

    // Attach data to view
    $data['prev_sell_amount'] = $prev_sell_amount;
    $data['prev_purchage_amount'] = $prev_purchage_amount;
    $data['prev_close_stock'] = $prev_close_stock;
    $data['prev_last_date'] = $prev_last_date;
    $data['prev_sell_qty'] = $prev_sell_qty;
    $data['prev_purchage_qty'] = $prev_purchage_qty;
    $data['daily_stock'] = $daily_stock;
    $data['prev_unit'] = $prev_unit;
    $data['prev_purchage_rate'] = $prev_purchage_rate;
    $data['prev_stock_value'] = $prev_purchage_rate * $prev_close_stock;

    // Return stock data
    $data['total_return_qty'] = $total_return_qty;
    $data['total_return_value'] = $total_return_value;

    $this->load->view('products/filter_over', $data);
}



 public function get_purchase_price($product_id) {
        $this->db->select('purchase_price');
        $this->db->from('products');
        $this->db->where('id', $product_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->purchase_price;
        } else {
            return null;
        }
    }
	
	
    public function label()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,product_price,product_code,barcode,expiry,code_type');
            $this->db->from('geopos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();

            $html = $this->load->view('barcode/label', array('lab' => $resultz), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');

        }
    }


    public function poslabel()
    {
        $pid = $this->input->get('id');
        if ($pid) {
            $this->db->select('product_name,product_price,product_code,barcode,expiry,code_type');
            $this->db->from('geopos_products');
            //  $this->db->where('warehouse', $warehouse);
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $resultz = $query->row_array();
            $html = $this->load->view('barcode/poslabel', array('lab' => $resultz), true);
            ini_set('memory_limit', '64M');
            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_thermal();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');
        }
    }

    public function report_product()
    {
        $pid = intval($this->input->post('id'));

        $r_type = intval($this->input->post('r_type'));
        $s_date = datefordatabase($this->input->post('s_date'));
        $e_date = datefordatabase($this->input->post('e_date'));

        if ($pid && $r_type) {


            switch ($r_type) {
                case 1 :
                    $query = $this->db->query("SELECT geopos_invoices.tid,geopos_invoice_items.qty,geopos_invoice_items.price,geopos_invoices.invoicedate FROM geopos_invoice_items LEFT JOIN geopos_invoices ON geopos_invoices.id=geopos_invoice_items.tid WHERE geopos_invoice_items.pid='$pid' AND geopos_invoices.status!='canceled' AND (DATE(geopos_invoices.invoicedate) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;

                case 2 :
                    $query = $this->db->query("SELECT geopos_purchase.tid,geopos_purchase_items.qty,geopos_purchase_items.price,geopos_purchase.invoicedate FROM geopos_purchase_items LEFT JOIN geopos_purchase ON geopos_purchase.id=geopos_purchase_items.tid WHERE geopos_purchase_items.pid='$pid' AND geopos_purchase.status!='canceled' AND (DATE(geopos_purchase.invoicedate) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;

                case 3 :
                    $query = $this->db->query("SELECT rid2 AS qty, DATE(d_time) AS  invoicedate,note FROM geopos_movers  WHERE geopos_movers.d_type='1' AND rid1='$pid'  AND (DATE(d_time) BETWEEN DATE('$s_date') AND DATE('$e_date'))");
                    $result = $query->result_array();
                    break;
            }

            $this->db->select('*');
            $this->db->from('geopos_products');
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $product = $query->row_array();

            $cat_ware = $this->categories_model->cat_ware($pid, $this->aauth->get_user()->loc);

//if(!$cat_ware) exit();
            $html = $this->load->view('products/statementpdf-ltr', array('report' => $result, 'product' => $product, 'cat_ware' => $cat_ware, 'r_type' => $r_type), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($pid . 'report.pdf', 'I');
        } else {
            $pid = intval($this->input->get('id'));
            $this->db->select('*');
            $this->db->from('geopos_products');
            $this->db->where('pid', $pid);
            $query = $this->db->get();
            $product = $query->row_array();
            $head['title'] = "Product Sales";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/statement', array('id' => $pid, 'product' => $product));
            $this->load->view('fixed/footer');
        }
    }

   /*  public function custom_label()
    {
        if ($this->input->post()) {
            require APPPATH . 'third_party/barcode/autoload.php';
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $bar_width = $this->input->post('bar_width');
            $label_width = $this->input->post('label_width');
            $label_height = $this->input->post('label_height');
            $product_name = $this->input->post('product_name');
            $font_size = $this->input->post('font_size');
            $max_char = $this->input->post('max_char');
            $b_type = $this->input->post('b_type');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $products = array();
            if(!$this->input->post('products_l')) exit('No Product Selected!');
            foreach ($this->input->post('products_l') as $row) {
                $this->db->select('geopos_products.product_name,geopos_products.product_price,geopos_products.product_code,geopos_products.barcode,geopos_products.expiry,geopos_products.code_type,geopos_warehouse.title,geopos_warehouse.loc');
                $this->db->from('geopos_products');
                $this->db->join('geopos_warehouse', 'geopos_warehouse.id = geopos_products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('geopos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('geopos_products.pid', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);


            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows, 'bar_width' => $bar_width, 'label_width' => $label_width, 'label_height' => $label_height, 'product_name' => $product_name, 'font_size' => $font_size, 'max_char' => $max_char, 'b_type' => $b_type);


            $this->load->view('barcode/custom_label', array('products' => $products, 'style' => $design));

          

        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Custom Label";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/custom_label', $data);
            $this->load->view('fixed/footer');
        }
    } */
	
	
	
	public function custom_label()
{
    if ($this->input->post()) {
        require APPPATH . 'third_party/barcode/autoload.php';

        // Collect POST inputs
        $width = $this->input->post('width');
        $height = $this->input->post('height');
        $padding = $this->input->post('padding');
        $store_name = $this->input->post('store_name');
        $warehouse_name = $this->input->post('warehouse_name');
        $product_price = $this->input->post('product_price');
        $product_code = $this->input->post('product_code');
        $bar_height = $this->input->post('bar_height');
        $bar_width = $this->input->post('bar_width');
        $label_width = $this->input->post('label_width');
        $label_height = $this->input->post('label_height');
        $product_name = $this->input->post('product_name');
        $font_size = $this->input->post('font_size');
        $max_char = $this->input->post('max_char');
        $b_type = $this->input->post('b_type');
        $total_rows = $this->input->post('total_rows');
        $items_per_rows = $this->input->post('items_per_row');

        if (!$this->input->post('products_l')) exit('No Product Selected!');

        $products = array();
        foreach ($this->input->post('products_l') as $row) {
            $this->db->select('products.name, products.product_price, products.barcode, products.code_type, products.warehouse, geopos_warehouse.title, geopos_warehouse.loc');
            $this->db->from('products');
            $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse', 'left');

            if ($this->aauth->get_user()->loc) {
                $this->db->group_start();
                $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);
                if (defined('BDATA') && BDATA) $this->db->or_where('warehouse.loc', 0);
                $this->db->group_end();
            } elseif (!defined('BDATA') || !BDATA) {
                $this->db->where('geopos_warehouse.loc', 0);
            }

            $this->db->where('products.id', $row);
            $query = $this->db->get();
            $resultz = $query->row_array();

            if (!empty($resultz)) {
                $products[] = $resultz;
            }
        }

        $loc = location($resultz['loc']); // Get location info from location helper/function

        $design = array(
            'store' => $loc['cname'],
            'warehouse' => $resultz['title'],
            'width' => $width,
            'height' => $height,
            'padding' => $padding,
            'store_name' => $store_name,
            'warehouse_name' => $warehouse_name,
            'product_price' => $product_price,
            'product_code' => $product_code,
            'bar_height' => $bar_height,
            'total_rows' => $total_rows,
            'items_per_row' => $items_per_rows,
            'bar_width' => $bar_width,
            'label_width' => $label_width,
            'label_height' => $label_height,
            'product_name' => $product_name,
            'font_size' => $font_size,
            'max_char' => $max_char,
            'b_type' => $b_type
        );

        $this->load->view('barcode/custom_label', array('products' => $products, 'style' => $design));

    } else {
        $data['cat'] = $this->categories_model->category_list();
        $data['warehouse'] = $this->categories_model->warehouse_list();
        $head['title'] = "Custom Label";
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('products/custom_label', $data);
        $this->load->view('fixed/footer');
    }
}


    public function custom_label_old()
    {
        if ($this->input->post()) {
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $products = array();


            foreach ($this->input->post('products_l') as $row) {
                $this->db->select('geopos_products.product_name,geopos_products.product_price,geopos_products.product_code,geopos_products.barcode,geopos_products.expiry,geopos_products.code_type,geopos_warehouse.title,geopos_warehouse.loc');
                $this->db->from('geopos_products');
                $this->db->join('geopos_warehouse', 'geopos_warehouse.id = geopos_products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('geopos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('geopos_products.pid', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);

            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows);


            $html = $this->load->view('barcode/custom_label', array('products' => $products, 'style' => $design), true);
            ini_set('memory_limit', '64M');

            //PDF Rendering
            $this->load->library('pdf');
            $pdf = $this->pdf->load_en();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I');


        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Custom Label";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/custom_label', $data);
            $this->load->view('fixed/footer');
        }
    }

    public function standard_label()
    {
        if ($this->input->post()) {
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $standard_label = $this->input->post('standard_label');
            $products = array();


            foreach ($this->input->post('products_l') as $row) {
                $this->db->select('products.name as product_name,products.product_price,products.article as product_code,products.article as barcode,"" as expiry, products.code_type,geopos_warehouse.title,geopos_warehouse.loc');
                $this->db->from('products');
                $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('geopos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('products.id', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);

            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows);

            switch ($standard_label) {
                case 'eu30019' :
				//$this->load->view('standard_label/eu30019', array('products' => $products, 'style' => $design));
                  $html = $this->load->view('standard_label/eu30019', array('products' => $products, 'style' => $design), true);
                    break;
            }


            ini_set('memory_limit', '64M');

            //PDF Rendering
           $this->load->library('pdf');
            $pdf = $this->pdf->load_en();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I'); 


        } else {
            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Stock Transfer";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/standard_label', $data);
            $this->load->view('fixed/footer');
        }
    }
	
	
	
	
	
	  public function thok_label()
    {
        if ($this->input->post()) {
            $width = $this->input->post('width');
            $height = $this->input->post('height');
            $padding = $this->input->post('padding');
            $store_name = $this->input->post('store_name');
            $warehouse_name = $this->input->post('warehouse_name');
            $product_price = $this->input->post('product_price');
            $product_code = $this->input->post('product_code');
            $bar_height = $this->input->post('bar_height');
            $total_rows = $this->input->post('total_rows');
            $items_per_rows = $this->input->post('items_per_row');
            $standard_label = $this->input->post('standard_label');
            $products = array();


            foreach ($this->input->post('products_l') as $row) {
                $this->db->select('products.name as product_name,products.product_price,products.article as product_code,products.article as barcode,"" as expiry, products.code_type,geopos_warehouse.title,geopos_warehouse.loc');
                $this->db->from('products');
                $this->db->join('geopos_warehouse', 'geopos_warehouse.id = products.warehouse', 'left');

                if ($this->aauth->get_user()->loc) {
                    $this->db->group_start();
                    $this->db->where('geopos_warehouse.loc', $this->aauth->get_user()->loc);

                    if (BDATA) $this->db->or_where('geopos_warehouse.loc', 0);
                    $this->db->group_end();
                } elseif (!BDATA) {
                    $this->db->where('geopos_warehouse.loc', 0);
                }

                //  $this->db->where('warehouse', $warehouse);
                $this->db->where('products.id', $row);
                $query = $this->db->get();
                $resultz = $query->row_array();

                $products[] = $resultz;

            }


            $loc = location($resultz['loc']);

            $design = array('store' => $loc['cname'], 'warehouse' => $resultz['title'], 'width' => $width, 'height' => $height, 'padding' => $padding, 'store_name' => $store_name, 'warehouse_name' => $warehouse_name, 'product_price' => $product_price, 'product_code' => $product_code, 'bar_height' => $bar_height, 'total_rows' => $total_rows, 'items_per_row' => $items_per_rows);

            switch ($standard_label) {
                case 'eu30019' :
				//$this->load->view('standard_label/eu30019', array('products' => $products, 'style' => $design));
                  $html = $this->load->view('standard_label/eu30019', array('products' => $products, 'style' => $design), true);
                    break;
            }


            ini_set('memory_limit', '64M');

            //PDF Rendering
           $this->load->library('pdf');
            $pdf = $this->pdf->load_en();
            $pdf->WriteHTML($html);
            $pdf->Output($resultz['product_name'] . '_label.pdf', 'I'); 


        } else {
			
			// $data['warehouse'] = $this->db->get('warehouse')->result_array();
 // $data['warehouse'] = $this->db->get('warehouse')->result_array();
    $products = $this->db->get('products')->result_array();
    $data['all_products'] = $products;

    $all_variant_data = [];

    foreach ($products as $p) {
        $this->db->where('product_id', $p['id']);
        $variants = $this->db->get('product_variants')->result_array();

        $grouped = [];

        foreach ($variants as $v) {
            $print_name = $v['print_name'];

            // Get attribute values (like 1KG, 500GM)
            $variant_values = [];
            if (!empty($v['attribute_value_ids'])) {
                $ids = explode(',', $v['attribute_value_ids']);
                $this->db->where_in('id', $ids);
                $vals = $this->db->get('attribute_values')->result_array();
                foreach ($vals as $val) {
                    $variant_values[] = $val['value'];
                }
            }

            // Get article no
            $article_row = $this->db->get_where('product_barcode_info', [
                'product_id' => $p['id'],
                'print_name' => $print_name
            ])->row_array();

            $article_no = $article_row ? $article_row['article_no'] : '';

            $grouped[$print_name][] = [
                'variant_id' => $v['id'],
                'variant_values' => $variant_values,
                'article_no' => $article_no
            ];
        }

        $all_variant_data[$p['id']] = $grouped;
    }

    $data['all_variant_data'] = $all_variant_data;


            $data['cat'] = $this->categories_model->category_list();
            $data['warehouse'] = $this->categories_model->warehouse_list();
            $head['title'] = "Stock Transfer";
            $head['usernm'] = $this->aauth->get_user()->username;
            $this->load->view('fixed/header', $head);
            $this->load->view('products/thok_label', $data);
            $this->load->view('fixed/footer');
        }
    }
	
	public function update_product()
{
    $this->load->model('Products_model'); // Load your model for database operations

    // Get form data
    $product_id = $this->input->post('product_id');
    $sell_price = $this->input->post('sell_price');
    $purchase_price = $this->input->post('purchase_price');
    $wastage = $this->input->post('wastage');
    $stock = $this->input->post('stock');
    $updated_date = date('Y-m-d H:i:s');

    // Fetch product details from the `products` table
    $product = $this->Products_model->get_product($product_id);
    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        return;
    }

    // Fetch the first variant with status 1
    $variant = $this->Products_model->get_first_variant($product_id);
    if (!$variant) {
        echo json_encode(['status' => 'error', 'message' => 'No active variant found.']);
        return;
    }

    // Extract unit from attribute_values
    $unit = $this->Products_model->get_unit_from_attribute($variant['attribute_value_ids']);

    // Update `products` table

    // Prepare data for `product_variants` table
    $variant_data = [
        'stock' => $stock-$wastage,
        'purchase_price' => $purchase_price,
       
    ];
	
	  $product_data = [
        'product_price' => $calculated_price,
        'purchase_price' => $purchase_price,
        'fproduct_price' => $purchase_price,
        'stock' => $stock-$wastage,
        'updated_date' => $updated_date,
    ]; 


    // Check if purchase price has changed but price has not
    if ($product['product_price'] == $sell_price && $variant['purchase_price'] != $purchase_price) {
        if ($variant['margin_type'] === 'Fixed') {
            $calculated_price = $purchase_price + $variant['margin_percent'];
        } elseif ($variant['margin_type'] === 'Percentage') {
            $calculated_price = $purchase_price + ($purchase_price * $variant['margin_percent'] / 100);
        }

        $variant_data['price'] = $calculated_price;
        $variant_data['special_price'] = $calculated_price;
        $product_data['product_price'] = $calculated_price;
		$sell_rate = $calculated_price;
    }else{
		$sell_rate = $sell_price;
		
	$product_data['product_price'] = $sell_price;
	}

  
    $this->Products_model->update_product($product_id, $product_data);


  
    $this->Products_model->update_variant($variant['id'], $variant_data);

   $ledger_type = (!empty($wastage) && floatval($wastage) != 0) ? 'Wastage' : 'Updated by ' . $this->aauth->get_user()->username;

$ledger_data = [
    'product_id' => $product_id,
    'product_variants' => $variant['id'],
    'product_name' => $product['name'], 
    'unit' => $unit, 
    'purchage_rate' => $purchase_price,
    'sell_rate' => $sell_rate,
    'open_stock' => $product['stock'], 
    'close_stock' => $stock-$wastage,
    'wastage' => $wastage,
    'created_date' => date('Y-m-d H:i:s'),
    'ledger_type' => $ledger_type,
    'created_by' => 1, 
];
$this->Products_model->insert_ledger($ledger_data);

	$wastage_data = [
        'product_id' => $product_id,
        'product_name' => $product['name'],
        'unit' => $unit,
        'qty' => $wastage,
        'sell_rate' => $sell_rate,
        'purchase_rate' => $purchase_price,
        'wastage_reson' => 'wastage',
        'created_date' => $updated_date,
        'created_by' => 1,
    ];

$this->db->insert('wastage', $wastage_data);


    echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
}



public function getlivestock($pid){
	
	//$pid = $_GET['pid'];

	$from_date="";
	$to_date ="";
	$balance = $this->products->get_total_livebalance($pid, $from_date, $to_date);
	//print_r($balance);
	return round($balance['total_balance']-$balance['total_return_value'],2);
	//return round($balance,0);
}

public function getteststock(){
	
	echo $pid = $_GET['pid'];

	$from_date="";
	$to_date ="";
	$balance = $this->products->get_total_balance($pid, $from_date, $to_date);
	print_r($balance);
//	return round($balance['total_balance'],2);
	//return round($balance,0);
}

public function get_sell_report()
{
    $product_id = $this->input->post('product_id');
    $from_date = $this->input->post('from_date');
    $to_date = $this->input->post('to_date');

    $is_filtered = !empty($from_date) && !empty($to_date);
    if (!$is_filtered) {
        $from_date = date('Y-m-01');
        $to_date = date('Y-m-t');
    }

    $data = $this->products->get_combined_ledger($product_id, $from_date, $to_date);
    $output = '';
    $sr = 1;
    $total_debit = 0;
    $total_credit = 0;

    $opening_balance = $this->products->get_opening_balance($product_id, $from_date);
    $output .= '<tr style="font-weight:bold; background:#ffffdd;">
        <td colspan="5">Opening Balance (Before ' . date('d-m-Y', strtotime($from_date)) . ')</td>
        <td>' . number_format($opening_balance, 2) . '</td>
        <td></td>
    </tr>';

    $running_balance = $opening_balance;

    foreach ($data as $row) {
        $debit = '-';
        $credit = '-';

        if (strtolower($row->ledger_type) === 'sell') {
            $debit = number_format($row->qty, 2);
            $running_balance -= $row->qty;
            $total_debit += $row->qty;
        }

        if (strtolower($row->ledger_type) === 'purchase') {
            $credit = number_format($row->purchage_qty, 2);
            $running_balance += $row->purchage_qty;
            $total_credit += $row->purchage_qty;
        }

        $output .= '<tr>
            <td>' . $sr++ . '</td>
            <td>' . date('d-m-Y', strtotime($row->created_date)) . '</td>
            <td>' . ucfirst($row->ledger_type) . '</td>
            <td>' . $debit . '</td>
            <td>' . $credit . '</td>
            <td>' . number_format($running_balance, 2) . '</td>
            <td>' . $row->order_id . '</td>
        </tr>';
    }

    $output .= '<tr style="font-weight:bold; background:#f2f2f2;">
        <td colspan="3" class="text-right">Total</td>
        <td>' . number_format($total_debit, 2) . '</td>
        <td>' . number_format($total_credit, 2) . '</td>
        <td colspan="2"></td>
    </tr>';

    $output .= '<tr style="font-weight:bold; background:#f2f2f2;">
        <td colspan="3" class="text-right">Balance</td>
        <td>' . number_format($total_credit - $total_debit, 2) . '</td>
        <td></td>
        <td colspan="2"></td>
    </tr>';

    echo $output;
}




/* public function save_barcode_rows() {
    $product_id = $this->input->post('products_id');
    $row_ids = $this->input->post('row_id');
    $printnames = $this->input->post('printname');
    $articles = $this->input->post('article');
    $barcodes = $this->input->post('barcode');

    for ($i = 0; $i < count($printnames); $i++) {
        $data = [
            'product_id' => $product_id,
            'print_name' => $printnames[$i],
            'article_no' => $articles[$i],
            'UOM' => $barcodes[$i]
        ];

        if (!empty($row_ids[$i]) && $row_ids[$i] != 0) {
            // UPDATE existing row
            $this->db->where('id', $row_ids[$i]);
            $this->db->update('product_barcode_info', $data);
        } else {
            // INSERT new row
            $this->db->insert('product_barcode_info', $data);
        }
    }

    echo json_encode(['status' => 'success']);
} */

public function save_barcode_rows() {
    $product_id = $this->input->post('products_id');
    $row_ids     = $this->input->post('row_id');
    $printnames  = $this->input->post('printname');
    $articles    = $this->input->post('article');
    $uom    = $this->input->post('uom');
    $hpnumber    = $this->input->post('hpnumber');
   // $barcodes    = $this->input->post('barcode');
    $deleted_ids = $this->input->post('deleted_ids');

    if (empty($product_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID missing']);
        return;
    }

    // Decode deleted IDs from JSON
    $deleted_ids = !empty($deleted_ids) ? json_decode($deleted_ids, true) : [];

    // Insert or Update rows
    for ($i = 0; $i < count($printnames); $i++) {
        $data = [
            'product_id' => $product_id,
            'print_name' => $printnames[$i],
            'article_no' => $articles[$i],
            'hpnumber' => $hpnumber[$i],
            'UOM'        => $uom[$i]
        ];

        if (!empty($row_ids[$i]) && $row_ids[$i] != '0') {
            // UPDATE existing row
            $this->db->where('id', $row_ids[$i]);
            $this->db->update('product_barcode_info', $data);
        } else {
            // INSERT new row
            $this->db->insert('product_barcode_info', $data);
        }
    }

    // Delete removed rows from DB
    if (!empty($deleted_ids)) {
        $this->db->where_in('id', $deleted_ids);
        $this->db->delete('product_barcode_info');
    }

    echo json_encode(['status' => 'success']);
}




}
