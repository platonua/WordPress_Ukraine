<?php

function platon_pay_sending_data(){
	$json = array();
	$nonceCode = check_ajax_referer( 'platon_pay_nonce_action', 'security' );
	if(isset($_POST['data_form']) && $nonceCode){
		global $wpdb;
		$data_form = $_POST['data_form'];
		
		$resultId = 0; /*Присваивается id заказа для дальнейшего изменения статуса*/
		
		/*Получение информации по шорткоду*****************************************************************************/
		$table = $wpdb->prefix . 'platon_shortcodes';
		$table_invoice = $wpdb->prefix . 'platon_invoice';
		$table_order = $wpdb->prefix . 'platon_orders';
		$thisIdShortcode = $data_form['shortcode_id'];
		$infoShortcode = $wpdb->get_results("SELECT * FROM $table WHERE id_shortcode = $thisIdShortcode");
		
		/*Создание заказа и запись в базу******************************************************************************/
		$create_date = date("Y.m.d G:i:s");
		if($data_form){
			$dataConversion = array();
			$itemKey = 0;
			foreach ($data_form as $key => $form_info){
				$dataConversion[$itemKey]['name_field'] = $key;
				$dataConversion[$itemKey]['value_field'] = $form_info;
				$itemKey++;
			}
			$wpdb->insert($table_invoice, array("status" => "В ожидании оплаты", "create_date" => $create_date), array("%s", "%s"));
			$resultId = $wpdb->insert_id;
			foreach ($dataConversion as $key => $dataInf){
				$wpdb->insert($table_order, array("invoice" => $resultId, "name_field" => $dataInf['name_field'], "value_field" => $dataInf['value_field']), array("%d", "%s", "%s"));
			}
		}
		
		/*Настройки для подключения к Platon***************************************************************************/
		$infoUrl = ( get_option('platon_url_field') ) ? get_option('platon_url_field') : '';
		if(get_option('platon_test_mode') !== '0'){
			$infoSecret = 'F5QQ6NQS64';
			$infoPassword = 'TaHycyY5z7PeZsX4fpuQcXusX5JHjmLy';
		}else{
			$infoSecret = ( get_option('platon_secret_key') ) ? get_option('platon_secret_key') : '';
			$infoPassword = ( get_option('platon_password_key') ) ? get_option('platon_password_key') : '';
		}
		
		/*Сбор данных для совершения оплаты****************************************************************************/
		if($infoShortcode){
			$resultInfo = array();
			foreach ($infoShortcode as $result){
				$resultInfo[$result->name_field] = $result->value_field;
				$resultInfo['id_shortcode'] = $result->id_shortcode;
			}
			if (!$resultInfo['optional']) {
				$newPriceFormat = number_format($resultInfo["price"], 2, '.', '');
			} else {
				$newPriceFormat = number_format($data_form["price"], 2, '.', '');
			}

			
			$callbackUrl = '/?platon-result=Result_Payment';
			$result_url = home_url() . $callbackUrl;
			$returnUrl = ( get_option('return_url') ) ? get_option('return_url').$callbackUrl : $result_url;
			
			$data = base64_encode(serialize(array('amount' => $newPriceFormat, 'recurring'=>'Y','description' => $resultInfo["title_form"], 'currency' => 'UAH')));
			
			$sign = md5(strtoupper(
				strrev($infoSecret).
				strrev('CC').
				strrev($data).
				strrev($returnUrl).
				strrev($infoPassword)));
			
			$args = array(
				'key'           => $infoSecret,
				'payment'       => 'CC',
				'order'         => $resultId,
				'data'          => $data,
				'ext1'          => '',
				'ext2'          => '',
				'ext3'          => '',
				'ext4'          => '',
				'url'           => $returnUrl,
				'sign'          => $sign
			);
			
			$args_input = '';
			foreach ($args as $key => $value) {
				$args_input .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
			}
			
			$json['args'] = $args;
			$json['url_to_send'] = $infoUrl;
			$json['args_inputs'] = $args_input;
		}
		
		$json['data'] = $data_form;
		$json['id_order'] = $resultId;
	}
	
	$json['nonce'] = $nonceCode;
	echo json_encode($json);
	wp_die();
}
add_action('wp_ajax_sending_data', 'platon_pay_sending_data');
add_action('wp_ajax_nopriv_sending_data', 'platon_pay_sending_data');



/*Изменение статуса заказа и запись внутренего id заказа от Platon*****************************************************/
if(!isset($_GET["order"]) && isset($_POST['sign'])){
	$order_id = sanitize_text_field($_POST['id']);
	$sign = sanitize_text_field($_POST['sign']);
	$status = sanitize_text_field($_POST['status']);
	
	$invoice_table = $wpdb->prefix . 'platon_invoice';
	$internal_order_id = sanitize_text_field($_POST['order']);

	$card = sanitize_text_field(isset($_REQUEST['card']) ? $_REQUEST['card'] : '');

	$password = ( get_option('platon_password_key') ) ? get_option('platon_password_key') : '';
	if ($card != ""){
		$email = sanitize_email(isset($_REQUEST['email']) ? $_REQUEST['email'] : '');

		$self_md5 = md5(strtoupper(strrev($email).$password.$internal_order_id.strrev(substr($card,0,6).substr($card,-4))));

		if ($self_md5 == $sign && $status === 'SALE') {
			$wpdb->update( $invoice_table,
				array( 'status' => 'Оплачен', 'order_platon' =>  $order_id),
				array( 'id' => $internal_order_id ));

		}
	} else {

		$self_md5_P24 =	md5(strtoupper($password . $internal_order_id ));

		if ($self_md5_P24 == $sign && $status === 'SALE') {
			$wpdb->update( $invoice_table,
				array( 'status' => 'Оплачен'),
				array( 'id' => $internal_order_id ));
		}
	}
}

/*Изменение статуса заказа в тестовом режиме***************************************************************************/
if(isset($_GET['platon-result']) && isset($_GET['order']) && get_option('platon_test_mode')){
	$invoice_table = $wpdb->prefix . 'platon_invoice';
	$internal_order_id = sanitize_text_field($_GET['order']);
	$wpdb->update( $invoice_table, array( 'status' => 'Тестовый платеж'), array( 'id' => $internal_order_id ));
}


/*Показ оповещения после успешного совершения заказа*******************************************************************/
if(isset($_GET['platon-result'])) {
	$colorOverlay = ( get_option('color_overlay') ) ? get_option('color_overlay') : '052c3b';
	$colorForm = ( get_option('color_form') ) ? get_option('color_form') : 'ffffff';
	$opacityOverlay = ( get_option('opacity_overlay') ) ? get_option('opacity_overlay') : '1';
	$messageOrder = (get_option('message_success_order')) ? get_option('message_success_order') : '';

	$pOpen = htmlspecialchars("<p>", ENT_QUOTES);
	$pClose = htmlspecialchars("</p>", ENT_QUOTES);
	$res = str_replace(array($pOpen,$pClose), ' ', $messageOrder);
	
	$html = '<div class="info-success-order-overlay">';
	$html .=     '<div class="modal-frontend-form" id="success-order-modal" style="background-color: #'.$colorForm.';display: block;opacity: 1;text-align: center;">';
	$html .=        '<span class="modal-close success-custom-bl">x</span>';
	$html .=        '<p>'.html_entity_decode($messageOrder).'</p>';
	$html .=    '</div>';
	$html .=    '<div class="overlay success-custom-bl" style="display:block;background-color: #'.$colorOverlay.'; opacity: '.$opacityOverlay.'"></div>';
	$html .= '</div>';
	echo $html;
}


/*Удаление заказа******************************************************************************************************/
function platon_pay_remove_order(){
	$id = sanitize_text_field($_POST['order_id']);
	if($id && current_user_can('administrator')){
		global $wpdb;
		$json = array();

		$invoice = $wpdb->prefix . 'platon_invoice';
		$orders = $wpdb->prefix . 'platon_orders';
		$remove_invoice = $wpdb->delete( $invoice, array( 'id' => $id ) );
		$remove_order = $wpdb->delete( $orders, array( 'invoice' => $id ) );

		if($remove_invoice && $remove_order){
			$json['message'] = 'success';
		}else {
			$json['message'] = 'error';
		}
		$json['remove_invoice'] = $remove_invoice;
		$json['remove_order'] = $remove_order;
		echo json_encode($json);
		wp_die();
		
	}
}
add_action('wp_ajax_remove_order', 'platon_pay_remove_order');


/*Функция для логирования полученых данных*******************************************************************************/
/*
function platon_pay_log_info($info = array()){
	$fp = fopen(plugin_dir_path( __FILE__ ).'platon-logs.txt','w+');
	fwrite($fp, $info);
	fclose($fp);
}
*/