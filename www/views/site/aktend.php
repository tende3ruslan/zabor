<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>




<span class="noprint">

<div class="row">
<div class="col-xs-12 alright">
<a name="link-back" href="<?= Url::to(['site/calc'], true); ?>" title="НАЗАД">
<?= Html::Button(' << К СТРАНИЦЕ КЛИЕНТА', ['id'=>'btn-back', 'class' => 'btn-lg btn-warning','style'=>'width:100%']) ?> 
</a></div>

</div>

<h3 class="alcenter" style="color:red; margin-bottom:30px"></h3>


<div class="row" style="margin-bottom:70px">

<?php $form = ActiveForm::begin(['action' =>['site/mailgo','id_client' => $cl->id_client ],'method' => 'post', 'id' => 'form-client'] ); ?>

<div class="col-xs-2">
<?=
$form->field($gs_head, 'document')
    ->dropDownList($gs_head->document_text,
    [ 'options' => [$gs_head->document=>['selected' => 'selected']]]
		  
	)->label(false);
	?>
</div>


<div class="col-xs-2 alcenter"><?= Html::submitButton('РАСПЕЧАТАТЬ', ['id'=>'btn-print-smeta', 'class'=>'print', 'onclick'=>'print();return false','style'=>'font-size:19px']); ?></div>

<div class="col-xs-offset-4 col-xs-2 alcenter">
<?= Html::submitButton('ОТПРАВИТЬ', ['id'=>'btn-send-smeta','style'=>'font-size:19px']) ?>
</div>

<div class="col-xs-2 alright"><?= $form->field($gs_head,"usermail")->label(false); ?></div>

<?php ActiveForm::end(); ?>

</div>



</span>

<?= $aktend->page ?>




<?php

$script = <<< JS

$(document).on('change', '#editsmeta-document', function() {
	add=$(this).val();
	
	if ($(this).val()=='texsmeta')  add='printsmeta' + "&minusa=show";
	
	location.href="/web/index.php?r=site/" + add;
});

	
JS;

$this->registerJs($script, yii\web\View::POS_READY);

?>

