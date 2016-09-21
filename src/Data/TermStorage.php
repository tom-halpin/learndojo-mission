<?php

namespace Drupal\mission\Data;

class TermStorage {
      
  static function loadGrid($header, $pagesize, $country, $term)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('katerm', 'ka') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('ka', 
                        array('id', 'country_id', 'name', 'description', 'start_date', 'num_weeks')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        $select->join("kacountry","kac","ka.country_id = kac.id");  //join country table
        $select -> fields('kac', array('name')) ;
        $select->addField('kac', 'name', 'countryname'); // alias country.name
        if (isset($country)) {
          $select->condition('kac.name', '%' . db_like(trim($country)) . '%', 'LIKE');
        }                        
                       
        if (isset($term)) {
          $select->condition('ka.name', '%' . db_like(trim($term)) . '%', 'LIKE');
        }   
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT t.id, t.country_id, t.name, t.start_date, t.num_weeks, t.description, c.name as countryname FROM katerm t, kacountry c 
                        WHERE t.country_id = c.id')->fetchAllAssoc('id');    
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT t.id, t.country_id, t.name, t.start_date, t.num_weeks, t.description, c.name as countryname FROM katerm t, kacountry c 
                        WHERE t.country_id = c.id and t.id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function getAllForCountry($country_id) {
    $result = db_query('SELECT t.id, t.country_id, t.name, t.start_date, t.num_weeks, t.description, c.name as countryname FROM katerm t, kacountry c 
                        WHERE t.country_id = c.id and t.country_id = :country_id', array(':country_id' => $country_id))->fetchAllAssoc('id');
     return $result;
  }
  
  static function add($country_id, $name, $startdate, $numweeks, $description) {
    db_insert('katerm')->fields(array(
      'country_id' => $country_id,    
      'name' => trim($name),
      'start_date' => trim($startdate),
      'num_weeks' => trim($numweeks),
      'description' => trim($description),
    ))->execute();
  }

  static function edit($id, $country_id, $name, $startdate, $numweeks, $description) {
    db_update('katerm')->fields(array(
      'country_id' => $country_id,
      'name' => trim($name),
      'start_date' => trim($startdate),
      'num_weeks' => trim($numweeks),
      'description' => trim($description),
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('katerm')->condition('id', $id)->execute();
  }
  
  static function getIDByCountryTermName($country, $term) {
    $id = db_query('SELECT t.id, t.country_id, t.name, t.start_date, t.num_weeks, t.description, c.name as countryname FROM katerm t, kacountry c 
                    WHERE t.country_id = c.id and c.name = :countryname and t.name = :name', 
                    array(':countryname' => trim($country), ':name' => trim($term)))->fetchField();
    return $id;
  }
  
  static function getIDByCountryTermID($country, $term) {
    $id = db_query('SELECT t.id, t.country_id, t.name, t.start_date, t.num_weeks, t.description, c.name as countryname FROM katerm t, kacountry c 
                    WHERE t.country_id = c.id and c.id = :countryid and t.id = :id', 
                    array(':countryid' => trim($country), ':id' => trim($term)))->fetchField();
    return $id;
  }  
}
