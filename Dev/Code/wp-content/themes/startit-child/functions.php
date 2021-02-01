<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'qode_startit_default_style','qode_startit_default_style','qode_startit_modules_plugins','qode_startit_modules','qodef_font_awesome','qodef_font_elegant','qodef_ion_icons','qodef_linea_icons','qodef_simple_line_icons','qodef_dripicons','qode_startit_modules_responsive','qode_startit_blog_responsive','qode_startit_style_dynamic_responsive','qode_startit_style_dynamic' ) );

           wp_enqueue_script( 'chld_thm_cfg_child1', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/js/jquery.easyPaginate.js');
           wp_enqueue_script( 'custom_script', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/custom_script.js');



              // wp_enqueue_script( 'child_style_css', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css');
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );



// END ENQUEUE PARENT ACTION
//This function prints the JavaScript to the footer
function add_this_script_footer(){ ?>

<script>
    document.addEventListener( 'wpcf7mailsent', function( event ) {
    //    if ( '9089' == event.detail.contactFormId ){
    //     location = 'https://dev.bizzsecure.com/thank-you';
    // }
    //   if ( '616' == event.detail.contactFormId ){
    //     location = 'https://dev.bizzsecure.com/thank-you';
    // }

    if ( '9691' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/wp-content/uploads/2020/01/BizzSecure-Overview.pdf';
    }
   else if ( '9693' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/wp-content/uploads/2019/06/Case-Study_LeadEngine.pdf';
    }
     else if ( '11191' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/Sample_HIPAA_Policies-_Information_Access_Management_164.308a4.pdf';
    }
    else if ( '11398' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/NIST_800-53_Sample_Policies_and_Security_Controls.pdf';
    }
    else if ( '11399' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/ISO_27002_Sample_Policies_and_Security_Controls.pdf';
    }
    else if ( '11400' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/Sample_PCI-DSS_Policies_and_Security_Controls.pdf_1.pdf';
    }
    else if ( '11552' === event.detail.contactFormId ){
      var site_url = 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/';
      
        $('.custm_report_pop input:checked').each(function() {
        if($(this).val()  == "Detailed Compliance Risk Report"){
               window.open('https://dev.bizzsecure.com/wp-content/uploads/2019/10/Sample_Compliance_Risk_Report.pdf', '_blank');
             
        }
        else if($(this).val() == "Enterprise Risk Report"){
              window.open(site_url + 'Sample_Enterprise_Risk_Report.pdf', '_blank');
          }
        else if($(this).val() == "Network Security Risk Report"){
               window.open(site_url + 'Sample_Network_Security_Risk_Report.pdf', '_blank');
          }
           $('.custm_report_pop input').attr('checked', false);
    });
       
    }
        else if ( '11591' === event.detail.contactFormId ){
      var site_url = 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/';
      
        $('.custm_report_pop input:checked').each(function() {
        if($(this).val()  == "HIPAA Policies"){
               window.open(site_url + 'Sample_HIPAA_Policies-_Information_Access_Management_164.308a4.pdf', '_blank');
             
        }
        else if($(this).val() == "NIST Policies"){
              window.open(site_url + 'NIST_800-53_Sample_Policies_and_Security_Controls.pdf', '_blank');
          }
        else if($(this).val() == "ISO Policies"){
               window.open(site_url + 'ISO_27002_Sample_Policies_and_Security_Controls.pdf', '_blank');
          }
         else if($(this).val() == "PCI DSS Policies"){
               window.open(site_url + 'Sample_PCI-DSS_Policies_and_Security_Controls.pdf_1.pdf', '_blank');
          }
           $('.custm_report_pop input').attr('checked', false);
    });
       
    }
     else if ( '11195' === event.detail.contactFormId ){
       jQuery('.wpcf7').find('form')[0].reset(); 
              setTimeout(function() {
               jQuery('.wpcf7-mail-sent-ok').fadeOut();
              }, 2000 );
      setTimeout(function(){
            jQuery("#paoc-modal-1 ,#paoc-modal-3 , .custombox-content , .custombox-fadein , .custombox-loader").hide();
            jQuery ( 'form.wpcf7form' ). trigger ( 'reset' );
      jQuery('.embed-responsive-item').attr('src', 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/VSl_v01-1.mp4');
      // jQuery("#myModal").css('src' , 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/VSl_v01-1.mp4');
      jQuery("#myModal").modal('show');
         }, 3000);
     
    }
    else if ( '11957' === event.detail.contactFormId ){
       jQuery('.wpcf7').find('form')[0].reset(); 
              setTimeout(function() {
               jQuery('.wpcf7-mail-sent-ok').fadeOut();
              }, 2000 );
      setTimeout(function(){
            jQuery("#paoc-modal-1 ,#paoc-modal-3 , .custombox-content , .custombox-fadein , .custombox-loader").hide();
            jQuery ( 'form.wpcf7form' ). trigger ( 'reset' );
      // jQuery('.embed-responsive-item').attr('src', 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/VSl_v01-1.mp4');
      // jQuery("#myModal").css('src' , 'https://dev.bizzsecure.com/wp-content/uploads/2019/09/VSl_v01-1.mp4');
      jQuery("#myModal").modal('show');
      
         }, 3000);
     
    }
    else if ( '11044' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-it-audit-thank-you/';
    }
     else if ( '10995' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-information-security-and-it-assessments02-thank-you/';
    }
      else if ( '11026' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-compliance-assessments02-thank-you/';  
    }
      else if ( '11025' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-virtual-infosec-staff02-thank-you/';
    }
      else if ( '10089' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-policy-templates-thank-you/';
    }
      else if ( '10046' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-virtual-infosec-staff-thank-you/';
    }
      else if ( '10097' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-information-security-and-it-assessments-thank-you/';
    }
      else if ( '10096' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-compliance-assessments-thank-you/';
    }
      else if ( '9609' === event.detail.contactFormId ){
        location = 'https://dev.bizzsecure.com/landing-eaid-next-generation-grc-thank-you/';
    }
    else{
          setTimeout(function(){
           location = 'https://dev.bizzsecure.com/thank-you';
         }, 3000);
        
    }


}, false );


</script>

<?php } 

add_action('wp_footer', 'add_this_script_footer');

add_filter( 'wpcf7_validate_text', 'alphanumeric_validation_filter', 20, 2 );
add_filter( 'wpcf7_validate_text*', 'alphanumeric_validation_filter', 20, 2 );

function alphanumeric_validation_filter( $result, $tag ) {
    $tag = new WPCF7_Shortcode( $tag );
    $msg = "Allowed characters only (A-Z, a-z)";
    if ( 'your-name' == $tag->name ) {
        $name_of_the_input = isset( $_POST['your-name'] ) ? trim( $_POST['your-name'] ) : '';

        if ( !preg_match('/^[a-zA-Z ]+$/',$name_of_the_input) ) {
            $result->invalidate( $tag, $msg );
        }
    }

    if ( 'last-name' == $tag->name ) {
        $name_of_the_input = isset( $_POST['last-name'] ) ? trim( $_POST['last-name'] ) : '';

        if ( !preg_match('/^[a-zA-Z ]+$/',$name_of_the_input) ) {
            $result->invalidate( $tag, $msg );
        }
    }

    if ( 'text-457' == $tag->name ) {
        $name_of_the_input = isset( $_POST['text-457'] ) ? trim( $_POST['text-457'] ) : '';

        if ( !preg_match('/^[a-zA-Z ]+$/',$name_of_the_input) ) {
            $result->invalidate( $tag, $msg );
        }
    }

    if ( 'text-458' == $tag->name ) {
        $name_of_the_input = isset( $_POST['text-458'] ) ? trim( $_POST['text-458'] ) : '';

        if ( !preg_match('/^[a-zA-Z ]+$/',$name_of_the_input) ) {
            $result->invalidate( $tag, $msg );
        }
    }

    if ( 'text-459' == $tag->name ) {
        $name_of_the_input = isset( $_POST['text-459'] ) ? trim( $_POST['text-459'] ) : '';

        if ( !preg_match('/^[a-zA-Z ]+$/',$name_of_the_input) ) {
            $result->invalidate( $tag, $msg );
        }
    }

    if ( 'text-460' == $tag->name ) {
        $name_of_the_input = isset( $_POST['text-460'] ) ? trim( $_POST['text-460'] ) : '';

        if ( !preg_match('/^[a-zA-Z ]+$/',$name_of_the_input) ) {
            $result->invalidate( $tag, $msg );
        }
    }

    if ( 'phone-561' == $tag->name ) {
        $phoneNumber = isset( $_POST['phone-561'] ) ? trim( $_POST['phone-561'] ) : '';
          $replace = preg_replace('/[-() . ]+/', '', $phoneNumber);
          $_POST['phone-561'] = $replace;
         if ( preg_match('/^[a-zA-Z ]+$/',$phoneNumber) ) {
             $result->invalidate( $tag, 'Phone number is invalid.' );
         }
      
        // if (strlen((string)$phoneNumber) > 15) {
        //     $result->invalidate( $tag,  $_POST['phone-561'] );
        // }
    }

    if ( 'phone-149' == $tag->name ) {
        $phoneNumber = isset( $_POST['phone-149'] ) ? trim( $_POST['phone-149'] ) : '';
          $replace = preg_replace('/[-() . ]+/', '', $phoneNumber);
          $_POST['phone-149'] = $replace;
         if ( preg_match('/^[a-zA-Z ]+$/',$phoneNumber) ) {
             $result->invalidate( $tag, 'Phone number seems invalid.' );
         }
    }

    return $result;
} 


// define the wpcf7_posted_data callback 
function action_wpcf7_posted_data( $array ) { 
    //'checkbox-name' is the name that you gave the field in the CF7 admin.
    $value = $array['phone-561'];
     $replace = preg_replace('/[-() . ]+/', '', $value);
    if( !empty( $replace ) ){
        $array['phone-561'] = $replace;
    }

    return $array;
}; 
add_filter( 'wpcf7_posted_data', 'action_wpcf7_posted_data', 10, 1 );

// Add custom validation for CF7 form fields
    function is_company_email($email){ // Check against list of common public email providers & return true if the email provided *doesn't* match one of them
    if(
        preg_match('/@gmail.com/i', $email)
        
    ){
                    return false; // It's a publicly available email address
                }else{
                    return true; // It's probably a company email address
                }
            }
            function email_validation_filter_func($result,$tag){
                $name = $tag->name;

                $value = isset( $_POST[$name] )
                ? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
                : '';

                if ( 'email' == $tag->basetype ) 
                {
                   
                    /*add the domain names you want to block in the $domains array*/
                   $domains = array("gmail.com","yahoo.com","hotmail.com" , "icloud.com" ,"live.com", "mail.rua", "mail.ru", "mail.com", "inbox.ru");
            /*explode will store the string into array
                 e.g: example@gmail.com
                 array(example, gmail.com)*/
                 $value_strtolower = strtolower($value);
                 $udomain = explode('@', $value_strtolower);
                 
            //select the email domain from the above splitted array
                 $email_domain = $udomain[1];
                 array_map('nestedLowercase', $email_domain);
            // check name is 'company-email' else default validation will work
               
                //check entered value = $value exists in $domain array
                    if(in_array($email_domain, $domains)) {
                    //display error
                        $result->invalidate( $tag->name, "Please enter your company email address" );
                    }

               
            }
            return $result;
        }
   add_filter( 'wpcf7_validate_email', 'email_validation_filter_func', 10, 2 ); // Email field  field
   add_filter( 'wpcf7_validate_email*', 'email_validation_filter_func', 10, 2 ); // Req. Email  number



   add_filter( 'wpcf7_validate', 'email_already_in_db', 10, 2 );

   function email_already_in_db ( $result, $tags ) {
    // retrieve the posted email
    $form  = WPCF7_Submission::get_instance();

    
    $form_id = $form->get_posted_data('_wpcf7');
 if ($form_id == 9691){ // 9691 => Your Form ID.
    // if already in database, invalidate
   $email = $form->get_posted_data('resource-email');
   $email_count = do_shortcode("[acf7db form_id='9691' search='".$email."' display='count']");
      if($email_count > 0) // email_exists is a WP function
      {
           $result->invalidate('resource-email', 'Your email exists in our database');
       }
   }

    if ($form_id == 9693){ // 9693 => Your Form ID.
    // if already in database, invalidate
      $email = $form->get_posted_data('resource-case-email');
      $email_count = do_shortcode("[acf7db form_id='9693' search='".$email."' display='count']");
     if($email_count > 0) // email_exists is a WP function
      {
       $result->invalidate('resource-case-email', 'Your email exists in our database');
   }
}
    // return the filtered value
return $result;
}

function custom_phone_validation($result,$tag){

    $type = $tag->type;
    $name = $tag->name;

    if($type == 'tel' || $type == 'tel*'){

        $phoneNumber = isset( $_POST[$name] ) ? trim( $_POST[$name] ) : '';

        $phoneNumbers = preg_replace('/[() +-_]/', '', $phoneNumber);
        if (strlen((string)$phoneNumber) > 15) {
            $result->invalidate( $tag, 'Phone number length is invalid.' );
        }
        
    }
    return $result;
}
add_filter('wpcf7_validate_tel','custom_phone_validation', 10, 2);
add_filter('wpcf7_validate_tel*', 'custom_phone_validation', 10, 2);


function access_by_user_role(){
// get current login user's role
    $roles = wp_get_current_user()->roles;
    
// test role
    if(  !in_array('editor',$roles)){
        return;
    }
    
//remove menu from site backend.
// remove_menu_page( 'index.php' ); //Dashboard
// remove_menu_page( 'edit.php' ); //Posts
remove_menu_page( 'upload.php' ); //Media
remove_menu_page( 'edit-comments.php' ); //Comments
remove_menu_page( 'themes.php' ); //Appearance
remove_menu_page( 'plugins.php' ); //Plugins
remove_menu_page( 'users.php' ); //Users
remove_menu_page( 'tools.php' ); //Tools
remove_menu_page( 'options-general.php' ); //Settings
remove_menu_page('edit.php?post_type=product');



remove_menu_page('vc-welcome');
remove_menu_page('profile.php');
remove_menu_page( 'edit.php?post_type=page' ); //Pages
remove_menu_page('edit.php?post_type=services');
remove_menu_page('edit.php?post_type=landingpage'); 
remove_menu_page('edit.php?post_type=portfolio-item');
remove_menu_page('edit.php?post_type=my_logos'); 
remove_menu_page('edit.php?post_type=aoc_popup'); 
remove_menu_page('edit.php?post_type=slides');  
remove_menu_page('edit.php?post_type=carousels'); 
remove_menu_page('edit.php?post_type=testimonials'); 
remove_menu_page('edit.php?post_type=tcmembers'); 
remove_menu_page('wpcf7'); 







}
add_action( 'admin_menu', 'access_by_user_role' , 100 );

   $roles = wp_get_current_user()->roles;
    
// test role
    if(  !in_array('editor',$roles)){
        return;
    }
// Remove submenus
function remove_submenu() {
    remove_submenu_page( 'es_dashboard', 'es_forms' );
    remove_submenu_page( 'es_dashboard', 'es_campaigns' );
    remove_submenu_page( 'es_dashboard', 'es_reports' );
    remove_submenu_page( 'es_dashboard', 'es_settings' );
    remove_submenu_page( 'es_dashboard', 'es_general_information' );
    remove_submenu_page( 'es_dashboard', 'es_pricing' );
    remove_submenu_page( 'es_dashboard', 'es_dashboard' );
    remove_submenu_page( 'contact-form-listing', 'import_cf7_csv' );
    remove_submenu_page( 'contact-form-listing', 'shortcode' );
    remove_submenu_page( 'contact-form-listing', 'extentions' );
}
add_action( 'admin_menu', 'remove_submenu', 999 );



function hide_delete_email_btn()
{
 $user = wp_get_current_user();
 if ( in_array( 'email_manager', (array) $user->roles ) ) {
    //The user has the "author" role
    ?>

    <style type="text/css">
    .email-subscribers_page_es_subscribers .row-actions span.delete ,  .email-subscribers_page_es_subscribers .wrap a.page-title-action , .toplevel_page_contact-form-listing .reset-class{
        display: none;
    }
</style>

    <script type="text/javascript">
        jQuery( document ).ready(function() 
        {
             jQuery(".row-actions .resend a").attr("name","resend_email_msg");
           
        });
    </script>

<?php }

}
add_action('admin_head', 'hide_delete_email_btn');

// add_filter( 'wpcf7_mail_components', 'remove_blank_lines' );

// function remove_blank_lines( $mail ) {
//     if ( is_array( $mail ) && ! empty( $mail['body'] ) )
//         $mail['body'] = preg_replace( '|\n\s*\n|', "\n\n", $mail['body'] );

//     return $mail;
// }

  // function do_custom_shortcode_banner()
  //   {
  //     $banner_data  =  get_option( 'banner_content_option_name' ); echo $banner_data['banner_data_0'];
  //   }
  //   add_shortcode( 'custom_shortcode_banner', 'do_custom_shortcode_banner' );

 
add_action('wp_print_scripts', 'wra_filter_scripts', 100000);
add_action('wp_print_footer_scripts',  'wra_filter_scripts', 100000);
 
function wra_filter_scripts(){
    #wp_deregister_script($handle);
    #wp_dequeue_script($handle);

    wp_deregister_script('bbpress-editor');
    wp_dequeue_script('bbpress-editor');
 
    // Device Pixels support
    // This improves the resolution of gravatars and wordpress.com uploads on hi-res and zoomed browsers. We only have gravatars so we should be ok without it.
    wp_deregister_script('devicepx');
    wp_dequeue_script('devicepx');
 
    if( !is_singular( 'docs' ) ){
        // the table of contents plugin is being used on documentation pages only
        wp_deregister_script('toc-front');
        wp_dequeue_script('toc-front');
    }
 
    if( !is_singular( array('docs', 'post' ) ) ){
        wp_deregister_script('codebox');
        wp_dequeue_script('codebox');
    }
}


// function myplugin_register_settings() {
//    add_option( 'myplugin_option_name', 'This is my option value.');
//    register_setting( 'myplugin_options_group', 'myplugin_option_name', 'myplugin_callback' );
// }
// add_action( 'admin_init', 'myplugin_register_settings' );

// function myplugin_register_options_page() {
//   add_options_page('Page Title', 'Plugin Menu', 'manage_options', 'myplugin', 'myplugin_options_page');
// }
// add_action('admin_menu', 'myplugin_register_options_page');




?>