<?php
    global $wpdb;
    $table = $wpdb->prefix . 'platon_shortcodes';
    $resultShortcodesAll = $wpdb->get_results("SELECT * FROM $table ORDER BY id_shortcode ASC");
    if($resultShortcodesAll){
        $resultInfo = array();
        foreach ($resultShortcodesAll as $resultShortcodes){
            $resultInfo[$resultShortcodes->id_shortcode][$resultShortcodes->name_field] = $resultShortcodes->value_field;
        }
    }
    
    $callbackUrl = '/?platon-result=Result_Payment';

    $testMode = ( get_option('platon_test_mode') ) ? get_option('platon_test_mode') : '';

//    $path = plugins_url( 'platon-pay/index.php');
//    $data = get_plugin_data($path);
    $platonUrl = ( get_option('platon_url_field') ) ? get_option('platon_url_field') : 'https://secure.platononline.com/payment/auth';
    $platonSecret = ( get_option('platon_secret_key') ) ? get_option('platon_secret_key') : '';
    $platonPassword = ( get_option('platon_password_key') ) ? get_option('platon_password_key') : '';
    $returnUrl = ( get_option('return_url') ) ? get_option('return_url') : $protocol.$serverName.$newDir;
  
    $firstName = ( get_option('platon_first_name') ) ? get_option('platon_first_name') : '';
    $firstNameRequire = ( get_option('platon_first_name_require') ) ? get_option('platon_first_name_require') : '';
    $firstNamePlaceholder = ( get_option('platon_first_name_placeholder') ) ? get_option('platon_first_name_placeholder') : 'Имя';
    $lastName = ( get_option('platon_last_name') ) ? get_option('platon_last_name') : '';
    $lastNameRequire = ( get_option('platon_last_name_require') ) ? get_option('platon_last_name_require') : '';
    $lastNamePlaceholder = ( get_option('platon_last_name_placeholder') ) ? get_option('platon_last_name_placeholder') : 'Фамилия';
    $email = ( get_option('platon_email') ) ? get_option('platon_email') : '';
    $emailRequire = ( get_option('platon_email_require') ) ? get_option('platon_email_require') : '';
    $emailPlaceholder = ( get_option('platon_email_placeholder') ) ? get_option('platon_email_placeholder') : 'E-mail';
    $phone = ( get_option('platon_phone') ) ? get_option('platon_phone') : '';
    $phoneRequire = ( get_option('platon_phone_require') ) ? get_option('platon_phone_require') : '';
    $phonePlaceholder = ( get_option('platon_phone_placeholder') ) ? get_option('platon_phone_placeholder') : 'Телефон';

    $comment = ( get_option('platon_comment') ) ? get_option('platon_comment') : '';
    $commentRequire = ( get_option('platon_comment_require') ) ? get_option('platon_comment_require') : '';
    $commentPlaceholder = ( get_option('platon_comment_placeholder') ) ? get_option('platon_comment_placeholder') : 'Введите комментарий';
    $payButtonColor = ( get_option('pay_button_color') ) ? get_option('pay_button_color') : 'white';


    $nameButtonForm = ( get_option('name_button_form') ) ? get_option('name_button_form') : 'Оплатить картой';
    $colorOverlay = ( get_option('color_overlay') ) ? get_option('color_overlay') : 'e5e5e5';
    $opacityOverlay = ( get_option('opacity_overlay') ) ? get_option('opacity_overlay') : '1';
    $colorForm = ( get_option('color_form') ) ? get_option('color_form') : 'ffffff';
    $colorButtonForm = ( get_option('color_button_form') ) ? get_option('color_button_form') : 'fe8c02';
    $defaultTextSuccessOrder = ( get_option('message_success_order') ) ? get_option('message_success_order') : 'Ваш заказ был успешно оплачен!';
?>

<div class="wrapper-plugin">
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
        <span class="btn-default" id="save-settings">
            <img class="preloader" src="<?php echo plugins_url('platon-pay/images/preloader.gif'); ?>" alt="Preloader">
            Сохранить
        </span>
    </div>
    
    
    <form id="platon-form">
        
        <ul class="tab-block">
            <li data-tab="system-settings" class="active-tab tab-default">Системные настройки Platon</li>
            <li data-tab="form-settings" class="tab-default">Настройки формы</li>
            <li data-tab="content-shortcode" class="tab-default">Формирование шорткода</li>
            <li data-tab="design-form" class="tab-default">Оповещения</li>
        </ul>
        
        <div class="all-content-tab">
            <div class="content-tab simple-fields active-content" id="system-settings">
                <div class="full-width-block">
                    <div class="left-test-mode-info">
                        <div class="field-block full-width-block">
                            <input type="checkbox" class="checkbox"  id="platon-test-mode" name="platon_test_mode" <?php echo ($testMode) ? 'checked' : ''; ?>>
                            <label for="platon-test-mode">Рабочий режим</label><!--Тестовый режим-->
                        </div>
                        <span class="button-default-platon" id="platon-psp-hook-up">Подключить PSP Platon</span>
                    </div>
                    <div class="right-test-mode-info">
                        <div class="field-block full-width-block" id="test-mode-info">
                            <p>Вы можете протестировать процесс работы модуля без проведения оплат.</p>
                            <p>Для включения реальных платежей – вам необходимы выключить «Режим тестирования» - и связаться с нашими специалистами для получения необходимых данных.</p>
                            <div class="test-info-card">
                                <p>Для тестирования успешной оплаты картой, введите следующие реквизиты:</p>
                                <p>№ карты: 4111 1111 1111 1111</p>
                                <p>Срок: 01/22, CVV: 123</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="field-block">
                    <label for="url-field">Url</label>
                    <input type="text" id="url-field" name="platon_url_field" value="<?=$platonUrl; ?>">
                    <span class="desc-field">Url выданный platon.ua для отправки платежного POST запроса.</span>
                </div>
                <div class="field-block">
                    <label for="secret-key">Секретный ключ</label>
                    <input type="text" id="secret-key" name="platon_secret_key" value="<?=$platonSecret; ?>">
                    <span class="desc-field">Ключ выданный platon.ua для идентификации Клиента.</span>
                </div>
                <div class="field-block">
                    <label for="password-key">Пароль</label>
                    <input type="text" id="password-key" name="platon_password_key" value="<?=$platonPassword; ?>">
                    <span class="desc-field">Пароль выданный platon.ua участвующий в формировании MD5 подписи.</span>
                </div>
                <div class="field-block return-url">
                    <label for="return-url">Return url</label>
                    <input type="text" id="return-url" name="return_url" value="<?=$returnUrl; ?>">
                    <span class="desc-field">Укажите URL, на который будет перенаправлен пользователь, после оплаты.</span>
                </div>
                <div class="field-block callback-url">
                    <label>Callback Url</label>
                    <pre class="copy-shortcode">
                        <?php echo home_url() . $callbackUrl; ?>
                    </pre>
                    <span class="desc-field">Сообщите в тех. поддержку platon.ua что ваш Callback Url:</span>
                </div>
            </div>
            
            <div class="content-tab simple-fields" id="form-settings">
                <span class="user-information">Обязательно установите поля к заполнению «E-mail» и «Имя» - что бы иметь возможность связаться с покупателем.</span>
                <table class="table-form-field">
                    <thead>
                        <tr>
                            <td>Название поля</td>
                            <td>Статус поля</td>
                            <td>Обязательно к заполнению</td>
                            <td class="placeholder-column">Подсказка для поля</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Имя</strong></td>
                            <td>
                                <input type="checkbox" class="checkbox" id="first-name" name="platon_first_name" <?php echo ($firstName) ? 'checked' : ''; ?>>
                                <label for="first-name"></label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="first-name-require" name="platon_first_name_require" <?php echo ($firstNameRequire) ? 'checked' : ''; ?>>
                                <label for="first-name-require"></label>
                            </td>
                            <td class="placeholder-column">
                                <input class="one-field" type="text" name="platon_first_name_placeholder" value="<?php echo $firstNamePlaceholder; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Фамилия</strong></td>
                            <td>
                                <input type="checkbox" class="checkbox"  id="last-name" name="platon_last_name" <?php echo ($lastName) ? 'checked' : ''; ?>>
                                <label for="last-name"></label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="last-name-require" name="platon_last_name_require" <?php echo ($lastNameRequire) ? 'checked' : ''; ?>>
                                <label for="last-name-require"></label>
                            </td>
                            <td class="placeholder-column">
                                <input class="one-field" type="text" name="platon_last_name_placeholder" value="<?php echo $lastNamePlaceholder; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>E-mail</strong></td>
                            <td>
                                <input type="checkbox" class="checkbox" id="email" name="platon_email" <?php echo ($email) ? 'checked' : ''; ?>>
                                <label for="email"></label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="platon-email-require" name="platon_email_require" <?php echo ($emailRequire) ? 'checked' : ''; ?>>
                                <label for="platon-email-require"></label>
                            </td>
                            <td class="placeholder-column">
                                <input class="one-field" type="text" name="platon_email_placeholder" value="<?php echo $emailPlaceholder; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Телефон</strong></td>
                            <td>
                                <input type="checkbox" class="checkbox" id="phone" name="platon_phone" disabled checked>
                                <label for="phone" class="phone-field checked-field"></label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="platon-phone-require" name="platon_phone_require" disabled checked>
                                <label for="platon-phone-require" class="phone-field checked-field"></label>
                            </td>
                            <td class="placeholder-column">
                                <input class="one-field" type="text" name="platon_phone_placeholder" value="<?php echo $phonePlaceholder; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Комментарий</strong></td>
                            <td>
                                <input type="checkbox" class="checkbox" id="comment" name="platon_comment" <?php echo ($comment) ? 'checked' : ''; ?>>
                                <label for="comment"></label>
                            </td>
                            <td>
                                <input type="checkbox" class="checkbox" id="comment-require" name="platon_comment_require" <?php echo ($commentRequire) ? 'checked' : ''; ?>>
                                <label for="comment-require"></label>
                            </td>
                            <td class="placeholder-column">
                                <input class="one-field" type="text" name="platon_comment_placeholder" value="<?php echo $commentPlaceholder; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Текст кнопки оплаты</strong></td>
                            <td></td>
                            <td></td>
                            <td class="placeholder-column">
                                <input class="one-field" type="text" name="name_button_form" value="<?php echo $nameButtonForm; ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="design-style-form">
                    <h1>Внешний вид формы</h1>
                    <hr />
                    <div class="left-settings-form">
                        <div class="field-block">
                            <label for="color-picker">Цвет подложки для всплывающего окна</label>
                            <input id="color-picker" name="color_overlay" value="<?php echo $colorOverlay; ?>" style="background-color: #<?php echo $colorOverlay; ?>" type="text">
                        </div>
                        <div class="field-block block-range-slider">
                            <label for="opacity-picker">Прозрачность подложки(0 - фон прозрачный)</label>
                            <input id="opacity-picker" name="opacity_overlay" value="<?php echo $opacityOverlay; ?>" type="hidden">
                            <div id="range-slider"></div>
                        </div>
                        <div class="field-block">
                            <label for="color-form">Цвет формы</label>
                            <input id="color-form" name="color_form" value="<?php echo $colorForm; ?>" style="background-color: #<?php echo $colorForm; ?>" type="text">
                        </div>
                        <div class="field-block">
                            <label for="color-button-form">Цвет рамки кнопки</label>
                            <input id="color-button-form" name="color_button_form" value="<?php echo $colorButtonForm; ?>" style="background-color: #<?php echo $colorButtonForm; ?>" type="text">
                        </div>
                    </div>
                    <div class="right-visual-form"></div>
                    <h2>Платежные кнопки</h2>
                    <div class="paybuttons">
                        <div class="paybutton">
                            <label for="white">
                                <input type="radio" id="white" name="pay_button_color" value="white" <?php echo $payButtonColor == 'white' ? 'checked="checked"' : '' ?>>
                                <div class="button-feed"><img src="<?php echo plugins_url('platon-pay/images/icon.svg'); ?>"> <span></span></div>
                            </label>
                        </div>
                        <div class="paybutton">
                            <label for="black">
                                <input type="radio" id="black" name="pay_button_color" value="black" <?php echo $payButtonColor == 'black' ? 'checked="checked"' : '' ?>>
                                <div class="black button-feed"><img src=" <?php echo plugins_url('platon-pay/images/icon.svg'); ?>"> <span></span></div>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="content-tab array-fields" id="content-shortcode">
                <div class="block-shortcodes" id="all-shortcode">
                    <div class="line-shortcode header-line">
                        <div class="column-field price-column">
                            <strong>Цена</strong>
                            <span class="help-icon">i</span>
                            <span class="tooltip-block">Укажите стоимость товара в формате: 12.00</span>
                        </div>
                        <div class="column-field payment-name-column">
                            <strong>Имя платежа</strong>
                            <span class="help-icon">i</span>
                            <span class="tooltip-block">Укажите название товара или услуги, для которой вы устанавливаете данную кнопку оплаты</span>
                        </div>
                        <div class="column-field button-name-column">
                            <strong>Кнопка вызова формы</strong>
                        </div>
                        <div class="column-field title-form">
                            <strong>Заголовок формы</strong>
                            <span class="help-icon">i</span>
                            <span class="tooltip-block">Вы можете указать заголовок всплывающего окна, при подтверждении оформления заказа, или продублировать в него название товара или услуги</span>
                        </div>
                        <div class="column-field shortcode-column">
                            <strong>Шорткод</strong>
                        </div>
                        <div class="column-field actions-column">
                            <strong>Действие</strong>
                        </div>
                    </div>
                    <?php if(isset($resultInfo) && $resultInfo != ''){
                        foreach($resultInfo as $key =>$result){ ?>
                            <div class="line-shortcode info-line" id="line-<?php echo $key; ?>">
                                <div class="field-block column-field price-column priceOptional">
                                    <input type="text" id="price<?php echo $key; ?>" name="shortcodes[<?php echo $key; ?>][price]" value="<?php echo $result['price']; ?>">
                                    <label for="optional<?php echo $key; ?>" class="optional-label">
                                        <input class="optional-checkbox" type="checkbox" id="optional<?php echo $key; ?>" name="shortcodes[<?php echo $key; ?>][optional]" <?php echo $result['optional'] == 1 ? 'checked' : '' ?>>
                                        Произвольно
                                        <span class="help-icon">i</span>
                                        <span class="tooltip-block">Если вам необходимо что бы пользователи сами указывали сумму оплаты, оставьте поле пусты и установите галочку «произвольно»</span>
                                    </label>
                                </div>
                                <div class="field-block column-field payment-name-column">
                                    <input type="text" name="shortcodes[<?php echo $key; ?>][payment_name]" value="<?php echo $result['payment_name']; ?>">
                                </div>
                                <div class="field-block column-field button-name-column">
                                    <input type="text" name="shortcodes[<?php echo $key; ?>][name_button]" value="<?php echo $result['name_button']; ?>">
                                </div>
                                <div class="field-block column-field title-form">
                                    <input type="text" name="shortcodes[<?php echo $key; ?>][title_form]" value="<?php echo $result['title_form']; ?>">
                                </div>
                                <div class="column-field shortcode-column">
                                    <pre class="copy-shortcode">
                                        [platon_pay id=<?php echo $key; ?>]
                                    </pre>
                                </div>
                                <div class="field-block column-field actions-column">
                                    <span class="btn-default red remove-shortcode" data-remove-id="<?php echo $key; ?>">Удалить</span>
                                </div>
                            </div>
                        <?php }
                    } ?>
                    
                </div>
                <span id="add-new-shortcode" class="btn-default orange">Добавить шорткод</span>
            </div>
    
            <div class="content-tab" id="design-form">
                <div class="field-block">
                    <label for="message-success-order">Успешное оформления заказа</label>
                    <span class="desc-field">Это сообщение появится после совершения успешного заказа, когда клиент вернется на сайт.</span>
                    <?php
                        $settings = array(
                            'textarea_name' =>  'message_success_order',
                            'editor_class'  =>  'my_redactor commons',
                            'dfw'       =>  true,
                            'quicktags' =>  false
                        );
                        
                        wp_editor( $defaultTextSuccessOrder, 'message_success_order' );
                    ?>
                </div>
            </div>
        </div>
    </form>

    <div class="bottom-panel">
        <span class="btn-default" id="save-settings-bottom">
            <img class="preloader" src="<?php echo plugins_url('platon-pay/images/preloader.gif'); ?>" alt="Preloader">
            Сохранить
        </span>
    </div>
    
    <div class="wrapper-psp-hook-up" id="psp-hook-up">
        <span class="close-modal-hook-up">x</span>
        <span class="title-modal-hook-up">Заявка на подключение</span>
        <p class="message-result">Заявка была успешно отправлена</p>
        <form class="form-hook-up">
            <div class="line-field-hook-up">
                <input type="text" name="your-name" placeholder="Ваше имя">
            </div>
            <div class="line-field-hook-up">
                <input type="email" name="your-email" placeholder="E-mail">
            </div>
            <div class="line-field-hook-up">
                <input type="tel" name="your-tel" class="phone-field-platon" placeholder="Телефон">
            </div>
            <input type="hidden" name="your-site" value="<?php echo home_url(); ?>">
            <input type="hidden" name="your-callback-url" value="<?php echo home_url() . $callbackUrl; ?>">
            <input type="hidden" name="your-topic" value="860">
            <input type="hidden" name="description" value="from_module">
            <input type="hidden" name="action" value="myaction" />
            <?php echo wp_nonce_field( 'platon_pay_nonce_hook_up','platon_pay_nonce_hook_up',true, false ); ?>
            <button id="send-info-hook-up" class="button-default-platon">Отправить заявку</button>
        </form>
    </div>
    <div class="overlay-psp-hook-up"></div>
    
    <script type="text/javascript">
        $(document).ready(function(){
            jQuery("#range-slider").slider({
                min: 0,
                max: 1,
                value: <?php echo $opacityOverlay; ?>,
                step: 0.1,
                slide: function (event, ui) {
                    $('#opacity-picker').attr('value', ui.value);
                }
            });
        });
    </script>
    
</div>