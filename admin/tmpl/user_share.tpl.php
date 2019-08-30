<div class="row">
    <div class="col-md-12">

		<form class="form-horizontal form-row-seperated" data-qid="<?=$data['id']?>">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="actions btn-set mr-l-0">
                        <button class="btn btn-success" id="save_button">
                            <i class="fa fa-check-circle"></i> Save & Continue Edit
                        </button>
                    </div>
                </div>
                <div class="portlet-body">

                     <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> id вопроса </th>
                                    <th> уровень </th>
                                    <th> Начисленные баллы </th>
                                    <th> Время </th>
                                    <th> в какой соц сети </th>
                                </tr>
                            </thead>
                            <tbody>

                            <? foreach ($data as $cert) { ?>
                                
                                <tr>
                                    <td> <?=$cert['id']?> </td>
                                    <td> <?=$cert['q_id']?> </td>
                                    <td> <?=$cert['level']?> </td>
                                    <td> <?=$cert['score']?> </td>
                                    <td> <?=$cert['created']?> </td>
                                    <td> <?=$cert['provider']?> </td>
                                </tr>
                                
                            <? } ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>