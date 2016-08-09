<?php
/**
 * @file
 * Contains \Drupal\mission\MissionAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;

class MissionAddForm extends FormBase {
    protected $id;

    function getFormId() {
        return 'mission_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $mission = MissionStorage::get($this -> id);

        $form['name'] = array('#type' => 'textfield', '#title' => t('Mission Name'), '#required' => TRUE, '#default_value' => ($mission) ? $mission -> name : '', );
        $form['description'] = array('#type' => 'textarea', '#title' => t('Mission Description'), '#required' => TRUE, '#default_value' => ($mission) ? $mission -> description : '', );
        
        
        /* build array for Mission drop down list */
        foreach (CountryStorage::getAll() as $id => $content) {
            $key = $content -> id;
            $value = $content -> name;
            $countryoptions[$key] = $value;
        }

        $validate = array('::elementValidateRequired');

        $form['country_id'] = array('#type' => 'select', '#title' => 'Country Name', '#options' => $countryoptions, '#required' => TRUE, '#form_test_required_error' => t('Please select something.'),
        //'#element_validate' => $validate,
        '#default_value' => ($mission) ? $mission -> country_id : '', );
                
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($mission) ? t('Save') : t('Add'), );

        $form['actions']['cancel'] = array('#type' => 'submit', '#value' => t('Cancel'), '#submit' => array('::cancelform'), '#limit_validation_errors' => array(), );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

    }

    function submitForm(array &$form, FormStateInterface $form_state) {
            
        $country_id = $form_state -> getValue('country_id');
        $name = $form_state -> getValue('name');
        $description = $form_state -> getValue('description');
        if (!empty($this -> id)) {
            try {
                MissionStorage::edit($this -> id, SafeMarkup::checkPlain($name), SafeMarkup::checkPlain($country_id), SafeMarkup::checkPlain($description));
                drupal_set_message(t('Mission: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the mission name entered is unique for the selected country."), 'error');
                //return;
            }

        } else {
            try {
                MissionStorage::add(SafeMarkup::checkPlain($name), SafeMarkup::checkPlain($country_id),  SafeMarkup::checkPlain($description));
                drupal_set_message(t('Mission: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the mission name entered is unique for the selected country."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('mission_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('mission_list');
        return;
    }

}
