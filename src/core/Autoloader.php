<?php

namespace Thunderhawk;

class Autoloader {
	
	const PRIORITY_LOW = 'p_low';
	const PRIORITY_HIGH = 'p_high';
	const PRIORITY_NULL = 'p_null';
	const NAMESPACES = 'namespaces';
	const PREFIXES = 'prefixes';
	const DIRS = 'dirs';
	const CLASSES = 'classes';

	private $_is_registered = false;
	private $_base_dir = '';
	private $_namespaces = array ();
	private $_prefixes = array ();
	private $_directories = array ();
	private $_classes = array ();
	private $_priorities = array ();
	private $_extensions = array (
			'php' 
	);
	private $_prefix_separator = '_';
	private $_external_loader = null;
	
	public function __construct($basedir = '') {
		$this->_priorities [self::NAMESPACES] = self::PRIORITY_NULL;
		$this->_priorities [self::PREFIXES] = self::PRIORITY_NULL;
		$this->_priorities [self::DIRS] = self::PRIORITY_NULL;
		$this->_priorities [self::CLASSES] = self::PRIORITY_NULL;
		$this->setBaseDir ( $basedir );
	}
	public function demandLoader($className) {
		if (! $className)
			return;
		if ($this->isRegistered ())
			$this->unregister ();
		$args = func_get_args ();
		unset ( $args [0] );
		
		if (count ( $args ) == 0) {
			$this->_external_loader = new $className ();
		} else {
			$r = new \ReflectionClass ( $className );
			$this->_external_loader = $r->newInstanceArgs ( $args );
		}
	}
	public function getExternalLoader() {
		return $this->_external_loader;
	}
	public function __call($name, $arguments) {
		//var_dump ( 'call : ' . $name );
		if (is_null ( $this->_external_loader ))
			return;
		if (! method_exists ( $this->_external_loader, $name )) {
			$message = sprintf ( 'The "%s->%s()" method does not exist', get_class ( $this->_external_loader ), $name );
			throw new \BadMethodCallException ( $message );
		}
		return call_user_func_array ( array (
				$this->_external_loader,
				$name 
		), $arguments );
	}
	public function setPriority($key, $value) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (isset ( $this->_priorities [$key] )) {
			$this->_priorities [$key] = ( string ) $value;
		}
	}
	public function setPriorities(array $priorities) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		foreach ( $priorities as $key => $value ) {
			$this->setPriority ( $key, $value );
		}
	}
	public function setBaseDir($basedir) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		$this->_base_dir = ( string ) $basedir;
	}
	public function getBaseDir() {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		return $this->_base_dir;
	}
	public function register() {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		spl_autoload_register ( array (
				$this,
				'loadClass' 
		) );
		$this->_is_registered = true;
	}
	public function unregister() {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		spl_autoload_unregister ( array (
				$this,
				'loadClass' 
		) );
		$this->_is_registered = false;
	}
	public function isRegistered() {
		return $this->_is_registered;
	}
	public function registerNamespace($namespace, $path) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (is_string ( $namespace ) && is_string ( $path )) {
			$this->_namespaces [$namespace] = $this->replaceDirSeparator ( $path );
		}
		return $this;
	}
	public function registerNamespaces(array $namespaces, $merge = false) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		$namespaces = $this->formatArray ( $namespaces );
		$this->_namespaces = $merge ? array_unique ( array_merge ( $this->_namespaces, $namespaces ) ) : array_unique ( $namespaces );
		return $this;
	}
	public function registerPrefix($prefix, $path) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (is_string ( $prefix ) && is_string ( $path )) {
			$this->_prefixes [$prefix] = $this->replaceDirSeparator ( $path );
		}
		return $this;
	}
	public function registerPrefixes(array $prefixes, $merge = false) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		$prefixes = $this->formatArray ( $prefixes );
		$this->_prefixes = $merge ? array_unique ( array_merge ( $this->_prefixes, $prefixes ) ) : array_unique ( $prefixes );
		return $this;
	}
	public function registerDir($directory) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (is_string ( $directory ) && ! in_array ( $directory, $this->_directories )) {
			$this->_directories [] = $directory;
		}
		return $this;
	}
	public function registerDirs(array $directories, $merge = false) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		foreach ( array_values ( $directories ) as $index => $dir ) {
			$directories [$index] = ( string ) $dir;
		}
		$this->_directories = $merge ? array_unique ( array_merge ( $this->_directories, $directories ) ) : array_unique ( $directories );
		return $this;
	}
	public function registerClass($className, $path) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (is_string ( $className ) && is_string ( $path )) {
			$this->_classes [$className] = $this->replaceDirSeparator ( $path );
		}
		return $this;
	}
	public function registerClasses(array $classes, $merge = false) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		$classes = $this->formatArray ( $classes );
		$this->_classes = $merge ? array_unique ( array_merge ( $this->_classes, $classes ) ) : array_unique ( $classes );
		return $this;
	}
	public function addExtension($ext) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (is_string ( $ext ) && ! in_array ( $ext, $this->_extensions )) {
			$this->_extensions [] = $ext;
		}
		return $this;
	}
	public function setExtensions(array $extensions) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (empty ( $extensions ))
			return;
		$this->_extensions = ( string ) array_values ( $extensions );
	}
	public function getExtensions() {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		return $this->_extensions;
	}
	public function setPrefixSeparator($separator) {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		if (! $separator)
			return;
		$this->_prefix_separator = ( string ) $separator;
	}
	public function getPrefixSeparator() {
		if (! is_null ( $this->_external_loader ))
			return $this->__call ( __FUNCTION__, func_get_args () );
		return $this->_prefix_separator;
	}
	public function getNamespaces() {
		return $this->_namespaces;
	}
	public function getPrefixes() {
		return $this->_prefixes ;
	}
	public function getDirs() {
		return $this->_directories ;
	}
	public function getClasses() {
		return $this->_classes ;
	}
	private function formatArray(array $array) {
		$keys = array_keys ( $array );
		$values = array_values ( $array );
		foreach ( $keys as $index => $key ) {
			$keys [$index] = ( string ) $key;
		}
		foreach ( $values as $index => $value ) {
			$values [$index] = ( string ) $value;
		}
		return array_combine ( $keys, $values );
	}
	private function replaceDirSeparator($path) {
		return str_replace ( array (
				'\\',
				'/' 
		), DIRECTORY_SEPARATOR, $path );
	}
	private function loadClass($className) {
		//var_dump ( 'start load class - search for : ' . $className );
		
		$class = $className;
		$package = array ();
		$previous = '';
		$classPath = '';
		
		if (! empty ( $this->_namespaces )) {
			// search in namespaces
			while ( false !== $pos = strpos ( $className, '\\' ) ) {
				$previous .= ltrim ( $classPath . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR );
				$classPath = str_replace ( '\\', DIRECTORY_SEPARATOR, substr ( $className, 0, $pos ) );
				$className = substr ( $className, $pos + 1 );
				$namespace = $previous . $classPath;
				$package [$namespace] = $className;
				if ($this->_priorities [self::NAMESPACES] == self::PRIORITY_NULL) {
					if (isset ( $this->_namespaces [$namespace] )) {
						//var_dump ( 'found : ' . $namespace . ' - path : ' . $this->_namespaces [$namespace] );
						$filename = $this->_base_dir . $this->_namespaces [$namespace] . str_replace ( '\\', '/', $className );
						if($this->requireFile ( $filename ))return ;
					}
				}
			}
			// Namespace found : searching priority
			if (! empty ( $package )) {
				switch ($this->_priorities [self::NAMESPACES]) {
					case self::PRIORITY_LOW :
						ksort ( $package );
						break;
					case self::PRIORITY_HIGH :
						krsort ( $package );
						break;
					default :
				}
				//print_r ( $package );
				foreach ( $package as $namespace => $className ) {
					if (isset ( $this->_namespaces [$namespace] )) {
						$filename = $this->_base_dir . $this->_namespaces [$namespace] . str_replace ( '\\', '/', $className );
						if($this->requireFile ( $filename ))return;
					}
				}
			}
		}
		
		if (! empty ( $this->_prefixes )) {
			// search in prefixes
			while ( false !== $pos = strpos ( $className, $this->_prefix_separator ) ) {
				$previous .= ltrim ( $classPath . $this->_prefix_separator, $this->_prefix_separator );
				$classPath = substr ( $className, 0, $pos );
				$className = substr ( $className, $pos + 1 );
				$prefix = $previous . $classPath;
				$package [$prefix] = $className;
				if ($this->_priorities [self::PREFIXES] == self::PRIORITY_NULL) {
					if (isset ( $this->_prefixes [$prefix] )) {
						$filename = $this->_base_dir . $this->_prefixes [$prefix] . str_replace ( $this->_prefix_separator, '/', $className );
						if($this->requireFile ( $filename ))return ;
					}
				}
			}
			
			if (! empty ( $package )) {
				switch ($this->_priorities [self::PREFIXES]) {
					case self::PRIORITY_LOW :
						ksort ( $package );
						break;
					case self::PRIORITY_HIGH :
						krsort ( $package );
						break;
					default :
				}
				foreach ( $package as $prefix => $className ) {
					if (isset ( $this->_prefixes [$prefix] )) {
						$filename = $this->_base_dir . $this->_prefixes [$prefix] . str_replace ( $this->_prefix_separator, '/', $className );
						if($this->requireFile ( $filename ))return;
					}
				}
			}
		}
		// search in classes
		// TODO classes priorities
		// TODO classes like namespaces ?
		//var_dump ( 'search in classes : ' . $class );
		if (isset ( $this->_classes [$class] )) {
			$filename = $this->_base_dir . $this->_classes [$class] . str_replace ( DIRECTORY_SEPARATOR, '/', $class );
			if($this->requireFile ( $filename ))return;
		}
		// search in dirs
		switch ($this->_priorities [self::DIRS]) {
			case self::PRIORITY_LOW :
				ksort ( $this->_directories );
				break;
			case self::PRIORITY_HIGH :
				krsort ( $this->_directories );
				break;
			default :
		}
		//var_dump ( 'search in dirs : ' . $class );
		foreach ( $this->_directories as $dir ) {
			$filename = $this->_base_dir . $dir . str_replace ( DIRECTORY_SEPARATOR, '/', $class );
			if($this->requireFile ( $filename ))return;
		}
		//var_dump ( 'end load class' );
	}
	private function requireFile($path) {
		$file_exist = false ;
		foreach ( $this->_extensions as $ext ) {
			$filename = $path . '.' . $ext;
			//var_dump ( 'file exists  ? : ' . $filename );
			if (file_exists ( $filename )) {
				$file_exist = true ;
				//var_dump('require '.$filename);
				require $filename;
			}
		}
		return $file_exist ;
	}
}