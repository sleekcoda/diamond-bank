<?php

class DBNTradePrice{
    function __construct() 
    {
        /* 
         * Hook object functions into WorPress 
         */ 
        add_action('admin_init',array('DBNTradePrice','add_settings'));
        add_action('admin_menu',array('DBNTradePrice','add_menu_page'));

        //add_action('admin_menu',array('DBNShare','nse_admin_view'));
        add_action('admin_enqueue_scripts',array('DBNTradePrice','add_scripts'));
        add_action('wp_enqueue_scripts',array('DBNTradePrice','add_scripts'));
        
        register_activation_hook(__DBN_BASE_FILE__,array('DBNTradePrice','plugin_activate'));
        register_deactivation_hook(__DBN_BASE_FILE__,array('DBNTradePrice','plugin_deactivate'));
        /* 
         * Hook object functions into WorPress 
         */ 
        
        add_shortcode('dbn_share_price',array('DBNTradePrice','prices_shortcode'));
        add_shortcode('dbn_admin_records',array('DBNTradePrice','dbn_admin_records'));

        /**
         * Cron action 
         */
        add_action('nse_cron_job', array('DBNTradePrice','nse_cron_task') );

    }
    public static function add_menu_page(){
        add_menu_page( "Diamond Bank", "Share Price", "edit_posts", "dbn_special", array('DBNTradePrice','admin_view'),'dashicons-chart-area',10);
    }
    public static function plugin_activate(){
        self::create_table();
        flush_rewrite_rules();
    }

    public static function plugin_deactivate(){
        flush_rewrite_rules();
    }
    static function nse_trade_request(){

        $args = array(
            'timeout'     => 1200,
            'httpversion' => '1.0',
            'sslverify'   => false,
        ); 
        $url = 'https://marketdataapi.nse.com.ng:8447/v2/api/trade/todaytrades.json?s=DIAMONDBNK&_t=f447346f8987483498e297dde5a2e444';
        $response = wp_safe_remote_get($url,$args);

        return $response;
    }
      
    function nse_cron_task() {

        $response = self::nse_trade_request();
        $seconds = ($response[0] - 621355968000000000) / 10000000;
        $i=1;

        foreach($response as $key => $value):
            $date = (date("Y-m-d",($value[0] - 621355968000000000) / 10000000));
            echo "<tr><td>".$date." </td><td>$value[1]</td><td>$value[2]</td><td>$value[3]</td><td>$value[4]</td><td>$value[5]</td></tr>";
            $i = $i+1;
            
        self::update_table($date,$value[1],$value[2],$value[3],$value[4],$value[5]);
        endforeach;
    }

    public static function add_scripts($hook){
        /**
         * Add JS files
         */
        
        wp_register_script('rc_share_price_js',plugins_url('css.js/admin_script.js',__FILE__),array('jquery'),10,true);
        wp_register_script('rc_jquery_validate',plugins_url('/css.js/validate.js',__FILE__),array('jquery'),10,true);
        wp_register_script('nse-script-js',plugins_url('css.js/nse-script.js',__FILE__),array('jquery'),10,true);

        if(is_admin()):
            wp_enqueue_script( 'jquery-ui-datepicker',array('jquery') );
            wp_enqueue_script('rc_jquery_validate');
            wp_enqueue_script('rc_share_price_js');
        else:
            wp_enqueue_style('nse-style-css',plugins_url('css.js/nse-style.css',__FILE__),'20180730');
        endif;
        
        /**
         * Add CSS files
         */

         /**
          * Do Cron Job
          */
          if ( ! wp_next_scheduled( 'nse_cron_job' ) ) {
            wp_schedule_event( time(), 'daily', 'nse_cron_job' );
        }
    }
    public static function admin_view(){
        include_once('inc/dbn_price_admin_view.php');
    }
    
    
    public static function add_settings(){
        //register_settings('rcshare_price_group','');
    }

    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . "stock_prices";
        $charset = $wpdb->get_charset_collate();
        
         #Check to see if the table exists already, if not, then create it

        if($wpdb->get_var( "SHOW TABLES LIKE $table_name" ) != $table_name) 
        {

            $sql = "CREATE TABLE {$table_name} ( ";         
            $sql .= "`id` INT NOT NULL AUTO_INCREMENT, ";
            $sql .= "`trade_date` DATE NOT NULL , ";
            $sql .= "`day_open_price` FLOAT NOT NULL , ";
            $sql .= "`day_highest_price` FLOAT NOT NULL , ";
            $sql .= "`day_lowest_price` FLOAT NOT NULL , ";
            $sql .= "`day_close_price` FLOAT NOT NULL , ";
            $sql .= "`volume` FLOAT(16) NOT NULL , ";
            $sql .= "PRIMARY KEY (`id`)) ";
            $sql .= "{$charset};";
            
            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
            $details = dbDelta($sql);
            /*
            This is for debug purpose
            
            */foreach($details as $detail):
                printf("<p><error>%s</error><p>",$detail);
            endforeach;
        }
        
    }
    
    private static function update_table($date,$open,$high,$low,$close,$volume){
        global $wpdb;
        $table_name = $wpdb->prefix . "stock_prices";
        $wpdb->insert( 
            $table_name, 
            array(
                'id'    =>  '',
                'trade_date' => $date, 
                'day_open_price' => $open, 
                'day_highest_price' => $high, 
                'day_lowest_price' => $low, 
                'day_close_price' => $close, 
                'volume' => $volume, 
            ) 
        );
    }
    
    public static function get_current_day(){
        global $wpdb;
        $tablename = $wpdb->prefix.'stock_prices';
        $data = $wpdb->get_row("SELECT * FROM `$tablename` WHERE trade_date=(SELECT MAX(trade_date) FROM `$tablename`)");
        return $data;   
    }
    
    public static function get_previous_day(){
        $most_recent_day = self::get_current_day();
        global $wpdb;
        $tablename = $wpdb->prefix.'stock_prices';
        
        $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($most_recent_day->trade_date)));
        $previous_day = $wpdb->get_row("SELECT * FROM `$tablename` WHERE trade_date = '$previous_date'");
        return $previous_day;
    } 
    
    public static function dbn_admin_records(){
        global $wpdb;
        $tablename = $wpdb->prefix.'stock_prices';
        $datas       = $wpdb->get_results("SELECT * FROM $tablename ORDER BY `trade_date` DESC LIMIT 30;");
        
        foreach($datas as $data):
           echo "<tr> <td>".(date('M d Y',strtotime($data->trade_date) ))."</td> <td>".$data->day_open_price."</td> <td>". $data->day_highest_price." </td> <td>". $data->day_lowest_price ."</td>"." </td> <td>". $data->day_close_price ."</td> <td>".number_format_i18n($data->volume,0)  ."</td></tr>";
        endforeach;
     
    }
    
    /*public static function dbn_range_records(){
        global $wpdb;
        $tablename = $wpdb->prefix.'stock_prices';
        $datas       = $wpdb->get_results("SELECT * FROM $tablename ORDER BY `trade_date` DESC LIMIT 30;");
        
        foreach($datas as $data):
           echo "<tr> <td>".(date('M d Y',strtotime($data->trade_date) ))."</td> <td>".$data->day_open_price."</td> <td>". $data->day_highest_price." </td> <td>". $data->day_lowest_price ."</td>"." </td> <td>". $data->day_close_price ."</td> <td>".number_format_i18n($data->volume,0)  ."</td></tr>";
        endforeach;
     
    }*/

    function prices_shortcode( $atts ) {
        wp_enqueue_script('nse-script-js');
        $previous_day = self::get_previous_day();
        $p_close =     $previous_day->day_close_price;
        $p_open =     $previous_day->day_open_price;
        $p_volume =     $previous_day->volume;
        $p_highest =     $previous_day->day_highest_price;
        $p_lowest =     $previous_day->day_lowest_price;
        $current_day = self::get_current_day();
        if(isset($atts['get']) && $atts['get']=='date'):
            $atts['get'] = date('D, M j, Y', strtotime($current_day->trade_date));
            return "<span class=\"ir-stock-date\">{$atts['get']}</span>";
        elseif(isset($atts['get']) && $atts['get']=='share_price'):
            $growth = ($current_day->day_open_price >= $p_close ) ? 'gain-up' : 'gain-down';
            $rise = $current_day->day_open_price - $p_close;
            $growth = ($rise > 0) ? 'gain-up' : 'gain-down';
            $atts['get'] = number_format_i18n($current_day->day_open_price, 2);
            return '<span class="ir-stock-price '.$growth.'">'.$atts['get'].'</span> <span class="small ir-stock-price-rise">('.$rise.')</span>';
            //return "₦ {$atts['get']}".$arrow;
        elseif(isset($atts['get']) && $atts['get']=='open'):
            $growth = ($current_day->day_open_price >= $p_close ) ? 'gain-up' : 'gain-down';
            //$arrow = ($current_day->day_open_price >= $p_close ) ? '&nbsp;<i class="fa fa-arrow-up" style="color:green;"></i>' : '&nbsp;<i class="fa fa-arrow-down" style="color:red;"></i>';
            $atts['get'] = number_format_i18n($current_day->day_open_price, 2);
            return '<span class="ir-stock-price '.$growth.'">'.$atts['get'].'</span>';
            //return "₦ {$atts['get']}".$arrow;
        elseif(isset($atts['get']) && $atts['get']=='close'):
            $growth = ($current_day->day_open_price >= $p_close ) ? 'gain-up' : 'gain-down';
            $atts['get'] = number_format_i18n($current_day->day_close_price, 2);
            //$arrow = ($current_day->day_close_price >= $current_day->day_open_price ) ? '&nbsp;<i class="fa fa-arrow-up" style="color:green;"></i>' : '&nbsp;<i class="fa fa-arrow-down" style="color:red;"></i>';
            return '<span class="ir-stock-price '.$growth.'">'.$atts['get'].'</span>';
            //return "₦ {$atts['get']}".$arrow;
        elseif(isset($atts['get']) && $atts['get']=='highest'):
            $atts['get'] = number_format_i18n($current_day->day_highest_price, 2);
            return "₦ {$atts['get']}";
        elseif(isset($atts['get']) && $atts['get']=='lowest'):
            $atts['get'] = number_format_i18n($current_day->day_lowest_price, 2);
            return "₦ {$atts['get']}";
        elseif(isset($atts['get']) && $atts['get']=='volume'):
            //$arrow = ($current_day->volume >= $p_volume ) ? '&nbsp;<i class="fa fa-arrow-up" style="color:green;"></i>' : '&nbsp;<i class="fa fa-arrow-down" style="color:red;"></i>';
            $atts['get'] = number_format_i18n($current_day->volume,0);
            $growth = ($current_day->volume >= $p_volume ) ? 'gain-up' : 'gain-down';
            return '<span class="ir-stock-volume '.$growth.'">'.$atts['get'].'</span>';
        elseif(isset($atts['get']) && $atts['get']=='past_year'):
            global $wpdb;
            $tablename = $wpdb->prefix.'stock_prices';
            $datas = $wpdb->get_results("SELECT * FROM $tablename WHERE `trade_date` >= DATE_SUB(NOW(),INTERVAL 1 YEAR) ORDER BY `trade_date` DESC LIMIT 30;");
        else: return "<code style=\"color:orange;\">unknown</code>";
        endif;  
    }
    //This code below did the job of update the table from nse
    //$response = [[635932512000000000.0,1.540000,1.550000,1.480000,1.520000,2462325.00],[635626656000000000.0,3.800000,3.800000,3.780000,3.790000,6633706.00]];
                    ////$seconds = ($response[0] - 621355968000000000) / 10000000;
                    //$i=1;
                    //foreach($response as $key => $value):
                    //    $date = (date("Y-m-d",($value[0] - 621355968000000000) / 10000000));
                    //    echo "<tr><td>".$date." </td><td>$value[1]</td><td>$value[2]</td><td>$value[3]</td><td>$value[4]</td><td>$value[5]</td></tr>";
                    //    $i = $i+1;
                    //    //self::update_table($date,$value[1],$value[2],$value[3],$value[4],$value[5]);
                    //endforeach;
    

}