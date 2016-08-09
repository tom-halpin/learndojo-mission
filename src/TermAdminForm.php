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

use Drupal\mission\Data\TermStorage;

include_once 'modal.inc'; 
include_once 'global.inc';

class TermAdminForm extends FormBase {
  protected $id;
  protected $term;
  
  function getFormId() {
    return 'term_admin';
  }

  function buildForm(array $form, FormStateInterface $form_state) {

       $term = null;
       $formValues = $form_state->getValues();
       $term = ($form_state->getValue('term_name') !== null) ? $form_state->getValue('term_name') :  '';
       
        $form['filter'] =  array(
          '#type' => 'details',
          '#title' => t('Filter'),
          '#open' => FALSE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );
        
        $form['filter']['term_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Term Name'),
            '#size' => 60,
            '#default_value' => $term,
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
                
        //Create new term link that uses ajax to open the add term form in a modal window
        $addlink = Url::fromRoute('term_add', array('js' => 'nojs'));
        $addlink->setOptions(array(
          'attributes' => array(
            'class' => array('use-ajax', 'btn', 'btn-info'),
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode(array('height' => 400, 'width' => 500)),
          )
        ));
        $addtext =  array(
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl(t('New Term'), $addlink)->toString(),
          '#attached' => array('library' => array('core/drupal.dialog.ajax'))
        );
           
        # configure the table header columns
        $header = array( 
            array('data' => 'Term ID', 'field' => 'ID'), 
            array('data' => 'Term Name', 'field' => 'name', 'sort' => 'asc'), 
            array('data' => 'Term Description', 'field' => 'description'), 
            array('data' => 'Delete'), );

       # load grid
       $pagesize = GRID_PAGE_SIZE;
       #reset the pager before loading the result set
       pager_default_initialize(0, $pagesize);
       $results = TermStorage::loadGrid($header, $pagesize, $term);

        # configure the table rows, making the first column a link to our 'edit' page and the last column a delete link
        $rows = array();
        foreach ($results as $row) {
            // create links for each row
            $editurl = \Drupal::l(
                        t($row -> id),
                        Url::fromRoute('term_edit', array('id' => $row -> id, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 400, 'width' => 500)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));

            $deleteUrl = \Drupal::l(
                        t('Delete'),
                        Url::fromRoute('term_delete', array('id' => $row -> id, 'name' => $row -> name, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 200, 'width' => 600)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));
            // build row
            $rows[] = array('data' => array($editurl, 
                                        $row -> name, 
                                        $row -> description, 
                                        $deleteUrl));
        }
        // create the table
        $table = array('#theme' => 'table', 
                        '#header' => $header, 
                        '#rows' => $rows, 
                        '#empty' => t('No Terms found!'),
                        '#attributes' => array('id' => 'term-table', ));
                        
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
