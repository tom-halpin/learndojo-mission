<?php
/**
 * @file
 * Contains \Drupal\mission\UnitAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;
use Drupal\mission\Data\UnitStorage;

include_once 'global.inc';

class UnitAddForm extends FormBase {
  protected $id;

  function getFormId() {
    return 'unit_add';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $this->id = \Drupal::request()->get('id');
    $unit = UnitStorage::get($this->id);

    $countryoptions = $this->getCountries();
	$formValues = $form_state->getValues();

         $setmissiondefault = false;	
         $setstranddefault = false;

         if (isset($formValues['country_id']))
         {
            $selectedcountry = $formValues['country_id']   ;
         }  
         else if($unit)
         {
             $selectedcountry = $unit -> countryid;             
         }
         else {
             $selectedcountry =  key($countryoptions);
         }
                   
         $missionoptions = $this -> getCountryMissions($selectedcountry);
         
         if (isset($formValues['mission_id']))
         {
            $selectedmission = $formValues['mission_id']  ; 
         }
         else if($unit)
         {
             $selectedmission = $unit -> mission_id;  
             $setmissiondefault = true;
         }
         else {
             $selectedmission =  key($missionoptions);
         }
         
         $strandoptions = $this -> getMissionStrands($selectedmission); 
         
                 
         if (isset($formValues['strand_id']))
         {
            $selectedstrand = $formValues['strand_id']   ;
         }  
         else if($unit)
         {
             $selectedstrand = $unit -> strand_id;
             $setstranddefault = true;             
         }
         else {
             $selectedstrand =  key($strandoptions);
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
            /* build array for Unit drop down list */
            $form['strand_id'] = array(
                '#type' => 'select', 
                '#title' => $missionoptions[$selectedmission] . '-' . t(' Strands'), 
                '#prefix' => '<div id="strand_id_replace">', 
                '#suffix' => '</div>', 
                '#options' =>  $strandoptions, 
                '#default_value' => $selectedstrand, 
                '#required' => TRUE, 
                '#form_test_required_error' => t('Please select something.'), 
            );
        }
        else
        {
            /* build array for Unit drop down list */
            $form['strand_id'] = array(
                '#type' => 'select', 
                '#title' => $missionoptions[$selectedmission] . '-' . t(' Strands'), 
                '#prefix' => '<div id="strand_id_replace">', 
                '#suffix' => '</div>', 
                '#options' =>  $strandoptions, 
                //'#default_value' => $selectedstrand, 
                '#required' => TRUE, 
                '#form_test_required_error' => t('Please select something.'), 
            ); 
        }
  
	$form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Unit Name'),
	  '#required' => TRUE,
      '#default_value' => ($unit) ? $unit->name : '',
      '#attributes' => array('maxlength' => NAME_LENGTH),
    );
    $form['description'] = array(
      '#type' => 'textarea',
      '#title' => t('Unit Description'),
	  '#required' => TRUE,
      '#default_value' => ($unit) ? $unit->description : '',
      '#attributes' => array('maxlength' => DESCRIPTION_LENGTH),      
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => ($unit) ? t('Save') : t('Add'),
    );

	$form['actions']['cancel'] = array(
		'#type'   => 'submit',
		'#value'  => t('Cancel'),
		'#submit' => array('::cancelform'),
		'#limit_validation_errors' => array(),
	);
  
    return $form;
  }

public function countryChangedAjaxCallback($form,  &$form_state)
{
    $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
    $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#mission_id_replace', $form['mission_id']));
    $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form['strand_id']));
    return $ajax_response;
}

public function missionChangedAjaxCallback($form,  &$form_state)
{
    $ajax_response = new \Drupal\Core\Ajax\AjaxResponse();
    $ajax_response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand('#strand_id_replace', $form['strand_id']));
    return $ajax_response;
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

function getMissionStrands($mission = '')
{
    $strandoptions = array();
    /* build array for strand drop down list */
    foreach (StrandStorage::getAllForMission($mission) as $missionid=>$content) {
        $key = $content->id;
        $value = $content->name;
        $strandoptions[$key] = $value;
    }
    return $strandoptions;
}
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    $name = Html::escape($form_state->getValue('name'));
	$strand_id = $form_state->getValue('strand_id');
    $description = Html::escape($form_state->getValue('description'));
    if (!empty($this->id)) {
        try {
      UnitStorage::edit($this->id, $name, $strand_id, $description);
      drupal_set_message(t('Unit: ' . $name . ' has been edited'));
        }
        catch(\Exception $e)
        {
            drupal_set_message(t("Sorry, that didn't work. Please ensure the unit name entered is unique for the selected strand."), 'error');
            //return;
        }

    }
    else {
		try
		{
			UnitStorage::add($name, $strand_id, $description);
            drupal_set_message(t('Unit: ' . $name . ' has been added'));
	  	}
		catch(\Exception $e)
		{
			drupal_set_message(t("Sorry, that didn't work. Please ensure the unit name entered is unique for the selected strand."), 'error');
			//return;
		}
    }
    $form_state->setRedirect('unit_list');
    return;
  }
  
  function cancelform($form, &$form_state) {
	$form_state->setRedirect('unit_list');
    return;
  }
}
