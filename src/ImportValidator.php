<?php

namespace Drupal\mission;

use Drupal\mission\Data\SupportedSiteStorage;

include_once 'global.inc';
include_once 'importglobal.inc';

class ImportValidator {

  static function ValidateTopicRow($data, $rowcount, $numcols, &$rowerror, &$msg, $unitid, $topictypeid, $termid)
  {
    $rowerror = false;
    $numcols = count($data);
    
    if($numcols != TOPIC_IMPORT_NUM_COLUMNS)
    {
        $msg = t('Invalid number of columns :numcols in file.', array(':numcols' => $numcols));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(ImportValidator::isempty($data[TOPIC_IMPORT_COUNTRY]))
    {
        $msg = $msg . t('Country must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }            
    if(ImportValidator::isempty($data[TOPIC_IMPORT_MISSON]))
    {
        $msg = $msg . t('Mission must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(ImportValidator::isempty($data[TOPIC_IMPORT_STRAND]))
    {
        $msg = $msg . t('Strand must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(ImportValidator::isempty($data[TOPIC_IMPORT_UNIT]))
    {
        $msg = $msg . t('Unit must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }   
    if(ImportValidator::isempty($data[TOPIC_IMPORT_NAME]))
    {
        $msg = $msg . t('Topic Name must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_DESCRIPTION]))
    {
        $msg = $msg . t('Topic Description must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_TOPIC_TYPE]))
    {
        $msg = $msg . t('Topic Type must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_CORE_CONTENT]))
    {
        $msg = $msg . t('Core Content must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(($data[TOPIC_IMPORT_CORE_CONTENT] != 0) && ($data[TOPIC_IMPORT_CORE_CONTENT] != 1))
    {
        $msg = $msg . t('Core Content :corecontent must a boolean value :rowcount.', array(':corecontent' => $data[TOPIC_IMPORT_CORE_CONTENT], ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_DIFFICULTY_INDEX]))
    {
        $msg = $msg . t('Difficulty Index must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(is_int($data[TOPIC_IMPORT_DIFFICULTY_INDEX]) == FALSE)
    {
        $msg = $msg . t('Difficulty Index must an integer value :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    else if($data[TOPIC_IMPORT_DIFFICULTY_INDEX] < MIN_DIFFICULTY_INDEX || $data[TOPIC_IMPORT_DIFFICULTY_INDEX] > MAX_DIFFICULTY_INDEX) {
        $msg = $msg . t('Difficulty Index must an integer value between :min and :max. Row :rowcount.', array(':min' => MIN_DIFFICULTY_INDEX, ':max' => MAX_DIFFICULTY_INDEX, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;	
    }
    if(ImportValidator::isempty($data[TOPIC_IMPORT_EXTERNAL_TOPIC]))
    {
        $msg = $msg . t('External Topic must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_EXTERNAL_URL]))
    {
        $msg = $msg . t('External Url must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if (SupportedSiteStorage::getByDomain($data[TOPIC_IMPORT_EXTERNAL_URL]) == false) {
        $msg = $msg . t('External Url is not one for the sites supported by this application. Please enter a link to a supported site. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(ImportValidator::isempty($data[TOPIC_IMPORT_LEARNING_OUTCOME]))
    {
        $msg = $msg . t('Learning Outcome must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 

    if(ImportValidator::isempty($data[TOPIC_IMPORT_NOTES]))
    {
        $msg = $msg . t('Notes must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_TERM]))
    {
        $msg = $msg . t('Term must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_WEEK_NUMBER]))
    {
        $msg = $msg . t('Week Number must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(is_int($data[TOPIC_IMPORT_WEEK_NUMBER]) == FALSE)
    {
        $msg = $msg . t('Week Number must an integer value. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    else if($data[TOPIC_IMPORT_WEEK_NUMBER] < MIN_WEEK_NUMBER || $data[TOPIC_IMPORT_WEEK_NUMBER] > MAX_WEEK_NUMBER) {
        $msg = $msg . t('Week Number must an integer value between :min and :max. Row :rowcount.', array(':min' => MIN_WEEK_NUMBER, ':max' => MAX_WEEK_NUMBER, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;  
    }    
    //drupal_set_message('Import Validator ID ' . ' ' . $countryid); 
    if(($unitid >= 1) == FALSE)
    {
        $msg = $msg . t('Country ' . $data[TOPIC_IMPORT_COUNTRY] . ' ' . 'Mission ' . $data[TOPIC_IMPORT_MISSON] . ' ' . 'Strand ' . $data[TOPIC_IMPORT_STRAND] . ' ' . 'Unit ' . $data[TOPIC_IMPORT_UNIT] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;                
    }
    if(($termid >= 1) == FALSE)
    {
        $msg = $msg . t('Term ' . $data[TOPIC_IMPORT_TERM] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;                
    }
    if(($topictypeid >= 1) == FALSE)
    {
        $msg = $msg . t('Topic Type ' . $data[TOPIC_IMPORT_TOPIC_TYPE] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;                
    }    
    return !$rowerror;
  }
  
  private static function isempty($var) {
         $var = trim($var);
         if(isset($var) === true && trim($var) === '') {
            return true;
        }
        else {
            return false;
        }
        
    }
}