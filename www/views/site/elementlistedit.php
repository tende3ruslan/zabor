<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = 'ИСПРАВИТЬ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<h3> <?= Html::encode($this->title) ?></h3>

<?php Pjax::begin(); ?>

<?php $form = ActiveForm::begin(['action' =>['site/elementlisteditsave'], 'options' => ['data-pjax' => true ], 'method' => 'post',  'id' => 'form-signup'] ); ?>

<?= $form->field($line,"Id")->hiddenInput(['value'=> htmlspecialchars($_GET["Id"])])->label(false); ?>

<div class="row">
<div class="col-xs-4">
<?= $form->field($line,"name"); ?>
</div>


<div class="col-xs-4">
<?= $form->field($line,"attr")->textInput(['readonly' => 'true']); ?>
</div>


<div class="col-xs-2">
<?= $form->field($line,"price"); ?>
</div>

<div class="col-xs-1 " style="padding-top:15px">
<?= Html::submitButton('Сохранить', ['id'=>'btn-save', 'class' => 'btn-lg btn-success']) ?> 
</div>

<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
</div>
<div class="row">
<div class="col-xs-offset-10 col-xs-2">
<br/><br/>
<a href="<?= Url::to(['site/listelementtypes', 'type_id'=>$_GET['type']], true); ?>"><?= Html::Button('< к списку', ['id'=>'btn-save', 'class' => 'btn-lg btn-default']) ?> </a>
</div>
</div>
</div>
