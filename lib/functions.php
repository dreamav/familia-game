<?php
include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'admin/classes/DBclass.php');

$DB = new c_database();
$DB->iniSet();
$DB->connect();



function view($path, $data = null){
    if (is_array($data)) {
        extract($data);
    }

    $path = $path.'.tpl.php';
    
    include "tmpl/layout.php";
}
function view_login($path, $data = null){
    if ($data){
        extract($data);
    }

    $path = $path.'.tpl.php';
    
    include "tmpl/login.php";
}

function auth($l,$p){
    global $DB;

    $DB->query_exec("SELECT * FROM managers WHERE login = '{$l}'");
    $pass = $DB->fetch();
    $pass = $pass[0];

    if ($pass['password']==md5($p)) {
        return $res = array('id'=>$pass['id'],'p_level'=>$pass['p_level']);
    } else {
        return false;
    }

}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
}
function formatPhone($phone){

    if ( strpos($phone, "|") ){
        $phones_arr = explode("|", $phone);

        $phone = "";
        foreach ($phones_arr as $tel) {
            preg_match('/^(\d{3})(\d{3})(\d{2})(\d{2})/', $tel, $matches);
            $phone .= $matches[1].' '.$matches[2].'-'.$matches[3].'-'.$matches[4].'<br>';
        }

        return substr($phone, 0, -4);

    } else {

        preg_match('/^(\d{3})(\d{3})(\d{2})(\d{2})/', $phone, $matches);
        return $matches[1].' '.$matches[2].'-'.$matches[3].'-'.$matches[4];

    }

}
function formatEmail($email){

    if ( strpos($email, "|") ){
        $emails_arr = explode("|", $email);

        $email = "";
        foreach ($emails_arr as $adr) {
            $email .= $adr.'<br>';
        }

        return substr($email, 0, -4);

    } else {

        return $email;

    }

}
function valid_phone($phone){
    if(preg_match("/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/", $phone)) {
        return true;
    }else{
        return false;
    }
}



$config = array(
    'username' => 'streetadv_todoru',
    'password' => 'HWgz9w4R',
    'database' => 'streetadv_todoru'
);


function connect($config){
    try {
        $conn = new PDO('mysql:host=localhost;dbname=' . $config['database'],
            $config['username'],
            $config['password']
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    } catch (Exception $e) {
        return false;
    }
}

function query($query, $bindings, $conn){
    $stmt = $conn->prepare($query);
    $stmt->execute($bindings);
    return $stmt;
}


function get($tabName, $conn, $limit = 10){
    try {
        $result = $conn->query("SELECT * FROM $tabName ORDER BY id DESC LIMIT $limit", PDO::FETCH_ASSOC);
        return ($result->rowCount() > 0)
            ? $result
            : false;
    } catch (Exception $e) {
        return false;
    }
}

function add_cert_activation($requestData){
    global $DB;

    extract($requestData);
    /**
        REQUEST:
        - $name
        - $cc
        - $email
        - $phone - может быть пустым
        - $certno
     */
    
    $DB->query_exec("INSERT INTO regusers(certno,name,email,phone,cc) VALUES ('{$certno}', '{$name}', '{$email}', '{$phone}', '{$cc}')");

}

function get_by_certno($certno, $conn){
    $query = query(
        'SELECT * FROM letters WHERE certno LIKE :certno LIMIT 1',
        array('certno' => $certno),
        $conn);

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Проверяет существование номера сертификата 
 * введенного при регистрации /go в таблице
 * regusers. Возвращает или номер сертификата
 * или false
 * @param  string  $certno 
 * @return boolean
 */ 
function hasRegusersCert($certno){
    global $DB;    

    $certno = clean($certno);
    $certno = str_replace(" ", "", $certno);
    // в условии проверки есть E 
    // проверяем ТОЛЬКО електронный сертификат
    // если сгенерированного сертификата (уже отправленного пользователю)
    // в таблице regusers нету значит или неправильный сертификат
    // или сертификат физической коробочки
    preg_match( "/(.*E)(.{2})(\d{6})/", strtoupper($certno), $to_gen_cert_for_check );
    $cert_for_check = $to_gen_cert_for_check[1].' '.$to_gen_cert_for_check[2].' '.$to_gen_cert_for_check[3];

    if ( !empty($to_gen_cert_for_check) ) {
        $DB->query_exec("SELECT certno FROM regusers WHERE certno LIKE '%{$to_gen_cert_for_check[3]}%'");
        $exist_cert = $DB->fetch();
    }

    if ( !empty($exist_cert) ) {
        return $exist_cert[0];
    } else {
        return false;
    }
}

function check_cert_code($certno, $conn){
    global $DB;
    $data = array();

    // $query = query(
    //     'SELECT certno FROM letters WHERE active = 1',
    //     array(),
    //     $conn);

    ### получили все активные сертификаты:
    // $partner_codes = $query->fetchAll(PDO::FETCH_ASSOC);

    $DB->query_exec("SELECT certno FROM letters WHERE active = 1");
    $partner_codes = $DB->fetchsimple('box_prefix');    
    if ( is_array($partner_codes) ) {
        foreach ($partner_codes as $key => $value) {
            $partner_codes[$key] = $partner_codes[$key][0];
        }
        $partner_codes_string = '('.implode("|", $partner_codes).')';
    }

    $DB->query_exec("SELECT box_prefix FROM boxes");
    $box_prefixes = $DB->fetchsimple('box_prefix');
    if ( is_array($box_prefixes) ) {
        foreach ($box_prefixes as $key => $value) {
            if ( $box_prefixes[$key][0] == null ) {
                $box_prefixes[$key] = "null";
            }else{
                $box_prefixes[$key] = $box_prefixes[$key][0];
            }
        }
        $box_prefixes_string = '('.implode("|", $box_prefixes).')';
    }    

    ### перебираем каждые двебуквы:
    // foreach ($partner_codes as $value) {

        ### две буквы:
        // $partner_code = strtolower($value['certno']);

        $el_cert = clean($certno);
        $el_cert = str_replace(" ", "", $el_cert);
        preg_match( "/(.*E)(.{2})(\d{6})/", strtoupper($el_cert), $to_gen_cert_for_check );
        $cert_for_check = $to_gen_cert_for_check[1].' '.$to_gen_cert_for_check[2].' '.$to_gen_cert_for_check[3];
        $DB->query_exec("SELECT * FROM electron_certs WHERE cert_no = '{$cert_for_check}'");
        $exist_cert = $DB->fetch();
        ### если в номере сертификата есть E и он найден в базе данных
        if ( !empty($exist_cert) ) {
            $today_date = date("Y-m-d H:i:s");
            ### запишем когда был зарегистрирован эл сертификат
            $DB->query_exec("UPDATE electron_certs SET reg_date = '{$today_date}' WHERE id = {$exist_cert[0]['id']}");

            $data = get_by_certno('%'.$to_gen_cert_for_check[2].'%',$conn);
            return $data[0];
        ### если в базе данных не найден
        } else {
            ### то или с E но неправильными цифрами
            if ( !empty($to_gen_cert_for_check[1]) ) {
                $data['error'] = "Ой!<br>Кажется, номер сертификата введён неверно.<br>Нужна помощь?<br>Пожалуйста, звоните:<br>8 495 255 77 89";
                return $data;
            }else{
                preg_match( "/^".$box_prefixes_string.".*".$partner_codes_string."(.*)/", strtoupper($certno), $matches );
                ### получить данные этого партн кода
                if(!empty($matches)){
                    $data = get_by_certno('%'.$matches[2].'%',$conn);
                } else {
                    $data['error'] = "Ой!<br>Кажется, номер сертификата введён неверно.<br>Нужна помощь?<br>Пожалуйста, звоните:<br>8 495 255 77 89";
                    return $data;
                }           
            }
            // нах die;
            ### или физическая коробочка
            ### тогда старый код
        }


        ### если в strtolower($certno - номер проверяемого сертификата) есть соответствие
        ### правилу 3 любых символа в начале, потом сколько угодно любых символов
        ### потом наши двебуквы 
        
        /*а можно сделать по-другому: получить все префиксы коробочек активных. записать их в строку так чтоб можно было подставить в regex
        и потом проверять соответствие префикса, кода и 6-ти цифр в конце*/

        /*прервать работу если есть E в префиксе - точное соответствие коду из базы*/


    // }

    return $data[0];
}

function get_clients($where){
    global $DB;

    return "No records";

}

function get_orders($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";
        // foreach ($where as $col => $value) {
        //     if (!empty($col)) {
        //         $sql_cond .= " AND ordered_month = {$value}";
        //     }
        // }
        if ( !empty( $where['box'] ) ) {
            $sql_cond .= " AND ordered_month = '{$where['box']}'";
        }
        if ( !empty( $where['name'] ) ) {
            $sql_cond .= " AND name LIKE '%{$where['name']}%'";
        }
        if ( !empty( $where['to'] ) ) {
            $sql_cond .= " AND payment_datetime < '{$where['to']}'";
        }
        if ( !empty( $where['from'] ) ) {
            $sql_cond .= " AND payment_datetime > '{$where['from']}'";
        }
        


        $DB->query_exec("SELECT * FROM orders {$sql_cond}");
    } else {
        $time_period = 0;
        $sql_date_cond = "LIKE";
        $today_date = "%" . date('Y-m-d', time() + $time_period) . "%";

        $DB->query_exec("SELECT * FROM orders WHERE payment_datetime {$sql_date_cond} '{$today_date}'");
    }


    return $DB->fetch();

}
function get_tdb_orders($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";

        if ( !empty( $where['name'] ) ) {
            $sql_cond .= " AND CL.name LIKE '%{$where['name']}%'";
        }
        if ( !empty($where['phone']) && $where['phone'] != "" ) {
            $sql_cond .= " AND CL.phone LIKE '%{$where['phone']}%'";
        }
        if ( !empty($where['email']) && $where['email'] != "" ) {
            $sql_cond .= " AND CL.email LIKE '%{$where['email']}%'";
        }
        if ( !empty($where['etap']) && $where['etap'] != "" ) {
            $sql_cond .= " AND Z.etap = '{$where['etap']}'";
        }
        if ( !empty($where['etap_source']) && $where['etap_source'] != "" ) {
            $sql_cond .= " AND Z.etap_source = '{$where['etap_source']}'";
        }
        if ( !empty($where['svyaz']) && $where['svyaz'] != "" ) {
            $sql_cond .= " AND Z.svyaz LIKE '%{$where['svyaz']}%'";
        }
        if ( !empty($where['comment']) && $where['comment'] != "" ) {
            $sql_cond .= " AND C.comm_text LIKE '%{$where['comment']}%'";
        }
        if ( !empty($where['box_name']) && $where['box_name'] != "" ) {
            $sql_cond .= " AND B.id = '{$where['box_name']}'";
        }
        if ( $where['box_type'] != "" ) {
            $sql_cond .= " AND Z.box_type = '{$where['box_type']}'";
        }
        if ( !empty($where['quantity']) && $where['quantity'] != "" ) {
            $sql_cond .= " AND Z.quantity LIKE '%{$where['quantity']}%'";
        }
        if ( !empty($where['delivery']) && $where['delivery'] != "" ) {
            $sql_cond .= " AND Z.delivery LIKE '%{$where['delivery']}%'";
        }
        if ( !empty($where['delivery_date']) && $where['delivery_date'] != "" ) {
            $sql_cond .= " AND Z.delivery_date LIKE '%{$where['delivery_date']}%'";
        }
        if ( !empty($where['city_id']) && $where['city_id'] != "" ) {
            $sql_cond .= " AND CT.id = '{$where['city_id']}'";
        }
        if ( !empty($where['from']) && $where['from'] != "" ) {
            $sql_cond .= " AND DATE(Z.z_date) >= DATE('{$where['from']}')";
        }
        if ( !empty($where['to']) && $where['to'] != "" ) {
            $sql_cond .= " AND DATE(Z.z_date) <= DATE('{$where['to']}')";
        }


        $DB->query_exec("SELECT Z.*, 
            CL.name, 
            CL.phone, 
            CL.email, 
            CL.label, 
            E.etap as etap_label,
            (select etap from etap where id = Z.etap_source) as etap_source_label,
            C.comm_text, 
            B.name as box_name,
            CT.city_label as city_label,
            CT.id as cl_city_id,
            B.id as box_id
                        FROM zayavki Z
                        LEFT JOIN clients CL ON Z.cli_id = CL.id
                        LEFT JOIN etap E ON Z.etap = E.id
                        LEFT JOIN comments C ON Z.comment_id = C.id
                        LEFT JOIN cities CT ON CT.id = CL.city_id
                        LEFT JOIN boxes B ON Z.box_name = B.id "
                        .$sql_cond." ORDER BY Z.z_date DESC");        


        // $DB->query_exec("SELECT * FROM orders {$sql_cond}");
    } else {
        $time_period = 0;
        $sql_date_cond = "LIKE";
        $today_date = "%" . date('Y-m-d', time() + $time_period) . "%";

        $DB->query_exec("SELECT Z.*,
         CL.name,
         CL.phone,
         CL.email,
         CL.label,
         E.etap as etap_label,
         (select etap from etap where id = Z.etap_source) as etap_source_label,
         C.comm_text,
         B.name as box_name,
         CT.city_label as city_label,
         CT.id as cl_city_id,
         B.id as box_id
                        FROM zayavki Z
                        LEFT JOIN clients CL ON Z.cli_id = CL.id
                        LEFT JOIN etap E ON Z.etap = E.id
                        LEFT JOIN comments C ON Z.comment_id = C.id
                        LEFT JOIN cities CT ON CT.id = CL.city_id
                        LEFT JOIN boxes B ON Z.box_name = B.id ORDER BY Z.z_date DESC");
    }

    $res = $DB->fetch();

    return $res;

}

function get_box_types($tp = 0){
    global $DB;

    $DB->query_exec("SELECT count(id), ordered_month from orders where ordered_month is not null group by ordered_month");
    return $DB->fetch();

}
function get_box_stats($where){
    global $DB;

    if (!empty($where['from'])&&!empty($where['from'])) {

        $sql_cond = "WHERE 1=1";

        if ( !empty( $where['to'] ) ) {
            $sql_cond .= " AND payment_datetime <= '{$where['to']}'";
        }
        if ( !empty( $where['from'] ) ) {
            $sql_cond .= " AND payment_datetime >= '{$where['from']}'";
        }


        $DB->query_exec("SELECT COUNT(id) AS quantity, ordered_month, payment_datetime
                        FROM orders {$sql_cond} GROUP BY DATE(payment_datetime), ordered_month");
    } else {
        $first_day_month = date('Y-m-01');
        $today_date = date('Y-m-d');

        $DB->query_exec("SELECT COUNT(id) AS quantity, ordered_month, payment_datetime
                        FROM orders
                        WHERE payment_datetime >= '{$first_day_month}' AND payment_datetime <= '{$today_date}' GROUP BY DATE(payment_datetime), ordered_month");
    }    


    return $DB->fetch();

}
function get_zayavki_stats($where){
    global $DB;

    if (!empty($where['from'])&&!empty($where['from'])) {

        $sql_cond = "WHERE 1=1";

        if ( !empty( $where['to'] ) ) {
            $sql_cond .= " AND z_date <= '{$where['to']}'";
        }
        if ( !empty( $where['from'] ) ) {
            $sql_cond .= " AND z_date >= '{$where['from']}'";
        }


        $DB->query_exec("SELECT COUNT(id) AS quantity, z_date, etap, etap_source
                        FROM zayavki {$sql_cond} GROUP BY DATE(z_date), etap, etap_source");
    } else {
        $first_day_month = date('Y-m-01');
        $today_date = date('Y-m-d');

        $DB->query_exec("SELECT COUNT(id) AS quantity, z_date, etap, etap_source
                            FROM zayavki
                            WHERE z_date BETWEEN '{$first_day_month}' AND '{$today_date}'
                            GROUP BY DATE(z_date), etap, etap_source");
    }    


    return $DB->fetch();

}


function insert_order($order){
    global $DB;

    extract($order);

    $DB->query_exec("INSERT INTO `orders` (invoice_id,status,payment_type,order_sum_amount,ordered_month,shp_month,payment_datetime,email,name,payment_payer_code,commision,shop_sum_amount,order_no) VALUES ({$invoice_id}, {$status}, {$payment_type}, {$order_sum_amount}, {$ordered_month}, {$shp_month}, {$payment_datetime}, {$email}, {$name}, {$payment_payer_code}, {$commision}, {$shop_sum_amount}, {$order_no})");
}

function update_order($order){
    global $DB;

    extract($order);

    if ( isset($invoice_id) ) {
        $DB->query_exec("UPDATE `orders` SET 
                            invoice_id = '{$invoice_id}',
                            status = '{$status}',
                            payment_type = '{$payment_type}',
                            order_sum_amount = '{$order_sum_amount}',
                            ordered_month = '{$ordered_month}',
                            shp_month = '{$shp_month}',
                            payment_datetime = '{$payment_datetime}',
                            payment_payer_code = '{$payment_payer_code}',
                            commision = '{$commision}',
                            shop_sum_amount = '{$shop_sum_amount}'
                            WHERE order_no = '{$order_no}'
                        ");
    } else {
        $DB->query_exec("UPDATE `orders` SET 
                            status = '{$status}',
                            payment_datetime = '{$payment_datetime}'
                            WHERE order_no = '{$order_no}'
                        ");
    }

}

function mobile_detect(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobiles = array(
        "iPod",
        "iPhone",
        "Android",
        "Symbian",
        "WindowsPhone",
        "WP7",
        "WP8",
        "Opera M",
        "webOS",
        "BlackBerry",
        "Mobile",
        "HTC_",
        "Fennec/"
    );
    foreach ($mobiles as $mob) if (strpos($user_agent, $mob) !== false) return true;
    return false;
}


function get_partnerCodes($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = " WHERE 1=1";

        if ( $where['active'] != '' ) {
            $sql_cond .= " AND active = {$where['active']}";
        }

        if ( $where['letter_code'] != '' ) {
            $sql_cond .= " AND certno LIKE '%{$where['letter_code']}%'";
        }

        if ( $where['letter_description'] != '' ) {
            $sql_cond .= " AND letters.desc LIKE '%{$where['letter_description']}%'";
        }

        $DB->query_exec("SELECT * FROM letters".$sql_cond);        

    } else {
        $DB->query_exec("SELECT * FROM letters");
    }

    $res = $DB->fetch();

    return $res;

}
function editPartnerCode($field,$fieldValue,$id){
    global $DB;

    $DB->query_exec("UPDATE letters SET letters.{$field} = '{$fieldValue}' WHERE id = '{$id}'");

}
function editComplect($field,$fieldValue,$id){
    global $DB;

    $DB->query_exec("UPDATE complects SET complects.{$field} = '{$fieldValue}' WHERE id = '{$id}'");

}

function get_qtity_used_certs($code_desc,$case){
    global $DB;

    $month_no = 20;

    switch ($case) {
        case '0':
            $DB->query_exec("SELECT COUNT(id) AS qtity FROM sformirovanye_zakazy WHERE complect LIKE '%{$code_desc}%' AND month_no = {$month_no}");
            break;
        case '1':
            $DB->query_exec("SELECT COUNT(id) AS qtity FROM sformirovanye_zakazy WHERE complect LIKE '%{$code_desc}%' AND box_type = 0 AND month_no = {$month_no}");
            break;
        case '2':
            $DB->query_exec("SELECT COUNT(id) AS qtity FROM sformirovanye_zakazy WHERE complect LIKE '%{$code_desc}%' AND box_type = 1 AND month_no = {$month_no}");
            break;
        case '3':
            $DB->query_exec("SELECT COUNT(id) AS qtity FROM sformirovanye_zakazy WHERE complect LIKE '%{$code_desc}%' AND box_type = 2 AND month_no = {$month_no}");
            break;
        case '4':
            $DB->query_exec("SELECT COUNT(id) AS qtity FROM sformirovanye_zakazy WHERE complect LIKE '%{$code_desc}%' AND box_type = 3 AND month_no = {$month_no}");
            break;
        
        default:
            # code...
            break;
    }
    
    $res = $DB->fetch();

    return $res[0];
}


function get_compiled_orders($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";

        if ( $where['s_price'] != '' ) {
            $sql_cond .= " AND s_price = '{$where['s_price']}'";
        }
        if ( $where['box_name'] != '' ) {
            $sql_cond .= " AND box_name = '{$where['box_name']}'";
        }
        if ( $where['box_type'] != '' ) {
            $sql_cond .= " AND box_type = '{$where['box_type']}'";
        }
        if ( $where['complect'] != '' ) {
            $sql_cond .= " AND complect = '{$where['complect']}'";
        }
        if ( $where['s_vydacha'] != '' ) {
            $sql_cond .= " AND s_vydacha = '{$where['s_vydacha']}'";
        }
        if ( $where['comment'] != '' ) {
            $sql_cond .= " AND comment LIKE '%{$where['comment']}%'";
        }
        if ( $where['phone'] != '' ) {
            $sql_cond .= " AND phone LIKE '%{$where['phone']}%'";
        }

        $DB->query_exec("SELECT SZ.id, SZ.s_price, SZ.box_name, SZ.box_type, SZ.complect, SZ.s_vydacha, SZ.comment, C.phone FROM sformirovanye_zakazy SZ left join clients C on SZ.cli_id = C.id ".$sql_cond);

    } else {
        $DB->query_exec("SELECT SZ.id, SZ.s_price, SZ.box_name, SZ.box_type, SZ.complect, SZ.s_vydacha, SZ.comment, C.phone FROM sformirovanye_zakazy SZ left join clients C on SZ.cli_id = C.id");
    }

    $res = $DB->fetch();

    return $res;

}

### заполняем таблицу Заказы клиента. получаем через ajax
function get_client_orders($where,$cli_id){
    global $DB;

    $DB->query_exec("SELECT * FROM sformirovanye_zakazy WHERE cli_id = {$cli_id}");
    $zakaz_data = $DB->fetch();

    $res = $zakaz_data;

    return $res;
}


function get_complects($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";

        if ( !empty( $where['name'] ) ) {
            $sql_cond .= " AND CL.name LIKE '%{$where['name']}'%";
        }
    } else {
        $DB->query_exec("SELECT * FROM complects WHERE active = 1");
    }

    $res = $DB->fetch();

    return $res;

}
function get_all_complects($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = " WHERE 1=1";

  

        if ( $where['active'] != '' ) {
            $sql_cond .= " AND active = {$where['active']}";
        }


        $DB->query_exec("SELECT * FROM complects".$sql_cond);

    } else {
        $DB->query_exec("SELECT * FROM complects");
    }

    $res = $DB->fetch();

    return $res;

}
function add_complect($postdata){
    global $DB;

    if (!empty($postdata['partnerCodeId'])) {
        extract($postdata);

        $label = $postdata['name'];
        $partnerCodesIds = serialize($postdata['partnerCodeId']);

        $DB->query_exec("INSERT INTO complects (label,sostav) VALUES ('{$label}','{$partnerCodesIds}')");

        header("Location:/admin/index.php?action=complecty");

    }

    $data = "";

    return $data;

}
function edit_complect($postdata){
    global $DB;

    if (!empty($postdata['partnerCodeId'])) {
        extract($postdata);

        $label = $postdata['name'];
        $partnerCodesIds = serialize($postdata['partnerCodeId']);        

        $DB->query_exec("UPDATE complects SET label = '{$label}',sostav = '{$partnerCodesIds}' WHERE id = {$postdata['compl_id']}");

        header("Location:/admin/index.php?action=complecty");

    }else{
        $DB->query_exec("SELECT * FROM complects WHERE id = {$postdata['compl_id']}");
        $res = $DB->fetch();
    }

    return $res;

}
function delete_complect($postdata){
    global $DB;

    $DB->query_exec("DELETE FROM complects WHERE  `id`={$postdata['compl_id']};");

}


function add_unic_complect($postdata){
    global $DB;

    if (!empty($postdata['partnerCodeId'])) {
        extract($postdata);

        ### сразу проверка доступности сертификатов
        foreach ($partnerCodeId as $check_code_id) {
            # code...
            $DB->query_exec("SELECT letters.desc FROM letters WHERE id = '{$check_code_id}'");
            $check_code_res = $DB->fetch();

            $code_desc = $check_code_res[0];

            switch ($box_type) {
                case '0':
                    # code...
                    break;
                case 'Физическая':
                    // физическая
                    $box_type_id = 1;
                    break;
                case 'Электронная':
                    // электронная
                    $box_type_id = 2;
                    break;
                case '3':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }


            
            $used = get_qtity_used_certs($code_desc['desc'],$box_type_id+1); // тут уже с учетом типа коробочки $used['qtity']

            $DB->query_exec("SELECT month_qtity_el, month_qtity_fiz FROM letters WHERE letters.desc = '{$code_desc['desc']}'");
            $vsegoMozhno = $DB->fetch(); // $vsegoMozhno[0]['month_qtity_el']||$vsegoMozhno[0]['month_qtity_fiz']


            switch ($box_type_id) {
                case '0':
                    # code...
                    break;
                case '1':
                    // физическая
                    if ( $vsegoMozhno[0]['month_qtity_fiz'] <= $used['qtity'] ) {
                        # code...
                        $error_qtity[$code_desc['desc']] = "<br>";
                    }
                    break;
                case '2':
                    // электронная
                    if ( $vsegoMozhno[0]['month_qtity_el'] <= $used['qtity'] ) {
                        # code...
                        $error_qtity[$code_desc['desc']] = "<br>";
                    }
                    break;
                case '3':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }            

        } // end foreach для перебора кодов партнеров для проверки доступности

        if ( !empty($error_qtity) ) {
            $errors_codes = '';
            foreach ($error_qtity as $descr => $descr_text) {
                $errors_codes .= $descr.' '.$descr_text."\n";
            }
            $res['error'] = $errors_codes;
            return $res;
        }




        $label = $postdata['name'];
        $partnerCodesIds = serialize($postdata['partnerCodeId']);

        ### вот тут начинаем проверку
        $DB->query_exec("SELECT * FROM complects WHERE sostav = '{$partnerCodesIds}'");
        $res = $DB->fetch();

        // если чё-то есть - добавляем стандартный комплект
        if ( is_array($res[0]) ) {
            if ( !empty($_COOKIE['user_id']) ) {

                $partnerCodesIDs = unserialize( $res[0]['sostav'] );

                $pCodesValues = get_by_ids('desc','letters',$partnerCodesIDs);
                $pCodesValues = implode(",",$pCodesValues);

                $metka = $res[0]['label'].':'.$pCodesValues;
                $metka = str_replace("::", ":", $metka);

                $DB->query_exec("UPDATE sformirovanye_zakazy SET complect = '{$metka}' WHERE id = {$postdata['zakaz_id']}");

                header("Location:/admin/index.php?action=compile_order&cli_id={$postdata['cli_id']}");

                die;
            }   
        } else { // создаем уникальный комплект
            if ( !empty($_COOKIE['user_id']) ) {

                $DB->query_exec("INSERT INTO complects (label,sostav,active,visible) VALUES ('{$label}','{$partnerCodesIds}',0,1)");


                $sostav_ids = unserialize( $partnerCodesIds );

                $sostav_str = get_by_ids('desc','letters',$sostav_ids);
                $sostav_str = implode(",",$sostav_str);

                $metka = $label.':'.$sostav_str;
                $metka = str_replace("::", ":", $metka);

                $DB->query_exec("UPDATE sformirovanye_zakazy SET complect = '{$metka}' WHERE id = {$postdata['zakaz_id']}");

                header("Location:/admin/index.php?action=compile_order&cli_id={$postdata['cli_id']}");

                die;
            }  
        }

    }

    $data = "";

    return $data;

}


function get_letters($tp = 0){
    global $DB;

    switch ($tp) {
        case 0:
            $time_period = 0;
            $sql_date_cond = "LIKE";
            $today_date = "%" . date('Y/m/d', time() + $time_period) . "%";
            break;

        default:
            # code...
            break;
    }

    $DB->query_exec("SELECT * FROM letters");
    return $DB->fetch();

}
function add_letter($postdata){
    global $DB;

    if (!empty($postdata['letter_code'])) {
        extract($postdata);

        $letter_tpl = "tpl/".strtolower($letter_code).".php";

        $DB->query_exec("INSERT INTO letters (certno,letter,city,letters.text) VALUES ('{$letter_code}','{$letter_tpl}','{$city}','{$letter_text}')");

    }

    $data = "";

    return $data;

}
function edit_letter($letter_id){
    global $DB;

    $DB->query_exec("SELECT * FROM letters WHERE id = $letter_id");
    return $DB->fetch();

}
function del_letter($letter_id){
    global $DB;

    $DB->query_exec("DELETE FROM letters WHERE  `id`={$letter_id};");

}
function save_letter($letter_id){
    global $DB;

    $text = $_POST['text'];

    // $DB->query_exec("INSERT INTO letters (text) VALUES () WHERE id=");
    $DB->query_exec("UPDATE `letters` SET `text`='$text' WHERE `id`=$letter_id;");
    return $DB->fetch();

}
function show_letter($letter_id){
    global $DB;

    // $DB->query_exec("INSERT INTO letters (text) VALUES () WHERE id=");
    $DB->query_exec("SELECT * FROM letters WHERE id = $letter_id");
    $letter_content = $DB->fetch();
    $letter_text = $letter_content[0]['text'];

    include rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'tpl/default.php';

    return $letter_body;

}



function get_boxletters(){
    global $DB;

    $DB->query_exec("SELECT * FROM boxletters ORDER BY box_id ASC");
    return $DB->fetch();

}
function add_boxletter($postdata){
    global $DB;

    if (!empty($postdata['box_id'])) {
        extract($postdata);

        $letter = str_replace("'", "\'", $letter);

        $DB->query_exec("INSERT INTO boxletters (box_id,boxletters.box,letter) VALUES ('{$box_id}','{$box}','{$letter}')");

    }

    $data = "";

    return $data;

}
function edit_boxletter($box_id){
    global $DB;

    $DB->query_exec("SELECT * FROM boxletters WHERE box_id = $box_id");
    return $DB->fetch();

}
function save_boxletter($box_id){
    global $DB;

    $text = $_POST['text'];
    $text = str_replace("'","\'",$text);

    // $DB->query_exec("INSERT INTO letters (text) VALUES () WHERE id=");
    $DB->query_exec("UPDATE `boxletters` SET `letter`='$text' WHERE `box_id`=$box_id;");
    return $DB->fetch();

}
function show_boxletter($box_id, $el = 1){
    global $DB;

    switch ($el) {
        case '1':
            # code...
            // $DB->query_exec("INSERT INTO letters (text) VALUES () WHERE id=");
            $DB->query_exec("SELECT * FROM boxletters WHERE box_id = $box_id");
            $letter_content = $DB->fetch();
            $letter_html = $letter_content[0]['letter'];

            require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'letter/default.php';
            break;

        case '2':
            # code...
            // $DB->query_exec("INSERT INTO letters (text) VALUES () WHERE id=");
            $DB->query_exec("SELECT * FROM boxletters WHERE box_id = $box_id");
            $letter_content = $DB->fetch();
            $letter_html = $letter_content[0]['letter'];

            $letter_html = str_replace("contact.png", "el-contact.png", $letter_html);

            require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'letter/default.php';
            break;
        
        default:
            # code...
            break;
    }


    return $letter_body;

}


// МЕНЕДЖЕРЫ
function get_managers(){
    global $DB;

    $DB->query_exec("SELECT * FROM managers");
    return $DB->fetch();

}
function add_manager($postdata){
    global $DB;
    if (!empty($postdata['login'])) {
        extract($postdata);
        $info = trim($info);
        $password = trim($password);

        $password = md5($password);
        $DB->query_exec("INSERT INTO managers (login,password,name,p_level,info) VALUES ('{$login}','{$password}','{$name}','{$p_level}','{$info}')");
    }

    $last_id = $DB->GetLastID();
    $DB->query_exec("SELECT * FROM managers WHERE id = {$last_id}");
    $res = $DB->fetch();
    $data = $res[0];
    return $data;
}
function del_manager($man_id){
    global $DB;

    $DB->query_exec("DELETE FROM managers WHERE id={$man_id}");

    $DB->query_exec("SELECT * FROM managers");
    return $DB->fetch();

}
function edit_manager($requestData){
    global $DB;

    if ( isset($requestData['update']) ) {
        
        $DB->query_exec("UPDATE managers SET name='{$requestData['name']}', login='{$requestData['login']}', p_level='{$requestData['p_level']}', info='{$requestData['info']}' WHERE id = {$requestData['manager_id']}");

        $DB->query_exec("SELECT * FROM managers WHERE id = {$requestData['manager_id']}");
        $res = $DB->fetch();
        $data = $res[0];
        return $data;
    } else {
        $DB->query_exec("SELECT * FROM managers WHERE id = {$requestData['manager_id']}");
        $res = $DB->fetch();
        $data = $res[0];
        return $data;
    }
}
// . МЕНЕДЖЕРЫ

function get_options(){
    global $DB;

    $DB->query_exec("SELECT * FROM options");
    return $DB->fetch();

}


function get_boxes(){
    global $DB;

    $DB->query_exec("SELECT * FROM boxes");
    return $DB->fetch();

}


function get_user_data($user_id){
    global $DB;

    $DB->query_exec("SELECT * FROM managers WHERE id = {$user_id}");
    $res = $DB->fetch();
    $data = $res[0];
    return $data;
}


// ПРОМОКОДЫ
function get_promocode($certno){
    global $DB;

    $DB->query_exec("SELECT * FROM promocodes WHERE certno = '{$certno}'");
    return $DB->fetch();

}
function set_promocode($certno,$email,$promocode_id,$next_id){
    global $DB;

    $DB->query_exec("SELECT id FROM promocodes WHERE certno = '{$certno}' ORDER BY id LIMIT 1");
    $current_id_arr = $DB->fetch();
    $current_id = $current_id_arr[0]['id'];

    if ($current_id) {
        $DB->query_exec("UPDATE promocodes SET email='{$email}' WHERE promocode_id=$promocode_id AND id = $current_id");
    } else {

        preg_match("/^man/", strtolower($certno), $matches);
        preg_match("/tdb/", strtolower($certno), $mart_match);
        preg_match("/go/", strtolower($certno), $go_match);

        if ( !empty($matches) ) {
            ### если в номере сертификата есть MAN
            ### тогда обновляем запись где box_id = 14
            $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 14 LIMIT 1");

        } else {

            if ( !empty($mart_match) ) {
                if ( !empty($go_match) ) {
                    $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 5 LIMIT 1");
                }else{
                    $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 3 LIMIT 1");
                }
            } else {
                $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 13 LIMIT 1");
            }
        }
    }
}
function fix_promocode($certno,$email,$promocode_id,$old_id){
    global $DB;

    $DB->query_exec("SELECT id FROM promocodes WHERE certno = '{$certno}' ORDER BY id LIMIT 1");
    $current_id_arr = $DB->fetch();
    $current_id = $current_id_arr[0]['id'];

    preg_match("/^man/", strtolower($certno), $matches);
    preg_match("/tdb/", strtolower($certno), $mart_match);

    if ( !empty($matches) ) {
        ### если в номере сертификата есть MAN
        ### тогда обновляем запись где box_id = 14
        $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 14 LIMIT 1");

    } else {

        if ( !empty($mart_match) ) {
            # code...
            $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 3 LIMIT 1");
            $DB->query_exec("UPDATE promocodes SET certno=NULL, email='0' WHERE id = $old_id LIMIT 1");
        } else {
            $DB->query_exec("UPDATE promocodes SET certno='{$certno}', email='{$email}' WHERE promocode_id=$promocode_id AND certno IS NULL AND box_id = 13 LIMIT 1");
        }
    }

}
function check_promocode($certno){
    global $DB;

    $DB->query_exec("SELECT id
                        FROM promocodes
                        WHERE certno IS NOT NULL
                        ORDER BY id
                        LIMIT 1");
    $res = $DB->fetch();
    return $res[0]['id'];

}
function get_all_promocodes($start,$limit){
    global $DB;

    if (!$start&&!$limit) {
        $start_id = 0;
        $end_id = 10;
        $limit = 10;
    } else {

        if ($start==1) {
            $start_id = 0;
        } else {
            $start_id = ($start-1) * $limit;
        }

        $end_id = $start * $limit;

    }

    $DB->query_exec("SELECT *
                        FROM promocodes WHERE id>{$start_id} AND id<={$end_id} AND certno IS NOT NULL ORDER BY id ASC LIMIT {$limit}");
    $res = $DB->fetch();

    $data = array($res,$start,$limit);

    return $data;

}
function add_promocode($promocode_id,$promocodes,$box_id){
    global $DB;

    $promocodes_array = explode("\r\n",$promocodes);

    if(is_array($promocodes_array)){
        foreach ($promocodes_array as $code) {
            $DB->query_exec("INSERT INTO promocodes (promocode,promocode_id,box_id) VALUES ('{$code}',{$promocode_id},{$box_id})");
        }
    }



    $data = "";

    return $data;

}
// . ПРОМОКОДЫ

function get_certregs($where,$limit){
    global $DB;



    // if (empty($search)) {
    //     # code...
    //     $DB->query_exec("SELECT *
    //                         FROM regusers WHERE id>{$start_id} AND id<={$end_id} ORDER BY id ASC LIMIT {$limit}");
    //     $res = $DB->fetch();
    // } else {
    //     $DB->query_exec("SELECT * FROM regusers WHERE certno LIKE '%{$search}%'");
    //     $res = $DB->fetch();
    // }

    // $data = array($res,$start,$limit);

    // return $data;


    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";

        if ( !empty( $where['certno'] ) ) {
            $sql_cond .= " AND certno LIKE '%{$where['certno']}%'";
        }
        if ( !empty( $where['name'] ) ) {
            $sql_cond .= " AND name LIKE '%{$where['name']}%'";
        }
        if ( !empty( $where['email'] ) ) {
            $sql_cond .= " AND email LIKE '%{$where['email']}%'";
        }
        if ( !empty( $where['phone'] ) ) {
            $sql_cond .= " AND phone LIKE '%{$where['phone']}%'";
        }
        if ( !empty( $where['to'] ) ) {
            $sql_cond .= " AND reg_time <= '{$where['to']}'";
        }
        if ( !empty( $where['from'] ) ) {
            $sql_cond .= " AND reg_time >= '{$where['from']}'";
        }      


        $DB->query_exec("SELECT * FROM regusers {$sql_cond} ORDER BY reg_time DESC");
    } else {
        $first_day_month = date('Y-m-01');
        $today_date = date('Y-m-d 23:59:59');

        $DB->query_exec("SELECT * FROM regusers WHERE reg_time BETWEEN '{$first_day_month }' AND '{$today_date}' ORDER BY reg_time DESC");
    }    


    return $DB->fetch();    

}


function generate_el_certs($postdata){
    global $DB;

    // $postdata['action'] = el_certs
    // $postdata['cert_prefix'] = 05TDB
    // $postdata['certno'] = 43 (id of AC)
    // $postdata['num_digits'] = ''
    // $postdata['quantity'] = 500
    // $postdata['start_pos'] = 26547
    // $postdata['box_id'] = 5 (id of MAY)


    if (isset($postdata['all_cert_no'])) {
        ###  сгенерируем по $postdata['quantity'] шт сертификатов для каждого кода
        ###  с указаной стартовой позиции
        
        $start_pos = $postdata['start_pos'];
        $box_id = $postdata['box_id'];

        $sql_values = '';
        
        $DB->query_exec("SELECT id FROM letters");
        $res = $DB->fetch();

        foreach ($res as $id) {
            # code...
            $cert_no = "{$postdata['cert_prefix']}"." ".get_by_id('certno','letters',$id['id']);
            for ($i=(int)$postdata['start_pos']; $i < (int)$postdata['start_pos']+(int)$postdata['quantity']; $i++) { 

                if ( strlen($i) < $postdata['num_digits'] ) {
                    $j = strlen($i);
                    $zeros = '';
                    while ($j < $postdata['num_digits']) {
                        $zeros .= "0";
                        $j++;
                    }

                    $digits = $zeros.$i;
                } else {
                    $digits = $i;
                }

                $cert_no .= " $digits";
                $sql_values .= ' (\''.$cert_no.'\','.$box_id.'),';
                $cert_no = substr($cert_no, 0, -6);
            }
        }

        $sql_values = substr($sql_values, 0, -1);
        

        $DB->query_exec("INSERT INTO electron_certs (cert_no,box_id) VALUES {$sql_values}");
    }else{


        $cert_no = "{$postdata['cert_prefix']}"." ".get_by_id('certno','letters',$postdata['certno']);
        $start_pos = $postdata['start_pos'];
        $box_id = $postdata['box_id'];

        $sql_values = '';
        for ($i=(int)$postdata['start_pos']; $i < (int)$postdata['start_pos']+(int)$postdata['quantity']; $i++) { 
            $cert_no .= " $i";
            $sql_values .= ' (\''.$cert_no.'\','.$box_id.'),';
            $cert_no = substr($cert_no, 0, -6);
        }

        $sql_values = substr($sql_values, 0, -1);


        $DB->query_exec("INSERT INTO electron_certs (cert_no,box_id) VALUES {$sql_values}");
    }

}

function get_el_certs($where){
    global $DB;

    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";

        if ( $where['name'] != '' ) {
            $sql_cond .= " AND C.name LIKE '%{$where['name']}%'";
        }
        if ( $where['email'] != '' ) {
            $sql_cond .= " AND C.email LIKE '%{$where['email']}%'";
        }
        if ( $where['cert_no'] != '' ) {
            $sql_cond .= " AND EC.cert_no LIKE '%{$where['cert_no']}%'";
        }
        if ( $where['reg_date_from'] != '' ) {
            $sql_cond .= " AND EC.reg_date >= DATE'{$where['reg_date_from']}'";
        }
        if ( $where['reg_date_to'] != '' ) {
            $sql_cond .= " AND EC.reg_date <= DATE'{$where['reg_date_to']}'";
        }
        if ( $where['used'] != '' ) {
            $sql_cond .= " AND EC.used = '{$where['used']}'";
        }

        $DB->query_exec("SELECT EC.id, EC.cert_no, EC.box_id, EC.used, EC.reg_date, EC.cli_id, C.name, C.phone, C.email FROM electron_certs EC left join clients C on EC.cli_id = C.id ".$sql_cond);

    } else {
        $DB->query_exec("SELECT EC.id, EC.cert_no, EC.box_id, EC.used, EC.reg_date, EC.cli_id, C.name, C.phone, C.email FROM electron_certs EC left join clients C on EC.cli_id = C.id");
    }

    $res = $DB->fetch();

    return $res;

}

function delete_el_cert($data){
    global $DB;

    $DB->query_exec("DELETE FROM electron_certs WHERE id = {$data}");

}

// API AJAX FUNCTIONS
function api_get_orders(){

}

// РАБОТА С ЗАКАЗАМИ
function compile_order($data){
    global $DB;

    $cli_name = get_by_id('name','clients',$data['cli_id']);

    $DB->query_exec("SELECT * FROM sformirovanye_zakazy WHERE cli_id = '{$data['cli_id']}'");
    $res = $DB->fetch();

    $data = array('cli_name'=>$cli_name, 'cli_zakazy'=>$res);

    return $data;
}
function delete_order($data){
    global $DB;

    $DB->query_exec("DELETE FROM `streetadv_todoru`.`sformirovanye_zakazy` WHERE  `id`={$data['zakaz_id']};");
    $res = $DB->fetch();

    return $data;
}
### выводит данные на основную страницу через view. не ajax
function cli_multiple_orders($data){
    global $DB;
    // получить имя телефон клиента из t:clients
    // получить все существующие заказы этого клиента t:sformirovanye_zakazy
    /* в t:sformirovanye_zakazy добавить поля
        - дата покупки
        - плановая дата выдачи
        - дата получения
     */
    ### получаем данные клиента для $data в view:
    $DB->query_exec("SELECT name, phone FROM clients WHERE id = {$data['cli_id']}");
    $cli_data = $DB->fetch();

    $DB->query_exec("SELECT * FROM sformirovanye_zakazy WHERE cli_id = '{$data['cli_id']}'");
    $ord_data = $DB->fetch();

    $ret['cli_data'] = $cli_data[0];
    $ret['ord_data'] = $ord_data;
    return $ret;
}

// . РАБОТА С ЗАКАЗАМИ

function edit_client($data){
    global $DB;

    if (empty($data['phone'])){
        $DB->query_exec("SELECT * FROM clients WHERE id = '{$data['cli_id']}'");
        $res = $DB->fetch();
    } else {
        $DB->query_exec("UPDATE clients SET name = '{$data['name']}', phone = '{$data['phone']}', email = '{$data['email']}' WHERE id = '{$data['cli_id']}'");

        header("Location:/admin/index.php?action=edit_client&cli_id=".$data['cli_id']);
        return;
    }


    return $res[0];

}


/*==================================
=            SEND BOXES            =
==================================*/

function send_box_one_client($zakaz_id){
    global $DB;
    include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'phpmailer/PHPMailerAutoload.php');

    $data_to_return = array();
    /*===========================================================================================
    =            проверяем можно ли отправлять коробочку в этот день + перестарховка            =
    ===========================================================================================*/
        $today_date = date("Y-m-d",time());


        $DB->query_exec("SELECT * FROM boxes WHERE send_date = DATE('{$today_date}')");
        $actual_box = $DB->fetch();
        $actual_box = $actual_box[0];

        if ($actual_box == null) {
            $data_to_return['error'] = "Сегодня коробочку отправлять нельзя. (это для перестраховки)";

            return json_encode($data_to_return);
        }
    /*=====  End of проверяем можно ли отправлять коробочку в этот день + перестарховка  ======*/
    
    ### получим комплект который надо отправить по zakaz_id
    $DB->query_exec("SELECT * FROM sformirovanye_zakazy WHERE box_type = 2 AND id = {$zakaz_id}");

    $complecty_to_send = $DB->fetch();
    $complect_to_send = $complecty_to_send[0];


        ### get client data by cli_id
        $DB->query_exec("SELECT * FROM clients WHERE id = {$complect_to_send['cli_id']}");
        $client_data = $DB->fetch();
        $client_data = $client_data[0];

        ### EXPLODE ПО | ПОТОМУ ЧТО МОЖЕТ БЫТЬ НЕСКОЛЬКО E-MAIL
        /*==============================================
        =            получили email клиента            =
        ==============================================*/
            $client_email = explode("|", $client_data['email']);
            if (is_array($client_email)) {
                $client_email = array_slice($client_email, -1);
                $client_email = trim($client_email[0]);
            }else{
                $client_email = trim($client_data['email']);
            }
        /*=====  End of получили email клиента  ======*/
        

        ### get cert codes
        $complect_items = explode(':', $complect_to_send['complect']);
        $sostav = explode(",", $complect_items[1]); // массив из описаний desc кодов партнеров. по ним будем получать данные


        ### тут генерируем коды
        ### берем префикс корбочки
        $current_box_prefix = $actual_box['box_prefix']."E"; // получаем префиикс текущей коробочки
        ### код партнера из sostav
        $certs = array(); // тут храним все сертификаты в коробочке
        $insert_into_letter = '';

        ### перебираем все desc в этом комплекте
        foreach ($sostav as $code_desc) {
            $DB->query_exec("SELECT * FROM letters WHERE letters.desc = '{$code_desc}'");
            $code_two_letters = $letter_data = $DB->fetch();
            $code_two_letters = trim($code_two_letters[0]['certno']);
            
if ( $letter_data[0]['active'] == 1 ) {
$cert_letter_text_part = <<<X
<div style="padding: 20px 50px;">
    <div style="padding: 10px 15px 40px 15px; border:1px solid #26b24b">
        <table style="text-align: center; font-family: 'Roboto Condensed', sans-serif; font-size: 16px;">
            <tbody>
                <tr>
                    <td><img src="{$letter_data[0]['logo']}" style="width: 150px"></td>
                    <td style="color: #26b24b">
                        <h1 style="color: #26b24b;text-transform: uppercase;font-size: 26px;">{$letter_data[0]['header']}</h1>
                        <h2 style="color: #26b24b;text-transform: uppercase;font-size: 16px;">{$letter_data[0]['sub_header']}</h2>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: justify;" colspan="2">
                        <p style="margin-bottom: 20px"><span style="text-transform: uppercase; color: #26b24b; margin-right: 7px; font-weight: bold;">где:</span>{$letter_data[0]['where']}</p>
                        <p style="margin-bottom: 20px"><span style="text-transform: uppercase; color: #26b24b; margin-right: 7px; font-weight: bold;">когда:</span>{$letter_data[0]['when']}</p>
                        <p style="margin-bottom: 20px"><span style="text-transform: uppercase; color: #26b24b; margin-right: 7px; font-weight: bold;">что там будет:</span><br>{$letter_data[0]['you_see']}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p style="font-weight: bold; text-transform: uppercase; color: #26b24b">номер вашего сертификата:   <span style="margin-left: 10px; font-size: 18px">{:certno}</span></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
X;
} elseif ( $letter_data[0]['active'] == 2 ) {
$cert_letter_text_part = <<<X
<div style="padding: 20px 50px;">
    <div style="padding: 10px 15px 40px 15px; border:1px solid #af63a7">
        <table style="text-align: center; font-family: 'Roboto Condensed', sans-serif; font-size: 16px;">
            <tbody>
                <tr>
                    <td><img src="{$letter_data[0]['logo']}" style="width: 150px"></td>
                    <td style="color: #af63a7">
                        <h1 style="color: #af63a7;text-transform: uppercase;font-size: 26px;">{$letter_data[0]['header']}</h1>
                        <h2 style="color: #af63a7;text-transform: uppercase;font-size: 16px;">{$letter_data[0]['sub_header']}</h2>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: justify;" colspan="2">
                        <p style="margin-bottom: 20px"><span style="text-transform: uppercase; color: #af63a7; margin-right: 7px; font-weight: bold;">где:</span>{$letter_data[0]['where']}</p>
                        <p style="margin-bottom: 20px"><span style="text-transform: uppercase; color: #af63a7; margin-right: 7px; font-weight: bold;">когда:</span>{$letter_data[0]['when']}</p>
                        <p style="margin-bottom: 20px"><span style="text-transform: uppercase; color: #af63a7; margin-right: 7px; font-weight: bold;">что там будет:</span><br>{$letter_data[0]['you_see']}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p style="font-weight: bold; text-transform: uppercase; color: #af63a7">номер вашего сертификата:   <span style="margin-left: 10px; font-size: 18px">{:certno}</span></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
X;
}

            ### проверяем есть ли в базе уже электронные сертификаты с текущим кодом партнера
            $DB->query_exec("SELECT * FROM electron_certs WHERE cert_no LIKE '%{$current_box_prefix} {$code_two_letters}%' ORDER BY id DESC LIMIT 0, 1");
            $max_num_cert = $DB->fetch();

            ### если нет - сгенерируем 6 цифр
            if (empty($max_num_cert)) {
                $num = sprintf("%06d",rand(1,222222));

                $cert_no = $current_box_prefix." ".$code_two_letters." ".$num;

                ### записываем в базу новый код и данные клиента, который получит
                $DB->query_exec("INSERT INTO electron_certs (cert_no,box_id,cli_id) VALUES ('{$cert_no}',{$actual_box['id']},{$client_data['id']})");

                $certs[] = $cert_no;
                $cert_letter_text_part = str_replace("{:certno}", $cert_no, $cert_letter_text_part);

                $insert_into_letter .= $cert_letter_text_part;
            }else{
                $max_num_cert = $max_num_cert[0]['cert_no'];
                ### получаем последние цифры с этим же кодом из базы данных, если такой записи нет - то генерируем
                $max_num = explode(" ", $max_num_cert);
                ### добавляем единичку
                $num = sprintf("%06d", $max_num[2] + 1);

                $cert_no = $current_box_prefix." ".$code_two_letters." ".$num;

                ### записываем в базу новый код и данные клиента, который получит
                $DB->query_exec("INSERT INTO electron_certs (cert_no,box_id,cli_id) VALUES ('{$cert_no}',{$actual_box['id']},{$client_data['id']})");

                $certs[] = $cert_no;
                $cert_letter_text_part = str_replace("{:certno}", $cert_no, $cert_letter_text_part);

                $insert_into_letter .= $cert_letter_text_part;
            }
        } // end foreach для перебора кодов партнеров в комплекте

        ### подготавливаем письмо
        $mail = new PHPMailer;

        /*if ($mail) {
            echo 'ok';
        } else {
            echo "no phpmailer";
        }*/

        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'smtp.timeweb.ru';
        $mail->Port = 2525;
        $mail->SMTPAuth = true;
        $mail->Username = 'info@todobox.ru';
        $mail->Password = 'pypAQhKn';

        $manager_adress = "todobox.info@gmail.com";

        $mail->setFrom('info@todobox.ru', 'ToDoBox');

        $mail->isHTML(true);
        $mail->Subject = 'Ваша электронная коробочка ToDoBox';

        $letter_text = show_boxletter(200,1);

        /*$ltr_phone = "8 495 255 77 89 | Никольская ул. 4/5 (офис)";
        $letter_text = str_replace('{$ltr_phone}', $ltr_phone, $letter_text);*/ 

        $client_box_codes = implode("<br>",$certs);
        $letter_text = str_replace('{client_box_codes}', $insert_into_letter, $letter_text);

        $mail->clearAllRecipients();
        $mail->addAddress($client_email);
        // $mail->addAddress("reloved@gmail.com");
        // $mail->addAddress("todobox.info@gmail.com");
        $mail->Body = $letter_text;

        ### ОТПРАВЛЯЕМ ПИСЬМО КЛИЕНТУ
            if(!$mail->send()) {
                $ltr_mes_manager = "Письмо с коробочкой клиенту:{$client_email} не отправлено. ". $mail->ErrorInfo ;
            } else {
                $ltr_mes_manager = "Письмо с коробочкой клиенту:{$client_email} отправлено.";
            }

        ### ОТПРАВЛЯЕМ ПИСЬМО-ДУБЛИКАТ МЕНЕДЖЕРУ
        $mail->Subject = 'Электронная коробочка клиенту '.$client_email.' отправлено';
        $mail->clearAllRecipients();
        $mail->addAddress("todobox.info@gmail.com");
        $mail->send();

        ### ОБНОВИМ СТАТУСЫ ЭТОГО
        update_qtity_zayavka($client_data['id']);
        update_sform_zakaz($complect_to_send['id']);

        $certs_list_str = implode("<br>", $certs);
        $data_to_return['success'] = "Коробочка с сертификатами:<br>".$certs_list_str."<br> клиенту:<br>".$client_email.'<br> отправлена<br><br>'.$ltr_mes_manager;

        return json_encode($data_to_return);
        
}
function send_corp_letter($data){
    global $DB;
    include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'phpmailer/PHPMailerAutoload.php');

    $data_to_return = array();

    $emails = str_replace(' ', '', $data['emails']);

    $pos = strpos($emails, ';');

    if ($pos) $emails = explode(";", $emails);

    

    // получаем HTML для нужного пиьсма
    $letter_html = show_boxletter(401);

    ### подготавливаем письмо
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = 'smtp.timeweb.ru';
    $mail->Port = 2525;
    $mail->SMTPAuth = true;
    $mail->Username = 'corporate@todobox.ru';    // info@todobox.ru
    $mail->Password = 'x14HRFy1';           // pypAQhKn

    $manager_adress = "todobox.corporate@gmail.com";

    $mail->setFrom('corporate@todobox.ru', 'ToDoBox');

    $mail->isHTML(true);
    $mail->Subject = $data['letter_subject'];
    $mail->Body = $letter_html;
    $mail->clearAllRecipients();

    if ( is_array($emails) ) {
        foreach ($emails as $email) {
            $mail->clearAllRecipients();
            $mail->addAddress($email);

            if(!$mail->send()) {
                $ltr_mes_manager .= "Корпоративное письмо на {$email} не отправлено. ". $mail->ErrorInfo ."<br>";
            } else {
                $ltr_mes_manager .= "Корпоративное письмо на {$email} отправлено.<br>";
            }
        }
    } else {
        $mail->addAddress($emails);

        if(!$mail->send()) {
            $ltr_mes_manager = "Корпоративное письмо на {$emails} не отправлено. ". $mail->ErrorInfo ;
        } else {
            $ltr_mes_manager = "Корпоративное письмо на {$emails} отправлено.";
        }        
    }

    return $ltr_mes_manager;

}
/*=====  End of SEND BOXES  ======*/




function get_option_select($option,$disabled){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.option = '{$option}'");
    $records = $DB->fetch();

    $option_values = explode("|", $records[0]['values']);

    $select = '<select class="form-control" name="'.$option.'" '.$disabled.'>';
    foreach ($option_values as $value) {
        $select .= '<option value="'.$value.'">'.$value.'</option>';
    }
    $select .= '</select>';

    return $select;

}
function generate_option_select_id($option,$id,$disabled){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.option = '{$option}'");
    $records = $DB->fetch();

    $option_values = explode("|", $records[0]['values']);

    $select = '<select class="form-control" name="'.$option.'" '.$disabled.'>';
    $i = 0;
    foreach ($option_values as $value) {
        $sel = ($i==$id)?"selected":"";
        $select .= '<option value="'.$i.'" '.$sel.'>'.$value.'</option>';
        $i++;
    }

    $select .= '</select>';

    return $select;
}
function generate_select($table, $field, $sel_name,$disabled){
    global $DB;

    if ( $table == "etap" ) {
        $DB->query_exec("SELECT id, {$field} FROM {$table} WHERE level = 0");
    } else {
        $DB->query_exec("SELECT id, {$field} FROM {$table}");
    }

    $records = $DB->fetch();

    $select = '<select class="form-control" name="'.$sel_name.'" '.$disabled.'>';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$value['id'].'">'.$value[$field].'</option>';
    }
    $select .= '</select>';

    return $select;
}

function generate_multi_select($table, $field, $sel_name,$disabled){
    global $DB;

    if ( $table == "etap" ) {
        $DB->query_exec("SELECT id, {$field} FROM {$table} WHERE level = 0");
    } elseif ( $table == "letters" ) {
        $DB->query_exec("SELECT id, {$table}.{$field} FROM {$table} WHERE active = 1 OR active = 2");
    } else {
        $DB->query_exec("SELECT id, {$table}.{$field} FROM {$table}");
    }

    $records = $DB->fetch();

    $select = '<select class="form-control" multiple="multiple" name="'.$sel_name.'[]" '.$disabled.' style="height:500px">';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$value['id'].'">'.$value[$field].'</option>';
    }
    $select .= '</select>';

    return $select;
}

function generate_complects_select(){
    global $DB;

    $vse_complecty = get_complects('');

    $records = array();

    foreach ($vse_complecty as $complect) {
        $partnerCodesIDs = unserialize($complect['sostav']);

        $pCodesValues = get_by_ids('desc','letters',$partnerCodesIDs);
        $pCodesValues = implode(",",$pCodesValues);

        $records[$complect['id']] = $complect['label'].':'.$pCodesValues;
    }



    $select = '<select class="form-control form-filter input-sm" name="complect"><option value=""></option>';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$key.'">'.$value.'</option>';
    }
    $select .= '</select>';

    return $select; 
}
function generate_complects_select_val_str(){
    global $DB;

    $vse_complecty = get_complects('');

    $records = array();

    foreach ($vse_complecty as $complect) {
        $partnerCodesIDs = unserialize($complect['sostav']);

        $pCodesValues = get_by_ids('desc','letters',$partnerCodesIDs);
        $pCodesValues = implode(",",$pCodesValues);

        $records[$complect['id']] = $complect['label'].':'.$pCodesValues;
    }



    $select = '<select class="form-control form-filter input-sm" name="complect"><option value=""></option>';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$value.'">'.$value.'</option>';
    }
    $select .= '</select>';

    return $select; 
}

function ff_get_option_select($option,$disabled){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.option = '{$option}'");
    $records = $DB->fetch();

    $option_values = explode("|", $records[0]['values']);

    $select = '<select class="form-control form-filter input-sm" name="'.$option.'" '.$disabled.'><option value=""></option>';
    foreach ($option_values as $value) {
        $select .= '<option value="'.$value.'">'.$value.'</option>';
    }
    $select .= '</select>';

    return $select;

}
function ff_get_option_select_val_num($option,$disabled){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.option = '{$option}'");
    $records = $DB->fetch();

    $option_values = explode("|", $records[0]['values']);

    $select = '<select class="form-control form-filter input-sm" name="'.$option.'" '.$disabled.'><option value=""></option>';
    $i = 0;
    foreach ($option_values as $value) {
        $select .= '<option value="'.$i.'">'.$value.'</option>';
        $i++;
    }

    $select .= '</select>';

    return $select;
}
function ff_generate_select($table, $field, $sel_name,$disabled){
    global $DB;

    if ( $table == "etap" ) {
        $DB->query_exec("SELECT id, {$field} FROM {$table} WHERE level = 0");
    } else {
        $DB->query_exec("SELECT id, {$field} FROM {$table}");
    }

    $records = $DB->fetch();

    $select = '<select class="form-control form-filter input-sm" name="'.$sel_name.'" '.$disabled.'><option value=""></option>';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$value['id'].'">'.$value[$field].'</option>';
    }
    $select .= '</select>';

    return $select;
}
function ff_generate_select_val_str($table, $field, $sel_name,$disabled){
    global $DB;

    if ( $table == "etap" ) {
        $DB->query_exec("SELECT id, {$field} FROM {$table} WHERE level = 0");
    } else {
        $DB->query_exec("SELECT id, {$field} FROM {$table}");
    }

    $records = $DB->fetch();

    $select = '<select class="form-control form-filter input-sm" name="'.$sel_name.'" '.$disabled.'><option value=""></option>';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$value[$field].'">'.$value[$field].'</option>';
    }
    $select .= '</select>';

    return $select;
}
function ff_generate_etap_source(){
    global $DB;


    $DB->query_exec("SELECT id, etap FROM etap WHERE level = 1");

    $records = $DB->fetch();

    $select = '<select class="form-control form-filter input-sm" name="etap_source"><option value=""></option>';
    foreach ($records as $key => $value) {
            $select .= '<option value="'.$value['id'].'">'.$value['etap'].'</option>';
    }
    $select .= '</select>';

    return $select;
}


function check_client($name,$phone,$email,$label,$city_id){
    global $DB;

    if ( empty($phone) ) {
        
        if ( $_POST['adm'] == "ok" ) {
            $email = trim($email);

            $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
            $res = $DB->GetLastID();

            return $res;
        } elseif ( !empty($_POST['certno']) ) {
            $email = trim($email);

            ### может такое быть что телефон не введет, но пользователь с таким email уже есть
            $DB->query_exec("SELECT id FROM clients WHERE clients.email LIKE '%{$email}%'");
            $chk_email = $DB->fetch();
            ### если email в базе нашелся (телефон пустой)
            if ( !empty($chk_email[0]) ) {
                ### тогда ничего больше не делаем
                ### получаем id пользователя
                return $chk_email[0]['id'];
            } else {
                ### если email нет
                ### создадим нового пользователя
                $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
                $res = $DB->GetLastID();

                return $res;
            }
        }


    }else{

    $phone = trim($phone);
    $email = trim($email);

    ### получаем телефон
    ### телефон должен записываться только цифры, без () и +
    ### показываться в таблице с разбивкой +d (ddd) ddd dd dd
    ### тоесть запись в бд только цифры
    $phone = substr(clean($phone),1); // вернет телефон без первой цифры

    ### если в таблице клиентов уже есть такой телефон
    $DB->query_exec("SELECT * FROM clients WHERE clients.phone LIKE '%{$phone}%'");
    $chk_phone = $DB->fetch();

    if ( !empty($chk_phone[0]) ) {
        ### телефон нашелся
        ### сравниваем email из базы и введенный пользователем
        $DB->query_exec("SELECT id FROM clients WHERE clients.email LIKE '%{$email}%'");
        $chk_email = $DB->fetch();
        if ( !empty($chk_email[0]) ) {
            ### если email тоже совпадает
            ### тогда ничего больше не делаем
            ### получаем id пользователя
            return $chk_phone[0]['id'];
        } else {
            ### если email не совпадает
            ### тогда добавим e-mail к существующему
            $additional_email = $chk_phone[0]['email'].'|'.$email;
            $DB->query_exec("UPDATE clients SET email = '{$additional_email}' WHERE id = {$chk_phone[0]['id']}");
            return $chk_phone[0]['id'];
        }
    }

    ### телефона в базе нет, посмотрим может есть email
    $DB->query_exec("SELECT * FROM clients WHERE clients.email LIKE '%{$email}%'");
    $chk_email = $DB->fetch();

    if ( !empty($chk_email[0]) ) {
        ### email нашелся
        ### то что телефон не совпадает мы уже знаем
        ### значит добавим введенный пользователем к существующему
        $additional_phone = $chk_email[0]['phone'].'|'.$phone;
        $DB->query_exec("UPDATE clients SET phone = '{$additional_phone}' WHERE id = {$chk_email[0]['id']}");
        return $chk_email[0]['id'];
    }

    // если совпадений нет
    // добавим нового клиента
    // но только цифры в телефон
    $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
    $DB->query_exec("SELECT id FROM clients WHERE clients.phone = {$phone}");
    
    $res = $DB->fetchsimple();

    return $res[0];
    } // phone is not empty
}

function regusers_check_client($name,$phone,$email,$label,$city_id){
    global $DB;

    if ( empty($phone) ) {
        
        if ( $_POST['adm'] == "ok" ) {
            $email = trim($email);

            $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
            $res = $DB->GetLastID();

            return $res;
        } elseif ( !empty($_POST['certno']) ) {
            $email = trim($email);

            ### может такое быть что телефон не введет, но пользователь с таким email уже есть
            $DB->query_exec("SELECT id FROM clients WHERE clients.email LIKE '%{$email}%'");
            $chk_email = $DB->fetch();
            ### если email в базе нашелся (телефон пустой)
            if ( !empty($chk_email[0]) ) {
                ### тогда ничего больше не делаем
                ### получаем id пользователя
                return $chk_email[0]['id'];
            } else {
                ### если email нет
                ### создадим нового пользователя
                $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
                $res = $DB->GetLastID();

                return $res;
            }
        } 


        ### телефона в базе нет, посмотрим может есть email
        $DB->query_exec("SELECT * FROM clients WHERE clients.email LIKE '%{$email}%'");
        $chk_email = $DB->fetch();

        if ( !empty($chk_email[0]) ) {
            ### email нашелся
            ### то что телефон не совпадает мы уже знаем
            ### значит добавим введенный пользователем к существующему
            $additional_phone = $chk_email[0]['phone'].'|'.$phone;
            // $DB->query_exec("UPDATE clients SET phone = '{$additional_phone}' WHERE id = {$chk_email[0]['id']}");
            return $chk_email[0]['id'];
        }

        // если совпадений нет
        // добавим нового клиента
        // но только цифры в телефон
        $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
        
        $res = $DB->GetLastID();

        return $res;      


    }else{

    $phone = trim($phone);
    $email = trim($email);

    ### получаем телефон
    ### телефон должен записываться только цифры, без () и +
    ### показываться в таблице с разбивкой +d (ddd) ddd dd dd
    ### тоесть запись в бд только цифры
    $phone = substr(clean($phone),1); // вернет телефон без первой цифры

    ### если в таблице клиентов уже есть такой телефон
    $DB->query_exec("SELECT * FROM clients WHERE clients.phone LIKE '%{$phone}%'");
    $chk_phone = $DB->fetch();

    if ( !empty($chk_phone[0]) ) {
        ### телефон нашелся
        ### сравниваем email из базы и введенный пользователем
        $DB->query_exec("SELECT id FROM clients WHERE clients.email LIKE '%{$email}%'");
        $chk_email = $DB->fetch();
        if ( !empty($chk_email[0]) ) {
            ### если email тоже совпадает
            ### тогда ничего больше не делаем
            ### получаем id пользователя
            return $chk_phone[0]['id'];
        } else {
            ### если email не совпадает
            ### тогда добавим e-mail к существующему
            $additional_email = $chk_phone[0]['email'].'|'.$email;
            // $DB->query_exec("UPDATE clients SET email = '{$additional_email}' WHERE id = {$chk_phone[0]['id']}");
            return $chk_phone[0]['id'];
        }
    }

    ### телефона в базе нет, посмотрим может есть email
    $DB->query_exec("SELECT * FROM clients WHERE clients.email LIKE '%{$email}%'");
    $chk_email = $DB->fetch();

    if ( !empty($chk_email[0]) ) {
        ### email нашелся
        ### то что телефон не совпадает мы уже знаем
        ### значит добавим введенный пользователем к существующему
        $additional_phone = $chk_email[0]['phone'].'|'.$phone;
        // $DB->query_exec("UPDATE clients SET phone = '{$additional_phone}' WHERE id = {$chk_email[0]['id']}");
        return $chk_email[0]['id'];
    }

    // если совпадений нет
    // добавим нового клиента
    // но только цифры в телефон
    $DB->query_exec("INSERT INTO clients(name,phone,email,label,city_id) VALUES ('{$name}', '{$phone}', '{$email}', '{$label}', '{$city_id}')");
    $DB->query_exec("SELECT id FROM clients WHERE clients.phone = {$phone}");
    
    $res = $DB->fetchsimple();

    return $res[0];
    } // phone is not empty
}


function add_comment($cli_id,$man_id,$comm_text){
    global $DB;

    $DB->query_exec("INSERT INTO comments(cli_id,man_id,comm_text) VALUES ('{$cli_id}', '{$man_id}', '{$comm_text}')");

    $DB->query_exec("SELECT MAX(id) FROM comments WHERE comments.cli_id = {$cli_id}");
    
    $res = $DB->fetchsimple();

    return $res[0];    


}

function get_by_id($field,$table,$id){
    global $DB;

    $DB->query_exec("SELECT {$field} FROM {$table} WHERE id = {$id}");
    $res = $DB->fetchsimple();
    $comment_id = $res[0];

    return $comment_id;
}
function get_by_ids($field,$table,$ids){
    global $DB;

    $where = "WHERE id IN (";

    foreach ($ids as $value) {
        $where .= $value.',';
    }

    $where = substr($where, 0, -1).")";

    $DB->query_exec("SELECT {$table}.{$field} FROM {$table} {$where}");
    $res = $DB->fetchsimple();
    return $res;
}

function check_zayavka($cli_id){
    global $DB;

    $DB->query_exec("SELECT id FROM zayavki WHERE cli_id = {$cli_id}");

    $z_id = $DB->fetchsimple();

    return $z_id[0];
}


function add_zayavka($requestData){
    global $DB;
    extract($requestData);

    $roistat_visit = $_COOKIE['roistat_visit'];

    $cli_id = check_client($name,$phone,$email,$cli_label,$city_id);

    //add comment
    // add_comment($cli_id,$man_id,$comm_text);
    if ( !empty($_COOKIE['user_id']) ) {
        $comm_id = add_comment($cli_id, $_COOKIE['user_id'], $comm_text);
        $man_id = $_COOKIE['user_id'];
    } elseif ( $requestData['man_id'] == 9 ) {
       $comm_id = add_comment($cli_id, $requestData['man_id'], $comm_text);
       $query_updt_commId = " comment_id = '{$comm_id}',";
       $man_id = $requestData['man_id'];
    }

    if ($etap_source == '12') {
        $order_status = "Что внутри";
    } else {
        $order_status = "Сформирован";
    }

    if ( isset($order_no) ) {
        $DB->query_exec("INSERT INTO orders (status,email,name,order_no,cli_id,roistat) VALUES ('{$order_status}','{$email}','{$name}','{$order_no}','{$cli_id}','{$roistat_visit}')");
    } else {
        $order_no = NULL;
    }

    // if (!isset($box_type)) {
    //     $box_type = 0;
    // }

    // вот тут: если в таблице zayavki уже есть запись с cli_id = $cli_id 
    // тогда надо эту запись обновить (последнее состояние)
    // если таких записей нет - тогда надо создать
    $z_id = check_zayavka($cli_id);

    if ( empty($z_id) ) {
        // если это регистрация сертификата, то надо обновить таблицу regusers
        if (!empty($_POST['certno'])) {
            $etap = 19;
            $_POST['certno'] = trim($_POST['certno']);
            $DB->query_exec("INSERT INTO zayavki(cli_id,etap,etap_source,city_id,svyaz,comment_id,quantity,box_name,box_type,delivery,order_no) VALUES ('{$cli_id}', '{$etap}', '{$etap_source}', '{$city}', '{$svyaz}', '{$comm_id}', '{$quantity}', '{$box_name}', '{$box_type}', '{$delivery}', '{$order_no}')");
            $DB->query_exec("INSERT INTO history(cli_id,certno,referer) VALUES ('{$cli_id}', '{$_POST['certno']}', '{$_COOKIE["origin_ref"]}')");

            hasRegusersCert($_POST['certno']);
            $DB->query_exec("INSERT INTO regusers(certno,name,email,phone,cc,cli_id) VALUES ('{$_POST['certno']}', '{$_POST['name']}', '{$_POST['email']}', '{$_POST['phone']}', '{$_POST['cc']}', {$cli_id})");

        } else {
            $DB->query_exec("INSERT INTO zayavki(cli_id,etap,etap_source,city_id,svyaz,comment_id,quantity,box_name,box_type,delivery,order_no) VALUES ('{$cli_id}', '{$etap}', '{$etap_source}', '{$city}', '{$svyaz}', '{$comm_id}', '{$quantity}', '{$box_name}', '{$box_type}', '{$delivery}', '{$order_no}')");

            $DB->query_exec("INSERT INTO history(cli_id,man_id,etap,etap_source,city_id,svyaz,comment_id,quantity,box_name,box_type,delivery,order_no,referer) VALUES ('{$cli_id}', '{$_COOKIE['user_id']}', '{$etap}', '{$etap_source}', '{$city}', '{$svyaz}', '{$comm_id}', '{$quantity}', '{$box_name}', '{$box_type}', '{$delivery}', '{$order_no}', '{$_COOKIE["origin_ref"]}')");
        }


    } else {

        $z_date = date("Y-m-d H:i:s");

        if (!empty($_POST['certno'])) {

            $_POST['certno'] = trim($_POST['certno']);
            $DB->query_exec("UPDATE zayavki SET z_date = '{$z_date}' WHERE id = {$z_id}");
            $DB->query_exec("INSERT INTO history(h_date,cli_id,certno,referer) VALUES ('{$z_date}', '{$cli_id}', '{$_POST['certno']}', '{$_COOKIE["origin_ref"]}')");

            hasRegusersCert($_POST['certno']);
            $DB->query_exec("INSERT INTO regusers(certno,name,email,phone,cc,cli_id) VALUES ('{$_POST['certno']}', '{$_POST['name']}', '{$_POST['email']}', '{$_POST['phone']}', '{$_POST['cc']}', {$cli_id})");

        } else {

            $DB->query_exec("UPDATE zayavki SET z_date = '{$z_date}', etap = '{$etap}', etap_source = '{$etap_source}', svyaz = '{$svyaz}',".$query_updt_commId." quantity = '{$quantity}', box_name = '{$box_name}', box_type = '{$box_type}', order_no = '{$order_no}' WHERE id = {$z_id}");

            $DB->query_exec("INSERT INTO history(h_date,cli_id,man_id,etap,etap_source,city_id,svyaz,comment_id,quantity,box_name,box_type,delivery,order_no,referer) VALUES ('{$z_date}', '{$cli_id}', '{$man_id}', '{$etap}', '{$etap_source}', '{$city}', '{$svyaz}', '{$comm_id}', '{$quantity}', '{$box_name}', '{$box_type}', '{$delivery}', '{$order_no}', '{$_COOKIE["origin_ref"]}')");
            
        }


    }// проверка новая или существующая заявка

}
function reguser_add_zayavka($requestData){
    global $DB;
    extract($requestData);


    $cli_id = regusers_check_client($name,$phone,$email,$cli_label,$city_id);

    //add comment
    // add_comment($cli_id,$man_id,$comm_text);
    if ( !empty($_COOKIE['user_id']) ) {
        $comm_id = add_comment($cli_id, $_COOKIE['user_id'], $comm_text);
        $man_id = $_COOKIE['user_id'];
    } elseif ( $requestData['man_id'] == 9 ) {
       $comm_id = add_comment($cli_id, $requestData['man_id'], $comm_text);
       $query_updt_commId = " comment_id = '{$comm_id}',";
       $man_id = $requestData['man_id'];
    }


    if ( isset($order_no) ) {
        $DB->query_exec("INSERT INTO orders (email,name,order_no,cli_id) VALUES ('{$email}','{$name}','{$order_no}','{$cli_id}')");
    } else {
        $order_no = NULL;
    }

    // if (!isset($box_type)) {
    //     $box_type = 0;
    // }

    // вот тут: если в таблице zayavki уже есть запись с cli_id = $cli_id 
    // тогда надо эту запись обновить (последнее состояние)
    // если таких записей нет - тогда надо создать
    $z_id = check_zayavka($cli_id);

    if ( empty($z_id) ) {
        // if (!empty($certno)) {
        //     $etap = 19;
        //     $z_date = $requestData['reg_time'];
        //     $certno = trim($certno);

        //     $DB->query_exec("INSERT INTO zayavki(z_date,cli_id,etap,etap_source,city_id,svyaz,comment_id,quantity,box_name,box_type,delivery,order_no) VALUES ('{$z_date}', '{$cli_id}', '{$etap}', '{$etap_source}', '{$city}', '{$svyaz}', '{$comm_id}', '{$quantity}', '{$box_name}', '{$box_type}', '{$delivery}', '{$order_no}')");

        //     $DB->query_exec("INSERT INTO history(h_date,cli_id,certno) VALUES ('{$z_date}', '{$cli_id}', '{$certno}')");

        // }

    } else {

        // $z_date = date("Y-m-d H:i:s");
        $z_date = $requestData['reg_time'];

        if (!empty($certno)) {

            $certno = trim($certno);
            // $DB->query_exec("UPDATE zayavki SET z_date = '{$z_date}' WHERE id = {$z_id}");
            $DB->query_exec("SELECT id from history where cli_id = {$cli_id} AND certno = '{$certno}';");
            $ex_cert = $DB->fetch();

            if ( !empty($ex_cert) ) {
                # code...
            } else {
                $DB->query_exec("INSERT INTO history(h_date,cli_id,certno) VALUES ('{$z_date}', '{$cli_id}', '{$certno}')");
            }


        }


    }// проверка новая или существующая заявка

}
function update_order_zayavka($etap,$order_no){
    global $DB;

    $z_date = date("Y-m-d H:i:s");

    $DB->query_exec("UPDATE zayavki SET z_date = '{$z_date}', etap = '{$etap}', etap_source = NULL WHERE order_no = '{$order_no}'");


    $DB->query_exec("SELECT cli_id FROM zayavki WHERE order_no = '{$order_no}'");
    $cli_id_arr = $DB->fetchsimple();
    $cli_id = $cli_id_arr[0];
    $DB->query_exec("INSERT INTO history(h_date,cli_id,man_id,etap) VALUES ('{$z_date}', '{$cli_id}', '{$_COOKIE['user_id']}', '{$etap}')");    

}

function update_qtity_zayavka($cli_id){
    global $DB;

    $z_date = date("Y-m-d H:i:s");

    $DB->query_exec("UPDATE zayavki SET z_date = '{$z_date}', etap = CASE WHEN quantity != 'подписка' THEN '9' ELSE '18' END, etap_source = NULL WHERE cli_id = '{$cli_id}'");

    $DB->query_exec("SELECT etap FROM zayavki WHERE cli_id = '{$cli_id}'; ");
    $cli_id_arr = $DB->fetchsimple();
    $etap = $cli_id_arr[0];


    $DB->query_exec("INSERT INTO history(h_date,cli_id,man_id,etap) VALUES ('{$z_date}', '{$cli_id}', '0', '{$etap}')");

}

function update_sform_zakaz($zakaz_id){
    global $DB;

    $DB->query_exec("UPDATE sformirovanye_zakazy SET s_vydacha = 'по маркировке' WHERE id = '{$zakaz_id}'");

}




function manager_update_zayavka_etap($etap_field,$etap_id,$id){
    global $DB;

    // обновим значение поля этап
    // в таблице заявок
    $DB->query_exec("UPDATE zayavki SET {$etap_field} = '{$etap_id}' WHERE id = '{$id}'");


    if ( $etap_id == 3 ) {
        // получим order_no по id заявки 
        // чтобы по нему потом обновить статус заказа
        $DB->query_exec("SELECT order_no FROM zayavki WHERE id = '{$id}'");
        $order_no_arr = $DB->fetch();
        $order_no = $order_no_arr[0]['order_no'];

        if ( strstr($order_no,'what_inside') ) {
            $DB->query_exec("UPDATE orders SET status = 'Допродажа' WHERE order_no = '{$order_no}'");
        }

    }





}
function manager_update_client_city($etap_field,$etap_id,$id){
    global $DB;

    $DB->query_exec("UPDATE clients SET {$etap_field} = '{$etap_id}' WHERE id = '{$id}'");

}
function manager_update_zayavka_comment($cli_id,$man_id,$comm_text,$z_id){
    global $DB;

    $DB->query_exec("INSERT INTO comments(cli_id,man_id,comm_text) VALUES ('{$cli_id}', '{$man_id}', '{$comm_text}')");
    $DB->query_exec("SELECT MAX(id) FROM comments WHERE comments.cli_id = {$cli_id}");

    $res = $DB->fetchsimple();
    $comment_id = $res[0];

    $DB->query_exec("UPDATE zayavki SET comment_id = '{$comment_id}' WHERE id = '{$z_id}'");
    $DB->query_exec("INSERT INTO history(cli_id,man_id,comment_id) VALUES ('{$cli_id}', '{$man_id}','{$comment_id}')");
}


function history_etap($etap_field,$etap_id,$cli_id,$man_id){
    global $DB;

    $DB->query_exec("INSERT INTO history({$etap_field},cli_id,man_id) VALUES ('{$etap_id}', '{$cli_id}', '{$man_id}')");
    
}


function save_compiled_order($data){
    global $DB;

    $res['cli_id'] = $data['ajxCliId'];

    switch ($data['ajxBoxType']) {
        case 'сертификат':
            $box_type_id = 3;
            break;
        case 'Электронная':
            $box_type_id = 2;
            break;
        case 'Физическая':
            $box_type_id = 1;
            break;
        case 'никакая':
            $box_type_id = 0;
            break;
        
        default:
            # code...
            break;
    }

    ### проверка доступности сертификатов
    if ( $data['ajxMark'] != "" || ($data['ajxOrderId'] != "" && $data['ajxOrderId'] != "undefined") ) {

        
        ### по номеру заказа получим маркировку
        if ( $data['ajxOrderId'] != "" ) {
            $DB->query_exec("SELECT complect FROM sformirovanye_zakazy WHERE id = {$data['ajxOrderId']}");
            $ajxMark = $DB->fetch();
            if ($data['ajxMark']=="") {
                $data['ajxMark'] = $ajxMark[0]['complect'];
            }
        }

        if ( $data['ajxMark'] != "" ) {
            $codes_arr = explode(':',$data['ajxMark']);
            $codes_arr = $codes_arr[1];
            $codes_arr = explode(',', $codes_arr);
        }

        $error_qtity = array();

        if (isset($codes_arr)) {
            foreach ($codes_arr as $key => $code_desc) {
                $used = get_qtity_used_certs($code_desc,$box_type_id+1); // тут уже с учетом типа коробочки $used['qtity']

                $DB->query_exec("SELECT month_qtity_el, month_qtity_fiz FROM letters WHERE letters.desc = '{$code_desc}'");
                $vsegoMozhno = $DB->fetch(); // $vsegoMozhno[0]['month_qtity_el']||$vsegoMozhno[0]['month_qtity_fiz']

                switch ($box_type_id) {
                    case '0':
                        # code...
                        break;
                    case '1':
                        // физическая
                        if ( $vsegoMozhno[0]['month_qtity_fiz'] <= $used['qtity'] ) {
                            # code...
                            $error_qtity[$code_desc] = "нельзя";
                        }
                        break;
                    case '2':
                        // электронная
                        if ( $vsegoMozhno[0]['month_qtity_el'] <= $used['qtity'] ) {
                            # code...
                            $error_qtity[$code_desc] = "нельзя";
                        }


                        break;
                    case '3':
                        # code...
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }


        if ( !empty($error_qtity) ) {
            $errors_codes = '';
            foreach ($error_qtity as $descr => $descr_text) {
                $errors_codes .= $descr.' '.$descr_text."\n";
            }
            $res['error'] = "Нельзя добавить этот компелкт.\n".$errors_codes;
            return $res;
        }

    // $used_all = get_qtity_used_certs($partnerCodes[$i]['desc'],0);
    // $used_none = get_qtity_used_certs($partnerCodes[$i]['desc'],1);
    // $used_fiz = get_qtity_used_certs($partnerCodes[$i]['desc'],2);
    // $used_el = get_qtity_used_certs($partnerCodes[$i]['desc'],3);
    // $used_cert = get_qtity_used_certs($partnerCodes[$i]['desc'],4);

    }


    $month_no = 20;

    if ($data['ajxOrderId']=='undefined') {
        if ( !empty($_COOKIE['user_id']) ) {

            $DB->query_exec("INSERT INTO sformirovanye_zakazy(cli_id,man_id,s_price,box_name,box_type,complect,s_vydacha,comment,month_no)
                            VALUES ({$data['ajxCliId']},{$_COOKIE['user_id']},'{$data['ajxPrice']}','{$data['ajxBox']}','{$box_type_id}','{$data['ajxMark']}','{$data['ajxVydacha']}','{$data['ajxComment']}',{$month_no})");
            $res['zakaz_id'] = $DB->GetLastID();
        }
    }else{

        if ( !empty($_COOKIE['user_id']) ) {

            if ( $data['ajxMark'] != "" ) {
                $DB->query_exec("UPDATE sformirovanye_zakazy SET man_id = {$_COOKIE['user_id']}, s_price = '{$data['ajxPrice']}', box_name = '{$data['ajxBox']}', box_type = '{$box_type_id}', complect = '{$data['ajxMark']}', s_vydacha = '{$data['ajxVydacha']}', comment = '{$data['ajxComment']}' WHERE id = {$data['ajxOrderId']}");
            } else {
                $DB->query_exec("UPDATE sformirovanye_zakazy SET man_id = {$_COOKIE['user_id']}, s_price = '{$data['ajxPrice']}', box_name = '{$data['ajxBox']}', box_type = '{$box_type_id}', s_vydacha = '{$data['ajxVydacha']}', comment = '{$data['ajxComment']}' WHERE id = {$data['ajxOrderId']}");
            }


            $res['zakaz_id'] = $data['ajxOrderId'];

        }        

    }


    return $res;
}
function save_compiled_order_new($data){
    global $DB;

    $res['cli_id'] = $data['ajxCliId'];

    switch ($data['ajxBoxType']) {
        case 'сертификат':
            $box_type_id = 3;
            break;
        case 'Электронная':
            $box_type_id = 2;
            break;
        case 'Физическая':
            $box_type_id = 1;
            break;
        case 'никакая':
            $box_type_id = 0;
            break;
        
        default:
            # code...
            break;
    }

    $month_no = 20;

    if ($data['ajxOrderId']=='undefined') {
        if ( !empty($_COOKIE['user_id']) ) {

            $DB->query_exec("INSERT INTO sformirovanye_zakazy(cli_id,man_id,s_price,box_name,box_type,complect,s_vydacha,comment,month_no)
                            VALUES ({$data['ajxCliId']},{$_COOKIE['user_id']},'{$data['ajxPrice']}','{$data['ajxBox']}','{$box_type_id}','{$data['ajxMark']}','{$data['ajxVydacha']}','{$data['ajxComment']}',{$month_no})");
            $res['zakaz_id'] = $DB->GetLastID();
        }
    }else{

        if ( !empty($_COOKIE['user_id']) ) {

            if ( $data['ajxMark'] != "" ) {
                $DB->query_exec("UPDATE sformirovanye_zakazy SET man_id = {$_COOKIE['user_id']}, s_price = '{$data['ajxPrice']}', box_name = '{$data['ajxBox']}', box_type = '{$box_type_id}', complect = '{$data['ajxMark']}', s_vydacha = '{$data['ajxVydacha']}', comment = '{$data['ajxComment']}' WHERE id = {$data['ajxOrderId']}");
            } else {
                $DB->query_exec("UPDATE sformirovanye_zakazy SET man_id = {$_COOKIE['user_id']}, s_price = '{$data['ajxPrice']}', box_name = '{$data['ajxBox']}', box_type = '{$box_type_id}', s_vydacha = '{$data['ajxVydacha']}', comment = '{$data['ajxComment']}' WHERE id = {$data['ajxOrderId']}");
            }


            $res['zakaz_id'] = $data['ajxOrderId'];

        }        

    }


    return $res;
}


function save_options($data){
    global $DB;


    if ($data['ajxOptionId']=='undefined') {
        // $DB->query_exec("INSERT INTO options(cli_id,man_id,s_price,box_name,box_type,complect,s_vydacha,comment)
        //                 VALUES ({$data['ajxCliId']},{$_COOKIE['user_id']},'{$data['ajxPrice']}','{$data['ajxBox']}','{$box_type_id}','{$data['ajxMark']}','{$data['ajxVydacha']}','{$data['ajxComment']}')");
        // $res = $DB->GetLastID();
    }else{
        $DB->query_exec("UPDATE options SET options.values = '{$data['ajxValues']}' WHERE id = {$data['ajxOptionId']}");
        
        $res = $DB->GetLastID();
    }


    return $res;
}
function save_boxes($data){
    global $DB;


    if ($data['ajxBoxId']=='undefined') {
        $DB->query_exec("INSERT INTO boxes(name,send_date,box_prefix)
                        VALUES ('{$data['ajxName']}','{$data['ajxSendDate']}','{$data['ajxBoxPrefix']}')");
        $res = $DB->GetLastID();
    }else{
        $DB->query_exec("UPDATE boxes SET send_date = '{$data['ajxSendDate']}', box_prefix = '{$data['ajxBoxPrefix']}' WHERE id = {$data['ajxBoxId']}");
        
        $res = $data['ajxBoxId'];
    }


    return $res;
}
function get_history_table($cli_id){
    global $DB;

    $DB->query_exec("SELECT * FROM history WHERE history.cli_id = {$cli_id} ORDER BY h_date DESC");
    $res = $DB->fetch();

    $table = '<table><thead><tr> <td>Время изменения</td> <td>Менеджер</td> <td>Этап</td> <td>Источник (этапа)</td> <td>Связь</td> <td style="width:20%">Комментарий</td> <td>Количество</td> <td>Коробочка</td> <td>Тип</td> <td>Доставка</td><td>Дата доставки</td><td>Откуда пришли</td><td>Номер Сертификата</td></tr></thead><tbody>';
    foreach ($res as $row) {
        $table .= '<tr>';
        foreach ($row as $col => $col_value) {
            switch ($col) {
                case 'h_date':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'man_id':
                    $col_value = get_by_id('name','managers',$col_value);
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'etap':
                    $col_value = get_by_id('etap','etap',$col_value);
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'etap_source':
                    $col_value = get_by_id('etap','etap',$col_value);
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'svyaz':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'comment_id':
                    $col_value = get_by_id('comm_text','comments',$col_value);
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'quantity':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'box_name':
                    $col_value = get_by_id('name','boxes',$col_value);
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'box_type':
                    $box_type_label = get_boxtype_arr();
                    $col_value = $box_type_label[$col_value];
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'delivery':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'delivery_date':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'referer':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                case 'certno':
                    $table .= '<td>'.$col_value.'</td>';
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        $table .= '</tr>';
    }
    $table .= '</tbody></table>';

    return $table;
}
function is_zayavka_new($cli_id){
    global $DB;
    $DB->query_exec("SELECT max(z_date) FROM zayavki WHERE cli_id = {$cli_id}");
    $last_z_date = $DB->fetchsimple();
    $DB->query_exec("SELECT max(h_date) FROM history WHERE cli_id = {$cli_id}");
    $last_h_date = $DB->fetchsimple();

    if ( $last_h_date[0] > $last_z_date[0]) {
        return false;
    } else {
        return true;
    }
}


function get_etap_label($etap_id){
    global $DB;

    $DB->query_exec("SELECT etap FROM etap WHERE id = $etap_id");

    $arr = $DB->fetch();

    return $arr[0]['etap'];
    
}
function get_etap_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM etap WHERE level = 0");

    $arr = $DB->fetch();

    $res = array();

    foreach ($arr as $etap) {
        $res[$etap['id']] = $etap['etap'];
    }

    return $res;
}
function get_etap_source_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM etap WHERE level = 1");

    $arr = $DB->fetch();

    $res = array();

    foreach ($arr as $etap) {
        $res[$etap['id']] = $etap['etap'];
    }

    return $res;    
}
function get_svyaz_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.`option` = 'svyaz'");

    $arr = $DB->fetch();

    $arr = explode('|',$arr[0]['values']);

    $res = array();

    foreach ($arr as $svyaz) {
        $res[$svyaz] = $svyaz;
    }

    return $res;    
}
function get_delivery_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.`option` = 'delivery'");
    $arr = $DB->fetch();

    $arr = explode('|',$arr[0]['values']);
    $res = array();

    foreach ($arr as $svyaz) {
        $res[$svyaz] = $svyaz;
    }

    return $res;    
}
function get_boxtype_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.`option` = 'box_type'");
    $arr = $DB->fetch();

    $arr = explode('|',$arr[0]['values']);
    $res = array();

    foreach ($arr as $svyaz) {
        $res[] = $svyaz;
    }

    return $res;    
}
function get_quantity_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM options WHERE options.`option` = 'quantity'");
    $arr = $DB->fetch();

    $arr = explode('|',$arr[0]['values']);
    $res = array();

    foreach ($arr as $svyaz) {
        $res[$svyaz] = $svyaz;
    }

    return $res;    
}
function get_box_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM boxes");
    $arr = $DB->fetch();
    $res = array();
    foreach ($arr as $box) {
        $res[$box['id']] = $box['name'];
    }

    return $res;    
}
function get_cities_arr(){
    global $DB;

    $DB->query_exec("SELECT * FROM cities");
    $arr = $DB->fetch();
    $res = array();
    foreach ($arr as $box) {
        $res[$box['id']] = $box['city_label'];
    }

    return $res;    
}




?>