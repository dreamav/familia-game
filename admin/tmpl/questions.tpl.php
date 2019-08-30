<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-shopping-cart"></i>Список вопросов
                </div>
                <div class="actions">
                    <a href="<?=$_SERVER['PHP_SELF']?>?action=add_question" class="btn btn-circle btn-info" id="add_question_button">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-xs"> Новый вопрос </span>
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span> </span>
                        <select class="table-group-action-input form-control input-inline input-small input-sm">
                            <option value="">Select...</option>
                            <option value="publish">Publish</option>
                            <option value="unpublished">Un-publish</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-success table-group-action-submit">
                            <i class="fa fa-check"></i> Submit</button>
                    </div>
                    <table class="table table-striped table-bordered" id="datatable_cli">
                        <thead>
                            <tr role="row" class="heading">
                                <th> id </th>
                                <th> Заголовок </th>
                                <th> Системное имя </th>
                                <th> Видимость </th>
                                <th> Жесткость </th>
                                <th> Номер </th>
                                <th> Категория </th>
                                <th> Тип </th>
                                <th> Действия </th>
                            </tr>
                            <tr role="row" class="filter">
                                <td><input type="text" class="form-control form-filter input-sm" name="id"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="q_title"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="sys_name"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="q_visibility"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="q_hard"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="q_nomer"></td>
                                <td><input type="text" class="form-control form-filter input-sm" name="q_category"></td>
                                <td><?=gen_select("types","q_type","form-filter")?></td>
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-sm green btn-outline filter-submit margin-bottom">
                                            <i class="fa fa-search"></i> Поиск</button>
                                    </div>
                                    <button class="btn btn-sm red btn-outline filter-cancel">
                                        <i class="fa fa-times"></i> Сбросить</button>
                                </td>                                
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<script>
var cliAjax = function () {

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: App.isRTL(),
            autoclose: true,
            language: 'ru-ru'
        });
    }

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#datatable_cli"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error  
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
                FormEditable.init();
            },
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js). 
                // So when dropdowns used the scrollable div should be removed. 
                // "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.   

                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 50, // default record count per page
                "ajax": {
                    "url": "/admin/tmpl/assets/questions_ajax.php", // ajax source
                },
                "order": [
                    [1, "des"]
                ]// set first column as a default sort by asc
            }
        });

        // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Please select an action',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        //grid.setAjaxParam("customActionType", "group_action");
        //grid.getDataTable().ajax.reload();
        //grid.clearAjaxParams();
    }

    return {

        //main function to initiate the module
        init: function () {

            initPickers();
            handleRecords();
        }

    };

}();


jQuery(document).ready(function($) {

cliAjax.init();

$('#add_question_button').click(function(event) {
    event.preventDefault();

    $.ajax({
        url: '/admin/index.php?action=add_question',
        type: 'post',
        dataType: 'json'
    })
    .done(function(data) {
        console.log(data);

        var edit_button = $(
        '<a href="<?=$_SERVER['PHP_SELF']?>?action=edit_question&qid='+data.q_id+'" class="btn btn-circle btn-info" id="edit_question_button">'+
        '    <i class="fa fa-edit"></i>'+
        '    <span class="hidden-xs"> Редактировать созданный вопрос </span>'+
        '</a>'
        ).appendTo($('.portlet-title .actions'));

    })
    .fail(function() {
        console.log("error");
    });
    
});

});    
</script>