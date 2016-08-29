<?php

namespace Drupal\mission\Data;

class TopicTypeStorage {

  static function loadGrid($header, $pagesize, $topictype)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('katopictype', 'ka') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('ka', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        if (isset($topictype)) {
          $select->condition('name', '%' . db_like(trim($topictype)) . '%', 'LIKE');
        }                           
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT * FROM {katopictype}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {katopictype} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function add($name, $description) {
    db_insert('katopictype')->fields(array(
      'name' => trim($name),
      'description' => trim($description),
    ))->execute();
  }

  static function edit($id, $name, $description) {
    db_update('katopictype')->fields(array(
      'name' => trim($name),
      'description' => trim($description),
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('katopictype')->condition('id', $id)->execute();
  }
  
  static function getIDByName($name) {
    $id = db_query('SELECT id FROM {katopictype} WHERE name = :name', array(':name' => trim($name)))->fetchField();
    return $id;
  }  
}
?>