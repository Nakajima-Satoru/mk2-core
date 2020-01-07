<?php

/*

mk2 |  Table

Class for performing database management.
Please put the business logic not in here but in the higher class Model class.

Copylight(C) Nakajima Satoru 2020.

*/

namespace mk2\core;

# use mk2 ORM
use mk2\orm\Orm;
use mk2\orm\OrmDo;

try{

	if(!empty(Config::get("database"))){
		OrmDo::setDo(Config::get("database"));
	}

}catch(\Exception $e){
	throw new \Exception($e);
}

class Table extends Orm{

	use traitCoreBlock;

	public $OrmDoExist=true;

	public $dbName="default";
	

	public function __construct($option=[]){

		# option setting
		if(!empty($option)){
			foreach($option as $key=>$o_){
				$this->{$key}=$o_;
			}
		}
		
		parent::__construct($this->table,$this->dbName);

	}

	# _settingsModel

	public function _settingsModel(){

		$this->ormSetting($this->table,$this->dbName);

	}

	# changeDbName

	public function changeDbName($dbName){
		
		$this->dbName=$dbName;
		$this->_settingsModel();

		return $this;
	}

	# changeDbTable

	public function changeDbTable($tableName){
		
		$this->__construct($tableName);

		return $this;
	}

	# associate

	public function associate($params=[]){

		$setParams=[
			"hasMany"=>[],
			"hasOne"=>[],
			"belongsTo"=>[],
		];

		if(!empty($params)){

			if(!empty($params["hasMany"])){

				$this->setModel($params["hasMany"]);

				foreach($params["hasMany"] as $key=>$p_){

					if(is_int($key)){
						$modelName=$p_;
						$p_=[
							"model"=>$p_,
						];
					}
					else
					{
						$modelName=$key;
						$p_["model"]=$key;
					}

					$p_["opject"]=$this->{$modelName};

					$setParams["hasMany"][$modelName]=$p_;

				}

			}

			if(!empty($params["hasOne"])){

				$this->setModel($params["hasOne"]);

				foreach($params["hasOne"] as $key=>$p_){

					if(is_int($key)){
						$modelName=$p_;
						$p_=[
							"model"=>$p_,
						];
					}
					else
					{
						$modelName=$key;
						$p_["model"]=$key;
					}

					$p_["opject"]=$this->{$modelName};

					$setParams["hasOne"][$modelName]=$p_;

				}

			}

			if(!empty($params["belongsTo"])){

				$this->setModel($params["belongsTo"]);

				foreach($params["belongsTo"] as $key=>$p_){

					if(is_int($key)){
						$modelName=$p_;
						$p_=[
							"model"=>$p_,
						];
					}
					else
					{
						$modelName=$key;
						$p_["model"]=$key;
					}

					$p_["opject"]=$this->{$modelName};

					$setParams["belongsTo"][$modelName]=$p_;

				}
			}

			$this->associate=$setParams;

		}
		else
		{
			$this->associate=[];
		}

		return $this;

	}

}