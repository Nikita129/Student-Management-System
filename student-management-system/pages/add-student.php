<div class="add-card">
    <h2>

    <?php 
        if(isset($action) && $action == "edit")
        {
            $nonce = wp_create_nonce("wp_nonce_add_student");
            echo "Edit Student Detail";
        }
        elseif(isset($action) && $action == "view")
        {
            echo "View Student Detail";
        }
        else{

            $nonce = wp_create_nonce("wp_nonce_add_student");

            echo "Add Student Detail";

            $action = "add";
        }
    ?>
    </h2>
    
        <?php if(!empty($displayMessage) && $displayStatus){?>
            <div class="alert-success">
                <?php echo $displayMessage;?>
            </div>
        <?php }?>

        <?php if(!empty($displayMessage) && !$displayStatus){?>
            <div class="alert-error">
                <?php echo $displayMessage;?>
            </div>
        <?php }?>


    
    <form class="add-student-form" method="post" action="javascript:void(0);" id="frm-sms-form">

    <input type="hidden" name="wp_nonce_add_student" value="<?php echo $nonce;?>">

    <?php 
                if($action == 'edit')
                {?>
                    <input type="hidden" name="operation_type" value="edit">

                    <input type="hidden" name="student_id" value="<?php echo $student['id']?>">
                <?php } else{ ?>
                    <input type="hidden" name="operation_type" value="add">
                <?php }
            ?>
    
        <!--Name Field-->
        <div class="form-group">
            <?php 
                $save_name_option = get_option("sms_name_validation");
            ?>

           
            <label for="name">Name <?php if(!empty($save_name_option)){echo '<span style="color:red">*</span>';}?></label>
            <input type="text" <?php if(isset($action) && $action == 'view'){echo 'readonly';}?> name="name" placeholder="Enter Name" id="name" value="<?php 
                if(isset($student['name']))
                {
                    echo $student['name'];
                }
            ?>"
            <?php if(!empty($save_name_option)){echo "required";}?>
            >
        </div>

        <!--Email Field-->
        <div class="form-group">
        <?php 
            $save_email_option = get_option("sms_email_validation");
        ?>
            <label for="email">Email <?php if(!empty($save_email_option)){echo '<span style="color:red">*</span>';}?></label>
            <input type="email" <?php if(isset($action) && $action == 'view'){echo 'readonly';}?> name="email" placeholder="Enter Email" id="email" value="<?php 
                if(isset($student['email']))
                {
                    echo $student['email'];
                }
            ?>"
             <?php if(!empty($save_email_option)){echo "required";}?>
            >
           
        </div>

         <!--Gender Field-->
         <div class="form-group">
           <?php $saved_gender_option = get_option("sms_gender_validation"); ?>
            <label for="gender">Gender <?php if(!empty($saved_gender_option)){echo '<span style="color:red"
                >*</span>';}?></label>
            <select name="gender" id="gender" <?php if(isset($action) && $action == 'view') {echo 'disabled';} ?> <?php if(!empty($saved_gender_option)){echo "required";}?>>
                <option>Select Gender</option>
                <option value="male" <?php if(isset($student['gender']) &&  $student['gender'] == 'male'){echo "selected"; } ?>>Male</option>
                <option value="female" <?php if(isset($student['gender']) &&  $student['gender'] == 'female'){echo "selected"; } ?>>Female</option>
                <option value="other" <?php if(isset($student['gender']) &&  $student['gender'] == 'other'){echo "selected"; } ?>>other</option>
            </select>
            
        </div>

         <!--phno Field-->
         <div class="form-group">
         <?php $saved_phno_option = get_option("sms_phno_validation"); ?>
            <label for="phno">Phone No <?php if(!empty($saved_phno_option)){echo '<span style="color:red">*</span>';}?></label>
            <input type="tel" name="phno" <?php if(isset($action) && $action == 'view'){echo 'readonly';}?> placeholder="Enter Phone No." id="phno" value="<?php 
                if(isset($student['phno']))
                {
                    echo $student['phno'];
                }
            ?>"
            <?php if(!empty($saved_phno_option)){echo "required";}?>
            >
        </div>

        <div class="form-group">
        <label for="bio-desc">Bio Description</label>
            <?php 

            // if(isset($student['profile_bio']))
            // {
            //     $content = $student['profile_bio'];
            // }
            // else{
                $content = isset($student['profile_bio']) && !empty($student['profile_bio']) ? $student['profile_bio'] : "";
            //}
            
            $editor_id = "sms_bio_editor"; 
            $args = array(
                'tinymce'       => array(
                    'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2'      => '',
                    'toolbar3'      => '',
                ),
            );
            wp_editor( $content, $editor_id, $args );
            ?>
        </div>
        <!-- Upload Button-->
        <input type="text" name="profile-url" id="profile-url" readonly>

        <button id="btn-upload-profile">Upload Profile Image</button>
        
        <?php 
        if(isset($action) && $action == "view")
        {
            // no button
        }
        else { ?>
                 <button type="submit" name="btn-submit" id="btn-sms-form" class="btn-submit">Submit</button>
        <?php }
        ?>
       
    </form>
</div>