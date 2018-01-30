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


<div class="col-xs-2 alcenter"><?= Html::submitButton('РАСПЕЧАТАТЬ', ['id'=>'btn-print-smeta', 'class'=>'print', 'onclick'=>'print();return false;','style'=>'font-size:19px']); ?></div>

<div class="col-xs-offset-4 col-xs-2 alcenter">
<?= Html::submitButton('ОТПРАВИТЬ', ['id'=>'btn-send-smeta','style'=>'font-size:19px']) ?>
</div>

<div class="col-xs-2 alright"><?= $form->field($gs_head,"usermail")->label(false); ?></div>

<?php ActiveForm::end(); ?>

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
	График работы офиса — 9.00 — 19.00. <br/></div>
	ИНН 7720301406<br/>
	КПП 772001001<br/>	
	</td>
  </tr>
</table>

<?php Pjax::begin(); ?>
<?php $form = ActiveForm::begin(['action' =>['site/editsmeta'],  'options' => ['data-pjax' => true ] ,'method' => 'post',  'id' => 'form-editsmeta'] ); ?>


<h3  style="text-align:center;margin-bottom:50px">СМЕТА № <?= $cl->dogovor ?><br/><br/>ОБЩАЯ СУММА=<?= number_format($smeta_itog ,2,'.',' ') ?> Руб</h3>

<div class="row">
<?= $form->field($gs_head,"focus_input")->hiddenInput(['value'=>''])->label(false); ?>
<div class="col-xs-offset-4 col-xs-2 alright">% изменения  стоимости материалов</div>
<div class="col-xs-1"><?= $form->field($gs_head,"add_element")->label(false); ?></div>
<div class="col-xs-2 alright">% изменения стоимости работ</div>
<div class="col-xs-1"><?= $form->field($gs_head,"add_work")->label(false); ?></div>
<div class="col-xs-2 alright"><?= Html::submitButton('ОБНОВИТЬ', ['id'=>'btn-save','style'=>'font-size:19px']) ?></div>
</div>

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


	<h3><?php echo $gs_head->zabor_elements[$item['zabortype']]; ?></h3>
	<div class="row print_head_smeta">
	<div class="col-xs-1">Поз</div>
	<div class="col-xs-7">Наименование</div>
	<div class="col-xs-1 kol">Кол</div>
	<div class="col-xs-1 alright kol">Ед.изм.</div>	
	<div class="col-xs-1 alright price">Цена</div>
	<div class="col-xs-1 alright price">Сумма</div>
	</div>
			<?php 	
				if ($item['zabortype'] != 'transport') {
			?>		
				 <div style="text-decoration:underline; font-weight:700">cтоимость материалов</div>
			<?php 
				} else {
			?>
				<div style='margin-top:10px;'></div>
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
 
	<div class="row line">
	<div class="col-xs-1"><?= $form->field($gs[$i],"poz")->textInput(["name" => "col[$i][1]", 'readonly' => 'true', 'id' => 'poz-'.$i ])->label(false); ?></div>
	<div class="col-xs-7"><?= $form->field($gs[$i],"name")->textInput(["name" => "col[$i][2]", "id" => "name-".$i ])->label(false); ?></div>
	<div class="col-xs-1 alright kol"><?= $form->field($gs[$i],"kol")->textInput(["name" => "col[$i][3]", "id" => "kol-".$i ])->label(false); ?></div>
	<div class="col-xs-1 alcenter kol"><?= $form->field($gs[$i],"ed")->textInput(["name" => "col[$i][4]", "id" => "ed-".$i ])->label(false); ?></div>	
	<div class="col-xs-1 alright price"><?= $form->field($gs[$i],"price")->textInput(["name" => "col[$i][5]", "id" => "price-".$i ])->label(false); ?></div>
	<div class="col-xs-1 alright price" style="border:1px solid #cccccc; background:#EEEEEE; padding:4px; font-size:16px"><?= $item["summa"] ?></div>
	<div class="hidden"><?= $form->field($gs[$i],"ew")->textInput(["name" => "col[$i][6]", "id" => "ew-".$i ])->label(false); ?></div>
	</div>
<?php 
} // if
?>


<?php } 
if (!$flag_work) {
?>
 <?php $itog=$itog_element+$itog_work ?>
<h4 style="text-align:right">ИТОГО:  Материалы  <?= number_format($itog_element ,2,'.',' ') ?> +  Работа  <?=  number_format($itog_work ,2,'.',' ')  ?>  =  <?=  number_format($itog ,2,'.',' '); ?> Руб </h4>

<?php 
}
} // foreach zabor_item
?>


<div class="row" style="background-color:gray; color:white; padding:5px 10px; font-size:16px">Итого за работы и материалы = <?= number_format($smeta_itog ,2,'.',' ') ?> Руб </div>


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

