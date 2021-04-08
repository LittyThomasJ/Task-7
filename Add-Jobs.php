<?php
  /*
  Plugin Name: Add Jobs
  Plugin URI: https://jobs.com/
  Description: Declares a plugin that will create a custom post type displaying jobs.
  Version: 1.1
  Author: Litty thomas
  Author URI: http://litty4ever.com/
  License: GPLv2
  */
  if( !defined('ABSPATH') ) : exit(); endif;

  /**
   * Define plugin constants
   */
  define( 'MYPLUGIN_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
  define( 'MYPLUGIN_URL', trailingslashit( plugins_url('/', __FILE__) ) );
  //require_once MYPLUGIN_PATH . 'Settings/settings.php';
  // class for creating cutsom post type
  class JobsCustomType{
  	// Function for creating custom post type for jobs
    public function create_jobs() {
    	register_post_type( 'jobs',
    		array(
    			'labels' => array(
    				'name' => 'Jobs',
    				'singular_name' => 'Jobs',
    				'add_new' => 'Add New',
    				'add_new_item' => 'Add New Jobs',
    				'edit' => 'Edit',
    				'edit_item' => 'Edit Jobs',
    				'new_item' => 'New Jobs',
    				'view' => 'View',
    				'view_item' => 'View Jobs',
    				'search_items' => 'Search Jobs',
    				'not_found' => 'No Jobs found',
    				'not_found_in_trash' => 'No Jobs found in Trash',
    				'parent' => 'Parent Jobs'
    			),

    			'public' => true,
    			'menu_position' => 15,
          		'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
			    'taxonomies' => array( '' ),
    			'menu_icon' => 'dashicons-groups',
    			'has_archive' => true
    		)
    	);
    }
  }
  // Class for creating the metabox inside the custom post type
  class JobsMetabox extends JobsCustomType{
	// function for creating metabox
    public function my_admin() {
    	// Adding metabox
    	add_meta_box( 'job_meta_box',
    		'Job Details',
    		array($this, 'display_job_meta_box'),
    		'jobs', 'normal', 'high'
    	);
    }
    // Function for displaying metabox
    public function display_job_meta_box( $object ) {
    	// Validating contents using nonce field
      wp_nonce_field(basename(__FILE__), "meta-box-nonce");
      ?>
      <!-- The contents within Custom metabox -->
      <div>
        <label for="meta-box-title">Job Title</label>
        <!-- value of the input is fetched using get_post_meta -->
        <input name="meta-box-title" type="text" value="<?php echo esc_html(get_post_meta($object->ID, "_meta-box-title", true)); ?>">
        <br><br>
        <!-- For email -->
        <label for="meta-box-email">Email &nbsp &nbsp &nbsp</label>

        <input name="meta-box-email" type="email" value="<?php echo esc_html(get_post_meta($object->ID, "_meta-box-email", true)); ?>">
        <br>
        <br>
        <label for="meta-box-date">Date &nbsp &nbsp &nbsp </label>
        <input name="meta-box-date" type="date" value="<?php echo esc_html(get_post_meta($object->ID, "_meta-box-date", true)); ?>">


      </div>
      <?php
    }
    // Function for saving contents of metabox
    function save_custom_meta_box($post_id){
      // For verifying using wp_verify_nonce, Verifies that a correct security nonce was used with time limit.
      if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
      // To check the user have the capability to edit
      if(!current_user_can("edit_post", $post_id))
        return $post_id;

      // aborting the logic that is to follow beneath the condition, if doing autosave = true
      if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
      $meta_box_title_value = "";
      $meta_box_email_value = "";
      $meta_box_date_value = "";
      // checking for the condition if meta-box-text is posted
      if(isset($_POST["meta-box-title"])){
        //Sanitized data is fetched to a variable which is posted
        $meta_box_title_value = sanitize_text_field($_POST["meta-box-title"]);
      }
      // Updates a post meta field based on the given post ID.
      update_post_meta($post_id, "_meta-box-title", $meta_box_title_value);
      // checking for the condition if meta-box-checkbox is posted
      if(isset($_POST["meta-box-email"])){
          //Sanitized data is fetched to a variable which is posted
          $meta_box_email_value = sanitize_text_field($_POST["meta-box-email"]);
      }
      // Updates a post meta field based on the given post ID.
      update_post_meta($post_id, "_meta-box-email", $meta_box_email_value);
      if(isset($_POST["meta-box-date"])){
          //Sanitized data is fetched to a variable which is posted
          $meta_box_date_value = sanitize_text_field($_POST["meta-box-date"]);
      }
      // Updates a post meta field based on the given post ID.
      update_post_meta($post_id, "_meta-box-date", $meta_box_date_value);
    }

  }
  // Class for creating Settings page inherits
  class JobsSettings extends JobsMetabox{
  	// Initialized usng constructor
    public function __construct(){
      add_action( 'init', array($this,'create_jobs') );
      add_action( 'admin_init', array($this,'my_admin' ));
      // For calling save_custom_meta_box
      add_action("save_post", array($this,"save_custom_meta_box"));
      add_filter('the_content',array($this,'display_front_end'),20,1);
      // add_action('wp_enqueue_scripts', array($this,'Style_contents'));
      add_action('admin_menu', array($this,'add_jobs_submenu_example'));
      add_action( 'admin_init', array($this,'myplugin_settings_init' ));

    }
    // Function for adding submenu 'Settings'
    public function add_jobs_submenu_example(){

     add_submenu_page(
                     'edit.php?post_type=jobs', //$parent_slug
                     'Admin Page',  //$page_title
                     'Settings',        //$menu_title
                     'manage_options',           //$capability
                     'myplugin-settings-page',//$menu_slug
                     array($this,'jobs_submenu_render_page')//$function
     );
    }

	//add_submenu_page callback function
    public function jobs_submenu_render_page($result) {
      ?>
      <div class="container">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
          <?php
              // security field
              settings_fields( 'myplugin-settings-page' );

              // output settings section here
              do_settings_sections('myplugin-settings-page');

              // save settings button
              submit_button( 'Save Settings' );
          ?>
        </form>
      </div>
      <?php
    }
    // Function for creating settings page contents
    public function myplugin_settings_init() {

      // Setup settings section
      add_settings_section(
          'myplugin_settings_section',
          '',
          '',
          'myplugin-settings-page'
      );

      // Register field for organization name
      register_setting(
          'myplugin-settings-page',
          'myplugin_settings_organization_name_field',
          array(
              'type' => 'string',
              'sanitize_callback' => 'sanitize_text_field',
              'default' => ''
          )
      );

      // Add field for organization name
      add_settings_field(
          'myplugin_settings_organization_name_field',
          __( 'Organization name', 'my-plugin' ),
          array($this,'myplugin_settings_organization_name_field_callback'),
          'myplugin-settings-page',
          'myplugin_settings_section'
      );
      // Registe field for description
	    register_setting(
	        'myplugin-settings-page',
	        'myplugin_settings_description_field',
	        array(
	            'type' => 'string',
	            'sanitize_callback' => 'sanitize_textarea_field',
	            'default' => ''
	        )
	    );

	     // Add field for description
	     add_settings_field(
	        'myplugin_settings_description_field',
	        __( 'Description', 'my-plugin' ),
	        array($this,'myplugin_settings_description_field_callback'),
	        'myplugin-settings-page',
	        'myplugin_settings_section'
	    );
	     // Register vacancy field
      register_setting(
          'myplugin-settings-page',
          'myplugin_settings_vacancy_field',
          array(
              'type' => 'int',
              'sanitize_callback' => 'sanitize_text_field',
              'default' => ''
          )
      );

      // Add vacancy fields
      add_settings_field(
          'myplugin_settings_vacancy_field',
          __( 'Number of Vacancies', 'my-plugin' ),
          array($this,'myplugin_settings_vacancy_field_callback'),
          'myplugin-settings-page',
          'myplugin_settings_section'
      );
      // Register title visibility field
      register_setting(
        'myplugin-settings-page',
        'myplugin_settings_title_visibility_field',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
      );

      // Add title visibility fields
      add_settings_field(
          'myplugin_settings_title_visibility_field',
          __( 'Display options', 'my-plugin' ),
          array($this,'myplugin_settings_title_visibility_field_callback'),
          'myplugin-settings-page',
          'myplugin_settings_section'
      );
	     //  Register email visibility field
      register_setting(
        'myplugin-settings-page',
        'myplugin_settings_email_visibilty_field',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
      );

      // Add email visibility fields
      add_settings_field(
          'myplugin_settings_email_visibilty_field',
          __( 'Display options', 'my-plugin' ),
          array($this,'myplugin_settings_email_visibilty_field_callback'),
          'myplugin-settings-page',
          'myplugin_settings_section'
      );
     // Register expiry date field
      register_setting(
          'myplugin-settings-page',
          'myplugin_settings_date_field',
          array(
              'type' => 'string',
              'sanitize_callback' => 'sanitize_text_field',
              'default' => ''
          )
      );

      // Add expiry date fields
      add_settings_field(
          'myplugin_settings_date_field',
          __( 'Expiry date ', 'my-plugin' ),
          array($this,'myplugin_settings_date_field_callback'),
          'myplugin-settings-page',
          'myplugin_settings_section'
      );
      // Register color field
      register_setting(
          'myplugin-settings-page',
          'myplugin_settings_color_field',
          array(
              'type' => 'string',
              'sanitize_callback' => 'sanitize_text_field',
              'default' => ''
          )
      );

      // Add color fields
      add_settings_field(
          'myplugin_settings_color_field',
          __( 'Choose a color', 'my-plugin' ),
          array($this,'myplugin_settings_color_field_callback'),
          'myplugin-settings-page',
          'myplugin_settings_section'
      );
    }
    // callback function for organization name
    function myplugin_settings_organization_name_field_callback() {
    	// Retrieving values from input field using get_option()
      $myplugin_organization_name_field = get_option('myplugin_settings_organization_name_field');
      ?>
      <input type="text" name="myplugin_settings_organization_name_field" class="regular-text" value="<?php echo isset($myplugin_organization_name_field) ? esc_attr( $myplugin_organization_name_field ) : ''; ?>" />
      <?php
    }
    // callback function for description field
	  function myplugin_settings_description_field_callback() {
		// Retrieving values from input field using get_option()
	    $myplugin_description_field = get_option('myplugin_settings_description_field');
	    ?>
	    <textarea name="myplugin_settings_description_field" class="regular-text" rows="5"><?php echo isset($myplugin_description_field) ? esc_textarea( $myplugin_description_field ) : ''; ?></textarea>
	    <?php
	  }
  	// callback function for vacancy field
  	function myplugin_settings_vacancy_field_callback() {
  		// Retrieving values from input field using get_option()
        $myplugin_vacancy_field = get_option('myplugin_settings_vacancy_field');
        ?>
        <input type="number" name="myplugin_settings_vacancy_field" class="regular-text" value="<?php echo isset($myplugin_vacancy_field) ? esc_attr( $myplugin_vacancy_field ) : ''; ?>" min=1 max=100/>
        <?php
      }
      // callback function for title visibility
  	function myplugin_settings_title_visibility_field_callback() {
  		// Retrieving values from input field using get_option()
  	    $myplugin_title_visibility_field = get_option( 'myplugin_settings_title_visibility_field' );
  	    ?>
  	    <label for="value1">
  	        <input type="radio" name="myplugin_settings_title_visibility_field" value="value1" <?php checked( 'value1', $myplugin_title_visibility_field ); ?>/> Title only
  	    </label>
  	    <label for="value2">
  	        <input type="radio" name="myplugin_settings_title_visibility_field" value="value2" <?php checked( 'value2', $myplugin_title_visibility_field ); ?>/> Title and contents
  	    </label>
  	    <?php
    }
  	// callback function for email visibility
  	function myplugin_settings_email_visibilty_field_callback() {
  		// Retrieving values from input field using get_option()
        $myplugin_email_visibilty_field = get_option('myplugin_settings_email_visibilty_field');
        ?>
        <input type="checkbox" name="myplugin_settings_email_visibilty_field" value="1" <?php checked(1, $myplugin_email_visibilty_field, true); ?> />Show email
        <?php
    }
    // callback function for date field
    function myplugin_settings_date_field_callback() {
    	// Retrieving values from input field using get_option()
      $myplugin_date_field = get_option('myplugin_settings_date_field');
      ?>
      <input type="date" name="myplugin_settings_date_field" class="regular-text" value="<?php echo isset($myplugin_date_field) ? esc_attr( $myplugin_date_field ) : ''; ?>" />
      <?php
    }
    // callback function for color field
    function myplugin_settings_color_field_callback() {
    	// Retrieving values from input field using get_option()
      $myplugin_color_field = get_option('myplugin_settings_color_field');
      ?>
      <input type="color" name="myplugin_settings_color_field" class="regular-text" value="<?php echo isset($myplugin_color_field) ? esc_attr( $myplugin_color_field ) : ''; ?>" />
      <?php
    }
    // Function for displaying contents in front end
    public function display_front_end($val){
      global $post;
      // Initialzing variables with null values
      $test=$title=$email=$date=$myplugin_organization_name_field=$myplugin_description_field=$myplugin_vacancy_field = $myplugin_email_visibilty_field = $myplugin_title_visibility_field =$myplugin_date_field ="";
      $content = "<div>";
      // Terniary operation is done with retrieving values
      (empty(get_post_meta($post->ID, "_meta-box-title", true))) ? '' : ($title = '<p>Job Type : ' . get_post_meta($post->ID, "_meta-box-title", true) . '</p>') ;
      $date =  get_post_meta($post->ID, '_meta-box-date', true);
      (empty(get_post_meta($post->ID, '_meta-box-email', true))) ? '' : $email = '<p>Email : ' . get_post_meta($post->ID, '_meta-box-email', true) . '</p>';
      (empty(get_option('myplugin_settings_organization_name_field'))) ? '' : $myplugin_organization_name_field = '<p>Organization name : ' . get_option('myplugin_settings_organization_name_field') . '</p>';
      (empty(get_option('myplugin_settings_description_field'))) ? '' : $myplugin_description_field = '<p>Description : ' . get_option('myplugin_settings_description_field') . '</p>';
      (empty(get_option('myplugin_settings_vacancy_field')))? '' : $myplugin_vacancy_field = '<p>Number of vacancy : ' . get_option('myplugin_settings_vacancy_field') . '</p>';
      $myplugin_email_visibilty_field = get_option('myplugin_settings_email_visibilty_field');
      $myplugin_title_visibility_field = get_option( 'myplugin_settings_title_visibility_field' );
      $myplugin_date_field = get_option('myplugin_settings_date_field');
      ($myplugin_email_visibilty_field == 1) ? $email = $email : $email ='';
      // Checking whether date field is empty or not
      if(!empty($date) && !empty($myplugin_date_field)){
        if ($date >= $myplugin_date_field) {
        	// If date field in metabox is greater than date field in settings is page, job expired
        	$content .= "<p> Job Expired </p>";
        } else{
        	// retrieving data with terniary operation
          (empty(get_post_meta($post->ID, '_meta-box-date', true))) ? '' : $date = '<p>Last date :'. $date .'</p>';
          // Terniary operation to find whether the visbility field checked for title oly or not
        	if($myplugin_title_visibility_field == 'value1') {
            $content .= "$title";
          }else{
          // Contents to be displayed
          $content .= "$title $myplugin_organization_name_field  $myplugin_description_field $myplugin_vacancy_field $date $email";
          $content .= '<div class="apply-job">

                    <button class="apply" data-post_id="' . get_the_ID() . '">' .
                        __( 'Apply', 'reportabug' ) .
                    '</button>
                      <form id="application_form" action="#" method="POST" data-url="' . admin_url('admin-ajax.php') . '" enctype="multipart/form-data">
                				<input type="text" class="job" name="post_name" id="post_name" placeholder="Name" required/><br>
                        <span id="name_error_message" class="job"></span>
                        <input type="email" class="job" name="post_email" id="post_email" placeholder="Email" required/><br>
                        <span id="email_error_message" class="job"></span>
                        <input type="text" class="job" name="post_designation" id="post_designation" placeholder="Designation" required/><br>
                        <span id="designation_error_message" class="job"></span><br>
                				<button type="submit" class="job" ><i class="glyphicon glyphicon-pencil"></i> Submit</button>
                      </form>
                      <div id="result_msg">

                      </div>
                      </div>' ;
          }
        }
      } else{
      	$content .= "<p>Nothing to show</p>";
      }
      $content .= "</div>";
      return $val . $content;

    }
  }
  class AddApplication extends JobsSettings{
    // Initialized usng constructor
    public function __construct(){
      // Hooks
      global $jal_db_version;
      $jal_db_version = '1.0';
      add_action( 'init', array($this,'create_jobs') );
      add_action( 'admin_init', array($this,'my_admin' ));
      add_action( 'admin_init', array($this,'create_application_metabox' ));
      add_action("save_post", array($this,"save_custom_meta_box"));
      add_filter('the_content',array($this,'display_front_end'),20,1);
      add_action('admin_menu', array($this,'add_jobs_submenu_example'));
      add_action( 'admin_init', array($this,'myplugin_settings_init' ));
      add_action( 'wp_enqueue_scripts', array($this,'wpb_adding_styles'));
      add_action('wp_ajax_nopriv_save_post_details_form',array($this,'save_enquiry_form_action'));
      add_action('wp_ajax_save_post_details_form',array($this,'save_enquiry_form_action'));
      add_action('wp_ajax_nopriv_save_post_details_form', array($this,'save_enquiry_form_action'));
      add_action('admin_head', array($this,'my_action_javascript'));
      add_action('wp_ajax_your_delete_action', array($this,'delete_row'));
      add_action( 'wp_ajax_nopriv_your_delete_action', array($this,'delete_row'));
      register_activation_hook( __FILE__, array($this,'jal_install'));
      // add_action( 'admin_enqueue_scripts', array($this,'wpdocs_selectively_enqueue_admin_script') );
    }

    // Create table on installation
    public function jal_install() {
    	global $wpdb;
    	// global $jal_db_version;
    	$table_name = $wpdb->prefix . 'addjob';
    	// $charset_collate = $wpdb->get_charset_collate();
      $sql = "CREATE TABLE $table_name (
          		job_id int NOT NULL AUTO_INCREMENT,
          		name varchar(50) NOT NULL,
          		email varchar(50) NOT NULL,
          		designation varchar(50) NOT NULL,
              post_id int NOT NULL,
          		PRIMARY KEY  (job_id)
          	);";
    	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    	dbDelta( $sql );
    	// add_option( 'jal_db_version', $jal_db_version );
    }
    // For including css and javascript
    function wpb_adding_styles() {
      wp_enqueue_style( 'apply-job', plugin_dir_url( __FILE__ ) . 'css/style.css' );

      wp_enqueue_script( 'apply-job', plugin_dir_url( __FILE__ ) . 'js/scripts.js?newversion', array( 'jquery' ), null, true );

      // set variables for script
      wp_localize_script( 'apply-job', 'settings', array(
          'ajaxurl'    => admin_url( 'admin-ajax.php' ),
          'send_label' => __( 'Applying', 'apply' ),
          'post_id' => get_the_ID()
      ) );
    }
    // callback function for inserting the application through front end
    function save_enquiry_form_action() {
      global $wpdb;
      $table_name = $wpdb->prefix . 'addjob';
      // The values posted via ajax are stored in variables
      $post_name = sanitize_text_field($_POST['post_details']['post_name']);
      $post_email = sanitize_email($_POST['post_details']['post_email']);
      $post_designation = sanitize_text_field($_POST['post_details']['post_designation']);
      $post_id = $_POST['post_details']['post_id'];
      // store the contents posted via ajax in an array
      $args = array(
    		'name'=> $post_name,
    		'email'=>$post_email,
        'designation'=>$post_designation,
        'post_id'=>$post_id
    	);
      // For inserting the data
      if(is_email($post_email)){
    	  $is_post_inserted = $wpdb->insert($table_name,$args);
        // Check whether the value is inserted or not , then return json encoded the data.
      	if($is_post_inserted) {
          $output = array(
                      'name' => $post_name,
                      'email'  => $post_email,
                      'designation' => $post_designation
                     );

      		wp_send_json_success($output);
      	} else {
      		wp_send_json_error("Please try again");
      	}
      }
    }
    // function for creating metabox for viewing applicants
    public function create_application_metabox() {
    	// Adding metabox
    	add_meta_box( 'job_application_meta_box',
    		'View Job Application',
    		array($this, 'display_job_application_meta_box'),
    		'jobs', 'normal', 'high'
    	);
    }
    public function display_job_application_meta_box(){
      ?>
      <?php
      // Get the id of post
      $current_post_id = get_the_ID();
      global $wpdb;
      $table_name = $wpdb->prefix . 'addjob';
      // Retieving data from database
      $result = $wpdb->get_results("SELECT * FROM $table_name where post_id=$current_post_id");
      // echo $current_post_id;
      if($result){
        echo "<form id='view_application'><table border='0'>";
        echo "<tr><th>Name</th><th>Email</th><th>Designation</th><th>Action</th></tr>";
        foreach ( $result as $print )   {
          ?>
          <tr>
            <td><?php esc_attr_e($print->name);?></td>
            <td><?php esc_attr_e($print->email);?></td>
            <td><?php esc_attr_e($print->designation);?></td>
            <td> <a name="delete" id="delete" class="delete" data-id="<?php echo $print->job_id; ?>" data-url="<?php echo admin_url( 'admin-ajax.php' ) ?>">Delete</a> </td>
          </tr>
        <?php
        }
            echo "</table></form>";
      }
      else {
        echo "<table border='0'><tr><td> No Records Found </td></tr></table>";
      }
    }
    public function my_action_javascript() {
    ?>
    <script type="text/javascript" >
    jQuery(document).ready(function($) {
      // Delete records on click delete anchor tag
      $('.delete').click(function(event){
        event.preventDefault();
        var tr = $(this).closest('tr');
        var form= $(this);
        var id= form.data('id');
        var link= form.data('url');
        // ajax
        jQuery.ajax({
            type: 'POST',
            url: link,
            data: {"action": "your_delete_action", "element_id": id},
            error: function(error) {
                alert("Insert Failed" + error);
            },
            success: function (data) {
             alert("Deleted");
             // Fadeout and remove table row
             setTimeout(function () {
                         tr.fadeOut(1000,function(){
                           tr.remove();
                         });
                     }, 300);
            }

        });

      });
    });
    </script>
    <?php
    }
    // callback function tho ajax deletion
    public function delete_row() {
      global $wpdb;
      $id = $_POST['element_id'];
      // delete the record
      $deleted = $wpdb->delete( 'wp_addjob', array( 'job_id' => $id ) );
      if($deleted){
        return "success";
      }else{
        return "error";
      }
    }
    // function wpdocs_selectively_enqueue_admin_script() {
    //   wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'application.js', array(), '1.0' );
    //   // set variables for script
    //   wp_localize_script( 'my_custom_script', 'job', array(
    //       'ajaxurl'    => admin_url( 'admin-ajax.php' ),
    //       'send_label' => __( 'Applying', 'apply' ),
    //       'post_id' => get_the_ID()
    //   ) );
    // }


  }
  // Object created
  new AddApplication();
  if (!function_exists('write_log')) {
  	function write_log ( $log )  {
  		if ( true === WP_DEBUG ) {
  			if ( is_array( $log ) || is_object( $log ) ) {
  				error_log( print_r( $log, true ) );
  			} else {
  				error_log( $log );
  			}
  		}
  	}
  }

?>
