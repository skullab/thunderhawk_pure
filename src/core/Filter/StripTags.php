<?php

namespace Thunderhawk\Filter;
use Thunderhawk\Filter\FilterHandlerInterface;
class StripTags implements FilterHandlerInterface{
	protected $allowable_tags = array();
	public function allowTag($tag){
		$this->allowable_tags[] = $tag ;
	}
	public function removeTag($tag){
		if(false !== $key = array_search($tag, $this->allowable_tags)){
			unset($this->allowable_tags[$key]);
		}
	}
	public function filter($value) {
		return strip_tags($value,implode("",$this->allowable_tags));
	}

}