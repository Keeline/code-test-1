<?php

  # process settings.conf file and place values in array $settings
  # James D. Keeline
  # 2016-04-06
  
  # file to process (same directory in this example)
  $file = 'settings.conf';
  
  # expected variables
  $expected = array('host', 'server_id', 'server_load_alarm', 'user', 'verbose', 'test_mode', 'debug_mode', 'log_file_path', 'send_notifications');
  
  # expected values
  $true     = array('true',  'on',  'yes');
  $false    = array('false', 'off', 'no');
  $comment  = array('#');  // since the spec did not offer the option for multi-character comment markers, will assume a single character
  $equal    = '=';
  
  # initialize $settings array
  foreach ($expected as $var)
  {
    $settings[$var] = NULL;
  }
  
  # open file (use of this function depends on fopen configuration in php.ini)
  $lines    = file($file);
  
  # loop through file and take actions
  foreach ($lines as $L)
  {
    # ignore comments
    if (in_array(substr($L,0,1), $comment)) continue;  // 1 assumes that comment marker is a single character per spec example
    
    # process settings lines
    if (stripos($L, $equal))  // line includes an equal sign for setting a value
    {
      #split line
      $regex = "|^([A-Za-z_0-9]+)[ ]?" . $equal . "[ ]?(.+)$|i";
      if (preg_match($regex, $L, $matches))
      {
        # print_r($matches); print "\n\n";  // diagnostic for regex
        
        $var = trim($matches[1]);
        $val = trim($matches[2]);
        
        if (in_array(strtolower($val), $true))
        {
          $val = TRUE;
        }
        else if (in_array(strtolower($val), $false))
        {
          $val = FALSE;
        }
        
        if (is_numeric($val))
        {
          $val = 0 + $val;
        }
        
        if (in_array($var, $expected))
        {
          $settings[$var] = $val;
        }
      }
    }
  }

  # if all expected values must not be NULL, report on validity of file
  $valid = TRUE;
  foreach ($settings as $var=>$val)
  {
    # print "$var : $val\n";  // diagnostic for validation
    if ($val === NULL)
    {
      $valid = FALSE;
      break;
    }
  }
  print "\nFile $file is ";
  print ($valid) ? "valid" : "missing one or more required values";
  print ".\n\n";

  # display $settings
  print "Settings loaded are:\n\n";
  var_dump($settings);
?>