<html>
<head>
    <title>Trip Planner: Group Form</title>
</head>



<?php
/*references
https://forums.oracle.com/ords/apexds/post/how-to-fetching-column-names-from-oracle-using-php-2006
https://github.students.cs.ubc.ca/CPSC304/CPSC304_PHP_Project
https://docs.oracle.com/database/121/TDPPH/ch_three_db_access_class.htm#TDPPH149
*/
require('serverstart.inc.php');
/* ----------------------------Itinerary/GENERAL QUERIES-------------------------------------- */


function handleNewGroupRequest() {
    global $db_conn;
    global $newGroupMessage;

    if (!$_POST['email-create'] || !$_POST['name-create']) {
        $newGroupMessage = "Please input all information!";
        return;
    }

    $idhash = substr(hash('md5', (string) rand(0, 2147483647)), 0, 16);
    $emails = explode(',', $_POST['email-create']);

    $newgrouptuple = array(
        ":bind1" => $idhash,
        ":bind2" => $_POST['name-create']
    );

    $grouptuples = array($newgrouptuple);


    $res1 = executeBoundSQL("INSERT INTO TOURISTGROUP(ID, title) 
                                VALUES (:bind1, :bind2)", $grouptuples);

    for ($i=0; $i < count($emails); $i++) {

        $checkemail = executePlainSQL("SELECT COUNT(*) FROM PERSON WHERE email='". trim($emails[$i]) ."'");
        $resid = executePlainSQL("SELECT ID FROM PERSON WHERE email='" . trim($emails[$i]) . "'");

        if (($row = oci_fetch_row($checkemail)) && $row[0] > 0) {
            $res = executePlainSQL("INSERT INTO MEMBER(userID, groupID) 
                                VALUES ('" . OCI_Fetch_Array($resid, OCI_BOTH)[0] . "', '" . $idhash . "')");
        }
    }

    $newGroupMessage = "New Group ID: " . $idhash;

    OCICommit($db_conn);
}


function handleJoinGroupRequest() {
    global $db_conn;
    global $joinGroupMessage;

    if (!$_POST['id-join'] || !$_POST['email-join']) {
        $joinGroupMessage = "Please input all information!";
        return;
    }

    $uniqueid = $_POST['id-join'];

    $checkemail = executePlainSQL("SELECT COUNT(*) FROM PERSON WHERE email='". $_POST['email-join'] ."'");
    $checkgroup = executePlainSQL("SELECT COUNT(*) FROM TOURISTGROUP WHERE ID='". $uniqueid ."'");


    if (($row = oci_fetch_row($checkemail)) && $row[0] == 0) {
        $joinGroupMessage = "No such user exists.";
    } else if (($row = oci_fetch_row($checkgroup)) && $row[0] == 0) {
        $joinGroupMessage = "No such group exists.";
    } else {

        $resid = executePlainSQL("SELECT ID FROM PERSON WHERE email='" . $_POST['email-join'] . "'");
        $fetchedid = OCI_Fetch_Array($resid, OCI_BOTH)[0];
        $checkmember = executePlainSQL("SELECT COUNT(*) FROM MEMBER WHERE 
                                           userID='" . $fetchedid . "' 
                                           AND groupID='" . $uniqueid . "'");

        if (($row = oci_fetch_row($checkmember)) && $row[0] > 0) {
            $joinGroupMessage = "You are already a member of this group.";
        } else {
            $res = executePlainSQL("INSERT INTO MEMBER(userID, groupID) 
                                   VALUES ('" . $fetchedid . "', '" . $uniqueid . "')");

            $joinGroupMessage =  "Successfully Joined Group!";
        }
    }

    OCICommit($db_conn);
}


function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('createGroup', $_POST)) {
            handleNewGroupRequest();
        } else if (array_key_exists('joinGroup', $_POST)) {
            handleJoinGroupRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['create-group']) ||
    isset($_POST['join-group'])) {
    handlePOSTRequest();
}

?>

<body>

    <h2>Create Group</h2>
    <p>Enter a group name and a list of comma separated emails to add initially (You should include your own). Your unique group id will show up where indicated.</p>
    <form method="POST" action="group-form.php">
        <input type="hidden" id="createGroup" name="createGroup">
        <br>
        Name: <input type="text" name="name-create">
        <br><br>
        E-mails: <textarea name="email-create" rows="4" cols="50"></textarea>
        <br><br>
        <p><input type="submit" value="Create Group" name="create-group"></p>
        <?php if ($newGroupMessage) echo '<br><br> '.$newGroupMessage.''; ?>
    </form>

    <hr />

    <h2>Join Group</h2>
    <p>Enter your email and the id of the group you wish to join.</p>
    <form method="POST" action="group-form.php">
        <input type="hidden" id="joinGroup" name="joinGroup">
        <br>
        E-mail: <input type="text" name="email-join">
        <br><br>
        Group ID: <input type="text" name="id-join">
        <br><br>
        <p><input type="submit" value="Join Group" name="join-group"></p>
        <?php if ($joinGroupMessage) echo '<br><br> '.$joinGroupMessage.''; ?>
    </form>

    <hr />

    <h2>Return Home</h2>
    <p>All done? To return to the home page, press the Return Home button</p>

    <a href="./home.php">Return Home</a>

</body>
</html>