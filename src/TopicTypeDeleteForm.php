<?php

namespace Drupal\mission;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

use Drupal\mission\Data\TopicTypeStorage;

class TopicTypeDeleteForm extends ConfirmFormBase {
  protected $id;
  protected $name;
  
  function getFormId() {
    return 'topictype_delete';
  }

  function getQuestion() {
    return t('Are you sure you want to delete Topic Type ' . $this->name . '?'); 
  }
  
  function getDescription() {
    return t('This action cannot be undone so only do this if you are sure!');
  }

  function getConfirmText() {
    return t('Delete');
  }

  function getCancelText() {
     // note known issue with Cancel button alignment when form displayed modally that https://www.drupal.org/node/2253257 should be fixed in forthcoming patch
    return t('Cancel');
  }
  
  function getCancelUrl() {
    return new Url('topictype_list');
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $this->id = \Drupal::request()->get('id');
    $this->name = \Drupal::request()->get('name');
    
    //$topictype = TopicTypeStorage::get($this->id);
    return parent::buildForm($form, $form_state);
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    try
    {
        topictypeStorage::delete($this->id);
        drupal_set_message(t('Topic Type: ' . $this->name . ' has been deleted.'  ));
    }
    catch(\Exception $e) {
        drupal_set_message(t("Sorry, that did not work. Please ensure Topic Type: " . $this->name . " is not being used in the definition of a topic."), 'error');
        //return;
    }
    $form_state->setRedirect('topictype_list');
  }
}