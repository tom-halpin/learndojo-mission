<?php

namespace Drupal\mission\Data;

class UnitStorage {

  static function loadGrid($header, $pagesize, $country, $mission, $strand, $unit)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('kaunit', 'kau') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('kau', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        $select->join("kastrand","kas","kau.strand_id = kas.id");  //join strand table
        $select -> fields('kas', array('name')) ;
        $select->addField('kas', 'name', 'strandname'); // alias kamission.name        
        $select->join("kamission","kam","kas.mission_id = kam.id");  //join mission table
        $select -> fields('kam', array('name')) ;
        $select->addField('kam', 'name', 'missionname'); // alias kamission.name
        $select->join("kacountry","kac","kam.country_id = kac.id");  //join mission table
        $select -> fields('kac', array('name')) ;
        $select->addField('kac', 'name', 'countryname'); // alias kamission.name        
        if (isset($country)) {
          $select->condition('kac.name', '%' . db_like($country) . '%', 'LIKE');
        }        
        if (isset($mission)) {
          $select->condition('kam.name', '%' . db_like($mission) . '%', 'LIKE');
        }
        if (isset($strand)) {
          $select->condition('kas.name', '%' . db_like($strand) . '%', 'LIKE');
        }  
        if (isset($unit)) {
          $select->condition('kas.name', '%' . db_like($unit) . '%', 'LIKE');
        }         
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT u.id, u.strand_id, u.name, u.description, s.name as strandname, s.mission_id, m.name as missionname FROM kaunit u, kastrand s, kamission m where u.strand_id = s.id and s.mission_id = m.id')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT u.id, u.strand_id, u.name, u.description, s.name as strandname, s.mission_id as mission_id, m.name as missionname, c.id as countryid, c.name as countryname 
                        FROM kaunit u, kastrand s, kamission m, kacountry as c where u.strand_id = s.id and s.mission_id = m.id and m.country_id = c.id and u.id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function getAllForStrand($strand_id) {
    $result = db_query('SELECT u.id, u.strand_id as strand_id, u.name, u.description, s.mission_id as mission_id, s.name as strandname, m.name as missionname, c.id as countryid, c.name as countryname 
                        FROM kaunit u, kastrand s, kamission m, kacountry as c where u.strand_id = s.id and s.mission_id = m.id and m.country_id = c.id and u.strand_id = :strand_id', array(':strand_id' => $strand_id))->fetchAllAssoc('id');
     return $result;
  }
  
  static function add($name, $strand_id, $description) {
    db_insert('kaunit')->fields(array(
      'name' => $name,
	  'strand_id' => $strand_id,
      'description' => $description,
    ))->execute();
  }

  static function edit($id, $name, $strand_id, $description) {
    db_update('kaunit')->fields(array(
      'name' => $name,
	  'strand_id' => $strand_id,
      'description' => $description,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('kaunit')->condition('id', $id)->execute();
  }
}
