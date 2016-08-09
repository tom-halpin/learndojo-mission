<?php
/**
 * @file
 * Contains \Drupal\csvImporter\ImportForm.
 */

namespace Drupal\mission;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;

define('IMPORT_TYPE_MISSION', '0'); 
define('IMPORT_TYPE_STRAND', '1'); 
define('IMPORT_TYPE_UNIT', '2'); 
define('IMPORT_TYPE_TOPIC', '3'); 

class ImportForm extends FormBase {

  protected $fid;

  function getFormId() {
    return 'import';
  }

  function buildForm(array $form, FormStateInterface $form_state) {
	
	 
	//https://www.monarchdigital.com/blog/2012-06-06/upload-and-import-files-your-drupal-site
	$form['notes'] = array(
		'#type' => 'markup',
		'#markup' => '<div class="import-notes">Notes: <ul><li>Make sure the file being imported is in a .csv format.</li><li>Be sure to click the "Upload" button after you select a csv file to import.</li></ul></div>',
		'#upload_location' => 'public://tmp/',
	  );
	  
	$importtypes = array('Mission', 'Strand', 'Unit', 'Topic');
	
	$form['import_type'] = array(
		'#type' => 'select',
		'#title' => 'Import Type',
		'#options' => $importtypes,
		'#required' => TRUE,
		'#form_test_required_error' => t('Please select a file to upload.'),
	);
  
	$form['import'] = array(
		'#title' => t('Import'),
		'#type' => 'managed_file',
		'#description' => t('The uploaded csv will be uploaded and processed.'),
		'#upload_location' => 'public://tmp/',
		'#upload_validators' => array(
		  'file_validate_extensions' => array('csv'),
		),
	  );
	  
	  $form['submit'] = array (
		'#type' => 'submit',
		'#value' => t('Import'),
	  );
	  return $form;
	
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	  
  }


  function submitForm(array &$form, FormStateInterface $form_state) {
	  
	//clear any messages on the screen from previous imports
	drupal_get_messages();	  
	// make sure it is a supported import type
	$importtype = $form_state->getValue('import_type');
	if($importtype != IMPORT_TYPE_MISSION)
	{
		drupal_set_message(t('Import type selected not supported.'), 'error');
		return;
	}
	
	// Check to make sure that the file was uploaded to the server properly
	// Using the fid get the uri of the file loaded from the file_managed table and process it
	$import = $form_state->getValue('import');
	$fid = $import[0];
	$uri = db_query("SELECT uri FROM {file_managed} WHERE fid = :fid", array(':fid' => $fid,))->fetchField();
	  
	  // if we have a file to process
	  if(!empty($uri)) {
		if(file_exists(drupal_realpath($uri))) { 
		  // Open the csv file
		  $handle = fopen(drupal_realpath($uri), "r");
		  $rowcount = 0; $numcols = 0;
		  $rowerror = false;
		  $msg = '';
		  
		  // Go through each row in the csv and process it. 
		  while (($data = fgetcsv($handle, 0, ',')) !== FALSE && $rowerror == false) {
				$rowcount = $rowcount + 1;
				if($importtype == IMPORT_TYPE_MISSION)
				{
					if(ImportValidator::ValidateMissionRow($data, $rowcount, $numcols, $rowerror, $msg))
					{
						db_merge('kamission')->key(
						array('name' => $data[0]))->fields(array(
							  'name' => $data[0],
							  'description' =>  $data[1],
							))->execute();
					}
				}
				else if($importtype == IMPORT_TYPE_STRAND)
				{
					if(ImportValidator::ValidateStrandRow($data, $rowcount, $numcols, $rowerror, $msg))
					{
						;
					}
				}
				else if($importtype == IMPORT_TYPE_UNIT)
				{
					if(ImportValidator::ValidateUnitRow($data, $rowcount, $numcols, $rowerror, $msg))
					{
						;
					}
				}
				else if($importtype == IMPORT_TYPE_TOPIC)
				{
					if(ImportValidator::ValidateTopicRow($data, $rowcount, $numcols, $rowerror, $msg))
					{
						;
					}
				}
				
		  }
		  fclose($handle);
		// display results
		  if($rowerror)
		  {
			 drupal_set_message(t('There was an error uploading your file. :msg', array(':msg' => $msg)), 'error'); 
		  }
		  else
		  {
			 drupal_set_message(t('File successfully imported.'));
		  }
		}
	  }
	  else {
		drupal_set_message(t('There was an error uploading your file. Please contact a System administrator.'), 'error');
	  }	  
  }
  
    function cancelform($form, &$form_state) {
	
    return;
  }
}