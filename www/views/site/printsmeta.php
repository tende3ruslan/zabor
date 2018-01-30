<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

  



function otnimalka (&$arr_minus,$attr, $zabortype, $table, $kol) {
	
	foreach ($arr_minus as $item) {
	  if (($item['attr']==$attr ) && ($item['zabortype']==$zabortype) && ($item['table']!='-')) {
		  $kol=$kol+$item['kol'];
		  break;
	  }		  
	}
	
	return $kol;
}
?>




<?php
 $zabor_elements=['proflist'=>'Забор из профнастила','shtaket'=>'Забор из штакета', 'kalitka'=>'Калитка', 'vorota'=>'Распашные ворота','otkatnievorota'=>'Откатные ворота',
 'fundament'=>'Ленточный фундамент', 'parkovka'=>'Парковка', 'svai'=>'Сваи', 'kanava'=>'Вьезд через канаву','transport'=>'Транспорт'];
 $cl=$client->readClientId($id_client);
 $smeta_itog=$printsmeta->summaSmeta($cl->id_client); 

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
$form->field($gs_mail, 'document')
    ->dropDownList($gs_mail->document_text,
    [ 'options' => [$gs_mail->document=>['selected' => 'selected']]]
		  
	)->label(false);
	?>
</div>

<div class="col-xs-2 alcenter"><?= Html::submitButton('РАСПЕЧАТАТЬ', ['id'=>'btn-print-smeta', 'class'=>'print', 'onclick'=>'print();return false;','style'=>'font-size:19px']); ?></div>

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

</div>
<div class="col-xs-6">
</div>

</div>

<h3  style="text-align:center;margin-bottom:50px">ТЕХНИЧЕСКОЕ ЗАДАНИЕ № <?= $cl->dogovor ?><br/><br/>ОБЩАЯ СУММА=<?= number_format($smeta_itog ,2,'.',' ') ?> Руб</h3>

<?php


 $num=1;
 foreach ($zabor as $zabor_item) {
	 if (substr($zabor_item['type'], 0, 4)=='calc') $zabor_item['type']=substr($zabor_item['type'], 4);
 ?>

 <?php $sm=$printsmeta->readSmetaForSmetaId($cl->id_client,$zabor_item['type'],$zabor_item['smeta_id']); ?>
 
 <?php if ($sm) { ?>
 
  <h3><?= $zabor_elements[$zabor_item['type']] ?></h3>

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
//'user_id','smeta_id','attr','table','type','kol','price'
$itog=0;
$itog_element=0;
$itog_work=0;
$flag=true;

 foreach ($sm as $i=>$item) {
 if (($item['table'][0]=='w') && $flag){
	 $flag=false;
?>
<div style="text-decoration:underline; font-weight:700">cтоимость работ</div>
<?php
 }
 
 if ($flag) $itog_element+=$item['price']*$item['kol']; else $itog_work+=$item['price']*$item['kol'];
 
 //показываем смету с минусуемыми материалами или без минусуемых
 
 if  ($printsmeta->smeta_type=='minusa_hide')  $kol=otnimalka($arr_minus, $item['attr'], $item['zabortype'], $item['table'], $item['kol']);
 if  ($printsmeta->smeta_type=='minusa_show') $kol=$item['kol'];
//echo "attr= {$item['attr']}, zabortype={$item['zabortype']}, table={$item['table']}, kol={$item['kol']}";

 ?>
 
 
 <!-- Отображаем смету с минусуемыми материалами -->
 
<?php if ((($printsmeta->smeta_type=='minusa_show')  &&  ($item['price'] > 0)  &&  ($kol!=0))  || 
 (($printsmeta->smeta_type=='minusa_hide')  &&  ($kol>0)  &&  ($item['price'] > 0) && ($item['table']!='-') )) {?>

	<div class="row line" style="border-bottom:1px dotted #000">
	<div class="col-xs-1"><?= $num ?></div>
	<div class="col-xs-7">
	
	<?php
	 // выводим слово профлист для  e6 - не экологично 
		echo (($item['table']=='e6') && ($item['zabortype']=='proflist') || (($item['table'] == '-') && ($kol>0))) ? 'Профлист: ' :'';
		
	?>
	
	<?= ($item['table'][0]!='w') ? $printsmeta->getelement_name($item['attr']).' '.$item['type'] :  $printsmeta->getwork_name($item['attr']).' '.$item['type']?>
	</div>
	<div class="col-xs-1 kol" <?= ($kol<0) ? 'style="background-color:yellow"': '' ?>><?=  $kol ?></div>
	<div class="col-xs-1 alcenter kol"><?= $item['ed'] ?></div>	
	<div class="col-xs-1 alright price"><?= number_format($item['price'],2,'.',' '); ?></div>

	<div class="col-xs-1 alright price"><?php $itog=$itog+$item['price']*$kol; echo number_format($item['price']*$kol, 2, '.', ' ') ; ?></div>
	</div>
<?php $num++;	 ?>
<?php }
} ?>
<h4 style="text-align:right">ИТОГО:  Материалы  <?= number_format($itog_element,2,'.',' ') ?> +  Работа  <?= number_format($itog_work,2,'.',' ') ?>  =  <?=  number_format($itog,2,'.',' ')?> Руб </h4>

<?php

 }
 }
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

