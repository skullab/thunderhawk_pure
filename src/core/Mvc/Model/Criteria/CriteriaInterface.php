<?php

namespace Thunderhawk\Mvc\Model\Criteria;

interface CriteriaInterface {
	public function setModelName($modelName);
	public function getModelName();
	public function bind($bindParams);
	public function bindTypes($bindTypes);
	public function where($conditions);
	public function conditions($conditions);
	public function orderBy($orderColumns);
	public function limit($limit, $offset);
	public function forUpdate($forUpdate);
	public function sharedLock($sharedLock);
	public function andWhere($conditions, $bindParams, $bindTypes);
	public function orWhere($conditions, $bindParams, $bindTypes);
	public function betweenWhere($expr, $minimum, $maximum);
	public function notBetweenWhere($expr, $minimum, $maximum);
	public function inWhere($expr, $values);
	public function notInWhere($expr, $values);
	public function getWhere();
	public function getConditions();
	public function getLimit();
	public function getOrder();
	public function getParams();
	//public function fromInput ( $dependencyInjector, $modelName, $data);
	public function execute();
}