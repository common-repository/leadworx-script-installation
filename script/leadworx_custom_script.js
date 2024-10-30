// JavaScript Document
	var set_time = 1500;
	var added_success_msg = "The Leadworx script has been successfully added to your website";
	var deleted_success_msg = "The Leadworx script has been successfully removed from your website";
	setTimeout(function(){ jQuery("#setting-error-settings_updated").hide(); },set_time);
	function leadworxAuthenticate(){
       jQuery('#authenticate_leadworx_credentials').validate();
        if (jQuery('#authenticate_leadworx_credentials').valid()){
           jQuery("#divLoading").addClass("show");
		   var leadworx_email = jQuery("#leadworx_email").val();
		   var leadworx_password = jQuery("#leadworx_password").val();			
		   var data = {
				'action': 'authenticate_leadworx',
				'leadworx_email': leadworx_email,
				'leadworx_password' : leadworx_password
			};
		jQuery.post(ajaxurl, data, function(response) {
			var res = jQuery.trim(response);					
			if(res=='0'){
			 jQuery(".showError").html("Incorrect credentials"); 	
			 setTimeout(function(){ jQuery(".showError").html(""); },3000);					 
			}else{
				jQuery(".hideData").show();
				jQuery(".showData").hide();
				jQuery(".showWebsites").html(res);							
			}
			jQuery("#leadworx_email").val("");
			jQuery("#leadworx_password").val("");
			jQuery("#divLoading").removeClass("show");
		});
        }	   
    }
	jQuery(document).ready(function(e) {
		jQuery("#remove_script").click(function(e){			
            if(confirm('Are you sure you want to remove the script from your website header?')){
				jQuery("#divLoading").addClass("show");
				jQuery(".codeAddedSuccessfully").hide();
				var data = {
					'action': 'remove_leadworx_script',
					'remove_script':'yes'
				};
				jQuery.post(ajaxurl, data, function(response){
					setTimeout(function(){ window.location.reload(); 
						jQuery("#setting-error-settings_updated").show();
						jQuery("#setting-error-settings_updated p strong").html(deleted_success_msg);
						},set_time);
						jQuery("#divLoading").removeClass("show");
				});	
			}
        });		
	jQuery("#save_select_website").click(function(e){
	   	jQuery("#divLoading").addClass("show");	
	     var website_url = jQuery(".copyscriptinwphead option:selected").text();
		 var website_id = jQuery(".copyscriptinwphead option:selected").attr("website_id");
		 var website_script = jQuery(".copyscriptinwphead").val();
				var data = {
					'action': 'add_website_script',
					'website_script':website_script,
					'website_url':website_url,
					'website_id':website_id
				};
				jQuery.post(ajaxurl, data, function(response){
					jQuery(".showData").hide();
						jQuery(".hideData").show();
						jQuery(".showScriptData").show();
						jQuery(".showWebsitesData").hide();
						jQuery(".codeAddedSuccessfullyAjax").html(response);						
						jQuery("#divLoading").removeClass("show");
						jQuery("#setting-error-settings_updated").show();
						jQuery("#setting-error-settings_updated p strong").html(added_success_msg);
						setTimeout(function(){ jQuery("#setting-error-settings_updated").hide(); },set_time);
				});
    });
	jQuery(".leadworx_authenticate").click(function(e) {			
		leadworxAuthenticate();
			return false;
	});
    jQuery('#leadworx_email').keypress(function(e){
  		if(e.keyCode == 13){
			leadworxAuthenticate();
			return false;
  		}
	});
	jQuery('#leadworx_password').keypress(function(e){
  		if(e.keyCode == 13){
			leadworxAuthenticate();
			return false;
  		}
	});
	jQuery(".addscripthead_direct_btn").click(function(e) {
            jQuery('#addscripthead_direct_frm').validate();
        	if (jQuery('#addscripthead_direct_frm').valid()){				
				jQuery('#addscripthead_direct_frm').submit();	
			}
   });    
   jQuery(".updatescript_btn").click(function(e) {
            jQuery('.addscripthead_frm').validate();
        	if (jQuery('.addscripthead_frm').valid()){
				jQuery('.addscripthead_frm').submit();	
			}
   });		
});