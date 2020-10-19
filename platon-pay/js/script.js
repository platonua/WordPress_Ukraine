$(document).ready(function () {

    /*Функция сохранения настроек------------------------*/
    function save_settings() {

        let dataOptions = {};
        dataOptions['shortcodes'] = new Array();

        $('#platon-form').find('input, textarea').each(function () {
            if (this.type === 'checkbox' && !jQuery(this).hasClass('optional-checkbox')) {
                dataOptions[this.name] = $(this).prop('checked') ? 1 : 0;
            } else if (this.type === 'textarea') {
                let idTextarea = $(this).attr('id');
                let visibleTextarea = $('#' + idTextarea).is(":visible");
                if (visibleTextarea) {
                    dataOptions[this.name] = '<p>' + $(this).val() + '</p>';
                } else {
                    dataOptions[this.name] = tinyMCE.get(idTextarea).getContent();
                }
            } else {
                let nameField = this.name.split('[');
                if (nameField.length > 1) {
                    let nameObj = nameField[0];
                    let keyObj = nameField[1].replace(']', '');
                    let nameFieldObj = nameField[2].replace(']', '');
                    let valInput = $(this).val();
                    if (jQuery(this).hasClass('optional-checkbox')) {
                        valInput = $(this).is(':checked') ? 1 : 0;
                    }
                    dataOptions['shortcodes'].splice(0, 0, { 'line': keyObj, 'name_field': nameFieldObj, 'value_field': valInput });
                } else {
                    if (this.type === 'radio' && jQuery(this).is(':checked')) {
                        dataOptions[this.name] = $(this).val();
                    } else if (this.type !== 'radio') {
                        dataOptions[this.name] = $(this).val();
                    } else {
                        return true;
                    }
                }
            }
        });

        let errors = "";
        let index = 1;

        $('#platon-form').find('.priceOptional').each(function () {
            if (jQuery('#price' + index).val() == "" && !jQuery('#optional' + index).is(':checked')) {
                errors += '<p class="errors">Цена не указана в строке ' + index + '</p>';
            }
            index++;
        })

        if (errors != "") {
            jQuery('.errors').remove();
            jQuery('.active-content').append(errors);
            return;
        } else {
            jQuery('.errors').remove();
        }

        let data = {
            action: 'save_settings',
            dataType: 'json',
            data_settings: dataOptions
        };
        jQuery.ajax({
            url: ajaxurl,
            data: data,
            type: 'POST',
            beforeSend: function () {
                $('.preloader').show();
            },
            success: function (response) {
                let result = JSON.parse(response);
                if (result.open_access) {
                    $(result.shortcodes).each(function (index, element) {
                        $('#line-' + element.line).find('.copy-shortcode').html("[platon_pay id=" + element.line + "]");
                    });
                    massage();
                } else {
                    alert('У вас нет прав для редактирования настроек плагина');
                }
                $('.preloader').hide();
            }
        });
    }

    /*Запуск функции сохранения настроек------------------------*/
    $('#save-settings, #save-settings-bottom').on('click', function () {
        save_settings();
    });


    /*Удаления строки шорткода----------------------------------*/
    $('#platon-form').on('click', '.remove-shortcode', function () {
        let selParent = $(this).parents('.line-shortcode');
        let objInfo = {
            title: 'Удаление шорткода',
            desc: 'Вы подтверждаете удаление шорткода?',
            btnTrue: 'Удалить',
            btnFalse: 'Отменить',
            funcTrue: remove_shortcode,
            funcFalse: '',
            infoToFuncTrue: selParent,
            infoToFuncFalse: ''
        }
        confirmation_of_information(objInfo);
    });
    function remove_shortcode(line) {
        line.animate(
            {
                left: '100%'
            },
            500,
            function () {
                line.remove();
                save_settings();
            }
        );
    }


    /*Удаление заказа с админки****************************************************************************************/
    $(document).on('click', '.remove-order', function () {
        let self = $(this);
        let objInfo = {
            title: 'Удаление заказа',
            desc: 'Вы подтверждаете удаление заказа?',
            btnTrue: 'Удалить',
            btnFalse: 'Отменить',
            funcTrue: remove_order_function,
            funcFalse: '',
            infoToFuncTrue: self,
            infoToFuncFalse: ''
        }
        confirmation_of_information(objInfo);
    });
    function remove_order_function(infoResult) {
        let idOrder = $(infoResult).attr('data-remove');
        let selParent = $(infoResult).parents('tr');
        let data = {
            action: 'remove_order',
            dataType: 'json',
            order_id: idOrder
        };
        jQuery.post(ajaxurl, data, function (response) {
            let result = JSON.parse(response);
            selParent.animate({ left: '100%' }, 500,
                function () {
                    selParent.remove();
                }
            );
            if (result.message === 'success') {
                console.log('Заказа был успешно удален!');
            } else {
                console.log('Произошла ошибка при удалении заказа');
            }
        });
    }
    if (jQuery('input[name="name_button_form"]').size()) {
        jQuery('.button-feed span').text(jQuery('input[name="name_button_form"]').val())
    }
    jQuery(document).on('change keyup input', 'input[name="name_button_form"]', function () {
        jQuery('.button-feed span').text(jQuery(this).val())
    })

    /*Переключение табов***********************************************************************************************/
    $('.tab-block').on('click', '.tab-default', function () {
        let sel = $(this);
        if (!sel.hasClass('active-tab')) {
            let selCode = $(this).attr('data-tab');
            $(this).addClass('active-tab').siblings('li').removeClass('active-tab');
            $('#' + selCode).addClass('active-content').siblings('div').removeClass('active-content');
        }
    });

    jQuery(document).on('click', '.optional-label', function () {
        let checkbox = jQuery(this).find('.optional-checkbox');
        if (checkbox.is(':checked')) {
            jQuery(this).parents('.priceOptional').find('input[type=text]').attr('disabled', 'disabled')
            jQuery(this).parents('.priceOptional').find('input[type=text]').val("")
        } else {
            jQuery(this).parents('.priceOptional').find('input[type=text]').removeAttr('disabled')
        }
    })

    /*Добавление строки для Шорткода***********************************************************************************/
    let countLine = ($('#all-shortcode').find('.info-line').length) + 1;
    $(document).on('click', '#add-new-shortcode', function () {
        let line_shortcode = '' +
            '<div class="line-shortcode info-line" id="line-' + countLine + '">' +
            '    <div class="field-block column-field price-column">\n' +
            '        <input class="priceOptional" type="text" id="price' + countLine + '" name="shortcodes[' + countLine + '][price]">\n' +
            '           <label for="optional' + countLine + '" class="optional-label">\n' +
            '                <input class="optional-checkbox" type="checkbox" id="optional' + countLine + '" name="shortcodes[' + countLine + '][optional]">Произвольно\n' +
            '                   <span class="help-icon">i</span>\n' +
            '                   <span class="tooltip-block">Если вам необходимо что бы пользователи сами указывали сумму оплаты, оставьте поле пусты и установите галочку «произвольно»</span>\n' +
            '           </label>' +
            '    </div>\n' +
            '    <div class="field-block column-field payment-name-column">\n' +
            '        <input type="text" name="shortcodes[' + countLine + '][payment_name]">\n' +
            '    </div>\n' +
            '    <div class="field-block column-field button-name-column">\n' +
            '        <input type="text" name="shortcodes[' + countLine + '][name_button]">\n' +
            '    </div>\n' +
            '    <div class="field-block column-field title-form">\n' +
            '        <input type="text" name="shortcodes[' + countLine + '][title_form]">\n' +
            '    </div>\n' +
            '    <div class="field-block column-field shortcode-column">\n' +
            '        <pre class="copy-shortcode">Для создания шорткода, сохраните настройки!</pre>\n' +
            '    </div>\n' +
            '    <div class="field-block column-field actions-column">\n' +
            '        <span class="btn-default red remove-shortcode">Удалить</span>\n' +
            '    </div>\n' +
            '</div>';
        $('#all-shortcode').append(line_shortcode);
        countLine = countLine + 1;
    });


    /*Окно оповещения после сохранения информации**********************************************************************/
    function massage() {
        $('.wrapper-plugin').append('<div class="message-block success">Ваши настройки успешно сохранены!</div>');
        setTimeout(function () {
            let mess = $('.message-block');
            mess.animate(
                {
                    opacity: 0
                },
                2000,
                function () {
                    mess.remove();
                }
            )
        }, 2000);
    }


    /*Инициализация виджета ColorPicker********************************************************************************/
    $("#color-picker").wheelColorPicker();
    $("#color-form").wheelColorPicker();
    $("#color-button-form").wheelColorPicker();


    /*Изменение цвета input в зависимости от выбраного цвета в ColorPicker*********************************************/
    $('.left-settings-form').on('blur', 'INPUT', function () {
        let colorInput = '#' + $(this).val();
        $(this).css({ 'background-color': colorInput });
    });


    /*Всплывающие подсказки********************************************************************************************/
    jQuery(document).on('mouseover', '.help-icon', function () {
        $(this).parent('.column-field').addClass('visible-tooltip');
        $(this).parent('.optional-label').addClass('visible-tooltip');
    }).on('mouseout', '.help-icon', function () {
        $(this).parent('.column-field').removeClass('visible-tooltip');
        $(this).parent('.optional-label').removeClass('visible-tooltip');
    });

    $("*[data-tooltip]").hover(function () {
        $('.tooltip-custom').remove();
        $(this).css('position', 'relative');
        var $toolTiptext = $(this).attr("data-tooltip");
        $(this).append("<div class='tooltip-custom'>" + $toolTiptext + "</div>");
    }, function () {
        $(this).css('position', '');
        $('.tooltip-custom').remove();
    });


    /*Всплывающее окно подтверждения действия**************************************************************************/
    function confirmation_of_information(info) {
        let html = '' +
            '<div class="common-block-confirmation">' +
            '   <div class="confirmation-modal">' +
            '      <span class="title-confirmation">' + info.title + '</span>' +
            '      <p class="desc-confirmation">' + info.desc + '</p>' +
            '      <div class="block-button-confirmation">' +
            '          <span class="button-confirmation delete-confirm" data-type="true">' + info.btnTrue + '</span>' +
            '          <span class="button-confirmation cancel-confirm" data-type="false">' + info.btnFalse + '</span>' +
            '      </div>' +
            '   </div>' +
            '   <div class="overlay-confirmation"></div>' +
            '</div>';
        $('.wrapper-plugin').append(html);
        $('.common-block-confirmation').on('click', '.button-confirmation', function () {
            $('.common-block-confirmation').remove();
            let resultClick = $(this).attr('data-type');
            if (resultClick === 'true') {
                typeof (info.funcTrue) === "function" ? info.funcTrue(info.infoToFuncTrue) : info.funcTrue
            } else {
                typeof (info.funcFalse) === "function" ? info.funcFalse(info.infoToFuncFalse) : info.funcFalse
            }
        });
    }


    /*Блокирование полей при включеном тестовом режиме*****************************************************************/
    let testModeElement = $('#platon-test-mode');
    testModeElement.on('change', function (el) {
        let { checked } = testModeElement[0];
        if (checked) {
            $('#url-field, #secret-key, #password-key, #return-url').attr('disabled', true);
            $('#test-mode-info').slideDown();
            testModeElement.next('label').text('Тестовый режим');
        } else {
            $('#url-field, #secret-key, #password-key, #return-url').removeAttr('disabled');
            $('#test-mode-info').slideUp();
            testModeElement.next('label').text('Рабочий режим');
        }
    });
    if (checked) {
        $('#url-field, #secret-key, #password-key, #return-url').attr('disabled', true);
        $('#test-mode-info').slideDown();
        testModeElement.next('label').text('Тестовый режим');
    }


    /*Всплывающее окно отправки запроса на подключение модуля**********************************************************/
    $('#platon-psp-hook-up').on('click', function (event) {
        $('.form-hook-up').show();
        $('.message-result').text('').hide();
        $('.overlay-psp-hook-up').fadeIn(400,
            function () {
                $('#psp-hook-up').css('display', 'block').animate({ opacity: 1, top: '50%' }, 200);
            });
    });
    $('.close-modal-hook-up, .overlay-psp-hook-up').on('click', function () {
        $('#psp-hook-up')
            .animate({ opacity: 0, top: '45%' }, 200,
                function () {
                    $('.overlay-psp-hook-up').fadeOut(400);
                }
            );
    });


    /*К полю телефона добавление плюса(+)******************************************************************************/
    $('.phone-field-platon').focus(function () {
        let sel = $(this);
        if (sel.val() === '') {
            sel.val('+');
        }
    }).blur(function () {
        let sel = $(this);
        if (sel.val() === '+') {
            sel.val('');
        }
    });


    /*Отправка информации с формы  на подключение модуля***************************************************************/
    $('#send-info-hook-up').click(function (event) {
        event.preventDefault();

        let infoHookUpArray = new Object();
        let requireFields = new Array();
        $('.form-hook-up').find('input').each(function () {
            infoHookUpArray[this.name] = $(this).val();
            if ($(this).val() === '') {
                requireFields.push(this.name);
            }
        });

        if (requireFields.length === 0) {
            let dataInfo = {
                action: 'send_info_hook_up',
                dataType: 'json',
                info: infoHookUpArray,
                security: $('#platon_pay_nonce_hook_up').val()
            };
            jQuery.ajax({
                url: ajaxurl,
                data: dataInfo,
                type: 'POST',
                success: function (response) {
                    let result = JSON.parse(response);
                    // console.log(result);
                    $('.form-hook-up').slideUp();
                    $('.message-result').show().attr('class', 'message-result status-' + result.status).text(result.message);
                }
            });
        } else {
            $('.message-result').show().addClass('status-error').text('Все поля обязательны для заполнения!');
        }
    });


    ////// чек и анчек полей на странице Список транзакций
    columnPrinter();

    jQuery(document).on('click', '.checkboxes label', function (e) {

        let columns = JSON.parse(localStorage.getItem('columns'));

        if (!columns) {
            columns = [];
        }

        let checked = jQuery(this).find('input').is(':checked');
        switch (jQuery(this).attr('for')) {
            case 'date':
                checker('date', columns, checked)
                break;
            case 'id-invoice':
                checker('id-invoice', columns, checked)
                break;
            case 'order-platon':
                checker('order-platon', columns, checked)
                break;
            case 'fio':
                checker('fio', columns, checked)
                break;
            case 'email':
                checker('email', columns, checked)
                break;
            case 'phone':
                checker('phone', columns, checked)
                break;
            case 'price':
                checker('price', columns, checked)
                break;
            case 'payment-name':
                checker('payment-name', columns, checked)
                break;
            case 'comment':
                checker('comment', columns, checked)
                break;
            default:
                break;
        }
    })
    function checker(needle, columns, checked) {
        if (checked) {
            if (!columns.includes(needle)) {
                columns.push(needle);
                jQuery('.' + needle).show()
            }
        } else {
            if (columns.includes(needle)) {
                var columns = columns.filter(function (elem) {
                    return elem != needle;
                });
                jQuery('.' + needle).hide()
            }
        }
        localStorage.setItem('columns', JSON.stringify(columns));
        return;
    }

    function columnPrinter() {
        let columns = JSON.parse(localStorage.getItem('columns'));

        if (!columns) {
            return;
        }

        if (columns.length) {
            columns.forEach(function (column) {
                jQuery('.' + column).show()
                jQuery('#' + column).prop("checked", true);
            })

        }

    }
});