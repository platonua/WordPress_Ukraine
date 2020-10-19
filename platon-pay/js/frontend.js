let $ = jQuery;
$(document).on('click', '.open-form' ,function(event){
    event.preventDefault();
    let idOpen = $(this).attr('data-open');
    $('.overlay').fadeIn(400,
        function(){
            $('#'+idOpen)
                .css('display', 'block')
                .animate({opacity: 1, top: '50%'}, 200);
        });
});
$(document).on('click', '.modal-close, .overlay', function(){
    if($(this).hasClass('success-custom-bl')){
        $('.info-success-order-overlay').remove();
        remove_get_order();
    }else{
        $('.modal-frontend-form')
            .animate({opacity: 0, top: '45%'}, 200,
                function(){
                    $(this).css('display', 'none');
                    $('.overlay').fadeOut(400);
                }
            );
    }

});
$(document).on('click', '.button-order-processing' ,function(e){
    let self = $(this);
    $('.error-field-required').remove();
    if(self.attr('data-active') == 0){
        e.preventDefault();
    }
    let parentForm = $(this).parents('form');

    let fieldsInfo = new Object();
    let errorObject = new Object();
    parentForm.find('input').each(function() {
        let patternPhone = /^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/;
        let valField = $(this).val();

        if($(this).attr('required') && valField == ''){
            $(this).before('<span class="error-field-required">Заполните обязательное поле!</span>');
            errorObject[this.name] = 'Required field';
        }else if(this.type === 'tel' && !patternPhone.test(valField)){
            $(this).before('<span class="error-field-required">Заполните обязательное поле!</span>');
            errorObject[this.name] = 'Required field';
        }else if(jQuery(this).hasClass('price-modal-input') && jQuery(this).val() == ""){
            $(this).before('<span class="error-field-required">Заполните обязательное поле!</span>');
            errorObject[this.name] = 'Required field';
        }

        if(this.type === 'checkbox'){
            fieldsInfo[this.name] = $(this).prop('checked') ? 1 : 0;
        }else{
            fieldsInfo[this.name] = valField;
        }
    });
    let dataInfo = {
        'action': 'sending_data',
        'dataType': 'json',
        'data_form': fieldsInfo,
        'security': $('#platon_pay_nonce_field').val()
    };
    if(Object.keys(errorObject).length == 0) {
        $.ajax({
            url: fieldsInfo.link_to_handler,
            data: dataInfo,
            type: 'POST',
            success:function(data){
                let result = JSON.parse(data);
                if(result.args_inputs){
                    parentForm.attr('action', result.url_to_send).append(result.args_inputs);
                    self.attr('data-active', '1');
                    parentForm.find('.do-not-transmit-field').remove();
                    parentForm.submit();
                }
            }
        });
    }
});


/*Перебор параметров в url, удаление лишних параметров*****************************************************************/
function remove_get_order(){
    let strGET = window.location.search.replace( '?', '').split('&').reduce(
        function(p,e){
            var a = e.split('=');
            p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
            return p;
        },{} );

    let infoUrl = window.location
    let pathName = '';
    if(infoUrl.pathname){
        pathName = infoUrl.pathname;
    }
    let newUrl = infoUrl.origin + pathName;

    if(strGET['order']){
        delete strGET['order'];
    }
    if(strGET['platon-result']){
        delete strGET['platon-result'];

        let countElement = Object.keys(strGET).length;
        let newGet = '';
        if(countElement > 0){
            newGet += '?'
            let counter = 1;
            for (var code in strGET) {
                let nameGet = code;
                let valueGet = strGET[code];
                if(counter != countElement){
                    newGet += nameGet+'='+valueGet+'&';
                }else {
                    newGet += nameGet+'='+valueGet;
                }
                counter++;
            }
        }
        history.pushState(null, '', newUrl + newGet);
    }
}


/*Добавляем плюс к полю*****************************************************************/
$(document).on('focus', '.phone-field-form' ,function(){

    let valTelIntut = $('.phone-field-form').val();
    if(valTelIntut == ''){
        $('.phone-field-form').val("+");
    }

});