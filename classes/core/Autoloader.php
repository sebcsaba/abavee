<?php

/**
 * This class can load any class from the given directory tree.
 * For the class 'FooBar' the followin rules are satisfied:
 * - there must be a file called FooBar.php that contains that file
 * - this file can be anywhere in the subtree of the given base directories
 * 
 * @author sebcsaba
 */
class Autoloader {
	
	/**
	 * The cache will be stored in this file
	 * 
	 * @var string
	 */
	private $cacheFileName;
	
	/**
	 * The array containing the known classes. The indexes are the classnames,
	 * and the values are the corresponting absolute file paths.
	 *
	 * @var array (key=>string)
	 */
	private $classes;
	
	/**
	 * The list contains the list of the directories to scan. If a class is not
	 * found in the $classes, picks the first item of this, and scans that directory.
	 *
	 * @var array (string)
	 */
	private $directoryQueue;
	
	/**
	 * If true, a ClassNotFoundError can be thrown if the requested class is not found.
	 * If false, no exception is thrown, just a false value is returned.
	 * @var boolean
	 */
	private $throw;
	
	/**
	 * Creates the classloader.
	 *
	 * @param string $cacheFileName The cache will be stored in this file
	 * @param boolean $throw If true, a ClassNotFoundError can be thrown
	 */
	public function __construct($cacheFileName, $throw = true) {
		$this->cacheFileName = $cacheFileName;
		$this->classes = $this->loadCache();
		$this->directoryQueue = array();
		$this->throw = $throw;
	}
	
	/**
	 * Adds another directory to the lookup queue.
	 * 
	 * @param string $directory The path of the new directory.
	 */
	public function addDirectory($directory) {
		$this->directoryQueue []= $directory;
	}
	
	/**
	 * Reads the serialized cache (if exists) and returns.
	 *
	 * @return array (key=>string) The content of the cache, or empty if no cachefile exists.
	 */
	private function loadCache() {
		if (!is_readable($this->cacheFileName)) {
			return array();
		} else {
			return unserialize(file_get_contents($this->cacheFileName));
		}
	}

	/**
	 * Writes the cache to the specified file.
	 */
	private function writeCache() {
		@mkdir(dirname($this->cacheFileName));
		file_put_contents($this->cacheFileName, serialize($this->classes));
		@chmod($this->cacheFileName, 0664);
	}

	/**
	 * Lookup the file that contains the specified class, and loads it.
	 * 
	 * If it known about the class (it is in the $classes), then loads from there.
	 * Otherwise, while the $directoryQueue is not empty, pick the first element
	 * of that, and process that directory. If finds a subdirectory, then pushes
	 * that to the $directoryQueue. If finds a file, takes to the $classes. If the
	 * requested class is found, loads that file. Finally, if there was change in
	 * the $classes, then saves that to file.
	 * 
	 * @param string $className The name of the class to load.
	 * @throws ClassNotFoundException If the specified class was not found and throw=true
	 * @return boolean True if the class was loaded, false (or exception) othrerwise 
	 */
	public function load($className) {
		if (array_key_exists($className,$this->classes)) {
			$file = $this->classes[$className];
			if (is_readable($file)) {
				require_once($file);
				return true;
			}
		}
		$found = false;
		$modified = false;
		while (!$found && !empty($this->directoryQueue)) {
			$dir = array_shift($this->directoryQueue);
			foreach (scandir($dir) as $f) {
				$file = $dir.DIRECTORY_SEPARATOR.$f;
				if (is_dir($file)) {
					if ($f[0]!='.') { // ignore if starts with a dot: '.', '..', '.svn', '.git'
						$this->directoryQueue []= $file;
					}
				} else if (is_readable($file)) {
					$name = basename($file,'.php');
					$this->classes[$name] = $file;
					$modified = true;
					if ($name==$className) {
						$found = true;
					}
				}
			}
		}
		if ($modified) $this->writeCache();
		if ($found) {
			require_once($this->classes[$className]);
			return true;
		} else {
			if ($this->throw) {
				throw new ClassNotFoundException($className);
			} else {
				return false;
			}
		}
	}
	
}
