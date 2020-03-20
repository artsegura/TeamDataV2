<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php 
    // use the function ConfirmLoggedIn to see if the current user    
    // is allowed to view this page
    ConfirmLoggedIn();
?>

<?php
    $User = FindMemberById($_SESSION["MemberId"]);
    
    // Check to see if the current user can edit members.
    // If not, then the user cannot have access to this page.
    if($User["EditMembers"] == 0) {
        RedirectTo("index.php");
    }
?>

<?php
  $Member = FindMemberById($_GET["id"]);

  if (!$Member) {
    // page ID was missing or invalid or 
    // page couldn't be found in database
    RedirectTo("manage_members.php");
  }

  $id = $Member["MemberId"];
  $query = "DELETE FROM Members WHERE MemberId = {$id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    // Success
    $_SESSION["message"] = "Member deleted.";
    RedirectTo("manage_members.php");
  } else {
    // Failure
    $_SESSION["message"] = "Member deletion failed.";
    RedirectTo("manage_members.php");
  }

?>
