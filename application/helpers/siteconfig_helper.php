<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cached seller check — call before building DataTables queries.
 * ion_auth shares CI's query builder; calling is_seller() mid-query corrupts SQL.
 */
if (!function_exists('ensure_ion_auth')) {
    function ensure_ion_auth()
    {
        $ci =& get_instance();
        if (!isset($ci->ion_auth)) {
            if (!class_exists('Ion_auth', FALSE)) {
                require_once APPPATH . 'libraries/Ion_auth.php';
            }
            $ci->ion_auth = new Ion_auth();
        }
        return $ci->ion_auth;
    }
}

if (!function_exists('is_seller_user')) {
    function is_seller_user()
    {
        $ci =& get_instance();
        if (isset($ci->session)) {
            if ($ci->session->userdata('admin_switched_seller_id')) {
                return true;
            }
            if ($ci->session->userdata('usertype') == 4) {
                return true;
            }
        }
        return false;
    }
}

/**
 * The site's own business/company details (Settings -> Company), for the invoice
 * masthead/top header. Always the admin's own business, never a seller's -- who is
 * logged in / which seller owns the document does not matter here.
 * Shape matches location()'s row plus 'logo_path' (webroot-relative logo file path).
 */
if (!function_exists('invoice_company_details')) {
    function invoice_company_details($loc = 0)
    {
        $loc_data = location($loc);
        $loc_data['logo_path'] = 'userfiles/company/' . $loc_data['logo'];
        $loc_data['is_seller'] = false;
        $loc_data['bank_name'] = '';
        $loc_data['bank_code'] = '';
        $loc_data['account_name'] = '';
        $loc_data['account_number'] = '';
        return $loc_data;
    }
}

/**
 * The specific seller's store name/logo/address/tax/bank info for the "Seller"
 * section of an invoice -- the seller who actually owns the order/product/quote
 * being printed, looked up by that seller's users.id (NOT the current session's
 * identity). Returns null when $seller_user_id is empty or isn't a seller with a
 * seller_data row, so callers can fall back to invoice_company_details().
 */
if (!function_exists('invoice_seller_branding')) {
    function invoice_seller_branding($seller_user_id)
    {
        if (empty($seller_user_id)) {
            return null;
        }
        $ci =& get_instance();
        $ci->load->database();
        $seller = $ci->db->select('sd.store_name, sd.logo, sd.tax_number, sd.bank_name, sd.bank_code, sd.account_name, sd.account_number, u.address, u.mobile, u.email')
            ->from('seller_data sd')
            ->join('users u', 'u.id = sd.user_id')
            ->where('sd.user_id', (int)$seller_user_id)
            ->get()->row_array();
        if (empty($seller)) {
            return null;
        }
        return array(
            'cname' => $seller['store_name'],
            'address' => $seller['address'],
            'city' => '',
            'region' => '',
            'country' => '',
            'postbox' => '',
            'phone' => $seller['mobile'],
            'email' => $seller['email'],
            'taxid' => $seller['tax_number'],
            'logo' => $seller['logo'],
            'logo_path' => $seller['logo'],
            'foundation' => '',
            'is_seller' => true,
            'bank_name' => $seller['bank_name'],
            'bank_code' => $seller['bank_code'],
            'account_name' => $seller['account_name'],
            'account_number' => $seller['account_number'],
        );
    }
}

function dateformat($input)
{
    $ci =& get_instance();
    $date = new DateTime($input);
    $date = $date->format($ci->config->item('dformat'));
    return $date;
}

function assets_url($input = '')
{
    return base_url($input);
}

function dateformat_time($input)
{
    $ci =& get_instance();
    $date = new DateTime($input);
    $date = $date->format($ci->config->item('dformat') . ' H:i:s');
    return $date;
}

function datefordatabase($input)
{
    if (empty($input)) {
        return null; // या date("Y-m-d H:i:s") default date के लिए
    }
    
    $date = new DateTime($input);
    return $date->format('Y-m-d H:i:s');
}

function timefordatabase($input)
{

    $time = new DateTime($input);
    $time = $time->format('H:i:s');
    return $time;
}

function user_role($id = 5)
{
    $ci =& get_instance();
    switch ($id) {
        case 5:
            return $ci->lang->line('Business Owner');
            break;
        case 4:
            return $ci->lang->line('Business Manager');
            break;
        case 3:
            return $ci->lang->line('Sales Manager');
            break;
        case 2:
            return $ci->lang->line('Sales Person');
            break;
        case 1:
            return $ci->lang->line('Inventory Manager');
            break;
        case -1:
            return $ci->lang->line('Project Manager');
            break;
    }
}

function amountFormat($number)
{
    $ci =& get_instance();
    $query = $ci->db->query("SELECT currency FROM geopos_system WHERE id=1 LIMIT 1");
    $row = $query->row_array();
    $currency = $row['currency'];
    //get data from database
    $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
    $row = $query2->row_array();
    //Format money as per country
    if ($row['method'] == 'l') {
        return $currency . ' ' . @number_format($number, $row['url'], $row['key1'], $row['key2']);
    } else {
        return @number_format($number, $row['url'], $row['key1'], $row['key2']) . ' ' . $currency;
    }

}

function prefix($number)
{
    $ci =& get_instance();
    $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=51 LIMIT 1");
    $row = $query2->row_array();
    //Format money as per country
    switch ($number) {
        case 1:
            return $row['name'];
            break;
        case 2:
            return $row['key1'];
            break;
        case 3:
            return $row['key2'];
            break;
        case 4:
            return $row['url'];
            break;
        case 5:
            return $row['method'];
            break;
        case 6:
            return $row['other'];
            break;
        case 7:
            $query2 = $ci->db->query("SELECT other FROM univarsal_api WHERE id=52 LIMIT 1");
            $row = $query2->row_array();
            return $row['other'];
            break;
    }
}

function user_premission($input1, $input2)
{
    if (hash_equals($input1, $input2)) {
        return true;
    } else {
        return false;
    }
}


function amountFormat_s($number)
{
    $ci =& get_instance();
    $ci->load->database();
    //get data from database
    $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
    $row = $query2->row_array();
    //Format money as per country

    return @number_format($number, $row['url'], $row['key1'], $row['key2']);

}

function amountFormat_general($number=0)
{
    $ci =& get_instance();
    $ci->load->database();
    //get data from database
    $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
    $row = $query2->row_array();
    //Format money as per country
    $number = @number_format($number, $row['url'], $row['key1'], '');
    return $number;
}

function numberClean($number)
{
    $ci =& get_instance();
    $ci->load->database();
    $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
    $row = $query2->row_array();
    $number = str_replace($row['key2'], "", $number);
    $number = str_replace($row['key1'], ".", $number);
    return (float)$number;
}


function amountExchange($number, $id = 0, $loc = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    if ($loc > 0 && $id == 0) {
        $query = $ci->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
        $row = $query->row_array();
        $id = $row['cur'];
    }
    if ($id > 0) {
        $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
        $row = $query->row_array();
        $currency = $row['symbol'];
        $rate = $row['rate'];
        $thosand = $row['thous'];
        $dec_point = $row['dpoint'];
        $decimal_after = $row['decim'];
        $totalamount = $rate * $number;
        //get data from database
        //Format money as per country
        if ($row['cpos'] == 0) {
            return $currency . ' ' . @number_format($totalamount, $decimal_after, $dec_point, $thosand);
        } else {
            return @number_format($totalamount, $decimal_after, $dec_point, $thosand) . ' ' . $currency;
        }
    } else {

        $query = $ci->db->query("SELECT currency FROM geopos_system WHERE id=1 LIMIT 1");
        $row = $query->row_array();
        $currency = $row['currency'];

        //get data from database
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();
        //Format money as per country
        if ($row['method'] == 'l') {
            return $currency . ' ' . @number_format($number, $row['url'], $row['key1'], $row['key2']);
        } else {
            return @number_format($number, $row['url'], $row['key1'], $row['key2']) . ' ' . $currency;
        }
    }

}

function amountExchange_s($number, $id = 0, $loc = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    if ($loc > 0 && $id == 0) {
        $query = $ci->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
        $row = $query->row_array();
        $id = $row['cur'];
    }
    if ($id > 0) {
        $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
        $row = $query->row_array();
        $rate = $row['rate'];
        $dec_point = $row['dpoint'];
        $totalamount = $rate * $number;
		$decimal_after = $row['decim'];
        $totalamount = number_format($totalamount, $decimal_after, $dec_point, '');
        return $totalamount;
    } else {
        $query = $ci->db->query("SELECT currency FROM geopos_system WHERE id=1 LIMIT 1");
        $row = $query->row_array();
        $currency = $row['currency'];
        //get data from database
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();
        $number = number_format($number, $row['url'], $row['key1'], '');
        return $number;
    }

}

function edit_amountExchange_s($number, $id = 0, $loc = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    if ($loc > 0) {
        $query = $ci->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
        $row = $query->row_array();
        $id = $row['cur'];
    }
    if ($id > 0) {
        $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
        $row = $query->row_array();
        $rate = $row['rate'];
        $decimal_after = $row['decim'];
        $dec_point = $row['dpoint'];
        $number = str_replace($decimal_after, "", $number);
        $number = str_replace($dec_point, ".", $number);
        $totalamount = $rate * (float)$number;
        $totalamount = number_format($totalamount, $decimal_after, $dec_point, '');
        return $totalamount;
    } else {
        $query = $ci->db->query("SELECT currency FROM geopos_system WHERE id=1 LIMIT 1");
        $row = $query->row_array();
        $currency = $row['currency'];
        //get data from database
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();
       // $number = str_replace($row['key2'], "", $number);
        //$number = str_replace($row['key1'], ".", $number);
        $number = number_format($number, $row['url'], $row['key1'], '');
        return $number;
    }

}

function rev_amountExchange_s($number, $id = 0, $loc = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    $query2 = $ci->db->query("SELECT other FROM univarsal_api WHERE id=5 LIMIT 1");
    $row = $query2->row_array();
    $revers = $row['other'];

    if ($loc) {
        $query = $ci->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
        $row = $query->row_array();
        $lcid = $row['cur'];
        if ($lcid > 0) {
            $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$lcid' LIMIT 1");
            $row = $query->row_array();
			if($row['id']){
            $rate = $row['rate'];
            $number = str_replace($row['thous'], "", $number);
            $number = str_replace($row['dpoint'], ".", $number);
            $number = (float)$number / $rate;
			}
			else {
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();
        $number = str_replace($row['key2'], "", $number);
        $number = str_replace($row['key1'], ".", $number);

    }
        } elseif ($id) {
            $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
            $row = $query->row_array();
            if ($row['id']) {
            $rate = $row['rate'];
            $number = str_replace($row['thous'], "", $number);
            $number = str_replace($row['dpoint'], ".", $number);
            $number = (float)$number / $rate;
            } else {
                $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
                $row = $query2->row_array();
                $number = str_replace($row['key2'], "", $number);
                $number = str_replace($row['key1'], ".", $number);
            }
        }
		else {
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();
        $number = str_replace($row['key2'], "", $number);
        $number = str_replace($row['key1'], ".", $number);

    }
    } elseif ($id) {
        $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
        $row = $query->row_array();
        if ($row['id']) {
        $rate = $row['rate'];
        $number = str_replace($row['thous'], "", $number);
        $number = str_replace($row['dpoint'], ".", $number);
        if (!$revers) {

            $number = (float)$number / $rate;
        }
        } else {
            $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
            $row = $query2->row_array();
            $number = str_replace($row['key2'], "", $number);
            $number = str_replace($row['key1'], ".", $number);
        }
    } else {
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();
        $number = str_replace($row['key2'], "", $number);
        $number = str_replace($row['key1'], ".", $number);

    }

    return (float)$number;
}

function rev_amountExchange($number, $id = 0)
{
    $ci =& get_instance();
    $query = $ci->db->query("SELECT other FROM univarsal_api WHERE id='5' LIMIT 1");
    $row = $query->row_array();
    $reverse = $row['other'];
    if ($reverse && $id > 0) {
        $query = $ci->db->query("SELECT rate FROM geopos_currencies WHERE id='$id' LIMIT 1");
        $row = $query->row_array();
        if (!$row || !$row['rate']) {
            return $number;
        }
        $rate = $row['rate'];
        $totalamount = $number / $rate;
        return $totalamount;
    } else {
        return $number;
    }
}

function array_compare()
{
    $criteriaNames = func_get_args();
    $compare = function ($first, $second) use ($criteriaNames) {
        while (!empty($criteriaNames)) {
            $criterion = array_shift($criteriaNames);
            $sortOrder = 1;
            if (is_array($criterion)) {
                $sortOrder = $criterion[1] == SORT_DESC ? -1 : 1;
                $criterion = $criterion[0];
            }
            if ($first[$criterion] < $second[$criterion]) {
                return -1 * $sortOrder;
            } else if ($first[$criterion] > $second[$criterion]) {
                return 1 * $sortOrder;
            }
        }
        return 0;
    };

    return $compare;
}

function locations()
{
    $ci =& get_instance();
    $ci->load->database();
    $query2 = $ci->db->query("SELECT * FROM geopos_locations");
    return $query2->result_array();
}

function location($number = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    if ($number > 0) {
        $query2 = $ci->db->query("SELECT * FROM geopos_locations WHERE id=$number");
        return $query2->row_array();
    } else {
        $query2 = $ci->db->query("SELECT cname,address,city,region,country,postbox,phone,email,taxid,logo,foundation FROM geopos_system WHERE id=1 LIMIT 1");
        return $query2->row_array();
    }
}

function active($input1)
{

    $t_file = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'lic.php';
    if (is_writeable($t_file)) {
        file_put_contents($t_file, $input1);
        $lc = file_get_contents($t_file);
        if (empty($lc)) {
            echo json_encode(array('status' => 'WError', 'message' => 'Server write permissions denied'));
        } else {
            if ($input1 == 2) {
                echo json_encode(array('status' => 'Error', 'message' => 'License error!'));
            } else {
                echo json_encode(array('status' => 'Success', 'message' => 'License updated!'));
            }
        }
    } else {
        echo json_encode(array('status' => 'WError', 'message' => 'Server write permissions denied!'));
    }

}

function currency($loc = 0, $id = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    if ($loc > 0 && $id == 0) {
        $query = $ci->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
        $row = $query->row_array();
        $id = $row['cur'];
    }
    if ($id > 0) {
        $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
        $row = $query->row_array();
        $currency = $row['symbol'];
    } else {
        $query = $ci->db->query("SELECT currency FROM geopos_system WHERE id=1 LIMIT 1");
        $row = $query->row_array();
        $currency = $row['currency'];
    }
    return $currency;
}

function plugins_checker()
{
    $path = FCPATH . 'application/plugins';
    $plugins = array_diff(scandir($path), array('.', '..'));
    foreach ($plugins as $row) {
        $url = file_get_contents($path . '/' . $row);
        $plug = json_decode($url, true);
        echo '    <li><a class="dropdown-item"
                                                           href="' . base_url() . $plug['path'] . '"><i
                                                                    class="ft-chevron-right"></i> ' . $plug['name'] . '
                                                        </a></li>';
    }
}

function custom_plugins_checker($name='sms')
{
    $path = FCPATH . 'application'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$name;
      if(file_exists($path)) {


          $plugins = array_diff(scandir($path), array('.', '..'));
          foreach ($plugins as $row) {
              $url = file_get_contents($path . '/' . $row);
              $plug = json_decode($url, true);
              echo '    <li><a class="dropdown-item"
                                                           href="' . base_url() . $plug['path'] . '"><i
                                                                    class="ft-chevron-right"></i> ' . $plug['name'] . '
                                                        </a></li>';
          }
      }
}

function datatable_lang()
{
    $ci =& get_instance();
    $result='';
   $lang= $ci->config->item('mylang');
   $dfile=FCPATH . 'application/language/'.$lang.'/datatable.php';
   if(file_exists($dfile)) $result=include_once($dfile);
    echo $result;
}

function accounting($loc = 0)
{
    $ci =& get_instance();
    $ci->load->database();
    if ($loc > 0) {
        $query = $ci->db->query("SELECT cur FROM geopos_locations WHERE id='$loc' LIMIT 1");
        $row = $query->row_array();
        $id = $row['cur'];
        if ($id > 0) {
            $query = $ci->db->query("SELECT * FROM geopos_currencies WHERE id='$id' LIMIT 1");
            $row = $query->row_array();

            $thosand = $row['thous'];
            $dec_point = $row['dpoint'];
            $decimal_after = $row['decim'];
        }
    } else {
        $query2 = $ci->db->query("SELECT * FROM univarsal_api WHERE id=4 LIMIT 1");
        $row = $query2->row_array();

        $thosand = $row['key2'];
        $dec_point = $row['key1'];
        $decimal_after = $row['url'];
    }

    echo " <script type='text/javascript'>accounting.settings = {number: {precision :$decimal_after,thousand: '$thosand',decimal : '$dec_point'}};
var two_fixed=$decimal_after; </script>";

}

if (!function_exists('dd')) {
    function dd($var)
    {
        print_r($var);
        exit;
    }
 }