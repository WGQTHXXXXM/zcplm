<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Department */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<br>
<br>
<br>
<div class="department-view">

    <div class="row">
        <div class="col-sm-5">
            <input class="form-control search" data-target="avaliable"
                   placeholder="<?= Yii::t('rbac-admin', 'Search for avaliable') ?>">
            <select multiple size="20" class="form-control list" data-target="avaliable"></select>
        </div>
        <div class="col-sm-1">
            <br><br>
            <?= Html::a('&gt;&gt; <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>',
                [
                    'assign', 'id' => $model->id
                ],
                [
                'class' => 'btn btn-success btn-assign',
                'data-target' => 'avaliable',
                'title' => Yii::t('rbac-admin', 'Assign')
            ]) ?><br><br>
            <?= Html::a('&lt;&lt; <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>',
                [
                    'remove', 'id' => $model->id
                ],
                [
                'class' => 'btn btn-danger btn-assign',
                'data-target' => 'assigned',
                'title' => Yii::t('rbac-admin', 'Remove')
            ]) ?>
        </div>
        <div class="col-sm-5">
            <input class="form-control search" data-target="assigned"
                   placeholder="<?= Yii::t('rbac-admin', 'Search for assigned') ?>">
            <select multiple size="20" class="form-control list" data-target="assigned"></select>
        </div>
    </div>

</div>

<?php

$js = <<<JS
var dataUser = {$dataUser};

$('i.glyphicon-refresh-animate').hide();
function updateItems(r) {
    dataUser.avaliable = r.avaliable;
    dataUser.assigned = r.assigned;
    search('avaliable');
    search('assigned');
}

//分配和移除按键
$('.btn-assign').click(function () {
    var thisBtn = $(this);
    var target = thisBtn.data('target');
    var items = $('select.list[data-target="' + target + '"]').val();

    if (items && items.length) {
        thisBtn.children('i.glyphicon-refresh-animate').show();
        $.post(thisBtn.attr('href'), {items: items}, function (json) {
            if(json.status == 0)
                alert(json.message);
            else{
                updateItems(json.data);
            }
           
        },'json').always(function () {
            thisBtn.children('i.glyphicon-refresh-animate').hide();
        });
    }
    return false;
});

//键盘按下时搜索
$('.search[data-target]').keyup(function () {
    search($(this).data('target'));
});

//过滤的方法
function search(target) {
    var list = $('select.list[data-target="' + target + '"]');
    list.html('');
    var q = $('.search[data-target="' + target + '"]').val();

    $.each(dataUser[target], function (key, val) {
        if (val.indexOf(q) >= 0) {//如果这个值里有输入的就添加。
            $('<option>').text(val).val(key).appendTo(list);
        }
    });
}
// initial
search('avaliable');
search('assigned');



JS;

$this->registerJs($js);

?>