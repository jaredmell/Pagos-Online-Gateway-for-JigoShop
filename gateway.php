<?php

class pagosonline_jigoshop_gateway extends jigoshop_payment_gateway
{

  const ID = 'pagosonline';

  var $settings;

  public function __construct() {
    $this->id = self::ID;
    $this->icon = 'pagosonline.png';
    $this->has_fields = true;

    $this->settings = pagosonline_jigoshop_settings::getInstance();

    $this->enabled = $this->settings->enabled;
    $this->title = $this->settings->title;
    $this->description = $this->settings->description;
    $this->liveurl = $this->settings->gatewayurl;
    $this->testurl = pagosonline_jigoshop_settings::TEST_GATEWAY_URL;
    $this->testmode	= $this->settings->testmode;

    // check for pagosonline.net response
    add_action('init', array(&$this, 'check_confirmation'));

    // if the response fron pagosonline.net is valid,
    // process the payment
    add_action('valid-pagosonline-confirmation', array(&$this, 'successful_request'), 1, 2);

    // update admin options
    add_action('jigoshop_update_options', array(&$this, 'process_admin_options'));

    // show the gateway form
    add_action('receipt_pagosonline', array(&$this, 'receipt_page'));
  }

	function icon() {
		if ($this->icon) :
			return '<img src="'. pagosonline_jigoshop::plugin_url() . '/' . $this->icon.'" alt="'.$this->title.'" />';
		endif;
	}

	/**
	* Prints the specific gateway fields to the checkout form
	**/
	function payment_fields() {
    $user_id = get_current_user_id();
    $description = wpautop(wptexturize($this->description));
    $id_type_options = pagosonline_jigoshop_settings::$customer_id_types;

    if ($user_id > 0) {
      $id_type = get_user_meta($user_id, 'pagosonline_id_type', true);
      $id_number = get_user_meta($user_id, 'pagosonline_id_number', true);
      $office_phone = get_user_meta($user_id, 'pagosonline_office_phone', true);
      $mobile_phone = get_user_meta($user_id, 'pagosonline_mobile_phone', true);
    } else {
      $id_type = $id_number = $office_phone = $mobile_phone = NULL;
    }
    $variables = compact('id_type', 'id_type_options', 'id_number', 'office_phone', 'mobile_phone', 'description');
    print pagosonline_jigoshop::render('checkout_form.php', $variables);
	}

  function validate_fields() {
    if (empty($_POST['pagosonline_id_type'])) {
      jigoshop::add_error( __('Please select an identification type from the Pagos Online checkout panel', 'pagosonline'));
    }

    if (empty($_POST['pagosonline_id_number'])) {
      jigoshop::add_error( __('Please provide an identification number in the Pagos Online checkout panel', 'pagosonline'));
    }

    if (!empty($_POST['pagosonline_office_phone']) && !jigoshop_validation::is_phone($_POST['pagosonline_office_phone'])) {
      jigoshop::add_error( __('Provided office phone number in the in the Pagos Online checkout panel seems wrong, please correct.', 'pagosonline'));
    }

    if (!empty($_POST['pagosonline_mobile_phone']) && !jigoshop_validation::is_phone($_POST['pagosonline_mobile_phone'])) {
      jigoshop::add_error( __('Provided mobile phone number in the in the Pagos Online checkout panel seems wrong, please correct.', 'pagosonline'));
    }

    return jigoshop::error_count() ? FALSE : TRUE;
  }

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 **/
	public function admin_options() {
    $admin_form = $this->settings->render_form();
    print pagosonline_jigoshop::render('admin_form.php', compact('admin_form'));
  }

  function process_admin_options() {
    $properties = array_keys($this->settings->get_available());
    foreach ($properties as $prop) {
      if (isset($_POST[$prop])) {
        if ($prop == 'enabledpms' && is_array($_POST[$prop])) {
          $_POST[$prop] = join(",", $_POST[$prop]);
        }
        $this->settings->{$prop} = $_POST[$prop];
      }
    }
    return $this->settings->save();
  }

  /**
	 * Process the payment and return the result
	 **/
	function process_payment($order_id) {

		$order = &new jigoshop_order($order_id);

    if ($this->validate_fields()) {
      $keys = array('id_type', 'id_number', 'office_phone', 'mobile_phone');
      foreach ($keys as $key) {
        $data = $_POST['pagosonline_' . $key];
        if (!empty($data)) {
          $meta_key = 'pagosonline_' . $key;
          $meta_data = mysql_real_escape_string($data);
          update_post_meta($order_id, $meta_key, $meta_data);
          update_user_meta($order->user_id, $meta_key, $meta_data);
        }
      }

      // maybe store the firm for future use?
      return array(
        'result' 	=> 'success',
        'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('jigoshop_pay_page_id'))))
      );
    } else {
      return array('result' => 'error');
    }
	}

  function receipt_page($order_id) {
    $order = &new jigoshop_order($order_id);
    $order->update_status('processing', __('Sending to Pagos Online payment gateway.', 'pagosonline'));
		echo '<p>'.__('Thank you for your order, please click the button below to pay with Pagos Online.', 'pagosonline').'</p>';
		echo $this->generate_gateway_form($order_id);
  }

  function generate_gateway_form($order_id) {
    $order = &new jigoshop_order($order_id);

    $test_mode = $this->settings->testmode == 'yes' ? TRUE : FALSE;
    $action = $this->settings->get_gateway_url($test_mode);
    $cancel_order_url = $order->get_cancel_order_url();

    $firm = $this->firm($this->settings->salt, $this->settings->userid, $order->order_key, $order->order_total, $this->settings->currency);
    update_post_meta($order_id, 'pagosonline_firm', $firm);

    // $salt, $user_id, $purchase, $value, $currency
    $fields = array(
      // header data
      'firma' => $firm,
      'usuarioId' => $this->settings->userid,
      'lng' => $this->settings->defaultlang,
      'prueba' => $test_mode,
      'moneda' => $this->settings->currency,
      'tiposMediosDePago' => $this->settings->enabledpms,

      // order
      'refVenta' => $order->order_key,
      'descripcion' => sprintf(__('Purchase #%s on %s', 'pagosonline'), $order_id, get_bloginfo('name')),
      'valor' => $this->format_number($order->order_total),
      'iva' => $this->format_number($order->get_total_tax()),
      'baseDevolucionIva' => $this->format_number($order->get_total_tax() > 0 ? $order->order_total - $order->get_total_tax() : 0),

      // customer
      'tipoDocumentoIdentificacion' => get_post_meta($order->id, 'pagosonline_id_type', true),
      'documentoIdentificacion' => get_post_meta($order->id, 'pagosonline_id_number', true),
      'nombreComprador' => $order->billing_first_name . ' ' . $order->billing_last_name,
      'direccionCobro' => $order->billing_address_1 . (!empty($order->billing_address_2) ? '(' . $order->billing_address_2 . ')' : ''),
      'ciudadCobro' => sprintf('%s (%s)', $order->billing_city, $order->billing_state),
      'telefono' => $order->billing_phone,

      // callbacks
      'url_respuesta' => add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(get_option('pagosonline_response_page_id')))),
      'url_confirmacion' => add_query_arg('order', $order_id, add_query_arg('pagosonline_listener', 'pagosonline_traditional', trailingslashit(get_bloginfo('wpurl')))),
    );

    // optionals
    $office_phone = get_post_meta($order->id, 'pagosonline_office_phone', true);
    $mobile_phone = get_post_meta($order->id, 'pagosonline_mobile_phone', true);
    if (!empty($office_phone)) {
      $data['telefonoOficina'] = $office_phone;
    }
    if (!empty($mobile_phone)) {
      $data['telenfonoMovil'] = $mobile_phone;
    }

    if ($this->settings->enabledpms) {
      $data['tiposMedioDePago'] = $this->settings->enabledpms;
    }
    if ($this->settings->psecode) {
      $data['codigo_pse'] = $this->settings->psecode;
    }
    if ($this->settings->template) {
      $data['plantilla'] = $this->settings->template;
    }

    // todo: add shipping?

    print pagosonline_jigoshop::render('gateway_form.php', compact('action', 'fields', 'cancel_order_url'));
  }

  function check_confirmation() {
    if (isset($_GET['pagosonline_listener']) && isset($_GET['order']) && $_GET['pagosonline_listener'] == 'pagosonline_traditional') {
      $data = stripslashes_deep($_POST);
      $order = intval($_GET['order']);

      if (!$order) {
        pagosonline_jigoshop::log('No order id provided');
        print "Invalid";
        exit(0);
      }

      if (self::validate_response($data, $order)) {
        do_action('valid-pagosonline-confirmation', $data, $order);
      } else {
        print "Invalid";
        exit(0);
      }
    }
  }


  function successful_request($data, $order_id) {

    $order = new jigoshop_order($order_id);

    if ($order->order_key !== $data['ref_venta']) exit;

    if ($order->status !== 'completed') {
      $response_code = intval($data['codigo_respuesta_pol']);
      $transaction_code = intval($data['estado_pol']);

      $state = self::get_order_state_for_response_code($response_code);
      $responses = pagosonline_jigoshop_settings::$responses;
      $transaction_statuses = pagosonline_jigoshop_settings::$transaction_statuses;

      switch ($state) {
        case 'completed':
          $order->payment_complete();
		    break;

        case 'on-hold':
        case 'processing':
          $s = strtolower($transaction_statuses[$transaction_code]);
          $note = sprintf(__('Payment %s via Pagos Online.', 'pagosonline'), $s);
          if ($order->status != $state) {
            $order->update_status($state, $note);
          } else {
            $order->add_order_note($note);
          }
        break;

        case 'cancelled':
          $order->cancel_order();
          break;
      }

      $log = array();
      $log[] = sprintf(__('Pagos Online responded: %s (#%d)', 'pagosonline'), $responses[$response_code], $response_code);

      if (!empty($data['ref_pol'])) {
        $log[] = sprintf(__('Transaction Reference: %d', 'pagosonline'), $data['ref_pol']);
      }

      if (!empty($data['medio_pago'])) {
        $payment_mode = intval($data['medio_pago']);
        $available_payments = pagosonline_jigoshop_settings::$payment_types[$payment_mode];
        if (isset($available_payments[$payment_mode])) {
          $log[] = sprintf(__('Payment Method: %s', 'pagosonline'), $available_payments[$payment_mode]);
        }
      }

      if (!empty($data['codigo_autorizacion'])) {
        $log[] = sprintf(__('Auth Code: %s', 'pagosonline'), $data['codigo_autorizacion']);
      }

      if (!empty($data['banco_pse'])) {
        $log[] = sprintf(__('Bank Name: %s', 'pagosonline'), $data['banco_pse']);
      }
      if (!empty($data['cus'])) {
        $log[] = sprintf(__('Bank Payment Reference: %d', 'pagosonline'), $data['cus']);
      }

      $note = implode('<br />', $log);
      $order->add_order_note($note);
    }
  }

  static function get_order_state_for_response_code($code) {
    $state = 'pending';

    switch ($code) {
      // cancelled
      case 2:
      case 3:
      case 4:
      case 5:
      case 6:
      case 7:
      case 8:
      case 9:
      case 10:
      case 11:
      case 12:
      case 14:
      case 16:
      case 17:
      case 18:
      case 22:
      case 23:
      case 25:
      case 9995:
      case 9996:
      case 9997:
      case 9998:
      case 9999:
        $state = 'cancelled';
        break;

      case 15:
      case 21:
      case 26:
      case 9994:
        $state = 'on-hold';
        break;

      case 1:
      case 19:
      case 20:
      case 24:
        $state = 'completed';
        break;
    }

    return $state;
  }

  static function validate_response($data, $order_id) {
    $required = array('firma', 'usuario_id', 'ref_venta', 'estado_pol', 'codigo_respuesta_pol', 'moneda', 'valor');

    foreach ($required as $field) {
      if (array_key_exists($field, $data) && !empty($data[$field])) {
      } else {
        $message = sprintf('Error while validating confirmation for order #%d. "%s" wasn\'t provided.', $order_id, $field);
        pagosonline_jigoshop::log($message . ' {' . print_r($data, true) . '}');
        return FALSE;
      }
    }

    //$order = &new jigoshop_order($order_id);
    $salt = pagosonline_jigoshop_settings::getInstance()->salt;
    $firm = self::firm($salt, $data['usuario_id'], $data['ref_venta'], $data['valor'], $data['moneda'], $data['estado_pol']);
    if (strtoupper($firm) != $data['firma']) {
      $message = sprintf('Error validating firm for confirmation for order #%d. %s was provided, expected %s', $order_id, $data['firma'], $firm);
      pagosonline_jigoshop::log($message . ' {' . print_r($data, true) . '}');
      return FALSE;
    }

    return TRUE;
  }

  static function firm($salt, $user_id, $purchase_id, $value, $currency, $transaction_state = NULL) {
    $data = array($salt, $user_id, $purchase_id, self::format_number($value), $currency);
    if ($transaction_state) {
      $data[] = $transaction_state;
    }
    return md5(join('~', $data));
  }

  static function format_number($number) {
    return number_format($number, 2, '.', '');
  }

}

?>