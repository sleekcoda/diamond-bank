<?php
/**
*   Plugin Name:    Diamond Bank
*   Plugin URI:     http://rightclickng.com
*   Author:         Toheeb Ogunleye
*   Author URI:     http://rightclickng.com
*   Description:    This plugin helps <strong>Diamond Bank</strong> use wordpress perform special functionality on there website with ease <strong>NSE update price, Yearly report update and more</strong>
*   Version:        1.0
*   License:        GPLv2 or later
*   Text Domain:    rightclickng
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}
define("__DBN_BASE_FILE__",__FILE__);

require(plugin_dir_path( __FILE__ ).'class-dbn-trade-price.php');
require (plugin_dir_path( __FILE__ ).'class-year-report.php');

new DBNTradePrice();
new DBNYearlyReport();