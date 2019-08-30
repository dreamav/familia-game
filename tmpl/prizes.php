<?
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Familia - quest game</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=cyrillic" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/magnific-popup.css"/>
	<link rel="stylesheet" type="text/css" href="css/prizes.css"/>
	
	<!-- SCRIPTS -->
	<script type="text/javascript" src="js/jquery.min.js"></script>

<!-- Yandex.Metrika counter --> <script type="text/javascript"> (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter26209488 = new Ya.Metrika({ id:26209488, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/26209488" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter46338240 = new Ya.Metrika({
                    id:46338240,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/46338240" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-108221077-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-108221077-1');
</script>
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '428484804155664');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=428484804155664&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->	

</head>
<body id="prizesPage">

<div class="body">
	<div class="container">
		<div class="row header">
			<div class="col-md-4 col-xs-4 logo">
				<div class="middle">
					<a href="/index.php"><img src="images/design/landing/home.png" alt=""></a>
				</div>
			</div>
			<div class="col-md-4 col-xs-4 prizes">
				<h3>Призы!</h3>
			</div>
			<div class="col-md-4 col-xs-4 gifts">
				<div class="userPoints"><span><?=get_user_total_score($user->id)?></span></div>
			</div>
		</div> <!-- end top row with 3 cols -->
		<div class="row startGameSection">
			<div class="col-md-4 col-md-offset-4">
				<div class="discount">
					<h3>Скидка</h3>
					<p>7%</p>
				</div>
				<a id="prize1" href="index.php?action=start_game" class="btn btn-lg rounded startGame">Подробнее</a>

				<div class="forScroll">
					<a href="" class="scrollDown">
						<i class="fa fa-arrow-down"></i>
					</a>
				</div>
			</div>
		</div> <!-- end top row StartGame button -->
		<div class="row gameEtaps">
			<div class="col-md-2">
			</div>
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-4">
						<? if ( get_cert_qtity(2) > 0 ) { ?>
							<div class="cert1 text-center">
				                <h3>500 р</h3>
				            </div>
			                <a id="prize2" href="index.php?action=start_game" class="btn btn-lg rounded startGame">подробнее</a>
			                <p>осталось <?=get_cert_qtity(2);?><br><span>Когда сертификаты закончатся, стоимость аналогичных сертификатов в баллах может быть изменена!</span></p>
						<? } else { ?>
							<div class="certno text-center">
				                <h3>500 р</h3>
				            </div>
			                <div>Сертификаты<br>закончились</div>
						<? } ?>
					</div>
					<div class="col-md-4">
						<? if ( get_cert_qtity(3) > 0 ) { ?>
							<div class="cert2 text-center">
				                <h3>1000 р</h3>
				            </div>
			                <a id="prize3" href="index.php?action=start_game" class="btn btn-lg rounded startGame">подробнее</a>
			                <p>осталось <?=get_cert_qtity(3);?><br><span>Когда сертификаты закончатся, стоимость аналогичных сертификатов в баллах может быть изменена!</span></p>
						<? } else { ?>
							<div class="certno text-center">
				                <h3>1000 р</h3>
				            </div>
			                <div>Сертификаты<br>закончились</div>
						<? } ?>					
					</div>
					<div class="col-md-4">
						<? if ( get_cert_qtity(4) > 0 ) { ?>
							<div class="cert3 text-center">
				                <h3>1500 р</h3>
				            </div>
			                <a id="prize4" href="index.php?action=start_game" class="btn btn-lg rounded startGame">подробнее</a>
			                <p>осталось <?=get_cert_qtity(4);?><br><span>Когда сертификаты закончатся, стоимость аналогичных сертификатов в баллах может быть изменена!</span></p>
						<? } else { ?>
							<div class="certno text-center">
				                <h3>1500 р</h3>
				            </div>
			                <div>Сертификаты<br>закончились</div>
						<? } ?>	
					</div>
					<div class="col-md-12">
						<div class="main-prize">
							<h3>ГЛАВНЫЙ ПРИЗ</h3>
							<h2>20 000 Р</h2>
						</div>
						<div class="col-md-6 pokupki"><h3>ПОКУПКИ</h3></div>
						<div class="col-md-6 photosessia"><h3>ФОТОСЕССИЯ</h3></div>
						<div class="col-md-12 shoping">
							<h3>ШОПИНГ СО СТИЛИСТОМ</h3>
							<p>Разыгрывается случайным образом среди тех,</p>
							<p>кто наберёт <strong>150+ баллов</strong></p>
							<p class="small">
								Количество призов ограничено. <br>
								<a href="index.php?action=pravo&p=polozhenie">Юридичекая информация</a>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-2">
			</div>
		</div> <!-- end top row cases -->
	</div> <!-- end container -->
	<div class="footer">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<div class="middle footerContent">
						<div>
							<img src="images/design/landing/footer-logo.png" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<!-- SCRIPTS -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
<!-- LIVERELOAD DEV SCRIPT -->
<!-- <script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script> -->

<script type="text/javascript">
$('.scrollDown').on('click', function(e) {
	e.preventDefault();
	$('html, body').animate({
		scrollTop: $('.gameEtaps').offset().top
	}, 500, 'linear');
});	
$("a[id^='prize']").on('click', function(event) {
	event.preventDefault(event);
	/* Act on the event */
	var prize_t = $(this).attr('id'),	 // prize type
		uid = <?=$user->id?>;                 // user id

	$.ajax({
		url: 'index.php?action=check_prize',
		type: 'post',
		dataType: 'html',
		data: {prize_type:prize_t,u_id:uid}
	})
	.done(function(data) {
		$.magnificPopup.open({
		  items: {
		    src: data,
			type: 'inline'
		  },
		  showCloseBtn: false,
		  closeOnBgClick: false
		});
		$(".closeSkipPopup").on('click', function(event) {
			event.preventDefault();
			$.magnificPopup.close();
		});
	})
	.fail(function() {
		// console.log("error");
	})
	.always(function() {
		// console.log("complete");
	});
});


</script>

</body>
</html>