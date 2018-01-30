<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>




<style type="text/css">
<!--
.style3 {color: #FFFFFF; font-weight: bold; }
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
}
body {
	background-color: #FFFFFF;
}
.style4 {
	font-size: 13px;
	font-weight: bold;
}
.style5 {
	font-size: 13px;
	color: #FFFFFF;
}
-->
</style>


<body>


<!--START MAIL-->



<table width="100%" border="0" cellpadding="4">
  <tr>
    <td width="50%" ><span class="style4">Заказчик</span><br/>
Имя: <?=$client->name ?><br/>
Телефон: <?=$client->tel ?><br/>
Адрес: <?=$client->address ?><br/>
Примечание: <?=$client->comment ?><br/>	
	</td>
    <td width="50%"><div align="right" class="style4">Исполнитель: ООО "Империя Заборов"<br/></div><div align="right">
	105118, г. Москва,  Шоссе Энтузиастов дом 34, 1-34
	Офис: +7 (495) 231-21-68<br/>
	E-mail: zabor@zabor-stroim.ru<br/>
	График работы офиса — 9.00 — 19.00. <br/></div>
	</td>
  </tr>
</table>
<h3 align="center">ТЕХНИЧЕСКОЕ ЗАДАНИЕ №<?= $client->dogovor ?></h3>
<h3 align="center">ОБЩАЯ СУММА=<?= number_format($smeta_itog ,2,'.',' ')  ?> Руб </h3>


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


	<h3><?php echo $gs->zabor_elements[$item['zabortype']]; ?></h3>

<table width="100%" border="0" cellpadding="0">
  <tr>
    <td width="10%" align="center" valign="middle" bgcolor="#808080"><span class="style3">Поз</span></td>
    <td width="40%" align="center" valign="middle" bgcolor="#808080"><div align="left" class="style3">Наименование</div></td>
    <td width="10%" align="center" valign="middle" bgcolor="#808080"><span class="style3">Кол</span></td>
    <td width="10%" align="center" valign="middle" bgcolor="#808080"><span class="style3">Ед. изм. </span></td>
    <td width="15%" align="right" valign="middle" bgcolor="#808080"><span class="style3">Цена, Руб</span></td>
    <td width="15%" align="right" valign="middle" bgcolor="#808080"><span class="style3">Сумма, Руб</span></td>
  </tr>
</table>
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
 
 
<table width="100%" border="0" cellpadding="0">
  <tr>
    <td width="10%" align="center" valign="middle"><?= $item['poz'] ?></td>
    <td width="40%" align="left" valign="middle"><?= $item['name'] ?></td>
    <td width="10%" align="center" valign="middle"><?= $item['kol'] ?></td>
    <td width="10%" align="center" valign="middle"><?= $item['ed'] ?></td>
    <td width="15%" align="right" valign="middle"><?= number_format($item['price'] ,2,'.',' ') ?></td>
    <td width="15%" align="right" valign="middle"><?= number_format($item['summa'] ,2,'.',' ') ?></td>
  </tr>
</table>
<?php 
} // if
?>
<?php 
} 

if (!$flag_work) {
?>
 <?php $itog=$itog_element+$itog_work ?>
<h4 style="text-align:right">ИТОГО:  Материалы  <?= number_format($itog_element ,2,'.',' ') ?> +  Работа  <?=  number_format($itog_work ,2,'.',' ')  ?>  =  <?=  number_format($itog ,2,'.',' '); ?> Руб </h4>

<?php 
}
} 
?> 

 <table width="100%" border="0" align="left" cellpadding="4" bgcolor="#808080">
  <tr>
    <td><span class="style5">Итого за работы и материалы = <?= number_format($smeta_itog ,2,'.',' ') ?> Руб </span></td>
  </tr>
</table>



<!--END MAIL-->



</body>
