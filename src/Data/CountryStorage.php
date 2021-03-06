<?php

namespace Drupal\mission\Data;

class CountryStorage {
      
  static function loadGrid($header, $pagesize, $country)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('kacountry', 'ka') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('ka', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);                        
        if (isset($country)) {
          $select->condition('name', '%' . db_like(trim($country)) . '%', 'LIKE');
        }   
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT * FROM {kacountry} order by name asc')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {kacountry} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function getIDByName($name) {
    $id = db_query('SELECT id FROM {kacountry} WHERE name = :name', array(':name' => trim($name)))->fetchField();
    return $id;
  }
  
  static function getIDByUnitID($unitid) {
    $id = db_query('SELECT c.id FROM kacountry c, kamission m, kastrand s, kaunit u 
                    WHERE c.id = m.country_id and m.id = s.mission_id and s.id = u.strand_id and u.id = :unitid', array(':unitid' => $unitid))->fetchField();
    return $id;
  }

  static function add($name, $description) {
    db_insert('kacountry')->fields(array(
      'name' => trim($name),
      'description' => trim($description),
    ))->execute();
  }

  static function edit($id, $name, $description) {
    db_update('kacountry')->fields(array(
      'name' => trim($name),
      'description' => trim($description),
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('kacountry')->condition('id', $id)->execute();
  }
}
