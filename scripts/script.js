$(document).ready(function(){	
	$('#adminbtn').click(function(){
		$('#admincontent').slideToggle('slow');
	});
	
	$('#cartbtn').click(function(){
		$('#cartcontent').slideToggle('slow');
	});
	
	//Bootstrap tooltip
	$('[data-toggle="tooltip"]').tooltip();
	
	$('#product-name').focus();
	
	$('#username').focus();

	$('#user-name-form').focus();
	
	$('#email-subscribe').focus();
	
//-------------------------Validation with jquery validation plugin-----------------------------------------------
	$.validator.addMethod('lettersOnly', function(value, element){
		return /[a-zA-ZäöüßÄÖÜ]/.test(value);}, 'Endast bokstäver');
	
	$.validator.addMethod('pwdCheck', function(value){
		return /[A-Z]+/.test(value) && /[a-z]+/.test(value) && 
		/[\d\W]+/.test(value) && /\S{7,}/.test(value);}, 'Måste innehålla minst en stor och liten bokstav samt en siffra.');
		
	$.validator.addMethod('filename', function(value, element){
		return /^[\w,\s-]+\.(png|jpg|gif|bmp|jpeg|PNG|JPG|GIF|BMP)$/.test(value);}, 'Endast bokstäver');

	$.validator.addMethod('post_number', function(value, element){
		return /^\d{3} \d{2}$/.test(value);}, 'Ogiltigt postnummer. Exempel: 123 45');
	
	$('#products-admin').validate({
		rules: {			
			product_name:{
				required: true,
				minlength: 3,
				maxlength: 16,
			},
			sales_price:{
				required: true,
				maxlength: 8,
				digits: true
			},
			category:{
				required: true,
				minlength: 3,
				maxlength: 16,
				lettersOnly: true
			},
			original_price:{
				required: true,
				maxlength: 8,
				digits: true
			},
			image_filename:{
				required: true,
				minlength: 3,
				maxlength: 30,
				filename: true
			},
			in_stock:{
				required: true,
				maxlength: 16,
				digits: true
			}
		},
		messages: {
			sales_price:{
				maxlength: "Du får max skriva 8 siffror"
			},
			original_price:{
				maxlength: "Du får max skriva 8 siffror"
			},
			image_filename:{
				maxlength: "Du får max skriva 30 tecken"
			},
			in_stock:{
				maxlength: "Du får max skriva 16 siffror"
			}			
		}
	});	
 
    $(".product-row").each(function(){
		$(this).validate({
			rules: {
				add_quantity:{
				  required: true,
				  maxlength: 16,
				  digits: true
				}
			},
			messages: {
				add_quantity:{
					maxlength: "Du får max skriva 16 siffror"
				}			
			}
		});
    });
  	 
	$('#new-user').validate({
		rules: {			
			username:{
				required: true,
				minlength: 3,
				maxlength: 16,
			},
			password:{
				required: true,
				minlength: 8,
				maxlength: 20,
				pwdCheck: true
			},
			first_name:{
				required: true,
				maxlength: 30,
				lettersOnly: true
			},
			last_name:{
				required: true,
				maxlength: 30,
				lettersOnly: true
			},
			adress:{
				required: true,
				minlength: 3,
				maxlength: 16
			},
			postal:{
				required: true,
				minlength: 6,
				maxlength: 6,
				post_number: true
			},
			city:{
				required: true,
				maxlength: 30,
				lettersOnly: true
			}
		},
		messages: {
			password:{
				minlength: "Du måste skriva minst 8 tecken.",
				maxlength: "Du får skriva max 20 tecken."
			},
			postal:{
				minlength: "Minst 6 tecken",
				maxlength: "Max 6 tecken"
			},
			first_name:{
				maxlength: "Max 30 bokstäver"
			},
			last_name:{
				maxlength: "Max 30 bokstäver"
			},
			city:{
				maxlength: "Max 30 bokstäver"
			}				
		}
	});		
	 	 
	$('#login-form').validate({
		rules: {			
			username:{
				required: true
			},
			password:{
				required: true
			}
		}
	});	

	$( "#subscribe-form" ).validate({
		rules: {
			email: {
			  required: true,
			  email: true
			}
		}
	});		
	
	jQuery.extend(jQuery.validator.messages, {
		required:'Du måste fylla i detta fält.',
		minlength:'Du måste skriva minst 3 tecken.',
		maxlength:'Du får skriva max 16 tecken.',
		lettersOnly:'Du får endast använda bokstäver',
		digits: 'Du får endast skriva ett heltal',
		filename: 'Ogiltigt filnamn/filformat(Giltiga: png,jpg,gif,bmp,jpeg)',
		email: 'Ogiltig email-adress'
	});
});