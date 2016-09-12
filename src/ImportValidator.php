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
    if(strlen($data[TOPIC_IMPORT_COUNTRY]) > NAME_LENGTH)
    {
        $msg = $msg . t('Country name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }  
    if(ImportValidator::isempty($data[TOPIC_IMPORT_MISSION]))
    {
        $msg = $msg . t('Mission must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[TOPIC_IMPORT_MISSION]) > NAME_LENGTH)
    {
        $msg = $msg . t('Mission name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[TOPIC_IMPORT_STRAND]))
    {
        $msg = $msg . t('Strand must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[TOPIC_IMPORT_STRAND]) > NAME_LENGTH)
    {
        $msg = $msg . t('Strand name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[TOPIC_IMPORT_UNIT]))
    {
        $msg = $msg . t('Unit must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }   
    if(strlen($data[TOPIC_IMPORT_UNIT]) > NAME_LENGTH)
    {
        $msg = $msg . t('Unit name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[TOPIC_IMPORT_NAME]))
    {
        $msg = $msg . t('Topic Name must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[TOPIC_IMPORT_NAME]) > NAME_LENGTH)
    {
        $msg = $msg . t('Topic name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[TOPIC_IMPORT_DESCRIPTION]))
    {
        $msg = $msg . t('Topic Description must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[TOPIC_IMPORT_DESCRIPTION]) > DESCRIPTION_LENGTH)
    {
        $msg = $msg . t('Topic Description cannot be longer than :len. Row :rowcount.', array(':len' => DESCRIPTION_LENGTH, ':rowcount' => $rowcount));
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
    if(strlen($data[TOPIC_IMPORT_EXTERNAL_TOPIC]) > NAME_LENGTH)
    {
        $msg = $msg . t('External Topic cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
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
    if(strlen($data[TOPIC_IMPORT_LEARNING_OUTCOME]) > LEARNING_OUTCOME_LENGTH)
    {
        $msg = $msg . t('Learning Outcome cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_NOTES]))
    {
        $msg = $msg . t('Notes must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[TOPIC_IMPORT_NOTES]) > DESCRIPTION_LENGTH)
    {
        $msg = $msg . t('Notes cannot be longer than :len. Row :rowcount.', array(':len' => DESCRIPTION_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(ImportValidator::isempty($data[TOPIC_IMPORT_TERM]))
    {
        $msg = $msg . t('Term must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[TOPIC_IMPORT_TERM]) > NAME_LENGTH)
    {
        $msg = $msg . t('Term cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
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
        $msg = $msg . t('Country ' . $data[TOPIC_IMPORT_COUNTRY] . ' ' . 'Mission ' . $data[TOPIC_IMPORT_MISSION] . ' ' . 'Strand ' . $data[TOPIC_IMPORT_STRAND] . ' ' . 'Unit ' . $data[TOPIC_IMPORT_UNIT] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
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

  static function ValidateUnitRow($data, $rowcount, $numcols, &$rowerror, &$msg, $strandid)
  {
    $rowerror = false;
    $numcols = count($data);
    
    if($numcols != UNIT_IMPORT_NUM_COLUMNS)
    {
        $msg = t('Invalid number of columns :numcols in file.', array(':numcols' => $numcols));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(ImportValidator::isempty($data[UNIT_IMPORT_COUNTRY]))
    {
        $msg = $msg . t('Country must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[UNIT_IMPORT_COUNTRY]) > NAME_LENGTH)
    {
        $msg = $msg . t('Country name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }  
    if(ImportValidator::isempty($data[UNIT_IMPORT_MISSION]))
    {
        $msg = $msg . t('Mission must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[UNIT_IMPORT_MISSION]) > NAME_LENGTH)
    {
        $msg = $msg . t('Mission name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[UNIT_IMPORT_STRAND]))
    {
        $msg = $msg . t('Strand must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[UNIT_IMPORT_STRAND]) > NAME_LENGTH)
    {
        $msg = $msg . t('Strand name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[UNIT_IMPORT_NAME]))
    {
        $msg = $msg . t('Unit Name must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[UNIT_IMPORT_NAME]) > NAME_LENGTH)
    {
        $msg = $msg . t('Unit name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[UNIT_IMPORT_DESCRIPTION]))
    {
        $msg = $msg . t('Unit Description must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[UNIT_IMPORT_DESCRIPTION]) > DESCRIPTION_LENGTH)
    {
        $msg = $msg . t('Unit Description cannot be longer than :len. Row :rowcount.', array(':len' => DESCRIPTION_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    //drupal_set_message('Import Validator ID ' . ' ' . $countryid); 
    if(($strandid >= 1) == FALSE)
    {
        $msg = $msg . t('Country ' . $data[UNIT_IMPORT_COUNTRY] . ' ' . 'Mission ' . $data[UNIT_IMPORT_MISSION] . ' ' . 'Strand ' . $data[UNIT_IMPORT_STRAND] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;                
    }
    return !$rowerror;
  }

  static function ValidateStrandRow($data, $rowcount, $numcols, &$rowerror, &$msg, $missionid)
  {
    $rowerror = false;
    $numcols = count($data);
    
    if($numcols != STRAND_IMPORT_NUM_COLUMNS)
    {
        $msg = t('Invalid number of columns :numcols in file.', array(':numcols' => $numcols));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(ImportValidator::isempty($data[STRAND_IMPORT_COUNTRY]))
    {
        $msg = $msg . t('Country must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[STRAND_IMPORT_COUNTRY]) > NAME_LENGTH)
    {
        $msg = $msg . t('Country name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }  
    if(ImportValidator::isempty($data[STRAND_IMPORT_MISSION]))
    {
        $msg = $msg . t('Mission must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[STRAND_IMPORT_MISSION]) > NAME_LENGTH)
    {
        $msg = $msg . t('Mission name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    
    if(ImportValidator::isempty($data[STRAND_IMPORT_NAME]))
    {
        $msg = $msg . t('Strand Name must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[STRAND_IMPORT_NAME]) > NAME_LENGTH)
    {
        $msg = $msg . t('Strand name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[STRAND_IMPORT_DESCRIPTION]))
    {
        $msg = $msg . t('Strand Description must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[STRAND_IMPORT_DESCRIPTION]) > DESCRIPTION_LENGTH)
    {
        $msg = $msg . t('Strand Description cannot be longer than :len. Row :rowcount.', array(':len' => DESCRIPTION_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    //drupal_set_message('Import Validator ID ' . ' ' . $countryid); 
    if(($missionid >= 1) == FALSE)
    {
        $msg = $msg . t('Country ' . $data[STRAND_IMPORT_COUNTRY] . ' ' . 'Mission ' . $data[STRAND_IMPORT_MISSION] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;                
    }
    return !$rowerror;
  }

  static function ValidateMissionRow($data, $rowcount, $numcols, &$rowerror, &$msg, $countryid)
  {
    $rowerror = false;
    $numcols = count($data);
    
    //drupal_set_message(t($rowcount . ' ' . $numcols . ' ' . MISSION_IMPORT_NUM_COLUMNS));
      
    if($numcols != MISSION_IMPORT_NUM_COLUMNS)
    {
        $msg = t('Invalid number of columns :numcols in file.', array(':numcols' => $numcols));
        $rowerror = true;
        $errorrow = $rowcount;
            drupal_set_message(MISSION_IMPORT_NUM_COLUMNS . ' ' . $numcols);
    }
    if(ImportValidator::isempty($data[MISSION_IMPORT_COUNTRY]))
    {
        $msg = $msg . t('Country must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }
    if(strlen($data[MISSION_IMPORT_COUNTRY]) > NAME_LENGTH)
    {
        $msg = $msg . t('Country name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }  
    if(ImportValidator::isempty($data[MISSION_IMPORT_NAME]))
    {
        $msg = $msg . t('Mission Name must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[MISSION_IMPORT_NAME]) > NAME_LENGTH)
    {
        $msg = $msg . t('Mission name cannot be longer than :len. Row :rowcount.', array(':len' => NAME_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    if(ImportValidator::isempty($data[MISSION_IMPORT_DESCRIPTION]))
    {
        $msg = $msg . t('Mission Description must be supplied. Row :rowcount.', array(':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    } 
    if(strlen($data[MISSION_IMPORT_DESCRIPTION]) > DESCRIPTION_LENGTH)
    {
        $msg = $msg . t('Mission Description cannot be longer than :len. Row :rowcount.', array(':len' => DESCRIPTION_LENGTH, ':rowcount' => $rowcount));
        $rowerror = true;
        $errorrow = $rowcount;
    }    
    //drupal_set_message('Import Validator ID ' . ' ' . $countryid); 
    if(($countryid >= 1) == FALSE)
    {
        $msg = $msg . t('Country ' . $data[MISSION_IMPORT_COUNTRY] . ' not defined. Row :rowcount.', array(':rowcount' => $rowcount));
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