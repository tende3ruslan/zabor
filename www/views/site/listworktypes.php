<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = 'СТОИМОСТЬ РАБОТ';
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="font-size:12px">
<h3><a href="<?= Url::to(['site/listworknames'], true); ?>"><?= Html::encode($this->title) ?></a> / <?= $catname?></h3>
<?php if ($dataProvider): ?>	

<?php Pjax::begin(['id' => 'worklist']) ?>
<?= 
GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 

        [
            'label' => 'Название в смете',
            'format' => 'text',
			'headerOptions' => ['width' => '40%'],
			'attribute'=>'name',				
            'contentOptions' =>function ($model, $key, $index, $column){
                return ['class' => 'alleft'];
            },				
            'value' => function($data){		
                return $data['name'];
            },
        ],		
		
        [
            'label' => 'Обозначение',
            'format' => 'text',
			'headerOptions' => ['width' => '30%'],
			'attribute'=>'attr',				
            'contentOptions' =>function ($model, $key, $index, $column){
                return ['class' => 'alleft'];
            },			
            'value' => function($data){
                return $data['attr'];
            },
        ],		
		
        [
            'label' => 'Стоимость',
            'format' => 'text',
			'headerOptions' => ['width' => '25%'],
			'attribute'=>'price',			
            'contentOptions' =>function ($model, $key, $index, $column){
                return ['class' => 'alright'];
            },				
            'value' => function($data){
                return $data['price'];
            },
        ],			
		

		
         [
            'class' => 'yii\grid\ActionColumn',
            'header'=>'#', 
            'headerOptions' => ['width' => '50'],
            'template' => '{view}',
			'urlCreator'=>function($action, $model, $key, $index){
				return \yii\helpers\Url::to(['site/worklistedit', 'type'=>$model->type, 'Id'=>$model->Id]);
 }
        ],
        ],
    ]); ?>
	
	<?php Pjax::end() ?>
	



<?php else: ?>
Нет доступа, введите пароль.<br/>
<?php endif ?>
</div>
<br/><br/><br/><br/><br/>
<code><?= __FILE__ ?></code>