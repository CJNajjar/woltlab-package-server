<?php
namespace libs;

use libs\Package;
use libs\UpdatePackage;

class Generator
{
	private $packageDir;
	private $packages;
	private $tmpDir;
	private $outputFile;
	
	public function __construct($packageDir = __DIR__ ."/../../files/")
	{
		$this->packageDir = $packageDir;
		$this->tmpDir = __DIR__ . "/../tmp/";
		$this->packages = array();
		$this->outputFile = __DIR__ . "/../../package_server.xml";
	}
	
	private function removeDir($dir) {
		if (!is_dir($dir))
		{
			return;
		}
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->removeDir("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
	} 
	
	private function extractFileToTmp($filename)
	{
		if (is_dir($this->tmpDir))
		{
			$this->removeDir($this->tmpDir);
		}
		mkdir($this->tmpDir);
		$phar = new \PharData($this->packageDir.$filename);
		$phar->extractTo($this->tmpDir);
	}
	
	private function readPackage($tarFile)
	{
		try
		{
			$package = new Package($this->tmpDir."package.xml");
			$package->read($this->packageDir.$tarFile);
			$this->packages[] = $package->getArray();
		} catch (\Exception $e) {}
	}
	
	private function readFiles()
	{
		$tarFiles = scandir($this->packageDir);
		foreach ($tarFiles as $tarFile)
		{
			if ($tarFile == ".." || $tarFile == "." || !is_file($this->packageDir.$tarFile))
			{
				continue;
			}
			$this->extractFileToTmp($tarFile);
			$this->readPackage($tarFile);
			$this->removeDir($this->tmpDir);	
		}
	}
	
	private function writeUpdatePackage()
	{
		$updatePackage = new UpdatePackage($this->outputFile, $this->packages);
		$updatePackage->write();
	}
	
	public function execute()
	{
		$this->readFiles();
		$this->writeUpdatePackage();
	}
}