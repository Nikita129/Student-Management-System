jQuery(function(){

    // bind datatable with list table

    let table = new DataTable('#tbl-student-table');

    // add media model
    jQuery("#btn-upload-profile").on("click", function(event){
      
        event.preventDefault();

        // create media instance

        let mediaUploader = wp.media({
            title: "Select Profile Image",
            multiple: false
        })

        // select image handle

        mediaUploader.on("select", function(){

            let attachment = mediaUploader.state().get("selection").first().toJSON();
            console.log(attachment);
            jQuery("#profile-url").val(attachment.url);
        });

        // open media model

        mediaUploader.open();

        
    });

    // Confirmation before plugin deactivate
    jQuery("#deactivate-student-management-system").on("click", function(event){
        event.preventDefault();

        var deleteConfirm = confirm("Are you sure want to deactivate?");

        if(deleteConfirm)
            {
                window.location.href = jQuery(this).attr("href");
            }
    });

//     // Form submit code with ajax
    jQuery("#btn-sms-form").on("click", function(event){

        event.preventDefault();

        // Ajax Request

        var editorContent = tinymce.get("sms_bio_editor").getContent();
        
        var formData = jQuery("#frm-sms-form").serialize() + "&action=sms_ajax_handler&param=save_form&bio=" + editorContent;
        
        jQuery.ajax({
            url: sms_ajax_url,
            data: formData,
            method: "POST",
            success: function(response){
                // success response

                var data = jQuery.parseJSON(response);

                console.log(response);

                if(data.status)
                {
                    toastr.success(data.message);

                    setTimeout(function(){
                        location.reload()
                    }, 1000);
                }
                else{
                    toastr.error(data.message);

                    setTimeout(function(){
                        location.reload()
                    }, 1000);
                }

            },
            error: function(response){
                // error
                console.log(response);
            }
        })
    });

    if(jQuery("#tbl-student-table").length > 0)
    {
        load_students();
    }


function load_students()
{

    var formData = "&action=sms_ajax_handler&param=load_students";

    var studentHTML = "";

    jQuery("#tbl-student-table").DataTable().destroy();

    jQuery.ajax({
        url: sms_ajax_url,
        data: formData,
        method: "GET",
        success: function(response){
            
                var data = jQuery.parseJSON(response);

                if(data.status)
                {
                    // We have students

                    jQuery.each(data.data, function(index, student){
                        studentHTML += "<tr>";
                        studentHTML += "<td>"+ student.id +"</td>";
                        studentHTML += "<td>"+ student.name +"</td>";
                        studentHTML += '<td> <img src="'+ student.profile_pic+'" height="200px"> </td>';
                        studentHTML += "<td>"+ student.email +"</td>";
                        studentHTML += "<td>"+ student.gender +"</td>";
                        studentHTML += "<td>"+ student.phno +"</td>";
                        studentHTML += "<td>"+ student.profile_bio +"</td>";
                        studentHTML += '<td><a href="admin.php?page=student-system&action=edit&id='+ student.id +'" class="btn-edit">Edit</a> <a href="admin.php?page=student-system&action=view&id='+ student.id +'>" class="btn-view">View</a> <a href="admin.php?page=student-system" class="btn-delete btn-student-delete" data-id='+ student.id +'>Delete</a></td>'
                        studentHTML += "</tr>";

                    })

                   jQuery("#tbl-student-table tbody").html(studentHTML);
                   jQuery("#tbl-student-table").DataTable()
                }
        },
        error: function(){

        }
    });

    jQuery(document).on("click",".btn-student-delete", function(){

        if(confirm("Are you sure want to delete?"))
        {
            var student_id = jQuery(this).attr("data-id");

            var formData = "&action=sms_ajax_handler&param=delete_student&student_id="+student_id;

            jQuery.ajax({
                url: sms_ajax_url,
                data: formData,
                method: "POST",
                success: function(response){

                    var data = jQuery.parseJSON(response);

                    toastr.success(data.message);

                    setTimeout(function(){
                        location.reload()
                    }, 3000);
                },
                error: function(){

                }
            })
        }
        
    });
}
});
