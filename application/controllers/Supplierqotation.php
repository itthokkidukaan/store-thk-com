<?php

class Supplierqotation extends CI_Controller
{
	
	   public function __construct()
    {
        parent::__construct();
       
       // $this->load->helper(array('form', 'url'));
    }
/*     public function index($phone = null)
    {
        $this->load->model('purchase_model');

        $supplier = $this->purchase_model->get_supplier_by_phone($phone);
        if (!$supplier) {
            show_error('Supplier not found');
        }

        $quotation = $this->purchase_model->get_latest_supplier_quotation($supplier->id);
        $items = $this->purchase_model->get_supplier_quotation_items($quotation->id);

        $data = [
            'supplier' => $supplier,
            'quotation' => $quotation,
            'items' => $items
        ];

      //  $this->load->view('fixed/header');
        $this->load->view('supplierqotation_form', $data);
       // $this->load->view('fixed/footer');
    } */
	
	public function index($phone = NULL, $reference_no = NULL)
{
	
	  $this->load->model('purchase_model');
    if (!$phone || !$reference_no) {
        show_404();
    }

    $supplier = $this->purchase_model->get_supplier_by_phone($phone);
	

    if (!$supplier) {
        show_404();
    }

    $quotation = $this->purchase_model->get_quotation_by_reference($supplier->id, $reference_no);
    if (!$quotation) {
        $start_tid = 0;
        $end_tid = 0;
        if (strpos($reference_no, '_') !== false) {
            list($start_tid, $end_tid) = explode('_', $reference_no);
        } else {
            $start_tid = $reference_no;
            $end_tid = $reference_no;
        }

        $start_tid = intval($start_tid);
        $end_tid = intval($end_tid);

        if ($start_tid > 0 && $end_tid > 0) {
            // Find invoice date from geopos_quotes
            $q_indent = $this->db->select('invoicedate')
                ->from('geopos_quotes')
                ->where('tid >=', $start_tid)
                ->where('tid <=', $end_tid)
                ->limit(1)
                ->get()
                ->row();

            if ($q_indent) {
                $date = $q_indent->invoicedate;

                // Load quote model and get items
                $this->load->model('quote_model', 'quote');
                $quote_items = $this->quote->get_quote_items_by_date($date);

                if (!empty($quote_items)) {
                    // Start transaction
                    $this->db->trans_start();

                    // Insert quotation
                    $order_data = array(
                        'supplier_id'   => $supplier->id,
                        'invoice_no'    => uniqid('QTN'),
                        'reference_no'  => $reference_no,
                        'discount'      => 0,
                        'order_date'    => $date,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'due_date'      => $date,
                    );
                    $this->db->insert('seller_quotation', $order_data);
                    $order_id = $this->db->insert_id();

                    // Allowed products filter
                    $allowed_products = $this->db->select('product_id')
                        ->from('supplier_allowed_products')
                        ->where('supplier_id', $supplier->id)
                        ->get()
                        ->result_array();
                    $allowed_ids = array_column($allowed_products, 'product_id');

                    foreach ($quote_items as $item) {
                        if (empty($allowed_ids) || in_array($item['pid'], $allowed_ids)) {
                            $qi_data = array(
                                'order_id'   => $order_id,
                                'item_id'    => $item['pid'],
                                'item_name'  => $item['productname'] . (!empty($item['original_unit']) ? ' - ' . $item['original_unit'] : ''),
                                'quantity'   => $item['total_qty'],
                                'unit'       => $item['original_unit'],
                            );
                            $this->db->insert('seller_quotation_items', $qi_data);
                        }
                    }

                    $this->db->trans_complete();

                    // Reload quotation
                    $quotation = $this->purchase_model->get_quotation_by_reference($supplier->id, $reference_no);
                }
            }
        }
    }

    if (!$quotation) {
        show_404();
    }

    $items = $this->purchase_model->get_supplier_quotation_items($quotation->id);

    $data = array(
        'supplier' => $supplier->id,
        'supplier_name' => $supplier->name,
        'quotation' => $quotation,
        'items' => $items
    );

    $this->load->view('supplierqotation_form', $data);
}


	public function challan($phone = NULL, $reference_no = NULL)
{
	
	  $this->load->model('purchase_model');
    if (!$phone || !$reference_no) {
        show_404();
    }

    $supplier = $this->purchase_model->get_supplier_by_phone($phone);
	

    if (!$supplier) {
        show_404();
    }

    $quotation = $this->purchase_model->get_quotation_by_reference($supplier->id, $reference_no);
    if (!$quotation) {
        show_404();
    }

    $items = $this->purchase_model->get_supplier_challan_items($quotation->id);

    $data = array(
        'supplierID' => $supplier->id,
        'supplier' => $supplier,
        'supplier_name' => $supplier->name,
        'quotation' => $quotation,
        'items' => $items
    );





    $this->load->view('supplier_challan', $data);
}



public function submitchallan() {
    $post = $this->input->post();

    $employee_id   = $post['employee_id'];
    $employee_name = $post['employee_name'];
    $supplier_id   = $post['supplier_id'];
    $supplier_name = $post['supplier_name'];
    $order_no      = $post['order_no'];
    $reference     = $post['reference'];
    $order_date    = $post['order_date'];
    $due_date      = $post['due_date'];
    $items         = $post['items'];

    // Master entry check
    $master = $this->db->get_where("received_master", ["reference" => $reference])->row();
$master_id =0;
    if ($master) {
        $master_id = $master->id;
    } else {
        $master_data = [
            "employee_id"   => $employee_id,
            "employee_name" => $employee_name,
            "order_no"      => $order_no,
            "reference"     => $reference,
            "order_date"    => $order_date,
            "due_date"      => $due_date
        ];
        $this->db->insert("received_master", $master_data);
        $master_id = $this->db->insert_id();
    }
	
	 $emplurl    = base_url("indent/receiveditemedit/{$master_id}");

    // Insert items with duplicate check
    foreach ($items as $item) {
        // Check if this item already exists for the same reference(master_id + product_id)
        $exists = $this->db->get_where("received_items", [
            "master_id"   => $master_id,
            "product_id"  => $item['productID']
        ])->row();

        if ($exists) {
            // Skip if duplicate found
            continue;
        }

        $data = [
            "master_id"     => $master_id,
            "product_name"  => $item['product_name'],
            "product_id"    => $item['productID'],
            "required_qty"  => $item['quantity'],
            "unit"          => $item['unit'],
            "received_qty"  => $item['fill_quantity'],
            "balance_qty"   => $item['quantity'] - $item['fill_quantity'],
            "supplier_id"   => $supplier_id,
            "supplier_name" => $supplier_name,
            "price"         => $item['fill_rate'],
            "description"   => '',
            "amount"        => $item['fill_quantity'] * $item['fill_rate']
        ];
        $this->db->insert("received_items", $data);
    }


$this->sendemployeenotif($reference, $emplurl);


    echo json_encode([
        "status" => "success",
        "message" => "Challan submitted successfully"
    ]);
	
	
	
}




public function sendemployeenotif($references, $emplurl){
	
	
	  $notifyPayload = [
            "apiKey"    => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY",
            "campaignName" => "store received product message",
            "destination"  => "917895761831",
            "userName"     => "Thok ki dukaan",
            "templateParams" => [
                "Abhishek Rana",
                $references,
                $emplurl
            ],
            "source"    => "new-landing-page form",
            "media"     => new \stdClass(),
            "buttons"   => [],
            "carouselCards" => [],
            "location"  => new \stdClass(),
            "attributes"=> new \stdClass(),
            "paramsFallbackValue" => ["FirstName" => "Abhishek Rana"]
        ];

        $ch2 = curl_init("https://backend.aisensy.com/campaign/t1/api/v2");
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($notifyPayload));
        $notifyResponse = curl_exec($ch2);
        curl_close($ch2);
}
    public function update_items()
    {
        $this->load->model('purchase_model');
        $items = $this->input->post('items');

        foreach ($items as $item) {
            $this->purchase_model->update_supplier_item($item);
        }

        echo json_encode(['status' => 'success']);
    }
}