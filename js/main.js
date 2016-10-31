// JavaScript Document

$(document).ready( function(){

    //Р”Р»СЏ РєСЂРѕСЃСЃР±СЂР°СѓР·РµСЂРЅРіРѕ placeholder
    $(function(){
        $('input[placeholder], textarea[placeholder]').placeholder();
    });

    /* PIE РґР»СЏ IE7-8 */
    if (window.PIE) {
        $('.btn, input, .footer, .top-menu, .top-menu menu li a, .left-menu ul li a, .button').each(function() {
            PIE.attach(this);
        });
    }

    // select
    $("select").selectBox();

    // left-menu
    $(".left-menu > ul > li > a").click(function(){
        if($(this).attr('href') == "#"){
            if($(this).is('.open')){
                $(this).removeClass('open');
                $(this).next('ul').slideUp(400);
            }else{
                $(this).addClass('open');
                $(this).next('ul').slideDown(400);
            }
            return false;
        }
    });

    // fancybox
    $('.fancybox').fancybox();

    // tabs
    $('ul.tabs').delegate('li:not(.active)', 'click', function() {
        $(this).addClass('active').siblings().removeClass('active')
            .parents('.main-choice').find('div.box').hide().eq($(this).index()).fadeIn(150);
    })


    $(".t-choice-main").click(function(){
        $(".choice ul").slideToggle(0);
    });


    $('.choice ul li a').click(function(){
        var srchTxt = $(this).text();
        $('.choice-main input').attr({'value' : srchTxt}).addClass('focus-cl');
        return false;
    })

    $(".choice ul li a").on("click", function(){
        $(".choice ul").hide(0);
    })

});//end ready

$(window).load(function() {

    $('.slide-main').flexslider({
        animation: "fade",
        slideshow: false,
        slideshow: true
    });

});

$(window).load(function() {
    // The slider being synced must be initialized first
    $('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        asNavFor: '#slider'
    });

    $('#slider').flexslider({
        animation: "fade",
        controlNav: false,
        animationLoop: false,
        slideshow: false,
        sync: "#carousel"
    });
});

//Plugin placeholder
(function(b){function d(a){this.input=a;a.attr("type")=="password"&&this.handlePassword();b(a[0].form).submit(function(){if(a.hasClass("placeholder")&&a[0].value==a.attr("placeholder"))a[0].value=""})}d.prototype={show:function(a){if(this.input[0].value===""||a&&this.valueIsPlaceholder()){if(this.isPassword)try{this.input[0].setAttribute("type","text")}catch(b){this.input.before(this.fakePassword.show()).hide()}this.input.addClass("placeholder");this.input[0].value=this.input.attr("placeholder")}},
    hide:function(){if(this.valueIsPlaceholder()&&this.input.hasClass("placeholder")&&(this.input.removeClass("placeholder"),this.input[0].value="",this.isPassword)){try{this.input[0].setAttribute("type","password")}catch(a){}this.input.show();this.input[0].focus()}},valueIsPlaceholder:function(){return this.input[0].value==this.input.attr("placeholder")},handlePassword:function(){var a=this.input;a.attr("realType","password");this.isPassword=!0;if(b.browser.msie&&a[0].outerHTML){var c=b(a[0].outerHTML.replace(/type=(['"])?password\1/gi,
        "type=$1text$1"));this.fakePassword=c.val(a.attr("placeholder")).addClass("placeholder").focus(function(){a.trigger("focus");b(this).hide()});b(a[0].form).submit(function(){c.remove();a.show()})}}};var e=!!("placeholder"in document.createElement("input"));b.fn.placeholder=function(){return e?this:this.each(function(){var a=b(this),c=new d(a);c.show(!0);a.focus(function(){c.hide()});a.blur(function(){c.show(!1)});b.browser.msie&&(b(window).load(function(){a.val()&&a.removeClass("placeholder");c.show(!0)}),
    a.focus(function(){if(this.value==""){var a=this.createTextRange();a.collapse(!0);a.moveStart("character",0);a.select()}}))})}})(jQuery);

jQuery(document).ready(function(){
    jQuery(".check").each(function(){
        changeCheckStart(jQuery(this));
    });
});
function changeCheck(el) {
    var el = el, input = el.find("input").eq(0);
    if(el.attr("class").indexOf("niceCheckDisabled")==-1) {
        if(!input.attr("checked")) {el.addClass("checked");input.attr("checked", true);}
        else {el.removeClass("checked");input.attr("checked", false).focus();}
    }
    return true;
}
function changeVisualCheck(input) {
    var wrapInput = input.parent();
    if(!input.attr("checked")) {
        wrapInput.removeClass("checked");
    }
    else {
        wrapInput.addClass("checked");
    }
}
function changeCheckStart(el)
{
    try
    {
        var el = el,
            checkName = el.find('input').attr("name"),
            checkId = el.find('input').attr("id"),
            checkChecked = el.find('input').attr("checked"),
            checkDisabled = el.find('input').attr("disabled"),
            checkTab = el.find('input').attr("tabindex"),
            checkValue = el.find('input').attr("value");
        if(checkChecked)
            el.after("<span class='check checked'>"+
                "<input type='checkbox'"+
                "name='"+checkName+"'"+
                "id='"+checkId+"'"+
                "checked='"+checkChecked+"'"+
                "value='"+checkValue+"'"+
                "tabindex='"+checkTab+"' /></span>");
        else
            el.after("<span class='check'>"+
                "<input type='checkbox'"+
                "name='"+checkName+"'"+
                "id='"+checkId+"'"+
                "value='"+checkValue+"'"+
                "tabindex='"+checkTab+"' /></span>");

        if(checkDisabled)
        {
            el.next().addClass("check_disabled");
            el.next().find("input").eq(0).attr("disabled","disabled");
        }

        el.next().bind("mousedown", function(e) { changeCheck(jQuery(this)) });
        el.next().find("input").eq(0).bind("change", function(e) { changeVisualCheck(jQuery(this)) });
        if(jQuery.browser.msie)
        {
            el.next().find("input").eq(0).bind("click", function(e) { changeVisualCheck(jQuery(this)) });
        }
        el.remove();
    }
    catch(e)
    {
    }
    return true;
}

(function($) {

    /*---------------------------
     Defaults for Reveal
     ----------------------------*/

    /*---------------------------
     Listener for data-reveal-id attributes
     ----------------------------*/

    $('a[data-reveal-id], input[data-reveal-id]').live('click', function(e) {
        e.preventDefault();
        var modalLocation = $(this).attr('data-reveal-id');
        $('#'+modalLocation).reveal($(this).data());
    });

    /*---------------------------
     Extend and Execute
     ----------------------------*/

    $.fn.reveal = function(options) {


        var defaults = {
            animation: 'fadeAndPop', //fade, fadeAndPop, none
            animationspeed: 300, //how fast animtions are
            closeonbackgroundclick: true, //if you click background will modal close?
            dismissmodalclass: 'btn-close-modal' //the class of a button or element that will close an open modal
        };

        //Extend dem' options
        var options = $.extend({}, defaults, options);

        return this.each(function() {

            /*---------------------------
             Global Variables
             ----------------------------*/
            var modal = $(this),
                topMeasure  = parseInt(modal.css('top')),
                topOffset = modal.height() + topMeasure,
                locked = false,
                modalBG = $('.reveal-modal-bg');

            /*---------------------------
             Create Modal BG
             ----------------------------*/
            if(modalBG.length == 0) {
                modalBG = $('<div class="reveal-modal-bg" />').insertAfter(modal);
            }

            /*---------------------------
             Open & Close Animations
             ----------------------------*/
            //Entrance Animations
            modal.bind('reveal:open', function () {
                modalBG.unbind('click.modalEvent');
                $('.' + options.dismissmodalclass).unbind('click.modalEvent');
                if(!locked) {
                    lockModal();
                    if(options.animation == "fadeAndPop") {
                        modal.css({'top': $(document).scrollTop()-topOffset, 'opacity' : 0, 'display' : 'block'});
                        modalBG.fadeIn(options.animationspeed/2);
                        modal.delay(options.animationspeed/2).animate({
                            "top": $(document).scrollTop()+topMeasure + 'px',
                            "opacity" : 1
                        }, options.animationspeed,unlockModal());
                    }
                    if(options.animation == "fade") {
                        modal.css({'opacity' : 0, 'display' : 'block', 'top': $(document).scrollTop()+topMeasure});
                        modalBG.fadeIn(options.animationspeed/2);
                        modal.delay(options.animationspeed/2).animate({
                            "opacity" : 1
                        }, options.animationspeed,unlockModal());
                    }
                    if(options.animation == "none") {
                        modal.css({'display' : 'block', 'top':$(document).scrollTop()+topMeasure});
                        modalBG.css({"display":"block"});
                        unlockModal()
                    }
                }
                modal.unbind('reveal:open');
            });

            //Closing Animation
            modal.bind('reveal:close', function () {
                if(!locked) {
                    lockModal();
                    if(options.animation == "fadeAndPop") {
                        modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
                        modal.animate({
                            "top":  $(document).scrollTop()-topOffset + 'px',
                            "opacity" : 0
                        }, options.animationspeed/2, function() {
                            modal.css({'top':topMeasure, 'opacity' : 1, 'display' : 'none'});
                            unlockModal();
                        });
                    }
                    if(options.animation == "fade") {
                        modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
                        modal.animate({
                            "opacity" : 0
                        }, options.animationspeed, function() {
                            modal.css({'opacity' : 1, 'display' : 'none', 'top' : topMeasure});
                            unlockModal();
                        });
                    }
                    if(options.animation == "none") {
                        modal.css({'display' : 'none', 'top' : topMeasure});
                        modalBG.css({'display' : 'none'});
                    }
                }
                modal.unbind('reveal:close');
            });

            /*---------------------------
             Open and add Closing Listeners
             ----------------------------*/
            //Open Modal Immediately
            modal.trigger('reveal:open')

            //Close Modal Listeners
            var closeButton = $('.' + options.dismissmodalclass).bind('click.modalEvent', function () {
                modal.trigger('reveal:close')
            });

            if(options.closeonbackgroundclick) {
                modalBG.css({"cursor":"pointer"})
                modalBG.bind('click.modalEvent', function () {
                    modal.trigger('reveal:close')
                });
            }
            $('body').keyup(function(e) {
                if(e.which===27){ modal.trigger('reveal:close'); } // 27 is the keycode for the Escape key
            });
            /*---------------------------
             Animations Locks
             ----------------------------*/
            function unlockModal() {
                locked = false;
            }
            function lockModal() {
                locked = true;
            }

        });//each call
    }//orbit plugin call
})(jQuery);

jQuery(document).ready(function(){

    jQuery(".niceRadio").each(
        function() {
            changeRadioStart(jQuery(this));
        });

});

function changeRadio(el)

{
    var el = el,
        input = el.find("input").eq(0);
    var nm=input.attr("name");

    jQuery(".niceRadio input").each(

        function() {

            if(jQuery(this).attr("name")==nm)
            {
                jQuery(this).parent().removeClass("radioChecked");
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
    jQuery(".niceRadio input").each(
        function() {
            if(jQuery(this).attr("name")==nm)
            {
                jQuery(this).parent().removeClass("radioChecked");
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
        if(radioChecked)
            el.after("<span class='niceRadio radioChecked'>"+
                "<input type='radio'"+
                "name='"+radioName+"'"+
                "id='"+radioId+"'"+
                "checked='"+radioChecked+"'"+
                "tabindex='"+radioTab+"'"+
                "value='"+radioValue+"' /></span>");
        else
            el.after("<span class='niceRadio'>"+
                "<input type='radio'"+
                "name='"+radioName+"'"+
                "id='"+radioId+"'"+
                "tabindex='"+radioTab+"'"+
                "value='"+radioValue+"' /></span>");

        if(radioDisabled)
        {
            el.next().addClass("niceRadioDisabled");
            el.next().find("input").eq(0).attr("disabled","disabled");
        }
        el.next().bind("mousedown", function(e) { changeRadio(jQuery(this)) });
        el.next().find("input").eq(0).bind("change", function(e) { changeVisualRadio(jQuery(this)) });
        if(jQuery.browser.msie)
        {
            el.next().find("input").eq(0).bind("click", function(e) { changeVisualRadio(jQuery(this)) });
        }
        el.remove();
    }
    catch(e)
    {

    }
    return true;
}


jQuery(function() {

    $.fn.startCarousel = function() {
        var bodywidth = $('body').width(),
            itemwidth = $('.mycarousel li').outerWidth(true),
            mycontwidth = bodywidth > itemwidth ? bodywidth - bodywidth%itemwidth : itemwidth,
            licount = $('.mycarousel li').size(),
            jscroll = 1;

        if(licount > mycontwidth/itemwidth){
            jscroll =  mycontwidth/itemwidth;
        } else {
            jscroll = 0;
            mycontwidth = licount * itemwidth;
        }

        $('.mycont').width(mycontwidth);

        $('.mycarousel').jcarousel({
            scroll:jscroll
        });
    };

    $(this).startCarousel();

    $(window).resize(function(){
        $(this).startCarousel();
    });

});


function setEqualHeight(columns)
{
    var tallestcolumn = 0;
    columns.each(
        function()
        {
            currentHeight = $(this).height();
            if(currentHeight > tallestcolumn)
            {
                tallestcolumn = currentHeight;
            }
        }
    );
    columns.height(tallestcolumn);
}
$(document).ready(function() {
    setEqualHeight($(".catalog > article"));
});

<!--Start of Zopim Live Chat Script-->
window.$zopim||(function(d,s){var z=$zopim=function(c){
    z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?2NJYMieiE1KfaJSgixmPHTuhJ1iRWMCZ';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
<!--End of Zopim Live Chat Script-->
