<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Element_Type extends ActiveRecord {

    public static function tableName()
    {
        return '{{element_type}}';
    }
	

	public function attributeLabels()
    {
        return [
			'Id'=>'id',
			'type'=>'Тип',
			'sub_type'=>'Подтип',
			'name'=>'Имя',
			'type_kol'=>'Мера',
			'kol'=>'Кол в единице',
			'attr'=>'Аттрибут',
			'price'=>'Стоимость',
			'ord'=>'Порядок сортировки'
        ];
    }	
		
	
	public function rules()
    {
        return [
			[['Id','type','sub_type','ord'],'integer'],
            [['name', 'attr', 'type_kol'], 'string'],
			[['kol','price'],'double']	
        ];
    }

 public function search($params, $where = null)    {

		
		if (is_numeric($type=htmlspecialchars(\Yii::$app->request->get('type_id')))) {

		
		$query = Element_Type::find()->where(['type'=>$type])->orderBy(['Id'=>SORT_ASC]);
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => [
					'pagesize' => 200
				],

			]);

			$this->load($params);

			if (!$this->validate()) {
				return $dataProvider;
			}
			
		  
	
			$query->andFilterWhere(['like', 'name', $this->name]);
			$query->andFilterWhere(['like', 'attr', $this->attr]);
			$query->andFilterWhere(['like', 'price', $this->price]);
			$query->andFilterWhere(['<>', 'price', 0]);		
			
			return $dataProvider;
		}
    }

	
    public function readAll()

    {

		$element_types = Element_Type::find()->orderBy('id')->asArray()->all();
		
		return $element_types;
    }
}

?>