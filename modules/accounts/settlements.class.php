<?php
/** Settlement
*
*/
class Settlements{
	public $title='', $date, $currency, $user;
	public $rows = array();

	public function __construct($id=''){
		global $lang;
		$this->accms = new AccMS();
		if($id != ''){	
			$settl = do_query_obj("SELECT * FROM transactions WHERE id=$id", ACCMS_Database, $this->accms->ip);	
			if(isset( $settl->id )){
				foreach($settl as $key =>$value){
					$this->$key = $value;
				}
			}	
		}
	}
	
	public function _save(){
		if(count($this->rows) >0){	
			if($this->id = do_insert_obj($this, "transactions", ACCMS_Database, $this->accms->ip)){
				foreach($this->rows as $row){
					$row['trans_id'] = $this->id;
					if(isset($row['code'])){
						$account = new Accounts($row['code']);
						$row['main_code'] = $account->main_code;
						$row['sub_code']  = $account->sub_code;
					}
					do_insert_obj($row, "transactions_rows", ACCMS_Database, $this->accms->ip);
				}
				return $this->id;
					
			} else {
				return false;
			}
		} else {
			echo 'empty rows';
		}
	}
	
	public function getRows(){
		if(count($this->rows) == 0){
			$this->rows = do_query_array("SELECT * FROM transactions_rows WHERE trans_id=$this->id",  ACCMS_Database, $this->accms->ip);	
		} 
		return $this->rows;
	}
}