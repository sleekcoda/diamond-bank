<?php

class DBNYearlyReport{
    function __construct(){
        
        register_activation_hook(__DBN_BASE_FILE__,array('DBNYearlyReport','create_table'));
        add_action('admin_notices',array('DBNYearlyReport','admin_notification'));
        add_action('admin_menu',array('DBNYearlyReport','yearly_report_page'));

        add_action('wp_enqueue_scripts',array('DBNYearlyReport','styles_scripts'));
        add_action('admin_enqueue_scripts',array('DBNYearlyReport','styles_scripts'));

        add_action( 'wp_ajax_dbn_update_yearly_record', array('DBNYearlyReport','update_record') );
        add_action( 'wp_ajax_dbn_delete_record', array('DBNYearlyReport','delete_record') );

        
        add_shortcode('dbn_yearly_report_ui',array('DBNYearlyReport','report_ui_generator'));
    }

    static function yearly_report_page(){
        
        add_submenu_page( 'dbn_special', 'Yearly Report', 'Yearly Report', 'edit_posts', 'dbn_yearly_report', array('DBNYearlyReport','admin_view' ));
    }
    
     static function admin_view(){
         wp_enqueue_media();
         wp_enqueue_script('dbn-report-form-js');
         $reports=self::get_all_records();
        include_once(__DIR__.'/inc/yearly_report_admin_view.php');
    }

    static function delete_record(){
        global $wpdb;
        $table_name = $wpdb->prefix . "dbn_yearly_report_";
        $wpdb->delete($table_name,array(
            'id'        => $_POST['row']
        ) );

        set_transient('record_deleted', true, 30);
    }

    static function admin_notification(){
        if(get_transient('report_submitted')):
            
            $message =  '<div class="notice notice-success is-dismissible">';
            $message .= '<p>Record Updated successfully</p>';
            $message .= '</div>';
            echo $message;
        
        elseif(get_transient('record_deleted')):
            $message =  '<div class="notice notice-success is-dismissible">';
            $message .= '<p>Record Deleted successfully</p>';
            $message .= '</div>';
            echo $message;
        endif;
    }

    static function update_record() {
        if(!current_user_can('edit_posts')):
			wp_die("Unauthorize user");
		endif;
		
		if(!wp_verify_nonce($_POST['nonce'],'yearly_form_action')):
			wp_die('Form validation error');
		endif;
        
        if(!empty($_POST['date']) && wp_verify_nonce($_POST['nonce'],'yearly_form_action') ):
            $date = date_format(date_create($_POST['date']),'Y-m-d');
            $year = date_format(date_create($_POST['date']),'Y');
            $category= sanitize_text_field($_POST['category']);
            $title= sanitize_text_field($_POST['title']);
            $doc_id = (int) sanitize_text_field($_POST['document_id']);
            self::insert_record($year,$category,$title,$doc_id,$date);
        endif;
        exit();
    }
    
	
    static function insert_record($year,$category,$title,$doc_id,$date){
        
        global $wpdb;
        $table_name = $wpdb->prefix . "dbn_yearly_report_";
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM '$table_name' WHERE `date` = %d AND `document_id` = %s ", $date,$doc_id
          ) );
        
          if ( $exists===null ) {
            /* Your insert code here */
            $wpdb->insert($table_name,array(
                'id'        => '',
                'year'        => $year,
                'category'  => $category,
                'title'  => $title,
                'document_id'  => $doc_id,
                'date'      => $date
            ) );
          }
        

             

            set_transient('report_submitted', true, 15);
            exit();
            wp_send_json_success( '<div class="notice notice-success is-dismissible"><p>Report inserted successfully</p></div>');
        
       
    }

    static function styles_scripts(){
        wp_register_script('dbn-report-js',plugins_url( 'css.js/report-ui.js', __FILE__ ),array('jquery'),time(),true);
        wp_register_script('dbn-report-form-js',plugins_url( 'css.js/file-uploader.js', __FILE__ ),array('jquery'),'20180815',true);
        wp_register_style('dbn-report-css',plugins_url( 'css.js/report-ui.css', __FILE__  ),array(),time());
    }

    static function report_ui_generator(){

        wp_enqueue_style('dbn-report-css');
        wp_enqueue_script('dbn-report-js');
        $datas = self::get_ui_records();
        
        echo '<ul class="col-xs-2" id="years">';
        foreach($datas as $key => $item):
            echo "<li class='year_content_nav'><a href='#".$item['year']."'>".$item['year']."</a></li>";
        endforeach;
        echo '</ul>';
        echo '<div class="col-md col-xs"><div id="report_container">';

        foreach($datas as $key => $objects):
            $object = json_decode($objects['object']);
                include(plugin_dir_path(__FILE__).'/inc/report-ui-multiple.php');
        endforeach;
        echo '</div></div>';
        
            
    }

    static function get_ui_records(){
        global $wpdb;
       	$table_name = $wpdb->prefix . "dbn_yearly_report_";
    	$charset = $wpdb->get_charset_collate();//
    	$result = $wpdb->get_results("SELECT year,CONCAT('[',GROUP_CONCAT(CONCAT('{\"title\":\"',title,'\",\"category\":\"',category,'\",\"id\":\"',document_id,'\"}')),']')  object FROM {$table_name} GROUP BY year ORDER BY year DESC ;",ARRAY_A);
    	return $result;
    }
    static function get_all_records(){
        global $wpdb;
       	$table_name = $wpdb->prefix . "dbn_yearly_report_";
    	$charset = $wpdb->get_charset_collate();
    	$result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY year DESC;",ARRAY_A);
    	return $result;
    }

    static function create_table() {
       	global $wpdb;
       	$table_name = $wpdb->prefix . "dbn_yearly_report_";
    	$charset = $wpdb->get_charset_collate();
    	
    	 #Check to see if the table exists already, if not, then create it
    
    	if($wpdb->get_var( "SHOW TABLES LIKE $table_name" ) != $table_name): 
    	
    		$sql = "CREATE TABLE {$table_name}";
    		$sql    .=  "(`id` INT NOT NULL AUTO_INCREMENT,";
    		$sql    .=  "`year` INT(4) NOT NULL ,";
    		$sql    .=  "`category` TEXT(255)  NULL ,";
    		$sql    .=  "`title` TEXT(255)  NULL ,";
    		$sql    .=  "`document_id` TEXT(255)  NULL ,";
    		$sql    .=  "`date` DATE NOT NULL , ";
    		$sql    .=  "PRIMARY KEY (`id`))";

    		$sql .= "{$charset};";
    		
    		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    		$details = dbDelta($sql);
    		/*
    		This is for debug purpose
    		foreach($details as $detail):
    			printf("<p><error>%s</error><p>",$detail);
    		endforeach;
    		*/
    	endif;
		
    }
}