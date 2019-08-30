<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '527576160919115',
      xfbml      : true,
      version    : 'v2.10'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<div class="violetHeader">
	<div class="col-md-1">
		<div class="middle">
			<a href="/index.php"><img src="images/design/home-link.jpg" alt=""></a>
		</div>
	</div>
	<div class="col-md-10 text-center">
		<div class="middle wFull">
			<div>
				<h2 class="questionTitle"><?=get_question_title($_GET['qid'])?></h2>
			</div>
		</div>
	</div>
</div>
<div class="h90per questionFinal">
	<div class="col-md-6">
		<div class="questionFinalTitle">
			<div class="h30per flex flex-vh-center">
				<p>Ура!<br>ты достиг уровня <?=$data['level']?></p>
			</div>
	
			<div class="h70per flex flex-vh-center flex-column">
				<p>Теперь ты</p>
				<h3><?=$data['name']?></h3>
				<div>
					<img src="/images/<?=$data['image']?>" alt="">
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="questionFinalText h50per">
			<div>
				<p><?=$data['l_text']?></p>
			</div>
		</div>
		<div class="h50per flex flex-vh-center flex-column">
			<div class="hAuto question-share flex flex-vh-center flex-column">
				<p class="text-center">Поделитесь достижениями с друзьями <br> Получите +15 <img src="/images/design/gold-coin.png" alt=""> за первый пост. </p>
				<ul class="cleared">
					<li><a id="facebook" class="btn btn-lg rounded facebook" href="#"><i class="fa fa-facebook"></i></a></li>
					<li><a id="vk" class="btn btn-lg rounded vk" target="_blank" href="https://vk.com/share.php?url=http://adventure.famil.ru&title=<?=$description_text?>&image=http://adventure.famil.ru/images/design/landing/share.jpg"><i class="fa fa-vk"></i></a></li>
					<li><a id="odnoklassniki" class="btn btn-lg rounded odnoklassniki" target="_blank" href="https://connect.ok.ru/offer?url=http://adventure.famil.ru&description=<?=$description_text?>&imageUrl=http://adventure.famil.ru/images/design/landing/share.jpg"><i class="fa fa-odnoklassniki"></i></a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {

	$('.facebook').click(function(e) {
		e.preventDefault();
		/*FB.ui({
		  method: 'share',
		  href: 'http://familia.loc',
		}, function(response){});*/
		FB.ui({
			method: 'share_open_graph',
			action_type: 'og.shares',
			action_properties: JSON.stringify({
				object: {
					'og:url': 'http://adventure.famil.ru', // your url to share
					'og:title': 'Familia adventure',
					'og:description': '<?=$description_text?>'
				}
			})
		}, function(response) {});
	});

	$('.facebook, .vk, .odnoklassniki').on('click', function(event) {
		// event.preventDefault();
		var provider = $(this).attr('id');
		
		$.ajax({
			url: 'index.php?action=points_for_share&u_id=<?=$_SESSION["user"]->id?>&level=<?=$data['level']?>&provider='+provider,
			type: 'post',
			dataType: 'json'
		})
		.done(function() {
			// console.log("success");
		})
		.fail(function() {
			// console.log("error");
		})
		.always(function() {
			// console.log("complete");
		});
		




	});

});

</script>