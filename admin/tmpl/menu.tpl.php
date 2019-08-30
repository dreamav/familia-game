<?
if($_COOKIE['user_level'] == 0){
    $menu_actions = array(
            'questions' => "Вопросы",
            'game_levels' => "Уровни",
            'q_categories' => "Категории",
            'users' => 'Пользователи',
            'manual_check' => 'Вопросы с ручной проверкой'
        );
}

?>


    <? foreach( $menu_actions as $menu_action => $menu_label ) :?>
    <li class="nav-item start <?=$_GET['action']==$menu_action ? 'active' : ''?>">
        <!-- ?action=main -->
        <?php if ( !is_array($menu_label) ): ?>
            <a href="index.php?action=<?=$menu_action?>" class="nav-link ">
                <!-- <i class="icon-bar-chart"></i> -->
                <span class="title"><?=$menu_label?></span>
                <span class="selected"></span>
            </a>
        <?php else: ?>
            <a href="javascript:;" class="nav-link nav-toggle">
                <!-- <i class="icon-diamond"></i> -->
                <span class="title"><?=$menu_action?></span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <? foreach( $menu_label as $sub_menu_action => $sub_menu_label ) :?>
                <li class="nav-item <?=$_GET['action']==$sub_menu_action ? 'active open' : ''?>">
                    <a href="index.php?action=<?=$sub_menu_action?>" class="nav-link ">
                        <span class="title"><?=$sub_menu_label?></span>
                    </a>
                </li>
                <?endforeach;?>
            </ul>
        <?endif;?>
    </li>
    <?endforeach;?>

<script>
    jQuery(document).ready(function($) {
        var navItem = $('.sub-menu .nav-item');
        navItem.each(function(index, el) {
            var _ = $(this),
                pli = _.parents('li');

            if(_.hasClass('active')){
                pli.addClass('open active');
                pli.find('.arrow').addClass('open');
                pli.find('a').append($('<span></span>',{class:"selected"}));
            }
        });
    });
</script>    