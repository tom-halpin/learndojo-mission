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
use Drupal\Core\Url;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;

class StudentStrandForm extends FormBase {
  protected $id;

  function getFormId() {
    return 'student_strand';
  }

  function buildForm(array $form, FormStateInterface $form_state) {

  	/* build array for Country drop down list */
	$countryoptions = $this->getCountries();

	$formValues = $form_state->getValues();


         $setcountrydefault = false;
         $setmissiondefault = false;
         $setstranddefault = false;

         if (isset($formValues['country_id']))
         {
            $selectedcountry = $formValues['country_id']   ;
         }  
         else {
             $selectedcountry =  key($countryoptions);
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
    
	$form['mission_id'] = array(
		'#type' => 'select',
		'#title' => $countryoptions[$selectedcountry]. ' ' . t(' Missions'),
        '#prefix' => '<div id="mission_id_replace">',
        '#suffix' => '</div>',
        '#options' => $this->getCountryMissions($selectedcountry),
		'#required' => TRUE,
		'#form_test_required_error' => t('Please select something.'),
		'#default_value' => $selectedmission,
		'#ajax' => array(
        'callback' => '::missionChangedAjaxCallback',
        'wrapper' => 'strand_id_replace',
      ),		
	);
	
    $form['strand_id'] = array(
        '#type' => 'select',
        '#title' => $missionoptions[$selectedmission]. ' ' . t(' Strands'),
        '#prefix' => '<div id="strand_id_replace">',
        '#suffix' => '</div>',
        '#options' => $this->getMissionStrands($selectedmission),
        '#default_value' => $selectedstrand,
        '#required' => TRUE,
        '#form_test_required_error' => t('Please select something.'),
    );
   
	
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Next',
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

  //  $form_state->setRedirect('student_topic');
    $selectedstrand = $form_state->getValue('strand_id');
	$url = Url::fromRoute('student_topic', array('strand_id' => $selectedstrand));
	$form_state->setRedirectUrl($url);
	return;
  }
 
}
