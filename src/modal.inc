<?php

  function modal($js = 'nojs') {
  
    if ($js == 'ajax') {
      $options = array(
        'dialogClass' => 'popup-dialog-class',
        //'width' => '75%', // apply or override the width of the dialog
      );
      $response = new AjaxResponse();
      $response->addCommand(new OpenModalDialogCommand(t(''), t(''), $options));
      return $response;
    } else {
      return t('To access this application Javascript is required.');
    }
  }
?>