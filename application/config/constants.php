<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 2644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 2666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 2755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 2755);
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 2); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 2); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 225); // highest automatically-assigned error code
require_once('define_pre.php');
define('LTR', 'ltr');
define('GST_INCL', 'yes');
define('POSV', '1');
define('INVV', '1');
define('BDATA', '1');
define('CUSTOM', '1');
define('MENU', '0');
define('PAC', '1');
define('EMPS', '1');


define('NONE', '0');
define('ALL', '1');
define('INCLUDED', '2');
define('EXCLUDED', '3');
defined("WORD_LIMIT") || define("WORD_LIMIT", 12);

// Custom Constant Variables
define('FORMS', 'forms/');
define('ALLOW_MODIFICATION', 1);
define('DEMO_VERSION_MSG', 'Modification in demo version is not allowed');
define('TABLES', 'tables/');
define('VIEW', 'view/');
define('CATEGORY_IMG_PATH', 'uploads/category_image/');
define('SUBCATEGORY_IMG_PATH', 'uploads/subcategory_image/');
define('PRODUCT_IMG_PATH', 'uploads/product_image/');
define('SLIDER_IMG_PATH', 'uploads/slider_image/');
define('OFFER_IMG_PATH', 'uploads/offer_image/');
define('NOTIFICATION_IMG_PATH', 'uploads/notifications/');
define('USER_IMG_PATH', 'uploads/user_image/');
define('UPDATE_PATH', 'update/');
define('MEDIA_PATH', 'uploads/media/');
define('NO_IMAGE', 'assets/no-image.png');
define('EMAIL_ORDER_SUCCESS_IMG_PATH', 'assets/admin/images/order-success.png');
define('REVIEW_IMG_PATH', 'uploads/review_image/');
define('TICKET_IMG_PATH', 'uploads/tickets/');
define('DIRECT_BANK_TRANSFER_IMG_PATH', 'uploads/bank_transfer/');
define('SELLER_DOCUMENTS_PATH', 'uploads/seller/');
define('ORDER_ATTACHMENTS', 'uploads/order_attachments/');

//Thumbnail paths
define('THUMB_MD', 'thumb-md/');
define('THUMB_SM', 'thumb-sm/');
define('CROPPED_MD', 'cropped-md/');
define('CROPPED_SM', 'cropped-sm/');

define('PERMISSION_ERROR_MSG', ' You are not authorize to operate on the module ');
