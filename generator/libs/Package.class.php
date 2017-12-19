<?php
namespace libs;

use libs\XML;

class Package
{
	private $filename;
	private $doc;
	
	private $name;
	private $packageName;
	private $packageDescription;
	
	private $author;
	private $authorUrl;
	
	private $version;
	private $fromVersions;
	private $updateType;
	
	private $timestamp;
	private $requiredPackages;
	private $excludedPackages;
	
	public function __construct($filename)
	{
		$this->filename = $filename;
		$this->fromVersions = array();
		$this->updateType = "install";
		$this->requiredPackages = array();
		$this->excludedPackages = array();
	}
	
	public function read($tarFile)
	{
		$this->timestamp = filemtime ($tarFile);
		$this->doc = new XML();
		$this->doc->load($this->filename);
		
		$packages = $this->doc->getElementsByTagName("package");
		foreach ($packages as $package)
		{
			$this->name = $package->getAttribute("name");
			$this->readPackageInformations($package);
			$this->readAuthorInformations($package);
			$this->readInstructions($package);
			$this->readRequiredPackages($package);
			$this->readExcludedPackages($package);
			break;
		}
	}
	
	public function getArray()
	{
		$packageArr = array();
		
		$packageArr["name"] = $this->name;
		$packageArr["packageName"] = $this->packageName;
		$packageArr["packageDescription"] = $this->packageDescription;
		
		$packageArr["author"] = $this->author;
		$packageArr["authorUrl"] = $this->authorUrl;
		
		$packageArr["version"] = $this->version;
		$packageArr["fromVersions"] = $this->fromVersions;
		$packageArr["updateType"] = $this->updateType;
		
		$packageArr["timestamp"] = $this->timestamp;
		$packageArr["requiredPackages"] = $this->requiredPackages;
		$packageArr["excludedPackages"] = $this->excludedPackages;
		
		return $packageArr;
	}
	
	private function readExcludedPackages($package)
	{
		$excludedpackagesParents = $package->getElementsByTagName("excludedpackages");
		foreach ($excludedpackagesParents as $excludedpackagesParent)
		{
			$excludedpackages = $excludedpackagesParent->getElementsByTagName("excludedpackage");
			foreach ($excludedpackages as $excludedpackage)
			{
				$packageArray = array();
				$packageArray["version"] = $excludedpackage->getAttribute("version");
				$packageArray["name"] = $excludedpackage->nodeValue;
				$this->excludedPackages[] = $packageArray;
			}
			break;
		}
	}
	
	private function readRequiredPackages($package)
	{
		$requiredpackagesParents = $package->getElementsByTagName("requiredpackages");
		foreach ($requiredpackagesParents as $requiredpackagesParent)
		{
			$requiredpackages = $requiredpackagesParent->getElementsByTagName("requiredpackage");
			foreach ($requiredpackages as $requiredpackage)
			{
				$packageArray = array();
				$packageArray["minversion"] = $requiredpackage->getAttribute("minversion");
				$packageArray["name"] = $requiredpackage->nodeValue;
				$this->requiredPackages[] = $packageArray;
			}
			break;
		}
	}
	
	private function readInstructions($package)
	{
		$instructionsParents = $package->getElementsByTagName("instructions");
		foreach ($instructionsParents as $instructionsParent)
		{
			$type = $instructionsParent->getAttribute("type");
			$fromversion = $instructionsParent->getAttribute("fromversion");
			if ($type == "update")
			{
				$this->updateType = "update";
				$this->fromVersions[] = $fromversion;
			}
		}
	}
	
	private function readAuthorInformations($package)
	{
		$authorinformations = $package->getElementsByTagName("authorinformation");
		foreach ($authorinformations as $authorinformation)
		{
			$authors = $authorinformation->getElementsByTagName("author");
			foreach ($authors as $author)
			{
				$this->author = $author->nodeValue;
				break;
			}
			$authorurls = $authorinformation->getElementsByTagName("authorurl");
			foreach ($authorurls as $authorurl)
			{
				$this->authorUrl = $authorurl->nodeValue;
				break;
			}
		}
	}
	
	private function readPackageInformations($package)
	{
		$packageinformations = $package->getElementsByTagName("packageinformation");
		foreach ($packageinformations as $packageinformation)
		{
			$packagenames = $packageinformation->getElementsByTagName("packagename");
			foreach ($packagenames as $packagename)
			{
				$this->packageName = $packagename->nodeValue;
				break;
			}
			$packagedescriptions = $packageinformation->getElementsByTagName("packagedescription");
			foreach ($packagedescriptions as $packagedescription)
			{
				$this->packageDescription = $packagedescription->nodeValue;
				break;
			}
			$versions = $packageinformation->getElementsByTagName("version");
			foreach ($versions as $version)
			{
				$this->version = $version->nodeValue;
				break;
			}
			break;
		}
	}
}