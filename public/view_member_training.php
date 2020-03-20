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
  $MemberTraining = FindAllMemberTrainingById($_GET["id"]);

  if (!$Member) {
    // admin ID was missing or invalid or 
    // admin couldn't be found in database
    RedirectTo("manage_member_training.php");
  }
?>

<?php include("../includes/layouts/header.php"); ?>

<div id="main">
  <div id="navigation">
    &nbsp;
  </div>
  <div id="page">
    <?php echo message(); ?>
    <h2>Manage Member Training: <?php echo htmlentities($Member["FirstName"]) . " " . htmlentities($Member["LastName"]); ?></h2>
    <table>
      <tr>
        <th style="text-align: left; width: 100px;">Date Taken</th>
        <th style="text-align: left; width: 200px;">Course</th>
        <th colspan="2" style="text-align: left;">Actions</th>
      </tr>
    <?php while($Training = mysqli_fetch_assoc($MemberTraining)) { ?>
      <tr>
        <td>
            <?php
                // Convert a MySQL date string into a Unix date.
                $ModifiedMySQLDate = strtotime($Training["DateTaken"]);
                // Format the Unix date for display.
                $ModifiedDate = date("m/d/Y", $ModifiedMySQLDate);
                echo $ModifiedDate;
            ?>
        </td>
        <td><?php echo htmlentities($Training["CourseName"]); ?></td>
        <td><a href="edit_member_training.php?id=<?php echo urlencode($Member["MemberId"]); ?>&trainingtakenid=<?php echo urlencode($Training["TrainingTakenId"]); ?>">Edit</a></td>
        <td><a href="delete_member_training.php?id=<?php echo urlencode($Member["MemberId"]); ?>&trainingtakenid=<?php echo urlencode($Training["TrainingTakenId"]); ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
      </tr>
    <?php } ?>
    </table>
    <br/>
    <br />
    <br />
    <a href="new_member_training.php?id=<?php echo urlencode($Member["MemberId"]); ?>">Add new training</a>
    <br />
    <br />
    <a href="manage_member_training.php">Manage Member Training</a>
  </div>
</div>

<?php include("../includes/layouts/footer.php"); ?>