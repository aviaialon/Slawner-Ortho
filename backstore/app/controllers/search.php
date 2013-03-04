<?php
class SEARCH_CONTROLLER extends REQUEST_DISPATCHER
{
	protected final function indexAction()
	{
		$this->enableViewCache(false);
		$this->useCompression(false);
		$this->assignNoView();
		
		$arrResults = array(
			'results' => array(
				array(
					'title' => 	'Slawner Ortho - Page result #1',
					'text'	=>	'This is some text filtered from the search',
					'url'	=>	'http://slawner.dns04.com/en'
				),
				array(
					'title' => 	'Slawner Ortho - Page result #2',
					'text'	=>	'This is some text filtered from the search',
					'url'	=>	'http://slawner.dns04.com/en'
				),
				array(
					'title' => 	'Slawner Ortho - Page result #3',
					'text'	=>	'This is some text filtered from the search',
					'url'	=>	'http://slawner.dns04.com/en'
				),
				array(
					'title' => 	'Slawner Ortho - Page result #4',
					'text'	=>	'This is some text filtered from the search',
					'url'	=>	'http://slawner.dns04.com/en'
				)
			),
			
			'data'	=>	array(
				"ignore_words" 	 =>	NULL,
				"ent_query"	 	 =>	$_GET['query'],
				"time"			 =>	0.07,
				"did_you_mean"	 =>	"",
				"did_you_mean_b" => NULL,
				"num_of_results" =>	8,
				"from"			 => 1,
				"to"			 => 8,
				"total_results"	 => 8,
				
				"pages"			=> 1,
				"prev"			=> 0,
				"next"			=> 2,
				"start"			=> 1,
				"query"			=> $_GET['query'],
				"other_pages"	=> array(),
				"qry_results"	 =>	array(
					array(
						'num'	=>	1,
						'url'	=>	'http://slawner.dns04.com/en',
						'url2'	=> 'http://slawner.dns04.com/en',
						"domain_name" => 'http://slawner.dns04.com/en',
						'title' 	  => 'Slawner Ortho - Page result #1',
						'fulltxt'	  => 'This is some text filtered from the search..',
						'page_size'	  => '59.9kb'
					),
					array(
						'num'	=>	2,
						'url'	=>	'http://slawner.dns04.com/en',
						'url2'	=> 'http://slawner.dns04.com/en',
						"domain_name" => 'http://slawner.dns04.com/en',
						'title' 	  => 'Slawner Ortho - Page result #2',
						'fulltxt'	  => 'This is some text filtered from the search..',
						'page_size'	  => '59.9kb'
					),
					array(
						'num'	=>	3,
						'url'	=>	'http://slawner.dns04.com/en',
						'url2'	=> 'http://slawner.dns04.com/en',
						"domain_name" => 'http://slawner.dns04.com/en',
						'title' 	  => 'Slawner Ortho - Page result #3',
						'fulltxt'	  => 'This is some text filtered from the search..',
						'page_size'	  => '59.9kb'
					),
					array(
						'num'	=> 4,
						'url'	=> 'http://slawner.dns04.com/en',
						'url2'	=> 'http://slawner.dns04.com/en',
						"domain_name" => 'http://slawner.dns04.com/en',
						'title' 	  => 'Slawner Ortho - Page result #4',
						'fulltxt'	  => 'This is some text filtered from the search..',
						'page_size'	  => '59.9kb'
					)
				)
			)
		);
		
		
		sleep(2);
		header('Content-Type: text/json');
		echo (json_encode($arrResults));
		die;
	}
}