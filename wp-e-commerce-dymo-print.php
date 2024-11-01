<?php
/*
Plugin Name: WP E-Commerce DYMO Print
Plugin URI: http://wordpress.geev.nl/product/wp-e-commerce-dymo-print/
Description: This plugin provides shipping labels for your DYMO label printer from the backend. - Free version
Version: 0.0.2
Author: Bart Pluijms
Author URI: http://www.geev.nl/
*/
/*  Copyright 2012  Geev  (email : info@geev.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once( 'inc/wpsc-dymo-funct.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) {
  load_plugin_textdomain('wpsc-dymo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
  require_once('admin/dymo-settings.php');
  add_action('admin_menu', 'wpsc_dymo_admin_menu');
  add_action('admin_init', 'wpsc_dymo_window');
  add_action('admin_enqueue_scripts', 'wpsc_dymo_scripts');
} else {
  add_action('admin_notices', 'showWPSCAdminMessages');   
}
?>