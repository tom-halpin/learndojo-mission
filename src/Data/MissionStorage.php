<?php

namespace Drupal\mission\Data;

class MissionStorage {
      
  static function loadGrid($header, $pagesize, $country, $mission)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('kamission', 'ka') -> extend('Drupal\Core\Database\Query\TableSortExtender') -> extend('Drupal\Core\Database\Query\PagerSelectExtender');
        # get the desired fields
        $select -> fields('ka', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        $select->join("kacountry","kac","ka.country_id = kac.id");  //join country table
        $select -> fields('kac', array('name')) ;
        $select->addField('kac', 'name', 'countryname'); // alias country.name
        if (isset($country)) {
          $select->condition('kac.name', '%' . db_like(trim($country)) . '%', 'LIKE');
        }                        
        if (isset($mission)) {
          $select->condition('ka.name', '%' . db_like(trim($mission)) . '%', 'LIKE');
        }                        
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
   static function getAll() {
    $result = db_query('SELECT m.id, m.country_id, m.name, m.description, c.name as countryname FROM kamission m, kacountry c where m.country_id = c.id')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT m.id, m.country_id, m.name, m.description, c.name as countryname FROM kamission m, kacountry c where m.country_id = c.id and m.id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }
  
  static function getAllForCountry($country_id) {
    $result = db_query('SELECT m.id, m.country_id, m.name, m.description, c.name as countryname FROM kamission m, kacountry c where m.country_id = c.id and m.country_id = :country_id', array(':country_id' => $country_id))->fetchAllAssoc('id');
     return $result;
  }
  
  static function add($name, $country_id, $description) {
    db_insert('kamission')->fields(array(
      'name' => trim($name),
      'country_id' => $country_id,
      'description' => trim($description),
    ))->execute();
  }

  static function edit($id, $name, $country_id, $description) {
    db_update('kamission')->fields(array(
      'name' => trim($name),
      'country_id' => $country_id,
      'description' => trim($description),
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('kamission')->condition('id', $id)->execute();
  }

  static function getIDByCountryMission($country, $mission) {
    $id = db_query('SELECT kam.id 
                    FROM kamission kam, kacountry kac
                    where 
                     kam.country_id = kac.id and
                     kac.name = :country and 
                     kam.name = :mission', array(':country' => trim($country), ':mission' => trim($mission)))->fetchField();
    return $id;
  }   
  
  static function import($countryid, $missionname, $missiondescription){
    db_merge('kamission')->key(
        array('name' => trim($missionname), 'country_id' => $countryid))->fields(array(
              'country_id' => $countryid,
              'name' => trim($missionname),
              'description' =>  trim($missiondescription),
            ))->execute();
  }
}
