<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Smeta extends ActiveRecord {

    public static function tableName()
    {
        return '{{smeta}}';
    }
	
	
	public function rules()
    {
        return [
            [['user_id', 'smeta_id', 'attr','type','kol','price'], 'required'],
			[['zabortype', 'ed'], 'string']
        ];
    }
	
	public function readNastil($flag)  { //читаем список с таблици element

		if ($flag==0) {
		 $element = Element::find()->orderBy('id')->asArray()->all();
		} else {
		 $element = Element::find()->select(['attr','name'])->where(['flag' => $flag])->orderBy('id')->asArray()->all();	
		}
		$arr=array();
		foreach ($element as $item) {
			
			$arr[$item['attr']]=$item['name'];
			
		}
		return $arr;
	  }
	  
	
	public function readNastilAttr($flag)  { //читаем список с таблици element

		  $items=$this->readNastil(1);
		  $arr=array_keys( $items);
		  
		return $arr;
	  }
	
	
	public function getSmetaAttr($id_client, $attr) 
	{
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed', 'type','kol','price'])->
		where(['user_id' => $id_client, 'attr' => $attr])->asArray()->one();
		
		return $smeta;
	}
	
	public function getSmetaAttrTT($id_client, $attr,$zabortype,$table) //не использовалось, не проверена
	{
		//выбрать строку сметы для конкретного клиента, типа забора и работ или материалов table=e/w;
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed', 'type','kol','price'])->
		where(['user_id' => $id_client, 'attr' => $attr, 'zabortype'=>$zabortype])->andWhere(['like','table',$table])->asArray()->one();
		
		return $smeta;
	}
	
	public function getSmetaPrice($id_client, $attr,$zabortype) 
	{
		//выбрать строку сметы для конкретного клиента, типа забора
		
		$smeta = Smeta::find()->select(['price'])->
		where(['user_id' => $id_client, 'attr' => $attr, 'zabortype'=>$zabortype])->asArray()->one();
		
		return $smeta['price'];
	}
	
	public function getSmetaTable($id_client, $table) 
	{
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed', 'type','kol','price'])->
		where(['user_id' => $id_client, 'table' => $table])->asArray()->all();
		return $smeta;
	}
	
	
	 public function readSmeta($id_client,  $smeta_id)

    {
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client, 'smeta_id' => $smeta_id])->orderBy('table')->asArray()->all();
		
		return $smeta;
    }
	
	
	public function summaSmeta($id_client,  $smeta_id)

    {	
		//'user_id','smeta_id','attr','table','type','kol','price'
		
		$sql="SELECT SUM(summa) as itogo FROM (SELECT (kol*price) as summa FROM smeta WHERE (user_id = '".$id_client."') AND (smeta_id ='".$smeta_id."') ) as sum_table";
		$command = Yii::$app->db->createCommand($sql);
		$sum = $command->queryScalar();

		if ($sum>0) {
			$sql="UPDATE zabor SET summa=".$sum." WHERE (id_client = ".$id_client.") AND (smeta_id =".$smeta_id.") ";
			$command = Yii::$app->db->createCommand($sql)->execute();
		}

		return $sum;
    }
	
	
	
	public function delete_smeta($user_id, $smeta_id) 
	
	{
		Smeta::deleteAll(['user_id' => $user_id, 'smeta_id' => $smeta_id]);
		return ;
	}
	
}	

?>