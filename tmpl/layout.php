<!DOCTYPE html>
<html>
<head>
	<title>Я участвую в викторине от Familia!</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&amp;subset=cyrillic" rel="stylesheet">

	<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/jquery.mCustomScrollbar.css"/>
	<link rel="stylesheet" type="text/css" href="css/magnific-popup.css"/>
	<link rel="stylesheet" type="text/css" href="css/style.css?<?=time();?>"/>
	
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
<body <?=$data['page']?"id='".$data['page']."'":""?>>

<div class="parent">
	<div class="wrap">
	    <div class="row">
	        <? include $path; ?>
	    </div>
	</div>
</div>




<!-- SCRIPTS -->
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
<script type="text/javascript" src="js/owl.js"></script>


<script type="text/javascript" src="js/custom.js"></script>


<!-- <script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script> -->
</body>
</html>