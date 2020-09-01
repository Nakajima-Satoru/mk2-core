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

	$getDbConnect=Config::get("database");
	if(is_array($getDbConnect)){
		foreach($getDbConnect as $field=>$values){
			OrmDo::setDo($field,$values);
		}
	}

}catch(\Exception $e){}

class Table extends CoreBlock{

	public $dbName="default";
	public $prefix=null;

	public function __construct($option=[]){
		parent::__construct($option);

		$getDbConnect=Config::get("database");
		if(!empty($getDbConnect[$this->dbName])){
			$getDbConnect=$getDbConnect[$this->dbName];
		}
		else{
			$getDbConnect=null;
		}

		if(!empty($getDbConnect["prefix"])){
			$this->prefix=$getDbConnect["prefix"];
		}

		$this->orm=new Orm($this);
		$this->orm->prefix=&$this->prefix;
		$this->orm->PdoDriveName=&$this->dbName;
		$this->orm->table=&$this->table;

	}

	public function setSchema($database){

		if(!empty($database["prefix"])){
			$this->prefix=$database["prefix"];
		}
		else{
			$this->prefix=null;
		}
		$this->orm->setSchema(null,$database);

	}

	public function connectCheck(){
		return $this->orm->connectCheck();
	}
	/**
	 * query
	 */
	public function query($sql){
		return $this->orm->query($sql);
	}

	/**
	 * select
	 */
	public function select($option=null){
		return $this->orm->select($option);
	}

/*
	public $OrmDoExist=true;

	public $dbName="default";
	public $prefix=null;

	public function __construct($option=[]){
		parent::__construct($option);

		$database=Config::get("database");
		if(!empty($database[$this->dbName]["prefix"])){
			$this->prefix=$database[$this->dbName]["prefix"];
		}

		$this->orm=new Orm($this);

	}
	public function setSchema($dbSchema){
		return $this->orm->setSchema($dbSchema);
	}
	public function query($sql){
		return $this->orm->query($sql);
	}

	public function select($option=null){
		return $this->orm->select($option);
	}

	public function show(){
		return $this->orm->show();
	}

	public function save($data=[],$option=[]){
		return $this->orm->save($data,$option);
	}

	public function delete($data=[]){
		return $this->orm->delete($data);
	}

	public function migrate($data=[],$makeSchema=false){
		return $this->orm->migrate($data,$makeSchema);
	}

	public function associate($params=null){
		return $this->orm->associate($params);
	}

	# changeDbName

	public function changeDbName($dbName){
		$this->dbName=$dbName;
		$this->orm->ormSetting($this->table,$this->dbName);
		return $this;
	}

	# changeDbTable

	public function changeDbTable($tableName){
		$this->table=$tableName;
		$this->orm->ormSetting($this->table,$this->dbName);
		return $this;
	}

	# hasMany
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

			$this->orm->associate["hasMany"]=$setParams;

		}

		return $this;
	}

	# hasOne
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

			$this->orm->associate["hasOne"]=$setParams;

		}

		return $this;
	}

	# belongsTo
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

			$this->orm->associate["belongsTo"]=$setParams;

		}

		return $this;
	}

	public function getSqlLog(){
		return $this->orm->getSqlLog();
	}
	public function resetSqlLog(){
		$this->orm->resetSqlLog();
	}

	# selectBefore
	public function selectBefore($type){}

	# selectAfter

	public function selectAfter($output,$type){}

	# showBefore
	public function showBefore(){}

	# showAfter
	public function showAfter($output){}

	# saveBefore
	public function saveBefore($input){}

	# saveAfter
	public function saveAfter($output){}

	# deleteBefore
	public function deleteBefore(){}

	# deleteAfter
	public function deleteAfter(){
	*/
}