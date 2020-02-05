<?php

/**
 * mk2 Table Class
 * 
 * Class for performing database management.
 * Please put the business logic not in here but in the higher class Model class.
 * 
 * @copyright	 Copyright (C) Nakajima Satoru. 
 * @link		 https://www.mk2-php.com/
 * 
 */

namespace mk2\core;

# use mk2 ORM
use mk2\orm\Orm;
use mk2\orm\OrmDo;

try{

	if(!empty(Config::get("database"))){
		OrmDo::setSchema(Config::get("database"));
	}

}catch(\Exception $e){
	throw new \Exception($e);
}

class Table extends Orm{

	use traitCoreBlock;

	public $OrmDoExist=true;

	public $dbName="default";
	

	public function __construct($option=[]){

		if(empty($option["setSchema"])){
			if(!empty($this->setSchema)){
				$option["setSchema"]=$this->setSchema;
				unset($this->setSchema);
			}
		}

		if(!empty($option["setSchema"])){
			$PdoDriveName=hash("sha256",json_encode($option["setSchema"]));
			OrmDo::setSchemaAdd($PdoDriveName,$option["setSchema"]);
			$this->dbName=$PdoDriveName;
			$this->PdoDriveName=$PdoDriveName;
			unset($option["setSchema"]);
		}

		# option setting
		if(!empty($option)){
			foreach($option as $key=>$o_){
				$this->{$key}=$o_;
			}
		}

		// if table name not existed, auto create table name.
		if(empty($this->table)){
			$tableName=get_class($this);
			$tableName=explode("\\",$tableName);
			$tableName=$tableName[count($tableName)-1];
			$tLenCount=strrpos($tableName,"Table");
			$tableName=substr($tableName,0,$tLenCount);
			$this->table=strtolower($tableName);
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
		
		$this->table=$tableName;
		$this->_settingsModel();
		
		return $this;
	}

	# associate

	public function associate($params=[]){

		if(!empty($params)){

			if(!empty($params["hasMany"])){
				$this->hasMany($params["hasMany"]);
			}
			if(!empty($params["hasOne"])){
				$this->hasOne($params["hasOne"]);
			}
			if(!empty($params["belongsTo"])){
				$this->belongsTo($params["belongsTo"]);
			}
		}
		else
		{
			$this->associate=[];
		}

		return $this;

	}

	/**
	 * hasMany
	 */
	public function hasMany($params=[]){

		if(!empty($params)){

			$setParams=[];

			$this->setTable($params);

			foreach($params as $key=>$p_){
	
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
	
				$p_["opject"]=$this->Table->{$modelName};
	
				$setParams[$modelName]=$p_;
	
			}

			$this->associate["hasMany"]=$setParams;

		}

		return $this;
	}

	/**
	 * hasOne
	 */
	public function hasOne($params=[]){

		if(!empty($params)){

			$setParams=[];

			$this->setTable($params);

			foreach($params as $key=>$p_){

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

				$p_["opject"]=$this->Table->{$modelName};

				$setParams[$modelName]=$p_;

			}

			$this->associate["hasOne"]=$setParams;

		}

		return $this;
	}

	/**
	 * belongsTo
	 */
	public function belongsTo($params=[]){

		if(!empty($params)){

			$setParams=[];

			$this->setTable($params);

			foreach($params as $key=>$p_){

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

				$p_["opject"]=$this->Table->{$modelName};

				$setParams[$modelName]=$p_;

			}

			$this->associate["belongsTo"]=$setParams;

		}

		return $this;
	}
}