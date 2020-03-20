<?php
  // This starts the PaginationPart2 coding **********************
  // Set pagination controls variable to NULL
  $PaginationControls = '';
  // Only show the pagination co0ntrols if there is more than 1 page
  if($LastPageNumber != 1) {
      // If the current page number is greater than 1 then provide the Previous text and link and provide
      // a link to page 1.
      // If the current page is 1 then do not show the Previous and do not provide a clickable link to page 1.
      if($CurrentPageNumber > 1) {
          $Previous = $CurrentPageNumber - 1;
          $PaginationControls .= '<a href="' . $_SERVER['PHP_SELF'] . '?CurrentPageNumber=' . $Previous . '">Previous</a> &nbsp; &nbsp; ';
          // Render clickable number links that should appear on the left of the target page number.
          for($i = $CurrentPageNumber - 4; $i < $CurrentPageNumber; $i++) {
              if($i > 0) {
                  $PaginationControls .= '<a href="' . $_SERVER['PHP_SELF'] . '?CurrentPageNumber=' . $i . '">' . $i . '</a> &nbsp; ';
              }
          }
      }
      
      // Render the target page number without a link
      $PaginationControls .= '' . $CurrentPageNumber . ' &nbsp; ';
      // Render the clickable number links that should appear on the right of the target page number.
      for($i = $CurrentPageNumber + 1; $i <= $LastPageNumber; $i++) {
          $PaginationControls .= '<a href="' . $_SERVER['PHP_SELF'] . '?CurrentPageNumber=' . $i . '">' . $i . '</a> &nbsp; ';
          if($i >= $CurrentPageNumber + 4) {
              break;
          }
      }
      
      // Render the Next text and link if the current page is not the last page.
      if($CurrentPageNumber != $LastPageNumber) {
          $Next = $CurrentPageNumber + 1;
          $PaginationControls .= ' &nbsp; &nbsp; <a href="' . $_SERVER['PHP_SELF'] . '?CurrentPageNumber=' . $Next . '">Next</a> ';
      }
  }
  // This ends the PaginationPart2 coding **********************
?>