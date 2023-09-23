<html>
<head>
    <title>Trip Planner: Guide Form</title>
</head>

<?php
/*references
https://forums.oracle.com/ords/apexds/post/how-to-fetching-column-names-from-oracle-using-php-2006
https://github.students.cs.ubc.ca/CPSC304/CPSC304_PHP_Project
https://docs.oracle.com/database/121/TDPPH/ch_three_db_access_class.htm#TDPPH149
*/
require('serverstart.inc.php');
/* ----------------------------Itinerary/GENERAL QUERIES-------------------------------------- */
function printResult($table, $result) {
    echo "<br>Retrieved data from table ". $table. ":<br>";

    echo "<table>";
    $titles = "<tr>";
    $ncols = oci_num_fields($result);
    for ($i = 1; $i <= $ncols; $i++) {
        $titles = $titles . "<th>" .  oci_field_name($result, $i) . "</th>";
    }
    echo $titles . "</tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        $final = "<tr>";
        for ($i = 0; $i < $ncols; $i++) {
            $final = $final . "<td>" . $row[$i] . "</td>";
        }
        echo $final . "<tr>";
    }
    echo "</table>";
}


function handleNewGuideRequest() {
    global $db_conn;
    global $idhash;
    global $newGuideMessage;

    if (!$_POST['userID-create'] || !$_POST['title-create'] || !$_POST['title-create']) {
        $newGuideMessage = "Please input all information!";
        return;
    }

    $idhash = substr(hash('md5', (string) rand(0, 2147483647)), 0, 16);

    $checkid = executePlainSQL("SELECT COUNT(*) FROM PERSON WHERE ID='". $_POST['userID-create'] ."'");

    if (($row = oci_fetch_row($checkid)) && $row[0] == 0) {
        $newGuideMessage = 'No such user exists.';
    } else {
        $res = executePlainSQL("INSERT INTO GUIDE(ID, title, authorID) 
                                   VALUES ('" . $idhash . "',
                                           '" . $_POST['title-create'] . "',
                                           '" . $_POST['userID-create'] . "')");

        $newGuideMessage = "New Guide ID: " . $idhash;
    }

    OCICommit($db_conn);
}

function handleAddToGuideRequest() {
    global $db_conn;
    global $addToGuideMessage;

    if (!$_POST['userID-add'] || !$_POST['location-add'] || !$_POST['guideID-add']) {
        $addToGuideMessage = "Please input all information!";
        return;
    }

    $resid = executePlainSQL("SELECT authorID FROM GUIDE WHERE ID='" . $_POST['guideID-add'] . "'");

    $checkguide = executePlainSQL("SELECT COUNT(*) FROM GUIDE WHERE ID='". $_POST['guideID-add'] ."'");
    $checklocation = executePlainSQL("SELECT COUNT(*) FROM LOCATION WHERE address='". $_POST['location-add'] ."'");
    $checkcontainslocation = executePlainSQL("SELECT COUNT(*) FROM ABOUT 
                                          WHERE guideID='". $_POST['guideID-add'] ."'
                                          AND address = '". $_POST['location-add'] . "'");

    $resid = executePlainSQL("SELECT authorID FROM GUIDE WHERE ID='" . $_POST['guideID-add'] . "'");

    if (($row = oci_fetch_row($checkguide)) && $row[0] == 0) {
        $addToGuideMessage = 'There is no such guide.';
    } else if (($row = oci_fetch_row($checklocation)) && $row[0] == 0) {
        $addToGuideMessage = 'There is no such location.';
    } else if (($row = oci_fetch_row($checkcontainslocation)) && $row[0] > 0) {
        $addToGuideMessage = 'This guide already contains a description of this location.';
    } else if ($_POST['userID-add'] === OCI_Fetch_Array($resid, OCI_BOTH)[0]) {
        $res = executePlainSQL("INSERT INTO ABOUT(guideID, address, description) 
                                   VALUES ('" . $_POST['guideID-add'] . "',
                                           '" . $_POST['location-add'] . "',
                                           '" . $_POST['description-add'] . "')");

        $addToGuideMessage = 'Successfully added to guide!';
    } else {
        $addToGuideMessage = 'Sorry, you do not have access to this guide.';
    }

    OCICommit($db_conn);
}

function handleDeleteFromGuideRequest() {
    global $db_conn;
    global $deleteFromGuideMessage;

    if (!$_POST['userID-delete'] || !$_POST['location-delete'] || !$_POST['guideID-delete']) {
        $deleteFromGuideMessage = "Please input all information!";
        return;
    }

    $checkguide = executePlainSQL("SELECT COUNT(*) FROM GUIDE WHERE ID='". $_POST['guideID-delete'] ."'");
    $checklocation = executePlainSQL("SELECT COUNT(*) FROM ABOUT 
                                          WHERE guideID='". $_POST['guideID-delete'] ."'
                                          AND address = '". $_POST['location-delete'] . "'");

    $resid = executePlainSQL("SELECT authorID FROM GUIDE WHERE ID='" . $_POST['guideID-delete'] . "'");

    if (($row = oci_fetch_row($checkguide)) && $row[0] == 0) {
        $deleteFromGuideMessage = 'There is no such guide.';
    } else if (($row = oci_fetch_row($checklocation)) && $row[0] == 0) {
        $deleteFromGuideMessage = 'This guide does not contain the specified location.';
    } else if ($_POST['userID-delete'] === OCI_Fetch_Array($resid, OCI_BOTH)[0]) {
        $res = executePlainSQL("DELETE FROM ABOUT 
                                       WHERE guideID='" . $_POST['guideID-delete'] . "'
                                       AND address='" . $_POST['location-delete'] . "'");

        $deleteFromGuideMessage = 'Successfully deleted from guide!';
    } else {
        $deleteFromGuideMessage = 'Sorry, you do not have access to this guide!';
    }

    OCICommit($db_conn);
}

function handleGetLocationsRequest() {
    global $db_conn;
    global $getLocationRequest;
    $getLocationRequest = executePlainSQL("SELECT L.Address AS Address, L.locationName AS Name FROM Location L");
}

function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('createGuide', $_POST)) {
            handleNewGuideRequest();
        } else if (array_key_exists('addToGuide', $_POST)) {
            handleAddToGuideRequest();
        } else if (array_key_exists('deleteFromGuide', $_POST)) {
            handleDeleteFromGuideRequest();
        }
        disconnectFromDB();
    }
}

function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('getLocations', $_GET)) {
            handleGetLocationsRequest();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['create-guide']) ||
    isset($_POST['add-to-guide']) ||
    isset($_POST['delete-from-guide'])) {
    handlePOSTRequest();
}
else if (isset($_GET['get-locations'])) {
    handleGETRequest();
}
?>

<body>

    <h2> Show Locations </h2>
    <p> To see all locations, press the Show Locations Button </p>

    <form method="GET" action="guide-form.php">
        <input type="hidden" id="getLocations" name="getLocations">
        <p><input type="submit" value="Show Locations" name="get-locations"></p>
        <?php if ($getLocationRequest) echo printResult("Locations", $getLocationRequest);; ?>
    </form>

    <hr />

    <h2> Create a Guide! </h2>
    <p> To create a guide:
        Enter your user ID and the title of the guide you want to create.
    </p>

    <form method="POST" action="guide-form.php">
        <input type="hidden" id="createGuide" name="createGuide">
        Author ID: <input type="text" name="userID-create"> <br /><br />
        Title: <input type="text" name="title-create"> <br /><br />
        <input type="submit" value="Create Guide" name="create-guide"></p>
        <?php if ($newGuideMessage) echo '<br><br> '.$newGuideMessage.''; ?>
    </form>

    <hr />

    <h2> Add to your Guide! </h2>
    <p> To add to one of your guides:
        Enter your user ID, guide ID, chosen location, and a description/review.
    </p>

    <form method="POST" action="guide-form.php">
        <input type="hidden" id="addToGuide" name="addToGuide">
        Author ID: <input type="text" name="userID-add"> <br /><br />
        Guide ID: <input type="text" name="guideID-add"> <br /><br />
        Location Address: <input type="text" name="location-add"> <br /><br />
        Description: <textarea name="description-add" rows="4" cols="50"></textarea><br><br>
        <input type="submit" value="Add to Guide" name="add-to-guide"></p>
        <?php if ($addToGuideMessage) echo '<br><br> '.$addToGuideMessage.''; ?>
    </form>

    <hr />

    <h2> Delete an Item from your Guide</h2>
    <p> To delete:
        enter your user ID, the guide id, and the location. We'll take care of the rest!
    </p>

    <form method="POST" action="guide-form.php">
        <input type="hidden" id="deleteFromGuide" name="deleteFromGuide">
        Author ID: <input type="text" name="userID-delete"> <br /><br />
        Guide ID: <input type="text" name="guideID-delete"> <br /><br />
        Location Address: <input type="text" name="location-delete"> <br /><br />
        <input type="submit" value="Delete from Guide" name="delete-from-guide"></p>
        <?php if ($deleteFromGuideMessage) echo '<br><br> '.$deleteFromGuideMessage.''; ?>
    </form>

    <hr />

    <h2>Return Home</h2>
    <p>All done? To return to the home page, press the Return Home button</p>

    <a href="./home.php">Return Home</a>

</body>
</html>