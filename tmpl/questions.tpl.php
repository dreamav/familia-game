<?
$user = $_SESSION['user'];

$current_user_level = get_level(get_user_level($user->id));

// $warning = warning($user->id);

?>

<div class="violetHeader">
	<div class="col-md-1 col-xs-2">
		<div class="middle">
			<a href="/index.php"><img src="images/design/home-link.jpg" alt=""></a>
		</div>
	</div>
	<div class="col-md-10 col-xs-8 text-center">
		<div class="middle wFull">
			<div>
				<img src="images/design/familia-logo-violet.jpg" alt="">
			</div>
		</div>
	</div>
</div>

<div class="col-md-6 h90per availableQuestions">
	<div class="topMenu-xs"></div>
	<div class="authLinks hAuto flex flex-vh-center flex-column">
		<div class="avatar hAuto wFull text-center mrt-20 mrb-20"><img class="img-circle w30per" src="<?=$user->avatar?>" alt=""></div>
	</div>
	<div class="hAuto cleared">
		<div class="col-md-6">
			<div class="userLevel">Уровень: <?=!empty($current_user_level['level'])?$current_user_level['level']:"0"?></div>
			<div class="userLevelStatus wFull"><?=$current_user_level['name']?></div>
		</div>
		<div class="col-md-6 text-right">
			<div class="userPoints"><span>Баллы: <?=get_user_total_score($user->id)?></span></div>
		</div>
	</div>
	<div class="hAuto mainButtons text-center mrt-30 cleared">
		<div class="hAuto">
			<a href="index.php?action=show_prizes" class="btn btn-lg rounded gifts w40per"><i class="fa fa-gift"></i>Призы</a>
		</div>
		<div class="hAuto ">
			<a href="index.php?action=help" class="btn btn-lg rounded help w40per"><i class="fa fa-question"></i>Помощь</a>
		</div>
	</div>
</div>


<div class="col-md-6 h90per availableQuestions">
	<h3 class="text-uppercase">Доступные задачи:</h3>
	<div class="questions-box hAuto">
	<?

	$i=0;

	$user_level = get_user_level($user->id);
    $last_closed_questions = get_last_closed_questions($user->id);

    // тут $data это переданный из index.php массив $questions
    // см ф-цию 
	foreach ($data as $key => $question) {
        switch ($question['q_type']){
            case 1:
                $boxClass = "textInput";
                $boxImage = "textInput.png";
            break;
            case 3:
                $boxClass = "imagesSelect";
                $boxImage = "imagesSelect-icon.png";
            break;
            case 2:
                $boxClass = "textSelect";
                $boxImage = "textSelect-icon.jpg";
            break;
            case 4:
                $boxClass = "seria";
                $boxImage = "seria-icon.jpg";
            break;
        } // end switch
        
         
        if ( $user_level >= 6 ) {
	        if ( $last_closed_questions[$i]['u_level'] >= 6 ) {
	        	$dt = new DateTime($last_closed_questions[$i]['created']);
	        	$dt->add(new DateInterval('PT12H'));
	        	$dt_now = new DateTime();
	        	$interval = $dt_now->diff($dt);
	        	
	        	if ( $dt_now < $dt ) {
	        		$boxClass = "closedQ";
					$boxImage = "closedQ-icon.jpg";
					$qu_link = 'index.php';
					$qu_disable = "disable";
					$qu_hours = $interval->h.' ч '.$interval->i.' м';

	        		$qu_title = "<p>Будет доступно через<br>".$qu_hours.'</p>';

	        	}else{
	        		$qu_link = 'index.php?action=show_question&qid='.$question['id'];
	        		$qu_title = $question['q_title'];
	        		$qu_score = $question['q_score'];
	        		$qu_seria_cur = show_seria_current($_SESSION['user']->id,$question['id']);
	        	}

	        } else {
	    		$qu_link = 'index.php?action=show_question&qid='.$question['id'];
	    		$qu_title = $question['q_title'];
	    		$qu_score = $question['q_score'];
	    		$qu_seria_cur = show_seria_current($_SESSION['user']->id,$question['id']);
	        }
        } else {
    		$qu_link = 'index.php?action=show_question&qid='.$question['id'];
    		$qu_title = $question['q_title'];
    		$qu_score = $question['q_score'];
    		$qu_seria_cur = show_seria_current($_SESSION['user']->id,$question['id']);        	
        }
    ?>
		<div class="wFull <?=$boxClass?>">
			<a href="<?=$qu_link?>">
				<div class="qboxHeader cleared">
					<div class="col-md-6 col-xs-6 flex flex-h-center"><img src="/images/design/<?=$boxImage?>" alt=""></div>
					<div class="col-md-6 col-xs-6 flex flex-h-center">
						<?if (!empty($qu_score)):?>
						<div class="hAuto wFull text-right">
							<img src="/images/design/gold-coin.png" alt="">
							<span><?=$qu_score?></span>
						</div>
						<?endif;?>	
					</div>
				</div>
				<div class="qboxContent text-center">
					<?=$qu_title?>
					<div class="hAuto qboxFooter cleared">
						<div class="hAuto text-right"><?=$qu_seria_cur?></div>
					</div>
				</div>
			</a>
		</div>
	<? 
	$i++;
	}// end foreach вывода плашек вопросов ?>
	</div>
</div>
<div class="hAuto mainButtons-xs text-center mrt-30">
	<div class="hAuto mrt-10">
		<a href="index.php?action=help" class="btn btn-lg rounded help w40per"><i class="fa fa-question"></i>Помощь</a>
	</div>
	<div class="hAuto">
		<a href="index.php?action=show_prizes" class="btn btn-lg rounded gifts w40per"><i class="fa fa-gift"></i>Призы</a>
	</div>
</div>