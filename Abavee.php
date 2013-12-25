<?php

class Abavee {

	private $baseDir;
	private $configDir;
	private $classesDir;
	private $tempDir;

	public static function create($baseDir, $configDir = 'config', $classesDir = 'classes', $tempDir = 'temp') {
		return new Abavee(
			$baseDir,
			$baseDir.DIRECTORY_SEPARATOR.$configDir,
			$baseDir.DIRECTORY_SEPARATOR.$classesDir,
			$baseDir.DIRECTORY_SEPARATOR.$tempDir
		);
	}
	
	public function __construct($baseDir, $configDir, $classesDir, $tempDir) {
		$this->baseDir = $baseDir;
		$this->configDir = $configDir;
		$this->classesDir = $classesDir;
		$this->tempDir = $tempDir;
	}
	
	public function run() {
		$di = $this->prepareEnvironment(false);
		$handler = $di->get('RequestHandler');
		$handler->run();
	}
	
	public function test() {
		$di = $this->prepareEnvironment(true);
		$autoloader = $di->get('Autoloader');
		$autoloader->addDirectory($this->baseDir.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tests');
	}
	
	/**
	 * @return DI
	 */
	private function prepareEnvironment($testEnvironment) {
		require_once('functions.php');
		$autoloader = $this->prepareAutoloader($testEnvironment);
		$config = $this->loadConfig();
		$di = new DI($config->get('di'));
		$di->setInstance($config);
		$di->setInstance($autoloader);
		return $di;
	}
	
	/**
	 * @param boolean $testEnvironment
	 * @return Autoloader
	 */
	private function prepareAutoloader($testEnvironment) {
		require_once('classes/core/Autoloader.php');
		$autoloader = new Autoloader($this->tempDir.DIRECTORY_SEPARATOR.'autoload.cache.dat', !$testEnvironment);
		spl_autoload_register(array($autoloader,'load'));
		$autoloader->addDirectory(__DIR__.DIRECTORY_SEPARATOR.'classes');
		$autoloader->addDirectory($this->classesDir);
		return $autoloader;
	}
	
	private function loadConfig() {
		set_include_path(get_include_path().PATH_SEPARATOR.$this->configDir);
		return new Config(require_once('config.php'));
	}

}
