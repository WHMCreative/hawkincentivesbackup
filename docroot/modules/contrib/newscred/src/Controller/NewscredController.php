<?php

namespace Drupal\newscred\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector;

/**
* An example controller.
 */
class NewscredController {

    /**
    * {@inheritdoc}
     */
    public function getlogs() {
	$query = db_select('watchdog', 'wd')->fields('wd', array('message', 'variables', 'timestamp'))->orderBy('timestamp', 'DESC');
	$results = $query->execute()->fetchAll();

	$datas = array();
	foreach ($results as $result) {
	    if(strpos($result->message, 'NewsCred') !== false) {
		$data['message'] = $result->message;
		$data['timestamp'] = date('d.m.Y H:i', $result->timestamp);
		$datas[] = $data;
	    }
	}
	
	$resultsJSON = json_decode('{}');
	$resultsJSON->title = 'NC Logs';
	$resultsJSON->logs = $datas;
	
	$response = new Response();
	$response->setContent(json_encode($resultsJSON));
	$response->headers->set('Content-Type', 'application/json');
	
	return $response;
    }

}

?>
