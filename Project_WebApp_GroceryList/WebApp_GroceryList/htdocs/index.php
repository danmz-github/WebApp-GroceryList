<!-- 
    Daniel Zayas 
    CS 415 - Design of Database Systems
    Assignment #10 - Web Application - Due: 4/12/2019, before 4:00 pm
-->

    <?php
        #  Script - index.php
        $pageTitle = 'Grocery List Web App';
        include ('includes/header.html'); 
    ?>
        
    <h1>Your List of Grocery Items</h1>

    <?php 
        function displaySortedGroceryItems()
        {
            require ('../mysqli_connect.php'); // Connect to the db.
            
            // Determine the sort.
            // Default is by item name.
            $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'nameASC';

            // Determine the sorting order:
            switch ($sort) 
            {
                case 'typeASC':
                    $order_by = 'itemType ASC';
                    break;
                case 'typeDESC':
                    $order_by = 'itemType DESC';
                    break;
                case 'priceASC':
                    $order_by = 'price ASC';
                    break;
                case 'priceDESC':
                    $order_by = 'price DESC';
                    break;
                case 'quantityASC':
                    $order_by = 'quantity ASC';
                    break;
                case 'quantityDESC':
                    $order_by = 'quantity DESC';
                    break;
                case 'nameASC':
                    $order_by = 'itemName ASC';
                    break;
                case 'nameDESC':
                    $order_by = 'itemName DESC';
                    break;
                default:
                    $order_by = 'itemName ASC';
                    $sort = 'nameASC';
                    break;
            }
            
            // Make the query:
            $query = "SELECT itemName, itemType, price, quantity FROM grocery ORDER BY $order_by";
            $result = @mysqli_query($dbc, $query); // Run the query.
            
            // Count the number of returned rows, and format it with comma as the thousands separator:
            $itemCount = mysqli_num_rows($result);
            $formattedItemCount = number_format($itemCount);
            
            if ($itemCount > 0) // If the query ran OK, then display the records. 
            {
                // Indicate how many items are in the grocery list.
                echo ($itemCount == 1) ? "<p>There is only $itemCount item in your grocery list.</p>\n<br />" : "<p>There are currently $formattedItemCount items in your grocery list.</p>\n<br />";
                
                // Table header with sorting options:
                echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
                    <tr>
                        <td align="left"><a href="index.php?sort=nameASC">&#9650;</a><b>Item Name</b><a href="index.php?sort=nameDESC">&#9660;</a></td>
                        <td align="left"><a href="index.php?sort=typeASC">&#9650;</a><b>Item Type</b><a href="index.php?sort=typeDESC">&#9660;</a></td>
                        <td align="left"><a href="index.php?sort=priceASC">&#9650;</a><b>Price</b><a href="index.php?sort=priceDESC">&#9660;</a></td>
                        <td align="left"><a href="index.php?sort=quantityASC">&#9650;</a><b>Quantity</b><a href="index.php?sort=quantityDESC">&#9660;</a></td>
                    </tr>';
            
                // Initiate rowColor variable.
                $rowColor = '#ffffff';
            
                // Fetch and print all the records:
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    // Switch the background color for the row.
                    $rowColor = ($rowColor == '#eeeeee' ? '#ffffff' : '#eeeeee'); 
                
                    echo 
                        '<tr bgcolor="' . $rowColor . '">' .
                            '<td align="left">' . $row['itemName'] . '</td>' .
                            '<td align="left">' . $row['itemType'] . '</td>' .
                            '<td align="left">' . '$' . $row['price'] . '</td>' .
                            '<td align="left">' . $row['quantity'] . '</td>' .
                        '</tr>';
                } // END: while loop
            
                echo '</table>'; // Close the table.
            
                mysqli_free_result($result); // Free up the resources.
            }
            else // If no records were returned.
            { 
                echo '<p class="error">There are currently no items in your grocery list.</p>';
            }

            mysqli_close($dbc); // Close the connection to the database.
        } // END: function displaySortedGroceryItems()
    ?>
    
    <?php displaySortedGroceryItems(); ?>

    <?php include ('includes/footer.html'); ?>