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
	'shtaket'=>'Штакетник',
	'evroshtaket'=>'Евроштакетник',
	'rabica' => 'Сетка рабица',
	'rabicaramka' => 'Рабица в рамке',
	'kalitka' => 'Калитка',
	'vorota' => 'Ворота',
	'fundament' => 'Ленточный фундамент',
	'svai' => 'Сваи',
	'parkovka' => 'Парковочное место',
	'kanava' => 'Вьезд через канаву'
];

?>
<div class="site-about">

<?php if ($passtrue): ?>	



<div id="calc_main">

<div id="cart_client">

<div class="grid_client">

<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin(['action' =>['site/calc'], 'options' => ['data-pjax' => true ], 'method' => 'post',  'id' => 'form-client'] ); ?>
<?= $form->field($client,"focus_input")->hiddenInput()->label(false);?>


<div class="row">
<div class="col-xs-4 calc_title">Текущий расчет для клиента: </div>
<div class="col-xs-3 alright">
<?= Html::Button('<< пред', ['id'=>'btn-previus', 'class' => 'btn-default']) ?>&nbsp;&nbsp;
<?= Html::Button('след >>', ['id'=>'btn-next', 'class' => 'btn-default']) ?>
</div>

<div class="col-xs-offset-2 col-xs-3 alright">

<?= Html::Button('СОЗДАТЬ НОВЫЙ ЗАКАЗ', ['id'=>'btn-new-client', 'class' => 'btn-default']) ?>


</div>
</div>

<div class="client_info">
<div class="row">
<div class="col-xs-2"><?= $form->field($client,"id_client"); ?></div>

<div class="col-xs-3"><?= $form->field($client,"name"); ?></div>

<div class="col-xs-2"><?= $form->field($client,"tel"); ?></div>

<div class="col-xs-3"><?= $form->field($client,"address"); ?></div>
</div>

<div class="row">
<div class="col-xs-10"><?= $form->field($client,"comment"); ?></div>
</div>

<div class="row">
<div class="col-xs-2"><?= $form->field($client,"w"); ?></div>
<div class="col-xs-2"><?= $form->field($client,"h"); ?></div>
</div>
</div>




<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin(['action' =>['site/calcsmeta'], 'method' => 'post',  'id' => 'form-zabor'] ); ?>


<div class="row">
<div class="col-xs-12 alright"><?= Html::Button('ДОБАВИТЬ &nbsp;ТИП &nbsp;РАБОТ', ['id'=>'btn-add', 'class' => 'btn-default']) ?></div>
</div>

<div class="row">

<div class="col-xs-2">
<?=
$form->field($zabor, 'type')
    ->dropDownList($type,
    [ 'options' => [$zabor->type=>['selected' => 'selected']]]
		  
	);
?>
</div>

<div class="col-xs-1"><?= $form->field($zabor,"h"); ?></div>
<div class="col-xs-1"><?= $form->field($zabor,"w"); ?></div>
<div class="col-xs-2"><?= $form->field($zabor,"summa")->textInput(['readonly' => 'true']); ?></div>

<div class="col-xs-2 alcenter" style="padding-top:12px"><br/>

<a  name="link-calc" href="">
<?= Html::Button('РАССЧИТАТЬ', ['id'=>'btn-calc', 'class'=>'btn-default', 'disabled'=>'disabled']); ?>
</a><br/>
</div>

</div>



<div class="list">


</div>


<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>

<br/><br/><br/>
<div class="row">
<div class="col-xs-offset-8 col-xs-2 alright"><?= Html::Button('Распечатать', ['id'=>'btn-smeta', 'class' => 'btn-lg btn-success']) ?></div>
<div class="col-xs-2 alright"><?= Html::Button('Сохранить', ['id'=>'btn-smeta', 'class' => 'btn-lg btn-success']) ?> </div>

</div>


</div>

</div>


<div class="calc_variant">

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

$(document).on('change', '#client-h', function() {

 // проставляем высоту забора у элементов забора
 
 $("#zabor-h").val($("#client-h").val());
 

});





$(document).on('change', '#zabor-type', function() {
 
 if ($('#zabor-type option:selected').val() == 'none')  {

	 $('#btn-calc').attr('disabled','disabled');
 } else {

	 $('#btn-calc').removeAttr('disabled');
 }
 
 
});



$(document).on('click', '#btn-new-client', function() {
 
 // нажимаем кнопку создать нового клиента
 $("#client-focus_input").val('new_client');
  $("#form-client").submit();
});





$(document).on('click', '#btn-calc', function() {
 
 // при выборе типа забора формируем ссылку для расчета забора
  //$("a[name='link-calc']").attr('href', '/web/index.php?r=site%2F' + $('#zabor-type option:selected').val() + '&height=' + $('#zabor-h').val() +  '&len=' + $('#zabor-w').val() );
  $("#client-focus_input").val('go_to_calc');
  $("#form-client").submit();
  $("#form-zabor").submit();
});


// ********************************************* ДОБАВИТЬ ТИП ЗАБОРА *****************************************
// ********************************************* ДОБАВИТЬ ТИП ЗАБОРА *****************************************
// ********************************************* ДОБАВИТЬ ТИП ЗАБОРА *****************************************
// ********************************************* ДОБАВИТЬ ТИП ЗАБОРА *****************************************
// ********************************************* ДОБАВИТЬ ТИП ЗАБОРА *****************************************
// ********************************************* ДОБАВИТЬ ТИП ЗАБОРА *****************************************

$(document).on('click', '#btn-add', function() {
	
i=1;	
	
add_zabor='<div class="row"><div class="col-xs-2">';
add_zabor+='<div class="form-group field-zabor-type">';
add_zabor+='<label class="control-label" for="zabor-type[' + i +']">Тип работ</label>';
add_zabor+='<select id="zabor-type[' + i +']" class="form-control" name="Zabor[type][' + i +']">';
add_zabor+='<option value="none">Тип работ</option>';
add_zabor+='<option value="calcproflist">Профлист</option>';
add_zabor+='<option value="shtaket">Штакетник</option>';
add_zabor+='<option value="evroshtaket">Евроштакетник</option>';
add_zabor+='<option value="rabica">Сетка рабица</option>';
add_zabor+='option value="rabicaramka">Рабица в рамке</option>';
add_zabor+='<option value="kalitka">Калитка</option>';
add_zabor+='<option value="vorota">Ворота</option>';
add_zabor+='<option value="fundament">Ленточный фундамент</option>';
add_zabor+='<option value="svai">Сваи</option>';
add_zabor+='option value="parkovka">Парковочное место</option>';
add_zabor+='option value="kanava">Вьезд через канаву</option>';
add_zabor+='</select>';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';

add_zabor+='<div class="col-xs-1"><div class="form-group field-zabor-h">';
add_zabor+='<label class="control-label" for="zabor-h[' + i +']">Высота</label>';
add_zabor+='<input type="text" id="zabor-h[' + i +']" class="form-control" name="Zabor[h][' + i +']">';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';
add_zabor+='<div class="col-xs-1"><div class="form-group field-zabor-w">';
add_zabor+='<label class="control-label" for="zabor-w">Ширина</label>';
add_zabor+='<input type="text" id="zabor-w[' + i +']" class="form-control" name="Zabor[w][' + i +']">';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';
add_zabor+='<div class="col-xs-2"><div class="form-group field-zabor-summa">';
add_zabor+='<label class="control-label" for="zabor-summa[' + i +']">Сумма работ</label>';
add_zabor+='<input type="text" id="zabor-summa[' + i +']" class="form-control" name="Zabor[summa][' + i +']" readonly="true">';

add_zabor+='<div class="help-block"></div>';
add_zabor+='</div></div>';

add_zabor+='<div class="col-xs-2 alcenter" style="padding-top:12px"><br/>';

add_zabor+='<a  name="link-calc" href="">';
add_zabor+='<button type="button" id="btn-calc[' + i +']" class="btn-default" disabled="disabled">РАССЧИТАТЬ</button></a><br/>';
add_zabor+='</div>';

add_zabor+='</div>';

	
 $(".list").append(add_zabor);

});





JS;

$this->registerJs($script, yii\web\View::POS_READY);

?>

