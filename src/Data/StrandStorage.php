<?php

namespace Drupal\mission\Data;

class StrandStorage {

  static function loadGrid($header, $pagesize, $country, $mission, $strand)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('kastrand', 'kas') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('kas', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        $select->join("kamission","kam","kas.mission_id = kam.id");  //join mission table
        $select -> fields('kam', array('name')) ;
        $select->addField('kam', 'name', 'missionname'); // alias kacountry.name
        $select->join("kacountry","kac","kam.country_id = kac.id");  //join kacountry table
        $select -> fields('kac', array('name')) ;
        $select->addField('kac', 'name', 'countryname'); // alias kacountry.name
        if (isset($country)) {
          $select->condition('kac.name', '%' . db_like(trim($country)) . '%', 'LIKE');
        }                
        if (isset($mission)) {
          $select->condition('kam.name', '%' . db_like(trim($mission)) . '%', 'LIKE');
        }
        if (isset($strand)) {
          $select->condition('kas.name', '%' . db_like(trim($strand)) . '%', 'LIKE');
        } 
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT s.id, s.mission_id, s.name, s.description, m.name as missionname, m.country_id, c.name as countryname FROM kastrand s, kamission m, kacountry c where s.mission_id = m.id and m.country_id = c.id')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT s.id, s.mission_id as missionid, s.name, s.description, m.name as missionname, m.country_id as countryid, c.name as countryname 
                        FROM kastrand s, kamission m, kacountry c where s.mission_id = m.id and m.country_id = c.id and s.id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }
  
  static function getAllForMission($mission_id) {
    $result = db_query('SELECT s.id, s.mission_id, s.name, s.description, m.name as missionname, m.country_id, c.name as countryname  
                        FROM kastrand s, kamission m, kacountry c where s.mission_id = m.id and m.country_id = c.id and s.mission_id = :mission_id', array(':mission_id' => $mission_id))->fetchAllAssoc('id');
     return $result;
  }
  
  static function add($name, $mission_id, $description) {
    db_insert('kastrand')->fields(array(
      'name' => trim($name),
	  'mission_id' => $mission_id,
      'description' => trim($description),
    ))->execute();
  }

  static function edit($id, $name, $mission_id, $description) {
    db_update('kastrand')->fields(array(
      'name' => trim($name),
	  'mission_id' => $mission_id,
      'description' => trim($description),
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('kastrand')->condition('id', $id)->execute();
  }
}
