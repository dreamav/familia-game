<?php
if ( isset($_GET['pid']) ) {
	$user = $_SESSION['user'];
	// seria_update_current($user->id,$_GET['pid'],$_GET['qid']);
	// seria_update_score($user->id,$_GET['pid'],$data['points']);

	$active_sessia = seria_session($user->id,$_GET['pid']);
	$active_sessia = $active_sessia[0];
	$active_sessia['questions'] = explode("|", $active_sessia['questions']);
}

if ( isset($active_sessia['questions'])==false ) { // если текущий вопрос не из серии. просто вопрос
	$description_text = get_question_share($_GET['qid']);
	$is_manual = is_manual($_GET['qid']);
}else{ // если это серия - получить текст шаринга родителя
	if ($data['final_type']=='good') {
		$description_text = get_question_share($_GET['qid']);
	}
}

?>

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
	<div class="col-md-1 col-xs-2">
		<div class="middle">
			<a href="/index.php"><img src="images/design/home-link.jpg" alt=""></a>
		</div>
	</div>
	<div class="col-md-10 col-xs-8 text-center">
		<div class="middle wFull">
			<div>
				<h2 class="questionTitle"><?=get_question_title($_GET['qid'])?></h2>
			</div>
		</div>
	</div>
</div>

<div class="h80per questionFinal <?=($data['final_type']=='good')?'goodAnswer':''?>">
	<div class="col-md-6">
		<div class="questionFinalTitle">
			<div class="flex flex-vh-center row1">
				<p><?=$data['final_text']['title']?></p>
			</div>
	
			<div class="flex flex-vh-center flex-column row2">
			<?= ($data['final_type']=='good')?"<div class='rainbow'><div class='hAuto'>":'' ?>
				<? if ( !isset($active_sessia['current']) ): ?>
				<h2><?=isset($data['points'])?$data['points']:0?></h2>
				<h3><?=plural_form($data['points'],array('балл','балла','баллов'))?></h3>
				<?endif;?>
			<?= ($data['final_type']=='good')?"</div></div>":'' ?>
			</div>
			<?
			if ($data['final_type']=='good'){ // показать блок с шарингом 
				if ( !$is_manual ) { ?>
			<div class="flex flex-flex-end flex-v-center row3">
				<div class="hAuto question-share flex flex-h-center flex-column">
					<p>Поделитесь достижениями с друзьями! <br> Получите +15 <img src="/images/design/gold-coin.png" alt=""> за первый пост. </p>
					<ul class="cleared">
						<li><a id="facebook" class="btn btn-lg rounded facebook" href="#"><i class="fa fa-facebook"></i></a></li>
						<li><a id="vk" class="btn btn-lg rounded vk" target="_blank" href="https://vk.com/share.php?url=http://adventure.famil.ru&title=<?=$description_text?>&image=http://adventure.famil.ru/images/design/landing/share.jpg"><i class="fa fa-vk"></i></a></li>
						<li><a id="odnoklassniki" class="btn btn-lg rounded odnoklassniki" target="_blank" href="https://connect.ok.ru/offer?url=http://adventure.famil.ru&description=<?=$description_text?>&imageUrl=http://adventure.famil.ru/images/design/landing/share.jpg"><i class="fa fa-odnoklassniki"></i></a></li>
					</ul>
				</div>
			</div>					
					
			    <?} else { ?>
			<div class="flex flex-flex-end flex-v-center row3">
				<div class="hAuto question-share flex flex-h-center flex-column">
				<p>Баллы будут начислены после проверки администратором.</p>
				</div>
			</div>
			<?}?>
		<?}?>
		</div>
	</div>
	<div class="col-md-6">
		<div class="questionFinalText">
			<div>
				<?=htmlspecialchars_decode($data['final_text']['text'])?>
			</div>
		</div>
	</div>
</div>

<div class="h10per checkAnswerResult">
	<div class="questionButtons text-right">
	<? if ( isset($active_sessia['current']) ){ 
            if ( isset($active_sessia['questions'][$active_sessia['current']]) ) { ?>
                <a href="index.php?action=show_question&qid=<?=$active_sessia['questions'][$active_sessia['current']]?>&pid=<?=$_GET['pid']?>" class="btn rounded check_answer">Далее</a>
            <? }elseif(get_question_type($_GET['qid'])==1 && !check_if_question_closed($_GET['qid'])){ ?>
                <a href="index.php?action=show_question&qid=<?=$_GET['qid']?>&pid=<?=$_GET['pid']?>" class="btn rounded check_answer">Попробовать еще раз</a>
            <? }else{
                if ( $data["correct_answer"] ) {
                    if ( isset( $data['user_level'] ) ) { ?>
                         <a href="index.php?action=show_new_level&qid=<?=$_GET['qid']?>" class="btn rounded check_answer">Продолжить</a>
                    <? }else{ ?>
                        <a href="index.php?action=check_user_answer&qid=<?=$active_sessia['parent_q']?>" class="btn rounded check_answer">Далее</a>
                    <? } ?>
                <?} else { ?>
                    <a href="index.php?action=check_user_answer&qid=<?=$active_sessia['parent_q']?>" class="btn rounded check_answer">Далее</a>
            <? }
	        }
        }else{

			if ( get_question_type($_GET['qid'])==1 ) { // тип вопроса ввод текста
				// тут надо проверить правильный ли ответ или нет
                if ( $data["correct_answer"] ) {
                    if ( isset( $data['user_level'] ) ) {?>
                        <a href="index.php?action=show_new_level&qid=<?=$_GET['qid']?>" class="btn rounded check_answer">Продолжить</a>
                    <?}else{?>
                        <a href="index.php" class="btn rounded check_answer">Продолжить</a>
                    <?}
                    // потом проверить повысился уровень или нет
				}else{?>
					<a href="index.php?action=show_question&qid=<?=$_GET['qid']?>" class="btn rounded check_answer">Попробовать еще раз</a>
				<?}
            }else{ // не ввыод текста<a href="index.php" class="btn rounded check_answer">Продолжить</a>
	            if ( $data["correct_answer"] ) { // если ответ правильный
                    if ( isset( $data['user_level'] ) ) { // если при этом заработан уровень
                    	echo '<a href="index.php?action=show_new_level&qid='.$_GET['qid'].'" class="btn rounded check_answer">Продолжить</a>';
        			}else{ // если уровень не поднялся
        				echo '<a href="index.php" class="btn rounded check_answer">Продолжить</a>';
        			}
				} else { // если ответ неверный
					echo '<a href="index.php" class="btn rounded check_answer">Продолжить</a>';
				}
        	}
        	
        }?>
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
			url: 'index.php?action=points_for_share&u_id=<?=$_SESSION["user"]->id?>&q_id=<?=$_GET['qid']?>&provider='+provider,
			type: 'post',
			dataType: 'json'
		})
		.done(function(data) {
			console.log(data);
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