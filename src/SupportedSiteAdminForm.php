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

use Drupal\mission\Data\SupportedSiteStorage;

if(empty(session_id()))
    session_start();

include_once 'modal.inc'; 
include_once 'global.inc';

class SupportedSiteAdminForm extends FormBase {
  protected $id;
  protected $supportedsite;
  
  function getFormId() {
    return 'supportedsite_admin';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
      

       $this -> initFormSessionVariables();
       $supportedsite = $_SESSION['supportedsiteadmin_supportedsite'];
              
       $formValues = $form_state->getValues();
       
        $form = array();
        $form['filter'] =  array(
          '#type' => 'details',
          '#title' => t('Filter'),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );
        
        $form['filter']['supportedsite_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Supported Site Name'),
            '#size' => 60,
            '#default_value' => $supportedsite,
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
                
        //Create new supportedsite link that uses ajax to open the add supportedsite form in a modal window
        $addlink = Url::fromRoute('supportedsite_add', array('js' => 'nojs'));
        $addlink->setOptions(array(
          'attributes' => array(
            'class' => array('use-ajax', 'btn', 'btn-info'),
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode(array('height' => 500, 'width' => 500)),
          )
        ));
        $addtext =  array(
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl(t('New Supported Site'), $addlink)->toString(),
          '#attached' => array('library' => array('core/drupal.dialog.ajax'))
        );
           
        # configure the table header columns
        $header = array( 
            array('data' => 'Supported Site ID', 'field' => 'ID'), 
            array('data' => 'Supported Site Name', 'field' => 'name', 'sort' => 'asc'), 
            array('data' => 'Supported Site Description', 'field' => 'description'), 
            array('data' => 'Delete'), );

       # load grid
       $results = SupportedSiteStorage::loadGrid($header, GRID_PAGE_SIZE, $supportedsite);

        # configure the table rows, making the first column a link to our 'edit' page and the last column a delete link
        $rows = array();
        foreach ($results as $row) {
            // create links for each row
            $editurl = \Drupal::l(
                        t($row -> id),
                        Url::fromRoute('supportedsite_edit', array('id' => $row -> id, 'js' => 'nojs'),
                            array( 'attributes' => array(
                            'class' => array('use-ajax'), 
                            'data-dialog-type' => 'modal',
                            'data-dialog-options' => Json::encode(array('height' => 500, 'width' => 500)),
                            /*'attributes' => array('library' => array('core/drupal.dialog.ajax'))*/
                          ))));

            $deleteUrl = \Drupal::l(
                        t('Delete'),
                        Url::fromRoute('supportedsite_delete', array('id' => $row -> id, 'name' => $row -> name, 'js' => 'nojs'),
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
                        '#empty' => t('No supported sites found!'),
                        '#attributes' => array('id' => 'supportedsite-table', ));
                        
        // create pager
        $pager = array('#type' => 'pager');
        // return items to be rendered
        return array($form, $addtext, $table, $pager);

    }

  function submitForm(array &$form, FormStateInterface $form_state) {
    # store current values in session variables       
    $_SESSION['supportedsiteadmin_supportedsite'] = $form_state->getValue('supportedsite_name');          
     
    //$form_state->setRebuild(TRUE);
    if( $_SESSION['supportedsiteadmin_supportedsite'] == '')
    {
         #reset the pager before loading the result set
         pager_default_initialize(0, GRID_PAGE_SIZE);  
    }
  }
  
  function resetFilter($form, &$form_state) {
     # reset filters so clear session variables
     $_SESSION['supportedsiteadmin_supportedsite'] = '';          
     $form_state->setRebuild(FALSE);
     #reset the pager before loading the result set
     pager_default_initialize(0, GRID_PAGE_SIZE);
  }
   
  function initFormSessionVariables()
  {
     # initialise topic admin form session variables if they haven't already been initialised
     if(!isset($_SESSION['supportedsiteadmin_supportedsite']))
     {    
        $_SESSION['supportedsiteadmin_supportedsite'] = '';
     }
  }   
}
