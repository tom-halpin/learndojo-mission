<?php
/**
 * @file
 * Contains \Drupal\mission\TopicAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core;
use Drupal\Core\Ajax;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\TopicStorage;
use Drupal\mission\Data\StrandStorage;
use Drupal\mission\Data\UnitStorage;
use Drupal\mission\Data\TopicTypeStorage;
use Drupal\mission\Data\SupportedSiteStorage;
use Drupal\mission\Data\TermStorage;

class TopicAddForm extends FormBase {
    protected $id;
    protected $selectedmission;
    protected $selectedunit;
    protected $selectedstrand;
    protected $initEditValues;
    
    function getFormId() {
        return 'topic_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $topic = TopicStorage::get($this -> id);

        $countryoptions = $this->getCountries();
        $termoptions = $this -> getTerms();
        $formValues = $form_state -> getValues();
        /*
        if ($topic -> id > 0 ) {
            // form values not set so make initial selections as if editing
            $selectedmission = $topic->missionid;            
            $strandoptions = $this -> getMissionStrands($selectedmission);
            //$strandoptions = $this -> _ajaxGetStrands($selectedmission);
            $selectedstrand = $topic -> strandid;
            $unitoptions = $this -> getStrandUnits($selectedstrand);
            //$unitoptions = $this -> _ajaxGetUnits($selectedstrand);
            $selectedunit = $topic -> unitid;
            $selectedterm = $topic -> termid;
        } else {
            // form values set so make selections based on selected values
            $selectedmission = isset($formValues['mission_id']) ? $formValues['mission_id'] : key($missionoptions);  
            $strandoptions = $this -> getMissionStrands($selectedmission);
            //$strandoptions = $this -> _ajaxGetStrands($selectedmission);
            $selectedstrand = isset($formValues['strand_id']) ? $formValues['strand_id'] : key($strandoptions);
            //$selectedstrand = (array_key_exists($selectedstrand, $strandoptions)) ? $selectedstrand : '';
            $unitoptions = $this -> getStrandUnits($selectedstrand);       
            //$unitoptions = $this -> _ajaxGetUnits($selectedstrand);
            $selectedunit = isset($formValues['unit_id']) ? $formValues['unit_id'] : key($unitoptions);
            //$selectedunit = (array_key_exists($selectedunit, $unitoptions)) ? $selectedunit : '';
            $selectedterm = isset($formValues['term_id']) ? $formValues['term_id'] : '';
            $setdefault = false;
        }
*/
         $setmissiondefault = false;
         $setstranddefault = false;
         $setunitdefault = false;
         $settermdefault = false;

         if (isset($formValues['country_id']))
         {
            $selectedcountry = $formValues['country_id']   ;
         }  
         else if($topic)
         {
             $selectedcountry = $topic -> countryid;             
         }
         else {
             $selectedcountry =  key($countryoptions);
         }
             
         $missionoptions = $this -> getCountryMissions($selectedcountry);
         
         if (isset($formValues['mission_id']))
         {
            $selectedmission = $formValues['mission_id']  ; 
         }
         else if($topic)
         {
             $selectedmission = $topic -> missionid;  
             $setmissiondefault = true;
         }
         else {
             $selectedmission =  key($missionoptions);
         }
                            
         $strandoptions = $this -> getMissionStrands($selectedmission);
         
         if (isset($formValues['strand_id']))
         {
            $selectedstrand = $formValues['strand_id']  ; 
         }
         else if($topic)
         {
             $selectedstrand = $topic -> strandid;  
             $setstranddefault = true;
         }
         else {
             $selectedstrand =  key($strandoptions);
         }
         
         $unitoptions = $this -> getStrandUnits($selectedstrand); 
         
                 
         if (isset($formValues['unit_id']))
         {
            $selectedunit = $formValues['unit_id']   ;
         }  
         else if($topic)
         {
             $selectedunit = $topic -> unitid;
             $setunitdefault = true;             
         }
         else {
             $selectedunit =  key($unitoptions);
         }
         
         if (isset($formValues['term_id']))
         {
            $selectedterm = $formValues['term_id']   ;
         }  
         else if($topic)
         {
             $selectedterm = $topic -> term_id;
             $settermdefault = true;             
         }
         else {
             $selectedterm =  key($termoptions);
         }
         
        $validate = array('::elementValidateRequired');

        $form['country_id'] = array(
            '#type' => 'select',
            '#title' => 'Country Name',
            '#options' => $countryoptions,
            '#required' => TRUE,
            '#form_test_required_error' => t('Please select something.'),
            '#default_value' => $selectedcountry,
            '#ajax' => array(
                'callback' => '::countryChangedAjaxCallback',
                'wrapper' => 'mission_id_replace',
              ),
        );

        if($setmissiondefault)
        {
            $form['mission_id'] = array(
            '#type' => 'select', 
            '#title' => $countryoptions[$selectedcountry] . '-' . t(' Missions'), 
            '#prefix' => '<div id="mission_id_replace">', 
            '#suffix' => '</div>', 
            '#options' => $missionoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
            '#default_value' => $selectedmission, 
            '#ajax' => array(
                'callback' => '::missionChangedAjaxCallback', 
                'wrapper' => 'strand_id_replace', 
                ),             
            );
        }
        else {
            $form['mission_id'] = array(
            '#type' => 'select', 
            '#title' => $countryoptions[$selectedcountry] . '-' . t(' Missions'), 
            '#prefix' => '<div id="mission_id_replace">', 
            '#suffix' => '</div>', 
            '#options' => $missionoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
            //'#default_value' => $selectedmission, 
            '#ajax' => array(
                'callback' => '::missionChangedAjaxCallback', 
                'wrapper' => 'strand_id_replace', 
                ),             
            ); 
        }
                
        if($setstranddefault)
        {
            $form['strand_id'] = array(
            '#type' => 'select', 
            '#title' => $missionoptions[$selectedmission] . '-' . t(' Strands'), 
            '#prefix' => '<div id="strand_id_replace">', 
            '#suffix' => '</div>', 
            '#options' => $strandoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
            '#default_value' => $selectedstrand, 
            '#ajax' => array(
                'callback' => '::strandChangedAjaxCallback', 
                'wrapper' => 'unit_id_replace', 
                ),             
            );
        }
        else {
            $form['strand_id'] = array(
            '#type' => 'select', 
            '#title' => $missionoptions[$selectedmission] . '-' . t(' Strands'), 
            '#prefix' => '<div id="strand_id_replace">', 
            '#suffix' => '</div>', 
            '#options' => $strandoptions, 
            '#required' => TRUE, 
            '#form_test_required_error' => t('Please select something.'), 
            //'#default_value' => $selectedstrand, 
            '#ajax' => array(
                'callback' => '::strandChangedAjaxCallback', 
                'wrapper' => 'unit_id_replace', 
                ),             
            );	
        }
    
        if($setunitdefault)
        {
            /* build array for Unit drop down list */
            $form['unit_id'] = array(
                '#type' => 'select', 
                '#title' => $strandoptions[$selectedstrand] . '-' . t(' Units'), 
                '#prefix' => '<div id="unit_id_replace">', 
                '#suffix' => '</div>', 
                '#options' =>  $unitoptions, 
                '#default_value' => $selectedunit, 
                '#required' => TRUE, 
                '#form_test_required_error' => t('Please select something.'), 
            );
        }
        else
        {
            /* build array for Unit drop down list */
            $form['unit_id'] = array(
                '#type' => 'select', 
                '#title' => $strandoptions[$selectedstrand] . '-' . t(' Units'), 
                '#prefix' => '<div id="unit_id_replace">', 
                '#suffix' => '</div>', 
                '#options' =>  $unitoptions, 
                //'#default_value' => $selectedunit, 
                '#required' => TRUE, 
                '#form_test_required_error' => t('Please select something.'), 
            );  
        
        }

        $form['name'] = array('#type' => 'textfield', '#title' => t('Topic Name'), '#required' => TRUE, '#default_value' => ($topic) ? $topic -> name : '', );

        $form['corecontent'] = array('#type' => 'checkbox', '#title' => t('Core Content'), '#default_value' => ($topic) ? $topic -> corecontent : true, );

        $form['description'] = array('#type' => 'textarea', '#title' => t('Topic Description'), '#required' => TRUE, '#default_value' => ($topic) ? $topic -> description : '', );

        /* build array for Topic Type drop down list */
        foreach (TopicTypeStorage::getAll() as $id => $content) {
            $key = $content -> id;
            $value = $content -> name;
            $topictypeoptions[$key] = $value;
        }

        $form['learning_outcome'] = array('#type' => 'textarea', '#title' => t('Learning Outcome'), '#required' => TRUE, '#default_value' => ($topic) ? $topic -> learning_outcome : '', );

        $form['ka_topic'] = array('#type' => 'textfield', '#title' => t('Topic Title on External Site'), '#required' => TRUE, '#size' => 255, '#default_value' => ($topic) ? $topic -> ka_topic : '', );

        $form['ka_url'] = array('#type' => 'url', '#title' => t('Topic URL on External Site'), '#required' => TRUE, '#size' => 255, '#default_value' => ($topic) ? $topic -> ka_url : '', );

        $form['difficultyindex'] = array('#type' => 'number', '#title' => t('Difficulty Index (between 1 and 5)'), '#required' => TRUE, '#min' => 1, '#max' => 5, '#default_value' => ($topic) ? $topic -> difficultyindex : '1', );

        $form['term_id'] = array('#type' => 'select', '#title' => 'Term', '#options' => $termoptions, '#required' => TRUE, '#form_test_required_error' => t('Please select something.'), '#default_value' => $selectedterm );
        
        $form['weeknumber'] = array('#type' => 'number', '#title' => t('Week Number (between 1 and 52, leave at 1 if not known)'), '#min' => 1, '#max' => 52, '#required' => TRUE, '#default_value' => ($topic) ? $topic -> weeknumber : '1', );

        $form['topictype_id'] = array('#type' => 'select', '#title' => 'Topic Type Name', '#options' => $topictypeoptions, '#required' => TRUE, '#form_test_required_error' => t('Please select something.'), '#default_value' => ($topic) ? $topic -> topictypeid : -1, );

        $form['notes'] = array('#type' => 'textarea', '#title' => t('Notes'), '#required' => TRUE, '#default_value' => ($topic) ? $topic -> notes : '', );

        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($topic) ? t('Save') : t('Add'), );

        $form['actions']['cancel'] = array('#type' => 'submit', '#value' => t('Cancel'), '#submit' => array('::cancelform'), '#limit_validation_errors' => array(), );

        return $form;
    }

    public function countryChangedAjaxCallback($form,  &$form_state)
    {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#mission_id_replace', $form['mission_id']));
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form['strand_id']));
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#unit_id_replace', $form['unit_id']));
        return $ajax_response;
    }
    
    public function missionChangedAjaxCallback($form,  &$form_state)
    {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form['strand_id']));
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#unit_id_replace', $form['unit_id']));
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

    public function strandChangedAjaxCallback($form, &$form_state) {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form['strand_id']));        
        $ajax_response -> addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#unit_id_replace', $form['unit_id']));
        return $ajax_response;
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

    public function getTerms() {
        /* build array for Term drop down list */
        foreach (TermStorage::getAll() as $id => $content) {
            $key = $content -> id;
            $value = $content -> name;
            $termoptions[$key] = $value;
        }
        return $termoptions;
    }
    
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $ka_url = $form_state -> getValue('ka_url');
        if (SupportedSiteStorage::getByDomain($ka_url) == false) {
            $form_state -> setErrorByName('ka_url', 'The URL entered is not one of the sites supported by this application. Please enter a link to a supported site.');
        }
    }

    function submitForm(array &$form, FormStateInterface $form_state) {

        $unit_id = $form_state -> getValue('unit_id');
        $name = $form_state -> getValue('name');
        $description = $form_state -> getValue('description');
        $learning_outcome = $form_state -> getValue('learning_outcome');
        $corecontent = $form_state -> getValue('corecontent');
        $ka_topic = $form_state -> getValue('ka_topic');
        $ka_url = $form_state -> getValue('ka_url');
        $difficultyindex = $form_state -> getValue('difficultyindex');
        $term_id = $form_state -> getValue('term_id');
        $weeknumber = $form_state -> getValue('weeknumber');
        $topictype_id = $form_state -> getValue('topictype_id');
        $notes = $form_state -> getValue('notes');

        if (!empty($this -> id)) {
            try {
                TopicStorage::edit($this -> id, $unit_id, SafeMarkup::checkPlain($name), SafeMarkup::checkPlain($description), $corecontent, SafeMarkup::checkPlain($learning_outcome), SafeMarkup::checkPlain($ka_topic), SafeMarkup::checkPlain($ka_url), SafeMarkup::checkPlain($difficultyindex), $term_id, $weeknumber, $topictype_id, SafeMarkup::checkPlain($notes));
                drupal_set_message(t('Topic: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the topic name entered is unique for the selected unit."), 'error');
                //return;
            }
        } else {
            try {
                TopicStorage::add($unit_id, SafeMarkup::checkPlain($name), SafeMarkup::checkPlain($description), $corecontent, SafeMarkup::checkPlain($learning_outcome), SafeMarkup::checkPlain($ka_topic), SafeMarkup::checkPlain($ka_url), SafeMarkup::checkPlain($difficultyindex), $term_id, $weeknumber, $topictype_id, SafeMarkup::checkPlain($notes));
                drupal_set_message(t('Topic: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the topic name entered is unique for the selected unit."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('topic_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('topic_list');
        return;
    }

}