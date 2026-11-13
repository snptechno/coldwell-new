<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo esc($title ?? 'Coldwell || Banker'); ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="robots" content="noindex, nofollow">
  <?= csrf_meta() ?>
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/datatables/dataTables.bootstrap.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/dist/css/skins/_all-skins.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/iCheck/flat/blue.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/iCheck/all.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/morris/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/datepicker/datepicker3.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

  <!-- jQuery 2.2.3 -->
  <script src="<?php echo base_url(); ?>assets/admin/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script>
    $.ajaxSetup({
      headers: {
        '<?= esc(config('Security')->headerName, 'js') ?>': '<?= esc(csrf_hash(), 'js') ?>'
      }
    });
  </script>
</head>
