<?php

namespace Thunderhawk\Mvc\Model;

use Thunderhawk\Mvc\Model\Criteria\CriteriaInterface;

class Criteria implements CriteriaInterface {	const ASC = 'ASC' ;
	const DESC = 'DESC';
	
	protected $_modelName ;
	protected $_from ;
	protected $_distinct ;
	protected $_condictions = array(
			'main' => null,
			'and' => array(),
			'or' => array()
	);
	protected $_bindParams = array();
	protected $_bindTypes = array();
	protected $_orderColumns = array(
			0 => null, // mode
			1 => null, // column name
	);
	protected $_limit ;
	protected $_offset ;
	
	public function setModelName($modelName) {
		$this->_modelName = (string)$modelName ;
	}
	public function getModelName() {
		return $this->_modelName ;
	}
	public function bind(array $bindParams) {
		$this->_bindParams = $bindTypes ;
		return $this ;
	}
	public function bindTypes(array $bindTypes) {
		$this->_bindTypes = $bindTypes ;
		return $this ;
	}
	public function where($conditions) {
		$this->_condictions['main'] = $conditions ;
		return $this ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::conditions()
	 */
	public function conditions($conditions) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::orderBy()
	 */
	public function orderBy($orderColumns,$mode = Criteria::ASC) {
		$this->_orderColumns[0] => $mode ;
		$this->_orderColumns[1] => $orderColumns;
		return $this ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::limit()
	 */
	public function limit($limit, $offset) {
		$this->_limit = (int)$limit ;
		$this->_offset = (int)$offset ;
		return $this ;
	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::forUpdate()
	 */
	public function forUpdate($forUpdate) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::sharedLock()
	 */
	public function sharedLock($sharedLock) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::andWhere()
	 */
	public function andWhere($conditions, array $bindParams = array(), array $bindTypes = array()) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::orWhere()
	 */
	public function orWhere($conditions, array $bindParams = array(), array $bindTypes = array()) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::betweenWhere()
	 */
	public function betweenWhere($expr, $minimum, $maximum) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::notBetweenWhere()
	 */
	public function notBetweenWhere($expr, $minimum, $maximum) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::inWhere()
	 */
	public function inWhere($expr, $values) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::notInWhere()
	 */
	public function notInWhere($expr, $values) {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getWhere()
	 */
	public function getWhere() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getConditions()
	 */
	public function getConditions() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getLimit()
	 */
	public function getLimit() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getOrder()
	 */
	public function getOrder() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getParams()
	 */
	public function getParams() {
		// TODO: Auto-generated method stub

	}

	/* (non-PHPdoc)
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::execute()
	 */
	public function execute() {
		// TODO: Auto-generated method stub

	}

}