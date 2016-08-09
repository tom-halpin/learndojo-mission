<?php

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;

use Drupal\mission\Data\MissionStorage;

include_once 'modal.inc';
include_once 'global.inc';

class MissionAdminForm extends FormBase {
  protected $id;
  protected $mission;
  
  function getFormId() {
    return 'mission_admin';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    
       $country = null;
       $mission = null;
       $formValues = $form_state->getValues();
       $country = ($form_state->getValue('country_name') !== null) ? $form_state->getValue('country_name') :  '';
       $mission = ($form_state->getValue('mission_name') !== null) ? $form_state->getValue('mission_name') :  '';
       
        $form = array();
        $form['filter'] =  array(
          '#type' => 'details',
          '#title' => t('Filter'),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );

        $form['filter']['country_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Country Name'),
            '#size' => 60,
            '#default_value' => $country,
        );
             
        $form['filter']['mission_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Mission Name'),
            '#size' => 60,
            '#default_value' => $mission,
        );
        
        $form['filter']['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Filter'),
        );
        
        $form['filter']['clear'] = array(
          '#type' => 'submit',
          '#value' => t('Clear Filter'),
          '#submit' => array('::resetFilter'),
        );
               
                 
        //Create new mission link that uses ajax to open the add mission form in a modal window
        $addlink = Url::fromRoute('mission_add', array('js' => 'nojs'));
        $addlink->setOptions(array(
          'attributes' => array(
            'class' => array('use-ajax', 'btn', 'btn-info'),
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode(array('height' => 400, 'width' => 500)),
          )
        ));
        $addtext =  array(
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl(t('New Mission'), $addlink)->toString(),
          '#attached' => array('library' => array('core/drupal.dialog.ajax'))
        );
           
        # configure the table header columns
        $header = array( 
            array('data' => 'Mission ID', 'field' => 'ID'),
            array('data' => 'Country Name', 'field' => 'countryname', 'sort' => 'asc'), 
            array('data' => 'Mission Name', 'field' => 'name', 'sort' => 'asc'), 
            array('data' => 'Mission Description', 'field' => 'description'), 
            array('data' => 'Delete'), );

       # load grid
       $pagesize = GRID_PAGE_SIZE;
       #reset the pager before loading the result set
       pager_default_initialize(0, $pagesize);
       $results = MissionStorage::loadGrid($header, $pagesize, $country, $mission);

        # configure the table rows, making the first column a link to our 'edit' page and the last column a delete link
        $rows = array();
        foreach ($results as $row) {
            // create links for each row
            $editurl = \Drupal::l(
                        t($row -> id),
                        Url::fromRoute('mission_edit', array('id' => $row -> id, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 400, 'width' => 500)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));

            $deleteUrl = \Drupal::l(
                        t('Delete'),
                        Url::fromRoute('mission_delete', array('id' => $row -> id, 'name' => $row -> name, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 200, 'width' => 600)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));
            // build row
            $rows[] = array('data' => array($editurl, 
                                        $row -> countryname,
                                        $row -> name, 
                                        $row -> description, 
                                        $deleteUrl));
        }
        // create the table
        $table = array('#theme' => 'table', 
                        '#header' => $header, 
                        '#rows' => $rows, 
                        '#empty' => t('No missions found!'),
                        '#attributes' => array('id' => 'mission-table', ));
                        
        // create pager
        $pager = array('#type' => 'pager');
        // return items to be rendered
        return array($form, $addtext, $table, $pager);
  }

  function validateForm(array &$form, FormStateInterface $form_state) {

  }
  
  function submitForm(array &$form, FormStateInterface $form_state) {
    //$form_state->setValue("mission", $form_state->getValue('mission_name'));
    $form_state->setRebuild(TRUE);
    
  }
  
  function resetFilter($form, &$form_state) {
     $form_state->setRebuild(FALSE);
  }
  
}
