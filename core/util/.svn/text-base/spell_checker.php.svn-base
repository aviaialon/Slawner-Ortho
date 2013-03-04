<?php
  /*
   USE EXAMPLE
   
   }
   */
  class SPELL_CHECKER
  {
      public static function spell_check($query)
      {
          // Substitute this application ID with your own application ID provided by Yahoo!.
          $appID = __YAHOO_APPLICATION_ID__;
          
          // URI used for making REST call. Each Web Service uses a unique URL.
          $request = "http://search.yahooapis.com/WebSearchService/V1/spellingSuggestion?appid=$appID&query=" . urlencode($query);
          
          // Initialize the session by passing the request as a parameter
          $session = curl_init($request);
          
          // Set curl options by passing session and flags
          // CURLOPT_HEADER allows us to receive the HTTP header
          curl_setopt($session, CURLOPT_HEADER, true);
          
          // CURLOPT_RETURNTRANSFER will return the response
          curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
          
          // Make the request
          $response = curl_exec($session);
          
          // Close the curl session
          curl_close($session);
          
          // Get the XML from the response, bypassing the header
          if (!($xml = strstr($response, '<?xml'))) {
              $xml = null;
          }
          
          // Create a SimpleXML object with XML response
          $simple_xml = simplexml_load_string($xml);
          
          // Traverse XML tree and save desired values from child nodes
          $data = (is_object($simple_xml) ? $simple_xml->Result : array());
          return $data;
      }
  }
?>