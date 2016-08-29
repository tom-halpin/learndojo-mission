<?php
/**
 * @file
 * Contains \Drupal\csvImporter\ImportForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Link;
use Drupal\Core\Url;

use Drupal\mission\Data\MissionStorage;
use Drupal\mission\Data\CountryStorage;
use Drupal\mission\Data\UnitStorage;
use Drupal\mission\Data\TopicTypeStorage;
use Drupal\mission\Data\TermStorage;
use Drupal\mission\Data\TopicStorage;

include_once 'global.inc';
include_once 'importglobal.inc';

require_once 'excel_reader2.php';
        
class ImportForm extends FormBase {

  protected $fid;

  function getFormId() {
    return 'import';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
	 
	//https://www.monarchdigital.com/blog/2012-06-06/upload-and-import-files-your-drupal-site
	$form = array();
    $form['instructions'] =  array(
      '#type' => 'details',
      '#title' => t('Instructions'),
      '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
    );
	$form['instructions']['notes'] = array(
		'#type' => 'markup',
		'#markup' => '<div class="import-notes">Notes: <ul><li>Make sure the file being imported is in the .xls format.</li><li>Be sure to click the "Upload" button after you select the file to import.</li></ul></div>',
		'#upload_location' => 'public://tmp/',
	  );
	  
    $form['instructions']['templatelinks'] =  array(
          '#type' => 'markup',
          '#markup' => Link::fromTextAndUrl('Topic Template File (XLS) - Click to Download',  Url::fromUri('base://modules/mission/templates/topictemplate.xls'))->toString()
    ); 
           
	//$importtypes = array('Mission', 'Strand', 'Unit', 'Topic');
	$importtypes = array('Topic');
	
	$form['import_type'] = array(
		'#type' => 'select',
		'#title' => 'Import Type',
		'#options' => $importtypes,
		'#required' => TRUE,
		'#form_test_required_error' => t('Please select a file to upload.'),
	);
  
    $form['skipheader'] = array('#type' => 'checkbox', '#title' => t('Skip Header Row'), '#default_value' => true );

    $form['import'] = array(
        '#title' => t('Import'),
        '#type' => 'managed_file',
        '#description' => t('The uploaded xls file will be uploaded and processed.'),
        '#upload_location' => 'public://tmp/',
        '#upload_validators' => array(
          'file_validate_extensions' => array('xls'),
        ),
      );
       
	  //Create new mission link that uses ajax to open the add mission form in a modal window
      $form['prevalidate'] = array (
        '#type' => 'submit',
        '#value' => t('Pre-Validate'),
        '#submit' => array('::preValidateImport')
      );
      
      $form['spacer'] = array (
        '#type' => 'markup',
        '#markup' => t('&nbsp;&nbsp;'),
      );          
	  $form['submit'] = array (
		'#type' => 'submit',
		'#value' => t('Import'),
	  );
	  return array($form);
	
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	  
  }

  function preValidateImport($form, &$form_state) {
     $this->ProcessImport($form, $form_state, true);
  }
  
  function submitForm(array &$form, FormStateInterface $form_state) {
     $this->ProcessImport($form, $form_state, false);	  
   }
  
  function cancelform($form, &$form_state) {
    return;
  }

  function ProcessImport($form, &$form_state, $preValidateOnly)
  {
        //clear any messages on the screen from previous imports
    drupal_get_messages();    
    // make sure it is a supported import type
    $importtype = $form_state->getValue('import_type');
    if($importtype != IMPORT_TYPE_TOPIC)
    {
        drupal_set_message($importtype . ' ' . t('Import type selected not yet supported.'), 'error');
        return;
    }
    
    $skipheader = $form_state -> getValue('skipheader');
        
    // Check to make sure that the file was uploaded to the server properly
    // Using the fid get the uri of the file loaded from the file_managed table and process it
    $import = $form_state->getValue('import');
    $fid = $import[0];
    $uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(':fid' => $fid,))->fetchField();
      
      // if we have a file to process
      if(!empty($uri)) {
      if(file_exists(drupal_realpath($uri))) {
        $ext = pathinfo($uri, PATHINFO_EXTENSION); 
        if(strcasecmp ($ext, 'csv') == 0)
        {
          $validateOnly = true;
          if(($errordetected = $this->ProcessCSVFile($importtype, $skipheader, $uri, $validateOnly)) == TRUE)
          {
            // one or more pre-validation errors detected so don't proceed with load
            drupal_set_message('There were one or more errors detected while prevalidating your file. Please correct them before proceeding.', 'error');             
            return;
          }
          else
          {
              if($preValidateOnly == TRUE)
              {
                  // only requested a prevalidate and there were no errors detected so display a message indicating same and return
                   drupal_set_message(t('File successfully pre-validated.'));
                   return;
              }
              else
              {
                // no prevalidation errors so proceed to load data
                $validateOnly = false;
                if(($errordetected = $this->ProcessCSVFile($importtype, $skipheader, $uri, $validateOnly)) == FALSE)
                {
                    drupal_set_message(t('File successfully imported.'));
                }
              }
          }
        }
        else if(strcasecmp ($ext, 'xls') == 0 )
        {
          $validateOnly = true;
          if(($errordetected = $this->ProcessXLSFile($importtype, $skipheader, $uri, $validateOnly))== TRUE)
          {
            // one or more pre-validation errors detected so don't proceed with load
            drupal_set_message('There were one or more errors detected while prevalidating your file. Please correct them before proceeding.', 'error');            
            return;
          }
          else
          {
              if($preValidateOnly == TRUE)
              {
                  // only requested a prevalidate and there were no errors detected so display a message indicating same and return
                   drupal_set_message(t('File successfully pre-validated.'));
                   return;
              }
              else
              {
                // no prevalidation errors so proceed to load data
                $validateOnly = false;
                if(($errordetected = $this->ProcessXLSFile($importtype, $skipheader, $uri, $validateOnly)) == FALSE)
                {
                    drupal_set_message(t('File successfully imported.'));
                }
              }
          }
        }
        else if(strcasecmp ($ext, 'xlsx') == 0)
        {
          $this->ProcessXLSXFile($importtype, $skipheader, $uri);
        }          
      }
      }
      else {
          drupal_set_message(t('There was an error uploading your file. Please contact a System administrator.'), 'error');
      }  
  }

  function ProcessXLSFile($importtype, $skipheader, $uri, $validateOnly)
  {
    $filepath = drupal_realpath($uri);
    $reader = new \Spreadsheet_Excel_Reader($filepath);
    
    $rowcount = 0; $numrows = 0; $numcols = 0; $sheet = 0;
    $rowerror = false;
    $msg = '';
      
    $numrows = $reader->sheets[0]['numRows'];
    $numcols = $reader->sheets[0]['numCols'];
    
    $errordetected = false;
    
    //Start for loop
    $row = 1;
    while($row <= $numrows) {

        $msg = '';
        if($skipheader && $row == 1)
        {
            $row++;
            continue;
        }

        // if validating want to display all errors if importing want to stop on first error
        if($rowerror == true && $validateOnly == false)
            break;
        
        if($importtype == IMPORT_TYPE_TOPIC)
        {
          $country = $reader->val($row, TOPIC_IMPORT_COUNTRY + 1, $sheet);
          //drupal_set_message(t('Country.') . ' ' . $country);          
          $mission = $reader->val($row, TOPIC_IMPORT_MISSON + 1, $sheet);
          $strand = $reader->val($row, TOPIC_IMPORT_STRAND + 1, $sheet);
          $unit = $reader->val($row, TOPIC_IMPORT_UNIT + 1, $sheet);
          $topicname = $reader->val($row, TOPIC_IMPORT_NAME + 1, $sheet);
          $topicdescription = $reader->val($row, TOPIC_IMPORT_DESCRIPTION + 1, $sheet);
          $topicType = $reader->val($row, TOPIC_IMPORT_TOPIC_TYPE + 1, $sheet);
          $coreContent = $reader->val($row, TOPIC_IMPORT_CORE_CONTENT + 1, $sheet);
          $difficultyIndex = $reader->val($row, TOPIC_IMPORT_DIFFICULTY_INDEX + 1, $sheet);
          $externalTopic = $reader->val($row, TOPIC_IMPORT_EXTERNAL_TOPIC + 1, $sheet);
          $externalUrl = $reader->val($row, TOPIC_IMPORT_EXTERNAL_URL + 1, $sheet);
          $learningOutcome = $reader->val($row, TOPIC_IMPORT_LEARNING_OUTCOME + 1, $sheet);
          $notes = $reader->val($row, TOPIC_IMPORT_NOTES + 1, $sheet);
          $term = $reader->val($row, TOPIC_IMPORT_TERM + 1, $sheet);                                                                                
          $weeknumber = $reader->val($row, TOPIC_IMPORT_WEEK_NUMBER + 1, $sheet);
          
          $data = array($country, $mission, $strand, $unit, $topicname, $topicdescription, 
                $topicType, $coreContent, $difficultyIndex, $externalTopic, $externalUrl, $learningOutcome, $notes, $term, $weeknumber);
        }
        $this->ProcessRow($importtype, $data, $row, $numcols, $rowerror, $msg, $validateOnly);
        
        if($errordetected == FALSE && $rowerror == TRUE)
            $errordetected = TRUE;

        // display error message for row if error encountered
        if($rowerror)
        {
           drupal_set_message(t('There was an error uploading your file. :msg', array(':msg' => $msg)), 'error'); 
        }        
        $row++;
    }
    return $errordetected;
  }    
  
  function ProcessCSVFile($importtype, $skipheader, $uri, $validateOnly)
  {
      // Open the csv file
      $handle = fopen(drupal_realpath($uri), "r");
      $rowcount = 0; $numcols = 0;
      $rowerror = false;
      $msg = '';
      
      $errordetected = false;
          
      // Go through each row in the csv and process it. 
      while (($data = fgetcsv($handle, 0, ',')) !== FALSE) {
          
        $msg = '';
        $rowcount = $rowcount + 1;
        if($skipheader && $rowcount == 1)
            continue;  
        
        // if validating want to display all errors if importing want to stop on first error
        if($rowerror == true && $validateOnly == false)
            break;
        
         $this->ProcessRow($importtype, $data, $rowcount, $numcols, $rowerror, $msg, $validateOnly);
         
         if($errordetected == FALSE && $rowerror == TRUE)
            $errordetected = TRUE;
         
        // display error message for row if error encountered
        if($rowerror)
        {
           drupal_set_message(t('There was an error uploading your file. :msg', array(':msg' => $msg)), 'error.'); 
        }                 
      }
      fclose($handle);

      return $errordetected;
  }

  function ProcessXLSXFile($importtype, $skipheader, $uri) {
      module_load_include('inc', 'phpexcel');
      
      // The path to the excel file
      $filepath = drupal_realpath($uri);
      
      $result = phpexcel_import($filepath);
      $retcode = is_array($result);
      if ($retcode) {
        drupal_set_message(t("We did it !"));
      }
      else {
        drupal_set_message(t("Oops ! An error occured !"), 'error');
      }
      return $retcode;
  }
    
  function ProcessRow($importtype, $data, $rowcount, $numcols, &$rowerror, &$msg, $validateOnly)
  {
    if($importtype == IMPORT_TYPE_TOPIC)
    {
        //$countryid = CountryStorage::getIDByName($data[0]);
        $unitid = UnitStorage::getIDByCountryMissionStrandUnit($data[TOPIC_IMPORT_COUNTRY], $data[TOPIC_IMPORT_MISSON], 
                                                                $data[TOPIC_IMPORT_STRAND], $data[TOPIC_IMPORT_UNIT]);
        $topictypeid = TopicTypeStorage::getIDByName($data[TOPIC_IMPORT_TOPIC_TYPE]);  
        $termid = TermStorage::getIDByName($data[TOPIC_IMPORT_TERM]);
                
        //drupal_set_message(t('getIDByCountryMissionStrandUnit .') . ' ' . $data[0] . ' ' . $data[1] . ' ' . $data[2] . ' ' . $data[3] .' ID ' . $unitid);                  
        if(ImportValidator::ValidateTopicRow($data, $rowcount, $numcols, $rowerror, $msg, $unitid, $topictypeid, $termid))
        {
           
          if($validateOnly == FALSE)
            TopicStorage::import($unitid, utf8_encode($data[TOPIC_IMPORT_NAME]), utf8_encode($data[TOPIC_IMPORT_DESCRIPTION]), $topictypeid, $data[TOPIC_IMPORT_CORE_CONTENT], 
                    $data[TOPIC_IMPORT_DIFFICULTY_INDEX] , utf8_encode($data[TOPIC_IMPORT_EXTERNAL_TOPIC]), utf8_encode($data[TOPIC_IMPORT_EXTERNAL_URL]), 
                    utf8_encode($data[TOPIC_IMPORT_LEARNING_OUTCOME]), utf8_encode($data[TOPIC_IMPORT_NOTES]),  $termid, $data[TOPIC_IMPORT_WEEK_NUMBER]);
          
        }
    }
  }
}