jQuery(function(){

    console.log("Frontend js");

    jQuery("#shortcode_btn_submit").on("click", function(event){

        event.preventDefault();

        var formData = jQuery("#shortcode_form").serialize() + "&action=sms_ajax_handler&param=frontend_form";

        jQuery.ajax({
            url: sms_ajax_url,
            method: "POST",
            data: formData,
            success:function(response){
                
                var data = jQuery.parseJSON(response);

                toastr.success(data.message);

                setTimeout(function(){
                    window.location.reload()
                }, 2000);
            }, 
            error:function(response){
                
            }
        })
    })
})