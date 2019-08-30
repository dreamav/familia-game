<?
$user = $_SESSION['user'];
$active_sessia = seria_session($user->id,$_GET['qid']);
if ( empty( $active_sessia  ) ) {
	$ser_sessia['u_id'] = $user->id;
	$ser_sessia['current'] = 0;
	$ser_sessia['parent_q'] = $data['main_question']['id'];
	
	foreach ($data['questions'] as $qstion) {
		$ser_sessia['max_posible_points'] = $ser_sessia['max_posible_points']+get_question_score($qstion['id']);
		$ser_sessia['questions'][] = $qstion['id'];
	}
	start_seria_session($ser_sessia);
	$active_sessia = seria_session($user->id,$_GET['qid']);
	$active_sessia = $active_sessia[0];
	$active_sessia['questions'] = explode("|", $active_sessia['questions']);
}else{
	// reset_seria_current($user->id,$_GET['qid']);
	// тут надо получить текущие данные сессии
	$active_sessia = $active_sessia[0];
	$active_sessia['questions'] = explode("|", $active_sessia['questions']);
}

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
				<h2 class="questionTitle"><?=$data['main_question']['q_title']?></h2>
			</div>
		</div>
	</div>
</div>
<div class="pd-wrap h80per questionText seriaQuestionsPage">
	<?=$data['main_question']['q_text']?>
</div>
<div class="h10per seriaQuestionsPage">
	<div class="questionButtons text-right">
		<a href="index.php?action=show_question&qid=<?=$active_sessia['questions'][$active_sessia['current']]?>&pid=<?=$_GET['qid']?>" class="btn rounded check_answer">Начнем!<i class="fa fa-chevron-right mtl-7"></i></a>
	</div>
</div>