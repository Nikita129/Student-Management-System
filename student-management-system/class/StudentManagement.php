<?php 

class StudentManagement{

    private $message = "";
    private $status = "";

    private $action = "";

    public function __construct()
    {
        add_action("admin_menu",array($this, "addAdminMenus"));
        add_action("admin_enqueue_scripts", array($this, "addStudentPluginFiles"));
        add_action("wp_enqueue_scripts", array($this, "addStudentPluginFilesFrontend"));
        add_filter("plugin_action_links_".SMS_PLUGIN_BASENAME, array($this, "plugin_setting_link"));
        add_action("admin_init", array($this, "sms_plugin_settings"));
        add_action("wp_ajax_sms_ajax_handler", array($this, "sms_ajax_handler"));
        add_action("wp_ajax_nopriv_sms_ajax_handler", array($this, "sms_ajax_handler"));
        add_shortcode("my-tag", array($this, "my_tag_handle"));
        add_shortcode("sms-list-data", array($this, "sms_handle_list_data"));
    }

    // setting link

    public function plugin_setting_link($links)
    {
        $settings_link = '<a href="options-general.php?page=sms-plugin-settings">Settings</a>';

        array_unshift($links, $settings_link);
        return $links;
    }

    public function addAdminMenus()
    {
        // Plugin Menu

        add_menu_page(
            "Student System | Student Management System", 
            "Student System", 
            "manage_options", 
            "student-system", 
             array($this, "listStudentAction"), 
            "dashicons-welcome-learn-more",
            6
        );

        add_submenu_page(
            "student-system", 
            "List Student | Student Management System", 
            "List Student", 
            "manage_options", 
            "student-system", 
            array($this, "listStudentAction"),
            2
        );

        add_submenu_page(
            "student-system",
            "Add Student | Student Management System",
            "Add Student",
            "manage_options",
            "add-student",
            array($this, "addStudentAction"),
            1
        );

        
        

        add_options_page(
            "SMS Plugin Settings | Student Management System", 
            "SMS Plugin Settings", 
            "manage_options", 
            "sms-plugin-settings", 
            array($this, "sms_plugin_action_handle")
        );

       // add_submenu_page("upload.php", "SMS Plugin Settings","SMS Plugin Settings","manage_options","sms-plugin-settings",array($this, "sms_plugin_action_handle"));
    }

    //plugin action handler

    public function sms_plugin_action_handle()
    {
        echo '<h3> SMS Plugin Settings</h3>'; 
       
        include_once SMS_PLUGIN_PATH.'pages/sms-plugin-settings.php';  
    }

    // Plugin fields setting register
    public function sms_plugin_settings()
    {
        add_settings_section("sms_plugin_settings","Form field validation settings","","sms-plugin-settings");

        // add name field validation
        register_setting("sms_plugin_options","sms_name_validation");
        add_settings_field("sms_name_field", "Name field Validation", array($this, "nameFieldValidation"), "sms-plugin-settings", "sms_plugin_settings");

        // add email field validation
        register_setting("sms_plugin_options","sms_email_validation");
        add_settings_field("sms_email_field", "Email field Validation", array($this, "emailFieldValidation"), "sms-plugin-settings", "sms_plugin_settings");

        // add gender field validation
        register_setting("sms_plugin_options","sms_gender_validation");
        add_settings_field("sms_gender_field", "Gender field Validation", array($this, "genderFieldValidation"), "sms-plugin-settings", "sms_plugin_settings");

        // add phno field validation
        register_setting("sms_plugin_options","sms_phno_validation");
        add_settings_field("sms_phno_field", "Phno field Validation", array($this, "phnoFieldValidation"), "sms-plugin-settings", "sms_plugin_settings");
    }

    // add name field validation checkbox
    public function nameFieldValidation()
    {
        $saved_name_value = get_option("sms_name_validation");

        $checked = "";
        if(!empty($saved_name_value))
        {
             $checked = "checked";
        }
        echo '<input type="checkbox" name="sms_name_validation" value="1" '.$checked.'>';
    }

    // add email field validation checkbox
    public function emailFieldValidation()
    {
        $saved_email_value = get_option("sms_email_validation");

        $checked = "";
        if(!empty($saved_email_value))
        {
             $checked = "checked";
        }
        echo '<input type="checkbox" name="sms_email_validation" value="1" '.$checked.'>';
    }

    // add gender field validation checkbox
    public function genderFieldValidation()
    {
        $saved_gender_value = get_option("sms_gender_validation");

        $checked = "";
        if(!empty($saved_gender_value))
        {
             $checked = "checked";
        }
        echo '<input type="checkbox" name="sms_gender_validation" value="1" '.$checked.'>';
    }

    // add phno field validation checkbox
    public function phnoFieldValidation()
    {
        $saved_phno_value = get_option("sms_phno_validation");

        $checked = "";
        if(!empty($saved_phno_value))
        {
             $checked = "checked";
        }
        echo '<input type="checkbox" name="sms_phno_validation" value="1" '.$checked.'>';
    }

    // List students callback
    public function listStudentAction()
    {

        //Get Action & Id
        if(isset($_GET['action']) && $_GET['action'] == "edit")
        {
            global $wpdb;
            
            $this->action = "edit";
            $student_id = $_GET['id'];

            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn-submit']))
            {
                $name = sanitize_text_field($_POST['name']);
                $email = sanitize_text_field($_POST['email']);
                $gender = sanitize_text_field($_POST['gender']);
                $phno = sanitize_text_field($_POST['phno']);

               $wpdb->update("{$wpdb->prefix}student_system",
                array(
                    'name' => $name,
                    'email' => $email,
                    'gender' => $gender,
                    'phno' => $phno
                ),
                array(
                    'id' => $student_id
                ));

                $this->message = "Student Updated Successfully";
               
            }
            
            
    
            $student = $this->getStudentData($student_id);  
            $action = $this->action;
            $displayMessage = $this->message;

            include_once SMS_PLUGIN_PATH.'pages/add-student.php';
        }
        elseif(isset($_GET['action']) && $_GET['action'] == 'view')
        {
            global $wpdb;

            $this->action = "view";
            $student_id = $_GET['id'];

            $student = $this->getStudentData($student_id);

            $action = $this->action;
            include_once SMS_PLUGIN_PATH.'pages/add-student.php';
        }
        else
        {
            global $wpdb;

            if(isset($_GET['action']) && $_GET['action'] == 'delete')
            {
                $data = $this->getStudentData(intval($_GET['id']));

                if(!empty($data))
                {
                    $student_id = $_GET['id'];
                    $wpdb->delete("{$wpdb->prefix}student_system", array('id' => $student_id));
                    $this->message = "Student Record Deleted Successfully";

            
                }

                
            }

            $students = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}student_system", ARRAY_A);
            
            $displayMessage = $this->message;
            include_once SMS_PLUGIN_PATH."pages/list-student.php";
        }
       
    }

    //return student data
    private function getStudentData($student_id)
    {
        global $wpdb;
        $table_prefix = $wpdb->prefix;

        $student = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_prefix}student_system WHERE ID=%d", $student_id), ARRAY_A 
        );

        return $student;

    }

    // Add student callback
    public function addStudentAction()
    {
        // Form submission method

        if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['btn-submit']))
        {
            //NONCE verification

            if(isset($_POST['wp_nonce_add_student']) && wp_verify_nonce($_POST['wp_nonce_add_student'], "wp_nonce_add_student"))
            {
                //success
                $this->saveStudentData();
            }
            else{
                //fail

                $this->message = "Verification failed";
                $this->status = 0;
                
            }
            
        }

        $displayMessage = $this->message;
        $displayStatus = $this->status;
        include_once SMS_PLUGIN_PATH."pages/add-student.php";
    }

    private function saveStudentData()
    {
        global $wpdb;

            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_text_field($_POST['email']);
            $gender = sanitize_text_field($_POST['gender']);
            $phno = sanitize_text_field($_POST['phno']);
            $profile_pic = sanitize_text_field($_POST['profile-url']);

            $table_prefix = $wpdb->prefix;
            $wpdb->insert("{$table_prefix}student_system", array(
                'name' => $name,
                'email' => $email,
                'gender' => $gender,
                'phno' => $phno,
                'profile_pic' => $profile_pic
            ));

            $last_id = $wpdb->insert_id;

            if($last_id > 0)
            {
                $this->message = "Student data saved successfully.";
                $this->status = 1;
            }
            else{
                $this->message = "Failed to save data.";
                $this->status = 0;
            }
    }
    
    //create table
    public function createStudentTable()
    {
        global $wpdb;

        $prefix = $wpdb->prefix; // wp_

        $sql = '
        CREATE TABLE `'.$prefix.'student_system` (
        `id` int(5) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `email` varchar(80) NOT NULL,
        `gender` enum("male","female","other") DEFAULT NULL,
        `phno` varchar(25) DEFAULT NULL,
        `profile_pic` TEXT,
        `profile_bio` TEXT,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 
        ';

        include_once ABSPATH.'wp-admin/includes/upgrade.php';

        dbDelta($sql);

        // Insert dummy data

       $insetDummy = "INSERT into {$wpdb->prefix}student_system (`name`, `email`, `phno`, `gender`) values ('Dummy User 1', 'nikita@gmail.com', '8767568978', 'Female') , ('Dummy User 2', 'kanti@gmail.com', '8767568978', 'Male'), ('Dummy User 3', 'manisha@gmail.com', '8767568978', 'Female')";

       $wpdb->query($insetDummy);

        // Create wordpress page

        $post_data =  [
            "post_title" => "SMS List Page",
            "post_status" => "publish",
            "post_name" => "sms-list-page",
            "post_content" => "[sms-list-data]",
            "post_type" => "page"
        ];

        wp_insert_post($post_data);
    }

    // Drop table
    public function deleteStudentTable()
    {
        global $wpdb;

        $prefix = $wpdb->prefix; // wp_

        // Table data backup

        $db_username = DB_USER;
        $db_password = DB_PASSWORD;
        $db_name = DB_NAME;
        $wp_content_path = WP_CONTENT_DIR;

        $version = time();
        $file_name = "sms-db-table".$version.".sql";

        $backup_path = $wp_content_path."/sms-plugin/".$file_name;

        // Tables

        $tables = ["{$wpdb->prefix}student_system"];

        $tables_name = implode(" ", $tables);

        // create folder

        if(!is_dir($wp_content_path."/sms-plugin/"))
        {
            mkdir($wp_content_path."/sms-plugin/", 0777);
        }
        // Shell execute command

        shell_exec("mysqldump -u {$db_username} -p{$db_password} {$db_name} {$tables_name} > {$backup_path}");

        $sql = "DROP TABLE IF EXISTS ".$prefix."student_system";

        $wpdb->query($sql);
    }

    // Add plugin files

    public function addStudentPluginFiles()
    {
        wp_enqueue_script("datatable-js", SMS_PLUGIN_URL . "assets/js/dataTables.min.js", array("jquery"), "1.0");

        wp_enqueue_style("datatable-css", SMS_PLUGIN_URL . "assets/css/dataTables.dataTables.min.css", array(), "1.0", "all");

        wp_enqueue_script("custom-js", SMS_PLUGIN_URL . "assets/js/script.js", array("jquery"), "1.0");

        wp_enqueue_style("custom-css", SMS_PLUGIN_URL . "assets/css/custom.css", array(), "1.0", "all");
    
        wp_enqueue_media();

        $data = "var sms_ajax_url = '".admin_url('admin-ajax.php')."'";

        wp_add_inline_script("custom-js", $data);

        wp_enqueue_style("toaster-css", SMS_PLUGIN_URL."assets/css/toastr.min.css",array(),"1.0", "all");

        wp_enqueue_script("toaster-js", SMS_PLUGIN_URL."assets/js/toastr.min.js", array("jquery"), "1.0");
    }

    public function addStudentPluginFilesFrontend()
    {
        wp_enqueue_script("frontend-js", SMS_PLUGIN_URL . "assets/js/frontend.js", array("jquery"), "1.0");

        $data = "var sms_ajax_url = '".admin_url('admin-ajax.php')."'";

        wp_add_inline_script("frontend-js", $data);

        wp_enqueue_style("toaster-css", SMS_PLUGIN_URL."assets/css/toastr.min.css",array(),"1.0", "all");

        wp_enqueue_script("toaster-js", SMS_PLUGIN_URL."assets/js/toastr.min.js", array("jquery"), "1.0");

    }

    // ajax request handler

    public function sms_ajax_handler()
    {
        if(isset($_REQUEST['param']))
        {
            global $wpdb;

            if($_REQUEST['param'] == "save_form")
            {
                if(isset($_POST['wp_nonce_add_student']) && wp_verify_nonce($_POST['wp_nonce_add_student'],"wp_nonce_add_student"))
                {

                    $name = sanitize_text_field($_POST['name']);
                    $email = sanitize_text_field($_POST['email']);
                    $gender = sanitize_text_field($_POST['gender']);
                    $phno = sanitize_text_field($_POST['phno']);
                    $profile_url = sanitize_text_field($_POST['profile-url']);
                    $operation_type = sanitize_text_field($_POST['operation_type']);
                    $bio = sanitize_text_field($_POST['bio']);

                    if($operation_type == "edit")
                    {
                        // Student edit code

                        $student_id = $_REQUEST['student_id'];

                        $wpdb->update("{$wpdb->prefix}student_system", array(
                            'name' => $name,
                            'email' => $email,
                            'gender' => $gender,
                            'phno' => $phno,
                            'profile_pic' => $profile_url,
                            'profile_bio' => $bio
                        ), array(
                            'id' => $student_id
                        ));

                        echo json_encode(array(
                            "status" => 1,
                            "message" => "Student updated successfully",
                            "data" => []
                        ));
                    }

                    elseif($operation_type == "add")
                    {
                        // Student add code

                        $wpdb->insert("{$wpdb->prefix}student_system", array(
                            'name' => $name,
                            'email' => $email,
                            'gender' => $gender,
                            'phno' => $phno,
                            'profile_pic' => $profile_url,
                            'profile_bio' => $bio
                        ));
    
                        $student_id = $wpdb->insert_id;
    
                        if($student_id > 0)
                        {
                            echo json_encode(array(
                                "status" => 1,
                                "message" => "Student data saved successfully",
                                "data" => []
                            ));
                        }
                        else
                        {
                            echo json_encode(array(
                                "status" => 0,
                                "message" => "Failed to save student",
                                "data" => []
                            ));
                        }

                    }

                    // create student
                    
                }
            }
            elseif($_REQUEST['param'] == "load_students")
            {
                $students = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}student_system", ARRAY_A);

                if(count($students) > 0)
                {
                    echo json_encode([
                        "status" => 1,
                        "message" => "Students Data",
                        "data" => $students
                    ]);
                }
                else
                {
                    echo json_encode([
                        "status" => 0,
                        "message" => "No student found",
                        "data" => []
                    ]);
                }
            }
            elseif($_REQUEST['param'] == "delete_student")
            {
                $student_id = $_REQUEST['student_id'];

                $wpdb->delete("{$wpdb->prefix}student_system", array(
                    "id" => $student_id
                ));

                echo json_encode(array(
                    "status" => 1,
                    "message" => "Student deleted successfully",
                    "data" => ""
                ));
            }
            elseif($_REQUEST['param'] == "frontend_form")
            {
                $name = sanitize_text_field($_POST['sname']);
                $email = sanitize_text_field($_POST['semail']);
                $phno = sanitize_text_field($_POST['sphno']);
                $gender = sanitize_text_field($_POST['sgender']);

                $wpdb->insert("{$wpdb->prefix}student_system", array(
                    "name" => $name,
                    "email" => $email,
                    "gender" => $gender,
                    "phno" => $phno
                ));

                $student_id = $wpdb->insert_id;

                if($student_id > 0)
                {
                    echo json_encode(array(
                        "status" => 1,
                        "data" => [],
                        "message" => "shortcode student saved successfully"
                    ));
                }
                else
                {
                    echo json_encode(array(
                        "status" => 0,
                        "data" => [],
                        "message" => "Failed to save shortcode student"
                    ));
                }

                
            }
        }

        
        wp_die(); 
    }

    public function my_tag_handle($attribute)
    {

        $custom_attributes = shortcode_atts(array(
            "color" => "black",
            "font-size" => "16px"
        ), $attribute);

        return "<span style='color:{$custom_attributes['color']}; font-size:{$custom_attributes['font-size']};'>Welcome to Nayka</span>";
    }

    public function form_render()
    {
        ob_start();
        
        include_once SMS_PLUGIN_PATH."pages/custom-form.php";

        $content = ob_get_contents();

        ob_end_clean();

        return $content;

    }

    public function sms_handle_list_data()
    {
        global $wpdb;

        $students = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}student_system", ARRAY_A);

        ob_start();

        include_once SMS_PLUGIN_PATH."pages/sms-list-data.php";

        $content = ob_get_contents();

        ob_end_clean();

        return $content;


    }
}
?>