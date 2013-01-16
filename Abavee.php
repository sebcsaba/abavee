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
		require_once('functions.php');
		$this->prepareAutoloader();
		$config = $this->loadConfig();
		$di = new DI($config->get('di'));
		$di->setInstance($config);
		$handler = $di->get('RequestHandler');
		$handler->run();
	}
	
	private function prepareAutoloader() {
		require_once('classes/core/Autoloader.php');
		$autoloader = new Autoloader($this->tempDir.DIRECTORY_SEPARATOR.'autoload.cache.dat');
		spl_autoload_register(array($autoloader,'load'));
		$autoloader->addDirectory(__DIR__.DIRECTORY_SEPARATOR.'classes');
		$autoloader->addDirectory($this->classesDir);
	}
	
	private function loadConfig() {
		set_include_path(get_include_path().PATH_SEPARATOR.$this->configDir);
		return new Config(require_once('config.php'));
	}

}
