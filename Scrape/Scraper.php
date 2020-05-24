<?php

namespace Scrape;

use DOMDocument;

class Scraper {

  private $url;

  public function __construct()
  {
    $this->url = "https://www.worldometers.info/coronavirus/";
  }
  
  private function scrape()
  {
    
    $dom = new DOMDocument;
    // surpress warning on unwanted tags
    libxml_use_internal_errors(true);
    $dom->loadHTMLFile($this->url);
    // not interested in fancy spaces
    $dom->preserveWhiteSpace = false;
    libxml_clear_errors();

    return $dom;
  }

  private function getStringTable()
  {
    return $this->scrape()
                ->getElementById('main_table_countries_today');
  }

  public function getData()
  {
    return $this->extractData();
  }

  private function convertToArray()
  {
    $table = $this->getStringTable();
    return $table ? simplexml_import_dom($table) : NULL;
  }

  private function extractData()
  {
    $extract = [];
    $arrData = $this->convertToArray(); 
    if(! $arrData) return NULL;

    foreach($arrData[0] as $data)
    {
      $extract[] = $data;
    }

    return $this->getBodyRows($extract);
  }

  private function getBodyRows($data)
  {
    $rowData = [];
    foreach($data[1] as $row)
    {
      $rowData[] = $row;
    }
    // TD of body
    return $this->getBodyTD($rowData);
  }

  private function getBodyTD($rowData)
  {
    $td = [];
    for ($i=0; $i < count($rowData); $i++) 
    { 
      if ($i < 8) continue;
      $td[] = $this->getBodyDetails($rowData[$i]->td);
    }

    return $td;
  }

  private function getBodyDetails($tdData)
  {
    $details = [];
    for ($i=0; $i < count($tdData); $i++) 
    {
      $details['country']         = isset($tdData[1]->a) ? (string)$tdData[1]->a : (string)$tdData[1]->span;
      $details['totalCases']      = (string)$tdData[2];
      $details['newCases']        = (string)$tdData[3];
      $details['totalDeaths']     = (string)$tdData[4];
      $details['newDeaths']       = (string)$tdData[5];
      $details['totalRecovered']  = (string)$tdData[6];
      $details['continent']       = (string)$tdData[14]; // will use to color rows 
    }
    //Country, total cases, new cases, total deaths, new deaths and total recovered
    return ($details);
  }

}