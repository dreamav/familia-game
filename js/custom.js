$(window).load(function() {
	$('.questionText, .questionFinalText').mCustomScrollbar({
		scrollButtons: {
			enable: true
		}
	});
	// показываем картинку в окошке попапом
	$(".mCSB_container img, .ispopup").on('click', function(event) {
		event.preventDefault();
		var markup = '<div class="popupMainContainer imageHolder">'+
			            '<img src="'+$('.mCSB_container img').attr('src')+'" alt="">'+
			          '</div>';
		$.magnificPopup.open({
		  items: {
		    src: markup,
			type: 'inline'
		  }
		});
	});	
});


jQuery(document).ready(function($) {




	// меняем фон блока с выбором, когда он становиться выбранным
	$('.choice label').on('click', function(event) {
		// event.preventDefault();
		if ( $(this).closest('.floatLeft').hasClass('checked') == false ) {
			$(this).closest('.floatLeft').addClass('checked').siblings('div').removeClass('checked');
		} else {
			
		}
	});




	// fix призы/помощь buttons top-margin
	function fixMainButtonsTopMargin(){
		var parentHeight = $(".availableQuestions").height(),
			avatarHeight = $(".avatar").closest('.hAuto').height(),
			levelsHeight = $(".userLevel").closest('.hAuto').height(),
			mButtonsHeight = $(".mainButtons").height(),

			qBoxesHeight = $(".questions-box").height(),
			dostVoprHeight = $(".questions-box").prev("h3").outerHeight(),

			delta = (qBoxesHeight+dostVoprHeight)-(avatarHeight+levelsHeight+mButtonsHeight);

		if ( delta > 0 ) {$(".mainButtons").css('margin-top', delta+'px');}

	}
	$(window).load(function() {
		if ( $(".mainButtons").length > 0 ) {
			fixMainButtonsTopMargin();
		}
	});
	$(window).resize(function(event) {
		if ( $(".mainButtons").length > 0 ) {
			fixMainButtonsTopMargin();
		}
	});




	var $o = {};
	$o.carouselwidget = $(".carousel-widget").length > 0 ? $(".carousel-widget") : false;

	if ($o.carouselwidget) {
		for (var i = 0; i < $o.carouselwidget.length; i++) {
			// SET ID ON ALL OBJECTS
			var owlObj = 'owl' + i;
			$($o.carouselwidget[i]).css({ opacity: 0 }).attr("id", owlObj).addClass(owlObj);
			slider("#" + owlObj);
		}
	}





});