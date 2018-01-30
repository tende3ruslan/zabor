<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;

class SendSmeta extends FuncZabor {
	public $from;
	public $to;
	public $subj;
	public $content;

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
	
	    /**
     * @param string $view
     * @param string $subject
     * @param array $params
     * @return bool
     */
    public function sendMail() {
		
		\Yii::$app->mailer->compose('layouts/main-html',['content'=>$this->content]) // a view rendering result becomes the message body here
		->setFrom($this->from)
		->setTo($this->to)
		->setSubject($this->subj)
		->send();
		

        return ;
    }
	/*
	public function getArrMinus( $id_client) {
		//формируем массив название минуса и минус, затем при показе сметы прогоняем все через минуса и если надо минусуем
		$smeta = Smeta::find()->select(['attr','zabortype', 'type','kol'])->where(['user_id' => $id_client, 'table' => '-'])->asArray()->all();

		return $smeta;
	}

	public function readSmetaForSmetaId($id_client,$zabor_type,$smeta_id)

    {
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client, 'zabortype'=>$zabor_type, 'smeta_id'=>$smeta_id])->orderby('table')->asArray()->all();

		return $smeta;
    }
	
    public function readSmeta($id_client,$zabor_type)

    {
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client,'zabortype'=>$zabor_type])->orderby('table')->asArray()->all();
		
		return $smeta;
    }
	
	
	
	public function getSmetaAttr($id_client, $attr) 
	{
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed', 'type','kol','price'])->
		where(['user_id' => $id_client, 'attr' => $attr])->asArray()->one();
		
		return $smeta;
	}
	
	public function getSmetaTable($id_client, $attr) 
	{
		
		$smeta = Smeta::find()->select(['user_id','smeta_id','attr','table','zabortype', 'ed','type','kol','price'])->
		where(['user_id' => $id_client, 'attr' => $attr])->asArray()->one();
		
		return $smeta;
	}
	

	
	public function summaSmeta($id_client)

    {	
		//'user_id','smeta_id','attr','table','type','kol','price'
		
		$sql="SELECT SUM(summa) as itogo FROM (SELECT (kol*price) as summa FROM smeta WHERE user_id = '".$id_client."' ) as sum_table";
		$command = Yii::$app->db->createCommand($sql);
		$sum = $command->queryScalar();

		return $sum;
    }
	
	
	
	public function delete_smeta($user_id, $smeta_id) 
	
	{
		Smeta::deleteAll(['user_id' => $user_id, 'smeta_id' => $smeta_id]);
		return ;
	}
	
	
	
	
		public function getwork($attr) {
		$element = Rabota_Type::find()->select(['id','name', 'kol'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['kol'];
	}
	
	public function getelement($attr) {
		$element = Element_Type::find()->select(['id','name', 'kol'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['kol'];
	}

	
	public function getelement_price($attr) {
		$element = Element_Type::find()->select(['price'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['price'];	
	}
	
	public function getwork_price($attr) {
		$element = Rabota_Type::find()->select(['price'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['price'];	
	}
	
	public function getelement_name($attr) {
		$element = Element_Type::find()->select(['name'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['name'];	
	}
	
	public function getwork_name($attr) {
		$element = Rabota_Type::find()->select(['name'])->where(['attr' => $attr])->limit(1)->asArray()->all();
		return $element['0']['name'];	
	}
	
	*/
	
}	

?>