<?php
/**
 * 
 * Google webmaster tools class
 * @author aaialon
 *
 * For more info: http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html
 */
class WebmasterTools
{
	function __construct($username, $password)
	{
		$this->_Login($username, $password);
	}

	function _Http($method, $url, $contentType, $content='')
	{
		$method = strtoupper($method);
		$opts = array('http' =>
		array(
                'method'  => $method,
                'protocol_version' => 1.0,
                'header'  => 'Content-type: ' . $contentType .
		(isset($this->auth) && isset($this->auth['Auth']) ? "\nAuthorization: GoogleLogin auth=" . $this->auth['Auth']  : '' ) .
                             "\nContent-Length: " . strlen($content),
                'content' => $content
		)
		);
		$context  = stream_context_create($opts);
		$result = @file_get_contents($url, false, $context);
		return $result;
	}

	function _Login($username, $password, $service='sitemaps')
	{
		$postdata = http_build_query(
		array('accountType' => 'GOOGLE',
                  'Email'  => $username,
                  'Passwd' => $password,
                  'source' => 'WebmasterTools-Class',
                  'service'=> $service)
		);

		$login = $this->_Http('POST', 'https://www.google.com/accounts/ClientLogin','application/x-www-form-urlencoded', $postdata);
		$lines = explode("\n", $login);
		$data = array();
		foreach ($lines as $line)
		{
			list($var,$value) = explode('=', $line);
			$data[$var] = $value;
		}
		$this->auth=$data;
	}

	function _GetText($node)
	{
		$text = '';
		for ($i=0; $i < $node->childNodes->length; $i++)
		{
			$child = $node->childNodes->item($i);
			if ($child->nodeType==XML_TEXT_NODE)
			$text .= $child->wholeText;
		}
		return $text;
	}

	// array_elements_in has the set of tags we should use as array b
	// because they may repeat.
	function _ElementToArray($node, $array_elements_in = array())
	{
		$row = array();

		$array_elements = array();
		foreach ($array_elements_in as $array_element)
		$array_elements[$array_element] = true;

		for ($i=0; $i < $node->childNodes->length; $i++)
		{
			$item = $node->childNodes->item($i);
			if (!isset($item->tagName)) continue;
			$children = $this->_ElementToArray($item, $array_elements_in);
			if (count($children) > 0) {
				$value = $children;
			} else {
				$value = $this->_GetText($item);
			}
			if (isset($array_elements[$item->tagName])) {
				if (!isset($row[$item->tagName])) $row[$item->tagName] = array();
				$row[$item->tagName][] = $value;
			} else
			$row[$item->tagName] = $value;
		}
		return $row;
	}

	function _callWMT($method, $url, $site='', $params = array(), $array_elements_in = array())
	{

		$method = strtolower($method);
		$site = "http://$site/";
		$url = str_replace('{site}', urlencode($site), $url);
		$xml = '';

		if ($method=='post' || $method=='put')
		{

			$doc = new DOMDocument('1.0', 'utf-8');
			$root = $doc->createElementNS("http://www.w3.org/2005/Atom", 'atom:entry' );

			if (count($params) > 0) {
				$root->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:wt','http://schemas.google.com/webmasters/tools/2007');
			}
			$doc->appendChild($root);

			$element = $doc->createElement('atom:id', $site);
			$root->appendChild($element);

			if (count($params) > 0) {
				$element = $doc->createElement('atom:category');
				$element->setAttribute('scheme','http://schemas.google.com/g/2005#kind');
				$element->setAttribute('term','http://schemas.google.com/webmasters/tools/2007#site-info');
				$root->appendChild($element);
			} else {
				$element = $doc->createElement('atom:content');
				$element->setAttribute('src',$site);
				$root->appendChild($element);
			}

			foreach ($params as $tag => $value) {

				if (is_array($value))
				{
					$element = $doc->createElement("wt:$tag", $value['_value']);
					foreach($value as $att => $v) {
						if($att=='_value') continue;
						$element->setAttribute($att,$v);
					}
					$root->appendChild($element);
				}
				else
				{
					$element = $doc->createElement("wt:$tag", $value);
					$root->appendChild($element);
				}
			}

			$xml = $doc->saveXML();
		}

		$body = $this->_Http($method, $url, "application/atom+xml", $xml);

		if ($body!='')
		{
			$doc = new DOMDocument();
			$success = $doc->loadXML($body);
			return $this->_ElementToArray($doc, $array_elements_in);
		}
		else
		{
			return false;
		}

	}

	function createSite($site)
	{
		$this->_callWMT('post', 'https://www.google.com/webmasters/tools/feeds/sites/', $site);
		// Google does send Content-Lenght back and get_contents fails so we get the site again !
		return $this->getSite($site);
	}

	function deleteSite($site)
	{
		return $this->_callWMT('delete', 'https://www.google.com/webmasters/tools/feeds/sites/{site}', $site);
	}

	function setGeoLocation($site, $location)
	{
		return $this->_callWMT('put',"https://www.google.com/webmasters/tools/feeds/sites/{site}", $site, array('geolocation' => $location));
	}

	function setPreferredDomain($site, $domain='')
	{
		if ($domain=='') $domain = $site;
		return $this->_callWMT('put',"https://www.google.com/webmasters/tools/feeds/sites/{site}", $site, array('preferred-domain' => $domain));
	}
	
	function getCrawlErrors($site)
	{
		$entries = $this->_callWMT('get','https://www.google.com/webmasters/tools/feeds/{site}/crawlissues/', $site);
		return $entries;
	}
	
	/**
	 * 
	 * Returns the keyword feed for a site
	 * The Keywords feeds lists keywords (and phrases) discovered by Googlebot when crawling a 
	 * verified site in your account. These keywords are found either on your site (internal) or 
	 * in the anchor text used by other sites to link to yours (external). Together, these keywords 
	 * represent a snapshot of how Google sees the content of your site. If you see unexpected 
	 * keywords (such as "Viagra") in the Keywords feed, it could be a sign that your site has been 
	 * hacked. Conversely, if the feed does not contain expected keywords, it could indicate that 
	 * Googlebot was not able to crawl and index all the pages on your site. In this case, we recommend 
	 * submitting a Sitemap.
	 * 
	 * @param String $site - The site URL
	 * @return Ambigous <Array, multitype:multitype: Ambigous <Array, string> > The Keyword Feed
	 */
	function getKeywordsFeed($site)
	{
		$entries = $this->_callWMT('get','https://www.google.com/webmasters/tools/feeds/{site}/keywords/', $site);
		return $entries;
	}
	
	/**
	 * XML Sitemaps are a way for you to give Google information about your site. In its simplest terms, a Sitemap is a 
	 * list of the pages on your website. Creating and submitting a Sitemap helps make sure that Google knows about all 
	 * the pages on your site, including URLs that may not be discoverable by Google's normal crawling process.
	 * Sitemaps are particularly helpful if:
	 * 		Your site has dynamic content.
	 * 		Your site has pages that aren't easily discovered by Googlebot during the crawl process—for example, pages featuring rich AJAX or Flash.
	 * 		Your site is new and has few links to it. (Googlebot crawls the web by following links from one page to another, so if your site isn't well linked, it may be hard for us to discover it.)
	 * 		Your site has a large archive of content pages that are not well linked to each other, or are not linked at all.
	 * 
	 * While a standard Sitemap works for most sites, you can also create and submit specialized Sitemaps for certain types 
	 * of content. These Sitemap formats are specific to Google and are not used by other search engines. They're a good way 
	 * to give Google detailed information about specific content types. For example, publishers can use News Sitemaps to give 
	 * Google information relevant to news sites that appear in Google News search results, such as publication date, keywords, 
	 * and stock ticker symbol. As well as regular Sitemaps, specific Sitemap types include Video Sitemaps, Mobile Sitemaps, News 
	 * Sitemaps, and Code Search Sitemaps. More information about Sitemaps.
	 * You can use the Google Webmaster Tools Data API to request a list of all Sitemaps submitted for a given site, and to submit 
	 * and delete Sitemaps. Used in conjunction with a Sitemap tool such as the Sitemap Generator, this can help developers automate
	 * the creation and submission of Sitemaps.
	 * 
	 * Returns the Sitemap feed for a site
	 * @param String $site - The site URL
	 * @return Ambigous <Array, multitype:multitype: Ambigous <Array, string> > The Sitemap Feed
	 */
	function getSitemapFeed($site)
	{
		$entries = $this->_callWMT('get','https://www.google.com/webmasters/tools/feeds/{site}/sitemaps/', $site);
		return $entries;
	}
	
	function getSite($site)
	{
		$entries = $this->_callWMT('get','https://www.google.com/webmasters/tools/feeds/sites/{site}', $site);
		return $entries;
	}

	function getSites()
	{
		$rawSites = $this->_callWMT('get','https://www.google.com/webmasters/tools/feeds/sites','',array(),array('entry'));
		$sites = array();
		foreach ($rawSites['feed']['entry'] as $entry) {
			$site = explode('/', $entry['title']);
			$site = $site[2];
			$sites[$site] = $entry;
		}
		return $sites;
	}

	function verifySite($site, $location = '')
	{

		$entry = $this->getSite($site);

		$vm = $entry['entry']['wt:verification-method'];

		if ($location!='')
		{
			file_put_contents("$location/$vm", $vm);
		}

		return $this->_callWMT('put',"https://www.google.com/webmasters/tools/feeds/sites/{site}", $site,
		array('verification-method' =>
		array('_value' => $vm,
                                'type'   => 'htmlpage',
                                'in-use' => 'true',
                                'file-content' => "goolge-site-verification: $vm"
		)
		));

	}
	function verifySiteByMeta($site)
	{

		$entry = $this->getSite($site);

		$vm = $entry['entry']['wt:verification-method'];

		return $this->_callWMT('put',"https://www.google.com/webmasters/tools/feeds/sites/{site}", $site,
		array('verification-method' =>
		array('_value' => $vm,
                                'type'   => 'metatag',
                                'in-use' => 'true',
		)
		));

	}
}

function ut_WebmasterTools ($username, $password, $website) 
{
    $wt = new WebmasterTools($username, $password);
    
    
    print '<h1>LIVEBUCKS CRAWL ERROS</h1>';
    new dump($wt->getCrawlErrors('www.livebucks.com'));
    print '<hr />';
    
    print '<h1>LIVEBUCKS KEYWORD FEEDS</h1>';
    new dump($wt->getKeywordsFeed('www.livebucks.com'));
    print '<hr />';
    
    print '<h1>LIVEBUCKS SITEMAP FEEDS</h1>';
    new dump($wt->getSitemapFeed('www.livebucks.com'));
    print '<hr />';
    
    
    print '<h1>ALL SITES</h1>';
    new dump($wt->getSites());
    
    print '<br />';
    
    
	/*
    echo "Get Site\n";
    print_r($wt->getSite($website));

    echo "Delete Site\n";
    print_r($wt->deleteSite($website));

    echo "Create Site\n";
    print_r($wt->createSite($website));

    echo "Verify Site\n";
    print_r($wt->verifySite($website));

    echo "Set Location\n";
    print_r($wt->setGeoLocation($website,'AU'));
    */
}

ut_WebmasterTools('aviaialon', 'nqntvxaa');
die;
?>
