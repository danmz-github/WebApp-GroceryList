<!-- 
    Daniel Zayas 
    CS 415 - Design of Database Systems
    Assignment #10 - Web Application - Due: 4/12/2019, before 4:00 pm
-->

    <?php include_once ('includes/deleteAction.php'); ?>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery-3.3.1.min.js" charset="utf-8"></script>
        <script type="text/javascript">
            function delete_confirm()
            {
                if($('.checkbox:checked').length > 0)
                {
                    var result = confirm("Are you sure you want to delete the selected item(s) from your grocery list?");
                    if(result){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else
                {
                    alert('Select at least 1 item to delete.');
                    return false;
                }
            }
        
            $(document).ready(function()
            {
                $('#select_all').on('click', function()
                {
                    if(this.checked)
                    {
                        $('.checkbox').each(function()
                        {
                            this.checked = true;
                        });
                    }
                    else
                    {
                        $('.checkbox').each(function()
                        {
                            this.checked = false;
                        });
                    }
                });
	
                $('.checkbox').on('click', function()
                {
                    if($('.checkbox:checked').length == $('.checkbox').length)
                    {
                        $('#select_all').prop('checked', true);
                    }
                    else
                    {
                        $('#select_all').prop('checked', false);
                    }
                });
            });
        </script>
    </head>

    <!-- Display the page title and the header. -->
    <?php 
        $pageTitle = 'Grocery List: Remove an Item';
        include ('includes/header.html'); 
    ?>

    <!-- Display the status message. -->
    <?php if (!empty($statusMessage)) { ?>
        <div class="alert alert-success"><?php echo $statusMessage; ?></div>
    <?php } ?>
    
    <!-- Display the headline for this page. -->
    <h1>Remove an Item from Your Grocery List</h1>

    <form name="bulk_action_form" action="" method="post" onSubmit="return delete_confirm();">
        <?php
            require_once ('../mysqli_connect.php'); // Connect to the db.

            // Make the query:
            $query = "SELECT * FROM grocery ORDER BY id ASC";
            $result = @mysqli_query($dbc, $query); // Run the query.

            // Count the number of returned rows, and format it with comma as the thousands separator:
            $itemCount = mysqli_num_rows($result);
            $formattedItemCount = number_format($itemCount);
        
            if ($itemCount > 0) // If the query ran OK, then display the records. 
            { 
                // Indicate how many items are in the grocery list.
                echo ($itemCount == 1) ? "<p>There is only $itemCount item in your grocery list.</p>\n<br />" : "<p>There are currently $formattedItemCount items in your grocery list.</p>\n<br />";    
    
                // Table header
                echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
                        <tr>
                            <td align="left"><input type="checkbox" id="select_all" value="" /></td>
                            <td align="left"><b>Item Name</b></td>
                            <td align="left"><b>Item Type</b></td>
                            <td align="left"><b>Price</b></td>
                            <td align="left"><b>Quantity</b></td>
                        </tr>';

                // Initiate rowColor variable.
                $rowColor = '#ffffff';
                
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {    
                        // Switch the background color for the row.
                        $rowColor = ($rowColor == '#eeeeee' ? '#ffffff' : '#eeeeee'); 
                    
                        echo 
                        '<tr bgcolor="' . $rowColor . '">' .
                            '<td align="left"><input type="checkbox" name="checked_id[]" class="checkbox" value="' . $row['id'] . '" /></td>' .
                            '<td align="left">' . $row['itemName'] . '</td>' .
                            '<td align="left">' . $row['itemType'] . '</td>' .
                            '<td align="left">' . '$' . $row['price'] . '</td>' .
                            '<td align="left">' . $row['quantity'] . '</td>' .
                        '</tr>';
                }
                
                echo '</table>'; // Close the table.
                
                mysqli_free_result($result); // Free up the resources.
                
                echo '<input type="submit" name="bulk_delete_submit" value="Delete" />'; 
            }
            else
            {
                echo '<p class="error">There are currently no items in your grocery list.</p>';    
            } 
        
            mysqli_close($dbc); // Close the connection to the database.
        ?>
    </form>

    <?php include ('includes/footer.html'); ?>