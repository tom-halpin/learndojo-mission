<?php

namespace Drupal\mission\Data;

class TopicStorage {

  static function loadGrid($header, $pagesize, $country, $mission, $strand, $unit, $topic)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('katopic', 'kat') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('kat', 
                        array('id', 'name', 'description', 'corecontent', 'learning_outcome', 'ka_topic', 'ka_url', 'difficultyindex', 'term_id', 'weeknumber', 'topictype_id', 'notes')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        $select->addField('kat', 'topictype_id', 'topictypeid');
        $select->addField('kat', 'term_id', 'termid');   
        $select->join("kaunit","kau","kat.unit_id = kau.id");  //join unit table
        $select -> fields('kau', array('id', 'name')) ;
        $select->addField('kau', 'id', 'unitid'); 
        $select->addField('kau', 'name', 'unitname');                                     
        $select->join("kastrand","kas","kau.strand_id = kas.id");  //join strand table
        $select -> fields('kas', array('id', 'name')) ;
        $select->addField('kas', 'id', 'strandid'); 
        $select->addField('kas', 'name', 'strandname');        
        $select->join("kamission","kam","kas.mission_id = kam.id");  //join mission table
        $select -> fields('kam', array('id', 'name')) ;
        $select->addField('kam', 'id', 'missionid'); 
        $select->addField('kam', 'name', 'missionname'); // alias kamission.name
        $select->join("kacountry","kac","kam.country_id = kac.id");  //join country table
        $select -> fields('kac', array('id', 'name')) ;
        $select->addField('kac', 'id', 'countryid'); 
        $select->addField('kac', 'name', 'countryname'); // alias kacountry.name
        $select->join("katopictype","katt","kat.topictype_id = katt.id");  //join topic type table
        $select -> fields('katt', array('name')) ;
        $select->addField('katt', 'name', 'topictypename'); // alias kamission.name    
        $select->join("katerm","katr","kat.term_id = katr.id");  //join term table
        $select -> fields('katr', array('id', 'name')) ;
        $select->addField('katr', 'id', 'termid');  
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
          $select->condition('kau.name', '%' . db_like($unit) . '%', 'LIKE');
        }   
        if (isset($topic)) {
          $select->condition('kat.name', '%' . db_like($topic) . '%', 'LIKE');
        }  
        # execute the query
        $results = $select -> execute();
        return $results;
  }

    static function loadStudentGrid($header, $pagesize, $unitid, $corecontent)
  {
        # build the query requesting built in sort and paging support
        $select = db_select('katopic', 'kat') -> extend('Drupal\Core\Database\Query\PagerSelectExtender') -> extend('Drupal\Core\Database\Query\TableSortExtender');
        # get the desired fields
        $select -> fields('kat', 
                        array('id', 'name', 'description', 'corecontent', 'learning_outcome', 'ka_topic', 'ka_url', 'difficultyindex', 'term_id', 'weeknumber', 'topictype_id', 'notes')) 
                        -> limit($pagesize) 
                        -> orderByHeader($header);
        $select->addField('kat', 'topictype_id', 'topictypeid');
        $select->addField('kat', 'term_id', 'termid');   
        $select->join("kaunit","kau","kat.unit_id = kau.id");  //join unit table
        $select -> fields('kau', array('id', 'name')) ;
        $select->addField('kau', 'id', 'unitid'); 
        $select->addField('kau', 'name', 'unitname');                                     
        $select->join("kastrand","kas","kau.strand_id = kas.id");  //join strand table
        $select -> fields('kas', array('id', 'name')) ;
        $select->addField('kas', 'id', 'strandid'); 
        $select->addField('kas', 'name', 'strandname');        
        $select->join("kamission","kam","kas.mission_id = kam.id");  //join mission table
        $select -> fields('kam', array('id', 'name')) ;
        $select->addField('kam', 'id', 'missionid'); 
        $select->addField('kam', 'name', 'missionname'); // alias kamission.name
        $select->join("kacountry","kac","kam.country_id = kac.id");  //join country table
        $select -> fields('kac', array('id', 'name')) ;
        $select->addField('kac', 'id', 'countryid'); 
        $select->addField('kac', 'name', 'countryname'); // alias kacountry.name        
        $select->join("katopictype","katt","kat.topictype_id = katt.id");  //join topic type table
        $select -> fields('katt', array('name')) ;
        $select->addField('katt', 'name', 'topictypename'); // alias katopictype.name    
        $select->join("katerm","katr","kat.term_id = katr.id");  //join term table
        $select -> fields('katr', array('id', 'name')) ;
        $select->addField('katr', 'id', 'termid'); 
        $select->addField('katr', 'name', 'termname');  
        $select->condition('kau.id', $unitid);
        $select->condition('kat.corecontent', $corecontent);
        # execute the query
        $results = $select -> execute();
        return $results;
  }
  
  static function getAll() {
    $result = db_query('SELECT a.id as missionid, a.name as missionname, 
				b.id as strandid, b.name as strandname, 
				c.id as unitid, c.name as unitname, 
				d.id, d.name, d.description, d.corecontent, d.learning_outcome, d.ka_topic, d.ka_url, d.difficultyindex, d.term_id, d.weeknumber,
				d.topictype_id as topictypeid, e.name as topictypename, d.notes
				FROM kamission a, kastrand b, kaunit c, katopic d, katopictype e, katerm f
				where 
				a.id = b.mission_id AND 
				b.id = c.strand_id AND
				c.id = d.unit_id AND
				e.id = d.topictype_id AND
				f.id = d.term_id')->fetchAllAssoc('id');
    return $result;
  }
  
  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT h.id as countryid, h.name as countryname, 
                a.id as missionid, a.name as missionname, 
				b.id as strandid, b.name as strandname, 
				c.id as unitid, c.name as unitname, 
				d.id, d.name, d.description, d.corecontent, d.learning_outcome, d.ka_topic, d.ka_url, d.difficultyindex, d.term_id, d.weeknumber, 
				d.topictype_id as topictypeid, e.name as topictypename, d.notes
				FROM kamission a, kastrand b, kaunit c, katopic d, katopictype e, katerm f, kacountry h
				where 
				h.id = a.country_id AND
				a.id = b.mission_id AND 
				b.id = c.strand_id AND
				c.id = d.unit_id AND
				e.id = d.topictype_id AND
                f.id = d.term_id AND
				d.id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function add($unit_id, $name, $description, $corecontent, $learning_outcome, $ka_topic, $ka_url, $difficultyindex, $term_id, $weeknumber, $topictype_id, $notes) {
    db_insert('katopic')->fields(array(
	   'unit_id' => $unit_id,
      'name' => $name,
      'description' => $description,
      'corecontent' => $corecontent,
	  'learning_outcome' => $learning_outcome,
	  'ka_topic' => $ka_topic,
	  'ka_url' => $ka_url,
	  'difficultyindex' => $difficultyindex,
	  'term_id' => $term_id,
	  'weeknumber' => $weeknumber, 
	  'topictype_id' => $topictype_id,
	  'notes' => $notes,
    ))->execute();
  }

  static function edit($id, $unit_id, $name, $description, $corecontent, $learning_outcome, $ka_topic, $ka_url, $difficultyindex, $term_id, $weeknumber, $topictype_id, $notes) {
    db_update('katopic')->fields(array(
	  'unit_id' => $unit_id,
      'name' => $name,
      'description' => $description,
      'corecontent' => $corecontent,
	  'learning_outcome' => $learning_outcome,
	  'ka_topic' => $ka_topic,
	  'ka_url' => $ka_url,
	  'difficultyindex' => $difficultyindex,
	  'term_id' => $term_id,
      'weeknumber' => $weeknumber,	  
	  'topictype_id' => $topictype_id,
	  'notes' => $notes,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('katopic')->condition('id', $id)->execute();
  }
}
