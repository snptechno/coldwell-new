<!doctype html>
<html>

<head>
    <title></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<?= $this->include('partials/header') ?>
	<?= $this->include('partials/ga_code_header') ?>
	<meta name="viewport" content="width=device-width">
	<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
	<script>
function quickpay(){
	var msg='';
	var flg=false;
	if((document.getElementById('name').value=='') || (document.getElementById('name').value=='Name*'))
        {
          msg='You must enter Name\n';
         
		  flg=true;
         // return false;
        }
	if((document.getElementById('email').value=='') || (document.getElementById('email').value=='Email*'))
        {
          msg+='You must enter Email\n';
          
		  flg=true;
         // return false;
        }
		
	if((document.getElementById('amount').value=='') || (document.getElementById('amount').value=='Amount*'))
        {
          msg+='You must enter a valid amount\n';
          
		  flg=true;
         // return false;
        }
	if(flg){
	   	alert(msg);
		return false;
	   }else{
	var amount=document.getElementById('amount').value;
	if(amount==''){
		alert('Please enter a valid amount.');
		amount=0;
	}else{
		amount=amount*100;
	}
	var options = {
		"key": "rzp_live_N8QNJaeZzGa2je",
		"amount":amount, // 2000 paise = INR 20
		"name": "Coldwell Banker",
		"description": "India",
		"image": "http://www.coldwellbanker.in/assets/images/logo.png",
		"handler": function (response){
			alert('Your Payment Id is '+response.razorpay_payment_id);
			document.getElementById('name').value='';
			document.getElementById('email').value='';
			document.getElementById('amount').value='';
		},
		"prefill": {
			"name":document.getElementById('name').value,
			"email": document.getElementById('email').value
		},
		"notes": {
			"address": "Coldwell Banker"
		},
		"theme": {
			"color": "#004990"
		}
	};
	var rzp1 = new Razorpay(options);
	rzp1.open();
	e.preventDefault();
}
}
</script>
</head>

<body>
    <article class="aos-all">
        <section>
            <div class="container">
                <?php $this->setVar('act', 'Quick'); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="">
                <div class="slide-txt">
                    <div class="fr-main-left algn-left">
                      <div class="container contact-sec">
						  <h3>Quick Pay</h3>
							<form id="quick_pay" method="post" action="#" novalidate="novalidate">
<?= csrf_field() ?>
								<input name="name" id="name" placeholder="Name*" type="text">
								<input name="email" id="email" placeholder="Email*" type="text">
								<input name="amount" id="amount" placeholder="Amount*" type="text">
								<input  value="Quick Pay" type="button" onclick="quickpay();">
								<small>*fields are mandatory</small>
							</form>
						</div>
						<div class="mb_25">&nbsp;</div>
                    </div>
                   
                </div>
            </div>
        </section>
        <div class="clear"></div>

		<?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

</body>
</html>