<?php
/**
 * @file
 * Contains \Drupal\mission\SupportedSiteAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

use Drupal\mission\Data\SupportedSiteStorage;

include_once 'global.inc';

class SupportedSiteAddForm extends FormBase {
    protected $id;

    function getFormId() {
        return 'supportedsite_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $supportedsite = SupportedSiteStorage::get($this -> id);

        $form['name'] = array('#type' => 'textfield', '#title' => t('Supported Site Name'), '#required' => TRUE, '#default_value' => ($supportedsite) ? $supportedsite -> name : '', );
        $form['domain'] = array('#type' => 'textfield', '#title' => t('Supported Site Domain (All or part)'), '#required' => TRUE, '#default_value' => ($supportedsite) ? $supportedsite -> domain : '', );
        $form['description'] = array('#type' => 'textarea', '#title' => t('Supported Site Description'), '#required' => TRUE, '#default_value' => ($supportedsite) ? $supportedsite -> description : '', );
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($supportedsite) ? t('Save') : t('Add'), );

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
        $domain = Html::escape($form_state -> getValue('domain'));
        $description = Html::escape($form_state -> getValue('description'));
        if (!empty($this -> id)) {
            try {
                SupportedSiteStorage::edit($this -> id, $name, $domain, $description);
                drupal_set_message(t('Supported site: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the supported site name entered is unique."), 'error');
                //return;
            }
        } else {
            try {
                SupportedSiteStorage::add($name, $domain, $description);
                drupal_set_message(t('Supported site: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the supported site name entered is unique."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('supportedsite_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('supportedsite_list');
        return;
    }

}
