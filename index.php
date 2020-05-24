<?php

require_once 'Scrape/Scraper.php';

use Scrape\Scraper;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $scrape = new Scraper;
  exit(json_encode($scrape->getData(), 200));
}

exit(json_encode('HTTP Method not Allowed',403));

// Country, total cases, new cases, total deaths, new deaths and total recovered

?>