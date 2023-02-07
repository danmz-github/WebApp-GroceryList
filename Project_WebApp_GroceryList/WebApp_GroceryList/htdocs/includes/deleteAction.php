<!-- 
    Daniel Zayas 
    CS 415 - Design of Database Systems
    Assignment #10 - Web Application - Due: 4/12/2019, before 4:00 pm
-->

<?php
    // Include the database connection file.
    include_once('../mysqli_connect.php');
    
    // If record delete request is submitted
    if (isset($_POST['bulk_delete_submit']))
    {
        // If id array is not empty
        if (!empty($_POST['checked_id']))
        {
            // Get all selected IDs and convert it to a string.
            $idString = implode(',', $_POST['checked_id']);
            
            // Delete records from the database.
            $delete = "DELETE FROM grocery WHERE id IN ($idString)";
            $dResult = @mysqli_query($dbc, $delete);
            
            // If delete is successful
            if($dResult)
            {
                $statusMessage = '<br />Selected item(s) have been deleted from your grocery list.';
            }
            else
            {
                $statusMessage = '<br />An error occured, please try again.';
            }
        }
        else
        {
            if($numRecords > 0)
            {
                $statusMessage = '<br />Select at least 1 item to delete.';
            }
        }
    } // END: if (isset($_POST['bulk_delete_submit']))
?>