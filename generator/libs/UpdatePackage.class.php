<?php
namespace libs;

use libs\XML;

class UpdatePackage
{
	private $outputFile;
	private $packages;
	
	private $doc;
	private $section;
	
	public function __construct($outputFile, $packages)
	{
		$this->outputFile = $outputFile;
		$this->packages = $packages;
		$this->doc = new XML();
	}
	
	public function write()
	{
		$this->createSection();
		$this->createPackages();
		
		$this->doc->appendChild($this->section);
		$this->doc->save($this->outputFile);
	}
	
	private function createPackages()
	{
		foreach ($this->packages as $packageArr)
		{
			$package = $this->doc->createElement("package");
			$package->appendChild($this->doc->createSimpleAttribute("name", $packageArr["name"]));
			
			$package->appendChild($this->createPackageInformations($packageArr));
			$package->appendChild($this->createAuthorInformations($packageArr));
			$package->appendChild($this->createVersions($packageArr));
			
			$this->section->appendChild($package);
		}
	}
	
	private function createVersions($packageArr)
	{
		$versions = $this->doc->createElement("versions");
		
		$versions->appendChild($this->createVersion($packageArr));
		
		return $versions;
	}
	
	private function createVersion($packageArr)
	{
		$version = $this->doc->createElement("version");
		
		$version->appendChild($this->doc->createSimpleAttribute("name", $packageArr["version"]));
		$version->appendChild($this->doc->createSimpleAttribute("accessible", "true"));
		$version->appendChild($this->doc->createSimpleAttribute("requireAuth", "false"));
		if (count($packageArr["fromVersions"]) > 0)
		{
			$version->appendChild($this->createFromVersions($packageArr));
		}
		if (count($packageArr["requiredPackages"]) > 0)
		{
			$version->appendChild($this->createRequiredPackages($packageArr));
		}
		if (count($packageArr["excludedPackages"]) > 0)
		{
			$version->appendChild($this->createExcludedPackages($packageArr));
		}
		$version->appendChild($this->doc->createCDATAElement("updatetype", $packageArr["updateType"]));
		$version->appendChild($this->doc->createCDATAElement("timestamp", $packageArr["timestamp"]));
		
		return $version;
	}
	
	private function createFromVersions($packageArr)
	{
		$fromversions = $this->doc->createElement("fromversions");
		
		foreach($packageArr["fromVersions"] as $fromversionTmp)
		{
			$fromversion = $this->doc->createCDATAElement("fromversion", $fromversionTmp);
			
			$fromversions->appendChild($fromversion);
		}
		
		return $fromversions;
	}
	
	private function createExcludedPackages($packageArr)
	{
		$excludedpackages = $this->doc->createElement("excludedpackages");
		
		foreach($packageArr["excludedPackages"] as $excludedpackageTmp)
		{
			$excludedpackage = $this->doc->createCDATAElement("excludedpackage", $excludedpackageTmp["name"]);
			
			$excludedpackage->appendChild($this->doc->createSimpleAttribute("version", $excludedpackageTmp["version"]));
			
			$excludedpackages->appendChild($excludedpackage);
		}
		
		return $excludedpackages;
	}
	
	private function createRequiredPackages($packageArr)
	{
		$requiredpackages = $this->doc->createElement("requiredpackages");
		
		foreach($packageArr["requiredPackages"] as $requiredpackageTmp)
		{
			$requiredpackage = $this->doc->createCDATAElement("requiredpackage", $requiredpackageTmp["name"]);
			
			$requiredpackage->appendChild($this->doc->createSimpleAttribute("minversion", $requiredpackageTmp["minversion"]));
			
			$requiredpackages->appendChild($requiredpackage);
		}
		
		return $requiredpackages;
	}
	
	private function createAuthorInformations($packageArr)
	{
		$authorinformation = $this->doc->createElement("authorinformation");
		
		$authorinformation->appendChild($this->doc->createCDATAElement("author", $packageArr["author"]));
		$authorinformation->appendChild($this->doc->createCDATAElement("authorurl", $packageArr["authorUrl"]));
		
		return $authorinformation;
	}
	
	private function createPackageInformations($packageArr)
	{
		$packageinformation = $this->doc->createElement("packageinformation");
		
		$packageinformation->appendChild($this->doc->createCDATAElement("packagename", $packageArr["packageName"]));
		$packageinformation->appendChild($this->doc->createCDATAElement("packagedescription", $packageArr["packageDescription"]));
		
		return $packageinformation;
	}
	
	private function createSection()
	{
		$this->section = $this->doc->createElement("section");
		$this->section->appendChild($this->doc->createSimpleAttribute("xmlns", "http://www.woltlab.com"));
		$this->section->appendChild($this->doc->createSimpleAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance"));
		$this->section->appendChild($this->doc->createSimpleAttribute("name", "packages"));
		$this->section->appendChild($this->doc->createSimpleAttribute("xsi:schemaLocation", "http://www.woltlab.com https://www.woltlab.com/XSD/vortex/packageUpdateServer.xsd"));
	}
}
