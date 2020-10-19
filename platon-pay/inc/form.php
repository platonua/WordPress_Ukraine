<?php

wp_enqueue_style( 'frontend', plugins_url( 'platon-pay/css/frontend.css'));
wp_enqueue_script( 'frontend', plugins_url( 'platon-pay/js/frontend.js'), array('jquery'));

function platon_pay_add_shortcode_form($data){
	$id = $data['id'];
	global $wpdb;
	$table = $wpdb->prefix . 'platon_shortcodes';
	$infoToShortcode = $wpdb->get_results("SELECT * FROM $table WHERE id_shortcode = $id ORDER BY id_shortcode ASC");
	$info = array();
	foreach ($infoToShortcode as $infoShortcode){
		$info[$infoShortcode->name_field] = $infoShortcode->value_field;
		$info['id_shortcode'] = $infoShortcode->id_shortcode;
	}
	
	$firstNameRequire = ( get_option('platon_first_name_require') ) ? 'required' : '';
	$firstNamePlaceholder = ( get_option('platon_first_name_placeholder') ) ? get_option('platon_first_name_placeholder') : 'Имя';
	$lastNameRequire = ( get_option('platon_last_name_require') ) ? 'required' : '';
	$lastNamePlaceholder = ( get_option('platon_last_name_placeholder') ) ? get_option('platon_last_name_placeholder') : 'Фамилия';
	$emailRequire = ( get_option('platon_email_require') ) ? 'required' : '';
	$emailPlaceholder = ( get_option('platon_email_placeholder') ) ? get_option('platon_email_placeholder') : 'E-mail';
	$phoneRequire = ( get_option('platon_phone_require') ) ? 'required' : '';
	$phonePlaceholder = ( get_option('platon_phone_placeholder') ) ? get_option('platon_phone_placeholder') : 'Телефон';
	$commentRequire = ( get_option('platon_comment_require') ) ? 'required' : '';
	$commentPlaceholder = ( get_option('platon_comment_placeholder') ) ? get_option('platon_comment_placeholder') : 'Комментарий';
    $payButtonColor = ( get_option('pay_button_color') ) ? get_option('pay_button_color') : 'white';
	$nameButtonForm = ( get_option('name_button_form') ) ? get_option('name_button_form') : 'Отправить';
	
	$colorOverlay = ( get_option('color_overlay') ) ? get_option('color_overlay') : '052c3b';
	$opacityOverlay = ( get_option('opacity_overlay') ) ? get_option('opacity_overlay') : '1';
	$colorForm = ( get_option('color_form') ) ? get_option('color_form') : 'ffffff';
	$colorButtonForm = ( get_option('color_button_form') ) ? get_option('color_button_form') : 'ee8527';
	
	$randomCode = rand();
	
	$adminAjax = '/wp-admin/admin-ajax.php';
	$linkToHandler = home_url() . $adminAjax;
	
	$html = '<div style="border: 1px solid #'.$colorButtonForm.' !important;" class="open-form '.$payButtonColor.'" data-open="'.$randomCode.'"><img src="'.plugins_url('platon-pay/images/icon.svg').'"><span>'.$info["name_button"].'</span></div>';
	$html .= '<div class="modal-frontend-form" id="'.$randomCode.'" style="background-color: #'.$colorForm.'">';
		$html .= '<span class="modal-close">x</span>';
		$html .= '<span class="title-form-order">'.$info['title_form'].'</span>';
		$html .= '<form class="frontend-form" method="POST">';
			if(get_option('platon_first_name')) {
				$html .= '<input name="first_name" type="text" placeholder="'.$firstNamePlaceholder.'" '. $firstNameRequire .'>';
			}
			if(get_option('platon_last_name')) {
				$html .= '<input name="last_name" type="text" placeholder="'.$lastNamePlaceholder.'" '. $lastNameRequire .'>';
			}
			if(get_option('platon_email')) {
				$html .= '<input name="email" type="email" placeholder="'.$emailPlaceholder.'" '. $emailRequire .'>';
			}
			if(get_option('platon_phone')) {
				$html .= '<input name="phone" class="phone-field-form" type="tel" placeholder="'.$phonePlaceholder.'" '. $phoneRequire .'>';
			}
			if(get_option('platon_comment')) {
				$html .= '<input name="comment" class="comment-field-form" type="text" placeholder="'.$commentPlaceholder.'" '. $commentRequire .'>';
			}
			if ($info['optional']) {
				$html .= '<input name="price" type="number" class="price-modal-input" placeholder="Укажите сумму платежа" required>';
			}
			$html .=    '<div class="do-not-transmit-field">';
			$html .=        '<input type="hidden" name="shortcode_id" value="'.$info['id_shortcode'].'">';
			$html .=        '<input type="hidden" name="link_to_handler" value="'.$linkToHandler.'">';
			$html .=        wp_nonce_field( 'platon_pay_nonce_action','platon_pay_nonce_field',true, false );
			$html .=    '</div>';
			$html .=    '<button type="submit" data-active="0" style="border: 1px solid #'.$colorButtonForm.' !important;" class="button-order-processing '.$payButtonColor.'"><img src="'.plugins_url('platon-pay/images/icon.svg').'">'.$nameButtonForm.'</button>';

			$html .=    '<div class="pay-logos">';
			$html .=		'<img src="'.plugins_url('platon-pay/images/visa.svg').'">';
			$html .=		'<img src="'.plugins_url('platon-pay/images/mcardmodal.svg').'">';
			$html .=		'<img src="'.plugins_url('platon-pay/images/prostir.svg').'">';
			$html .=    '</div>';

		$html .= '</form>';
	$html .= '</div>';
	$html .= '<div class="overlay" style="background-color: #'.$colorOverlay.'; opacity: '.$opacityOverlay.'"></div>';
	
	return $html;
}
add_shortcode('platon_pay', 'platon_pay_add_shortcode_form');