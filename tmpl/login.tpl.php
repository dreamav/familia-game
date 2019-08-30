<?php
$adapterConfigs = array(
    'facebook' => array(
        'client_id'     => '527576160919115',
        'client_secret' => '260106c158041800a76beb073d2bab1e',
        // 'redirect_uri'  => 'http://familia.loc/index.php?action=facebook'
        'redirect_uri'  => 'http://adventure.famil.ru/index.php?action=facebook'
    ),
    'vk' => array(
        'client_id'     => '6214528',
        'client_secret' => 'wAHroOJMgyGqdbvvMLCc',
        // 'redirect_uri'  => 'http://familia.loc/index.php?action=vk'
        'redirect_uri'  => 'http://adventure.famil.ru/index.php?action=vk'
    ),
    'odnoklassniki' => array(
        'client_id'     => '1256447744',
        'client_secret' => '204F5E9E51935A6306DFC5FB',
        'redirect_uri'  => 'http://adventure.famil.ru/index.php?action=odnoklassniki',
        'public_key'    => 'CBAQNEOLEBABABABA'
    )
);
$adapters = array();
foreach ($adapterConfigs as $adapter => $settings) {
    $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
    $adapters[$adapter] = new $class($settings);
}

if (!isset($_GET['code'])) {
    foreach ($adapters as $title => $adapter) {
        $adapter->getProvider()=="vk"?$iconClass = "vk":"";
        $adapter->getProvider()=="odnoklassniki"?$iconClass = "odnoklassniki":"";
        $adapter->getProvider()=="facebook"?$iconClass = "facebook":"";

        $auth_links .= '<p><a class="btn btn-lg rounded '.$adapter->getProvider().'" href="' . $adapter->getAuthUrl() . '"><i class="fa fa-'.$iconClass.'"></i>Вход через ' . ucfirst($title) . '</a></p>';
    }
}
?>
<div class="violetHeader visible-xs">
    <div class="col-xs-12 text-center">
        <div class="middle wFull">
            <div>
                <img src="images/design/familia-logo-violet.jpg" alt="">
            </div>
        </div>
    </div>
</div>
<div class="mr0 pd-wrap loginPage">
    <div class="col-md-6 hidden-xs">
        <div class="flex flex-column flex-v-center text-center">
            <div class="flex flex-v-center h20per">
                <div><img src="images/design/familia-logo.jpg" alt=""></div>
            </div>
            <div class="flex flex-v-center h40per">
                <div><img src="images/design/stiralnaya-mashina.jpg" alt=""></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="authLinks flex flex-column flex-vh-center">
            <h2 class="text-center mrt-0 h20per" <?=isset($_GET['banner']) ? 'style="font-size:22px;"' : '' ?> ><?=isset($_GET['banner']) ? 'Зарегистрируйтесь, чтобы продолжить игру' : 'Добро пожаловать в игру' ?></h2>
            <div class="hAuto text-center visible-xs">
                <img src="images/design/stiralnaya-mashina.jpg" alt="">
            </div>
            <div class="h40per"><?=$auth_links;?></div>
            <div class="h20per">
                <p class="text-center mrb-30">(Мы не публикуем записи без разрешения)</p>
                <p class="text-center small">Осуществляя вход, вы выражаете согласие с нашими <a href="index.php?action=pravo&p=polozhenie">Условиями обслуживание</a> и <a href="index.php?action=pravo&p=politika">Политикой конфиденциальности</a></p>
            </div>
        </div>
    </div>
</div>