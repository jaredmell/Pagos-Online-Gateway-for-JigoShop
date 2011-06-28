<?php

$pagosonline_plugin_path = 'pagosonline_4_jigoshop/plugin.php';

require_once ABSPATH . '/wp-admin/includes/plugin.php';
if (is_plugin_active($pagosonline_plugin_path)) {
  require_once WP_PLUGIN_DIR . '/' . $pagosonline_plugin_path;
}

?>