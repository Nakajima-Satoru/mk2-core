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
		OrmDo::setSchema($getDbConnect);
		foreach($getDbConnect as $field=>$values){
			OrmDo::setDo($field,$values);
		}
	}

}catch(\Exception $e){}

class Table extends CoreBlock{

	public $dbName="default";
	public $prefix=null;
	public $primaryKey="id";

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

	/**
	 * show
	 */
	public function show(){
		return $this->orm->show();
	}

	/**
	 * save
	 */
	public function save($data=[],$option=[]){
		return $this->orm->save($data,$option);
	}

	/**
	 * delete
	 */
	public function delete($data=[]){
		return $this->orm->delete($data);
	}

	/**
	 * migrate
	 */
	public function migrate($data=[],$makeSchema=false){
		return $this->orm->migrate($data,$makeSchema);
	}

	/**
	 * associate
	 */
	public function associate($params=null){
		return $this->orm->associate($params);
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

	public function transaction($mode="BEGIN"){
		return $this->orm->transaction($mode);
	}

	public function tsBegin(){
		return $this->orm->tsBegin();
	}

	public function selectBefore($type){}
	public function selectAfter($output,$type){}
	public function showBefore(){}
	public function showAfter($output){}
	public function saveBefore($input){}
	public function saveAfter($output){}
	public function deleteBefore(){}
	public function deleteAfter(){}

}