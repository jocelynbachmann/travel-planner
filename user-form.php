<html>
<head>
    <title>Trip Planner: User Form</title>
</head>

<?php
/*references
https://forums.oracle.com/ords/apexds/post/how-to-fetching-column-names-from-oracle-using-php-2006
https://github.students.cs.ubc.ca/CPSC304/CPSC304_PHP_Project
https://docs.oracle.com/database/121/TDPPH/ch_three_db_access_class.htm#TDPPH149
*/
require('serverstart.inc.php');
/* ----------------------------User/GENERAL QUERIES-------------------------------------- */

function handleNewUserRequest() {
    global $db_conn;
    global $newMessage;

    if (!$_POST['name-create']  || !$_POST['email-create']) {
        $newMessage = "Please input all information!";
        return;
    }

    // Could hash email, but if a user changes email, then signs up again with that one a collision will occur
    $idhash = substr(hash('md5', (string) rand(0, 2147483647)), 0, 16);

    $tuple = array (
        ":bind1" => $idhash,
        ":bind2" => $_POST['name-create'],
        ":bind3" => $_POST['email-create']
    );

    $alltuples = array (
        $tuple
    );

    $checkemail = executePlainSQL("SELECT COUNT(*) as emailin FROM PERSON WHERE email='". $_POST['email-create'] ."'");
    if (($row = oci_fetch_row($checkemail)) && $row[0] > 0) {
        $newMessage = "A user with this email already exists.";
    } else {
        $res = executeBoundSQL("INSERT INTO PERSON(ID, uname, email) 
                                   VALUES (:bind1, :bind2, :bind3)", $alltuples);

        $newMessage = "New User ID: " . $idhash;
    }

    OCICommit($db_conn);
}


function handleUpdateUserRequest() {
    global $db_conn;
    global $updateMessage;

    if (!$_POST['id-update'] || !$_POST['email-update'] || !$_POST['name-update']) {
        $updateMessage = "Please input all information!";
        return;
    }

    $uniqueid = $_POST['id-update'];
    $newemail = $_POST['email-update'];
    $newname = $_POST['name-update'];


    $checkid = executePlainSQL("SELECT COUNT(*) FROM PERSON WHERE ID='" . $_POST['id-update'] . "'");
    $checkemail = executePlainSQL("SELECT COUNT(*) FROM PERSON WHERE email='" . $_POST['email-update'] . "' AND ID!='" . $_POST['id-update'] . "'" );

    if(($row = oci_fetch_row($checkid)) && $row[0] == 0) {
        $updateMessage = "No such user exists. Please double check your User ID.";
    } else if (($row = oci_fetch_row($checkemail)) && $row[0] > 0) {
        $updateMessage = "A user with this email already exists.";
    } else {
        $res = executePlainSQL("UPDATE Person SET 
                            uname='" . $newname . "',
                            email='" . $newemail . "'
                            WHERE ID='" . $uniqueid . "'");

        $updateMessage = "Updated User Information!";
    }

    OCICommit($db_conn);
}


function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('signUp', $_POST)) {
            handleNewUserRequest();
        } else if (array_key_exists('updateUser', $_POST)) {
            handleUpdateUserRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['sign-up']) ||
    isset($_POST['update-user'])) {
    handlePOSTRequest();
}

?>

<body>

    <h2>Sign Up</h2>
    <p>Enter a unique email, and a name to create a new user account. Your unique id will show up where indicated.</p>
    <form method="POST" action="user-form.php">
        <input type="hidden" id="signUp" name="signUp">
        <br>
        E-mail: <input type="text" name="email-create">
        <br><br>
        Name: <input type="text" name="name-create">
        <br><br>
        <p><input type="submit" value="Sign Up" name="sign-up"></p>
        <?php if ($newMessage) echo '<br><br>' . $newMessage . ''; ?>
    </form>

    <hr />

    <h2>Update User</h2>
    <p>Enter your unique user id and what you wish to change your name and email to.</p>
    <form method="POST" action="user-form.php">
        <input type="hidden" id="updateUser" name="updateUser">
        <br>
        ID: <input type="text" name="id-update">
        <br><br>
        E-mail: <input type="text" name="email-update">
        <br><br>
        Name: <input type="text" name="name-update">
        <br><br>
        <p><input type="submit" value="Update User" name="update-user"></p>
        <?php if ($updateMessage) echo '<br><br> '.$updateMessage.''; ?>
    </form>

    <hr />

    <h2>Return Home</h2>
    <p>All done? To return to the home page, press the Return Home button</p>

    <a href="./home.php">Return Home</a>
</body>
</html>