<?php
/**
 * @file
 * Contains \Drupal\mission\StudentStrandForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core;
use Drupal\Core\Ajax;

include_once 'global.inc';

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;
use Drupal\mission\Data\UnitStorage;
use Drupal\mission\Data\TopicStorage;

class StudentStrandForm extends FormBase {

  function getFormId() {
    return 'student_strand';
  }

  function buildForm(array $form, FormStateInterface $form_state) {

        $countryoptions = $this->getCountries();
        
        $formValues = $form_state -> getValues();
        
         if (isset($formValues['country_id']))
         {
            $selectedcountry = $formValues['country_id']   ;
         }  
         else {
             $selectedcountry = key($countryoptions);
         }
             
         $missionoptions = $this -> getCountryMissions($selectedcountry);
         
         if (isset($formValues['mission_id']))
         {
            $selectedmission = $formValues['mission_id']  ; 
         }
         else {
             $selectedmission =  key($missionoptions);
         }
                            
         $strandoptions = $this -> getMissionStrands($selectedmission);
         
         if (isset($formValues['strand_id']))
         {
            $selectedstrand = $formValues['strand_id']  ; 
         }
         else {
             $selectedstrand =  key($strandoptions);
         }
         
         $unitoptions = $this -> getStrandUnits($selectedstrand); 
         
         $selectedunit = '';        
         if (isset($formValues['unit_id']))
         {
            $selectedunit = $formValues['unit_id']   ;
         }  
         /*else {
             $selectedunit =  key($unitoptions);
         }*/
         
        
        $validate = array('::elementValidateRequired');

        $form =  array(
          '#type' => 'details',
          '#title' => t('Select a Learning Dojo Unit'),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );
        
        $form['country_id'] = array(
            '#type' => 'select',
            '#title' => 'Country Name',
            '#options' => $countryoptions,
            '#required' => TRUE,
            '#form_test_required_error' => t('Please select something.'),
            '#ajax' => array(
                'callback' => '::countryChangedAjaxCallback',
                'wrapper' => 'mission_id_replace',
              ),
        );

        $form['mission_id'] = array(
            '#type' => 'select', 
            '#title' => t('Mission'), 
            '#prefix' => '<div id="mission_id_replace">', 
            '#suffix' => '</div>', 
            '#options' => $missionoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
            '#ajax' => array(
                'callback' => '::missionChangedAjaxCallback', 
                'wrapper' => 'strand_id_replace', 
                ),             
        ); 
        
            
        $form['strand_id'] = array(
            '#type' => 'select', 
            '#title' => t('Strand'), 
            '#prefix' => '<div id="strand_id_replace">', 
            '#suffix' => '</div>', 
            '#options' => $strandoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
            '#ajax' => array(
                'callback' => '::strandChangedAjaxCallback', 
                'wrapper' => 'unit_id_replace', 
                ),             
        );  


        /* build array for Unit drop down list */
        $form['unit_id'] = array(
            '#type' => 'select', 
            '#title' => t('Unit'), 
            '#prefix' => '<div id="unit_id_replace">', 
            '#suffix' => '</div>', 
            '#options' =>  $unitoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
        );  

        $form['corecontent'] = array('#type' => 'checkbox', '#title' => t('Core Content Only'), '#default_value' => true, '#attributes' => array('onchange' => 'this.form.submit();'),);
        $corecontent =  ($form_state->getValue('corecontent') === null) ? true : $form_state->getValue('corecontent') ;
         
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Search'),
        );
        
        $form['clear'] = array(
          '#type' => 'submit',
          '#value' => t('Reset'),
          '#submit' => array('::resetFilter'),
        );
                
        # configure the table header columns
        $header = array( 
            /*array('data' => 'Country', 'field' => 'countryname', 'sort' => 'asc'),*/
            array('data' => 'Mission', 'field' => 'missionname', 'sort' => 'asc'),  
            array('data' => 'Strand', 'field' => 'strandname', 'sort' => 'asc'),            
            array('data' => 'Unit', 'field' => 'unit name', 'sort' => 'asc'),
            array('data' => 'Topic', 'field' => 'name', 'sort' => 'asc'),
            array('data' => 'Topic Description', 'field' => 'description'),
            array('data' => 'Core Content', 'field' => 'corecontent'),
            /*array('data' => 'Learning Outcome', 'field' => 'learning_outcome'),
            array('data' => 'Term', 'field' => 'termname'),
            array('data' => 'Week Number', 'field' => 'weeknumber'),
            array('data' => 'Difficulty Index', 'field' => 'difficultyindex'),*/
            array('data' => 'Topic Type', 'field' => 'topictypename'),
            array('data' => 'Topic Content', 'field' => 'ka_url'));
        
        # load grid
        $pagesize = GRID_PAGE_SIZE;
        #reset the pager before loading the result set
        pager_default_initialize(0, $pagesize);
        $results = TopicStorage::loadStudentGrid($header, $pagesize, $selectedunit, $corecontent);
           
        # configure the table rows
        $rows = array();
        foreach ($results as $row) {
            // build row
            $rows[] = array('data' => array(
                                        /*$row->countryname,*/
                                        $row->missionname,
                                        $row->strandname,
                                        $row->unitname,
                                        $row -> name, 
                                        $row -> description,
                                        ($row -> corecontent == true) ? 'Yes' : 'No', 
                                        /*$row -> learning_outcome,*/
                                        /*$row -> termname,
                                        $row -> weeknumber,
                                        $row -> difficultyindex,*/
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

    public function countryChangedAjaxCallback($form,  &$form_state)
    {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        // three parts returned in buildForm when creating form, $form, $table and $pager so when specifying what to replace div with have to use index into form parts array
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#mission_id_replace', $form[0]['mission_id']));
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form[0]['strand_id']));
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#unit_id_replace', $form[0]['unit_id']));
        return $ajax_response;
    }
    
    public function missionChangedAjaxCallback($form,  &$form_state)
    {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        // three parts returned in buildForm when creating form, $form, $table and $pager so when specifying what to replace div with have to use index into form parts array
        $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form[0]['strand_id']));
        $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#unit_id_replace', $form[0]['unit_id']));
        return $ajax_response;
    }

    public function strandChangedAjaxCallback($form,  &$form_state)
    {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        // three parts returned in buildForm when creating form, $form, $table and $pager so when specifying what to replace div with have to use index into form parts array
        $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#unit_id_replace', $form[0]['unit_id']));
        return $ajax_response;
    }
        
    public function getCountries()
    {
        /* build array for Country drop down list */
        foreach (CountryStorage::getAll() as $id=>$content) {
            $key = $content->id;
            $value = $content->name;
            $countryoptions[$key] = $value;
        }
        return $countryoptions;
    }
    
    function getCountryMissions($country = '')
    {
        $missionoptions = array();
        /* build array for mission drop down list */
        foreach (MissionStorage::getAllForCountry($country) as $country=>$content) {
            $key = $content->id;
            $value = $content->name;
            $missionoptions[$key] = $value;
        }
        return $missionoptions;
    }
    
    function getMissionStrands($mission = '') {
        $strandoptions = array();
        /* build array for strand drop down list */
        foreach (StrandStorage::getAllForMission($mission) as $missionid => $content) {
            $key = $content -> id;
            $value = $content -> name;
            $strandoptions[$key] = $value;
        }
        //if (isset($strandoptions[$strand])) {
        return $strandoptions;
        //}
    }
    
    function getStrandUnits($strand = '') {
        $unitoptions = array();
        /* build array for unit drop down list */
        foreach (UnitStorage::getAllForStrand($strand) as $strandid => $content) {
            $key = $content -> id;
            $value = $content -> name;
            $unitoptions[$key] = $value;
        }
        //if (isset($strandoptions[$strand])) {
        return $unitoptions;
        //}
    }    
    
  function validateForm(array &$form, FormStateInterface $form_state) {

  }
  
  function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  
  }

  function resetFilter($form, &$form_state) {
     $form_state->setRebuild(FALSE);
  } 
 
} 


