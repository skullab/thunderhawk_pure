<?php

namespace Thunderhawk\Parser;

class Ini {
	private $configuration = null;
	private $filename = null;
	private $readonly = false;
	private $locked = false;
	private $open_mode;
	private $file_stream = null;
	
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
	public function parse($process_section = false, $scanner_mode = INI_SCANNER_NORMAL) {
		if (is_null ( $this->filename ))
			return;
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
	}
	public function write($configuration = null) {
		if($this->readonly){
			$message = sprintf('The file "%s" is in read-only mode');
			throw new \Exception($message);
		}
		$this->configuration = is_null($configuration) ? $this->configuration : 
		(is_array($configuration) ? $configuration : array());
		$content = $this->createContentStream($this->configuration);
		$this->lock();
		if(false === fwrite($this->file_stream, $content)){
			$this->unlock();
			$message = sprintf('Impossible to write on file "%s"',$this->filename);
			throw new \Exception($message);
		}
		$this->unlock();
	}
	public function lock($operation = LOCK_EX) {
		if (is_null ( $this->file_stream )) {
			$this->file_stream = fopen ( $this->filename, $this->open_mode );
		}
		if (! flock ( $this->file_stream, $operation )) {
			$message = sprintf ( 'Impossible to lock file %s', $this->filename );
			throw new \Exception ( $message );
		}
	}
	public function unlock() {
		if (! is_null ( $this->file_stream )) {
			fflush($this->file_stream);
			flock ( $this->file_stream, LOCK_UN );
			fclose ( $this->file_stream );
			$this->file_stream = null;
		}
	}
	private function createContentStream($data){
		$content = '' ;
		foreach($data as $key => $value){
			
		}
	}
}