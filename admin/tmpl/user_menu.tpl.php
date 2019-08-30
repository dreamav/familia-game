<?

$user_data = get_user_data($_COOKIE['user_id']);

?>


<li class="dropdown dropdown-user">
    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <span class="username username-hide-on-mobile"> <?=$user_data['name']?> </span>
        <i class="fa fa-angle-down"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-default">
        <li>
            <a href="/admin/index.php?action=logout">
                <i class="icon-key"></i> Выйти </a>
        </li>
    </ul>
</li>