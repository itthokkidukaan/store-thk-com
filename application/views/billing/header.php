<!DOCTYPE html>
<html lang="en" data-textdirection="<?= defined('LTR') ? LTR : 'ltr' ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title><?= isset($title) && $title ? $title : 'Quote' ?></title>
    <link rel="apple-touch-icon" href="<?= assets_url() ?>app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= assets_url() ?>app-assets/images/ico/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/<?= defined('LTR') ? LTR : 'ltr' ?>/vendors.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>app-assets/<?= defined('LTR') ? LTR : 'ltr' ?>/app.css">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/css/style.css<?= defined('APPVER') ? APPVER : '' ?>">
    <link rel="stylesheet" type="text/css" href="<?= assets_url() ?>assets/admin/css/iziToast.min.css">
    <script src="<?= assets_url() ?>app-assets/vendors/js/vendors.min.js"></script>
</head>
<body style="background:#f4f6f9;">
