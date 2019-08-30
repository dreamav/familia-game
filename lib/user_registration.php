<?

die('not now');

require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fe_func.php';


$DB = new c_database();
$DB->iniSet();
$DB->connect();


/*$DB->query_exec("SELECT * FROM users WHERE u_registration = '0000-00-00' AND id BETWEEN 10001 and 10744");
$users = $DB->fetch();


foreach ( $users as $key => $user ) {
	
	$DB->query_exec("SELECT MIN(created) as created FROM gameplay WHERE u_id = {$user['id']}");
	$user_first_act = $DB->fetch();
	$user_first_act = $user_first_act[0]['created'];

	$DB->query_exec("UPDATE users SET u_registration = '{$user_first_act}' WHERE id = {$user['id']}");

}*/

$DB->query_exec("SELECT * FROM users");
$users = $DB->fetch();


foreach ( $users as $key => $user ) {
	
	$DB->query_exec("SELECT SUM(score) as gp_score FROM gameplay where u_id = {$user['id']} AND q_p = 1");
	$user_first_act = $DB->fetch();
	$user_first_act = $user_first_act[0]['gp_score'];

	$DB->query_exec("UPDATE users SET earned_points = {$user_first_act} WHERE id = {$user['id']}");

}




echo "done";




