<?php

namespace Thunderhawk\Parser;
use Thunderhawk\Parser\Ini;

class Configuration extends Ini{
	
	private $configuration_object ;
	
	public static function newSet(){
		return new  \stdClass() ;
	}
	
	public function __construct($configuration = null,$readonly = false,$locked= false){
		
		$this->readonly = $readonly ;
		$this->open_mode = $this->readonly ? 'r' : 'r+';
		$this->locked = $locked ;
		$this->configuration_object = new \stdClass();
		$this->configuration = is_array($configuration) ? $configuration : array();
		
		if(!is_null($configuration) && is_string($configuration)){
			parent::__construct($configuration,$readonly,$locked);
		}
		$this->sync();
	}
	public function read($filename = null){
		parent::read($filename);
		$this->sync();
		return $this->configuration ;
	}
	public function write($configuration = null){
		$this->sync(true);
		parent::write($configuration);
	}
	public function save($filename = null){
		$this->filename = is_null($filename) ? $this->filename : $filename ;
		if(file_exists($this->filename)){
			$this->write();
		}else if(!is_null($this->filename)){
			$this->open_mode = 'w' ;
			$this->write();
		}else{
			$message = 'Impossible to save file : no filename declared' ;
			throw new \Exception($message);
		}
	}
	public function __get($name){
		if(isset($this->configuration[$name])){
			return $this->configuration_object->{$name} ;
		}
	}
	
	public function __set($name,$value){
		parent::__set($name, $value);
		$this->sync();
	}
	public function getConfigurationObject(){
		return $this->configuration_object ;
	}
	public function getConfiguration(){
		$this->sync(true);
		return parent::getConfiguration();
	}
	public function extractSection($section){
		return  @(array)$this->configuration_object->{$section} ;
		
	}
	public function merge(Configuration $config){
		$in = $config->getConfiguration();
		$this->configuration = array_replace_recursive($this->configuration,$in);
		$this->sync();
	}
	protected function configurationToObject($configuration){
		if(is_array($configuration)){
			return (object)array_map(array($this,__FUNCTION__), $configuration) ;
		}else{
			return $configuration ;
		}
	}
	
	protected function configurationToArray($configuration){
		if(is_object($configuration)){
			$configuration = get_object_vars($configuration);
			if(is_array($configuration)){
				return array_map(array($this,__FUNCTION__), $configuration);
			}
		}else{
			return $configuration ;
		}
	}
	protected function sync($firstArray = false){
		if(!$firstArray){
			$this->configuration_object = $this->configurationToObject($this->configuration);
		}else{
			$this->configuration = $this->configurationToArray($this->configuration_object);
		}
	}
}