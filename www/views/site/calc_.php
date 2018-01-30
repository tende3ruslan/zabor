<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'РАСЧЕТ ЗАБОРА';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $type=[
	'none'=>'Тип работ',
	'calcproflist'=>'Профлист',
	'calcshtaket'=>'Штакетник',
	'rabica' => 'Сетка рабица',
	'rabicaramka' => 'Рабица в рамке',
	'kalitka' => 'Калитка',
	'vorota' => 'Ворота',
	'otkatnievorota' => 'Откатные ворота',
	'fundament' => 'Ленточный фундамент',
	'svai' => 'Сваи',
	'parkovka' => 'Парковочное место',
	'kanava' => 'Вьезд через канаву',
	'transport' => 'Транспорт'
];

?>
<div class="site-about">
<h2><a style="color:red" href="/blog.php">Список изменений (Обновлено 31 мая 17:40) </a></h2>
<?php if ($passtrue): ?>	



<div id="calc_main">

<div id="cart_client">

<div class="grid_client">
<?php $form = ActiveForm::begin(['action' =>['site/calc'],'method' => 'post',  'id' => 'form-client'] ); ?>
<?= $form->field($client,"focus_input")->hiddenInput()->label(false);?>


<div class="row">
<div class="col-xs-4 calc_title">Текущий расчет для клиента: </div>
<!--<div class="col-xs-2 alright"><?= Html::Button('РАСПЕЧАТАТЬ', ['id'=>'btn-print', 'class' => 'btn-default']) ?></div>-->

<div class="col-xs-offset-1 col-xs-3 alright">
<a href="<?= Url::to(['site/generatesmeta', 'id_client' => $client->id_client]); ?>"><?= Html::Button('ТехЗадание/ДОКУМЕНТЫ', ['id'=>'btn-print', 'class' => 'btn-default']) ?></a>
</div>
<div class="col-xs-2 alright"><?= Html::Button('НОВЫЙ ЗАКАЗ', ['id'=>'btn-new-client', 'class' => 'btn-default']) ?></div>
<div class="col-xs-2 alright"><?= Html::submitButton('СОХРАНИТЬ', ['id'=>'btn-save', 'class' => 'btn-default']) ?></div>
</div>

<div class="client_info">
<div class="row">
<div class="col-xs-1"><?= $form->field($client,"id_client")->textInput(['readonly' => 'true']); ?></div>
<div class="col-xs-2"><?= $form->field($client,"dogovor")->textInput(['readonly' => 'true']); ?></div>
<div class="col-xs-4"><?= $form->field($client,"name"); ?></div>
<div class="col-xs-2"><?= $form->field($client,"tel"); ?></div>
<div class="col-xs-3"><?= $form->field($client,"email"); ?></div>
</div>


<div class="row">
<div class="col-xs-6"><?= $form->field($client,"address"); ?></div>
<div class="col-xs-6"><?= $form->field($client,"comment"); ?></div>
<div class="col-xs-2 hidden"><?= $form->field($client,"w"); ?></div>
</div>

<div class="row">
<div class="col-xs-2"><?= $form->field($client,"datedogovor"); ?></div>
<div class="col-xs-2"><?= $form->field($client,"datestart"); ?></div>
<div class="col-xs-2"><?= $form->field($client,"dateend"); ?></div>
<div class="col-xs-2"><?= $form->field($client,"avans"); ?></div>
<div class="col-xs-2"><?= $form->field($client,"summa")->textInput(['readonly' => 'true']);  ?></div>
<div class="col-xs-2">
<?=
$form->field($client, 'work_status')
    ->dropDownList($client->work_status_text,
    [ 'options' => [$client->work_status=>['selected' => 'selected']]]);
?>
</div>
</div>

</div>
</div>




<!--
<div class="col-xs-12 alright">
<?= Html::Button('ДОБАВИТЬ &nbsp;ТИП &nbsp;РАБОТ', ['id'=>'btn-add', 'class' => 'btn-default']) ?></div>
</div>
-->


</div>

<div class="zabor-list">
<div class="row">

<div class="col-xs-2">Что делаем</div>

<div class="col-xs-1">Длина</div>
<div class="col-xs-1">Высота</div>
<div class="col-xs-2">Сумма</div>

<div class="col-xs-6 alright">
<?= Html::Button('<< пред', ['id'=>'btn-prev', 'class' => 'btn-default']) ?>&nbsp;&nbsp;
<?= Html::Button('след >>', ['id'=>'btn-next', 'class' => 'btn-default']) ?>
<br/>
</div>
<?php ActiveForm::end(); ?>

</div>



<?php $i=0; 
foreach ($zabor as $z) { ?>

<?php $form = ActiveForm::begin(['action' =>['site/calcsmeta'], 'method' => 'post',  'options' => ['data-pjax' => true ], 'id' => 'zabor-'.$i] ); ?>

<div class="row">

<div class="col-xs-2">
<?=
$form->field($z, 'type')
    ->dropDownList($type,
    [ 'options' => [$z->type=>['selected' => 'selected']], 'id' => 'type-'.$i])->label(false);
?>
</div>
<div class="col-xs-1"><?= $form->field($z,"l")->textInput(['id' => 'zabor-l-'.$i])->label(false); ?></div>
<div class="col-xs-1"><?= $form->field($z,"h")->textInput(['id' => 'zabor-h-'.$i])->label(false); ?></div>

<div class="col-xs-2"><?= $form->field($z,"summa")->textInput(['readonly' => 'true', 'id' => 'summa-'.$i ])->label(false); ?></div>
<?= $form->field($z,"smeta_id")->textInput(['id' => 'smeta_id-'.$i])->hiddenInput(['value' => $i])->label(false); ?>
<input name="focus_input_zabor" type="hidden" value="" id="focus_input_zabor-<?= $i ?>">
<div class="col-xs-2">
<a  name="link-calc" href="">
<?= Html::submitButton( ($z->arhiv=="go_to_calc") ? "ПРАВКА":"РАСЧЕТ" , ['id'=>'btn-calc-'.$i, 'class'=>'btn-default btn-calc']); ?>
</a>
<?= Html::submitButton('Х', ['id'=>'btn-clear-poz-'.$i, 'class' => 'btn-clear']) ?>
</div>

</div>

<?php ActiveForm::end(); ?>


<?php	
$i++;
}
?>






<?php
for ($j=$i;$j<12; $j++) {
?>	



<?php $form = ActiveForm::begin(['action' =>['site/calcsmeta'],  'options' => ['data-pjax' => true ], 'method' => 'post',  'id' => 'zabor-'.$j] ); ?>

<div class="row">

<div class="col-xs-2">
<?=
$form->field($zabor_null, 'type')
    ->dropDownList($type,
    [ 'options' => [$zabor_null->type=>['selected' => 'selected']], 'id' => 'type-'.$j]
		  
	)->label(false);
?>
</div>
<div class="col-xs-1"><?= $form->field($zabor_null,"l")->textInput(['id' => 'zabor-l-'.$j])->label(false); ?></div>
<div class="col-xs-1"><?= $form->field($zabor_null,"h")->textInput(['id' => 'zabor-h-'.$j])->label(false); ?></div>
<div class="col-xs-2"><?= $form->field($zabor_null,"summa")->textInput(['readonly' => 'true','id' => 'summa-'.$j ])->label(false); ?></div>
<?= $form->field($zabor_null,"smeta_id")->textInput(['id' => 'smeta_id-'.$j])->hiddenInput(['value' => $j])->label(false); ?>
<input name="focus_input_zabor" type="hidden" value="" id="focus_input_zabor-<?= $j ?>">
<div class="col-xs-2" >

<a  name="link-calc" href="">
<?= Html::submitButton('РАСЧЕТ', ['id'=>'btn-calc-'.$j, 'class'=>'btn-default btn-calc']); ?>
</a>
<?= Html::submitButton('Х', ['id'=>'btn-clear-poz-'.$i, 'class' => 'btn-clear']) ?>
</div>

</div>

<?php ActiveForm::end(); ?>


<?php	
}
?>
</div>




</div>




<div class="calc_variant hidden">

<div class="calc_title">
Рассчитать  для: 
</div>

<div class="row">
<div class="col-xs-2">

	<div class="calc_main_container" onclick="location.href='<?= Url::to(['site/calcproflist']); ?>'">
	<div class="image_calc_main">
	 <?= Html::img('@web/images/main_calc/proflist.jpg',  ['alt'=>'Профлист', 'class'=>'image_calc_main']) ?> 
	</div>
	<div class="text_calc_main">
	Профлист<br/> (в работе)
	</div>
	</div>
</div>


<div class="col-xs-2">
	<div class="calc_main_container" onclick="location.href='<?= Url::to(['site/calc_sh']); ?>'">
	<div class="image_calc_main ">
	<?= Html::img('@web/images/main_calc/shtaketnik.jpg',  ['alt'=>'Штакетник', 'class'=>'image_calc_main']) ?> 
	</div>
	<div class="text_calc_main">
	Штакетник<br/>  (не готово)
	</div>
	</div>
</div>


<div class="col-xs-2">
	<div class="calc_main_container" onclick="location.href='<?= Url::to(['site/calc_evrosh']); ?>'">
	<div class="image_calc_main">
	<?= Html::img('@web/images/main_calc/evroshtaketnik.png',  ['alt'=>'Евроштакетник', 'class'=>'image_calc_main']) ?> 
	</div>
	<div class="text_calc_main">
	Евроштакетник<br/>(не готово)
	</div>
	</div>
</div>


<div class="col-xs-2">
	<div class="calc_main_container" onclick="location.href='<?= Url::to(['site/calc_rabica']); ?>'">
	<div class="image_calc_main">
	<?= Html::img('@web/images/main_calc/rabica.png',  ['alt'=>'Сетка рабица', 'class'=>'image_calc_main']) ?> 
	</div>
	<div class="text_calc_main">
	Сетка рабица<br/>(не готово)
	</div>
	</div>
</div>


<div class="col-xs-2">
	<div class="calc_main_container" onclick="location.href='<?= Url::to(['site/calc_setkarabica']); ?>'">
	<div class="image_calc_main">
	<?= Html::img('@web/images/main_calc/rabica_ramka.jpg',  ['alt'=>'Сетка рабица в рамке', 'class'=>'image_calc_main']) ?> 
	</div>
	<div class="text_calc_main">
	Сетка рабица в рамке<br/>(не готово)
	</div>
	</div>
</div>

</div>

</div>

</div>

<?php else: ?>
Нет доступа, введите пароль.<br/>
<?php endif ?>


</div>


<?php

$script = <<< JS



$(document).on('click', '#btn-new-client', function() {


 // нажимаем кнопку создать нового клиента
 
  $("#client-focus_input").val('new_client');
  $("#form-client").submit();
  
  
  $('[name="Zabor[type]"]').val('none');
  $('[name="Zabor[h]"]').val('');
  $('[name="Zabor[l]"]').val('');
  $('[name="Zabor[summa]"]').val('');
  
// elements_enabled();

});





$(document).on('change', '#client-h', function() {

 // проставляем высоту забора у элементов забора
 
 $("[id^=zabor-h]").val($("#client-h").val());
 
});




$(document).on('change', '[name="Zabor[type]"]', function() {
	

 poz=$(this).attr('id').indexOf('-')+1;
 last=$(this).attr('id').length+1;
 index=$(this).attr('id').substr(poz,last);
 
 if ($('#type-'+index).val()=='kalitka') {
	 $('#zabor-l-' + index).val('1');
	 $('#zabor-h-' + index).val($('#zabor-h-0' ).val());
 }
 
  if ($('#type-'+index).val()=='vorota') {
	 $('#zabor-l-' + index).val('4');
	 $('#zabor-h-' + index).val($('#zabor-h-0' ).val());
 }
 
  if ($('#type-'+index).val()=='otkatnievorota') {
	 $('#zabor-l-' + index).val('4');
	 $('#zabor-h-' + index).val($('#zabor-h-0' ).val());
 }
  
 
 /*
 if ($('#' + $(this).attr('id') +' option:selected').val() == 'none')  {
	 $('#btn-calc-'+index).attr('disabled','disabled');
 } else {
	 $('#btn-calc-'+index).removeAttr('disabled');
 }
*/
});




$(document).on('click', '.btn-calc', function() {
  $("#client-focus_input").val('go_to_calc');
  $("#form-client").submit();
});

$(document).on('click', '.btn-clear', function() {
	$("#form-client").submit();
  $('[name="focus_input_zabor"]' ).val('clear_smeta');
});


$(document).on('click', '#btn-prev', function() {
	 $("#client-focus_input").val('prev');
	 $("#form-client").submit();
});


$(document).on('click', '#btn-next', function()  {
	$("#client-focus_input").val('next');
  $("#form-client").submit();
});




// ********************************************* НЕ ИСПОЛЬЗУЕТСЯ *****************************************
/*



elements_disabled();




$(document).on('click', '#btn-save', function() {

 // нажимаем кнопку создать нового клиента
 
  $("#client-focus_input").val('save_client');
  $("#form-client").submit();
});





function elements_disabled() {
	$('.grid_client').find('input, button, select').attr('disabled','true');
	$('#btn-new-client').removeAttr('disabled');
	$('#btn-previus').removeAttr('disabled');
	$('#btn-next').removeAttr('disabled');
	$('.grid-zabor').hide();
	return true;
}





function elements_enabled() {
	$('.grid_client').find('input, button, select').removeAttr('disabled');

	return true;

}





$(document).on('click', '#btn-add', function() {
	
 $(".list").append(add_zabor);

});




*/



JS;

$this->registerJs($script, yii\web\View::POS_READY);

?>

