<?php
/**
 * @file
 * Contains \Drupal\mission\CountryAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

use Drupal\mission\Data\CountryStorage;

include_once 'global.inc';

class CountryAddForm extends FormBase {
    protected $id;

    function getFormId() {
        return 'country_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $country = CountryStorage::get($this -> id);

        $form['name'] = array('#type' => 'textfield', '#title' => t('Country Name'), '#required' => TRUE, '#default_value' => ($country) ? $country -> name : '',  '#attributes' => array('maxlength' => NAME_LENGTH),);
        $form['description'] = array('#type' => 'textarea', '#title' => t('Country Description'), '#required' => TRUE, '#default_value' => ($country) ? $country -> description : '', '#attributes' => array('maxlength' => DESCRIPTION_LENGTH), );
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($country) ? t('Save') : t('Add'), );

        $form['actions']['cancel'] = array('#type' => 'submit', '#value' => t('Cancel'), '#submit' => array('::cancelform'), '#limit_validation_errors' => array(), );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {

    }

    function submitForm(array &$form, FormStateInterface $form_state) {
        $name = Html::escape($form_state -> getValue('name'));
        $description = Html::escape($form_state -> getValue('description'));
        if (!empty($this -> id)) {
            try {
                CountryStorage::edit($this -> id, $name, $description);
                drupal_set_message(t('Country: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the country name entered is unique."), 'error');
                //return;
            }
        } else {
            try {
                CountryStorage::add($name, $description);
                drupal_set_message(t('Country: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the country name entered is unique."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('country_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('country_list');
        return;
    }

}
