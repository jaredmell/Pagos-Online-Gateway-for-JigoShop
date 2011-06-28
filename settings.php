<?php

class pagosonline_jigoshop_settings
{

  const LIVE_GATEWAY_URL = "https://gateway.pagosonline.net/apps/gateway/index.html";
  const TEST_GATEWAY_URL = "https://gateway2.pagosonline.net/apps/gateway/index.html";

  public static $available_payment_methods = array(
    2 => 'Tarjetas de crédito',
    3 => 'Verified By VISA',
    4 => 'PSE [Proveedor de Servicios Electrónicos] (Cuentas de ahorro y corrientes)',
    5 => 'Débito ACH',
    7 => 'Pago en efectivo (via Efecty)',
    8 => 'Pago referenciado',
  );

  public static $available_currencies = array(
    'COP' => 'Peso colombiano',
    'EUR' => 'Euros',
    'GBP' => 'Libras esterlinas',
    'MXN' => 'Pesos mexicanos',
    'USD' => 'U.S. Dolar',
    'VEB' => 'Bolívares fuertes',
  );

  public static $available_languages = array(
    'fr' => 'Francés',
    'en' => 'Inglés',
    'es' => 'Español',
    'it' => 'Italiano',
  );

  public static $customer_id_types = array(
    1 => 'Cédula de ciudadanía',
    2 => 'NIT (Sin código de chequeo)',
    3 => 'Cédula de extranjería',
    5 => 'Pasaporte',
    6 => 'Social Security Card',
    7 => 'Sociedad extranjera sin NIT',
    8 => 'Fideicomiso',
    9 => 'Registro Civil',
    10 => 'Carné diplomático',
    99 => 'Otro',
  );

  // from gateway response: estado_pol
  public static $transaction_statuses = array(
    1 => 'Sin abrir',
    2 => 'Abierta',
    4 => 'Pagada y abandonada',
    5 => 'Cancelada',
    6 => 'Rechazada',
    7 => 'En validación',
    8 => 'Reservada',
    9 => 'Reservada fraudulenta',
    10 => 'Enviada a entidad financiera',
    11 => 'Capturando datos de tarjeta de crédito',
    12 => 'Esperando confirmación del sistema PSE',
    13 => 'Activa Débitos ACH',
    14 => 'Confirmando pago via Efecty',
    15 => 'Impreso',
    16 => 'Débito ACH Registrado',
  );

  // codigo_respuesta_pol
  public static $responses = array(
    1 => 'Transacción aprobada',
    2 => 'Pago cancelado por el usuario',
    3 => 'Pago cancelado por el usuario durante validación',
    4 => 'Transacción rechazada por la entidad',
    5 => 'Transacción declinada por la entidad',
    6 => 'Fondos insuficientes',
    7 => 'Tarjeta inválida',
    8 => 'Acuda a su entidad',
    9 => 'Tarjeta vencida',
    10 => 'Tarjeta restringida',
    11 => 'Discrecional POL',
    12 => 'Fecha de expiración o campo de seguridad inválido',
    13 => 'Repita la transacción',
    14 => 'Transacción inválida',
    15 => 'Transacción en proceso de validación',
    16 => 'Combinación de usuario y contraseña inválidos',
    17 => 'El monto excede el máximo permitido por la entidad',
    18 => 'Documento de identidad inválido',
    19 => 'Transacción abonada, capturando datos de la tarjeta de crédito',
    20 => 'Transacción abandonada',
    21 => 'Imposible reservar transacción',
    22 => 'La tarjeta no esta autorizada para realizar compras por internet',
    23 => 'Transacción parcial aprobada',
    25 => 'Rechazada por no confirmación',
    26 => 'Comprobante generado, esperando pago en banco',
    9994 => 'Transacción pendiente por confirmar',
    9995 => 'Certificado digital no encontrado',
    9996 => 'La entidad no responde',
    9997 => 'Error de mensajería con la entidad financiera',
    9998 => 'Error en la entidad financiera',
    9999 => 'Error no especificado',
  );

  static $payment_types = array(
    10 => 'VISA',
    11 => 'MASTERCARD',
    12 => 'AMEX',
    22 => 'DINERS',
    24 => 'Verified By VISA',
    25 => 'PSE',
    27 => 'VISA Débito',
    30 => 'Efecty',
    31 => 'Pago refenciado',
  );


  var $enabled;
  var $title;
  var $email;
  var $description;

  /* Gateway specific settings */
  var $userid;
  var $salt;
  var $currency;
  var $gatewayurl;
  var $testmode;
  var $enabledpms;
  var $defaultlang;
  var $template;
  var $psecode;

  private static $instance = NULL;

  private static $available;

  const PREFIX = 'jigoshop_pagosonline_';

  private function __construct() {
    $this->load();
  }

  public static function getInstance() {
    if (self::$instance == NULL) {
      self::$instance = new pagosonline_jigoshop_settings();
    }

    return self::$instance;
  }

  public static function get_payment_methods() {
    return self::$available_payment_methods;
  }

  public static function get_currencies() {
    return self::$available_currencies;
  }

  public static function get_languages() {
    return self::$available_languages;
  }

  public function get_available() {
    if (!self::$available) {
      self::$available = array(
        'enabled' => array('Enable'),
        'title' => array('Method Title'),
        'description' => array('Method Description'),
        'userid' => array('User ID', 'The user number provided by Pagos Online. It was sent in the confirmation email right after your account was created.'),
        'salt' => array('Encryption Key', 'Key provided by Pagos Online to firm the transactions. You can find it in the admin module in the Pagos Online Dashboard'),
        'template' => array('Template', 'Let you specify a template previously registered in the Pagos Online Dashboard. You should acquire the Look And Feel feature to this setting to work.'),
        'psecode' => array('PSE Code', 'Use this field if you have a service code created in the PSE platform. It applies for Enterprise and Corporate accounts only.'),
        'testmode' => array('Test Mode', 'Click yes if you want to send requests as tests to the gateway.'),
        //'default_currency' => array('Default Currency', 'The default currency on which the request amount is sent.'),
        'enabledpms' => array('Enabled Payment Methods', ''),
        'defaultlang' => array('Default Language', 'The Pagos Online interface language.'),
      );
    }

    return self::$available;
  }

  public function render_form() {
    $form = new pagosonline_jigoshop_settings_form();
    return $form->output();
  }

  public function load() {
    $this->currency = get_option('jigoshop_currency');
    $ids = array_keys($this->get_available());
    foreach ($ids as $setting) {
      $this->{$setting} = get_option(self::PREFIX . $setting, $this->{$setting} ? $this->{$setting} : NULL);
    }
  }

  public function save() {
    $ids = array_keys($this->get_available());
    foreach ($ids as $setting) {
      update_option(self::PREFIX . $setting, $this->{$setting});
    }
    return true;
  }

  public function get_gateway_url($test = FALSE) {
    if ($test == FALSE) {
      return self::LIVE_GATEWAY_URL;
    } else {
      return self::TEST_GATEWAY_URL;
    }
  }
}

/**
 * A little helper to generate the admin settings form
 * for PagosOnline.net gateway
 */
class pagosonline_jigoshop_settings_form
{
  const FORM_ITEM = '<tr><td class"titledesc">{help}{label}</td><td class="forminp">{input}</td></tr>';
  const HELP = '<a href="#" tip="{text}" class="tips" tabindex="99"></a>';
  const OPTION = '<{tag} value="{value}"{picked}>{label}</{tag}>';
  const INPUT = '<input type="{tag}" name="{name}" value="{value}" size="{size}"{picked}/>{label}';

  public function get_item($label, $input, $help = '') {
    return str_replace(
      array('{label}', '{input}', '{help}'),
      array($label, $input, $help),
      self::FORM_ITEM
    );
  }

  public function get_input($type, $name, $value, $size = '40') {
    return str_replace(
      array('{tag}', '{name}', '{value}', '{size}', '{picked}', '{label}'),
      array('text', $name, $value, $size, '', ''),
      self::INPUT
    );
  }

  public function get_help($text) {
    if (empty($text)) {
      return $text;
    }
    return str_replace('{text}', esc_attr($text), self::HELP);
  }

  public function get_select($name, $selected, $options) {
    return sprintf(
      '<select name="%s">%s</select>',
      $name,
      $this->_construct_options('option', $options, $selected, self::OPTION)
    );
  }

  public function get_options($options, $picked, $type = 'checkbox') {
    return $this->_construct_options($type, $options, $picked, self::INPUT, "<br />\n");
  }

  private function _construct_options($type, $options, $picked, $format, $separator = "\n") {
    $output = array();
    $selected_attr = $type == 'option' ? ' selected="selected"' : ' checked="checked"';
    foreach ($options as $value => $name) {
      if (is_array($name)) {
        list($item_name, $item_label) = $name;
      } else {
        $item_name = $item_label = $name;
      }
      $output[] = str_replace(
        array('{tag}', '{name}', '{value}', '{picked}', '{label}', '{size}'),
        array($type, $item_name, $value, in_array($value, $picked) ? $selected_attr : '', $item_label, ''),
        $format
      );
    }

    return join($separator, $output);
  }

  function output() {
    $form = array();
    $fields = pagosonline_jigoshop_settings::getInstance()->get_available();

    foreach ($fields as $field => $data) {
      if (is_array($data)) {
        if (count($data) > 1) {
          list ($label, $help) = $data;
        } else {
          $label = $data[0];
          $help = '';
        }
      } else {
        $label = $data;
        $help = '';
      }

      $label = __($label, 'pagosonline');
      if (!empty($help)) {
        $help = __($help, 'pagosonline');
      }

      $current = pagosonline_jigoshop_settings::getInstance()->{$field};

      switch ($field) {

        case 'enabled':
        case 'testmode':
          $options = array(
            'no' => array($field, 'No'),
            'yes' => array($field, 'Yes'),
          );

          $input = $field == 'enabled' ? $this->get_select($field, array($current), $options) : $this->get_options($options, array($current), 'radio');

          $form[] = $this->get_item(
            __($label, 'pagosonline'),
            $input,
            $this->get_help($help)
          );
          break;

        case 'currency':
          $options = array();
          $currencies = pagosonline_jigoshop_settings::get_currencies();
          foreach ($currencies as $code => $label) {
            $options[$code] = array($field, $label);
          }
          $form[] = $this->get_item(
            __('Currency', 'net_pagosonline'),
            $this->get_options($options, array($current), 'radio')
          );
          break;

        case 'defaultlang':
          $options = array();
          $languages = pagosonline_jigoshop_settings::get_languages();
          foreach ($languages as $code => $label) {
            $options[$code] = array($field, $label);
          }
          $form[] = $this->get_item(
            __('Default Language', 'pagosonline'),
            $this->get_options($options, array($current), 'radio')
          );
          break;

        case 'enabledpms':
          $options = array();
          $payments = pagosonline_jigoshop_settings::get_payment_methods();
          foreach ($payments as $code => $label) {
            $options[$code] = array($field . '[]', $label);
          }
          $form[] = $this->get_item(
            __('Enabled Payment Methods', 'pagosonline'),
            $this->get_options($options, split(',', $current), 'checkbox')
          );
          break;

        default:
          $form[] = $this->get_item(
            __($label, 'pagosonline'),
            $this->get_input('text', $field, $current),
            $this->get_help($help)
          );
          break;
      }
    }
    return join("", $form);
  }
}

?>