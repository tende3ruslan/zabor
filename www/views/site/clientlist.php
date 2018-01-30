<?php
use yii\grid\GridView;
use app\models\Client;
use yii\data\ActiveDataProvider;
use yii\helpers\HTML;
use yii\widgets\Pjax;
?>

<?php Pjax::begin(['id' => 'clients']) ?>
<?= 

GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 
            'dogovor',
            'name',
            'tel',
            'email',
        [
            'label' => 'Сумма',
            'format' => 'text',
			'headerOptions' => ['width' => '100'],
			'attribute'=>'summa',				
            'contentOptions' =>function ($model, $key, $index, $column){
                return ['class' => 'alright'];
            },				
            'value' => function($data){
				
                return number_format($data['summa'] ,2,'.',' ');
            },
        ],		
		
        [
            'label' => 'Дата начала.',
            'format' => 'text',
			'headerOptions' => ['width' => '100'],
			'attribute'=>'datestart',				
            'contentOptions' =>function ($model, $key, $index, $column){
                return ['class' => 'alcenter'];
            },			
            'value' => function($data){
				$d=$data['datestart'][0].$data['datestart'][1].$data['datestart'][2].$data['datestart'][3].'-'.$data['datestart'][4].$data['datestart'][5].'-'.$data['datestart'][6].$data['datestart'][7];
                return $d;
            },
        ],		
		
        [
            'label' => 'Дата заверш.',
            'format' => 'text',
			'headerOptions' => ['width' => '100'],
			'attribute'=>'dateend',			
            'contentOptions' =>function ($model, $key, $index, $column){
                return ['class' => 'alcenter'];
            },				
            'value' => function($data){
				$d=$data['dateend'][0].$data['dateend'][1].$data['dateend'][2].$data['dateend'][3].'-'.$data['dateend'][4].$data['dateend'][5].'-'.$data['dateend'][6].$data['dateend'][7];
                return $d;
            },
        ],			
		
        [
            'label' => 'Статус',
            'format' => 'text',
			'filter'=>array('1'=>'расчет','2'=>'думает','3'=>'подписан', '4'=>'в работе', '5'=>'завершено','6'=>'отменено'),
			'headerOptions' => ['width' => '130'],
			'attribute'=>'work_status',

            'contentOptions' =>function ($model, $key, $index, $column){
				if($model->work_status == '1')   $bg="ffffff";				
				if($model->work_status == '2')   $bg="f4f430";
				if($model->work_status == '3')   $bg="9ff2a1";
				if($model->work_status == '4')   $bg="f7a0a6";
				if($model->work_status == '5')   $bg="d8d4d4";
				if($model->work_status == '6')   $bg="939292";
				
                return ['class' =>'alright','style'=>'background-color:#'.$bg];
            },	
            'value' => function($data){
				$s=['1'=>'расчет','2'=>'думает','3'=>'подписан', '4'=>'в работе', '5'=>'завершено','6'=>'отменено'];
                return $s[$data['work_status']];
            },
        ],

		
         [
            'class' => 'yii\grid\ActionColumn',
            'header'=>'#', 
            'headerOptions' => ['width' => '50'],
            'template' => '{view}&nbsp;{delete}',
			'urlCreator'=>function($action, $model, $key, $index){
				return ($action==="delete") ? \yii\helpers\Url::to(['site/client'.$action,'id_client'=>$model->id_client]) : \yii\helpers\Url::to(['site/calc','id_client'=>$model->id_client]);
 }
        ],
        ],
    ]); ?>
	<?php Pjax::end() ?>