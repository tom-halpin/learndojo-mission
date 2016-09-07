<?php
/**
 * @file
 * Contains \Drupal\mission\TopicsForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core;
use Drupal\Core\Ajax;
use Drupal\Core\Url;
use Drupal\Core\Link;

include_once 'global.inc';

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;
use Drupal\mission\Data\UnitStorage;
use Drupal\mission\Data\TopicStorage;

class TopicsForm extends FormBase {

  function getFormId() {
    return 'topics';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
      
       $selectedstrand = \Drupal::request()->get('strandid');
       $strand = StrandStorage::get($selectedstrand); 
       
       $form =  array(
          '#type' => 'details',
          '#title' => t($strand -> missionname . ' - ' . $strand -> name ),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
          '#attributes' => array('class'=> array('panel-primary adjust-border-radius', )),          
        );
        
        $form['strandid'] =  array(
          '#type' => 'details',
          '#title' => t('Select a LearnDojo Topic'),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
          '#attributes' => array('class'=> array('panel-info adjust-border-radius', )),          
        );
        
        $markup = '<div class="container">
                    <h2>Units</h2>
                  </div>
                  <ul class="list-group">';
        
        $form['strandid']['Header'] =  array(
          '#type' => 'markup',
          '#markup' => $markup
        );
        $markup = "";
        foreach (UnitStorage::getAllForStrand($selectedstrand) as $unit) {
            //$key = $content -> id;
            //$value = $content -> name;
            //$unitoptions[$key] = $value;
            $markup .= t ('<li class="list-group-item">
                                <strong> ' . $unit->name . ' </strong>
                                <ul class="list-group">') ;
            foreach (TopicStorage::getAllForUnit($unit -> id) as $topic) {
                $markup .= t('<li class="list-group-item"> <a href="' . $topic -> ka_url . '" target="_blank"> ' . $topic -> name  . ' </a> </li>');    
            }                    
            $markup .= '</ul></li>' ;
        } 

        $form['strandid']['Body'] =  array(
          '#type' => 'markup',
          '#markup' => $markup
        ); 

        
        $markup = '</ul>';
        $form['strandid']['Footer'] =  array(
          '#type' => 'markup',
          '#markup' => $markup
        ); 

        return array($form);
  }

    
  function validateForm(array &$form, FormStateInterface $form_state) {

  }
  
  function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
  
  }

  function resetFilter($form, &$form_state) {
     $form_state->setRebuild(FALSE);
  } 
 
} 

