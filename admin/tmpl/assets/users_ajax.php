<?php
/*
 * Paging
 */
require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fam_functions.php';


$where = array();

$users = get_users($where);
$users = $users['users'];

$iTotalRecords = count($users);
$iDisplayLength = intval($_REQUEST['length']);
$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
$iDisplayStart = intval($_REQUEST['start']);
$sEcho = intval($_REQUEST['draw']);

$records = array();
$records["data"] = array();

$end = $iDisplayStart + $iDisplayLength;
$end = $end > $iTotalRecords ? $iTotalRecords : $end;

$status_list = array(
    array("success" => "Pending"),
    array("info" => "Closed"),
    array("danger" => "On Hold"),
    array("warning" => "Fraud")
);


for ($i = $iDisplayStart; $i < $end; $i++) {


    $records["data"][] = array(
        "DT_RowId" => 'q_id_'.$users[$i]['id'],
        $users[$i]['id'],
        $users[$i]['name'],
        $users[$i]['email'],
        $users[$i]['sex'],
        $users[$i]['provider'],
        $users[$i]['user_level'],
        $users[$i]['total_score'],
        '<div class="btn-group"> <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Действия <i class="fa fa-angle-down"></i> </button> <ul class="dropdown-menu" role="menu"> <li> <a href="index.php?action=edit_user&uid='.$users[$i]["id"].'"> <i class="icon-docs"></i> Редактировать пользователя </a> </li> </ul> </div>'
    );
}

if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
}

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);
?>