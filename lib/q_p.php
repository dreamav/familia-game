<?


require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fe_func.php';


$DB = new c_database();
$DB->iniSet();
$DB->connect();

	$DB->query_exec("UPDATE gameplay SET q_p = 1 WHERE q_id NOT IN (SELECT id FROM questions WHERE parent_id IS NOT NULL)");

echo "done";


?>