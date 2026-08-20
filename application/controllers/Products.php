<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library("Aauth");
        if (!is_cli()) {
            if (!$this->aauth->is_loggedin()) {
                redirect('/user/', 'refresh');
            }
            if (!$this->aauth->premission(2)) {
                exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
            }
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

    private function get_available_sellers()
    {
        $is_seller = is_seller_user();

        $query = $this->db->select('u.username as seller_name,u.id as seller_id,sd.category_ids,sd.id as seller_data_id')
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->join('seller_data sd', 'sd.user_id = u.id')
            ->where(['ug.group_id' => '4']);

        if ($is_seller) {
            $query->where('u.id', (int)$this->session->userdata('user_id'));
        }

        return $query->get('users u')->result_array();
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
        $data['sellers'] = $this->get_available_sellers();
        $data['product_variants'] = [];
        $data['barcode_data'] = [];
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
        $this->output->set_content_type('application/json');

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

        if (!$catid) {
            echo json_encode(array('error' => true, 'message' => 'Category is required.'));
            return;
        }

        try {
            $saved = $this->products->addnew($catid, $warehouse, $product_name, $product_code, $product_price, $factoryprice, $taxrate, $disrate, $product_qty, $product_qty_alert, $product_desc, $image, $unit, $barcode, $v_type, $v_stock, $v_alert, $wdate, $code_type, $w_type, $w_stock, $w_alert, $sub_cat, $brand, $serial);

            if ($saved) {
                echo json_encode(array('error' => false, 'message' => 'Product saved successfully.'));
                return;
            }

            $db_error = $this->db->error();
            $message = (!empty($db_error['message'])) ? $db_error['message'] : 'Unable to save product.';

            $this->output->set_status_header(500);
            echo json_encode(array('error' => true, 'message' => $message));
        } catch (Exception $e) {
            log_message('error', 'Product add failed: ' . $e->getMessage());
            $this->output->set_status_header(500);
            echo json_encode(array('error' => true, 'message' => $e->getMessage()));
        }
    }

    public function add_product()
    {
        $this->output->set_content_type('application/json');
        $this->load->library('form_validation');

        $response = array(
            'error' => true,
            'message' => 'Unable to save product.'
        );

        if (isset($_POST['edit_product_id'])) {
            if (function_exists('print_msg') && function_exists('has_permissions') && print_msg(!has_permissions('update', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                return false;
            }
        } else {
            if (function_exists('print_msg') && function_exists('has_permissions') && print_msg(!has_permissions('create', 'product'), PERMISSION_ERROR_MSG, 'product')) {
                return false;
            }
        }

        $deliverable_type = $this->input->post('deliverable_type');
        $video_type = $this->input->post('video_type');
        $product_type = $this->input->post('product_type');
        $variant_stock_status = $this->input->post('variant_stock_status');
        $variant_stock_level_type = $this->input->post('variant_stock_level_type');

        if (is_seller_user()) {
            $_POST['seller_id'] = (int)$this->session->userdata('user_id');
        }

        $this->form_validation->set_rules('pro_input_name', 'Product Name', 'trim|required');
        $this->form_validation->set_rules('purchaseprice', 'Product Purchase Price', 'trim|required');
        $this->form_validation->set_rules('short_description', 'Short Description', 'trim|required');
        $this->form_validation->set_rules('category_id', 'Category Id', 'trim|required', array('required' => 'Category is required'));
        $this->form_validation->set_rules('pro_input_tax', 'Tax', 'trim');
        $this->form_validation->set_rules('pro_input_image', 'Image', 'trim|required', array('required' => 'Image is required'));
        $this->form_validation->set_rules('made_in', 'Made In', 'trim');
        $this->form_validation->set_rules('product_type', 'Product type', 'trim|required');
        $this->form_validation->set_rules('seller_id', 'Seller', 'trim|required');
        $this->form_validation->set_rules('total_allowed_quantity', 'Total Allowed Quantity', 'trim');
        $this->form_validation->set_rules('minimum_order_quantity', 'Minimum Order Quantity', 'trim');
        $this->form_validation->set_rules('quantity_step_size', 'Quantity Step Size', 'trim');
        $this->form_validation->set_rules('warranty_period', 'Warranty Period', 'trim');
        $this->form_validation->set_rules('guarantee_period', 'Guarantee Period', 'trim');
        $this->form_validation->set_rules('video', 'Video', 'trim');
        $this->form_validation->set_rules('video_type', 'Video Type', 'trim');
        $this->form_validation->set_rules('deliverable_type', 'Deliverable Type', 'required|trim');

        if (!empty($video_type)) {
            if ($video_type == 'youtube' || $video_type == 'vimeo') {
                $this->form_validation->set_rules('video', 'Video link', 'trim|required', array('required' => ' Please paste a %s in the input box. '));
            } else {
                $this->form_validation->set_rules('pro_input_video', 'Video file', 'trim|required', array('required' => ' Please choose a %s to be set. '));
            }
        }

        if (!empty($_POST['tags'])) {
            $_POST['tags'] = json_decode($_POST['tags'], true);
            if (is_array($_POST['tags'])) {
                $tags = array_column($_POST['tags'], 'value');
                $_POST['tags'] = implode(',', $tags);
            }
        }

        if ($this->input->post('is_cancelable') == '1') {
            $this->form_validation->set_rules('cancelable_till', 'Till which status', 'trim|required');
        }
        if (isset($_POST['cod_allowed'])) {
            $this->form_validation->set_rules('cod_allowed', 'COD allowed', 'trim');
        }
        if (isset($_POST['is_prices_inclusive_tax'])) {
            $this->form_validation->set_rules('is_prices_inclusive_tax', 'Tax included in prices', 'trim');
        }
        if ($deliverable_type == INCLUDED || $deliverable_type == EXCLUDED) {
            $this->form_validation->set_rules('deliverable_zipcodes[]', 'Deliverable Zipcodes', 'trim|required');
        }

        if ($product_type == 'simple_product') {
            $simple_price = $this->input->post('simple_price');
            $simple_special_price = $this->input->post('simple_special_price');

            $this->form_validation->set_rules('simple_price', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $simple_special_price . ']');
            $this->form_validation->set_rules('simple_special_price', 'Special Price', 'trim|numeric|less_than_equal_to[' . $simple_price . ']');
            $this->form_validation->set_rules('purchaseprice', 'Purchase Price', 'required');
            $this->form_validation->set_rules('margin_percent', 'Margin Price', 'required');
            $this->form_validation->set_rules('disc_percent', 'Discount Percentage', 'required');

            if (isset($_POST['simple_product_stock_status']) && in_array($_POST['simple_product_stock_status'], array('0', '1'))) {
                $this->form_validation->set_rules('product_sku', 'SKU', 'trim');
                $this->form_validation->set_rules('product_total_stock', 'Total Stock', 'trim|required|numeric');
                $this->form_validation->set_rules('simple_product_stock_status', 'Stock Status', 'trim|required|numeric');
            }
        } elseif ($product_type == 'variable_product') {
            if ($variant_stock_status == '0') {
                if ($variant_stock_level_type == 'product_level') {
                    $this->form_validation->set_rules('sku_pro_type', 'SKU', 'trim');
                    $this->form_validation->set_rules('total_stock_variant_type', 'Total Stock', 'trim|required');
                    $this->form_validation->set_rules('variant_stock_status', 'Stock Status', 'trim|required');

                    if (isset($_POST['variant_price']) && isset($_POST['variant_special_price']) && is_array($_POST['variant_price']) && is_array($_POST['variant_special_price'])) {
                        foreach ($_POST['variant_price'] as $key => $value) {
                            $variant_special_price = isset($_POST['variant_special_price'][$key]) ? $_POST['variant_special_price'][$key] : '';
                            $this->form_validation->set_rules('purchase_price[' . $key . ']', 'Purchase Price', 'trim|required|numeric');
                            $this->form_validation->set_rules('margin_percent[' . $key . ']', 'Margin Percentage', 'trim|required|numeric');
                            $this->form_validation->set_rules('disc_percent[' . $key . ']', 'Discount Percentage', 'trim|required|numeric');
                            $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $variant_special_price . ']');
                            $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|less_than_equal_to[' . $value . ']');
                        }
                    } else {
                        $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                        $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                    }
                } else {
                    if (isset($_POST['variant_price']) && isset($_POST['variant_special_price']) && isset($_POST['variant_sku']) && isset($_POST['variant_total_stock']) && is_array($_POST['variant_price']) && is_array($_POST['variant_special_price'])) {
                        foreach ($_POST['variant_price'] as $key => $value) {
                            $variant_special_price = isset($_POST['variant_special_price'][$key]) ? $_POST['variant_special_price'][$key] : '';
                            $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $variant_special_price . ']');
                            $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|less_than_equal_to[' . $value . ']');
                            $this->form_validation->set_rules('variant_sku[' . $key . ']', 'SKU', 'trim');
                            $this->form_validation->set_rules('variant_total_stock[' . $key . ']', 'Total Stock', 'trim|required|numeric');
                            $this->form_validation->set_rules('variant_level_stock_status[' . $key . ']', 'Stock Status', 'trim|required|numeric');
                            $this->form_validation->set_rules('purchase_price[' . $key . ']', 'Purchase Price', 'trim|required|numeric');
                            $this->form_validation->set_rules('margin_percent[' . $key . ']', 'Margin Percentage', 'trim|required|numeric');
                            $this->form_validation->set_rules('disc_percent[' . $key . ']', 'Discount Percentage', 'trim|required|numeric');
                        }
                    } else {
                        $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                        $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                        $this->form_validation->set_rules('variant_sku', 'SKU', 'trim');
                        $this->form_validation->set_rules('variant_total_stock', 'Total Stock', 'trim|required|numeric');
                        $this->form_validation->set_rules('variant_level_stock_status', 'Stock Status', 'trim|required|numeric');
                    }
                }
            } else {
                if (isset($_POST['variant_price']) && isset($_POST['variant_special_price']) && is_array($_POST['variant_price']) && is_array($_POST['variant_special_price'])) {
                    foreach ($_POST['variant_price'] as $key => $value) {
                        $variant_special_price = isset($_POST['variant_special_price'][$key]) ? $_POST['variant_special_price'][$key] : '';
                        $this->form_validation->set_rules('variant_price[' . $key . ']', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $variant_special_price . ']');
                        $this->form_validation->set_rules('variant_special_price[' . $key . ']', 'Special Price', 'trim|numeric|less_than_equal_to[' . $value . ']');
                    }
                } else {
                    $this->form_validation->set_rules('variant_price', 'Price', 'trim|required|numeric|greater_than_equal_to[' . $this->input->post('variant_special_price') . ']');
                    $this->form_validation->set_rules('variant_special_price', 'Special Price', 'trim|numeric|less_than_equal_to[' . $this->input->post('variant_price') . ']');
                }
            }
        }

        if (!$this->form_validation->run()) {
            $response['error'] = true;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = validation_errors();
            echo json_encode($response);
            return;
        }

        if (!empty($_POST['deliverable_zipcodes'])) {
            $_POST['zipcodes'] = implode(',', $_POST['deliverable_zipcodes']);
        } else {
            $_POST['zipcodes'] = null;
        }

        try {
            file_put_contents('c:/xampp/htdocs/demostorethokkidukaan_com-main/uploads/post_debug.json', json_encode($_POST, JSON_PRETTY_PRINT));
            $this->product_model->add_product($_POST);
            $response['error'] = false;
            $response['csrfName'] = $this->security->get_csrf_token_name();
            $response['csrfHash'] = $this->security->get_csrf_hash();
            $response['message'] = isset($_POST['edit_product_id']) ? 'Product Updated Successfully' : 'Product Added Successfully';
            if (!isset($_POST['edit_product_id']) || empty($_POST['edit_product_id'])) {
                $response['redirect_url'] = base_url('products');
            }
        } catch (Exception $e) {
            log_message('error', 'Product add failed: ' . $e->getMessage());
            $this->output->set_status_header(500);
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        } catch (Throwable $e) {
            log_message('error', 'Product add failed: ' . $e->getMessage());
            $this->output->set_status_header(500);
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
    }

    public function save_product_update()
    {
        return $this->add_product();
    }

    public function fetch_attributes_by_id()
    {
        $this->output->set_content_type('application/json');

        $edit_id = $this->input->get('edit_id');
        $variants = get_variants_values_by_pid($edit_id);
        $res = array();
        $res['attr_values'] = get_attribute_values_by_pid($edit_id);
        $res['pre_selected_variants_names'] = !empty($variants) ? $variants[0]['attr_name'] : null;
        $res['pre_selected_variants_ids'] = $variants;

        $response = array();
        $response['csrfName'] = $this->security->get_csrf_token_name();
        $response['csrfHash'] = $this->security->get_csrf_hash();
        $response['result'] = $res;

        echo json_encode($response);
    }

    public function fetch_attribute_values_by_id($id = null)
    {
        $this->output->set_content_type('application/json');

        $aid = !empty($id) ? $id : $this->input->get('id');
        $variant_ids = get_attribute_values_by_id($aid);

        echo json_encode($variant_ids);
    }

    public function fetch_variants_values_by_pid()
    {
        $this->output->set_content_type('application/json');

        $edit_id = $this->input->get('edit_id');
        $res = get_variants_values_by_pid($edit_id);
        $response = array();
        $response['result'] = $res;

        echo json_encode($response);
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

    public function delete_product()
    {
        if ($this->aauth->premission(11)) {
            $id = intval($this->input->get('id'));
            if ($id > 0) {
                // Delete product variants
                $this->db->delete('product_variants', array('product_id' => $id));
                // Delete product attributes
                $this->db->delete('product_attributes', array('product_id' => $id));
                // Delete barcode info
                $this->db->delete('product_barcode_info', array('product_id' => $id));
                // Delete product
                $this->db->delete('products', array('id' => $id));

                $response['error'] = false;
                $response['message'] = 'Deleted Successfully';
            } else {
                $response['error'] = true;
                $response['message'] = 'Invalid Product ID';
            }
        } else {
            $response['error'] = true;
            $response['message'] = 'Insufficient Permissions';
        }
        $response['csrfName'] = $this->security->get_csrf_token_name();
        $response['csrfHash'] = $this->security->get_csrf_hash();
        echo json_encode($response);
    }

    public function edit($id='')
    {
        if ($id === '' || $id === null) {
            $id = $this->input->get('id');
        }
        $id = (int)$id;
		
		
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
    $data['product_variants'] = $this->db->get_where('product_variants', ['product_id' => $id])->result_array();
        $data['taxes'] = fetch_details('taxes', null, '*');
        $data['countries'] = fetch_details('countries', null, 'name,id');
        $data['sellers'] = $this->get_available_sellers();
       // if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {
            $data['title'] = 'Update Product | ';
            $data['meta_description'] = 'Update Product | ';
            $product_details = fetch_details('products', ['id' => $id], '*');
            $countries = fetch_details('countries', ['name' => $product_details[0]['made_in']], 'name');
            if (!empty($product_details)) {
                $data['product_details'] = $product_details;
                $data['product_variants'] = get_variants_values_by_pid($id);
				
                // Rebuild barcode_data for old products if it lacks UOM values but variants exist
                $has_uom = false;
                if (!empty($data['barcode_data'])) {
                    foreach ($data['barcode_data'] as $b_row) {
                        if (!empty($b_row['UOM'])) {
                            $has_uom = true;
                            break;
                        }
                    }
                }
                if (!$has_uom && !empty($data['product_variants'])) {
                    $new_barcode_data = [];
                    foreach ($data['product_variants'] as $variant) {
                        $existing_row = !empty($data['barcode_data']) ? array_shift($data['barcode_data']) : null;
                        $new_barcode_data[] = [
                            'id' => $existing_row ? $existing_row['id'] : 0,
                            'product_id' => $id,
                            'print_name' => (!empty($variant['print_name'])) ? $variant['print_name'] : (!empty($existing_row['print_name']) ? $existing_row['print_name'] : $product_details[0]['name']),
                            'article_no' => (!empty($variant['product_article'])) ? $variant['product_article'] : (!empty($existing_row['article_no']) ? $existing_row['article_no'] : ''),
                            'hpnumber' => $existing_row ? $existing_row['hpnumber'] : '',
                            'UOM' => $variant['variant_values'] ?? '',
                            'weight' => $existing_row ? $existing_row['weight'] : '0.00'
                        ];
                    }
                    $data['barcode_data'] = $new_barcode_data;
                }

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
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $popup_sell_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-success btn-sm  update-object">Updated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        } else {
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>   
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>  
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $popup_sell_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  update-object">Unupdated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
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
    sync_online_order_stock_ledger();
    $catid = $this->input->get('id');
    $list = $this->products->get_datatables($catid, true);
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

        $price_history = $this->get_purchase_price_history($prd->id);
        $display_purchase_price = $price_history['first_purchase_price'];
        $display_updated_price = $price_history['updated_price'];

        if ($display_purchase_price <= 0) {
            $display_purchase_price = (float)$prd->purchase_price;
        }

        $stock_valuation_price = ($display_updated_price > 0) ? $display_updated_price : $display_purchase_price;
        $livestockval = $livestock * $stock_valuation_price;
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

        // Compute special price the same way the DB (recalculate_variant_prices_on_update trigger)
        // does, since that's what the product edit page's Special Price box shows on load
        // (it renders the stored product_variants.special_price, not a live JS recalculation):
        // purchase_price -> + margin -> - discount% -> + packing_price (packing added last).
        $variant_row = $this->db->select('purchase_price, packing_price, margin_type, margin_percent, disc_percent')
            ->from('product_variants')
            ->where('product_id', $prd->id)
            ->limit(1)
            ->get()->row_array();

        $variant_purchase_price = !empty($variant_row['purchase_price']) ? (float)$variant_row['purchase_price'] : (float)$prd->purchase_price;
        $packing_price = isset($variant_row['packing_price']) ? (float)$variant_row['packing_price'] : 0;
        $margin_type = !empty($variant_row['margin_type']) ? $variant_row['margin_type'] : 'Fixed';
        $margin_percent = isset($variant_row['margin_percent']) ? (float)$variant_row['margin_percent'] : 0;
        $disc_percent = isset($variant_row['disc_percent']) ? (float)$variant_row['disc_percent'] : 0;

        if ($margin_type === 'Percentage') {
            $marked_up_price = $variant_purchase_price + ($variant_purchase_price * $margin_percent / 100);
        } else {
            $marked_up_price = $variant_purchase_price + $margin_percent;
        }
        $special_price = ($marked_up_price - ($marked_up_price * $disc_percent / 100)) + $packing_price;

        $row[] = number_format($special_price, 2);

        // Popup Sell Price = margin/discount/packaging-based special price; fall back to product_price if not set
        $popup_sell_price = ($special_price > 0) ? $special_price : (float)$prd->product_price;

        $last_bill_price = (float)$this->getlastbillprice($prd->id);
        if ($disc_percent > 0) {
            $last_bill_price = $last_bill_price - ($last_bill_price * ($disc_percent / 100));
        }
        $row[] = number_format($last_bill_price, 2);
        $row[] = number_format($display_purchase_price, 2);
        $row[] = ($display_updated_price > 0) ? number_format($display_updated_price, 2) : '-';
        if (!empty($prd->updated_date) && date('Y-m-d', strtotime($prd->updated_date)) === $today) {
            $upbutton = '';
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $popup_sell_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-success btn-sm  update-object">Updated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            } elseif (is_seller_user()) {
                $upbutton = '<a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $popup_sell_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-success btn-sm  update-object">Updated</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        } else {
            $upbutton = '';
            if ($this->aauth->get_user()->roleid == 1) {
                $upbutton = '<a href="' . base_url() . 'products/edit/' . $pid . '" class="btn btn-primary btn-sm"><span class="fa fa-pencil"></span> ' . $this->lang->line('Edit') . '</a>
                  <a href="#" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  delete-object"><span class="fa fa-trash"></span> ' . $this->lang->line('Delete') . '</a>
                  <a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $popup_sell_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  update-object">Unupdated</a>  <a href="#" onclick="sellreportspopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $prd->product_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-primary btn-sm  update-object">Sell Reports</a>';
            } elseif (is_seller_user()) {
                $upbutton = '<a href="#" onclick="updatePopup(' . $prd->id . ', \'' . addslashes($prd->name) . '\', ' . $popup_sell_price . ', ' . $prd->purchase_price . ', ' . $livestock . ')" data-object-id="' . $pid . '" class="btn btn-danger btn-sm  update-object">Unupdated</a>';
            }
            $row[] = '<a href="#" data-object-id="' . $pid . '" class="btn btn-success btn-sm  view-object"><span class="fa fa-eye"></span> ' . $this->lang->line('View') . ' </a> ' . $upbutton;
        }

        $data[] = $row;
    }

    $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->products->count_all($catid, true),
        "recordsFiltered" => $this->products->count_filtered($catid, true),
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

private function get_purchase_price_history($product_id)
{
    $result = array(
        'first_purchase_price' => 0,
        'updated_price' => 0,
    );

    $this->db->select('purchage_rate');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $product_id);
    $this->db->where('purchage_rate !=', 0);
    $this->db->order_by('created_date', 'ASC');
    $this->db->order_by('id', 'ASC');
    $this->db->limit(1);
    $first_query = $this->db->get();

    if ($first_query->num_rows() > 0) {
        $result['first_purchase_price'] = (float)$first_query->row()->purchage_rate;
    }

    $this->db->select('purchage_rate, ledger_type');
    $this->db->from('product_ledger');
    $this->db->where('product_id', $product_id);
    $this->db->where('purchage_rate !=', 0);
    $this->db->order_by('created_date', 'DESC');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $last_query = $this->db->get();

    if ($last_query->num_rows() > 0) {
        $row = $last_query->row();
        $latest_purchase_price = (float)$row->purchage_rate;
        if ($row->ledger_type === 'Price Update' || $latest_purchase_price != $result['first_purchase_price']) {
            $result['updated_price'] = $latest_purchase_price;
        }
    }

    return $result;
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
    $this->load->model('Products_model');

    $product_id = $this->input->post('product_id');
    $sell_price = $this->input->post('sell_price');
    $purchase_price = $this->input->post('purchase_price');
    $wastage = $this->input->post('wastage');
    $stock = $this->input->post('stock');
    $updated_date = date('Y-m-d H:i:s');

    $product = $this->Products_model->get_product($product_id);
    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        return;
    }

    if (is_seller_user()) {
        $seller_id = (int)$this->session->userdata('user_id');
        if ((int)$product['seller_id'] !== $seller_id) {
            echo json_encode(['status' => 'error', 'message' => 'You are not allowed to update this product.']);
            return;
        }
    }

    $variants = $this->Products_model->get_all_active_variants($product_id);
    if (empty($variants)) {
        echo json_encode(['status' => 'error', 'message' => 'No active variant found.']);
        return;
    }

    $variant = $variants[0];
    $unit = $this->Products_model->get_unit_from_attribute($variant['attribute_value_ids']);

    $calculated_price = $sell_price;
    $sell_rate = $sell_price;

    if ($product['product_price'] == $sell_price && $variant['purchase_price'] != $purchase_price) {
        // Same order the DB trigger uses: purchase -> margin -> discount -> + packing (packing added last)
        $variant_packing_price = isset($variant['packing_price']) ? (float)$variant['packing_price'] : 0;
        $variant_disc_percent = isset($variant['disc_percent']) ? (float)$variant['disc_percent'] : 0;

        if ($variant['margin_type'] === 'Percentage') {
            $marked_up_price = $purchase_price + ($purchase_price * $variant['margin_percent'] / 100);
        } else {
            $marked_up_price = $purchase_price + $variant['margin_percent'];
        }

        $calculated_price = ($marked_up_price - ($marked_up_price * $variant_disc_percent / 100)) + $variant_packing_price;
        $sell_rate = $calculated_price;
    }

    $stock_from_popup = floatval($stock);
    $wastage_amount = floatval($wastage);
    $final_stock = $stock_from_popup - $wastage_amount;
    
    if ($final_stock < 0) {
        $final_stock = 0;
    }
    
    $product_data = [
        'product_price' => $calculated_price,
        'purchase_price' => $purchase_price,
        'fproduct_price' => $purchase_price,
        'stock' => $final_stock,
        'updated_date' => $updated_date,
    ];

    $update_result = $this->Products_model->update_product($product_id, $product_data);
    
    if (!$update_result) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update product.']);
        return;
    }

    $base_measurement = null;
    foreach ($variants as $v) {
        $m = $this->extract_variant_measurement($v['attribute_value_ids']);
        if (!empty($m)) {
            $base_measurement = $m;
            break;
        }
    }

    foreach ($variants as $var) {
        $multiplier = 1.0;
        $variant_measurement = $this->extract_variant_measurement($var['attribute_value_ids']);
        if (!empty($base_measurement) && !empty($variant_measurement)) {
            if (
                $base_measurement['dimension'] === $variant_measurement['dimension'] &&
                (float)$base_measurement['normalized'] > 0
            ) {
                $multiplier = (float)$variant_measurement['normalized'] / (float)$base_measurement['normalized'];
            }
        }

        $variant_purchase_price = round(((float)$purchase_price) * $multiplier, 2);

        $variant_data = [
            'stock' => $final_stock,
            'purchase_price' => $variant_purchase_price,
        ];
        
        $this->Products_model->update_variant($var['id'], $variant_data);
    }

   $ledger_type = (!empty($wastage) && floatval($wastage) != 0) ? 'Wastage' : 'Updated by ' . $this->aauth->get_user()->username;

$ledger_data = [
    'product_id' => $product_id,
    'product_variants' => $variant['id'],
    'product_name' => $product['name'], 
    'unit' => $unit, 
    'purchage_rate' => $purchase_price,
    'sell_rate' => $sell_rate,
    'purchage_qty' => 0,
    'sell_qty' => 0,
    'purchage_amount' => 0,
    'sell_amount' => 0,
    'open_stock' => $product['stock'], 
    'close_stock' => $final_stock,
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

    $recalculated_balance = $this->products->get_total_livebalance($product_id, '', '');
    $recalculated_stock = round($recalculated_balance['total_balance'], 2);
    
    if (abs($final_stock - $recalculated_stock) > 0.01) {
        $this->db->where('id', $product_id)->update('products', ['stock' => $recalculated_stock]);
        foreach ($variants as $var) {
            $this->db->where('id', $var['id'])->update('product_variants', ['stock' => $recalculated_stock]);
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
}

private function extract_variant_measurement($attribute_value_ids)
{
    if (empty($attribute_value_ids)) {
        return null;
    }

    $ids = array_filter(array_map('trim', explode(',', (string)$attribute_value_ids)));
    if (empty($ids)) {
        return null;
    }

    $this->db->select('value');
    $this->db->where_in('id', $ids);
    $rows = $this->db->get('attribute_values')->result_array();
    if (empty($rows)) {
        return null;
    }

    foreach ($rows as $row) {
        $raw = isset($row['value']) ? (string)$row['value'] : '';
        $s = strtoupper(trim($raw));
        if ($s === '') {
            continue;
        }
        $s = preg_replace('/\s+/', '', $s);

        if (!preg_match('/(\d+(?:\.\d+)?)/', $s, $numMatch)) {
            continue;
        }
        $num = (float)$numMatch[1];
        if ($num <= 0) {
            continue;
        }

        preg_match('/[A-Z]+/', $s, $unitMatch);
        $unit = isset($unitMatch[0]) ? $unitMatch[0] : '';

        $dimension = 'count';
        $normalized = $num;

        if (in_array($unit, ['KG', 'KGS', 'KILO', 'KILOGRAM', 'KILOGRAMS'], true)) {
            $dimension = 'weight';
            $normalized = $num * 1000;
        } elseif (in_array($unit, ['G', 'GM', 'GR', 'GRAM', 'GRAMS'], true)) {
            $dimension = 'weight';
            $normalized = $num;
        } elseif (in_array($unit, ['L', 'LTR', 'LIT', 'LITER', 'LITRE', 'LITERS', 'LITRES'], true)) {
            $dimension = 'volume';
            $normalized = $num * 1000;
        } elseif (in_array($unit, ['ML', 'MILLILITER', 'MILLILITRE', 'MILLILITERS', 'MILLILITRES'], true)) {
            $dimension = 'volume';
            $normalized = $num;
        } elseif (in_array($unit, ['PCS', 'PC', 'PIECE', 'PIECES', 'NOS', 'NO'], true)) {
            $dimension = 'count';
            $normalized = $num;
        }

        return [
            'dimension' => $dimension,
            'normalized' => $normalized,
        ];
    }

    return null;
}



public function getlivestock($pid){
	$from_date="";
	$to_date ="";
	$balance = $this->products->get_total_livebalance($pid, $from_date, $to_date);
	return round($balance['total_balance'], 2);
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
        $hpnumber    = $this->input->post('hpnumber');
        $uoms        = $this->input->post('uom');
        $deleted_ids = $this->input->post('deleted_ids');

        // Variant details
        $variants_ids = $this->input->post('variants_ids');
        $purchase_prices = $this->input->post('purchase_price');
        $packing_prices = $this->input->post('packing_price');
        $margin_types = $this->input->post('margin_type');
        $margin_percents = $this->input->post('margin_percent');
        $disc_percents = $this->input->post('disc_percent');
        $variant_prices = $this->input->post('variant_price');
        $variant_special_prices = $this->input->post('variant_special_price');
        $variant_skus = $this->input->post('variant_sku');
        $variant_total_stocks = $this->input->post('variant_total_stock');
        $variant_stock_statuses = $this->input->post('variant_level_stock_status');

        if (empty($product_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Product ID missing']);
            return;
        }

        // Decode deleted IDs from JSON
        $deleted_ids = !empty($deleted_ids) ? json_decode($deleted_ids, true) : [];

        $schema_cache = [];
        $has_variant_price = $this->db->field_exists('price', 'product_variants');
        $has_variant_product_price = $this->db->field_exists('product_price', 'product_variants');
        $has_variant_stock = $this->db->field_exists('stock', 'product_variants');
        $has_variant_total_stock = $this->db->field_exists('total_stock', 'product_variants');
        $has_variant_availability = $this->db->field_exists('availability', 'product_variants');
        $has_variant_level_stock_status = $this->db->field_exists('level_stock_status', 'product_variants');

        // Save Barcode Info and Sync Variants
        for ($i = 0; $i < count($printnames); $i++) {
            $uom = isset($uoms[$i]) ? $uoms[$i] : '';
            
            // 1. Save to product_barcode_info
            $barcode_data = [
                'product_id' => $product_id,
                'print_name' => $printnames[$i],
                'article_no' => $articles[$i],
                'hpnumber' => $hpnumber[$i],
                'UOM'        => $uom
            ];

            if (!empty($row_ids[$i]) && $row_ids[$i] != '0') {
                $this->db->where('id', $row_ids[$i]);
                $this->db->update('product_barcode_info', $barcode_data);
            } else {
                $this->db->insert('product_barcode_info', $barcode_data);
            }

            // 2. Save/Update product_variants
            $v_id = isset($variants_ids[$i]) ? $variants_ids[$i] : '';
            if (!empty($v_id)) {
                $variant_data = [
                    'product_id' => $product_id,
                    'attribute_value_ids' => $v_id,
                    'purchase_price' => isset($purchase_prices[$i]) ? $purchase_prices[$i] : 0,
                    'packing_price' => isset($packing_prices[$i]) ? $packing_prices[$i] : 0,
                    'margin_type' => isset($margin_types[$i]) ? $margin_types[$i] : 'Fixed',
                    'margin_percent' => isset($margin_percents[$i]) ? $margin_percents[$i] : 0,
                    'disc_percent' => isset($disc_percents[$i]) ? $disc_percents[$i] : 0,
                    'special_price' => isset($variant_special_prices[$i]) ? $variant_special_prices[$i] : 0,
                    'sku' => isset($variant_skus[$i]) ? $variant_skus[$i] : '',
                ];

                if ($has_variant_price) {
                    $variant_data['price'] = isset($variant_prices[$i]) ? $variant_prices[$i] : 0;
                }
                if ($has_variant_product_price) {
                    $variant_data['product_price'] = isset($variant_prices[$i]) ? $variant_prices[$i] : 0;
                }
                if ($has_variant_stock) {
                    $variant_data['stock'] = isset($variant_total_stocks[$i]) ? $variant_total_stocks[$i] : 0;
                }
                if ($has_variant_total_stock) {
                    $variant_data['total_stock'] = isset($variant_total_stocks[$i]) ? $variant_total_stocks[$i] : 0;
                }
                if ($has_variant_availability) {
                    $variant_data['availability'] = isset($variant_stock_statuses[$i]) ? $variant_stock_statuses[$i] : 1;
                }
                if ($has_variant_level_stock_status) {
                    $variant_data['level_stock_status'] = isset($variant_stock_statuses[$i]) ? $variant_stock_statuses[$i] : 1;
                }

                // Check if variant exists
                $this->db->where(['product_id' => $product_id, 'attribute_value_ids' => $v_id]);
                $exists = $this->db->get('product_variants')->row();

                if ($exists) {
                    $this->db->where('id', $exists->id);
                    $this->db->update('product_variants', $variant_data);
                } else {
                    $this->db->insert('product_variants', $variant_data);
                }
            }
        }

        // Delete removed rows from DB
        if (!empty($deleted_ids)) {
            $this->db->where_in('id', $deleted_ids);
            $this->db->delete('product_barcode_info');
            // Note: We might want to delete variants too, but UOMs might be shared. 
            // For now, only deleting barcode info as requested.
        }

        echo json_encode(['status' => 'success']);
    }




}
