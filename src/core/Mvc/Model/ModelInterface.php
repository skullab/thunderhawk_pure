<?php

namespace Thunderhawk\Mvc\Model;

interface ModelInterface {
	//public function setTransaction($transaction);
	public function getTableName();
	public function getSchema();
	public function setConnectionService($connectionService);
	public function setWriteConnectionService($connectionService);
	public function setReadConnectionService($connectionService);
	public function getReadConnectionService();
	public function getWriteConnectionService();
	//public function getReadConnection();
	//public function getWriteConnection();
	//public function setDirtyState($dirtyState);
	//public function getDirtyState();
	//public function assign($data, $dataColumnMap, $whiteList);
	//public static function cloneResultMap($base, $data, $columnMap, $dirtyState, $keepSnapshots);
	//public static function cloneResult($base, $data, $dirtyState);
	//public static function cloneResultMapHydrate($data, $columnMap, $hydrationMode);
	public static function find($parameters);
	public static function findFirst($parameters);
	public static function query($dependencyInjector);
	public static function count($parameters);
	public static function sum($parameters);
	public static function maximum($parameters);
	public static function minimum($parameters);
	public static function average($parameters);
	//public function fireEvent($eventName);
	//public function fireEventCancel($eventName);
	//public function appendMessage($message);
	//public function validationHasFailed();
	//public function getMessages();
	public function save($data, $whiteList);
	public function create($data, $whiteList);
	public function update($data, $whiteList);
	public function delete();
	//public function getOperationMade();
	public function refresh();
	//public function skipOperation($skip);
	//public function getRelated($alias, $arguments);
	//public function setSnapshotData($data, $columnMap);
	public function reset();
	public function toArray(array $columns = array());
}