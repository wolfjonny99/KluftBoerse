<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 DasLampe <andre@lano-crew.org> |
// | Encoding:  UTF-8 |
// +----------------------------------------------------------------------+
class resourceController
{
	public function __construct(Array $file)
	{
		$path2file	= "";
		for($i=1;$i<count($file);$i++)
		{
			if($i!=1)
			{
				$path2file	.= '/';
			}
			$path2file		.= $file[$i];
		}
		$file	= $path2file;
		$type	= $this->getHeaderType($file);

		if(file_exists(PATH_MAIN.$file))
		{
			if($type =="application/x-httpd-php")
			{
			 	include(PATH_MAIN.$file);
			}
			else
			{
				header("Content-Type: ".$type);
				//Using cache to optimize page speed
				header ("cache-control: must-revalidate; max-age: 2592000");
				header ("expires: " . gmdate ("D, d M Y H:i:s", time() + 2592000) . " GMT");
				
				$content		= file_get_contents(PATH_MAIN.$file);
				
				$content		= str_replace("{LINK_MAIN}", LINK_MAIN, $content);
				
				/**
				* Remove whitespace characters & comments (in CSS files)
				* Based on http://phpperformance.de/optimierungen-von-css-und-javascript-on-the-fly/
				*/
				if($type == "text/css") {
					 $content		= preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $content);
					 $search		= array("\r\n", "\r", "\n", "\t", "  ", "    ", "    ");
					 $content		= str_replace($search, "", $content);
				}
				if($type == "text/javascript") {
					 $content		= preg_replace('/(\n)\n+/', '$1', $content);
					 $content		= preg_replace('/(\n)\ +/', '$1', $content);
					 $content		= preg_replace('/(\r)\r+/', '$1', $content);
					 $content = preg_replace('/(\r\n)(\r\n)+/', '$1', $content);
					 $content = preg_replace('/(\ )\ +/', '$1', $content);
				 }

				echo $content;
			}
		}
		else
		{
			echo 'Datei ('.$file.') existiert nicht!';
		}
	}

	private function getHeaderType($file)
	{
		$type	= explode(".", $file);
		switch($type[count($type) -1])
		{
			 case "css":
				$type = "text/css";
				break;
			case "jpg":
				$type = "image/jpg";
				break;
			case "gif":
				$type = "image/gif";
				break;
			case "png":
				$tpye = "image/png";
				break;
			case "js":
				$type = "text/javascript";
				break;
			case "php":
				$type = "application/x-httpd-php";
				break;
		}

		return $type;
	}
}
