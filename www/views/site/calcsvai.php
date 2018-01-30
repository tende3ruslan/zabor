<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = 'РАСЧЕТ СВАЙ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<!-- <h2><?= Html::encode($this->title) ?></h2> -->

<?php if ($passtrue): ?>	

<?php Pjax::begin(); ?>

<?php $form = ActiveForm::begin(['action' =>['site/calcsvai'], 'options' => ['data-pjax' => true ], 'method' => 'post',  'id' => 'form-signup'] ); ?>

<input name="calc_step" class="hidden" value="<?= $model->calc_step; ?>"/>

<?= $form->field($model,"focus_input")->hiddenInput(['value'=> $value])->label(false);?>

<?php if  ($model->calc_step == 1) { 
?>

<div class="row lite_gray">

<div class="col-xs-2">
<?=
$form->field($model, 'diametr')
    ->dropDownList($model->diametr_text,
    [ 'options' => [$model->diametr=>['selected' => 'selected']]]	  
	);
	?>
</div>


<div class="col-xs-2">
<?=
$form->field($model, 'height')
    ->dropDownList($model->height_text,
    [ 'options' => [$model->height=>['selected' => 'selected']]]	  
	);
	?>
</div>


<div class="col-xs-2">
<?= $form->field($model,"count"); ?>
</div>

<div class="col-xs-3"></div>



<div class="col-xs-1" style="padding-top:30px; padding-bottom:30px; text-align:right">
<?= Html::submitButton('Сброс', ['id'=>'calc_clear', 'class' => 'btn-danger']) ?>
</div>
 
<div class="col-xs-1" style="padding-top:30px; text-align:right">
<?= Html::submitButton('Расчет', ['id'=>'calc_submit', 'class' => 'btn-success']) ?>
</div>

<div class="col-xs-1" style="padding-top:30px; text-align:right">
<?= Html::Button('Смета', ['id'=>'btn-smeta', 'class' => 'btn-success']) ?> 
</div>
</div>
<div name="parametrs">

<div class="row lite_gray">


<!-- hello :- ) -->



</div>
</div>


<?php } ?> <!-- ШАГ 1 -->
<?php if  ($model->calc_step <=2) { ?>
<?php } ?> 






<div id="smeta" style="height:250px">
<div class="row">

<h3  style="text-align:right"> Смета</h3>
<?php $sm=$smeta->readSmeta($id_client, $id_smeta); ?>
<?php $smeta_itog=$smeta->summaSmeta($id_client, $id_smeta); ?>
<div>
	<h4>Стоимость материалов</h4>
	<div class="row headsmeta">
	<div class="col-xs-1">Поз</div>
	<div class="col-xs-7">Наименование</div>
	<div class="col-xs-1 kol">Кол</div>
	<div class="col-xs-1 kol">Ед. изм.</div>
	<div class="col-xs-1 price">Цена</div>
	<div class="col-xs-1 price">Сумма</div>
	</div>
<?php 
//'user_id','smeta_id','attr','table','type','kol','price'
$itog=0;
$itog_element=0;
$itog_work=0;
$flag=true;
 foreach ($sm as $i=>$item) {
 if (($item['table'][0]=='w') && $flag){
	 $flag=false;
?>
<h4>Стоимость работ</h4>
<?php
 }
 
 if ($flag) $itog_element+=$item['price']*$item['kol']; else $itog_work+=$item['price']*$item['kol'];
  
 ?>
<?php if (($item['price'] > 0) && ($item['kol']>0)) {?>
	<div class="row line">
	<div class="col-xs-1"><?= $i ?><?php $item['table'] ?></div>
	<div class="col-xs-7"><?= ($item['table'][0]!='w') ? $model->getelement_name($item['attr']).' '.$item['type'] :  $model->getwork_name($item['attr']).' '.$item['type']?></div>
	<div class="col-xs-1 kol"><?= $item['kol'] ?></div>
	<div class="col-xs-1 kol"><?= $item['ed'] ?></div>
	<div class="col-xs-1 price"><?= $item['price'] ?></div>
	<div class="col-xs-1 price"><?php $itog=$itog+$item['price']*$item['kol']; echo  $item['price']*$item['kol']; ?></div>
	</div>
<?php }
} ?>
<h3 style="text-align:right">ИТОГО:  Материалы= <?= $itog_element ?> +  Работы =  <?= $itog_work ?>  =  <?=  $itog; ?> Руб </h3>
</div>
</div>

</div>


<br/><br/><br/><br/><br/>


<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>

<div class="row">
<div class="col-xs-12 alright">
<a name="link-back" href="<?= Url::to(['site/calc','back'=>1], true); ?>" title="Вернуться  к карточке клиента"><?= Html::Button('ВЕРНУТЬСЯ НАЗАД К КАРТОЧКЕ КЛИЕНТА', ['id'=>'btn-back', 'class' => 'btn-lg btn-default']) ?> </a>
</div>
</div>

<?php else: ?>
Нет доступа, введите пароль.<br/>
<?php endif ?>


</div>





<?php

$script = <<< JS


$(document).on('change', '.form-control:lt(3)', function() { 
//если выбран первый или второй инпут формируем ссылку back с передачей h и w

 $("a[name='link-back']").attr('href', '/web/index.php?r=site%2Fcalc&h=' + $('#calcproflist-height').val() +  '&l=' + $('#calcproflist-len').val() );	

 });

 

$(document).on('change', '.form-control:gt(1)', function() {
 
 //передаем id  инпута в фокусе
 
 tmp=$(this).attr('id');

 $("#calcproflist-focus_input").val(tmp);
 
 // делаем сабмит форме

 $("#form-signup").submit();
});
 


//ловим кнопку смета

$(document).on('click', '#btn-smeta', function() {
	
	  if ($("#calcsvai-len1").val() == "") {
		alert('Укажите длину забора');
		return;
    }
	
	if ($("#calcsvai-height1").val() == "") {
		alert('Укажите высоту забора');
		return;
    }
	
    
	 $("#calcsvai-focus_input").val('smeta');
	 
	  
	 //Необходимо прокрутить в конец страницы
	var scrollTime=500;
	$("body,html").animate({"scrollTop":170},scrollTime);
	
		// сабмит формы
	  $("#form-signup").submit();
		  
	
});
    
//ловим кнопку сброс

$(document).on('click', '#calc_clear', function() {
	 $("#calcsvai-focus_input").val('clear');
	  $("#form-signup").submit();
});

	
JS;

$this->registerJs($script, yii\web\View::POS_READY);

?>

