<?php
/**
 * @file
 * Contains \Drupal\mission\TopicTypeAddForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

use Drupal\mission\Data\TopicTypeStorage;

include_once 'global.inc';

class TopicTypeAddForm extends FormBase {
    protected $id;

    function getFormId() {
        return 'topictype_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this -> id = \Drupal::request() -> get('id');
        $topictype = TopicTypeStorage::get($this -> id);

        $form['name'] = array('#type' => 'textfield', '#title' => t('Topic Type Name'), '#required' => TRUE, '#default_value' => ($topictype) ? $topictype -> name : '',  '#attributes' => array('maxlength' => NAME_LENGTH), );
        $form['description'] = array('#type' => 'textarea', '#title' => t('Topic Type Description'), '#required' => TRUE, '#default_value' => ($topictype) ? $topictype -> description : '',  '#attributes' => array('maxlength' => DESCRIPTION_LENGTH), );
        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array('#type' => 'submit', '#value' => ($topictype) ? t('Save') : t('Add'), );

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
                TopicTypeStorage::edit($this -> id, $name, $description);
                drupal_set_message(t('Topic type: ' . $name . ' has been edited'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the topic type name entered is unique."), 'error');
                //return;
            }
        } else {
            try {
                TopicTypeStorage::add($name, $description);
                drupal_set_message(t('Topic type: ' . $name . ' has been added'));
            } catch(\Exception $e) {
                drupal_set_message(t("Sorry, that didn't work. Please ensure the topic type name entered is unique."), 'error');
                //return;
            }
        }
        $form_state -> setRedirect('topictype_list');
        return;
    }

    function cancelform($form, &$form_state) {
        $form_state -> setRedirect('topictype_list');
        return;
    }

}
