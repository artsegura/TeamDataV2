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
  $TrainingCourse = FindTrainingCourseById($_GET["id"]);

  if (!$TrainingCourse) {
    // page ID was missing or invalid or 
    // page couldn't be found in database
    RedirectTo("manage_training_courses.php");
  }

  $id = $TrainingCourse["TrainingCourseId"];
  $query = "DELETE FROM TrainingCourses WHERE TrainingCourseId = {$id} LIMIT 1";
  $result = mysqli_query($connection, $query);

  if ($result && mysqli_affected_rows($connection) == 1) {
    // Success
    $_SESSION["message"] = "Training Course deleted.";
    RedirectTo("manage_training_courses.php");
  } else {
    // Failure
    $_SESSION["message"] = "Training Course deletion failed.";
    RedirectTo("manage_training_courses.php");
  }

?>

