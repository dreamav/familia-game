<?php
/*
 * Paging
 */
require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fam_functions.php';

$where = array();

/*if (!empty($_POST['id'])) {
    $where['id'] = $_POST['id'];
}
if (!empty($_POST['q_title']) && $_POST['q_title'] != "") {
    $where['q_title'] = $_POST['q_title'];
}
if (!empty($_POST['sys_name'])) {
    $where['sys_name'] = $_POST['sys_name'];
}
if (!empty($_POST['q_visibility'])) {
    $where['q_visibility'] = $_POST['q_visibility'];
}
if (!empty($_POST['q_hard'])) {
    $where['q_hard'] = $_POST['q_hard'];
}
if (!empty($_POST['q_nomer'])) {
    $where['q_nomer'] = $_POST['q_nomer'];
}
if (!empty($_POST['q_category'])) {
    $where['q_category'] = $_POST['q_category'];
}
if (!empty($_POST['q_type'])) {
    $where['q_type'] = $_POST['q_type'];
}*/

$questions = get_manual_check($where);
$questions = $questions['questions'];

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

    if ( $questions[$i]['m_approved'] == 0 ) {
        $add_points_link = '<li> <a href="index.php?action=admin_add_points&qid='.$questions[$i]["q_id"].'&uid='.$questions[$i]['u_id'].'&gpid='.$questions[$i]['id'].'"> <i class="icon-docs"></i> Добавить баллов </a> </li>';
    } else {
        $add_points_link = '<li> Баллы уже добавлены </li>';
    }

    $records["data"][] = array(
        "DT_RowId" => 'q_id_'.$questions[$i]['id'],
        $questions[$i]['id'],
        $questions[$i]['created'],
        $questions[$i]['u_id']." ".$questions[$i]['name'].' '.$questions[$i]['total_score'],
        $questions[$i]['q_id'],
        '<div style="width:300px;word-wrap: break-word;">'.$questions[$i]['answer'].'</div>',
        $questions[$i]['m_approved'],
        '<div class="btn-group"> <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Действия <i class="fa fa-angle-down"></i> </button> <ul class="dropdown-menu" role="menu"> '.$add_points_link.' </ul> </div>'
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