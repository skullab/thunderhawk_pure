<?php

namespace Thunderhawk\Mvc\Model\Criteria;

interface CriteriaInterface {
	public function setModelName($modelName);
	public function getModelName();
	public function bind(array $bindParams);
	public function bindTypes(array $bindTypes);
	public function where($conditions);
	public function conditions($conditions);
	public function orderBy($orderColumns,$mode);
	public function limit($limit, $offset);
	public function forUpdate($forUpdate);
	public function sharedLock($sharedLock);
	public function andWhere($conditions, array $bindParams = array(), array $bindTypes = array());
	public function orWhere($conditions, array $bindParams = array(), array $bindTypes = array());
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