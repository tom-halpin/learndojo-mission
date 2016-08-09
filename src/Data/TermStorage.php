<?php

namespace Drupal\mission\Data;

class TermStorage {
      
  static function loadGrid($header, $pagesize, $term)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('katerm', 'ka') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('ka', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        if (isset($term)) {
          $select->condition('name', '%' . db_like($term) . '%', 'LIKE');
        }   
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT * FROM {katerm}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {katerm} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function add($name, $description) {
    db_insert('katerm')->fields(array(
      'name' => $name,
      'description' => $description,
    ))->execute();
  }

  static function edit($id, $name, $description) {
    db_update('katerm')->fields(array(
      'name' => $name,
      'description' => $description,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('katerm')->condition('id', $id)->execute();
  }
}
