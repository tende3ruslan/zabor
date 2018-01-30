<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'СТОИМОСТЬ МАТЕРИАЛОВ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<h3><?= Html::encode($this->title) ?></h3>
<br/>
<?php if ($dataproviders): ?>	

<?php
 foreach ($dataproviders as $dataprovider) {
?>
<a href="<?= Url::toRoute(['site/listelementtypes', 'type_id' =>$dataprovider['type_id'] ]); ?>"><?= $dataprovider['name']; ?></a><br/>

<?php
 }
?>

<?php else: ?>
Нет доступа, введите пароль.<br/>
<?php endif ?>
</div>
<br/><br/><br/><br/><br/>
<code><?= __FILE__ ?></code>