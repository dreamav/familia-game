<?php
/*
 * Paging
 */
require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fam_functions.php';


$where = array();

$questions = get_game_levels($where);
$questions = $questions['game_levels'];

$iTotalRecords = count($questions);
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
        "DT_RowId" => 'q_id_'.$questions[$i]['id'],
        $questions[$i]['id'],
        $questions[$i]['level'],
        $questions[$i]['points'],
        $questions[$i]['name'],
        '<div class="btn-group"> <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Действия <i class="fa fa-angle-down"></i> </button> <ul class="dropdown-menu" role="menu"> <li> <a href="index.php?action=edit_game_level&qid='.$questions[$i]["id"].'"> <i class="icon-docs"></i> Редактировать уровень </a> </li> <li> <a href="index.php?action=del_game_level&qid='.$questions[$i]["id"].'"> <i class="icon-docs"></i> Удалить уровень </a> </li> </ul> </div>'
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