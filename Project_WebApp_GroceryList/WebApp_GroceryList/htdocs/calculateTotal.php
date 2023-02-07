<!-- 
    Daniel Zayas 
    CS 415 - Design of Database Systems
    Assignment #10 - Web Application - Due: 4/12/2019, before 4:00 pm
-->
    <?php
        error_reporting(0);
    ?>
	
    <?php
        // This functions calculates the total price of all the items in the grocery list, plus sales tax.
        // The calculated total includes the price of each item, the quantitiy of each item, and the sales tax.
        function calculateTotal($formatedUserEntry) 
        {
            require ('../mysqli_connect.php'); // Connect to the db.
            
            // Initiate answers array.
            $answers = array();
            
            // Initiate total plus tax.
            $totalPlusTax = 0;
            
            // Initiate subtotal.
            $subTotal = 0;
            
            // Initiate total count of items.
            $totalNumberOfItems = 0;
            
            // The user's submitted sales tax.
            if($formatedUserEntry >= 1)
            {
                $taxRate = $formatedUserEntry / 100;  // Problem: how to determine when is the right time to divide by 100?
            }
            else
            {
                $taxRate = $formatedUserEntry;
            }
            
            // Initiate the added cost for sales tax.
            $taxCost = 0;
            
            // Make the query:
            $query = "SELECT itemName, itemType, price, quantity FROM grocery";
            $result = @mysqli_query($dbc, $query); // Run the query.
            
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) // Read the records in the grocery table.
            {
                $quantity = $row['quantity']; // Current record's quantity.
                $price = $row['price']; // Current record's price.
                
                $totalNumberOfItems = $totalNumberOfItems + $quantity; // Tally the quantity of each grocery item.
                
                $subTotal = $subTotal + ($quantity * $price); // Calculate the total of all grocery items without tax.
                
                $totalPlusTax = $subTotal + ($subTotal * $taxRate); // Calculate the total of all grocery items plus tax.
                
                $taxCost = $totalPlusTax - $subTotal; // Calculate the amount that sales tax had included into the subtotal.
            }
                    
            // Round the total of all grocery items plus tax, and format it with comma as the thousands separator:
            $totalPlusTax = number_format($totalPlusTax, 2);
            
            // Round the amount that sales tax had included into the subtotal, and format it with comma as the thousands separator:
            $taxCost = number_format($taxCost, 2);
            
            // Round the total of all grocery items without tax, and format it with comma as the thousands separator:
            $subTotal = number_format($subTotal, 2);
            
            // Format the sum quantity of items with comma as the thousands separator:
            $totalNumberOfItems = number_format($totalNumberOfItems);
            
            $answers[0] = $totalNumberOfItems;
            $answers[1] = $subTotal;
			$answers[2] = $formatedUserEntry;
            $answers[3] = $taxCost;
            $answers[4] = $totalPlusTax;
            
            return $answers;
        } // END: function calculateTotal($formatedUserEntry)
    ?>

    <?php 
        #  Script - calculateTotal.php
        $pageTitle = 'Calculate the Total';
        include ('includes/header.html'); 
    ?>

    <h1>Calculate the Total Price of Your Grocery Items</h1>

    <?php 
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
                    <td align="left"><a href="calculateTotal.php?sort=nameASC">&#9650;</a><b>Item Name</b><a href="calculateTotal.php?sort=nameDESC">&#9660;</a></td>
                    <td align="left"><a href="calculateTotal.php?sort=typeASC">&#9650;</a><b>Item Type</b><a href="calculateTotal.php?sort=typeDESC">&#9660;</a></td>
                    <td align="left"><a href="calculateTotal.php?sort=priceASC">&#9650;</a><b>Price</b><a href="calculateTotal.php?sort=priceDESC">&#9660;</a></td>
                    <td align="left"><a href="calculateTotal.php?sort=quantityASC">&#9650;</a><b>Quantity</b><a href="calculateTotal.php?sort=quantityDESC">&#9660;</a></td>
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
    ?>

    <?php
        // Check for form submission, if there were at least one record counted:
        if ($_SERVER['REQUEST_METHOD'] == 'POST') 
        {
            if ($itemCount > 0)
            {
                // Initiate salesTaxError boolean.
                $salesTaxError = false;
            
                // Validation of form input: sales tax - step #1:
                // If an entry was written for sales tax, and the entry was a decimal number...
                //if ((isset($_POST['salesTax_txtBox'])) && (var_dump(is_float($_POST['salesTax_txtBox']))))
                if ((isset($_POST['salesTax_txtBox'])) && (is_numeric($_POST['salesTax_txtBox'])))
                {
                    $userEntry = $_POST['salesTax_txtBox'];
                
                    // Validation of form input: sales tax - step #2:
                    // If the user's entry for sales tax is between 0.00 and 100.00, inclusive... 
                    if (($userEntry >= 0.00) && ($userEntry <= 100.00))
                    {
                        $formatedUserEntry = number_format($userEntry, 2);
                        $results = array();
                        $results = calculateTotal($formatedUserEntry);
                    
                        echo '<p>Total Quantity of All Items: ' . $results[0] . '</p>';
                        echo '<p>Sub Total: $' . $results[1] . '</p>';
						echo '<p>Entered Sales Tax Rate: '. $results[2] . '%</p>';
                        echo '<p>Cost of Tax: $' . $results[3] . '</p>';
                        echo '<p>Total: $' . $results[4] . '</p>';
                    }
                    else
                    {
                        $salesTaxError = true;
                    }
                }
                else
                {
                    $salesTaxError = true;
                }
            
                if ($salesTaxError)
                {
                    // Tell user that their entry for the sales tax was invalid.
                    echo '<p class="error">Your entry for sales tax is invalid. <br />
                        Please enter an integer or decimal between 0 and 100.</p>';
                }
            } // END: if ($itemCount > 0)
        } // END: if ($_SERVER['REQUEST_METHOD'] == 'POST')
    ?>
    
	<form action="" method="post">
        <p>Insert sales tax: <input type="number" name="salesTax_txtBox" size="6" maxlength="6" min="0" max="100" step=".01" value="<?php if (isset($_POST['salesTax_txtBox'])) echo $_POST['salesTax_txtBox']; ?>" required /> %</p>
       
        <br />
        
        <input type="submit" name="calculate" value="Calculate Total" />
    </form>
	
    <?php include ('includes/footer.html'); ?>