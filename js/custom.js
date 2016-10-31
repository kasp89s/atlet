var currentDeliveryId = 1;

$(document).ready(function() {

    $('#analog-button').click(
        function () {
//            $('li.active').removeClass('active');
//            $('div.box').removeClass('visible');
//            $('#analog-link').addClass('active');
//            $('#analog-data').addClass('visible');
            $('#analog-link').addClass('active').siblings().removeClass('active')
                .parents('.main-choice').find('div.box').hide().eq($('#analog-link').index()).fadeIn(150);
            $('html, body').animate({ scrollTop: $('div.main-choice').offset().top }, 'slow');
        }
    );
	$('.choosing dd').click(function(){
		if($(this).find(':input').attr('checked') != 'checked'){
			$(this).parent().find('dd').removeClass('current');
			$(this).parent().find('dd div :input').attr('checked',false);
			$(this).parent().find('dd div .niceRadio').removeClass('radioChecked');

			$(this).addClass('current');
			$(this).find(':input').attr('checked', 'checked');
			$(this).find('.niceRadio').addClass('radioChecked');

			if($(this).find(':input').attr('name') == 'delivery' && $(this).find(':input').attr('deltype')=='id'){
				changeCurDelivery($(this).find(':input').val());
			}else if($(this).find(':input').attr('name') == 'delivery' && $(this).find(':input').attr('deltype')=='rel'){
				changeCurDelivery($('#'+$(this).find(':input').attr('relitem')).val());
			}
		}
	});

	$('.niceRadio').click(function(){
		if($(this).find(':input').attr('name') == 'delivery' && $(this).find(':input').attr('deltype')=='id'){
			changeCurDelivery($(this).find(':input').val());
		}else if($(this).find(':input').attr('name') == 'delivery' && $(this).find(':input').attr('deltype')=='rel'){
			changeCurDelivery($('#'+$(this).find(':input').attr('relitem')).val());
		}
	});

	function changeCurDelivery(newDeliveryId){
		currentDeliveryId = newDeliveryId;
		updateCartState();
	}

	$('.itemQuantity').keyup(function(){
		$('#totalPrice'+$(this).attr('itemId')).html($(this).parent().find('.basePrice').val()*$(this).val());
		$.ajax({
		  dataType: "json",
		  url: '/cart/item_update',
		  data: {id: $(this).attr('itemId'), quantity: $(this).val(), taste: $('#taste-' + $(this).attr('itemId')).text(), productId: $('#productId-' + $(this).attr('itemId')).val()},
		  success: function(json){
              if (json != null && json.error != null){
                  alert(json.error);
              } else {
                  updateCartState();
              }
		  }
		});
	});

	function updateCartState(){
        $.ajax({
		  dataType: "json",
		  url: '/cart/get_ajax_cart',
		  data: {delivery: currentDeliveryId},
		  success: function(json){
		  	  $('#prodCount').html(json.prodCount);
              $('#subtotal').html(json.prodCost);
              $('#subtotalDiscount').html(json.prodCost);
//              if (json.totalSum > 3000) {
//                  json.deliveryCost = 0;
//              }
              $('#deliveryCost').html(json.deliveryCost);
              $('#totalSum').html(json.totalSum);

              $('.prodCount').html(json.count);

              if (json.count > 0) {
                  $('#emptyCart').hide();
                  $('#normalCart').show();
                  $('.prodCount').text(json.count);
              }

              $('emptyCart').hide();
              $('normalCart').show();
		  }
		});
	}

	$('.addToCartLink').click(function(){
		$('#addToCartContainer').hide();
		$('#addToCartContainerWait').show();

		$.ajax({
		    url: $('.addToCartLink').attr('href')+'&ajax=true',
		    data:{options: {taste: $('#tasteSelector').val(), warehouse: $('#delivery-date').attr('data-warehouse')}},
		    dataType: 'json',
		    beforeSend: function( xhr ) {

		    },
		    error:   function(){
		    	alert('При добавлении товара в корзину произошла ошибка связи.');
               	$('#addToCartContainer').show();
				$('#addToCartContainerWait').hide();
		    },

		    success: function( data ) {
				if(data.status=='ok'){
					updateCartState();
                    window.location.href = '/cart';
                	//$('#addToCartContainerClicked').show();
                	//$('#addToCartContainerWait').hide();

                	//$('#cart_node').css('background', 'URL(/i/cart_full.jpg)');
                    //$('#open-myModal-1').click();
//                	alert('Товар успешно добавлен в корзину');
                }else{
                	alert('Ошибка:'+data.message+'.');
                	$('#addToCartContainer').show();
					$('#addToCartContainerWait').hide();
                }
		    }
		});

		return false;
	});

	updateCartState();



$("#addRadio").click(
function() {
	$("#testForm").append("<div><input type='radio' class='niceRadio' name='myradio'/></div>");

	var el = $("input.niceRadio");
	changeRadioStart(el);
});



function changeRadio(el)

{


	var el = el,
		input = el.find("input").eq(0);
	var nm=input.attr("name");

	$(".niceRadio input").each(

	function() {

		if($(this).attr("name")==nm)
		{
			$(this).parent().removeClass("radioChecked");
		}


	});


	if(el.attr("class").indexOf("niceRadioDisabled")==-1)
	{
		el.addClass("radioChecked");
		input.attr("checked", true);
	}

    return true;
}

function changeVisualRadio(input)
{
	var wrapInput = input.parent();
	var nm=input.attr("name");

	$(".niceRadio input").each(

	function() {

		if($(this).attr("name")==nm)
		{
			$(this).parent().removeClass("radioChecked");
		}


	});

	if(input.attr("checked"))
	{
		wrapInput.addClass("radioChecked");
	}
}

function changeRadioStart(el)
{

try
{
var el = el,
	radioName = el.attr("name"),
	radioId = el.attr("id"),
	radioChecked = el.attr("checked"),
	radioDisabled = el.attr("disabled"),
	radioTab = el.attr("tabindex");
	radioValue = el.attr("value");
	radioDelType = el.attr("deltype");
	radioRelItem = el.attr("relitem");
	if(radioChecked)
		el.after("<span class='niceRadio radioChecked'>"+
			"<input type='radio'"+
			"name='"+radioName+"'"+
			"id='"+radioId+"'"+
			"checked='"+radioChecked+"'"+
			"deltype='"+radioDelType+"'"+
			"relitem='"+radioRelItem+"'"+
			"tabindex='"+radioTab+"'"+
            "value='"+radioValue+"' /></span>");
	else
		el.after("<span class='niceRadio'>"+
			"<input type='radio'"+
			"name='"+radioName+"'"+
			"id='"+radioId+"'"+
			"deltype='"+radioDelType+"'"+
			"relitem='"+radioRelItem+"'"+
			"tabindex='"+radioTab+"'"+
	        "value='"+radioValue+"' /></span>");

	if(radioDisabled)
	{
		el.next().addClass("niceRadioDisabled");
		el.next().find("input").eq(0).attr("disabled","disabled");
	}

	el.next().bind("mousedown", function(e) { changeRadio($(this)) });
	el.next().find("input").eq(0).bind("change", function(e) { changeVisualRadio($(this)) });
	if($.browser.msie)
	{
		el.next().find("input").eq(0).bind("click", function(e) { changeVisualRadio($(this)) });
	}
	el.remove();
}
catch(e)
{

}

    return true;
}
    function str_replace ( search, replace, subject ) {	// Replace all occurrences of the search string with the replacement string
        //
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Gabriel Paderni

        if(!(replace instanceof Array)){
            replace=new Array(replace);
            if(search instanceof Array){//If search	is an array and replace	is a string, then this replacement string is used for every value of search
                while(search.length>replace.length){
                    replace[replace.length]=replace[0];
                }
            }
        }

        if(!(search instanceof Array))search=new Array(search);
        while(search.length>replace.length){//If replace	has fewer values than search , then an empty string is used for the rest of replacement values
            replace[replace.length]='';
        }

        if(subject instanceof Array){//If subject is an array, then the search and replace is performed with every entry of subject , and the return value is an array as well.
            for(k in subject){
                subject[k]=str_replace(search,replace,subject[k]);
            }
            return subject;
        }

        for(var k=0; k<search.length; k++){
            var i = subject.indexOf(search[k]);
            while(i>-1){
                subject = subject.replace(search[k], replace[k]);
                i = subject.indexOf(search[k],i);
            }
        }

        return subject;

    }

    function transliterate(string)
    {
        var roman = ["Sch","sch",'Yo','Zh','Kh','Ts','Ch','Sh','Yu','ya','yo','zh','kh','ts','ch','sh','yu','ya','A','B','V','G','D','E','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','','Y','','E','a','b','v','g','d','e','z','i','y','k','l','m','n','o','p','r','s','t','u','f','','y','','e', '_'];
        var cyrillic = ["Щ","щ",'Ё','Ж','Х','Ц','Ч','Ш','Ю','я','ё','ж','х','ц','ч','ш','ю','я','А','Б','В','Г','Д','Е','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Ь','Ы','Ъ','Э','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','ь','ы','ъ','э', ' '];
        return str_replace(cyrillic, roman, string);
    }

    $('.sel210').change(
        function(){
            if ($(this).attr('id') != 'manselect') {
                $("#sort-form").submit();
            }
        }
    );

    var delivery = {
        0: 'При заказе до 18:00 доставка будет выполнена на завтра',
        1: 'Доставка будет выполнена на послезавтра',
        2: 'При заказе до 18:00 доставка будет выполнена на послезавтра',
        3: 'Доставка будет выполнена через 2 дня'
    }

    $('#tasteSelector').change(
        function(){
            var hour = $('#hour').val();
            var id = transliterate($(this).val());
            var article = $('#' + id).attr('data-article');
            var count = $('#' + id).val();
            var count2 = $('#' + id).attr('data-count');
            if (count2 > 0) {
                if (parseInt(hour) < 18) {
                    $('#delivery-date').text(delivery[0]);
                    $('#delivery-date').attr('data-warehouse', '2');
                } else {
                    $('#delivery-date').text(delivery[1]);
                    $('#delivery-date').attr('data-warehouse', '2');
                }
            } else {
                if (parseInt(hour) < 18) {
                    $('#delivery-date').text(delivery[2]);
                    $('#delivery-date').attr('data-warehouse', '1');
                } else {
                    $('#delivery-date').text(delivery[3]);
                    $('#delivery-date').attr('data-warehouse', '1');
                }
            }

            $('#current-article').text(article);
        }
    );

});
