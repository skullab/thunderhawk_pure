<?php

namespace Thunderhawk\Parser;

class Ini {
	
	protected  $filename = null;
	protected  $readonly = false;
	protected  $locked = false;
	protected  $open_mode = 'r+';
	protected  $file_stream = null;
	protected  $configuration = null;
	
	private $booleans = array (
			'1' => true,
			'true' => true,
			'TRUE' => true,
			'0' => false,
			'false' => false,
			'FALSE' => false,
			'' => false 
	);
	private $nulls = array (
			'null' => null,
			'NULL' => null 
	);
	private $safeHaltCompiler = ';<?php die(); __halt_compiler(); ?>' ;
	
	public function __construct($filename, $readonly = false, $locked = false) {
		$this->filename = ( string ) $filename;
		$this->readonly = ( bool ) $readonly;
		$this->open_mode = $this->readonly ? 'r' : 'r+';
		$this->locked = $locked;
		$this->parse ();
	}
	public function read($filename = null) {
		if ($this->locked && ! is_null ( $filename )) {
			$message = sprintf ( 'This parser is locked ! Impossible to read file : %s', $filename );
			throw new \Exception ( $message );
		}
		$this->filename = is_null ( $filename ) ? $this->filename : $filename;
		$this->parse ();
		return $this->configuration;
	}
	public function parse($process_section = true, $scanner_mode = INI_SCANNER_RAW) {
		if (file_exists ( $this->filename )) {
			$this->configuration = parse_ini_file ( $this->filename, $process_section, $scanner_mode );
			if (false === $this->configuration) {
				$message = 'Parse ini file error';
				throw new \Exception ( $message );
			}
		} else {
			$message = sprintf ( 'File "%s" does not exist', $this->filename );
			throw new \Exception ( $message );
		}
		//var_dump ( $this->configuration );
		$this->normalizeConfiguration ( $this->configuration );
		//var_dump ( '-> normalized ->', $this->configuration );
	}
	public function write($configuration = null) {
		if ($this->readonly) {
			$message = sprintf ( 'The file "%s" is in read-only mode' , $this->filename);
			throw new \Exception ( $message );
		}
		
		$this->configuration = is_array( $configuration ) ? $configuration : $this->configuration ;
		$content = $this->safeHaltCompiler . PHP_EOL . PHP_EOL ;
		$content .= is_string($configuration) ? $configuration : $this->createContentStream ( $this->configuration );
		$this->lock ();
		
		if (false === $bytes = fwrite ( $this->file_stream, $content )) {
			$this->unlock ();
			$message = sprintf ( 'Impossible to write on file "%s"', $this->filename );
			throw new \Exception ( $message );
		}
		
		ftruncate($this->file_stream, $bytes);
		$this->unlock ();
	}
	public function save(){
		$this->write();
	}
	public function lock($operation = LOCK_EX) {
		if (is_null ( $this->file_stream )) {
			$this->file_stream = fopen ( $this->filename, $this->open_mode );
		}
		if (! flock ( $this->file_stream, $operation )) {
			fclose($this->file_stream);
			$this->file_stream = null ;
			$message = sprintf ( 'Impossible to lock file %s', $this->filename );
			throw new \Exception ( $message );
		}
	}
	
	public function unlock() {
		if (! is_null ( $this->file_stream )) {
			fflush ( $this->file_stream );
			flock ( $this->file_stream, LOCK_UN );
			fclose ( $this->file_stream );
			$this->file_stream = null;
		}
	}
	public function __get($name){
		if(isset($this->configuration[$name])){
			return $this->configuration[$name] ;
		}
		return null ;
	}
	public function __set($name,$value){
		if ($this->readonly) {
			$message = sprintf ( 'The file "%s" is in read-only mode' , $this->filename);
			throw new \Exception ( $message );
		}
		if($this->locked && !isset($this->configuration[$name])){
			$message = sprintf ( 'This parser is locked ! Impossible to set : %s', $name );
			throw new \Exception ( $message );
		}
		$this->configuration[$name] = $value ;
	}
	public function getConfiguration(){
		return $this->configuration ;
	}
	public function setConfiguration(array $configuration){
		$this->configuration = $configuration ;
	}
	private function createContentStream($data) {
		$content = '';
		foreach ( $data as $key => $value ) {
			if (is_array ( $value )) {
				$content .= PHP_EOL . '[' . $key . ']' . PHP_EOL;
				foreach ( $value as $sub_key => $sub_value ) {
					$content .= '	' . $sub_key . ' = ' . $this->parseValue ( $sub_value ) . PHP_EOL;
				}
			} else {
				$content .= PHP_EOL . $key . ' = ' . $this->parseValue ( $value ) . PHP_EOL;
			}
		}
		return $content;
	}
	private function normalizeConfiguration(&$data) {
		if (! is_array ( $data ))
			return;
		foreach ( $data as $key => $value ) {
			if (is_array ( $value )) {
				foreach ( $value as $sub_key => $sub_value ) {
					// var_dump($this->parseValue($sub_value,true));
					$data [$key] [$sub_key] = $this->parseValue ( $sub_value, true );
				}
			} else {
				// var_dump($this->parseValue($value,true));
				$data [$key] = $this->parseValue ( $value, true );
			}
		}
	}
	private function parseValue($value, $read = false) {
		//if(!$read)var_dump ( $value );
		
		if (is_null ( $value )) {
			$value = $read ? null : 'null';
			//var_dump ( 'parsing null : ', $value );
			return $value;
		}
		if ($this->isBool ( $value )) {
			// $value = $read ? ((bool)$value):($value === true ? 'true' : 'false') ;
			$value = $read ? $this->booleans [$value] : $this->toBoolString ( $this->booleans [$value] );
			//var_dump ( 'parsing bool : ', $value );
			return $value;
		}
		if (is_string ( $value )) {
			if ($read && is_numeric ( $value )) {
				$value = $this->isFloat ( $value ) ? ( float ) $value : ( int ) $value;
				//var_dump ( 'parsing numeric : ', $value );
				return $value;
			}
			$value = $read ? 
				(array_key_exists ( $value, $this->nulls ) ? $this->nulls [$value] : $value) : 
				(array_key_exists ( $value, $this->nulls ) ? $this->nulls [$value] : '"'.$value.'"');
			//var_dump ( 'parsing string : ', $value );
			return $value;
		}
		if (!$read && is_numeric ( $value )) {
			$value = $this->isFloat ( $value ) ? ( float ) $value : ( int ) $value;
			//var_dump ( 'parsing numeric : ', $value );
			return $value;
		}
		
	}
	private function isBool($value) {
		if (array_key_exists ( ( string ) $value, $this->booleans ))
			return true;
		return false;
	}
	private function isFloat($value) {
		return ((int)$value != $value);
	}
	private function toBoolString($value) {
		if ($value === true)
			return 'true';
		return 'false';
	}
}