/*Adding validator function*/
$.validator.addMethod("mobile", function (value, element) {
	return this.optional(element) || value.match(/^[7-9][0-9]*$/);
}, "Please enter valid mobile number");

$.validator.addMethod("alphabets", function (value, element) {
	return this.optional(element) || /^[a-zA-Z ]*$/.test(value);
}, "Please enter Alphabets only");

$.validator.addMethod("email", function (value, element) {
	return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
}, "Please enter a valid email address.");

$.validator.addMethod("intlTelNumber", function(value, element) {
    return this.optional(element) || $(element).intlTelInput("isValidNumber");
}, "Please enter a valid mobile number");
/*Adding validator function*/

$.validator.addMethod("radio", function(value, element) {
   var isChecked = jQuery("input[name=gender]:checked").val();
     if(!isChecked){
         return false;
     }else{
         return true;
     }
}, "Please select your gender");

$('#current_opening').validate({  
	rules: {
		applicant_fname: {
			required: true,
			alphabets: true,
			maxlength: 100
		},
		applicant_lname: {
			required: true,
			alphabets: true,
			maxlength: 100
		},
		applicant_mobile: {
			required: true,
			number: true
		},
		applicant_phone: {
			number: true
		},
		applicant_email: {
			required: true,
			email : true
		},
		applicant_address: {
			required: true
		},
		gender:{
			radio: true
		},
		resume_file:{
			required: true,
			accept: 'docx|doc|pdf'
		}
		
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		
		var file_data = $('#resume_file').prop('files')[0];   
		var form_data = new FormData();                  
		form_data.append('resume_file', file_data);
		form_data.append('applicant_fname', $('#applicant_fname').val());
		form_data.append('applicant_lname', $('#applicant_lname').val());
		form_data.append('gender', $('#gender').val());
		form_data.append('applicant_mobile', $('#applicant_mobile').val());
		form_data.append('applicant_phone', $('#applicant_phone').val());
		form_data.append('applicant_email', $('#applicant_email').val());
		form_data.append('applicant_address', $('#applicant_address').val());
		form_data.append('job_id', $('#applicant_position').val());

		var resume_file;

		$.ajax({
				url: baseUrl+'job-applications',
				type: 'POST',
				dataType: 'text', 
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,
				success: function (response) {
					if(response=='success'){
						$('input[type=text]').val('');
						$('input[type=email]').val(''); 
						$('#applicant_address').val('');
						$('input[type=radio]').val(''); 
						$("#resume_file").val("");  
						$("#message_for_applicant").html("Application has been successfully submitted");
						$("#message_for_applicant").addClass("message_success");
					}else{
						$('input[type=text]').val('');
						$('input[type=email]').val(''); 
						$('#applicant_address').val('');
						$('input[type=radio]').val('');  
						$("#resume_file").val("");
						$("#message_for_applicant").addClass("error_msg");
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
						$('input[type=text]').val('');
						$('input[type=email]').val(''); 
						$('#applicant_address').val('');
						$('input[type=radio]').val('');  
						$("#resume_file").val("");
						$("#message_for_applicant").html("Some error occurred.");
						$("#message_for_applicant").addClass("error_msg");
				}
			});
		
	}
	
});


$('#partner_form').validate({
//$("#document_file").hide();  
	rules: {
		partner_name: {
			required: true,
			alphabets: true,
			maxlength: 100
		},
		identity: {
			required: true
		},
		partner_email_id: {
			required: true,
			email : true
		},
		partner_mobile_no: {
			required: true
		},
		partner_telephone: {
			required: true
		},
		poperty_title: {
			required: true
		},
		property_country: {
			required: true
		},
		property_state: {
			required: true
		},
		property_city: {
			required: true
		},
		property_locality: {
			required: true
		},
		fsi_potentials: {
			required: true
		},
		fsi_potentials_measurement: {
			required: true
		},
		size_of_property: {
			required: true
		},
		size_of_property_measurement: {
			required: true
		},
		nature_of_transaction: {
			required: true
		},
		document_file:{
			required: true,
			accept: 'docx|doc|pdf|jpeg|jpg|png'
		},
		proposal: {
			required: true
		}
		
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {

		 $("#form_next").val('1');
		 var next_level = $("#form_next").val();
			
		if(next_level==1){

			$("#next").hide();
			$(".hidden-field").show();
			$("#submit_partner").show();
			// $("#form_submit").val('1');
			
		}
		var next_page = $("#form_submit").val();
		if(next_page == 1){
			form.submit();
		}
	}
});

$('#franchiseeForm').validate({  
	rules: {
		name: {
			required: true,
			alphabets: true,
			minlength: 3,
			maxlength: 100
		},
		email: {
			required: true,
			email: true
		},
		phone: {
			required: true,
			intlTelNumber: true
		}/*,
		profession: {
			required: true
		},
		industry: {
			required: true
		},
		loc: {
			required: true
		}*/
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		form.submit();
	}
});

$('#contact_us').validate({  
	rules: {
		name: {
			required: true,
			alphabets: true,
			minlength: 3,
			maxlength: 100
		},
		email: {
			required: true,
			email: true
		},
		phone: {
			required: true,
			intlTelNumber: true
		},
		message: {
			required: true,
			minlength: 7
		}
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		form.submit();
	}
});

$('#franchisee_contact').validate({  
	rules: {
		fname: {
			required: true,
			alphabets: true,
			minlength: 3
		},
		lname: {
			required: true,
			alphabets: true,
			minlength: 3
		},
		email: {
			required: true,
			email: true
		},
		phone: {
			required: true,
			intlTelNumber: true
		},
		'interest[]': {
			required: true
		}
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		form.submit();
		/*var full_name = $("#fname").val()+' '+$("#lname").val();
		var email_id = $("#email").val();
		var phone = $("#phone").val();
		var interest = '';
		$('.interest:checked').each(function () {
            interest = $(this).val();
            return false;
       }); 

		var name = full_name;
		var email = email_id;
		var mobile = phone;
		var type = "Query - "+interest; // other options "Query - Buying", "Query - Renting", "Query - Selling" based on check box selected
		var agentName = "Hemant Vyas";
		var campaign="Online"
		var source="Website"


		var data = {OBJECT_TYPE:'message',OBJECT:{campaign: campaign_name,source:source, status:type,assign_to:agentName,contact:{name:name,email:email,mobile:mobile}}};
		$.ajax({
			type: "POST",
			url: "http://bizhub.coldwellbanker.in/API",
			data:{APIK:'75744a696857745070386563596a56676336536575513d3d',Action:'ADD',Data:JSON.stringify(data)},
			cache: false,
			crossDomain: true,
			dataType: 'jsonp',
			success: function (response) {
				form.submit();
			},
			error: function (response) {}

		});*/
	}
});

$('#enquire_popup').validate({  
	rules: {
		name: {
			required: true,
			alphabets: true,
			minlength: 3
		},
		email: {
			required: true,
			email: true
		},
		phone: {
			required: true,
			intlTelNumber: true
		},
		'interest[]': {
			required: true
		}
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		var frm = $("#enquire_popup");
		var form_data = frm.serialize();
		//console.log(form_data);
		$.ajax({
			url: baseUrl+'contact-enquiry-popup',
			type: 'POST',
			//cache: false,
			data: form_data,
			success: function (response) {
				console.log("response"+response);
				if(response == '1'){
					frm[0].reset();
					$("#enquire_popup_response").html('Your enquiry has been successfully submitted!');
					$("#enquire_popup_response").show()
				}else{
					$("#enquire_popup_response").html('Error occurred');
					$("#enquire_popup_response").show()
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert('Some error occurred');
			}
		});
	}
});

$('#subscriberForm').validate({  
	rules: {
		email: {
			required: true,
			email: true
		}
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		form.submit();
	}
});
$('#nri_enquiry').validate({
	rules: {
		name: {
			required: true,
			alphabets: true,
			maxlength: 100
		},
		email: {
			required: true,
			email: true
		},
		phone: {
			required: true,
			intlTelNumber: true
		}
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		var frm = $("#nri_enquiry");
		var form_data = frm.serialize();
		$.ajax({
			url: baseUrl+'nri-enquiry',
			type: 'POST',
			data: form_data,
			success: function (response) {
				if(response=='Success'){
					$("#message").html('Your enquiry has been successfully submitted!');
					setTimeout(function(){$('.lead-form-wrp').removeClass('active');$("#message").html('');},10000);
					$('input[type="text"]').val('');
					$('textarea').val('');
					
				}
				
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert('Some error occurred');
			}
		});
	}
});


/*$('#agent_enquire_form').validate({
	rules: {
		fname: {
			required: true,
			alphabets: true,
			maxlength: 100
		},
		lname: {
			required: true,
			alphabets: true,
			maxlength: 100
		},
		email: {
			required: true,
			email: true
		},
		phone: {
			required: true,
			intlTelNumber: true
		},
		'interest[]': {
			required: true
		}
	},
	onkeyup: false,
	errorPlacement: function(error, element) {},
	submitHandler: function(form) {
		var frm = $("#agent_enquire_form");
		var form_data = frm.serialize();
		//console.log(form_data);
		$("#index_submit").prop('disabled', true);
		$.ajax({
			url: baseUrl+'home/contact_enquiry_popup',
			type: 'POST',
			//cache: false,
			data: form_data,
			success: function (response) {
				console.log("response"+response);
				if(response == '1'){
					frm[0].reset();
					$("#enquire_popup_response1").html('Your enquiry has been successfully submitted!');
					$("#enquire_popup_response1").show()
				}else{
					$("#enquire_popup_response1").html('Error occurred');
					$("#enquire_popup_response1").show()
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				alert('Some error occurred');
			}
		});
	}
});*/

