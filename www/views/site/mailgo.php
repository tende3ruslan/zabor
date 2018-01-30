<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<body>
<div class="alcenter">
<h2><br/><br/>Смета отправлена на e-mail <br/><br/></h2>

<a href="<?= Url::to(['site/calc']); ?>"><?= Html::Button('К СТРАНИЦЕ КЛИЕНТА', ['id'=>'btn-client-go']) ?></a>
<a href="<?= Url::to(['site/readsmetatxt']); ?>"><?= Html::Button('ВЕРНУТЬСЯ К СМЕТЕ', ['id'=>'btn-smeta-go']) ?></a>
</div>
</body>

