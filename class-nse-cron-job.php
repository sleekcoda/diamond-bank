<?php
class DBNShare{
    static function nse_admin_html_view(){
        
        include(__DIR__.'/inc/nse_admin_view.php');
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
    public static function nse_admin_view(){
        add_submenu_page('dbn_special', 'NSE Share Price', 'NSE Share Price', 'edit_posts', 'nse_share_price', array('DBNShare','nse_admin_html_view') );
    }
    //if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
    //    wp_schedule_event( time(), 'hourly', 'my_task_hook' );
    //  }
      
      add_action( 'my_task_hook', 'my_task_function' );
      
      function my_task_function() {
        wp_mail( 'your@email.com', 'Automatic email', 'Automatic scheduled email from WordPress.');
      }
}