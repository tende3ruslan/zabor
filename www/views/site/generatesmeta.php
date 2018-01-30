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

<?php

 $cl=$client->readClientId($id_client);

?>



<div class="row" style="margin-bottom:70px">

<?php $form = ActiveForm::begin(['action' =>['site/mailgo','id_client' => $cl->id_client ],'method' => 'post', 'id' => 'form-client'] ); ?>

<div class="col-xs-2">
<?=
$form->field($gs_mail, 'document')
    ->dropDownList($gs_mail->document_text,
    [ 'options' => [$gs_mail->document=>['selected' => 'selected']]]
		  
	)->label(false);
	?>
</div>


<div class="col-xs-2 alcenter"><?= Html::submitButton('РАСПЕЧАТАТЬ', ['id'=>'btn-print-smeta', 'class'=>'print', 'onclick'=>'print()','style'=>'font-size:19px']); ?></div>

<div class="col-xs-offset-4 col-xs-2 alcenter">
<?= Html::submitButton('ОТПРАВИТЬ', ['id'=>'btn-send-smeta','style'=>'font-size:19px']) ?>
</div>

<div class="col-xs-2 alright"><?= $form->field($gs_mail,"usermail")->label(false); ?></div>

<?php ActiveForm::end(); ?>

</div>



</span>
<div class="row">
<div class="col-xs-6">
<div style="font-weight:700">ЗАКАЗЧИК</div><br/>
Имя: <?=$cl->name ?><br/>
Телефон: <?=$cl->tel ?><br/>
Адрес: <?=$cl->address ?><br/>
Примечание: <?=$cl->comment ?><br/>
ИНН 7720301406<br/>
КПП 772001001<br/>	
</div>
<div class="col-xs-6">
</div>

</div>

<h3  style="text-align:center;margin-bottom:50px">ТЕХНИЧЕСКОЕ ЗАДАНИЕ № <?= $cl->dogovor ?><br/><br/>ОБЩАЯ СУММА=<?= number_format($smeta_itog ,2,'.',' ') ?> Руб</h3>


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


	<h3><?php echo $gs_mail->zabor_elements[$item['zabortype']]; ?></h3>
	<div class="row print_head_smeta">
	<div class="col-xs-1">Поз</div>
	<div class="col-xs-7">Наименование</div>
	<div class="col-xs-1 kol">Кол</div>
	<div class="col-xs-1 alright kol">Ед.изм.</div>	
	<div class="col-xs-1 alright price">Цена</div>
	<div class="col-xs-1 alright price">Сумма</div>
	</div>
	 <div style="text-decoration:underline; font-weight:700">cтоимость материалов</div>
<?php
 }	
 ?>
	
	
<?php   if (($item['ew']=='w') && $flag_work) { $flag_work=false; ?>	
<div style="text-decoration:underline; font-weight:700">cтоимость работ</div>
<?php } ?>


 <?php
 if ($flag_work) $itog_element+=$item['price']*$item['kol']; else $itog_work+=$item['price']*$item['kol'];

 ?>
 
	<div class="row line" style="border-bottom:1px dotted #000">
	<div class="col-xs-1 forprint"><?= $item['poz'] ?></div>
	<div class="col-xs-7 forprint"><?= $item['name'] ?>	</div>
	<div class="col-xs-1 alright kol forprint"><?=  $item['kol']?></div>
	<div class="col-xs-1 alcenter kol forprint"><?= $item['ed'] ?></div>	
	<div class="col-xs-1 alright price forprint"><?= number_format($item['price'],2,'.',' '); ?></div>
	<div class="col-xs-1 alright price forprint"><?php $itog=$itog+$item['price']*$item['kol']; echo number_format($item['price']*$item['kol'], 2, '.', ' ') ; ?></div>
	</div>
<?php 
} // if
?>


<?php } 
if (!$flag_work) {
?>

<h4 style="text-align:right">ИТОГО:  Материалы  <?= number_format($itog_element ,2,'.',' ') ?> +  Работа  <?=  number_format($itog_work ,2,'.',' ')  ?>  =  <?=  number_format($itog ,2,'.',' '); ?> Руб </h4>

<?php 
}
} // foreach zabor_item
?>


<div class="row" style="background-color:gray; color:white; padding:5px 10px; font-size:16px">Итого за работы и материалы = <?= number_format($smeta_itog ,2,'.',' ') ?> Руб </div>


<?php

$script = <<< JS

$(document).on('change', '#generatesmeta-document', function() {
	add=$(this).val();
	
	if ($(this).val()=='texsmeta')  add='printsmeta' + "&minusa=show";
	
	location.href="/web/index.php?r=site/" + add;
});

	
JS;

$this->registerJs($script, yii\web\View::POS_READY);

?>

