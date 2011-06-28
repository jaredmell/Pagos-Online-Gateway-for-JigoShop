<?php
/*
Plugin Name: Pagos Online Gateway for Jigoshop
Dependencies: jigoshop/jigoshop.php
Plugin URI: http://github.com/jardmell/pagosonline_4_jigoshop
Description: Blah, blah.
Version: 1.0-alpha
Author: Jared Mell
Author URI: http://webdsgn.me
License: BSD
*/

class pagosonline_jigoshop
{
  private static $instance = NULL;

	public static $plugin_url;
	public static $plugin_path;

  public static $logger;

  const VERSION = "1.0";

  private function __construct() {
    require_once $this->plugin_path() . '/settings.php';

    add_action('init', array(&$this, 'init'));
    // register gateway
    add_filter('jigoshop_payment_gateways', array(&$this, 'register_gateway'));
    // add colombian currency
    add_filter('jigoshop_currencies', array(&$this, 'register_currency'));
    add_filter('jigoshop_currency_symbol', array(&$this, 'set_currency_symbol'), 1, 2);

    add_shortcode('pagosonline_confirmation', array(&$this, 'show_confirmation_page'));

    add_action('wp_print_scripts', array(&$this, 'enqueue_scripts'));
  }

  public static function getInstance() {
    if (self::$instance == NULL) {
      self::$instance = new pagosonline_jigoshop();
    }

    return self::$instance;
  }

  function init() {
		// load text domain
    // npo = net_pagos_online
    $here = dirname(plugin_basename(__FILE__));
		if(!load_plugin_textdomain('pagosonline', false, $here)) {
			load_plugin_textdomain('pagosonline', false, $here);
    }
  }

  function enqueue_scripts() {
    if (is_page(get_option('jigoshop_checkout_page_id'))) {
      wp_enqueue_script('pagosonline_jigoshop', self::plugin_url() . '/plugin.js', NULL, NULL, true);
    }
  }

  function register_gateway($gateways) {
    // add the gateway class to the global namespace
    require_once self::plugin_path() . '/gateway.php';
    $gateways[] = 'pagosonline_jigoshop_gateway';
    return $gateways;
  }

  function register_currency($currencies) {
    $currencies['COP'] = __('Colombian Peso (&#36;)', 'pagosonline');
    return $currencies;
  }

  function set_currency_symbol($currency_symbol, $currency) {
    if ($currency == 'COP') {
      return '&#36;';
    }
    return $currency_symbol;
  }

  function show_confirmation_page() {
    require_once self::plugin_path() . '/pages.php';
    print pagosonline_jigoshop_pages::getInstance()->confirmation_page();
  }

	/**
	 * Get the plugin url
	 *
	 * @return  string	url
	 */
	public static function plugin_url() {
		if (self::$plugin_url) {
      return self::$plugin_url;
    }
		return self::$plugin_url = WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__));
	}

	/**
	 * Get the plugin path
	 *
	 * @return  string	url
	 */
	public static function plugin_path() {
		if (self::$plugin_path) {
      return self::$plugin_path;
    }
		return self::$plugin_path = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
	}

  public static function render($file, $data) {
    $file_path = self::plugin_path() . '/elements/' . $file;
    @ob_start();
    extract($data, EXTR_OVERWRITE);
    include($file_path);
    return ob_get_clean();
  }


  public function install() {
    require_once self::plugin_path() . '/plugin-installer.php';
    pagosonline_jigo_create_pages();
    pagosonline_jigo_create_options();
  }

  public function uninstall() {
    // restore this variables for security
    update_option('jigoshop_pagosonline_userid', '');
    update_option('jigoshop_pagosonline_salt', '');
  }

  public static function log($message, $severity = 1) {
    if (!self::$logger) {
      require_once 'Log.php';
      self::$logger =&Log::singleton('file', self::plugin_path() . '/logs/plugin.log', '> ', array('mode' => 0775, 'timeFormat' => '%X %x'));
    }
    self::$logger->log($message, $severity);
  }
}

$pagosonline_jigoshop = pagosonline_jigoshop::getInstance();

register_activation_hook(__FILE__, array(&$pagosonline_jigoshop, 'install'));
register_deactivation_hook(__FILE__, array(&$pagosonline_jigoshop, 'uninstall'));


?>