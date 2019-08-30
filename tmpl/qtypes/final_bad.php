<?
if ( isset($_GET['pid']) ) {
    $active_sessia = seria_session($_SESSION['user']->id,$_GET['pid']);
    $active_sessia = $active_sessia[0];
    $active_sessia['questions'] = explode("|", $active_sessia['questions']);
}
?>
<div class="popupMainContainer">
	<div class="finalBad">
		<div class="finalContent text-center">
			<h3><?=htmlspecialchars_decode($data['final_text']['title'])?></h3>
			<?=htmlspecialchars_decode($data['final_text']['text'])?>
		</div>
		<div class="finalButton">
			<?
			// если еще есть вопросы в серии, покажем сл вопрос
			if ( isset($active_sessia['questions'][$active_sessia['current']]) ) { ?>
				<a href="index.php?action=show_question&qid=<?=$active_sessia['questions'][$active_sessia['current']]?>&pid=<?=$_GET['pid']?>" class="btn">продолжить</a>
			<? } else { // если в серии вопросы закончились, покажем ответ/результат на родителя?>
				<a href="index.php?action=check_user_answer&qid=<?=$active_sessia['parent_q']?>" class="btn">продолжить</a>
			<? } ?>
		</div>
	</div>
</div>