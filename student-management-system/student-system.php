<?php 
/**
 * Plugin name: Student Management System
 * Description: This is a test plugin for OOPS concepts
 * Plugin URI: https://www.example.com/student-management-system
 * Author: Nikita Modhavadiya
 * Author URI: https://example.com
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Requires at least: 6.3.2
 */

define("SMS_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("SMS_PLUGIN_URL", plugin_dir_url(__FILE__));
define("SMS_PLUGIN_BASENAME", plugin_basename(__FILE__));


include_once SMS_PLUGIN_PATH.'class/StudentManagement.php';

$studentManagementObj = new StudentManagement();

register_activation_hook(__FILE__, array($studentManagementObj, "createStudentTable"));

register_deactivation_hook(__FILE__, array($studentManagementObj, "deleteStudentTable"));

add_shortcode("form-tag", array($studentManagementObj, "form_render"));





?>