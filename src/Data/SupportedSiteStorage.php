<?php

namespace Drupal\mission\Data;

class SupportedSiteStorage {
      
  static function loadGrid($header, $pagesize, $supportedsite)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('kasupportedsite', 'ka') -> extend('Drupal\Core\Database\Query\TableSortExtender') -> extend('Drupal\Core\Database\Query\PagerSelectExtender');
        # get the desired fields
        $select -> fields('ka', 
                        array('id', 'name', 'description')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        if (isset($supportedsite)) {
          $select->condition('name', '%' . db_like($supportedsite) . '%', 'LIKE');
        }   
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT * FROM {kasupportedsite}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {kasupportedsite} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function getByDomain($url) {
  	//$domain = 'https://www.test.com';
	$url = str_replace("http://","",$url);
	$url = str_replace("https://","",$url);

    $result = db_query("SELECT * FROM kasupportedsite where instr(:url, domain) > 0", array(':url' => $url))->fetchAllAssoc('id');
    if ($result) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  static function add($name, $domain, $description) {
    db_insert('kasupportedsite')->fields(array(
      'name' => $name,
	  'domain' => $domain,	  
      'description' => $description,
    ))->execute();
  }

  static function edit($id, $name, $domain, $description) {
    db_update('kasupportedsite')->fields(array(
      'name' => $name,
	  'domain' => $domain,
      'description' => $description,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('kasupportedsite')->condition('id', $id)->execute();
  }
}
