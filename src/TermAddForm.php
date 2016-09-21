<?php
/**
 * @file
 * Contains \Drupal\mission\TermAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\TermStorage;

include_once 'global.inc';

class TermAddForm extends FormBase {
    protected $id;

    function getFormId() {
        return 'term_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $term = TermStorage::get($this -> id);

        $form['name'] = array('#type' => 'textfield', '#title' => t('Term Name'), '#required' => TRUE, '#default_value' => ($term) ? $term -> name : '',  '#attributes' => array('maxlength' => NAME_LENGTH), );
        $form['description'] = array('#type' => 'textarea', '#title' => t('Term Description'), '#required' => TRUE, '#default_value' => ($term) ? $term -> description : '',  '#attributes' => array('maxlength' => DESCRIPTION_LENGTH), );
        
                /* build array for Mission drop down list */
        foreach (CountryStorage::getAll() as $id => $content) {
            $key = $content -> id;
            $value = $content -> name;
            $countryoptions[$key] = $value;
        }
        
        $form['country_id'] = array('#type' => 'select', '#title' => 'Country Name', '#options' => $countryoptions, '#required' => TRUE, '#form_test_required_error' => t('Please select something.'),
        //'#element_validate' => $validate,
        '#default_value' => ($term) ? $term -> country_id : '', );
        
        $form['start_date'] = array('#type' => 'date', '#title' => t('Start Date '), '#required' => TRUE, '#default_value' => ($term) ? $term -> start_date : '2016-01-01', '#min' => '2016-01-01' );
        $form['num_weeks'] = array('#type' => 'number', '#title' => t('Num Weeks (between 1 and 53, leave at 1 if not known)'), '#min' => MIN_WEEK_NUMBER, '#max' => MAX_WEEK_NUMBER, '#required' => TRUE, '#default_value' => ($term) ? $term -> num_weeks : '1', );
                
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($term) ? t('Save') : t('Add'), );

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
        $name = Html::escape($form_state -> getValue('name'));
        $startdate = $form_state -> getValue('start_date');
        $numweeks = $form_state -> getValue('num_weeks');
        $description = Html::escape($form_state -> getValue('description'));
        if (!empty($this -> id)) {
            try {
                TermStorage::edit($this -> id, $country_id, $name, $startdate, $numweeks, $description);
                drupal_set_message(t('Term: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the term name entered is unique."), 'error');
                //return;
            }
        } else {
            try {
                TermStorage::add($country_id, $name, $startdate, $numweeks, $description);
                drupal_set_message(t('Topic type: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the term name entered is unique."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('term_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('term_list');
        return;
    }

}
