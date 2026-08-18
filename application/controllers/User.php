<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class User extends CI_Controller
{
    // AiSensy WhatsApp settings
    private $aisensy_api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4YTNmNTg0YmU3MWMxMGMzM2FiODlmOCIsIm5hbWUiOiJUaG9rIGtpIGR1a2FhbiIsImFwcE5hbWUiOiJBaVNlbnN5IiwiY2xpZW50SWQiOiI2OGEzZjU4NGJlNzFjMTBjMzNhYjg5ZjMiLCJhY3RpdmVQbGFuIjoiRlJFRV9GT1JFVkVSIiwiaWF0IjoxNzU1NTc1Njg0fQ.aCATrXPQFmv1B0QP433i_MHHhPlC1pT0doRhSzHvVSY";
    private $aisensy_login_otp_campaign = "indent send for quotation"; // Using the approved quotation campaign for OTP delivery
    private $aisensy_reset_otp_campaign = "indent send for quotation"; // Using the approved quotation campaign for Reset OTP delivery
    private $aisensy_sender_name = "Thok ki dukaan";


    public function __construct()
    {
        parent::__construct();
        // YRegards constructor code
        $this->load->library("Aauth");
        $this->load->library("Captcha_u");
        $this->load->library("form_validation");
        $this->captcha = $this->captcha_u->public_key()->captcha;
		
		    $this->load->model('Customer_model');
    }

    public function index()
    {


        if ($this->aauth->is_loggedin()) {
            redirect('/dashboard/', 'refresh');
        }
        $data['response'] = '';
        $data['captcha_on'] = $this->captcha;
        $data['captcha'] = $this->captcha_u->public_key()->recaptcha_p;
        if ($this->input->get('e')) {
            if ($this->input->get('e') == 'deactive') {
                $data['response'] = 'Your account is deactivated. Please contact the administrator.';
            } else {
                $data['response'] = 'Invalid username or password!';
            }
        }
        $this->load->view('user/header');
        $this->load->view('user/index', $data);
        $this->load->view('user/footer');


    }

    public function no_access()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
            return;
        }
        $head['title'] = 'No Access';
        $head['usernm'] = $this->aauth->get_user()->username;
        $this->load->view('fixed/header', $head);
        $this->load->view('user/no_access');
        $this->load->view('fixed/footer');
    }
	
	

	public function generateOtp(){
		$this->processOtpRequest();
	}

	public function resendOtp()
	{
		$this->processOtpRequest();
	}

	private function processOtpRequest()
	{
					 
/*  $url = "https://wapi.dialtext.com/sendMessage.php";
 $user = $this->input->post('mobile_number');
 
 
$otp = random_int(100000, 999999);
$auth_key = "THOKKIDUKAAN";
$instance_id = "427790";
$message = "Your Login For THOKKIDUKAAN OTP is ".$otp;
$pdf_url = base_url("userfiles/temp/Invoice_".$tid.".pdf");
$phone = "91".$user;
    $data = array(
        'AUTH_KEY' => $auth_key,
        'instance_id' => $instance_id,
        'message' => $message,
        'phone' => $phone
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    }
    curl_close($ch);
echo json_encode(['success' => true]); */
		
		
		// Get mobile number from POST request
    $mobile_number = $this->normalizeMobileNumber($this->input->post('mobile_number'));

    if (empty($mobile_number)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid mobile number.']);
        return;
    }

    // Check if mobile number exists in the 'users' table with role_id != 0
    $user = $this->Customer_model->checkMobileNumber($mobile_number);

    if ($user) {
        // Generate OTP
        $otp = random_int(100000, 999999);

        // Send OTP to the mobile number using external API
        $send_result = $this->sendOtp($mobile_number, $otp);
        if (!$send_result['success']) {
            echo json_encode(['success' => false, 'message' => 'WhatsApp OTP send failed: ' . $send_result['message']]);
            return;
        }

        // Update OTP in the 'users' table
        $this->Customer_model->updateOtp($mobile_number, $otp);

        // Send success response
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully!']);
    } else {
        // Mobile number not found
        echo json_encode(['success' => false, 'message' => 'Mobile number not found or invalid user.']);
    }
	}

	private function jsonResponse($payload)
	{
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
	}
	
	
	public function verifyOtp() {
    // Load the model to validate the OTP
   // $this->load->model('User_model');

    // Get mobile number and OTP from POST request
    $mobile_number = $this->normalizeMobileNumber($this->input->post('mobile_number'));
    $user = $mobile_number;
    $entered_otp = $this->input->post('otp');

    if (empty($mobile_number) || empty($entered_otp)) {
        $this->jsonResponse(['success' => false, 'message' => 'Mobile number and OTP are required.']);
        return;
    }

    // Check if the OTP is valid for the given mobile number
    $is_valid = $this->Customer_model->validateOtp($mobile_number, $entered_otp);

    if ($is_valid) {
        // OTP is valid, proceed with login
        // You can set session data or redirect to the dashboard
		
 		 if ($this->aauth->loginbyotp($user)) {
             $this->Customer_model->updateOtp($mobile_number, ''); // Clear OTP after successful login
             $user_id = $this->aauth->get_user()->id;
             $seller = $this->db->get_where('seller_data', array('user_id' => $user_id))->row_array();
             if (!empty($seller) && $seller['status'] != 1) {
                 $this->aauth->logout();
                 $this->jsonResponse(['success' => false, 'message' => 'Your account is deactivated. Please contact the administrator.']);
                 return;
             }
             $this->aauth->applog("[Logged In] $user");
		 //  echo json_encode(['success' => true]);
             $this->jsonResponse(['success' => true, 'message' => 'OTP verified successfully!']);
             return;
         }

        $this->jsonResponse(['success' => false, 'message' => 'Unable to log in with OTP. Please try again.']);
    } else {
        // OTP is invalid, show an error message
        $this->jsonResponse(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
}
	
    private function normalizeMobileNumber($mobile_number)
    {
        $mobile_number = preg_replace('/\D+/', '', (string)$mobile_number);
        if (strpos($mobile_number, '91') === 0 && strlen($mobile_number) > 10) {
            $mobile_number = substr($mobile_number, -10);
        }

        return $mobile_number;
    }

    private function sendWhatsAppViaAiSensy($mobile_number, $campaign_name, $template_params)
    {
        $clean_phone   = preg_replace('/[^0-9]/', '', $mobile_number);
        $mobile_number = "91" . substr($clean_phone, -10);

        // AiSensy payload
        $data = array(
            "apiKey"          => $this->aisensy_api_key,
            "campaignName"    => $campaign_name,
            "destination"     => $mobile_number,
            "userName"        => $this->aisensy_sender_name,
            "templateParams"  => $template_params,
            "source"          => "user-auth",
            "media"           => new stdClass(),
            "buttons"         => [],
            "carouselCards"   => [],
            "location"        => new stdClass(),
            "attributes"      => new stdClass(),
            "paramsFallbackValue" => array(
                "FirstName" => "User"
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://backend.aisensy.com/campaign/t1/api/v2");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Required for localhost development
        $response = curl_exec($ch);
        $has_error = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($has_error) {
            log_message('error', 'cURL Error in sendWhatsAppViaAiSensy: ' . $curl_error);
            return array('success' => false, 'message' => 'Connection error: ' . $curl_error);
        }

        $res_data = json_decode($response, true);
        if (isset($res_data['success']) && $res_data['success'] == true) {
            return array('success' => true);
        } else {
            $msg = isset($res_data['message']) ? $res_data['message'] : 'Unknown error from AiSensy';
            log_message('error', 'AiSensy Failure: ' . $response);
            return array('success' => false, 'message' => $msg);
        }
    }

    private function sendWhatsAppMessage($mobile_number, $message)
    {
    $url = "https://wapi.dialtext.com/sendMessage.php";
    $auth_key = "THOKKIDUKAAN";
    $instance_id = "427790";
    $phone = "91" . $mobile_number;

    $data = array(
        'AUTH_KEY' => $auth_key,
        'instance_id' => $instance_id,
        'message' => $message,
        'phone' => $phone
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $has_error = curl_errno($ch);

    curl_close($ch);

    return !$has_error && $response !== false;
}

	private function sendOtp($mobile_number, $otp) {
        return $this->sendWhatsAppViaAiSensy($mobile_number, $this->aisensy_login_otp_campaign, ["User", "OTP: " . $otp]);
    }


    public function checklogin()
    {
        $user = $this->input->post('username');
        $password = $this->input->post('password');
        $remember_me = $this->input->post('remember_me');
        $rem = false;
        if ($remember_me == 'on') {
            $rem = true;
        }
        if ($this->aauth->login($user, $password, $rem, $this->captcha)) {
            $user_id = $this->aauth->get_user()->id;
            $seller = $this->db->get_where('seller_data', array('user_id' => $user_id))->row_array();
            if (!empty($seller) && $seller['status'] != 1) {
                $this->aauth->logout();
                redirect('/user/?e=deactive', 'refresh');
                return;
            }
            $this->aauth->applog("[Logged In] $user");
		//   echo json_encode(['success' => true]);
		    redirect('/dashboard/', 'refresh');
        } else {
echo json_encode(['success' => false,'message' => 'Invalid username Or Password']);
     redirect('/user/?e=eyxde', 'refresh');
        }

    }

    public function profile()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }


        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = $head['usernm'] . ' Profile';
        $this->load->model('employee_model', 'employee');
        $id = $this->aauth->get_user()->id;
        $data['employee'] = $this->employee->employee_details($id);
        $data['eid'] = intval($id);
        $this->load->view('fixed/header', $head);
        $this->load->view('user/profile', $data);
        $this->load->view('fixed/footer');


    }

    public function attendance()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }


        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = $head['usernm'] . ' attendance ';


        $this->load->view('fixed/header', $head);
        $this->load->view('user/attendance');
        $this->load->view('fixed/footer');


    }

    public function holidays()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = $head['usernm'] . ' attendance ';

        $this->load->view('fixed/header', $head);
        $this->load->view('user/holidays');
        $this->load->view('fixed/footer');

    }

    public function getAttendance()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $this->load->model('employee_model', 'employee');
        $id = $this->aauth->get_user()->id;

        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $result = $this->employee->getAttendance($id, $start, $end);
        echo json_encode($result);
    }

    public function getHolidays()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $this->load->model('employee_model', 'employee');
        $id = $this->aauth->get_user()->loc;

        $start = $this->input->get('start');
        $end = $this->input->get('end');
        $result = $this->employee->getHolidays($id, $start, $end);
        echo json_encode($result);
    }

    public function update()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }


        $id = $this->aauth->get_user()->id;
        $this->load->model('employee_model', 'employee');
        if ($this->input->post()) {
            $name = $this->input->post('name', true);
            $phone = $this->input->post('phone', true);
            $phonealt = $this->input->post('phonealt', true);
            $address = $this->input->post('address', true);
            $city = $this->input->post('city', true);
            $region = $this->input->post('region', true);
            $country = $this->input->post('country', true);
            $postbox = $this->input->post('postbox', true);
            $lang = $this->input->post('language', true);
            $this->employee->update_employee($id, $name, $phone, $phonealt, $address, $city, $region, $country, $postbox, $this->aauth->get_user()->loc);
            $this->db->set('lang',$lang);
            $this->db->where('id', $id);
            $this->db->update('geopos_users');

        } else {
            $head['usernm'] = $this->aauth->get_user()->username;
            $head['title'] = $head['usernm'] . ' Profile';
            $this->load->library("Common");
            $data['langs'] = $this->common->current_language($this->aauth->get_user()->lang);


            $data['user'] = $this->employee->employee_details($id);
            $data['eid'] = intval($id);
            $this->load->view('fixed/header', $head);
            $this->load->view('user/edit', $data);
            $this->load->view('fixed/footer');
        }


    }

    public function displaypic()
    {

        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        $this->load->model('employee_model', 'employee');
        $id = $this->aauth->get_user()->id;
        $this->load->library("uploadhandler", array(
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i', 'upload_dir' => FCPATH . 'userfiles/employee/'
        ));
        $img = (string)$this->uploadhandler->filenaam();
        if ($img != '') {
            $this->employee->editpicture($id, $img);
        }


    }

    public function user_sign()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }


        $this->load->model('employee_model', 'employee');
        $id = $this->aauth->get_user()->id;
        $this->load->library("uploadhandler", array(
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i', 'upload_dir' => FCPATH . 'userfiles/employee_sign/'
        ));
        $img = (string)$this->uploadhandler->filenaam();
        if ($img != '') {
            $this->employee->editsign($id, $img);
        }


    }


    public function updatepassword()
    {

        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }

        $id = $this->aauth->get_user()->id;
        $this->load->model('employee_model', 'employee');


        if ($this->input->post()) {
            $this->form_validation->set_rules('newpassword', 'Password', 'required');
            $this->form_validation->set_rules('renewpassword', 'Confirm Password', 'required|matches[newpassword]');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('status' => 'Error', 'message' => '<br>Rules<br> Password length should  be at least 6 [a-z-0-9] allowed!<br>New Password & Re New Password should be same!'));
            } else {
                $cpassword = $this->input->post('cpassword');
                $newpassword = $this->input->post('newpassword');
                $renewpassword = $this->input->post('renewpassword');

                $hash = $this->aauth->hash_password($cpassword, $id);

                if (hash_equals($this->aauth->get_user()->password, $hash)) {
                    echo json_encode(array('status' => 'Success', 'message' => 'Password Updated Successfully!'));

                    $this->aauth->update_user($id, false, $newpassword, false);

                } else {
                    echo json_encode(array('status' => 'Error', 'message' => 'Incorrect current password!'));
                }
            }


        } else {
            $head['usernm'] = $this->aauth->get_user()->username;
            $head['title'] = $head['usernm'] . ' Profile';


            $data['user'] = $this->employee->employee_details($id);
            $data['eid'] = intval($id);
            $this->load->view('fixed/header', $head);
            $this->load->view('user/password', $data);
            $this->load->view('fixed/footer');
        }


    }

    public function forgot()
    {
        if ($this->aauth->is_loggedin()) {
            redirect('/dashboard/', 'refresh');
        }

        $data['response'] = '';
        if ($this->input->get('e')) {
            $data['response'] = 'Invalid username or password!';
        }
        $this->load->view('user/header');
        $this->load->view('user/forgot', $data);
        $this->load->view('user/footer');
    }

    public function send_reset()
    {
        if ($this->aauth->is_loggedin()) {
            redirect('/dashboard/', 'refresh');
        }

        $data['response'] = '';
        $mobile_number = $this->normalizeMobileNumber($this->input->post('mobile', true));

        if (empty($mobile_number)) {
            echo json_encode(array('status' => 'Error', 'message' => 'Registered mobile number is required!'));
            return;
        }

        $user = $this->db->select('id, username, mobile')
            ->where('mobile', $mobile_number)
            ->get('users')
            ->row_array();

        if (empty($user)) {
            echo json_encode(array('status' => 'Error', 'message' => 'Mobile number not found in our records!'));
            return;
        }

        $otp = (string)random_int(100000, 999999);

        $send_result = $this->sendWhatsAppViaAiSensy($mobile_number, $this->aisensy_reset_otp_campaign, ["User", "OTP: " . $otp]);
        if (!$send_result['success']) {
            echo json_encode(array('status' => 'Error', 'message' => 'Unable to send OTP: ' . $send_result['message']));
            return;
        }

        $this->db->where('id', $user['id'])->update('users', array('verification_code' => $otp));

        $link = base_url('user/reset_pass?mobile=' . urlencode($mobile_number));
        echo json_encode(array('status' => 'Success', 'message' => 'OTP sent on WhatsApp. <a href="' . $link . '" class="btn btn-indigo btn-md">Reset Password</a>'));
    }

    public function reset_pass()
    {
        if ($this->aauth->is_loggedin()) {
            redirect('/dashboard/', 'refresh');
        }
        $data['code'] = $this->input->get('code', true);
        $data['email'] = $this->input->get('email', true);
        $data['mobile'] = $this->normalizeMobileNumber($this->input->get('mobile', true));

        $data['response'] = '';
        if ($this->input->get('e')) {
            $data['response'] = 'Invalid username or password!';
        }
        if ($this->input->get('k')) {
            $this->load->model('general_model', 'general');
            $this->general->reset($this->input->get('k'));
        }
        $this->load->view('user/header');
        $this->load->view('user/reset', $data);
        $this->load->view('user/footer');
    }

    public function reset_change()
    {
        if ($this->aauth->is_loggedin()) {
            redirect('/dashboard/', 'refresh');
        }

        $password = $this->input->post('n_password');
        $code = $this->input->post('n_code', true);
        $email = $this->input->post('email', true);
        $mobile = $this->normalizeMobileNumber($this->input->post('mobile', true));

        if (strlen($password) > 5) {
            if (!empty($mobile)) {
                $user = $this->db->where('mobile', $mobile)
                    ->where('verification_code', $code)
                    ->get('users')
                    ->row();

                if ($user) {
                    $data = array(
                        'verification_code' => '',
                        'password' => $this->aauth->hash_password($password, $user->id)
                    );
                    $this->db->where('id', $user->id)->update('users', $data);
                    echo json_encode(array('status' => 'Success', 'message' => "Password Changed Successfully! Redirecting to login...", 'redirect' => base_url('user')));
                } else {
                    echo json_encode(array('status' => 'Error', 'message' => "Invalid or expired OTP! <a href='" . base_url('user/forgot') . "' class='btn btn-blue btn-md'>Retry</a>"));
                }
                return;
            }

            $out = $this->aauth->reset_password($email, $code, $password);
            if ($out) echo json_encode(array('status' => 'Success', 'message' => "Password Changed Successfully! Redirecting to login...", 'redirect' => base_url('user')));
            else echo json_encode(array('status' => 'Error', 'message' => "Code Expired! <a href='" . base_url() . "' class='btn btn-blue btn-md'><span class='fa fa-home' aria-hidden='true'></span> " . $this->lang->line('Login') . "  </a>"));
        } else {
            echo json_encode(array('status' => 'Error', 'message' => "Password must be at least 6 characters long!"));
        }


        $data['response'] = '';
        if ($this->input->get('e')) {
            $data['response'] = 'Invalid username or password!';
        }

    }

    public function logout()
    {
        $this->aauth->applog('[Logged Out] ' . $this->aauth->get_user()->username);
        $this->aauth->logout();

        redirect('/user/', 'refresh');

    }

    public function salary()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('/user/', 'refresh');
        }
        $id = $this->aauth->get_user()->id;
        $head['usernm'] = $this->aauth->get_user()->username;
        $head['title'] = $head['usernm'] . ' salary ';
        $this->load->model('employee_model', 'employee');
        $id = $this->aauth->get_user()->id;
        $data['employee_salary'] = $this->employee->salary_view($id);
        $data['employee'] = $this->employee->employee_details($id);
        $this->load->view('fixed/header', $head);
        $this->load->view('user/salary', $data);
        $this->load->view('fixed/footer');
    }

}
