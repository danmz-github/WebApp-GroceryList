<!-- 
    Daniel Zayas 
    CS 415 - Design of Database Systems
    Assignment #10 - Web Application - Due: 4/12/2019, before 4:00 pm
-->
	
    <script type="text/javascript" src="js/jquery-3.3.1.min.js" charset="utf-8"></script>
    <script type="text/javascript">
        function editConfirm()
        {
            var result = confirm("Are you sure you want to save these edits to your grocery list?");
            
            if(result)
            {
                return true;
            }
            else
            {
                return false;    
            }
        }
    </script>

    <?php 
        #  Script - editItem.php
        $pageTitle = 'Edit an Item';
        include ('includes/header.html'); 
    
        echo "<h1>Edit an Item in Your Grocery List</h1>";
    ?>

    <?php
        error_reporting(0);
    ?>

    <?php
        if (isset($_POST['bulkEditSubmit']))
        {
            // Arrays of user's entries:
            $user_itemNames = $_POST['name'];
            $user_itemTypes = $_POST['type'];
            $user_prices = $_POST['price'];
            $user_quantities = $_POST['quantity'];
            $record_ids = $_POST['id'];
            
            // Make array of valid item types:
            $valid_itemTypes = array("Alcohol", "Baby Care", "Bakery", "Beverages", "Breakfast", "Canned Goods", "Condiments", "Dairy", "Deli", "Frozen Foods", "Fruits & Vegetables", "Grains, Pasta, & Sides", "Home Cleaning", "International Cuisine", "Meat & Seafood", "Pet Care", "Personal Care & Health", "Snacks", "Tobacco");
                
            // Boolean error detection triggers:
            $error_itemName = false;
            $error_itemType = false;
            $error_price = false;
            $error_quantity = false;
            
            // Validate and sanitize the user's entries: 
            // Validate the user's item name entries.
            for ($index = 0; $index < count($user_itemNames) && $error_itemName == false; $index++)
            {
                // If the user's entry is null, then it is invalid.
                if (is_null($user_itemNames[$index]))
                {
                    $error_itemName = true; 
                }
                else // Check if the user's entry is a empty string.
                {
                    // Place a copy of the user's entry in a temporary variable.
                    // and trim out the white spaces.
                    $temp = $user_itemNames[$index];
                    $temp = trim($temp);
                    
                    // If trim caused the temp variable to turn into a empty string,
                    // then the user's entry is not valid.
                    if (empty($temp))
                    {
                        $error_itemName = true;
                    }
                    else // No error found.
                    {
                        // Sanitize the user's entry.
                        $user_itemNames[$index] = trim($user_itemNames[$index]); // Remove whitespace.
                        $user_itemNames[$index] = stripslashes($user_itemNames[$index]); // Remove slashes.
                        $user_itemNames[$index] = strip_tags($user_itemNames[$index]); // Remove HTML, XML, and PHP tags. 
                    }
                }
            }
            // END: Validate the user's item name entries.
        
            // Validate the user's item type entries.
            $foundValidItemType = false;
            for ($x = 0; $x < count($user_itemTypes) && $foundValidItemType == false; $x++)
            {
                for ($y = 0; $y < count($valid_itemTypes) && $foundValidItemType == false; $y++)
                { 
                    // Strict comparison of the two variables:
                    // They must both be a string, and the user's entry must be
                    // one of the valid item types.
                    if ($user_itemTypes[$x] === $valid_itemTypes[$y]) 
                    {
                        $foundValidItemType = true;
                    }
                }
            }
            
            // If a match was not found, then the user's entry for item type is invalid.
            if ($foundValidItemType == false)
            {
                $error_itemType = true;
            }
            // END: Validate the user's item type entries.
            
            // Validate the user's price entries.
            for ($index = 0; $index < count($user_prices) && $error_price == false; $index++)
            {
                // If the user's entry is numeric, then... 
                if (is_numeric($user_prices[$index]))
                { 
                    // Copy user's entry into a sanitize variable. 
                    //$testPrice = filter_var($user_prices[$index], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $testPrice = (float) $user_prices[$index];
                        
                    // If the sanitize variable is not in valid range, then user's entry is not valid.
                    if (($testPrice < 0) xor ($testPrice > 999999999999.99))
                    {
                        $error_price = true;
                    }
                    else // The user's entry is sanitized, and it will be entered into the database.
                    {
                        $user_prices[$index] = $testPrice;
                    }
                }
                else // User's entry is not numeric. It is invalid.
                {
                    $error_price = true;
                }   
            }
            // END: Validate the user's price entries.
            
            // Validate the user's quantity entries.
            for ($index = 0; $index < count($user_quantities) && $error_quantity == false; $index++)
            {
                // If the entry is an integer, then check if it is less than one.
                if (is_numeric($user_quantities[$index])) 
                {
                    // Copy user's entry into a sanitize variable. 
                    //$testQuantity = filter_var($user_prices[$index], FILTER_SANITIZE_NUMBER_INT);
                    $testQuantity = (int) $user_quantities[$index];
                     
                    // If the sanitize variable is not in valid range, then user's entry is not valid.
                    if ($testQuantity < 1)
                    {
                        $error_quantity = true;
                    }
                    else // The user's entry is sanitized, and it will be entered into the database.
                    {
                        $user_quantities[$index] = $testQuantity;
                    }
                }
                else // The entry is not an integer. It is invalid.
                {
                    $error_quantity = true; 
                }
            }
            // END: Validate the user's quantity entries.
            
            // If validation found no errors... 
            if (($error_itemName == false) && ($error_itemType == false) && ($error_price == false) && ($error_quantity == false))
            {
                require ('../mysqli_connect.php'); // Connect to the db.
                
                // Make the query:
                $query = 'UPDATE grocery SET itemName = ?, itemType = ?, price = ?, quantity = ? WHERE id = ?';
            
                // Prepare the statement:
                $stmt = mysqli_prepare($dbc, $query);
                
                // Bind the variables:
                mysqli_stmt_bind_param($stmt, 'ssdii', $itemName, $itemType, $price, $quantity, $id);
                
                for ($index = 0; $index < count($user_itemNames); $index++)
                {
                    // Assign the values to the variables:
                    $itemName = $user_itemNames[$index];
                    $itemType = $user_itemTypes[$index];
                    $price = $user_prices[$index];
                    $quantity = $user_quantities[$index];
                    $id = $record_ids[$index];
                               
                    // Execute the query:
                    mysqli_stmt_execute($stmt);
                }
                
                echo '<p>Editing complete. Please see your edits in the Grocery List page.</p>';
                
                // Close the statement:
                mysqli_stmt_close($stmt);
                
                // Close the connection:
                mysqli_close($dbc);
            }
            else
            {      
                if ($error_itemName == true)
                {
                    if (count($user_itemNames) == 1)
                    {
                        echo '<p class="error">The name that you have entered for your new item is invalid.</p>';
                    }
                    else if (count($user_itemNames) > 1)
                    {
                        echo '<p class="error">More than one of your new items has an invalid entry for its name.</p>';
                    }
                    
                    echo '<p class="error">Please try again.</p>';
                }
                
                if ($error_itemType == true)
                {
                    if (count($user_itemNames) == 1)
                    {
                        echo '<p class="error">Your selection of item type for your new item is invalid.</p>';
                    }
                    else if (count($user_itemNames) > 1)
                    {
                        echo '<p class="error">More than one of your new items has an invalid selection of item type.</p>';
                    }
                    
                    echo '<p class="error">You may only enter an item type that is provided to you in the drop down list.</p>';
                }
                
                if ($error_price == true)
                {
                    if (count($user_itemNames) == 1)
                    {
                        echo '<p class="error">The price that you have entered for your new item is invalid.</p>';
                    }
                    else if (count($user_itemNames) > 1)
                    {
                        echo '<p class="error">More than one of your new items has an invalid entry for its price.</p>';
                    }
                        
                    echo '<p class="error">Please enter an integer or decimal value for price that greater than or equal to zero.</p>';
                }
                
                if ($error_quantity == true)
                {
                    if (count($user_itemNames) == 1)
                    {
                        echo '<p class="error">The quantity that you have entered for your new item is invalid.</p>';
                    }
                    else if (count($user_itemNames) > 1)
                    {
                        echo '<p class="error">More than one of your new items has an invalid entry for its quantity.</p>';
                    }
                    
                    echo '<p class="error">Please enter an integer value for quantity that greater than or equal to one.</p>';
                }
            } // END: else    
        } // END: if (isset($_POST['bulkEditSubmit']))
    ?>

    <form name="editGroceryListForm" action="" method="post" onSubmit="return editConfirm();">
        <?php
            require ('../mysqli_connect.php'); // Connect to the db.

            // Make the query:
            $query = "SELECT id, itemName, itemType, price, quantity FROM grocery";
            $result = @mysqli_query($dbc, $query); // Run the query.

            // Count the number of returned rows, memorize that number, and then format it with comma as the thousands separator:
            $itemCount = mysqli_num_rows($result);
            $formattedItemCount = number_format($itemCount);
            
            if ($itemCount > 0) // If the query ran OK, then display the records. 
            {
                // Indicate how many items are in the grocery list.
                echo ($itemCount == 1) ? "<p>There is only $itemCount item in your grocery list.</p>\n<br />" : "<p>There are currently $formattedItemCount items in your grocery list.</p>\n<br />";

                // Table header:
                echo '<table align="center" cellspacing="3" cellpadding="3" width="75%">
                    <tr>
                        <td align="left"><b>Item Name</b></td>
                        <td align="left"><b>Item Type</b></td>
                        <td align="left"><b>Price</b></td>
                        <td align="left"><b>Quantity</b></td>
                    </tr>';
             
                // Fetch and print all the records:
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    echo '<input type="hidden" name="id[]" value="' . $row['id'] . '">';
                    
                    echo 
                    '<tr>' . 
                        '<td align="left"><input type="text" name="name[]" maxlength="80" value="' . $row['itemName'] . '" required/></td>' .
                        '<td>' .
                            '<select name="type[]" required>' .
                                '<option value="' . $row['itemType'] . '">' . $row['itemType'] . '</option>' .
                                '<option value="Alcohol">Alcohol</option>' .
                                '<option value="Baby Care">Baby Care</option>' .
                                '<option value="Bakery">Bakery</option>' .
                                '<option value="Beverages">Beverages</option>' .
                                '<option value="Breakfast">Breakfast</option>' .
                                '<option value="Canned Goods">Canned Goods</option>' .
                                '<option value="Condiments">Condiments</option>' .
                                '<option value="Dairy">Dairy</option>' .
                                '<option value="Deli">Deli</option>' .
                                '<option value="Frozen Foods">Frozen Foods</option>' .
                                '<option value="Fruits & Vegetables">Fruits &amp; Vegetables</option>' .
                                '<option value="Grains, Pasta, & Sides">Grains, Pasta, &amp; Sides</option>' .
                                '<option value="Home Cleaning">Home Cleaning</option>' .
                                '<option value="International Cuisine">International Cuisine</option>' .
                                '<option value="Meat & Seafood">Meat &amp; Seafood</option>' .
                                '<option value="Pet Care">Pet Care</option>' .
                                '<option value="Personal Care & Health">Personal Care &amp; Health</option>' .
                                '<option value="Snacks">Snacks</option>' .
                                '<option value="Tobacco">Tobacco</option>' .
                            '</select>' .
                        '</td>'.
                        '<td align="left"><input type="number" name="price[]" maxlength="6" min="0" max="999999999999.99" step=".01" style="width:11em" value="' . $row['price'] . '" required /></td>' . 
                        '<td align="left"><input type="number" name="quantity[]" min="1" step="1" style="width:7em" value="' . $row['quantity'] . '" required /></td>' . 
                    '</tr>';    
            
                } // END: while loop
            
                echo '</table>'; // Close the table.
            
                mysqli_free_result($result); // Free up the resources.
                
                echo '<input type="submit" name="bulkEditSubmit" value="Save Edits">';
            }
            else // If no records were returned.
            { 
                echo '<p class="error">There are currently no items in your grocery list.</p>';
            }

            mysqli_close($dbc); // Close the connection to the database.
        ?>
    </form>
    
    <?php include ('includes/footer.html'); ?>