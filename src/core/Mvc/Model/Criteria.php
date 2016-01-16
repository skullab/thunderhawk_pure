<?php

namespace Thunderhawk\Mvc\Model;

use Thunderhawk\Mvc\Model\Criteria\CriteriaInterface;
use Thunderhawk\Mvc\Model;
use Thunderhawk\Db\Database;
use Thunderhawk\Mvc\Model\Resultset;
use Thunderhawk\Di\InjectionInterface;
use Thunderhawk\Di\ContainerInterface;

class Criteria implements CriteriaInterface, InjectionInterface {
	const APEX = "`";
	const SELECT = " SELECT ";
	const FROM = " FROM ";
	const WHERE = " WHERE ";
	const GROUP_BY = " GROUP BY ";
	const HAVING = " HAVING ";
	const ORDER_BY = " ORDER BY ";
	const LIMIT = " LIMIT ";
	const OFFSET = " OFFSET ";
	const PROCEDURE = " PROCEDURE ";
	const INTO_OUTFILE = " INTO OUTFILE ";
	const INTO_DUMPFILE = " INTO DUMPFILE ";
	const INTO = " INTO ";
	const CHARACTER_SET = " CHARACTER SET ";
	const FOR_UPDATE = " FOR UPDATE ";
	const LOCK_IN_SHARE_MODE = " LOCK IN SHARE MODE ";
	/**
	 * Comparison type.
	 */
	const BETWEEN = " BETWEEN ";
	/**
	 * Comparison type.
	 */
	const NOT_BETWEEN = " NOT BETWEEN ";
	/**
	 * Comparison type.
	 */
	const EQUAL = " = ";
	/**
	 * Comparison type.
	 */
	const NOT_EQUAL = " <> ";
	/**
	 * Comparison type.
	 */
	const ALT_NOT_EQUAL = " != ";
	/**
	 * Comparison type.
	 */
	const GREATER_THAN = " > ";
	/**
	 * Comparison type.
	 */
	const LESS_THAN = " < ";
	/**
	 * Comparison type.
	 */
	const GREATER_EQUAL = " >= ";
	/**
	 * Comparison type.
	 */
	const LESS_EQUAL = " <= ";
	/**
	 * Comparison type.
	 */
	const LIKE = " LIKE ";
	/**
	 * Comparison type.
	 */
	const NOT_LIKE = " NOT LIKE ";
	/**
	 * Comparison for array column types
	 */
	const CONTAINS_ALL = " CONTAINS_ALL ";
	/**
	 * Comparison for array column types
	 */
	const CONTAINS_SOME = " CONTAINS_SOME ";
	/**
	 * Comparison for array column types
	 */
	const CONTAINS_NONE = " CONTAINS_NONE ";
	/**
	 * PostgreSQL comparison type
	 */
	const ILIKE = " ILIKE ";
	/**
	 * PostgreSQL comparison type
	 */
	const NOT_ILIKE = " NOT ILIKE ";
	/**
	 * Comparison type.
	 */
	const CUSTOM = " CUSTOM ";
	/**
	 * Comparison type
	 */
	const RAW = " RAW ";
	/**
	 * Comparison type for update
	 */
	const CUSTOM_EQUAL = " CUSTOM_EQUAL ";
	/**
	 * Comparison type.
	 */
	const DISTINCT = " DISTINCT ";
	/**
	 * Comparison type.
	 */
	const IN = " IN ";
	/**
	 * Comparison type.
	 */
	const NOT_IN = " NOT IN ";
	/**
	 * Comparison type.
	 */
	const ALL = " ALL ";
	/**
	 * Comparison type
	 */
	const ALL_ASTERIX = " * ";
	/**
	 * Comparison type.
	 */
	const JOIN = " JOIN ";
	/**
	 * Binary math operator: AND
	 */
	const BINARY_AND = " & ";
	/**
	 * Binary math operator: OR
	 */
	const BINARY_OR = " | ";
	/**
	 * "Order by" qualifier - ascending
	 */
	const ASC = " ASC ";
	/**
	 * "Order by" qualifier - descending
	 */
	const DESC = " DESC ";
	/**
	 * "IS NULL" null comparison
	 */
	const ISNULL = " IS NULL ";
	/**
	 * "IS NOT NULL" null comparison
	 */
	const ISNOTNULL = " IS NOT NULL ";
	/**
	 * "CURRENT_DATE" ANSI SQL function
	 */
	const CURRENT_DATE = " CURRENT_DATE ";
	/**
	 * "CURRENT_TIME" ANSI SQL function
	 */
	const CURRENT_TIME = " CURRENT_TIME ";
	/**
	 * "CURRENT_TIMESTAMP" ANSI SQL function
	 */
	const CURRENT_TIMESTAMP = " CURRENT_TIMESTAMP ";
	/**
	 * "LEFT JOIN" SQL statement
	 */
	const LEFT_JOIN = " LEFT JOIN ";
	/**
	 * "RIGHT JOIN" SQL statement
	 */
	const RIGHT_JOIN = " RIGHT JOIN ";
	/**
	 * "INNER JOIN" SQL statement
	 */
	const INNER_JOIN = " INNER JOIN ";
	/**
	 * logical OR operator
	 */
	const LOGICAL_OR = " OR ";
	/**
	 * logical AND operator
	 */
	const LOGICAL_AND = " AND ";
	protected $_messages = array();
	protected $_errorCode = '00000' ;
	
	protected $_query;
	protected $_model;
	protected $_modelName;
	protected $_distinct;
	protected $_columns;
	protected $_bindParams = array ();
	protected $_bindTypes = array ();
	protected $_conditions;
	protected $_groupBy;
	protected $_orderBy = array (
			'orderColumns' => null,
			'mode' => null 
	);
	protected $_limit = array (
			'limit' => null,
			'offset' => null 
	);
	protected $_di;
	protected $_lastConnection;
	public function __construct(ModelInterface $model = null) {
		if (! is_null ( $model )) {
			$this->setModel($model);
			$this->setModelName ( $this->getModel()->getTableName () );
		}
	}
	
	
	public function setModel(ModelInterface $model) {
		$this->_model = $model;
	}

	public function getModel() {
		return $this->_model ;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::setModelName()
	 */
	public function setModelName($modelName) {
		$this->_modelName = ( string ) $modelName;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getModelName()
	 */
	public function getModelName() {
		return $this->_modelName;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::bind()
	 */
	public function bind(array $bindParams) {
		$this->_bindParams = array_merge ( $this->_bindParams, $bindParams );
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::bindTypes()
	 */
	public function bindTypes(array $bindTypes) {
		$this->_bindTypes = array_merge ( $this->_bindTypes, $bindTypes );
		var_dump($this->_bindTypes);
		return $this;
	}
	public function distinct($flag) {
		$this->_distinct = self::DISTINCT.$flag;
		return $this;
	}
	public function columns($columns){
		$this->_columns = (string)$columns;
		return $this ;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::where()
	 */
	public function where($conditions) {
		$this->_conditions = self::WHERE . $conditions;
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::conditions()
	 */
	public function conditions($conditions) {
		if(is_null($conditions))return $this ;
		
		if(is_string($conditions)){
			$this->_conditions = ( string ) $conditions;
		}else if(is_array($conditions)){
			$_c = false ;
			if(isset($conditions[0]) || ($_c = isset($conditions['conditions']) ? $conditions['conditions'] : false)){
				$_c = $_c !== false ? $_C : $conditions[0] ;
				$this->where($_c);
			}
			if(isset($conditions['distinct']) && is_string($conditions['distinct'])){
				$this->distinct($conditions['distinct']);
			}
			if(isset($conditions['columns']) && is_string($conditions['columns'])){
				$this->columns($conditions['columns']);
			}
			if(isset($conditions['bind']) && is_array($conditions['bind'])){
				$this->bind($conditions['bind']);
			}
			if(isset($conditions['bindTypes']) && is_array($conditions['bindTypes'])){
				$this->bindTypes($conditions['bindTypes']);
			}
			if(isset($conditions['order']) && is_string($conditions['order'])){
				$this->orderBy($conditions['order']);
			}
			if(isset($conditions['limit'])){
				$this->limit($conditions['limit']);
			}
			if(isset($conditions['offset'])){
				$this->offset($conditions['offset']);
			}
			if(isset($conditions['group']) && is_string($conditions['group'])){
				$this->groupBy($conditions['group']);
			}
			if(isset($conditions['for_update']) && $conditions['for_update'] === true){
				$this->forUpdate();
			}
			if(isset($conditions['shared_lock']) && $conditions['shared_lock'] === true){
				$this->sharedLock();
			}
		}
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::orderBy()
	 */
	public function orderBy($orderColumns, $mode = false) {
		$this->_orderBy ['orderColumns'] = ( string ) $orderColumns;
		if ($mode !== false)
			$this->_orderBy ['mode'] = ( string ) $mode;
		return $this;
	}
	public function groupBy($group) {
		$this->_groupBy = ( string ) $group;
		return $this;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::limit()
	 */
	public function offset($offset){
		$this->_limit['offset'] = (int)$offset;
		return $this ;
	}
	public function limit($limit, $offset = false) {
		$this->_limit ['limit'] = ( int ) $limit;
		if ($offset !== false)
			return $this->offset($offset);
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::forUpdate()
	 */
	public function forUpdate($forUpdate = false) {
		$this->_conditions .= $forUpdate ? $forUpdate : self::FOR_UPDATE;
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::sharedLock()
	 */
	public function sharedLock($sharedLock = false) {
		$this->_conditions .= $sharedLock ? $sharedLock : self::LOCK_IN_SHARE_MODE;
		return $this;
	}
	protected function logicalWhere($logic, $conditions, array $bindParams = array(), array $bindTypes = array()) {
		$this->_conditions .= $logic . $conditions;
		$this->bind ( $bindParams );
		return $this->bindTypes ( $bindTypes );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::andWhere()
	 */
	public function andWhere($conditions, array $bindParams = array(), array $bindTypes = array()) {
		return $this->logicalWhere ( self::LOGICAL_AND, $conditions, $bindParams, $bindTypes );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::orWhere()
	 */
	public function orWhere($conditions, array $bindParams = array(), array $bindTypes = array()) {
		return $this->logicalWhere ( self::LOGICAL_OR, $conditions, $bindParams, $bindTypes );
	}
	public function andBetweenWhere($expr, $minimum, $maximum) {
		return $this->betweenWhere ( self::LOGICAL_AND . $expr, $minimum, $maximum );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::betweenWhere()
	 */
	public function betweenWhere($expr, $minimum, $maximum) {
		$this->_conditions .= ( string ) $expr . self::BETWEEN . $minimum . self::LOGICAL_AND . $maximum;
		return $this;
	}
	public function andNotBetweenWhere($expr, $minimum, $maximum) {
		return $this->notBetweenWhere ( self::LOGICAL_AND . $expr, $minimum, $maximum );
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::notBetweenWhere()
	 */
	public function notBetweenWhere($expr, $minimum, $maximum) {
		$this->_conditions .= ( string ) $expr . self::NOT_BETWEEN . $minimum . self::LOGICAL_AND . $maximum;
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::inWhere()
	 */
	public function inWhere($expr, $values) {
		if (is_array ( $values )) {
			foreach ( $values as $value ) {
				$_values .= $value . ',';
			}
			$_values = rtrim ( $_values, ',' );
		} else {
			$_values = ( string ) $values;
		}
		$this->_conditions .= ( string ) $expr . self::IN . "($_values)";
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::notInWhere()
	 */
	public function notInWhere($expr, $values) {
		if (is_array ( $values )) {
			foreach ( $values as $value ) {
				$_values .= $value . ',';
			}
			$_values = rtrim ( $_values, ',' );
		} else {
			$_values = ( string ) $values;
		}
		$this->_conditions .= ( string ) $expr . self::NOT_IN . "($_values)";
		return $this;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getWhere()
	 */
	public function getWhere() {
		return $this->getConditions () . $this->getGroupBy () . $this->getOrderBy () . $this->getLimit ();
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getConditions()
	 */
	public function getConditions() {
		return $this->_conditions;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getLimit()
	 */
	public function getLimit() {
		$clause = '';
		if (! is_null ( $this->_limit ['limit'] )) {
			$clause = self::LIMIT . $this->_limit ['limit'];
		}
		if (! is_null ( $this->_limit ['offset'] )) {
			$clause .= self::OFFSET . $this->_limit ['offset'];
		}
		return $clause;
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getOrder()
	 */
	public function getOrderBy() {
		$clause = '';
		if (! is_null ( $this->_orderBy ['orderColumns'] )) {
			$clause = self::ORDER_BY . $this->_orderBy ['orderColumns'];
		}
		if (! is_null ( $this->_orderBy ['mode'] )) {
			$clause .= $this->_orderBy ['mode'];
		}
		return $clause;
	}
	public function getGroupBy() {
		$clause = ! is_null ( $this->_groupBy ) ? self::GROUP_BY . $this->_groupBy : '';
		return $clause;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::getParams()
	 */
	public function getParams() {
		return $this->_bindParams;
	}
	public function getTypes() {
		return $this->_bindTypes;
	}
	public function getDistinct() {
		return $this->_distinct ;
		//return (! is_null ( $this->_distinct ) ? $this->_distinct : self::ALL_ASTERIX);
	}
	public function getColumns(){
		return $this->_columns ;
		//return (! is_null ( $this->_columns ) ? $this->_columns : null);
	}
	public function getWhat(){
		return (!is_null($this->getDistinct())? $this->getDistinct() : (!is_null($this->getColumns())?$this->getColumns():self::ALL_ASTERIX));
	}
	public function stripString($str) {
		return trim(str_replace ( "  ", " ", $str ));
	}
	public function resolveQuery() {
		$this->_query = self::SELECT . $this->getWhat().self::FROM . $this->getModelName () . $this->getWhere ();
		// return rtrim(ltrim(str_replace(" ", " ",$this->_query)," ")," ");
		return $this->stripString ( $this->_query );
	}
	public function setDi(ContainerInterface $di) {
		$this->_di = $di;
	}
	public function getDi() {
		return $this->_di;
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Thunderhawk\Mvc\Model\Criteria\CriteriaInterface::execute()
	 */
	public function getLastConnection($write = false){
		if($this->_lastConnection == null){
			if ($this->_model) {
				$this->_lastConnection = $write ? $this->_model->getWriteConnectionService() : $this->_model->getReadConnectionService ();
			} else {
				$this->_lastConnection = $this->getDi ()->db;
			}
		}
		return $this->_lastConnection ;
	}
	public function getErrorMessages(){
		return $this->_messages ;
	}
	public function getErrorCode(){
		return $this->_errorCode ;
	}
	public function executeRow($write = false){
		return $this->execute($write,true);
	}
	public function execute($write = false,$row = false) {
		$statement = $this->getLastConnection($write)->prepare ( $this->resolveQuery () );
		if ($statement === false)
			return false;
		
		foreach ( $this->getParams () as $placeholder => $param ) {
			
			$type = array_key_exists ( $placeholder, $this->getTypes () ) ? $this->getTypes () [$placeholder] : Model::TYPE_STRING;
			//var_dump ( $type );
			
			if(is_null($param)){
				//$param = 'NULL' ;
				$type = Model::TYPE_NULL ;
				var_dump('is null',$param,$type);
			}
			
			if (is_numeric ( $placeholder ))$placeholder ++;
			$statement->bindValue ( $placeholder, $param, $type );
		}
		
		$response = $statement->execute ();
		$statement->debugDumpParams();
		
		$this->_messages = $statement->errorInfo();
		$this->_errorCode = $statement->errorCode();
		
		if ($response === false){
			return false;
		}
		
		if($row)return $statement ;
		
		if ($this->_model) {
			$statement->setFetchMode ( Database::FETCH_CLASS | Database::FETCH_PROPS_LATE, get_class ( $this->_model ) );
		}
		
		var_dump ( $statement );
		$resultset = new Resultset ();
		while ( $obj = $statement->fetch () ) {
			$obj->reset ();
			$resultset [] = $obj;
		}
		$statement->closeCursor ();
		return $resultset;
	}
}