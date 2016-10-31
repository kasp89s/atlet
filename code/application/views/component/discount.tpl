<tr><td colspan=2><a rel="popup_name" class="poplight" href="/prezent1000{$url_suffix}"><img src="/i/discount.jpg" alt="1000 рублей в подарок!!!" border="0"></a><div id="popup_name" class="discount_div popup_block">
	<table class="discount_table" cellpadding=0 cellspacing=0>
		<tr>
			<td class="tlb_discount">
			</td>
			<td class="tb_discount" colspan=2>&nbsp;
			</td>
			<td class="trb_discount">
				<a href="#" class="close_discount"><img src="/i/action_img/cute_ball_stop.png" border="0"></a>
			</td>
		</tr>
		<tr>
			<td class="lb_discount">
			</td>
			<td class="content_discount">
				<img src="/i/action_img/boxtext.gif" border="0">
				<div class="listitem_discount">
					Вы сможете использовать эти деньги для оплаты любого товара в магазине Luxpodarki.ru, при условии, что сумма заказа превышает 20000 руб.
				</div>
				<div class="listitem_discount">
					Вы сможете принимать участие в конкурсах с ценными подарками!
				</div>
				<img src="/i/action_img/arrow_discount.gif" class="arrow_discount" border="0">
			</td>
			<td class="discount_form">
               <div class="message_discount">Введите Ваше имя и e-mail для получения сертификата!</div>
               <div class="clear10"></div>
               <div class="height10"></div>
               <form method="post" action="{$data.uri_base}/add" id="discountForm">
                    <font class="field_name_discount">Имя:</font>
                    <div class="open_input_discount"><div class="close_input_name_discount"><div class="input_discount"><input name="author" type="text" id="inp_author" value=""></div></div></div>
                    <div class="clear10"></div>
                    <div class="height10"></div>
                    <font class="field_name_discount">Email:</font>
                    <div class="open_input_discount"><div class="close_input_email_discount"><div class="input_discount"><input name="email" type="text" id="inp_email" value=""></div></div></div>
                    <div class="clear10"></div>
                    <div class="height10"></div>
                    <input type="image" class="button_discount" src="/i/action_img/button_discount.png" name="submit">
                    <div class="clear10"></div>
                    <div class="confid_discount">100 % конфиденциальность данных гарантируется!</div>
               </form>
			</td>
			<td class="rb_discount">
			</td>
		</tr>
		<tr>
			<td class="blb_discount">
			</td>
			<td class="bb_discount" colspan=2>&nbsp;
			</td>
			<td class="brb_discount">
			</td>
		</tr>
	</table>
</div><script language="JavaScript">
{literal}
	$('.poplight').click(function() {
	    var popID = $(this).attr('rel'); //Get Popup Name
        $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
	    //Определяет запас на выравнивание по центру (по вертикали по горизонтали)мы добавим 80px к высоте / ширине, значение полей вокруг содержимого (padding) и ширину границы устанавливаем в CSS
	    var popMargTop = ($('#' + popID).height()) / 2;
	    var popMargLeft = ($('#' + popID).width()) / 2;

	    //Устанавливает величину отступа на Popup
	    $('#' + popID).css({
	        'margin-top' : -popMargTop,
	        'margin-left' : -popMargLeft
	    });

	   //Fade in Background
	    $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn(); //Постепенное исчезание слоя - .css({'filter' : 'alpha(opacity=80)'}) используется для фиксации в IE , фильтр для устранения бага тупого IE
	    $('.popup_block').css({'filter' : 'alpha(opacity=100)'}).fadeIn(); //Постепенное исчезание слоя - .css({'filter' : 'alpha(opacity=80)'}) используется для фиксации в IE , фильтр для устранения бага тупого IE

		$('object').hide();

	    return false;
	});

	//Закрыть всплывающие окна и Fade слой
	$('.close_discount, #fade').live('click', function() { //When clicking on the close or fade layer...
	    $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeOut(); //Постепенное исчезание слоя - .css({'filter' : 'alpha(opacity=80)'}) используется для фиксации в IE , фильтр для устранения бага тупого IE
	    $('.popup_block').css({'filter' : 'alpha(opacity=100)'}).fadeOut(function(){$('object').show();});

	    return false;
	});

	var options = {
        success:       parseResponse,  // post-submit callback
        beforeSubmit:  beforeSubmit,
        // other available options:
        url:       '{/literal}{$data.uri_base}{literal}/send_json',         // override for form's 'action' attribute
        type:      'post',        // 'get' or 'post', override for form's 'method' attribute
        dataType:  'json'        // 'xml', 'script', or 'json' (expected server response type)
        //clearForm: true        // clear all form fields after successful submit
        //resetForm: true        // reset the form after successful submit

        // $.ajax options can be used here too, for example:
        //timeout:   3000
    };

    function beforeSubmit(formData, jqForm, options) {
        $('#inp_author').css('background-color', 'white');
		$('#inp_email').css('background-color', 'white');
	    return true;
	}

    // post-submit callback
	function parseResponse(response, statusText, xhr, $form)  {	    if(response.error_form == 0){            $('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeOut(); //Постепенное исчезание слоя - .css({'filter' : 'alpha(opacity=80)'}) используется для фиксации в IE , фильтр для устранения бага тупого IE
	    	$('.popup_block').css({'filter' : 'alpha(opacity=100)'}).fadeOut(function(){$('object').show();});
	    	window.location = '{/literal}{$data.uri_base}{literal}/success';	    }else{	 		if(response.err_author){
	 			$('#inp_author').css('background-color', '#FF2222');	 		}

	 		if(response.err_email){
	 			$('#inp_email').css('background-color', '#FF2222');
	 		}	    }
	}

    $('#discountForm').ajaxForm(options);

    //$('.button_discount').click(function(){    //	$('#discountForm').submit();    //});
{/literal}
</script></td></tr>