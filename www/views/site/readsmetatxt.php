<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
?>




<span class="noprint">

<div class="row">
<div class="col-xs-12 alright">
<a name="link-back" href="<?= Url::to(['site/calc'], true); ?>" title="НАЗАД">
<?= Html::Button(' << К СТРАНИЦЕ КЛИЕНТА', ['id'=>'btn-back', 'class' => 'btn-lg btn-warning','style'=>'width:100%']) ?> 
</a></div>

</div>

<h3 class="alcenter" style="color:red; margin-bottom:30px"></h3>


<?php $form = ActiveForm::begin(['action' =>['site/mailgo','id_client' => $client->id_client ],'method' => 'post', 'id' => 'form-client'] ); ?>
<div class="row" style="margin-bottom:70px">

<div class="col-xs-2">
<?=
$form->field($gs_head, 'document')
    ->dropDownList($gs_head->document_text,
    [ 'options' => [$gs_head->document=>['selected' => 'selected']]]
		  
	)->label(false);
	?>
</div>


<div class="col-xs-2 alcenter"><?= Html::submitButton('РАСПЕЧАТАТЬ', ['id'=>'btn-print-smeta', 'class'=>'print', 'onclick'=>'print(); return false;','style'=>'font-size:19px']); ?></div>
<div class="col-xs-4 alcenter">
<a name="link-back" href="<?= Url::to(['site/generatesmeta'], true); ?>" title="СОЗДАТЬ СМЕТУ">
<?= Html::Button('ИСХОДНОЕ ТехЗадание', ['id'=>'btn-new-smeta','style'=>'font-size:19px']) ?>
</a>
</div>

<div class="col-xs-2 alcenter">
<?= Html::submitButton('ОТПРАВИТЬ', ['id'=>'btn-send-smeta','style'=>'font-size:19px']) ?>
</div>

<div class="col-xs-2 alright"><?= $form->field($gs_head,"usermail")->label(false); ?></div>

<?php ActiveForm::end(); ?>

</div>

<div class="row">
<div class="col-xs-12 alright" style="color:red">
<?= ($percent_element>=0) ? "Увеличение" : "уменьшение" ?> стоимости на материалы: <strong><?= $percent_element ?></strong>%,
<?= ($percent_work>=0) ? "увеличение" : "уменьшение" ?> стоимости на работы: <strong><?= $percent_work?></strong>%. 
</div>
</div>


</span>


<h3>Заказ-наряд (техническое задание) № <?= $client->dogovor ?> от <?= $client->date_dogovor_text ?> </h3>
<hr/>

<table width="100%" border="0" cellpadding="4">
  <tr>
    <td width="50%" ><span style="font-weight:800">Заказчик</span><br/>
Имя: <?=$client->name ?><br/>
Телефон: <?=$client->tel ?><br/>
Адрес: <?=$client->address ?><br/><br/>
<!-- Примечание: <?=$client->comment ?><br/>	-->
Дата приема заказа: <?=$client->date_base_text ?><br/>
Дата начала работ : <?=$client->date_start_text ?><br/>	
Дата окончания работ: <?=$client->date_end_text ?><br/>		
	</td>
    <td width="50%" valign="top"><div align="right"  style="font-weight:800">Исполнитель:<br/> ООО "Империя Заборов"<br/></div><div align="right">
	<br/>105118, г. Москва,  Шоссе Энтузиастов дом 34, 1-34<br/>
	Офис: +7 (495) 231-21-68<br/>
	E-mail: zabor@zabor-stroim.ru<br/>
	График работы офиса — 9.00 — 19.00. <br/>
	ИНН 7720301406<br/>
	КПП 772001001<br/>		
	</div>
	</td>
  </tr>
</table>

<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin(['action' =>['site/editsmeta'],  'options' => ['data-pjax' => true ] ,'method' => 'post',  'id' => 'form-editsmeta'] ); ?>



<?php for ($p=0;$p<=20;$p++) { ?>
<?php 
	$itog=0;
	$itog_element=0;
	$itog_work=0;
	$flag_work=true;
	$flag_title=true;

	 foreach ($gs as $i => $item) {
	 if ($p == $item['smeta_id'])  {
		 if ($flag_title) { $flag_title=false;
?>


	<h3><?= $gs_head->zabor_elements[$item['zabortype']]; ?></h3>
	<div class="row print_head_smeta">
	<div class="col-xs-1 forprint">Поз</div>
	<div class="col-xs-7 forprint">Наименование</div>
	<div class="col-xs-1 kol forprint">Кол</div>
	<div class="col-xs-1 alright kol forprint">Ед.изм.</div>	
	<div class="col-xs-1 alright price forprint">Цена</div>
	<div class="col-xs-1 alright price forprint">Сумма</div>
	</div>
			<?php 	
				if ($item['zabortype'] != 'transport') {
			?>		
				 <div style="text-decoration:underline; font-weight:700">cтоимость материалов</div>
			<?php 
				}
			?>	 

	 
<?php
 }	
 ?>
	
	
<?php   if (($item['ew']=='w') && $flag_work) { $flag_work=false; ?>	
<div style="text-decoration:underline; font-weight:700">cтоимость работ</div>
<?php } ?>


 <?php
 if ($flag_work) $itog_element+=$item['price']*$item['kol']; else $itog_work+=$item['price']*$item['kol'];

 ?>
 
	<div class="row line" style="border-bottom:1px dotted">
	<div class="col-xs-1"><?= $item['poz'] ?></div>
	<div class="col-xs-7"><?= $item['name'] ?></div>
	<div class="col-xs-1 alright kol"><?= $item['kol'] ?></div>
	<div class="col-xs-1 alcenter kol"><?= $item['ed'] ?></div>	
	<div class="col-xs-1 alright price"><?= number_format($item['price'] ,2,'.',' ') ?></div>
	<div class="col-xs-1 alright price"> <?= number_format($item['summa'] ,2,'.',' ') ?></div>
	
	</div>
<?php 
} // if
?>


<?php } 
if (!$flag_work) {
?>
 <?php $itog=$itog_element+$itog_work ?>
<h4 style="text-align:right">ИТОГО:  Материалы  <?= number_format($itog_element ,2,'.',' ') ?> +  Работа  <?=  number_format($itog_work ,2,'.',' ')  ?>  =  <?=  number_format($itog ,2,'.',' '); ?> Руб </h4>
<div class="row" style="background-color:gray; color:white; padding:5px 10px; font-size:16px">
Аванс = <?= number_format($client->avans ,2,'.',' ') ?>; Долг = <?= number_format($itog-$client->avans ,2,'.',' ') ?>
</div>
<?php 
}
} // foreach zabor_item
?>


<div class="row" style="background-color:gray; color:white; margin-top:18px; padding:5px 10px; font-size:16px">Итого за работы и материалы = <?= number_format($smeta_itog ,2,'.',' ') ?> Руб </div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>


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

