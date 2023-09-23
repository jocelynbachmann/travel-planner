<html>
  <head>
    <title>Trip Planner: Itinerary Form</title>
  </head>

  <body>

    <h2> Show Itinerary </h2>
    <p>To see an itinerary, input the itinerary id and press the Show Itinerary Button</p>

    <form method="GET" action="itinerary-form.php">
      <input type="hidden" id="getItinerary" name="getItinerary">
      Itinerary ID: <input type="text" name="inputItinerary"> <br /><br />
      <p><input type="submit" value="Show Itinerary" name="get-itinerary"></p>
    </form>

    <hr />

    <h2> Show Locations </h2>
    <p> To see all locations, press the Show Locations Button </p>

    <form method="GET" action="itinerary-form.php">
      <input type="hidden" id="getLocations" name="getLocations">
      <p><input type="submit" value="Show Locations" name="get-locations"></p>
    </form>

    <hr />

    <h2> Show Transport </h2>
    <p> To see all transport options, press the Show Transport Button </p>

    <form method="GET" action="itinerary-form.php">
      <input type="hidden" id="getTransports" name="getTransports">
      <p><input type="submit" value="Show Transport" name="get-transports"></p>
    </form>

    <hr />

    <h2> Add to your Group's Itinerary! </h2>
    <p> To add to one of your group's itinerary:
        enter your group ID, the itinerary id, locations of departure and arrival, departure time, and transport id. 
        We'll add it to your existing itinerary (or a new one) if all the items are valid.
        Make sure both date and time is in your departure time (ex. 07-JUN-23 01:45:00.00 PM - copy, paste, and edit this time).
        Feel free to look at some locations and transport options on this page for travel inspo!
    </p>

    <form method="POST" action="itinerary-form.php">
            <input type="hidden" id="insertGroupItineraryRequest" name="insertGroupItineraryRequest">
            Group ID: <input type="text" name="insGroupInsert"> <br /><br />
            Itinerary ID: <input type="text" name="insGroupItineraryInsert"> <br /><br />
            departure time: <input type="text" name="insGroupDepartureInsert"> <br /><br />
            Leaving From (Location): <input type="text" name="insGroupFrom"> <br /><br />
            Going To (Location): <input type="text" name="insGroupTo"> <br /><br />
            Transport ID: <input type="text" name="insGroupTransport"> <br /><br />

            <input type="submit" value="Add to Group Itinerary" name="insertGroupSubmit"></p>
    </form>


    <h2> Delete an Item from your Group Itinerary </h2>
    <p> To delete:
        enter your group ID, the itinerary id, and the item's departure time.
        Make sure both date and time is in your departure time (ex. 07-JUN-23 01:45:00.00 PM - copy, paste, and edit this time). 
        We'll take care of the rest!
    </p>

    <form method="POST" action="itinerary-form.php">
            <input type="hidden" id="deleteGroupItineraryRequest" name="deleteGroupItineraryRequest">
            Group ID: <input type="text" name="insGroupDelete"> <br /><br />
            Itinerary ID: <input type="text" name="insItineraryGroupDelete"> <br /><br />
            departure time: <input type="text" name="insDepartureGroupDelete"> <br /><br />

            <input type="submit" value="Delete from Group Itinerary" name="deleteGroupSubmit"></p>
    </form>

    <hr />

    <h2> Add to your Personal Itinerary! </h2>
    <p> To add to a personal itinerary:
        enter your user ID, the itinerary id, locations of departure and arrival, departure time, and transport id.  
        We'll add it to your existing itinerary (or a new one) if all the items are valid. 
        Make sure both date and time is in your departure time (ex. 07-JUN-23 01:45:00.00 PM - copy, paste, and edit this time). 
        Feel free to look at some locations and transport options on this page!
    </p>

    <form method="POST" action="itinerary-form.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertUserItineraryRequest" name="insertUserItineraryRequest">
            User ID: <input type="text" name="insUserInsert"> <br /><br />
            Itinerary ID: <input type="text" name="insUserItineraryInsert"> <br /><br />
            departure time: <input type="text" name="insUserDepartureInsert"> <br /><br />
            Leaving From (Location): <input type="text" name="insUserFrom"> <br /><br />
            Going To (Location): <input type="text" name="insUserTo"> <br /><br />
            Transport ID: <input type="text" name="insUserTransport"> <br /><br />

            <input type="submit" value="Add to Personal Itinerary" name="insertUserSubmit"></p>
    </form>

    <h2> Delete an Item from your Personal Itinerary </h2>
    <p> To delete:
        enter your user ID, the itinerary id, and the item's departure time. 
        Make sure both date and time is in your departure time (ex. 07-JUN-23 01:45:00.00 PM - copy, paste, and edit this time). 
        We'll take care of the rest!
    </p>

    <form method="POST" action="itinerary-form.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteUserItineraryRequest" name="deleteUserItineraryRequest">
            User ID: <input type="text" name="insUserDelete"> <br /><br />
            Itinerary ID: <input type="text" name="insItineraryUserDelete"> <br /><br />
            departure time: <input type="text" name="insDepartureUserDelete"> <br /><br />

            <input type="submit" value="Delete from Personal Itinerary" name="deleteUserSubmit"></p>
    </form>

    <hr />

    <h2>Return Home</h2>
    <p>All done? To return to the home page, press the Return Home button</p>

    <a href="./home.php">Return Home</a>
    <?php
      /*references
      https://forums.oracle.com/ords/apexds/post/how-to-fetching-column-names-from-oracle-using-php-2006
      https://github.students.cs.ubc.ca/CPSC304/CPSC304_PHP_Project
      https://docs.oracle.com/database/121/TDPPH/ch_three_db_access_class.htm#TDPPH149
      */
      require('serverstart.inc.php');
      /* ----------------------------Itinerary/GENERAL QUERIES-------------------------------------- */
      //all the info to view locations, tansport options, and insert/delete items in group/user itineraries
      //generic printResult function for any given table - provide name of table and the resulting relation
      function printResult($table, $result) { //prints results from a select statement
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
      /*
      function insertPersonTest() {
        global $db_conn;
        $res = executePlainSQL("INSERT INTO Person(ID, uname, email) VALUES ('0000000000000001', 'Benjamin Raine', 'beepbeep@sheep.com')");
        OCICommit($db_conn);
        //$print = executePlainSQL("SELECT * FROM Person p");
        //printResult("PEOPLE", $print);
      } */

      function handleGetItineraryRequest() {
        global $db_conn;
        $id = $_GET['inputItinerary'];
        $res = executePlainSQL("SELECT t.fromAddress, t.departure, t.transportID, t.toAddress, t.arrival
                              FROM TravelsBetween t
                              WHERE t.itineraryID = '$id'");
        printResult("Current Itinerary", $res);
      }

      function handleGetLocationsRequest() {
        global $db_conn;
        $res = executePlainSQL("SELECT L.Address AS Address, L.locationName AS Name FROM Location L");
        printResult("Locations", $res);
      }

      function handleGetTransportRequest() {
        global $db_conn;
        $res = executePlainSQL("SELECT t.ID AS ID, t.transportType AS TYPE FROM Transport T");
        printResult("Transport Options", $res);
      }

      //DELETE FROM GROUP'S ITINERARY
      function handleGroupItineraryDeleteRequest() {
        global $db_conn;
        $id = $_POST['insGroupDelete'];
        $itineraryID = $_POST['insItineraryGroupDelete'];
        $departure = $_POST['insDepartureGroupDelete'];
        if (!$id || !$itineraryID || !$departure) {
          echo "<br>Please input all information!<br>";
            return;
        }
        $check = executePlainSQL("SELECT COUNT (*) 
                            FROM GroupPlans g 
                            WHERE g.groupID = '$id' AND g.itineraryID = '$itineraryID'");

        if (($row = oci_fetch_row($check)) != false && $row[0] == 0) {
          echo "<br> Sorry, you do not have access to this itinerary!<br>";
          return;
        }
        
        $res = executePlainSQL("DELETE FROM TravelsBetween t 
                                WHERE t.itineraryID = $itineraryID AND t.departure = TO_TIMESTAMP('$departure')");
        
        echo "<br>Successfully deleted<br>";
        $deleteItinerary = executePlainSQL("SELECT COUNT (*)
                                            FROM TravelsBetween t
                                            WHERE t.itineraryID = '$itineraryID'");
        
        if (($row = oci_fetch_row($deleteItinerary)) != false && $row[0] == 0) {
          executePlainSQL("DELETE FROM Itinerary i
                          WHERE i.ID = '$itineraryID'");
          //CASCADE FUNCTIONALITY              
          echo "<br> Itinerary empty, so deleted<br>";
          //return;
          
        }
        OCICommit($db_conn);
      }

      //DELETE FROM USER'S ITINERARY
      function handleUserItineraryDeleteRequest() {
        global $db_conn;
        $id = $_POST['insUserDelete'];
        $itineraryID = $_POST['insItineraryUserDelete'];
        $departure = $_POST['insDepartureUserDelete'];
        if (!$id || !$itineraryID || !$departure) {
          echo "<br>Please input all information!<br>";
            return;
        }
        $check = executePlainSQL("SELECT COUNT (*) 
                            FROM UserPlans u 
                            WHERE u.userID = '$id' AND u.itineraryID = '$itineraryID'");

        if (($row = oci_fetch_row($check)) != false && $row[0] == 0) {
          echo "<br> Sorry, you do not have access to this itinerary!<br>";
          return;
        }
        
        $res = executePlainSQL("DELETE FROM TravelsBetween t 
                                WHERE t.itineraryID = '$itineraryID' AND t.departure = TO_TIMESTAMP('$departure')");
        $deleteItinerary = executePlainSQL("SELECT COUNT (*)
                                            FROM TravelsBetween t
                                            WHERE t.itineraryID = '$itineraryID'");
        if (($row = oci_fetch_row($deleteItinerary)) != false && $row[0] == 0) {
          executePlainSQL("DELETE FROM Itinerary i
                          WHERE i.ID = '$itineraryID'");
          //CASCADE FUNCTIONALITY              
          echo "<br> Itinerary empty, so deleted<br>";
  
        }
        echo "<br>Successfully deleted<br>";
        OCICommit($db_conn);
      }

      function handleGroupItineraryInsertRequest() {
        global $db_conn;
        $id = $_POST['insGroupInsert'];
        $itineraryID = $_POST['insGroupItineraryInsert'];
        $departure = $_POST['insGroupDepartureInsert'];
        $to = $_POST['insGroupTo'];
        $from = $_POST['insGroupFrom'];
        $transport = $_POST['insGroupTransport'];

        if (!$id || !$itineraryID || !$departure || !$to || !$from || !$transport) {
            echo "<br>Please input all information!<br>";
            return;
        }

        //if the group does not exist, send error message
        $checkGroupInsert = executePlainSQL("SELECT COUNT (*) FROM TouristGroup g WHERE g.ID = '$id'");;
        if (($row1 = oci_fetch_row($checkGroupInsert)) != false && $row1[0] == 0) {
            echo "<br>Sorry, that group doesn't exist! Return to the home page to add new group<br>";
            return;
        }

        //echo "<br>Group Exists<br>";

        $checkItinerary = executePlainSQL("SELECT COUNT (*) FROM Itinerary j WHERE j.ID =  '$itineraryID'");
        $checkAccess = executePlainSQL("SELECT COUNT(*) FROM GroupPlans g WHERE g.itineraryID = '$itineraryID' and g.groupID = '$id'");

        //if trying to access an itinerary that is not their group's, send error message
        if (($row = oci_fetch_row($checkItinerary)) != false && $row[0] != 0 && 
            ($row2 = oci_fetch_row($checkAccess)) != false && $row2[0] == 0) {
              echo "<br>Sorry, that's not your itinerary<br>";
            return;
        }

        $checkValidTransport = executePlainSQL("SELECT COUNT (*) FROM TravelsBetween t 
                                                WHERE t.transportID = '$transport' AND t.toAddress = '$to' AND t.fromAddress = '$from'");
        //if trying to insert a transport between locations that does not exist
        if (($row = oci_fetch_row($checkValidTransport)) != false && $row[0] == 0) {
           echo "<br>Sorry, that transport cannot be used between to get to these locations<br>";
           return;                         
        }

        $checkItinerary = executePlainSQL("SELECT COUNT (*) FROM Itinerary j WHERE j.ID =  '$itineraryID'");
        //if itinerary does not exist yet, add itinerary (without title)
        if (($row = oci_fetch_row($checkItinerary)) != false && $row[0] == 0) {
          executePlainSQL("INSERT INTO Itinerary (ID, title) VALUES ('$itineraryID', 'Untitled')");     
          executePlainSQL("INSERT INTO GroupPlans(itineraryID, groupID) 
                          VALUES ('$itineraryID', '$id')");
          //OCICommit($db_conn);
        }

        //find proper arrival time and insert the new item into the itinerary
        $findTripLength = executePlainSQL("SELECT (t.arrival - t.departure + TO_TIMESTAMP('$departure')) AS Length
                                            FROM TravelsBetween t
                                            WHERE t.fromAddress = '$from' AND t.toAddress = '$to' AND t.transportID = '$transport'");
        if (($rowLength = oci_fetch_row($findTripLength)) != false) {
          //$arrival = TO_TIMESTAMP($departure) + $rowLength[0];
          executePlainSQL("INSERT INTO TravelsBetween(itineraryID, departure, arrival, transportID, toAddress, fromAddress)
                        VALUES ('$itineraryID', TO_TIMESTAMP('$departure'), TO_TIMESTAMP('$rowLength[0]'),
                                '$transport', '$to', '$from')");
        echo "<br>Successfully added<br>";
        OCICommit($db_conn); 
        }
        
      }

      function handleUserItineraryInsertRequest() {
        global $db_conn;
        $id = $_POST['insUserInsert'];
        $itineraryID = $_POST['insUserItineraryInsert'];
        $departure = $_POST['insUserDepartureInsert'];
        $to = $_POST['insUserTo'];
        $from = $_POST['insUserFrom'];
        $transport = $_POST['insUserTransport'];

        if (!$id || !$itineraryID || !$departure || !$to || !$from || !$transport) {
            echo "<br>Please input all information!<br>";
            return;
        }

        //if the user does not exist, send error message
        $checkUserInsert = executePlainSQL("SELECT COUNT (*) FROM Person p WHERE p.ID = $id");;
        if (($row1 = oci_fetch_row($checkUserInsert)) != false && $row1[0] == 0) {
            echo "<br>Sorry, you don't have an account yet! Return to the home page to add new group<br>";
            return;
        }

        $checkItinerary = executePlainSQL("SELECT COUNT (*) FROM Itinerary j WHERE j.ID =  '$itineraryID'");
        $checkAccess = executePlainSQL("SELECT COUNT(*) FROM UserPlans u WHERE u.itineraryID = '$itineraryID' and u.userID = '$id'");

        //if trying to access an itinerary that is not their's, send error message
        if (($row = oci_fetch_row($checkItinerary)) != false && $row[0] != 0 && 
            ($row2 = oci_fetch_row($checkAccess)) != false && $row2[0] == 0) {
              echo "<br>Sorry, that's not your itinerary<br>";
            return;
        }

        $checkValidTransport = executePlainSQL("SELECT COUNT (*) FROM TravelsBetween t 
                                                WHERE t.transportID = '$transport' AND t.toAddress = '$to' AND t.fromAddress = '$from'");
        //if trying to insert a transport between locations that does not exist
        if (($row = oci_fetch_row($checkValidTransport)) != false && $row[0] == 0) {
           echo "<br>Sorry, that transport cannot be used between to get to these locations<br>";
           return;                         
        }

        $checkItinerary = executePlainSQL("SELECT COUNT (*) FROM Itinerary j WHERE j.ID =  '$itineraryID'");
        //if itinerary does not exist yet, add itinerary (without title)
        if (($row = oci_fetch_row($checkItinerary)) != false && $row[0] == 0) {
          executePlainSQL("INSERT INTO Itinerary (ID, title) VALUES ('$itineraryID', 'Untitled')");            
          executePlainSQL("INSERT INTO UserPlans(itineraryID, userID) 
                          VALUES ('$itineraryID', '$id')");
        }

        //find proper arrival time and insert the new item into the itinerary
        $findTripLength = executePlainSQL("SELECT (t.arrival - t.departure + TO_TIMESTAMP('$departure')) AS Length
                                            FROM TravelsBetween t
                                            WHERE t.fromAddress = '$from' AND t.toAddress = '$to' AND t.transportID = '$transport'");
        if (($rowLength = oci_fetch_row($findTripLength)) != false) {
          //$arrival = TO_TIMESTAMP($departure) + $rowLength[0];
          executePlainSQL("INSERT INTO TravelsBetween(itineraryID, departure, arrival, transportID, toAddress, fromAddress)
                        VALUES ('$itineraryID', TO_TIMESTAMP('$departure'), TO_TIMESTAMP('$rowLength[0]'),
                                '$transport', '$to', '$from')");
          echo "<br>Successfully added<br>";
          OCICommit($db_conn); 
        }

      }

      function handlePOSTRequest() {
        
          if (connectToDB()) {
            if (array_key_exists('deleteGroupItineraryRequest', $_POST)) {
              handleGroupItineraryDeleteRequest();
            } else if (array_key_exists('deleteUserItineraryRequest', $_POST)) {
              handleUserItineraryDeleteRequest();
            } else if (array_key_exists('insertGroupItineraryRequest', $_POST)) {
              handleGroupItineraryInsertRequest();
            } else if (array_key_exists('insertUserItineraryRequest', $_POST)) {
              handleUserItineraryInsertRequest();
            }
            

              disconnectFromDB();
          }
      }

      function handleGETRequest() {
        if (connectToDB()) {
            if (array_key_exists('getLocations', $_GET)) {
              handleGetLocationsRequest();
            } else if (array_key_exists('getTransports', $_GET)) {
              handleGetTransportRequest();
            } else if (array_key_exists('getItinerary', $_GET)) {
              handleGetItineraryRequest();
            }

            disconnectFromDB();
        }
      }

      if (isset($_POST['deleteGroupSubmit']) ||
          isset($_POST['deleteUserSubmit']) ||
          isset($_POST['insertGroupSubmit']) ||
          isset($_POST['insertUserSubmit'])) {
          handlePOSTRequest();
      } else if (isset($_GET['get-locations'])
                || isset($_GET['get-transports'])
                || isset($_GET['get-itinerary'])
                ) {
          handleGETRequest();
      }
    ?>
  </body>
</html>
