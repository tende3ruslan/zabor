<?php

namespace app\models;

use yii\db\ActiveRecord;

class InputForm extends ActiveRecord {

    public static function tableName()
    {
        return '{{input_form}}';
    }
	
	
	public function rules()
    {
        return [
            [['type_calc' ,'user_id', 'smeta_id','input_attr','input_label','input_data','datetime'], 'required']

        ];
    }
	
	public function getAttr($id_client, $attr,$zabortype) 
	{
		if ($zabortype!='') 	
			$inputdata = InputForm::find()->select(['input_attr','input_label','input_data'])->
			where(['user_id' => $id_client, 'input_attr' => $attr, 'type_calc'=>$zabortype])->asArray()->one();
		else 
			$inputdata = InputForm::find()->select(['input_attr','input_label','input_data'])->
			where(['user_id' => $id_client, 'input_attr' => $attr])->asArray()->one();
			
			
			
		return $inputdata;
	}
	
	 public function readAttr($user_id, $smeta_id, $type, $attr) {
		 
		// echo $user_id.'----*'.$smeta_id.'---'.$type.'----'.$attr;
		
		 if ($smeta_id >= 0)  {
		  if ($type=='') 
			 $inputdata = InputForm::find()->select(['input_data'])->where(['user_id' => $user_id, 'input_attr' => $attr, 'smeta_id' => $smeta_id])->asArray()->one();			 
			else
			 $inputdata = InputForm::find()->select(['input_data'])->where(['user_id' => $user_id, 'type_calc' => $type, 'input_attr' => $attr, 'smeta_id' => $smeta_id])->asArray()->one();

		 } else {
			if ($type=='') 
			 $inputdata = InputForm::find()->select(['input_data'])->where(['user_id' => $user_id, 'input_attr' => $attr])->asArray()->one();	 
			else 
			 $inputdata = InputForm::find()->select(['input_data'])->where(['user_id' => $user_id, 'type_calc' => $type, 'input_attr' => $attr])->asArray()->one();	 
		 }
		 
		 
		return $inputdata['input_data']; 
		 
	 }
	 
	 
	 public function readInputForm($user_id, $smeta_id)

    {
		
		$inputdata = InputForm::find()->select(['id','type_calc' ,'user_id', 'smeta_id','input_attr','input_label','input_data','datetime'])->
		where(['user_id' => $user_id, 'smeta_id' => $smeta_id])->orderBy('id')->asArray()->all();
		
		return $inputdata;
    }
	
	public function delete_inputform($user_id, $smeta_id) 
	
	{
		InputForm::deleteAll(['user_id' =>$user_id, 'smeta_id' => $smeta_id]);
		return ;
	}
	
}	

?>