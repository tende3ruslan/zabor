<?php

namespace app\models;

use yii\BaseYii;
use app\models\FuncZabor;
use app\models\SmetaTxt;

class GenerateSmeta extends FuncZabor {

	public $usermail;
	public $document;

    public static function tableName()
    {
        return '{{smetatxt}}';
    }
	
		
	public function attributeLabels()
    {
        return [
			'usermail'=>'Email для сметы'
        ];
    }
	
	
	
	public function rules()
    {
        return [
			[['usermail'],'email']
        ];
    }	
	
	public function init() {
		
	}
	
	public function saveSmetaTxt($id_client,$percent_element,$percent_work) {
		
		\Yii::$app->db->createCommand()->delete('smetatxt', ['id_client'=>$id_client]) ->execute(); // чистим смету
		
		$client = Client::find()->where(['id_client' => $id_client])->one();
		$zabor = Zabor::find()->where(['id_client' => $id_client])->orderBy('smeta_id')->asArray()->all();

		$arr_minus=FuncZabor::getArrMinus($id_client);
		
		$num=1;
		
		foreach ($zabor as $zabor_item) {

			if (substr($zabor_item['type'], 0, 4)=='calc') $zabor_item['type']=substr($zabor_item['type'], 4);		
			
			 $sm=$this->readSmetaForSmetaId($client->id_client,$zabor_item['type'],$zabor_item['smeta_id']);		 
			 		 
			 if ($sm) {
				foreach ($sm as $i=>$item) {

				 $kol=$this->otnimalka($arr_minus, $item['attr'], $item['zabortype'], $item['table'], $item['kol']); 
				 $price=$item['price']  ;
				 $ew=$item['table'][0];
				 
				 if (($price>0) && ($kol>0) && ($ew!='-')) { // кидаем в смету только ...
					 $sm_txt=new SmetaTxt();
					 $sm_txt->id_client=$id_client;
					 $sm_txt->zabortype=$item['zabortype'];
					 $sm_txt->poz=$num;
					 $sm_txt->kol=$kol;
					 $sm_txt->ed=$item['ed'];			 
					 $sm_txt->price=($sm_txt->ew!='w') ? $price+$price*$percent_element/100 : $price+$price*$percent_work/100;
					 $sm_txt->ew=$ew;
					 $sm_txt->smeta_id=$item['smeta_id'];
					 $name='';
					 if (($item['table']=='e6') && ($item['zabortype']=='proflist')) $name='Профлист ';
					 $sm_txt->name=$name. (($sm_txt->ew!='w') ? $this->getelement_name($item['attr']).' '.$item['type'] :  $this->getwork_name($item['attr']).' '.$item['type']);				
					 $sm_txt->summa=$sm_txt->kol*$sm_txt->price;
					 $sm_txt->insert(false);
					 
					 $num++;
					 $sm_txt=null;
				 }
			 }
		}	
		}
		

	}
	
	function summaSmetaTxt ($id_client) {
		$sql="SELECT SUM(summa) as itogo FROM smetatxt where id_client=".$id_client;
		$command =\Yii::$app->db->createCommand($sql);
		$sum = $command->queryScalar();

		return $sum;		
	}

	function otnimalka ($arr_minus,$attr, $zabortype, $table, $kol) {
		
		foreach ($arr_minus as $item) {
		  if (($item['attr']==$attr ) && ($item['zabortype']==$zabortype) && ($item['table']!='-')) {
			  $kol=$kol+$item['kol'];
			  break;
		  }		  
		}
		
		return $kol;
}
	
}	

?>