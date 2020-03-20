<?php
  // This starts the PaginationPart1 coding **********************
  // PageRowsMax is the maximum number of rows allowed per page
  $PageRowsMax = 20;
  // Calculate the last page's page number
  $LastPageNumber = ceil($RowCount/$PageRowsMax);
  // Make sure $LastPageNumber cannot be less than 1
  if($LastPageNumber < 1) {
      $LastPageNumber = 1;
  }
  
  // Get the current page number from from a GET request if it is set
  if(isset($_GET['CurrentPageNumber'])) {
      // Remove all characters except numbers from the CurrentPageNumber in the GET request
      $CurrentPageNumber = preg_replace('#[^0-9]#', '', $_GET['CurrentPageNumber']);
  } else {
      // Set the $CurrentPageNumber to a value of 1
      $CurrentPageNumber = 1;
  }
  // Make sure $CurrentPageNumber is not less than 1 or more than $LastPageNumber
  if($CurrentPageNumber < 1) {
      $CurrentPageNumber = 1;
  } else if($CurrentPageNumber > $LastPageNumber) {
      $CurrentPageNumber = $LastPageNumber;
  }
  // Set the range of rows to query for the current $CurrentPageNumber
  // The text string includes a space in front of LIMIT
  $Limit = ' LIMIT ' .($CurrentPageNumber - 1) * $PageRowsMax .',' .$PageRowsMax;
  // Set a string variable to show the current page number and the total page number
  $PageNumberText = "Page ". $CurrentPageNumber . " of " . $LastPageNumber;
  // This ends the PaginationPart1 coding **********************
?>