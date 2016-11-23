<?php 

namespace Ashfaq1701/SitemapGenerator;

use DOMDocument;
use SimpleXMLElement;

class Sitemap
{
	public $url;
	public $basepath;
	public $host;
	public $scheme;
	public $internalLinks;
	public $countLinks;
	public $maximum;
	public $sitemapPath;
	
	public function __construct($url, $path='sitemap.xml', $maximum=1000)
	{
		$components = parse_url($url);
		$this->host = $components['host'];
		$this->scheme = $components['scheme'];
		$this->basepath = $this->scheme.'://'.$this->host;
		$this->url = $url;
		$this->internalLinks = [];
		$this->countLinks = 0;
		$this->maximum = $maximum;
		$this->sitemapPath = $path;
	}
	
	public function createLinkArray()
	{
		$this->internalLinks[$this->url] = ['read'=>0];
		$count = 0;
		do
		{
			if($count >= count($this->internalLinks))
			{
				break;
			}
			$assoc = array_slice($this->internalLinks, $count, 1);
			foreach ($assoc as $url=>$read)
			{
				if($this->countLinks <= $this->maximum)
				{
					if($read['read'] == 0)
					{
						$this->getLinks($url);
					}
				}
				else
				{
					return $this->internalLinks;
				}
			}
			$count++;
		} while($count < count($this->internalLinks));
		return array_keys($this->internalLinks);
	}
	
	function getLinks($url)
	{
		$html = file_get_contents($url);
		if($html === false)
		{
			return $this->internalLinks;
		}
		$dom = new DOMDocument;
		$dom->loadHTML($html);
		foreach ($dom->getElementsByTagName('a') as $node)
		{
			$link = $node->getAttribute("href");
			if ((strpos($link, 'javascript:') === 0) || (strpos($link, 'mailto:') === 0) || (strpos($link, 'tel:') === 0)) {
   				continue;
			}
			if($this->isexternal($link) == true)
			{
				continue;
			}
			if($this->isImage(link))
			{
				continue;
			}
			if($this->countLinks <= $this->maximum)
			{
				$lastChar = substr($link, strlen($link)-1);
				if($lastChar == '/')
				{
					$link = substr($link, 0, strlen($link)-1);
				}
				if($lastChar == '#')
				{
					$link = substr($link, 0, strlen($link)-1);
				}
				if(!array_key_exists($link, $this->internalLinks))
				{
					$this->internalLinks[$link] = ['read' => 0];
					$this->countLinks++;
				}
			}
			else
			{
				$this->internalLinks[$url]['read'] = 1;
				return $this->internalLinks;
			}
		}
		$this->internalLinks[$url]['read'] = 1;
		return $this->internalLinks;
	}
	
	function isexternal($url) {
		$components = parse_url($url);
		return !empty($components['host']) && strcasecmp($components['host'], $this->host);
	}
	
	function isImage($url)
	{
		return is_array(getimagesize($url));
	}
	
	function generateSitemap()
	{
		$linkArray = $this->createLinkArray();
		$urlset = new SimpleXMLElement("<urlset></urlset>");
		$urlset->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		foreach ($linkArray as $url)
		{
			$url = $this->relToAbs($url);
			$urlElement = $urlset->addChild('url');
			$locElement = $urlElement->addChild('loc', $url);
		}
		$this->writeXMLSitemap($urlset->asXML());
		return $urlset->asXML();
	}
	
	function writeXMLSitemap($sitemapXml)
	{
		file_put_contents($this->sitemapPath, $sitemapXml);
	}
	
	function relToAbs($url)
	{
		if(substr($url, 0, strlen($this->basepath)) !== $this->basepath)
		{
			if(substr($url, 0, 1) == '/')
			{
				$url = $this->basepath.$url;
			}
			else
			{
				$url = $this->basepath.'/'.$url;
			}
		}
		return $url;
	}
}

?>
