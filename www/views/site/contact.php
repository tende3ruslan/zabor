<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<h1><?= Html::encode($this->title) ?></h1>

<?php if ($passtrue): ?>

Скоро здесь будет информация о клиентах.
	
<?php else: ?>
Нет доступа, введите пароль.<br/>
<?php endif ?>
</div>
<br/><br/><br/><br/><br/>
<code><?= __FILE__ ?></code>