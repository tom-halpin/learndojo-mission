<?php

namespace Drupal\mission;

class ImportValidator {

  static function ValidateMissionRow($data, $rowcount, $numcols, &$rowerror, &$msg)
  {
        if($rowcount == 1 && (strtolower($data[0]) == 'name') && (strtolower($data[1]) == 'description'))
        {
            // skip first row   
            return false;               
        }
        else {
            $numcols = count($data);
            if($numcols > 3)
            {
                $msg = t('Invalid number of columns :numcols in file.', array(':numcols' => $numcols));
                $rowerror = true;
                $errorrow = $rowcount;
            }
            if(ImportValidator::isempty($data[0]))
            {
                $msg = $msg . t('Name must be supplied row :rowcount.', array(':rowcount' => $rowcount));
                $rowerror = true;
                $errorrow = $rowcount;
            }
            if(ImportValidator::isempty($data[1]))
            {
                $msg = $msg . t('Description must be supplied row :rowcount.', array(':rowcount' => $rowcount));
                $rowerror = true;
                $errorrow = $rowcount;
            }
        }
      return !$rowerror;
  }
  
  static function ValidateStrandRow($data, $rowcount, $numcols, &$rowerror, &$msg)
  {
      // to be implemented need to validate mission name and id for FK
  }

  static function ValidateUnitRow($data, $rowcount, $numcols, &$rowerror, &$msg)
  {
      // to be implemented need to validate strand name and id for FK
  }

  static function ValidateTopicRow($data, $rowcount, $numcols, &$rowerror, &$msg)
  {
      // to be implemented need to validate unit name and id for FK
  }
  
  private static function isempty($var) {
         $var = trim($var);
         if(isset($var) === true && $var === '') {
            return true;
        }
        else {
            return false;
        }
    }
}