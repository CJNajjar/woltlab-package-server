<?php
function showFile($file)
{
	if (!file_exists("files/".$file.".tar"))
	{
		exit;
	}
	
	header("Content-type: application/x-tar");
	header("Content-disposition: attachment; filename=\"".$file."\"");
	echo file_get_contents("files/".$file.".tar");
}

if (isset($_GET["packageName"]) && isset($_GET["packageVersion"]))
{
	showFile($_GET["packageName"]);
}
else if (isset($_POST["packageName"]) && isset($_POST["packageVersion"]))
{
	showFile($_POST["packageName"]);
}
else
{
	header('Content-type: application/xml');
	echo file_get_contents("package_server.xml");
}