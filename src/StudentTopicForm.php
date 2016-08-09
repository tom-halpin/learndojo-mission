<?php
/**
 * @file
 * Contains \Drupal\mission\StudentTopicForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core;
use Drupal\Core\Ajax;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;
use Drupal\mission\Data\UnitStorage;
use Drupal\mission\Data\TopicStorage;

include_once 'modal.inc';
include_once 'global.inc';

class StudentTopicForm extends FormBase {
  protected $countryid;
  protected $missionid;
  protected $strandid;
  protected $countryname;
  protected $missioname;
  protected $strandname;
  protected $defaultunit;
  protected $unit;
  protected $corecontent;
  
  function getFormId() {
    return 'student_topic';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $this->strandid = \Drupal::request()->get('strand_id');
    
    $strand = StrandStorage::get($this->strandid);
    
    $form['search'] =  array(
      '#type' => 'details',
      '#title' => t('Search'),
      '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
    );
  
    $form['search']['country_name'] = array('#type' => 'textfield', '#title' => t('Country'), '#disabled' => 'disabled', );
    $form['search']['country_name']['#value'] = $strand -> countryname;
    
    $form['search']['mission_name'] = array('#type' => 'textfield', '#title' => t('Mission'), '#disabled' => 'disabled', );
    $form['search']['mission_name']['#value'] = $strand -> missionname;
    

    $form['search']['strand_name'] = array('#type' => 'textfield', '#title' => t('Strand'), '#disabled' => 'disabled', );
    $form['search']['strand_name']['#value'] = $strand -> name;
        
    $form['search']['unit_id'] = array('#type' => 'select', 
                        '#title' => $strand -> name . '-' . t(' Units'), 
                        '#options' => $this -> getStrandUnits($this->strandid), 
                        '#required' => TRUE, 
                        '#default_value' => $this->defaultunit, 
                        '#form_test_required_error' => t('Please select something.'),
                        '#attributes' => array('onchange' => 'this.form.submit();'),);
    $this->unit = ($form_state->getValue('unit_id') != null) ? $form_state->getValue('unit_id') : $this->defaultunit;

    $form['search']['corecontent'] = array('#type' => 'checkbox', '#title' => t('Core Content'), '#default_value' => true, '#attributes' => array('onchange' => 'this.form.submit();'),);
    $this->corecontent =  ($form_state->getValue('corecontent') === null) ? true : $form_state->getValue('corecontent') ;
    
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Back',
    );
    
    # configure the table header columns
    $header = array( 
        /*array('data' => 'Country Name', 'field' => 'countryname', 'sort' => 'asc'),
        array('data' => 'Mission Name', 'field' => 'missionname', 'sort' => 'asc'),  
        array('data' => 'Strand Name', 'field' => 'strandname', 'sort' => 'asc'),            
        array('data' => 'Unit Name', 'field' => 'unit name', 'sort' => 'asc'), */
        array('data' => 'Topic Name', 'field' => 'name', 'sort' => 'asc'),
        array('data' => 'Topic Description', 'field' => 'description'),
        array('data' => 'Core Content', 'field' => 'corecontent'),
        array('data' => 'Learning Outcome', 'field' => 'learning_outcome'),
        array('data' => 'Term', 'field' => 'termname'),
        array('data' => 'Week Number', 'field' => 'weeknumber'),
        array('data' => 'Difficulty Index', 'field' => 'difficultyindex'),
        array('data' => 'Topic Type', 'field' => 'topictypename'),
        array('data' => 'Topic Content', 'field' => 'ka_url'));

   # load grid
   $pagesize = GRID_PAGE_SIZE;
   #reset the pager before loading the result set
   pager_default_initialize(0, $pagesize);
   $results = TopicStorage::loadStudentGrid($header, $pagesize, $this->unit, $this->corecontent);
   
    # configure the table rows, making the first column a link to our 'edit' page and the last column a delete link
    $rows = array();
    foreach ($results as $row) {
        // build row
        $rows[] = array('data' => array(
                                    /*$row->countryname,
                                    $row->missionname,
                                    $row->strandname,
                                    $row->unitname,*/
                                    $row -> name, 
                                    $row -> description,
                                    ($row -> corecontent == true) ? 'Yes' : 'No', 
                                    $row -> learning_outcome,
                                    $row -> termname,
                                    $row -> weeknumber,
                                    $row -> difficultyindex,
                                    $row -> topictypename,
                                    t('<a href="' . $row -> ka_url . '" target="_blank"> View Content </a>'),
                                    ));
    }
    // create the table
     $table = array('#theme' => 'table', 
                    '#header' => $header, 
                    '#rows' => $rows, 
                    '#empty' => t('No topics found!'),);
    // create pager
    $pager  = array('#type' => 'pager');  
    
    // return items to be rendered
    return array($form, $table, $pager);
  }

 function buildStudentGrid() {
                  
    return array($table, $pager);
 }

  function validateForm(array &$form, FormStateInterface $form_state) {
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    //$form_state->setRebuild(TRUE);
    $url = Url::fromRoute('student_strand');
    $form_state->setRedirectUrl($url);
  }
  
  function getStrandUnits($strand = '') {
        $unitoptions = array();
        $this->defaultunit = -1;
        /* build array for unit drop down list */
       foreach (UnitStorage::getAllForStrand($strand) as $strandid => $content) {
            if($this->defaultunit == -1)
                $this->defaultunit = $content->id;
            $key = $content -> id;
            $value = $content -> name;
            $unitoptions[$key] = $value;
        }
        //if (isset($strandoptions[$strand])) {
        return $unitoptions;
        //}
    }
}
