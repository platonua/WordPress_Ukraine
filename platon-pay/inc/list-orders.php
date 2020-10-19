<?php
	global $wpdb;
	$allInfo = array();
    
    $templateUrl = '/wp-admin/admin.php?page=platon-pay/inc/list-orders.php&pagination=';
    $urlToPagination = home_url().$templateUrl;
	
	$table_invoice = $wpdb->prefix . 'platon_invoice';
	$table_orders = $wpdb->prefix . 'platon_orders';
	$table_shortcodes = $wpdb->prefix . 'platon_shortcodes';
	
	$allOrders = $wpdb->get_results("SELECT * FROM $table_invoice ORDER BY id DESC");
	
	if($allOrders){
        $limit = 20;
        $allCountOrders = count($allOrders);
        $numberOfPages = count(array_chunk($allOrders, $limit));
        $currentPage = isset($_GET['pagination']) ? (int)$_GET['pagination'] : 1;
        $startOrders = ($currentPage - 1) * $limit;
            $minVisiblePage = $currentPage - 2;
            $maxVisiblePage = $currentPage + 2;
        $nextPage = $currentPage + 1;
        $prevPage = $currentPage - 1;
            $textStartCount = $startOrders === 0 ? 1 : $startOrders+1;
            $textCountOrder = ($currentPage*$limit > $allCountOrders) ? $allCountOrders : $currentPage*$limit;

        $limitedOrder = array_slice($allOrders, $startOrders, $limit);
	
		$idInfo = 0;
		foreach ($limitedOrder as $invoice){
			$invoiceId = $invoice->id;
			
			/*Получение заказа*/
			$allInfo[$idInfo] = array(
				'id_invoice'    => $invoiceId,
				'status'        => $invoice->status,
				'order_platon'  => $invoice->order_platon,
				'create_date'   => $invoice->create_date
			);
			
			/*Получение полей заказа*/
			$fields_orders = $wpdb->get_results("SELECT * FROM $table_orders WHERE invoice = $invoiceId");
			if($fields_orders){
				foreach ($fields_orders as $orders){
					$allInfo[$idInfo][$orders->name_field] = $orders->value_field;
				}
			}
			
			/*Получение информации о шорткоде*/
			$shortcodeId = $allInfo[$idInfo]['shortcode_id'];
			$shortcodes = $wpdb->get_results("SELECT * FROM $table_shortcodes WHERE id_shortcode = $shortcodeId");
			if($shortcodes){
				$optional = false;
				foreach ($shortcodes as $shortcode){
					if ($shortcode->name_field == 'optional') {
						$optional = $shortcode->value_field == "1" ? true : false;
					} 
					if ($optional && $shortcode->name_field == 'price') {
						$optional = false;
						continue;
					} else {
						$allInfo[$idInfo][$shortcode->name_field] = $shortcode->value_field;
					}
				}
			}
			
			$idInfo++;
		}
	}
	$id_invoice = $allInfo['id_invoice'] ? $allInfo['id_invoice'] : '';
	
?>

<div class="wrapper-plugin list-orders">
	<div class="header-plugin-page">
		<a href="https://platon.ua/" target="_blank" class="logo-plugin">
			<svg xmlns="http://www.w3.org/2000/svg" width="130" height="33" viewBox="0 0 130 33" fill="none">
				<path d="M51.24 7.05a83.063 83.063 0 0 1 2.595-.176c.882-.05 1.765-.076 2.647-.076.936 0 1.9.076 2.808.253.936.154 1.792.51 2.594.99a5.366 5.366 0 0 1 1.872 2.03c.482.862.722 1.978.722 3.348 0 1.37-.24 2.486-.722 3.323-.428.813-1.07 1.497-1.872 2.03a7.08 7.08 0 0 1-2.54 1.015c-.91.177-1.846.253-2.782.253h-.668c-.187 0-.4-.025-.588-.05v6.087c-.348.05-.696.076-1.043.102-.32.025-.67.025-.963.025-.32 0-.642 0-.963-.025-.348-.026-.722-.05-1.096-.102V7.05zm4.04 9.767c.213.025.427.05.614.05h.695c.454 0 .908-.05 1.363-.177.428-.1.83-.304 1.177-.558a2.92 2.92 0 0 0 .83-1.04c.213-.507.32-1.04.293-1.598a3.918 3.918 0 0 0-.294-1.65 3.012 3.012 0 0 0-.83-1.09 2.99 2.99 0 0 0-1.177-.583 5.038 5.038 0 0 0-1.364-.176h-.562c-.16 0-.428.025-.776.076v6.747h.027zM67.1 6.823a12.74 12.74 0 0 1 3.93 0v13.85c0 .43.027.887.108 1.318.053.28.133.534.294.788.133.177.32.304.535.38.267.076.56.102.83.102.16 0 .32 0 .48-.026.16-.025.348-.05.508-.076a8.01 8.01 0 0 1 .294 2.105v.38c0 .128-.027.255-.054.38-.348.103-.695.154-1.07.18-.428.024-.802.05-1.15.05-1.417 0-2.567-.38-3.423-1.116-.856-.736-1.284-1.953-1.284-3.653V6.824zm16.313 10.324a1.887 1.887 0 0 0-.214-1.015 1.39 1.39 0 0 0-.616-.61 3.03 3.03 0 0 0-.963-.303 10.45 10.45 0 0 0-1.256-.077 10.65 10.65 0 0 0-3.21.558 7.17 7.17 0 0 1-.587-1.293c-.134-.482-.214-.964-.188-1.446a16.137 16.137 0 0 1 2.433-.582 13.73 13.73 0 0 1 2.273-.178c1.926 0 3.423.432 4.52 1.32 1.096.887 1.63 2.282 1.63 4.21v7.914c-.774.228-1.577.406-2.352.558-1.043.178-2.086.28-3.156.254-.882 0-1.738-.076-2.594-.23a5.805 5.805 0 0 1-2.033-.785 3.788 3.788 0 0 1-1.31-1.42 4.512 4.512 0 0 1-.454-2.13c-.026-.736.16-1.47.562-2.13a4.362 4.362 0 0 1 1.47-1.37 6.333 6.333 0 0 1 2.007-.736c.722-.152 1.47-.228 2.22-.228.534 0 1.123.025 1.764.076l.053-.355zm0 2.714c-.214-.025-.455-.075-.722-.1-.24-.026-.454-.05-.695-.05a4.556 4.556 0 0 0-2.166.455 1.575 1.575 0 0 0-.803 1.497c-.027.38.08.735.294 1.065.188.228.456.43.75.533.294.126.615.177.91.203.32.025.614.05.855.05.267 0 .562-.025.83-.076l.72-.127.028-3.45zm7.73-4.54h-1.82l-.133-.532 5.188-6.316h.642v3.982h3.37c.053.228.08.482.106.71.027.203.027.432.027.66 0 .228 0 .456-.027.71a7.422 7.422 0 0 1-.107.76h-3.344v5.353c0 .43.027.888.134 1.32.08.278.214.557.4.785.162.203.402.33.643.406.294.076.615.102.91.102.267 0 .56-.026.828-.076l.697-.127c.133.33.214.685.267 1.04.054.33.08.634.08.964v.482c0 .127-.026.254-.053.38-.855.204-1.764.28-2.647.28-1.685 0-2.968-.38-3.878-1.116-.882-.736-1.337-1.953-1.337-3.653l.054-6.113zM107.482 26.43a7.796 7.796 0 0 1-3.05-.532c-.828-.33-1.55-.863-2.138-1.497a6.198 6.198 0 0 1-1.284-2.256 9.07 9.07 0 0 1-.428-2.79c0-.964.134-1.903.428-2.816a6.52 6.52 0 0 1 1.284-2.283 6.002 6.002 0 0 1 2.14-1.522 7.82 7.82 0 0 1 3.048-.558 7.82 7.82 0 0 1 3.05.558c.828.355 1.576.863 2.165 1.522a6.305 6.305 0 0 1 1.283 2.283 9.23 9.23 0 0 1 .428 2.816 9.07 9.07 0 0 1-.428 2.79 6.2 6.2 0 0 1-1.283 2.257 5.738 5.738 0 0 1-2.166 1.498 8.847 8.847 0 0 1-3.048.533zm0-2.866c1.043 0 1.792-.355 2.246-1.09.455-.736.696-1.776.67-3.146 0-1.37-.242-2.41-.67-3.145-.428-.736-1.203-1.09-2.246-1.09-1.016 0-1.765.354-2.22 1.09-.455.736-.668 1.776-.668 3.145 0 1.37.24 2.41.668 3.146.455.735 1.204 1.09 2.22 1.09zm9.76-11.084c.27-.05.51-.102.777-.127.293-.025.56-.05.855-.05.267 0 .56 0 .83.05.24.025.48.076.747.127.08.127.107.253.16.38.054.178.08.355.135.533.053.177.08.355.107.533.026.177.053.33.08.456.187-.28.428-.558.668-.786.268-.28.562-.508.91-.71.347-.23.748-.38 1.15-.508a5.308 5.308 0 0 1 1.417-.203c1.63 0 2.86.432 3.69 1.27.83.836 1.23 2.18 1.23 3.98v8.7c-1.31.204-2.648.204-3.958 0V18.39c0-.94-.16-1.674-.455-2.182-.294-.507-.83-.76-1.605-.76-.32 0-.668.05-.962.126-.348.102-.642.28-.91.508-.294.304-.534.66-.64 1.065a5.145 5.145 0 0 0-.242 1.8v7.154c-1.31.204-2.648.204-3.958 0l-.027-13.62zM5.242 33c.24 0 .454-.076.668-.178l9.628-4.185c1.043-.456 1.925-1.75 1.925-2.84v-4.998c0-.71-6.632-2.715-6.632-2.715l-4.706 2.03c-1.043.456-1.925 1.75-1.925 2.84v8.853c-.028.33.08.634.293.888a1.1 1.1 0 0 0 .75.304z" fill="#fff"/><path d="M17.463 20.19v3.273L2.567 16.97C1.177 16.36 0 14.636 0 13.19v-.583l17.463 7.584zM17.463 17.198V9.46c0-1.445-1.176-3.17-2.567-3.778L2.3.202A2.16 2.16 0 0 0 1.39 0C.536 0 0 .61 0 1.623v7.99l17.463 7.585zM5.108 5.53c0 .583-.48.862-1.043.634L2.22 5.377a1.868 1.868 0 0 1-1.043-1.52V2.688c0-.585.48-.864 1.043-.635l1.845.786c.615.305.99.89 1.043 1.523V5.53z" fill="#fff"/><path d="M12.222 14.915c-.24 0-.455-.076-.67-.178l-9.626-4.185C.883 10.095 0 8.802 0 7.712V2.713C0 2.004 6.632 0 6.632 0l4.707 2.03c1.042.456 1.925 1.75 1.925 2.84v8.852c.026.33-.08.635-.294.888a1.1 1.1 0 0 1-.748.305z" transform="translate(20.272 18.085)" fill="url(#a)"/><path d="M0 7.584v3.272l14.896-6.493c1.39-.61 2.567-2.334 2.567-3.78V0L0 7.584z" transform="translate(20.272 12.607)" fill="url(#b)"/>
				<path d="M17.463 9.613v-7.99c0-.99-.534-1.623-1.39-1.623-.32 0-.642.076-.91.203L2.568 5.683C1.177 6.29 0 8.014 0 9.46V17.2l17.463-7.585zm-5.108-5.25c.027-.634.428-1.218 1.043-1.522l1.846-.785c.56-.254 1.043.025 1.043.634v1.166c-.027.634-.428 1.217-1.043 1.52l-1.845.788c-.563.253-1.045-.026-1.045-.634V4.363z" transform="translate(20.272)" fill="url(#c)"/>
				<defs>
					<linearGradient id="a" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="scale(13.2729 14.3691) rotate(180 .5 .51)"><stop stop-color="#fff"/></linearGradient>
					<linearGradient id="b" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="scale(17.4688 10.4592) rotate(180 .5 .51)"><stop stop-color="#fff"/></linearGradient>
					<linearGradient id="c" x2="1" gradientUnits="userSpaceOnUse" gradientTransform="matrix(-17.4688 0 0 -16.5685 17.463 16.883)"><stop stop-color="#fff"/></linearGradient>
				</defs>
			</svg>
		</a>
		<?php $linkToFile = '/wp-admin/admin.php?page=platon-pay/inc/settings.php'; ?>
		<a class="default-icon-link settings-link" href="<?php echo home_url() . $linkToFile; ?>">
            <span class="dashicons-before dashicons-admin-generic"></span>
            Настройки плагина
        </a>
	</div>
	
    
	<?php if($allInfo){ ?>
		<div class="checkboxes">
			<h3>Отобразить данные</h3>
			<label for="date">
				Дата
				<input type="checkbox" id="date" name="date">
			</label>
			<label for="id-invoice">
				ID
				<input type="checkbox" id="id-invoice" name="id-invoice">
			</label>
			<label for="order-platon">
				ID Platon
				<input type="checkbox" id="order-platon" name="order-platon">
			</label>
			<label for="fio">
				ФИО
				<input type="checkbox" id="fio" name="fio">
			</label>
			<label for="email">
				Email
				<input type="checkbox" id="email" name="email">
			</label>
			<label for="phone">
				Телефон
				<input type="checkbox" id="phone" name="phone">
			</label>
			<label for="price">
				Цена
				<input type="checkbox" id="price" name="price">
			</label>
			<label for="payment-name">
				Имя платежа
				<input type="checkbox" id="payment-name" name="payment-name">
			</label>
			<label for="comment">
				Комментарий
				<input type="checkbox" id="comment" name="comment">
			</label>
		</div>
		<table class="table-form-field table-list">
			<thead>
				<tr>
					<td class="date">Дата</td>
					<td class="id-invoice">ID</td>
					<td class="order-platon">ID Platon</td>
					<td class="fio">ФИО</td>
					<td class="email">E-mail</td>
					<td class="phone">Телефон</td>
					<td class="price">Цена</td>
					<td class="payment-name">Имя платежа</td>
					<td class="comment">Комментарий</td>
					<td class="status">Статус</td>
					<td class="action">Действие</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($allInfo as $info){
					$date1 = substr( $info['create_date'], 0,-3); $date = str_replace(" ", ",", $date1); ?>
					<?php $iconBefore = ($info['status'] === 'Оплачен') ? 'paid-status status-all' : 'not-paid-status status-all'; ?>
					<tr>
						<td data-tooltip="<?php echo $date ? $date : ' '; ?>" class="date"><span><?php echo $date ? $date : ' '; ?></span></td>
						<td data-tooltip="<?php echo $info['id_invoice'] ? $info['id_invoice'] : ' '; ?>" class="id-invoice"><span><?php echo $info['id_invoice'] ? $info['id_invoice'] : ' '; ?></span></td>
						<td data-tooltip="<?php echo $info['order_platon'] ? $info['order_platon'] : '&nbsp;'; ?>" class="order-platon"><span><?php echo $info['order_platon'] ? $info['order_platon'] : '&nbsp;'; ?></span></td>
						<td data-tooltip="<?php echo (($info['first_name'] ? $info['first_name'] : '') . ' ' . ($info['last_name'] ? $info['last_name'] : '&nbsp;')); ?>" class="fio"><span><?php echo (($info['first_name'] ? $info['first_name'] : '') . ' ' . ($info['last_name'] ? $info['last_name'] : '&nbsp;')); ?></span></td>
						<td data-tooltip="<?php echo $info['email'] ? $info['email'] : '&nbsp;'; ?>" class="email"><span><?php echo $info['email'] ? $info['email'] : '&nbsp;'; ?></span></td>
						<td data-tooltip="<?php echo $info['phone'] ? $info['phone'] : '&nbsp;'; ?>" class="phone"><span><?php echo $info['phone'] ? $info['phone'] : '&nbsp;'; ?></span></td>
						<td data-tooltip="<?php echo $info['price'] ? $info['price'] : '&nbsp;'; ?>" class="price">
							<span><?php echo $info['price'] ? $info['price'] : '&nbsp;'; ?></span>
						</td>
						<td data-tooltip="<?php echo $info['payment_name'] ? $info['payment_name'] : '&nbsp;'; ?>" class="payment-name"><span><?php echo $info['payment_name'] ? $info['payment_name'] : '&nbsp;'; ?></span></td>
						<td data-tooltip="<?php echo $info['comment'] ? $info['comment'] : '&nbsp;'; ?>" class="comment"><span><?php echo $info['comment'] ? $info['comment'] : '&nbsp;'; ?></span></td>
						<td data-tooltip="<?php echo $info['status'] ? $info['status'] : '&nbsp;'; ?>" class="status <?php echo $iconBefore; ?>"><span><?php echo $info['status'] ? $info['status'] : '&nbsp;'; ?></span></td>
						<td class="action"><span class="btn-default red remove-order" data-remove="<?php echo $info['id_invoice']; ?>">Удалить</span></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		
  
		<?php if($numberOfPages > 1){ ?>
            <div class="block-pagination">
                <span class="text-count-pages"><?php echo '('.$textStartCount.' - '.$textCountOrder.') записей из '.$allCountOrders; ?></span>
                <ul class="link-pagination">
                    <?php
                        if($currentPage !== 1){
                            echo '<li class="first-page"><a href="'.$urlToPagination.$prevPage.'">«</a></li>';
                        }
                        if($minVisiblePage > 1){
                            echo '<li><a href="'.$urlToPagination.'1">1</a></li>';
                            echo '<li class="dots-li-page">...</li>';
                        }
                        for ($i = 1; $i <= $numberOfPages; $i++) {
                            if($i === $currentPage){
                                echo '<li class="active-page">'.$i.'</li>';
                            }
                            else if(($i >= $minVisiblePage) AND ($i <= $maxVisiblePage)){
                                echo '<li><a href="'.$urlToPagination.$i.'">'.$i.'</a></li>';
                            }
                        }
                        if($maxVisiblePage < $numberOfPages){
                            echo '<li class="dots-li-page">...</li>';
                            echo '<li class="last-number-page"><a href="'.$urlToPagination.$numberOfPages.'">'.$numberOfPages.'</a></li>';
                        }
                        if($currentPage !== $numberOfPages){
                            echo '<li class="last-page"><a href="'.$urlToPagination.$nextPage.'">»</a></li>';
                        }
                    ?>
                </ul>
            </div>
		<?php } ?>
	<?php }else{ ?>
		<h1 class="not-list-order">Ваш список транзакций пуст!</h1>
	<?php } ?>
 
</div>