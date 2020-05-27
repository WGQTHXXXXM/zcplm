<div class="row">
    <div class="col-md-5"></div>
    <div class="col-md-7">
        <?php
        use kartik\editable\Editable;
        use frontend\models\UserTask;
        use frontend\models\Tasks;
        use kartik\popover\PopoverX;
        //说明是从审批清单的链接过来的并且UserTask模型不空还得是待审批的
        //if(isset($_SERVER['HTTP_REFERER'])&&strpos($_SERVER['HTTP_REFERER'],'user-task/index')&& !empty($mdlUserTask))
        $ableApprove = false;
        if(!empty($mdlUserTask)){
            $arrTemp = UserTask::find()->where(['task_id'=>$mdlUserTask->task_id])->select('user_id')->column();
            if(array_search(Yii::$app->user->id,$arrTemp)!==false)
                $ableApprove = true;
        }
        if($ableApprove)
        {
            $displayValue = UserTask::STATUS_APPROVE;
            $displayValue[UserTask::STATUS_UNAPPROVE] = '审批';
            $statusApprove = UserTask::STATUS_APPROVE;
            unset($statusApprove[0]);//去掉未审批
            asort($statusApprove);
            echo '<br><br><br>';
            echo Editable::widget([
                'editableValueOptions'=> ($mdlUserTask->taskStatus==Tasks::STATUS_COMMITED&&
                    $mdlUserTask->userTaskStatus==UserTask::STATUS_UNAPPROVE&&$mdlUserTask->approve_able==1)?['class'=>'btn btn-success btn-lg','style'=>'']:
                    ['disabled' => '','class'=>'btn btn-warning btn-lg','title'=>'该任务已被退回，暂无需审批。'],
                'formOptions' => ['action' => ['/user-task/do-approve']],
                'submitButton'=>['icon'=>'<i class="glyphicon glyphicon-ok"></i>','class'=>'btn btn-sm btn-primary kv-editable-submit'],
                'resetButton'=>['icon'=>'<i class="glyphicon glyphicon-remove"></i>','class'=>'hide btn btn-sm btn-default kv-editable-reset'],
                'model'=>$mdlUserTask,
                'attribute'=>'[0]userTaskStatus',
                'submitOnEnter' => false,
                'placement'=>PopoverX::ALIGN_TOP,
                'displayValue' => $displayValue[$mdlUserTask->userTaskStatus],
                'displayValueConfig'=>$displayValue,
                'size' => 'md',
                'header' => '请审批',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => $statusApprove,
                'pluginEvents'=>['editableSuccess'=>'function(event, val, form, data){
                    $("#usertask-0-usertaskstatus-targ").prop("style","color:black;cursor:pointer;pointer-events: none;");
                    $("#usertask-0-usertaskstatus-targ").attr("style","color:black;cursor:pointer;pointer-events: none;");
                    $("#usertask-0-usertaskstatus-targ").prop("class","btn btn-warning btn-lg");
                    var spqdval = parseInt($("#spqd span").eq(1).text())-1;
                    $("#spqd span").eq(1).text(spqdval);
                }'],
                'afterInput'=>function($form, $widget)use($mdlUserTask){
                    $strTemp ='<input type="text" class="hide" name="editableKey" value="'.$_GET['idUserTask'].'">';
                    if(strpos($mdlUserTask->tasks->name,'物料三审') !== false&&
                        Yii::$app->user->id == $mdlUserTask->tasks->modifyMaterial->materialApprover->approver3purchase)
                    {//当为物料时，看是不是采购，如果是采购要加个采购推荐级别。
                        $mdlUserTask->recommend_purchase = $mdlUserTask->tasks->modifyMaterial->recommend_purchase;
                        $strTemp .= $form->field($mdlUserTask, "[0]recommend_purchase")->label('采购推荐级别')
                            ->dropDownList(\frontend\models\ModifyMaterial::RECOMMEND_PURCHASE);
                    }
                    $strTemp .= $form->field($mdlUserTask, "[0]userTaskRemark")->label('备注')->textarea([
                        'displayValue' => 'more...',
                        'inputType' => Editable::INPUT_TEXTAREA,
                        //  'value' => "Raw denim you...",
                        'submitOnEnter' => false,
                        'size' => 'md',
                        'rows' => 5,
                    ]);
                    return $strTemp;
                }

            ]);
            echo '<br><br><br><br><br>';
        }

        ?>
    </div>
</div>


