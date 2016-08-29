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

use Drupal\mission\Data\TopicStorage;

include_once 'modal.inc'; 
include_once 'global.inc';

class TopicAdminForm extends FormBase {
  protected $id;
  protected $topic;
  
  function getFormId() {
    return 'topic_admin';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
           
       $country = null;
       $mission = null;
       $strand = null;
       $unit = null;
       $topic = null;
       
       $formValues = $form_state->getValues();

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

       $strand = ($form_state->getValue('strand_name') !== null) ? $form_state->getValue('strand_name') :  '';
       
        $form['filter']['strand_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Strand Name'),
            '#size' => 60,
            '#default_value' => $strand,
        );

       $unit = ($form_state->getValue('unit_name') !== null) ? $form_state->getValue('unit_name') :  '';
       
        $form['filter']['unit_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Unit Name'),
            '#size' => 60,
            '#default_value' => $unit,
        );
            
       $topic = ($form_state->getValue('topic_name') !== null) ? $form_state->getValue('topic_name') :  '';
       
        $form['filter']['topic_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Topic Name'),
            '#size' => 60,
            '#default_value' => $topic,
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
                
        //Create new topic link that uses ajax to open the add unit form in a modal window
        $addlink = Url::fromRoute('topic_add', array('js' => 'nojs'));
        $addlink->setOptions(array(
          'attributes' => array(
            'class' => array('use-ajax', 'btn', 'btn-info'),
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode(array('height' => 800, 'width' => 900)),
          )
        ));
        $addtext =  array(
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl(t('New Topic'), $addlink)->toString(),
          '#attached' => array('library' => array('core/drupal.dialog.ajax'))
        );
           
        # configure the table header columns
        $header = array( 
            array('data' => 'Topic ID', 'field' => 'ID'), 
            array('data' => 'Country Name', 'field' => 'countryname', 'sort' => 'asc'),             
            array('data' => 'Mission Name', 'field' => 'missionname', 'sort' => 'asc'),  
            array('data' => 'Strand Name', 'field' => 'strandname', 'sort' => 'asc'),            
            array('data' => 'Unit Name', 'field' => 'unit name', 'sort' => 'asc'), 
            array('data' => 'Topic Name', 'field' => 'name', 'sort' => 'asc'),
            array('data' => 'Topic Description', 'field' => 'description'), 
            array('data' => 'Delete'), );

       # load grid
       $pagesize = GRID_PAGE_SIZE;
       #reset the pager before loading the result set
       pager_default_initialize(0, $pagesize);
       $results = TopicStorage::loadGrid($header, $pagesize, $country, $mission, $strand, $unit, $topic);

        # configure the table rows, making the first column a link to our 'edit' page and the last column a delete link
        $rows = array();
        foreach ($results as $row) {
            // create links for each row
            $editurl = \Drupal::l(
                        t($row -> id),
                        Url::fromRoute('topic_edit', array('id' => $row -> id, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 800, 'width' => 900)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));

            $deleteUrl = \Drupal::l(
                        t('Delete'),
                        Url::fromRoute('topic_delete', array('id' => $row -> id, 'name' => $row -> name, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 200, 'width' => 800)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));
            // build row
            $rows[] = array('data' => array($editurl,
                                        $row->countryname, 
                                        $row->missionname,
                                        $row->strandname,
                                        $row->unitname,
                                        $row -> name, 
                                        $row -> description, 
                                        $deleteUrl));
        }
        // create the table
        $table = array('#theme' => 'table', 
                        '#header' => $header, 
                        '#rows' => $rows, 
                        '#empty' => t('No units found!'),
                        '#attributes' => array('id' => 'unit-table', ));
                        
        // create pager
        $pager = array('#type' => 'pager');
        // return items to be rendered
        return array($form, $addtext, $table, $pager);       
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
