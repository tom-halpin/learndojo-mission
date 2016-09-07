<?php
/**
 * @file
 * Contains \Drupal\mission\ClassesForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core;
use Drupal\Core\Ajax;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;

include_once 'global.inc';

use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\StrandStorage;
use Drupal\mission\Data\UnitStorage;
use Drupal\mission\Data\TopicStorage;

class ClassesForm extends FormBase {

  function getFormId() {
    return 'classes';
  }

  function buildForm(array $form, FormStateInterface $form_state) {

        $form =  array(
          '#type' => 'details',
          '#title' => t('Select a LearnDojo Strand'),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
          '#attributes' => array('class'=> array('panel-primary adjust-border-radius', )),
        );

        # configure the table header columns
        $countryid =1 ; $missionid =1 ;
       
        $countryMissions = MissionStorage::getAllForCountry($countryid);
        $countryMissionRows = array();
        
        $i = 0;
        
        foreach ($countryMissions as $countryMissionRow) {
            
            $markup = '';
            $missionid = $countryMissionRow -> id;
            $panelColor = 'panel-info';
            if($i == 0)
            {
                // first mission so create bootstrap container, first row and column
                $markup = '<div class="container">
                    <h2>Classes</h2>
                    <div class="row text-center pad-top">
                    <div class="col-md-4 col-sm-4 col-xs-4">';
            }
            else if( $i % 2 == 1)
            {
                // odd number so close the previously opened column and start a new column
                $markup = '</div>
                           <div class="col-md-4 col-sm-4 col-xs-4">';
                $panelColor = 'panel-info';
            }
            else if($i > 0 && $i % 2 == 0)
            {
                // even number so close previous column and row, add a spacer and then start a new row and column
                $markup = '</div></div><div>&nbsp;</div>
                <div class="row text-center pad-top">
                <div class="col-md-4 col-sm-4 col-xs-4">';
                $panelColor = 'panel-info';
            }


                $form[$countryid][$missionid] =  array(
                  '#type' => 'markup',
                  '#markup' => $markup
                );


            $form[$countryid][$missionid]['main'] =  array(
              '#type' => 'details',
              '#title' => t('<div><i class="glyphicon glyphicon-folder-open"></i>  ' . $countryMissionRow->name . ' </div>'),
              '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.  
              '#attributes' => array('class'=> array( t($panelColor . ' adjust-border-radius'), ),
              '#prefix' => '<div class="panel-group">',
              '#suffix' => '</div>'
              ) 
            );
     
            $missionStrandResults = StrandStorage::getAllForMission($missionid);
    
            # configure the table rows, 
            $missionStrandRows = array();
            foreach ($missionStrandResults as $missionStrandRow) {
                // create links for each row
                $topicurl = \Drupal::l(
                            t($missionStrandRow -> name),
                            Url::fromRoute('topics', array('strandid' => $missionStrandRow -> id)));
                // build row
                $missionStrandRows[] = array('data' => array($topicurl));
            }
            
            // create the table
            $form[$countryid][$missionid]['main'][$missionid] = array('#theme' => 'table', 
                            '#header' => $header, 
                            '#rows' => $missionStrandRows, 
                            '#empty' => t('No strands found!'),
                            '#attributes' => array('id' => 'strand-table', 'class' => 'table-no-striping'));       
            $i = $i+1;
        }

        if($i > 1)
        {
            // finished loop displayed at least one mission so close off open column, row and container.
            $form[$countryid][$missionid]['main']['suffix'] =  array(
              '#type' => 'markup',
              '#markup' => '</div></div></div>'
            );
        }

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

