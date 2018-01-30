<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'СТОИМОСТЬ МАТЕРИАЛОВ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<h2><?= Html::encode($this->title) ?></h2>
	
<?php if ($passtrue): ?>
<?php $form = ActiveForm::begin(); ?>

<div class="pull-right"><?= Html::submitButton('Сохранить', ['class' => 'submit']) ?></div>
<br/><br/>

	<span style="color:red"><?= $message ?></span>
	<?php foreach ($elementnames as $names) { ?>
	<div class="title"> <?php echo $names['name']; ?> </div>
	
	<div class="subtitle">
	
	 <?php  foreach ($elementtypes as $i=>$types) { ?>

		<?php if (($types['type']==$names['type_id']) and ($types['type_kol']!='s')) : ?>


		<div class="col-xs-6"> 
		<div class="col-xs-8 label_name"><?= $types['Id'].') '.$types['name']; ?> 
		<?= (($names['type_id']>=1200) && ($names['type_id']<=1300)) ? '-'.substr($types['attr'],-3) :'' ?>
		<?= ($types['type_kol']=='m')? '(м)':'(шт.)' ?>
		<?php ($types['kol']>0 ) ? '('. number_format ( $types['kol'],2 ) . ')' : '' ?>
		</div>
		
		<div class="col-xs-4">
		<?php $i=$types['Id']; ?>
		<?= $form->field($model,"inputdata[$i][Id]")->hiddenInput()->label(false); ?>
		<?= $form->field($model,"inputdata[$i][price]")->label(false);  ?>

		</div>		
		</div>

		<?php endIf; ?>

	<?php } ?>
	</div>

	<?php } ?>
	
	
<div class="pull-right"><?= Html::submitButton('Сохранить', ['class' => 'submit']) ?></div>
<?php ActiveForm::end(); ?>




<?php else: ?>
Нет доступа, введите пароль.<br/>
<?php endif ?>
</div>
<br/><br/><br/><br/><br/>
<code><?= __FILE__ ?></code>