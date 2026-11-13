
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<style>	
	body {margin: 0}
	.smooth_zoom_preloader {
		background-image: url(../../assets/images/preloader.gif);
	}	
	.smooth_zoom_icons {
		background-image: url(../../assets/images/icons.png);
	}
</style>
<script src="<?php echo base_url();?>assets/js/jquery-1.7.1.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.smoothZoom.min.js"></script>
<script>
	jQuery(function($){
		$('#ZoomObject').smoothZoom({
			width: 800,
			height: 600,
			button_SIZE: 22,
			button_ALIGN: "bottom center",
			zoom_OUT_TO_FIT: "NO",
			button_AUTO_HIDE: "YES",
			button_AUTO_HIDE_DELAY: 2,
			responsive: true,
			responsive_maintain_ratio: true,
			max_WIDTH: '',
			max_HEIGHT: ''
		});
	});
</script>
 
</head>
<body>
<?php if($iframe['image_type']=='news'){?>
	<img id="ZoomObject" src="<?= base_url() . 'uploads/news/' . esc($iframe['image'], 'url') ?>" />
<?php } ?>
<?php if($iframe['image_type']=='knowledge_series'){?>
	<img id="ZoomObject" src="<?= base_url() . 'uploads/knowledge_series/' . esc($iframe['image'], 'url') ?>" />
<?php } ?>	 
<?php if($iframe['image_type']=='industry_news'){?>
	<img id="ZoomObject" src="<?= base_url() . 'uploads/industry_news/' . esc($iframe['image'], 'url') ?>" />
<?php } ?>
<?php if($iframe['image_type']=='newsletter'){?>
	<img id="ZoomObject" src="<?= base_url() . 'uploads/newsletter/' . esc($iframe['image'], 'url') ?>" />
<?php } ?>	
<?php if($iframe['image_type']=='awards'){?>
	<img id="ZoomObject" src="<?= base_url() . 'uploads/awards/' . esc($iframe['image'], 'url') ?>" />
<?php } ?>	
</body>

</html>
