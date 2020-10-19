<?php
/*
* Plugin Name: Platon Pay
* Description: “Platon Pay” is perfect for both single-page landing page and small sites where there is no large catalog of goods and store functions.
* Author: udjin
* Version: 1.9
* Requires at least: 4.7
* Requires PHP: 5.2
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


/**********************************************************************************************************************/
/*Создание таблиц в базе после установки плагина***********************************************************************/
/**********************************************************************************************************************/
function platon_pay_activate() {
	if(current_user_can('administrator')){
		global $wpdb;
		$table_name = $wpdb->prefix . 'platon_shortcodes';
		$table_invoice = $wpdb->prefix . 'platon_invoice';
		$table_order = $wpdb->prefix . 'platon_orders';
		
		/*Включение тестового режима*/
		update_option( 'platon_test_mode', 1, true);
		
		$sql = "CREATE TABLE $table_name (
		    id int(11) NOT NULL AUTO_INCREMENT,
		    id_shortcode int(11) NOT NULL,
		    name_field varchar(255) DEFAULT NULL,
		    value_field varchar(255) DEFAULT NULL,
		    UNIQUE KEY id (id)
		) DEFAULT CHARACTER SET utf8;";
		
		$sql_invoice = "CREATE TABLE $table_invoice (
		    id int(11) NOT NULL AUTO_INCREMENT,
		    status varchar(55) DEFAULT NULL,
		    create_date varchar(20) DEFAULT NULL,
		    order_platon varchar(55) DEFAULT NULL,
		    UNIQUE KEY id (id)
		) DEFAULT CHARACTER SET utf8;";
		
		$sql_order = "CREATE TABLE $table_order (
		    id int(11) NOT NULL AUTO_INCREMENT,
		    invoice varchar(11) DEFAULT NULL,
		    name_field varchar(55) DEFAULT NULL,
		    value_field varchar(255) DEFAULT NULL,
		    UNIQUE KEY id (id)
		) DEFAULT CHARACTER SET utf8;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		dbDelta( $sql_invoice );
		dbDelta( $sql_order );
	}
}
register_activation_hook( __FILE__, 'platon_pay_activate' );

/**********************************************************************************************************************/
/*Удаление данных с базы после удаление плагина************************************************************************/
/**********************************************************************************************************************/
register_uninstall_hook( __FILE__, 'platon_pay_options_uninstall' );
function platon_pay_options_uninstall(){
	if(current_user_can('administrator')){
		delete_option('platon_url_field');
		delete_option('platon_secret_key');
		delete_option('platon_password_key');
		delete_option('return_url');
			delete_option('platon_first_name');
			delete_option('platon_first_name_require');
			delete_option('platon_first_name_placeholder');
		delete_option('platon_last_name');
		delete_option('platon_last_name_require');
		delete_option('platon_last_name_placeholder');
			delete_option('platon_email');
			delete_option('platon_email_require');
			delete_option('platon_email_placeholder');
		delete_option('platon_phone');
		delete_option('platon_phone_require');
		delete_option('platon_phone_placeholder');
			delete_option('platon_comment');
			delete_option('platon_comment_require');
			delete_option('platon_comment_placeholder');
			delete_option('pay_button_color');
		delete_option('name_button_form');
			delete_option('color_overlay');
			delete_option('opacity_overlay');
			delete_option('color_form');
			delete_option('color_button_form');
		delete_option('message_success_order');
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'platon_shortcodes';
		$table_invoice = $wpdb->prefix . 'platon_invoice';
		$table_order = $wpdb->prefix . 'platon_orders';
		
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		$wpdb->query( "DROP TABLE IF EXISTS $table_invoice" );
		$wpdb->query( "DROP TABLE IF EXISTS $table_order" );
	}
}


/**********************************************************************************************************************/
/*Добавление ссылки в левый sidebar для настроек плагина***************************************************************/
/**********************************************************************************************************************/
function platon_pay_admin_add_menu(){
	add_menu_page(
		'Настройки плагина Platon',
		'Platon Pay',
		'manage_options',
		plugin_dir_path( __FILE__ ).'inc/settings.php',
		'',
		plugin_dir_url( __FILE__ )."images/icon.png"
	);

	wp_enqueue_script( 'script',  plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery'));
	wp_enqueue_script( 'wheelcolorpicker',  plugin_dir_url( __FILE__ ) . 'js/jquery.wheelcolorpicker.min.js', array('jquery'));
	wp_enqueue_script( 'jquery-ui-slider');
	wp_enqueue_style( 'wheelcolorpicker', plugin_dir_url( __FILE__ ) . 'css/wheelcolorpicker.dark.css');
	wp_enqueue_style( 'custom-ui-css', plugin_dir_url( __FILE__ ) . 'css/custom-ui.css');
	wp_enqueue_style( 'style', plugin_dir_url( __FILE__ ) . 'css/style.css');
}
add_action("admin_menu", "platon_pay_admin_add_menu");


function platon_pay_admin_add_menu_list_transaction() {
    add_submenu_page(
        plugin_dir_path(__FILE__) . 'inc/settings.php',
        'Список транзакций',
        'Список транзакций',
        'manage_options',
        plugin_dir_path(__FILE__) . 'inc/list-orders.php',
        ''
    );
}
add_action("admin_menu", "platon_pay_admin_add_menu_list_transaction" ,30);

/**********************************************************************************************************************/
/*Добавление ссылки "Настройки" возле плагина, на странице всех плагинов***********************************************/
/**********************************************************************************************************************/
add_filter( 'plugin_action_links', 'platon_pay_settings_link', 10, 2 );
function platon_pay_settings_link( $actions, $plugin_name ){
	if( false === strpos( $plugin_name, basename(__FILE__) ) )
		return $actions;
	$settings_link = '<a href="options-general.php?page='. basename(dirname(__FILE__)).'/inc/settings.php' .'">'.__("Settings").'</a>';
	array_unshift( $actions, $settings_link );
	return $actions;
}


/**********************************************************************************************************************/
/*Сохранение данных с формы********************************************************************************************/
/**********************************************************************************************************************/
add_action('wp_ajax_save_settings', 'platon_pay_save_settings');
function platon_pay_save_settings() {
	if(current_user_can('administrator')){
		global $wpdb;
		$table = $wpdb->prefix . 'platon_shortcodes';
		
		$json = array();
		
		$sanitize = sanitize_text_field(json_encode($_POST['data_settings']));
		$allOptions = json_decode($sanitize, true);
		$settingsObj = $allOptions['shortcodes'];
		
		if($allOptions){
				$platonTestMode = sanitize_text_field($allOptions['platon_test_mode']);
			$platonUrlField = esc_url($allOptions['platon_url_field']);
			$platonSecretKey = sanitize_text_field($allOptions['platon_secret_key']);
			$platonPasswordKey = sanitize_text_field($allOptions['platon_password_key']);
			$returnUrl = esc_url($allOptions['return_url']);
				$platonFirstName = sanitize_text_field($allOptions['platon_first_name']);
				$platonFirstNameRequire = sanitize_text_field($allOptions['platon_first_name_require']);
				$platonFirstNamePlaceholder = sanitize_text_field($allOptions['platon_first_name_placeholder']);
			$platonLastName = sanitize_text_field($allOptions['platon_last_name']);
			$platonLastNameRequire = sanitize_text_field($allOptions['platon_last_name_require']);
			$platonLastNamePlaceholder = sanitize_text_field($allOptions['platon_last_name_placeholder']);
				$platonEmail = sanitize_text_field($allOptions['platon_email']);
				$platonEmailRequire = sanitize_text_field($allOptions['platon_email_require']);
				$platonEmailPlaceholder = sanitize_text_field($allOptions['platon_email_placeholder']);
			$platonPhone = sanitize_text_field($allOptions['platon_phone']);
			$platonPhoneRequire = sanitize_text_field($allOptions['platon_phone_require']);
			$platonPhonePlaceholder = sanitize_text_field($allOptions['platon_phone_placeholder']);
				$platonСomment = sanitize_text_field($allOptions['platon_comment']);
				$platonСommentRequire = sanitize_text_field($allOptions['platon_comment_require']);
				$platonСommentPlaceholder = sanitize_text_field($allOptions['platon_comment_placeholder']);
				$payButtonColor = sanitize_text_field($allOptions['pay_button_color']);
				$nameButtonForm = sanitize_text_field($allOptions['name_button_form']);
			$colorOverlay = sanitize_text_field($allOptions['color_overlay']);
			$opacityOverlay = sanitize_text_field($allOptions['opacity_overlay']);
			$colorForm = sanitize_text_field($allOptions['color_form']);
			$colorButtonForm = sanitize_text_field($allOptions['color_button_form']);
			
				$pOpen = htmlspecialchars("<p>", ENT_QUOTES);
				$pClose = htmlspecialchars("</p>", ENT_QUOTES);
				$messageSuccessOrder = sanitize_text_field(str_replace(array($pOpen,$pClose), ' ', $allOptions['message_success_order']));
			
				update_option( 'platon_test_mode', $platonTestMode, true);
			update_option( 'platon_url_field', $platonUrlField, true);
			update_option( 'platon_secret_key', $platonSecretKey, true);
			update_option( 'platon_password_key', $platonPasswordKey, true);
			update_option( 'return_url', $returnUrl, true);
				update_option( 'platon_first_name', $platonFirstName, true);
				update_option( 'platon_first_name_require', $platonFirstNameRequire, true);
				update_option( 'platon_first_name_placeholder', $platonFirstNamePlaceholder, true);
			update_option( 'platon_last_name', $platonLastName, true);
			update_option( 'platon_last_name_require', $platonLastNameRequire, true);
			update_option( 'platon_last_name_placeholder', $platonLastNamePlaceholder, true);
				update_option( 'platon_email', $platonEmail, true);
				update_option( 'platon_email_require', $platonEmailRequire, true);
				update_option( 'platon_email_placeholder', $platonEmailPlaceholder, true);
			update_option( 'platon_phone', $platonPhone, true);
			update_option( 'platon_phone_require', $platonPhoneRequire, true);
			update_option( 'platon_phone_placeholder', $platonPhonePlaceholder, true);
				update_option( 'platon_comment', $platonСomment, true);
				update_option( 'platon_comment_require', $platonСommentRequire, true);
				update_option( 'platon_comment_placeholder', $platonСommentPlaceholder, true);
				update_option( 'pay_button_color', $payButtonColor, true);
			update_option( 'name_button_form', $nameButtonForm, true);
			update_option( 'color_overlay', $colorOverlay, true);
			update_option( 'opacity_overlay', $opacityOverlay, true);
			update_option( 'color_form', $colorForm, true);
			update_option( 'color_button_form', $colorButtonForm, true);
				update_option( 'message_success_order', $messageSuccessOrder, true);
			
			
			$wpdb->query("DELETE FROM $table");
			foreach ($settingsObj as $key => $settings){
				$wpdb->insert($table, array("id_shortcode" => sanitize_text_field($settings['line']), "name_field" => sanitize_text_field($settings['name_field']), "value_field" => sanitize_text_field($settings['value_field'])), array("%d", "%s", "%s"));
			}
			
			$json['options'] = $allOptions;
			$json['shortcodes'] = $settingsObj;
			$json['open_access'] = true;
		}
	}else{
		$json['open_access'] = false;
	}
	echo json_encode($json);
	wp_die();
}

/**********************************************************************************************************************/
/*Отправка информации на подключения оплаты****************************************************************************/
/**********************************************************************************************************************/
add_action('wp_ajax_send_info_hook_up', 'platon_pay_send_info_hook_up');
function platon_pay_send_info_hook_up() {
	$json = array();
	
	$nonceCode = check_ajax_referer( 'platon_pay_nonce_hook_up', 'security' );
	$sanitizeInfo = sanitize_text_field(json_encode($_POST['info']));
	$infoArray = json_decode($sanitizeInfo, true);
	if($nonceCode){
		$hookUpArray = array(
			'your-name'         => sanitize_text_field($infoArray['your-name']),
			'your-email'        => sanitize_text_field($infoArray['your-email']),
			'your-tel'          => sanitize_text_field($infoArray['your-tel']),
			'your-site'         => sanitize_text_field($infoArray['your-site']),
			'your-callback-url' => sanitize_text_field($infoArray['your-callback-url']),
			'your-topic'        => /*sanitize_text_field($infoArray['your-topic'])*/'860',
			'theme_name'         => 'Подключение интернет-эквайринга',
			'description'         => 'from_module',
			'action'         => 'myaction'
		);
		
		$headers = array();
		$headers[] = "Content/Type: application/json";
		$headers[] = "Accept: application/json";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://platon.ua/wp-content/themes/platon/ajax_platon_bitrix_lead.php');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $hookUpArray);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$res = json_decode(curl_exec($curl), true);
		if($res['status'] == 'success') {
			$json['message'] = 'Спасибо! Ваша заявка получена. В скором времени с Вами свяжется наш менеджер.';
			$json['status'] = 'success';
		}else{
			$errorResult = curl_error($curl);
			$json['message'] = 'Ошибка';
			$json['status'] = 'error';
		}
		curl_close($curl);
		
		$json['info_result_status'] = $res;
	}
	
	echo json_encode($json);
	wp_die();
}



require_once(plugin_dir_path( __FILE__ ).'/inc/form.php');

require_once(plugin_dir_path( __FILE__ ).'/inc/order-processing.php');