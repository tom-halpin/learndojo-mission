<?php
/**
 * @file
 * Contains \Drupal\mission\StrandAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core;
use Drupal\Core\Ajax;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;

include_once 'global.inc';

class StrandAddForm extends FormBase {
    protected $id;

    function getFormId() {
        return 'strand_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $strand = StrandStorage::get($this -> id);
        $formValues = $form_state -> getValues();

        $setcountrydefault = false;
        $setmissiondefault = false;

        /* build array for Country drop down list */
        $countryoptions = $this->getCountries();
        
        if (isset($formValues['country_id']))
        {
           $selectedcountry = $formValues['country_id']   ;
        }  
        else if($strand)
        {
            $selectedcountry = $strand -> countryid;             
        }        
        else {
            $selectedcountry =  key($countryoptions);
        }
                   
        $missionoptions = $this -> getCountryMissions($selectedcountry);
        
        if (isset($formValues['mission_id']))
        {
           $selectedmission = $formValues['mission_id']  ; 
        }
        else if($strand)
        {
            $selectedmission = $strand -> missionid;             
           $setmissiondefault = true;
        }        
        else {
            $selectedmission =  key($missionoptions);
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
                '#title' => $countryoptions[$selectedcountry]. ' ' . t(' Missions'),
                '#prefix' => '<div id="mission_id_replace">',
                '#suffix' => '</div>',
                '#options' => $this->getCountryMissions($selectedcountry),
                '#required' => TRUE,
                '#form_test_required_error' => t('Please select something.'),
                '#default_value' => $selectedmission,
            );
        }
        else {
            $form['mission_id'] = array(
                '#type' => 'select',
                '#title' => $countryoptions[$selectedcountry]. ' ' . t(' Missions'),
                '#prefix' => '<div id="mission_id_replace">',
                '#suffix' => '</div>',
                '#options' => $this->getCountryMissions($selectedcountry),
                '#required' => TRUE,
                '#form_test_required_error' => t('Please select something.'),
            );            
        }        

         
        $form['name'] = array('#type' => 'textfield', '#title' => t('Strand Name'), '#required' => TRUE, '#default_value' => ($strand) ? $strand -> name : '', '#attributes' => array('maxlength' => NAME_LENGTH), );
        $form['description'] = array('#type' => 'textarea', '#title' => t('Strand Description'), '#required' => TRUE, '#default_value' => ($strand) ? $strand -> description : '',  '#attributes' => array('maxlength' => DESCRIPTION_LENGTH), );


        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($strand) ? t('Save') : t('Add'), );

        $form['actions']['cancel'] = array('#type' => 'submit', '#value' => t('Cancel'), '#submit' => array('::cancelform'), '#limit_validation_errors' => array(), );

        return $form;
    }

    public function getCountries()
    {
        /* build array for Mission drop down list */
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
        /* build array for strand drop down list */
        foreach (MissionStorage::getAllForCountry($country) as $country=>$content) {
            $key = $content->id;
            $value = $content->name;
            $missionoptions[$key] = $value;
        }
        return $missionoptions;
    }

    public function countryChangedAjaxCallback($form,  &$form_state)
    {
        $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
        $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#mission_id_replace', $form['mission_id']));
        return $ajax_response;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

    }

    function submitForm(array &$form, FormStateInterface $form_state) {
        $name = Html::escape($form_state -> getValue('name'));
        $mission_id = $form_state -> getValue('mission_id');
        $description = Html::escape($form_state -> getValue('description'));
        if (!empty($this -> id)) {
            try {
                StrandStorage::edit($this -> id, $name, $mission_id, $description);
                drupal_set_message(t('Strand: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the strand name entered is unique for the selected mission."), 'error');
                //return;
            }

        } else {
            try {
                StrandStorage::add($name, $mission_id, $description);
                drupal_set_message(t('Strand: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the strand name entered is unique for the selected mission."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('strand_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('strand_list');
        return;
    }

}
