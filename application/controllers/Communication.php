<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Communication extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('communication_model');
        $this->load->model('Products_model');
        $this->load->library("Aauth");
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
    }

  public function send_invoice()
    {
        if (!$this->aauth->premission(1)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }
        $mailtoc = $this->input->post('mailtoc');
        $mailtotilte = $this->input->post('customername');
        $subject = $this->input->post('subject');
				$att = '';
        $message = $this->input->post('message');
        $att = $this->input->post('attach');
        $attachmenttrue = false;
        $attachment = '';
        if ($att) {
            $tid = $this->input->post('tid');
            $attachmenttrue = true;
            $attach = $this->mail_attach($tid);
            $attachment = FCPATH . DIRECTORY_SEPARATOR . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'Invoice_' . $tid . '.pdf';
        }

        $this->communication_model->send_email($mailtoc, $mailtotilte, $subject, $message, $attachmenttrue, $attachment);
        if ($att) {
            unlink(FCPATH . DIRECTORY_SEPARATOR . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'Invoice_' . $tid . '.pdf');
        }
    }


	public function whatsapp_invoice()
    {
        if (!$this->aauth->premission(1)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }

$tid = $_GET['tid'];
				$this->mail_attach($tid);

 $url = "https://wapi.dialtext.com/sendMessage.php";


$auth_key = "THOKKIDUKAAN";
$instance_id = "427790";
$message = "Your today invoice";
$pdf_url = base_url("userfiles/temp/Invoice_".$tid.".pdf");
$phone = "91".$_GET['phone'];
    $data = array(
        'AUTH_KEY' => $auth_key,
        'instance_id' => $instance_id,
        'message' => $message,
        'pdf' => $pdf_url,
        'phone' => $phone
    );

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }

    // Close the cURL session
    curl_close($ch);

    // Return the response from the API
    echo $response;
 unlink(FCPATH . DIRECTORY_SEPARATOR . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'Invoice_' . $tid . '.pdf');

// Dynamic data (replace these values with your own data)



    }
	
	
	/* public function whatsapp_pricelist()
    {
        if (!$this->aauth->premission(1)) {
            exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
        }

				$this->pricelist_attach($this->input->post('catid'));

 $url = "https://wapi.dialtext.com/sendMessage.php";


$auth_key = "THOKKIDUKAAN";
$instance_id = "427790";
if($this->input->post('clientext')==''){
	
$message = "Our Today Price list";
	
}else{
	
	$message =$this->input->post('clientext');
	
}
$pdf_url = base_url("userfiles/temp/todaypricelist.pdf");
$phone =  $this->input->post('selectedClients');
    $data = array(
        'AUTH_KEY' => $auth_key,
        'instance_id' => $instance_id,
        'message' => $message,
        'pdf' => $pdf_url,
        'phone' => $phone
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }

    // Close the cURL session
    curl_close($ch);

    // Return the response from the API
  //  echo $response;
  return true;
 unlink(FCPATH . DIRECTORY_SEPARATOR . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'Invoice_' . $tid . '.pdf');

// Dynamic data (replace these values with your own data)



    } */
	
	public function whatsapp_pricelist()
{
    // permission check
    if (!$this->aauth->premission(1)) {
        exit('<h3>Sorry! You have insufficient permissions to access this section</h3>');
    }

    // 1) Generate today's PDF (your existing function)
    $this->pricelist_attach($this->input->post('catid'));

    // 2) Prepare values from POST or defaults
    // 'selectedClients' can hold one or more comma-separated numbers (one per checked customer)
    $destinationRaw = $this->input->post('selectedClients');
    if (empty($destinationRaw)) {
        // fallback - you can change this default or remove it
        $destinationRaw = $this->input->post('destination'); // try alternate field if present
    }

    // Validate destination is not empty
    if (empty($destinationRaw)) {
        log_message('error', "whatsapp_pricelist - No destination phone number provided");
        echo json_encode(['status' => 'error', 'message' => 'Please select at least one customer with a valid phone number.']);
        return false;
    }

    $destinations = array_filter(array_map('trim', explode(',', $destinationRaw)));
    if (empty($destinations)) {
        log_message('error', "whatsapp_pricelist - No destination phone number provided");
        echo json_encode(['status' => 'error', 'message' => 'Please select at least one customer with a valid phone number.']);
        return false;
    }

    $userName = $this->input->post('userName');
    if (empty($userName)) {
        $userName = "Thok ki dukaan"; // default
    }

    // Message template params (keeping same format as your curl example)
	$todayDate = date('d F Y'); // e.g., "14 October 2025"

    $templateParams = array(
        "Customer",
        "$todayDate"
    );

    // PDF URL (where your generated pdf is accessible publicly)
    $pdfUrl = base_url("userfiles/temp/todaypricelist.pdf");

    $apiUrl = "https://backend.aisensy.com/campaign/t1/api/v2";
    $apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY";

    // 3) The AiSensy campaign API only accepts a single "destination" per call,
    // so a customer must be sent its own request rather than a comma-joined list.
    $results = array();
    $anySuccess = false;

    foreach ($destinations as $destination) {
        $payload = array(
            "apiKey" => $apiKey,
            "campaignName" => "daily share product price",
            "destination" => $destination,
            "userName" => $userName,
            "templateParams" => $templateParams,
            "source" => "new-landing-page form",
            "media" => array(
                "url" => $pdfUrl,
                "filename" => "todaypricelist_" . date('Ymd')
            ),
            "buttons" => array(),
            "carouselCards" => array(),
            "location" => new stdClass(),   // empty object as in your curl
            "attributes" => new stdClass(), // empty object as in your curl
            "paramsFallbackValue" => array(
                "FirstName" => "Customer"
            )
        );

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $curlErrNo = curl_errno($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErrNo) {
            $errMsg = "cURL error ({$curlErrNo}): {$curlError}";
            log_message('error', "whatsapp_pricelist - curl error for {$destination}: {$errMsg}");
            $results[] = array(
                'destination' => $destination,
                'status' => 'error',
                'message' => $errMsg
            );
            continue;
        }

        log_message('info', "whatsapp_pricelist - destination: {$destination} | http_code: {$httpCode} | response: {$response}");

        $ok = ($httpCode >= 200 && $httpCode < 300);
        if ($ok) {
            $anySuccess = true;
        }
        $results[] = array(
            'destination' => $destination,
            'status' => $ok ? 'success' : 'api_error',
            'http_code' => $httpCode,
            'response' => $response
        );
    }

    // 4) Cleanup temporary PDF file (suppress errors if any)
   // @unlink(FCPATH . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'todaypricelist.pdf');

    echo json_encode([
        'status' => $anySuccess ? 'success' : 'error',
        'results' => $results
    ]);

    return true;
}



    public function send_general()
    {
        $mailtoc = $this->input->post('mailtoc');
        $mailtotilte = $this->input->post('customername');
        $subject = $this->input->post('subject');
        $message = $this->input->post('text');
        $attachmenttrue = false;
        $attachment = '';
        $this->communication_model->send_email($mailtoc, $mailtotilte, $subject, $message, $attachmenttrue, $attachment);
    }


    private function mail_attach($tid)
    {
        $this->load->model('invoices_model', 'invocies');
        $this->load->library("Custom");
        $data['id'] = $tid;
        $data['invoice'] = $this->invocies->invoice_details($tid);
        $data['title'] = "Invoice " . $data['invoice']['tid'];
        $data['products'] = $this->invocies->invoice_products($tid);
        $data['employee'] = $this->invocies->employee($data['invoice']['eid']);
        if (CUSTOM) $data['c_custom_fields'] = $this->custom->view_fields_data($data['invoice']['cid'], 1, 1);

        $data['round_off'] = $this->custom->api_config(4);
        if ($data['invoice']['i_class'] == 1) {
            $pref = prefix(7);
        } elseif ($data['invoice']['i_class'] > 1) {
            $pref = prefix(3);
        } else {
            $pref = $this->config->item('prefix');
        }
        $data['general'] = array('title' => $this->lang->line('Invoice'), 'person' => $this->lang->line('Customer'), 'prefix' => $pref, 't_type' => 0);
        ini_set('memory_limit', '64M');
        if ($data['invoice']['taxstatus'] == 'cgst' || $data['invoice']['taxstatus'] == 'igst') {
            $html = $this->load->view('print_files/invoice-a4-gst_v' . INVV, $data, true);
        } else {
            $html = $this->load->view('print_files/invoice-a4_v' . INVV, $data, true);
        }
        //PDF Rendering
        $this->load->library('pdf');
        if (INVV == 1) {
            $header = $this->load->view('print_files/invoice-header_v' . INVV, $data, true);
            $pdf = $this->pdf->load_split(array('margin_top' => 40));
            $pdf->SetHTMLHeader($header);
        }
        if (INVV == 2) {
            $pdf = $this->pdf->load_split(array('margin_top' => 5));
        }
        $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #' . $data['invoice']['tid'] . '</div>');
        $pdf->WriteHTML($html);

        return $pdf->Output(FCPATH . DIRECTORY_SEPARATOR . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'Invoice_' . $tid . '.pdf', 'F');
       


    } 
	
	
	public function pricelist_attach($catid)
    {
        $this->load->model('invoices_model', 'invocies');
        $this->load->library("Custom");
        $this->load->model('quote_model', 'quote');
        $this->load->model('Products_model');
        
        // Handle empty or null catid
        if (empty($catid)) {
            $catid = 'All';
        }
        
        $data['id'] = '111';
        $data['title'] = "Price List";
        $data['products'] = $this->quote->productslist($catid);
        
        // Log for debugging
        log_message('info', "pricelist_attach - catid: {$catid}, products count: " . count($data['products']));
        
        // Check if products exist
        if (empty($data['products'])) {
            log_message('error', "pricelist_attach - No products found for catid: {$catid}");
            // Return false or create empty PDF with message
        }
        
        $data['round_off'] = $this->custom->api_config(4);
        $data['general'] = array('title' => 'Today Price', 'person' => $this->lang->line('Customer'), 'prefix' => prefix(1), 't_type' => 1);

        ini_set('memory_limit', '64M');
     
        // Load view and capture any errors
        ob_start();
        try {
            $html = $this->load->view('print_files/product_price', $data, true);
        } catch (Exception $e) {
            log_message('error', "pricelist_attach - View error: " . $e->getMessage());
            $html = '<html><body><h1>Error generating price list</h1><p>' . $e->getMessage() . '</p></body></html>';
        }
        ob_end_clean();
        
        // Log HTML length for debugging
        log_message('info', "pricelist_attach - HTML length: " . strlen($html));
       
        //PDF Rendering
        $this->load->library('pdf');
        try {
            if (INVV == 1) {
                $header = $this->load->view('print_files/invoice-header_v' . INVV, $data, true);
                $pdf = $this->pdf->load_split(array('margin_top' => 40));
                $pdf->SetHTMLHeader($header);
            }
            if (INVV == 2) {
                $pdf = $this->pdf->load_split(array('margin_top' => 5));
            }
            $pdf->SetHTMLFooter('<div style="text-align: right;font-family: serif; font-size: 8pt; color: #5C5C5C; font-style: italic;margin-top:-6pt;">{PAGENO}/{nbpg} #Price List</div>');
            $pdf->WriteHTML($html);

            $output_path = FCPATH . DIRECTORY_SEPARATOR . 'userfiles' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'todaypricelist.pdf';
            $result = $pdf->Output($output_path, 'F');
            
            // Verify file was created
            if (file_exists($output_path) && filesize($output_path) > 0) {
                log_message('info', "pricelist_attach - PDF created successfully, size: " . filesize($output_path));
                return $result;
            } else {
                log_message('error', "pricelist_attach - PDF file not created or is empty");
                return false;
            }
        } catch (Exception $e) {
            log_message('error', "pricelist_attach - PDF generation error: " . $e->getMessage());
            return false;
        }
       


    }


}
