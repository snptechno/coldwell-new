<meta charset="utf-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, user-scalable=0">
<?php if (($page_active ?? '') === 'home'): ?>
<link rel="preload" as="image" href="<?php echo base_url(); ?>assets/images/main_banner.webp" type="image/webp" media="(min-width: 768px)" fetchpriority="high">
<link rel="preload" as="image" href="<?php echo base_url(); ?>assets/images/main_banner-mobile.webp" type="image/webp" media="(max-width: 767px)" fetchpriority="high">
<?php else: ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
<?php endif; ?>
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.ico">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/reset.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.sliding_menu.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/owl.carousel.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/fonts.css?v=<?= filemtime(FCPATH . 'assets/css/fonts.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/animate.css"> 
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/custom_responsive.css?v=<?= filemtime(FCPATH . 'assets/css/custom_responsive.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.mCustomScrollbar.css">

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/fancybox.css" />
<link href="<?php echo base_url(); ?>assets/build/css/intlTelInput.css" rel="stylesheet">

<script>
var baseUrl = '<?php echo base_url(); ?>';
</script>
<?= $this->include('partials/tracking') ?>

