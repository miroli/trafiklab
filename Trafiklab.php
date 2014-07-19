<?php
/**
* Trafiklab class
*/
class Trafiklab {
  /* Contains users own API key */
  private $key;
  /* Contains host */
  private $host = 'https://api.trafiklab.se/sl/';
  /* Contains one of the four APIs */
  private $api;
  /* Contains data format */
  
  /* Constructor sets API key */
  function __construct($key) {
    $this->key = 'key='.$key;
  }
  
  /**
  * Set API
  */
  function setAPI($api) {
    switch (strtolower($api)) {
      case 'realtid':
        $this->api = 'realtid/';
        break;
      case 'trafiklaget';
        $this->api = 'trafikenjustnu/';
        break;
      case 'storningsinfo':
        $this->api = 'storningsinfo/';
        break;
      case 'reseplanerare':
        $this->api = 'reseplanerare';
        break;
    }
  }
  
  /**
  * Generic function for methods
  */
  function method($method, $params, $format = NULL) {    
    if ($format !== NULL) {
      if (strtolower($format) === 'json') {
        return $this->buildQuery($method, $params, 'json');
      } else if (strtolower($format) === 'xml') {
        return $this->buildQuery($method, $params, 'xml');
      } else {
        echo 'Invalid format';
      }
    } else return $this->buildQuery($method, $params);
  }
  
  /**
  * Methods for the Realtid API 
  */
  function getSite($stationSearch, $format = NULL) {
    $params = '?stationsearch='.rawurlencode($stationSearch).'&';
    return $this->method('GetSite', $params, $format !== NULL ? $format : NULL);
  }
  
  function getDepartures($siteId, $format = NULL) {
    $params = '?siteId='.rawurlencode($siteId).'&';
    return $this->method('GetDepartures', $params, $format !== NULL ? $format : NULL);
  }
  
  function getDpsDepartures($siteId, $timeWindow = NULL, $format = NULL) {
    if ($timeWindow !== NULL) {
      $params = '?siteId='.rawurlencode($siteId).'&timeWindow='.$timeWindow.'&';
    } else {
      $params = '?siteId='.rawurlencode($siteId).'&';
    }
      
    return $this->method('GetDpsDepartures', $params, $format !== NULL ? $format : NULL);
  }
  
  /**
  * Methods for the Trafikläget API 
  */
  function trafikenJustNu($format = NULL) {
    $params = '?';
    return $this->method('14448', $params, $format !== NULL ? $format : NULL);
  }
  
  /**
  * Methods for the Störningsinformation API 
  */
  function getAllDeviations($format = NULL) {
    $params = '?';
    return $this->method('GetAllDeviations', $params, $format !== NULL ? $format : NULL);
  }
  
  function getAllDeviationsRawData($format = NULL) {
    $params = '?';
    return $this->method('GetAllDeviationsRawData', $params, $format !== NULL ? $format : NULL);
  }
  
  function getDeviations($fDate, $tDate, $mode = NULL, $format = NULL) {
    if ($mode !== NULL) {
      $params = '?transportMode='.$mode.'&fromDate='.$fDate.'&toDate='.$tDate.'&';
    } else {
      $params = '?fromDate='.$fDate.'&toDate='.$tDate.'&';
    }
    return $this->method('GetDeviations', $params, $format !== NULL ? $format : NULL);
  }
  
  function getDeviationsRawData($fDate, $tDate, $mode = NULL, $format = NULL) {
    if ($mode !== NULL) {
      $params = '?transportMode='.$mode.'&fromDate='.$fDate.'&toDate='.$tDate.'&';
    } else {
      $params = '?fromDate='.$fDate.'&toDate='.$tDate.'&';
    }
    return $this->method('GetDeviationsRawData', $params, $format !== NULL ? $format : NULL);
  }
  
  /**
  * Methods for the Reseplanerare API 
  */
  function reseplanerare($params = array(), $format = NULL) {
    $valid_params = ['S', 'SID', 'Z', 'ZID', 'V1', 'Date', 'Time', 'Timesel', 'Lang'];
    $paramstr = '?';
    
    foreach ($params as $key => $value) {
      if (!in_array($key, $valid_params)) {
        echo $key.' is not a valid parameter.';
        die();
      }
      $paramstr .= '&'.$key.'='.rawurlencode($value);
    }
    
    $paramstr .= '&';
    
    return $this->method('', $paramstr, $format !== NULL ? $format : NULL);
  }
  
  /**
  * Build query and cURL
  */
  function buildQuery($method, $params, $format = NULL) {
    if ($format !== NULL) {
      $url = $this->host.$this->api.$method.'.'.$format.$params.$this->key;
    } else { $url =  $url = $this->host.$this->api.$method.'.json'.$params.$this->key; }
    
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    /* Accept any server certificate */
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);

    return utf8_decode($response);
  }
  
}
?>