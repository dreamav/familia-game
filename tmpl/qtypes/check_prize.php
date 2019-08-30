<div class="popupMainContainer">
	<div class="checkPrize">
		<div class="finalContent text-center">
			<?= !empty($tpl_data['title'])?'<h3>'.$tpl_data['title'].'</h3>':'' ?>
			<?=$tpl_data['text']?>
			<?= !empty($tpl_data['price'])?'<p>Приз можно приобрести за <strong>'.$tpl_data['price'].'</strong></p>':'' ?>
		</div>
		<div class="finalButton <?=$tpl_data['allow']?'allowSkip':''?>">
			<?
				if ( $tpl_data['allow'] ) {
					echo '<a href="#" class="btn closeSkipPopup">отмена</a>';
					echo '<a href="index.php?action=get_prize&pt='.$tpl_data['id'].'" class="btn">приобрести</a>';
				}else{
					echo '<a href="index.php" class="btn closeSkipPopup">отмена</a>';
				}
			?>
		</div>
	</div>
</div>