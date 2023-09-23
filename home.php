<html>
  <head>
    <title>Trip Planner</title>
  </head>

    <?php
    /*references
    https://forums.oracle.com/ords/apexds/post/how-to-fetching-column-names-from-oracle-using-php-2006
    https://github.students.cs.ubc.ca/CPSC304/CPSC304_PHP_Project
    https://docs.oracle.com/database/121/TDPPH/ch_three_db_access_class.htm#TDPPH149
    */
    require('serverstart.inc.php');

    /* ----------------------------GENERAL-------------------------------------- */
    //generic printResult function for any given table - provide name of table and the resulting relation
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

    // GENERAL PROJECTION QUERY
    function handleProjectionRequest() {
      global $db_conn;
      global $projectionTableResult;
      global $projectionQueryResult;
      global $projectionErrorMessage;

      $table = $_GET['projection-table'];
      $attributes = $_GET['projection-attributes'];

      if (count($attributes) == 0) {
        $projectionErrorMessage = "There was an issue fetching the data. Please make sure you have selected a table and 1 or more attributes.";
      } else {
        $query = "SELECT " . implode(", ", $attributes) . " FROM " . $table;
        $res = executePlainSQL($query);
        $projectionTableResult = $table;
        $projectionQueryResult = $res;
      }
    }

    // GENERAL SELECTION QUERY 
    function handleSelectionRequest() {
      global $db_conn;
      global $selectionRequirement;
      global $selectionQueryResult;

      $table = $_GET['selection-table'];
      $attributes = $_GET['selection-attributes'];

      $query = "SELECT " . implode(", ", $attributes) . 
               " FROM " . $table . 
               " WHERE " . $selectionRequirement;

      $res = executePlainSQL($query);
      $selectionQueryResult = $res;
    }

    /* ----------------------------HOME PAGE QUERIES-------------------------------------- */

    // JOIN QUERY - HOME PAGE GUIDES
    function getGuides() {
      global $db_conn;
      global $getGuidesResult;

      $res = executePlainSQL("SELECT G.ID as GuideID, G.title as Title, U.ID as UserID, U.uname as UserName
                              FROM Guide G, Person U 
                              WHERE G.authorID = U.ID");

      $getGuidesResult = $res;
    }

    // JOIN QUERY - HOME PAGE USER ITINERARIES 
    function getUserItineraries() {
      global $db_conn;
      global $getUserItinerariesResult;

      $res = executePlainSQL("SELECT I.ID as ItineraryID, I.title as Title, U.ID as UserID, U.uname as UserName 
                              FROM Itinerary I, UserPlans UP, Person U 
                              WHERE I.ID = UP.itineraryID AND UP.userID = U.ID");

      $getUserItinerariesResult = $res;
    }

    // JOIN QUERY - GET GUIDE BY ID
    function getGuideByID() {
      global $db_conn;
      global $getGuideByIDResult;
      $guideID = $_GET['guideID'];

      $query = "SELECT G.ID as ID, G.title, G.authorID, A.address, L.locationName, L.cityName, L.region, L.country, L.opening, L.closing, A.description
                FROM Guide G, About A, Location L 
                WHERE G.ID = '$guideID' AND A.guideID = G.ID AND A.address = L.address";

      $res = executePlainSQL($query);
      $getGuideByIDResult = $res;
    }

    // JOIN QUERY - GET ITINERARY BY ID
    function getItineraryByID() {
      global $db_conn;
      global $getItineraryByIDResult;
      $itineraryID = $_GET['itineraryID'];

      $query = "SELECT I.ID, I.title, TB.transportID, T.transportType, TB.departure, TB.fromAddress, TB.arrival, TB.toAddress
                FROM Itinerary I, TravelsBetween TB, Transport T
                WHERE I.ID = '$itineraryID' AND I.ID = TB.itineraryID AND TB.transportID = T.ID";
                
      $res = executePlainSQL($query);
      $getItineraryByIDResult = $res;
    }

    // JOIN QUERY - HOME PAGE GROUP ITINERARIES
    function getGroupItineraries() {
      global $db_conn;
      global $getGroupItinerariesResult;

      $res = executePlainSQL("SELECT I.ID as ItineraryID, I.title as Title, G.ID as GroupID, G.title as GroupTitle 
                              FROM Itinerary I, GroupPlans GP, TouristGroup G 
                              WHERE I.id = GP.itineraryID AND GP.groupID = G.ID");

      $getGroupItinerariesResult = $res;
    }

    // DIVISION QUERY - GET GUIDES THAT ARE ABOUT ALL LOCATIONS
    function filterGuidesAboutAllLocations() {
      global $db_conn;
      global $getTopGuidesResult;

      $divisionQuery = "SELECT G.ID as ID
                        FROM Guide G
                        WHERE NOT EXISTS (SELECT L.address
                                          FROM Location L
                                          WHERE NOT EXISTS (SELECT A.guideID
                                                            FROM About A
                                                            WHERE A.address = L.address AND A.guideID = G.ID))";

      $displayTable = "SELECT G.ID as GuideID, G.title as Title, G.authorID as AuthorID, U.uname as AuthorName
                      FROM Guide G, Person U, ($divisionQuery) D
                      WHERE G.ID = D.ID AND G.authorID = U.ID";

      $res = executePlainSQL($displayTable);
      $getTopGuidesResult = $res;
    }

    // AGGREGATION WITH GROUP BY
    function getItineraryCountByGroup() {
      global $db_conn;
      global $getGroupActivityResult;

      $res = executePlainSQL("SELECT groupID as GroupID, COUNT(itineraryID) as ItineraryCount
                              FROM GroupPlans 
                              GROUP BY groupID");

      $getGroupActivityResult = $res;
      }

      // NESTED AGGREGATION WITH GROUP BY
      function getGroupWithMostSpace() {
      global $db_conn;
      global $groupsWithSpaceResult;
        
      $res = executePlainSQL("SELECT G.groupID as GroupID, AVG(G.maxParticipants) as AverageMaxParticipants
                              FROM GroupPlans G 
                              GROUP BY G.groupID
                              HAVING AVG(G.maxParticipants) >= ALL (SELECT AVG(G2.maxParticipants) 
                                                                  FROM GroupPlans G2
                                                                  GROUP BY groupID)");

      $groupsWithSpaceResult = $res;
    }

    function setAttributeOptions() {
      global $selectedTable;
      global $attributeOptions;
      global $selectedLabels;
      $attributeOptions = "";

      // somehow this did not work with a 2D array, so there's a huge switch statement instead
      $personLabels = ["ID" => "ID", "uname" => "Name", "email" => "Email"];
      $groupLabels = ["ID" => "ID", "title" => "Title"];
      $memberLabels = ["userID" => "UserID", "groupID" => "GroupID"];
      $guideLabels = ["ID" => "ID", "title" => "Title", "authorID" => "AuthorID"];
      $itineraryLabels = ["ID" => "ID", "title" => "Title"];
      $groupPlansLabels = ["itineraryID" => "ItineraryID", "groupID" => "GroupID", "maxParticipants" => "maxParticipants"];
      $userPlansLabels = ["itineraryID" => "ItineraryID", "userID" => "UserID"];
      $cityLabels = ["cityName" => "CityName", "country" => "Country", "region" => "Region"];
      $languageLabels = ["localLanguage" => "Language", "country" => "Country", "region" => "Region"];
      $currencyLabels = ["currency" => "Currency", "country" => "Country"];
      $transportLabels = ["ID" => "ID", "transportType" => "TransportType"];
      $eventLabels = ["eventName" => "EventName", "startsAt" => "StartsAt", "address" => "Address", "admissionPrice" => "AdmissionPrice"];
      $locationLabels = [
        "locationName" => "LocationName", 
        "address" => "Address", 
        "country" => "Country", 
        "cityName" => "CityName", 
        "region" => "Region", 
        "opening" => "Opening", 
        "closing" => "Closing"
      ];
      $attractionLabels = ["address" => "Address", "admissionPrice" => "AdmissionPrice", "attractionType" => "AttractionType"];
      $diningLabels = ["address" => "Address", "cuisine" => "Cuisine"];
      $hospitalityLabels = ["address" => "Address", "rate" => "Rate"];
      $aboutLabels = ["guideID" => "GuideID", "address" => "Address", "description" => "Description"];
      $travelsBetweenLabels = [
        "itineraryID" => "ItineraryID", 
        "departure" => "Departure", 
        "arrival" => "Arrival", 
        "transportID" => "TransportID", 
        "toAddress" => "ToAddress", 
        "fromAddress" => "FromAddress"
      ];

      switch ($selectedTable) {
        case "Person":
          $selectedLabels = $personLabels;
          break;
        case "TouristGroup":
          $selectedLabels = $groupLabels;
          break;
        case "Member":
          $selectedLabels = $memberLabels;
          break;
        case "Guide":
          $selectedLabels = $guideLabels;
          break;
        case "Itinerary":
          $selectedLabels = $itineraryLabels;
          break;
        case "GroupPlans":
          $selectedLabels = $groupPlansLabels;
          break;
        case "UserPlans":
          $selectedLabels = $userPlansLabels;
          break;
        case "City":
          $selectedLabels = $cityLabels;
          break;
        case "Language":
          $selectedLabels = $languageLabels;
          break;
        case "Currency": 
          $selectedLabels = $currencyLabels;
          break;
        case "Transport":
          $selectedLabels = $transportLabels;
          break;
        case "Event":
          $selectedLabels = $eventLabels;
          break;
        case "Location":
          $selectedLabels = $locationLabels;
          break;
        case "Attraction":
          $selectedLabels = $attractionLabels;
          break;
        case "Dining":
          $selectedLabels = $diningLabels;
          break;
        case "Hospitality":
          $selectedLabels = $hospitalityLabels;
          break;
        case "About":
          $selectedLabels = $aboutLabels;
          break;
        case "TravelsBetween":
          $selectedLabels = $travelsBetweenLabels;
          break;
      }

      foreach($selectedLabels as $key => $value) {
        $attributeOptions = $attributeOptions . "<option value=" . $key . ">" . $value . "</option>";
      }
    }

    function getCheapVenues() {
        global $db_conn;
        global $getCheapVenuesResult;

        $price = (float) $_GET['price-having'];

        $res = executePlainSQL("SELECT L.locationName, L.address, AVG(E.admissionPrice)
                              FROM LOCATION L, EVENT E
                              WHERE L.address = E.address AND L.cityName = '" . $_GET['city-having'] . "'
                              GROUP BY L.address, L.locationName
                              HAVING AVG(E.admissionPrice) <= '" . $price . "'");

        $getCheapVenuesResult = $res;
    }

    function handleGETRequest() {
      global $projectionErrorMessage;

      if (connectToDB()) {
        if (array_key_exists('getGuides', $_GET)) {
          getGuides();
        } else if (array_key_exists('getUserItineraries', $_GET)) {
          getUserItineraries();
        } else if (array_key_exists('getGroupItineraries', $_GET)) {
          getGroupItineraries();
        } else if (array_key_exists('filterGuidesAboutAllLocations', $_GET)) {
          filterGuidesAboutAllLocations();
        } else if (array_key_exists('getItineraryCountByGroup', $_GET)) {
          getItineraryCountByGroup();
        } else if (array_key_exists('getGroupWithMostSpace', $_GET)) {
          getGroupWithMostSpace();
        } else if (array_key_exists('projectionRequest', $_GET)) {
          if ($_GET['projection-table'] and $_GET['projection-attributes']) {
            handleProjectionRequest();
          } else {
            $projectionErrorMessage = "There was an issue fetching the data. Please make sure you have selected a table and one or more fields to view.";
          }
        } else if (array_key_exists('selectionRequest', $_GET)) {
          if ($_GET['selection-table'] and $_GET['selection-attributes'] and $_GET['selection-query-attribute'] and $_GET['selection-query-operator'] and $_GET['selection-query-value']) {
            global $selectionRequirement;
            $value = $_GET['selection-query-value']; // need to sanitize
            $selectionRequirement = $_GET['selection-query-attribute'] . $_GET['selection-query-operator'] . "'" . $value . "'";
            handleSelectionRequest();
          } else {
            global $selectionErrorMessage;
            $selectionErrorMessage = "There was an issue with your search. Please make sure you have selected a table, one or more fields to view, and provided a complete condition to filter by.";
            echo $selectionErrorMessage;
          }
        } else if (array_key_exists('getGuideByID', $_GET)) {
          if (strlen($_GET['guideID']) == 16 and preg_match('/^[A-Za-z0-9]*$/', $_GET['guideID'])) {
            getGuideByID();
          } else {
            global $getGuideByIDErrorMessage;
            $getGuideByIDErrorMessage = "There was an issue fetching the guide details. Please make sure you have entered a valid guide ID. An ID should be 16 digits long and contain only letters or numbers.";
          }
        } else if (array_key_exists('getItineraryByID', $_GET)) {
          if (strlen($_GET['itineraryID']) == 16 and preg_match('/^[A-Za-z0-9]*$/', $_GET['itineraryID'])) {
            getItineraryByID();
          } else {
            global $getItineraryByIDErrorMessage;
            $getItineraryByIDErrorMessage = "There was an issue fetching the itinerary details. Please make sure you have entered a valid itinerary ID. An ID should be 16 digits and contain only letters or numbers.";
          }
        } else if (array_key_exists('cheapVenues', $_GET)) {
            if(is_numeric($_GET['price-having'])) {
                getCheapVenues();
            } else {
                global $getCheapValuesError;
                $getCheapValuesError = "You did not enter a valid number, please enter a valid number into the price input.";
            }
        }

        disconnectFromDB();
      }
    }

    if (isset($_GET['projection-table'])) {
      global $selectedTable;
      $selectedTable = $_GET['projection-table'];
      setAttributeOptions();
    } else if (isset($_GET['selection-table'])) {
      global $selectedTable;
      $selectedTable = $_GET['selection-table'];
      setAttributeOptions();
    }

    if (isset($_GET['get-guides']) ||
      isset($_GET['get-user-itineraries']) ||
      isset($_GET['get-group-itineraries']) || 
      isset($_GET['filter-itineraries-location']) || 
      isset($_GET['filter-guides-location']) || 
      isset($_GET['filter-guides-about-all-locations']) || 
      isset($_GET['get-itinerary-count-by-group']) || 
      isset($_GET['get-group-with-most-space']) ||
      isset($_GET['projection-request']) ||
      isset($_GET['selection-request']) ||
      isset($_GET['get-guide-by-id']) ||
      isset($_GET['get-itinerary-by-id']) ||
      isset($_GET['cheap-venues'])) {
      handleGETRequest();
    } 

  ?>

  <?php
  if (strlen($getGuideByIDErrorMessage) > 0) {
      echo "<p style='color:red;'>" . $getGuideByIDErrorMessage . "</p>";
  }
  ?>

  <?php
  if (strlen($getItineraryByIDErrorMessage) > 0) {
      echo "<p style='color:red;'>" . $getItineraryByIDErrorMessage . "</p>";
  }
  ?>

  <?php
  if (strlen($getCheapValuesError) > 0) {
      echo "<p style='color:red;'>" . $getCheapValuesError . "</p>";
  }
  ?>

  <div style="display:flex;">

      <div style="width:40%;">
        <h2>Sign Up / Update User</h2>
        <p>To create a user, or to edit their information press the Sign Up button</p>

        <a href="./user-form.php">Sign Up</a>
      </div>

      <div style="width:40%;">
        <h2>Create Group / Join Group</h2>
        <p>To create a Group, or to join an existing group press the Create Group button</p>

        <a href="./group-form.php">Create Group</a>
      </div>

  </div>

  <hr />

  <div style="display:flex;">
      <div style="width:40%;">
        <h2>Locations with Low Event Costs</h2>
        <p>Enter a city and maximum price, you will receive all locations in that city with a lower average cost.</p>

        <form method="GET" action="home.php">
        <input type="hidden" id="cheapVenues" name="cheapVenues">
        City: <input type="text" name="city-having">
        <br><br>
        Average Price: <input type="text" name="price-having">
        <br><br>
        <p><input type="submit" value="Locations with low event prices" name="cheap-venues"></p>
            <?php if ($getCheapVenuesResult) echo printResult("Cheap Venues", $getCheapVenuesResult); ?>
        </form>
      </div>

  </div>

  <hr />

    <h2>View Guides and Itineraries</h2>
    <div style="display:flex;">
      <div style="width:40%;">
        <h3>All Guides</h3>
        <p>To see a list of all guides and their authors, press the Show Guides button</p>
        <form method="GET" action="home.php">
          <input type="hidden" id="getGuides" name="getGuides">
          <p><input type="submit" value="Show Guides" name="get-guides"></p>
          <?php if ($getGuidesResult) echo printResult("All Guides", $getGuidesResult); ?>
        </form>
      </div>

      <hr style="margin:20px;"/>

      <div>
        <h3>All Itineraries</h3>
        <p>To see a list of all itineraries planned by users, press the Show User Itineraries button</p>
        <form method="GET" action="home.php">
          <input type="hidden" id="getUserItineraries" name="getUserItineraries">
          <p><input type="submit" value="Show User Itineraries" name="get-user-itineraries"></p>
          <?php if ($getUserItinerariesResult) echo printResult("All User Itineraries", $getUserItinerariesResult); ?>
        </form>

        <p>To see a list of all itineraries planned by groups, press the Show Group Itineraries button</p>
        <form method="GET" action="home.php">
          <input type="hidden" id="getGroupItineraries" name="getGroupItineraries">
          <p><input type="submit" value="Show Group Itineraries" name="get-group-itineraries"></p>
          <?php if ($getGroupItinerariesResult) echo printResult("All Group Itineraries", $getGroupItinerariesResult); ?>
        </form>
      </div>
    </div>

    <hr>

    <div>
      <div style="display:flex;">
        <div>
          <h3>Guide Details</h3>
          <p>To see the details of a specific guide, enter the guide's ID below and click View Guide:</p>
          <form>
            <input type="text" name="guideID" id="guideID" placeholder="Enter an ID">

            <input type="hidden" id="getGuideByID" name="getGuideByID">
            <p><input type="submit" value="View Guide" name="get-guide-by-id"></p>
              <?php if ($getGuideByIDResult) echo printResult('Guide Details', $getGuideByIDResult); ?>
          </form>
        </div>

        <hr style="margin:20px;"/>
        
        <div>
          <h3>Itinerary Details</h3>
          <p>To see the details of a specific itinerary, enter the itinerary's ID below and click View Itinerary:</p>
          <form>
            <input type="text" name="itineraryID" id="itineraryID" placeholder="Enter an ID">

            <input type="hidden" id="getItineraryByID" name="getItineraryByID">
            <p><input type="submit" value="View Itinerary" name="get-itinerary-by-id"></p>
              <?php if ($getItineraryByIDResult) echo printResult("All User Itineraries", $getItineraryByIDResult); ?>
          </form>
        </div>
      </div>
    </div>

    <hr />

    <h2>Create Your Own</h2>
    <div style="display:flex;">
      <div>
        <a href="./itinerary-form.php"><h3>Create New Itinerary</h3></a>

      </div>

        <hr style="margin:20px;"/>

        <div>
            <a href="./guide-form.php"><h3>Create Guide</h3></a>
        </div>
    </div>

    <hr />

    <h2>Community Activity</h2>

    <div style="display:flex;">
      <div style="width:33%;">
        <h3>Top Guides</h3>
        <p>To see guides that are about all of the locations in our database, press the See Top Guides button</p>
        <form method="GET" action="home.php">
          <input type="hidden" id="filterGuidesAboutAllLocations" name="filterGuidesAboutAllLocations">
          <p><input type="submit" value="See Top Guides" name="filter-guides-about-all-locations"></p>
          <?php if ($getTopGuidesResult) echo printResult("Guides About All Locations", $getTopGuidesResult); ?>
        </form>
      </div>

      <hr style="margin:20px;"/>
      
      <div style="width:33%;">
        <h3>Group Activity Levels</h3>
        <p>To see the number of itineraries created by each group, press the See Group Activity button</p>
        <form method="GET" action="home.php">
          <input type="hidden" id="getItineraryCountByGroup" name="getItineraryCountByGroup">
          <p><input type="submit" value="See Group Activity" name="get-itinerary-count-by-group"></p>
          <?php if ($getGroupActivityResult) echo printResult("Itinerary Count By Group", $getGroupActivityResult); ?>
        </form>
      </div> 

      <hr style="margin:20px;"/>

      <div style="width:33%;">
        <h3>Groups With Lots of Space</h3>
        <p>To see the group(s) with the highest average maxParticipants, press the Find Groups With Space button</p>
        <form method="GET" action="home.php">
          <input type="hidden" id="getGroupWithMostSpace" name="getGroupWithMostSpace">
          <p><input type="submit" value="Find Groups With Space" name="get-group-with-most-space"></p>
          <?php if  ($groupsWithSpaceResult) echo printResult('Groups With Highest Average MaxParticipants', $groupsWithSpaceResult); ?>
        </form> 
      </div>
    </div>

    <hr />

    <h2>Find Information</h2>

    <div style="display:flex;">
      <div style="width:50%;">
        <h3>Discover</h3>
        <p>Get any information you want to see</p>

        <?php 
          global $selectedTable;
          global $tableOptions;
          global $optionLabels;
          
          $optionLabels = [
            "Person" => "Users",
            "TouristGroup" => "Groups",
            "Member" => "Members of Groups",
            "Guide" => "Guides",
            "Itinerary" => "Itineraries",
            "GroupPlans" => "Itineraries Planned by Groups",
            "UserPlans" => "Itineraries Planned by Users",
            "City" => "Cities",
            "Language" => "Languages",
            "Currency" => "Currencies",
            "Transport" => "Transport",
            "Event" => "Events",
            "Location" => "Locations",
            "Attraction" => "Attractions",
            "Dining" => "Dining",
            "Hospitality" => "Hospitality",
            "About" => "About",
            "TravelsBetween" => "Itinerary Details",
          ];

          $tableOptions = "<option value='' disabled selected>Select table</option>";
          foreach($optionLabels as $key => $value){
            if ($key == $selectedTable) {
              $tableOptions = $tableOptions . "<option value=" . $key . " selected >" . $value . "</option>";
            } else {
              $tableOptions = $tableOptions . "<option value=" . $key . ">" . $value . "</option>";
            }
          }
        ?>

        <form method="GET" action="home.php">
          <form>
            <label for="projection-table">Select a table to view: </label>
            <select name="projection-table" id="projection-table" onchange="this.form.submit()">
              <?php echo $tableOptions; ?>
            </select>
          </form>

          <br />

          <form>
            <label for="projection-attributes[]">Select specific fields to view from the table: </label>
            <select name='projection-attributes[]' id='projection-attributes' multiple>
              <?php echo $attributeOptions; ?>
            </select>
            <p>*fields will appear based on the selected table, hold cmd to select multiple (ctrl on windows)</p>

            <?php
              global $selectedTable;
              echo "<input type='hidden' id='projection-table' name='projection-table' value=" . $selectedTable . ">";
            ?>
            <input type="hidden" id="projectionRequest" name="projectionRequest">
            <p><input type="submit" value="Get Data" name="projection-request"></p>
          </form>

          <?php
            global $projectionErrorMessage;
            global $projectionTableResult;
            global $projectionQueryResult;

            if ($projectionTableResult and $projectionQueryResult) {
              echo "<p style='color:green';>See results below</p>";
            } else if (strlen($projectionErrorMessage) > 0) {
              echo "<p style='color:red;'>" . $projectionErrorMessage . "</p>";
            }
          ?>
        </form>
      </div>

      <hr style="margin:20px;"/>

      <div>
        <h3>Search</h3>
        <p>Filter any information</p>

        <form method="GET" action="home.php">
          <form>
            <label for="selection-table">Select a table to search: </label>
            <select name="selection-table" id="selection-table" onchange="this.form.submit()">
              <?php echo $tableOptions; ?>
            </select>
          </form>

          <br />

          <form>
            <label for="selection-attributes[]">Select fields to view from the filtered table: </label>
            <select name='selection-attributes[]' id='selection-attributes' multiple>
              <?php echo $attributeOptions; ?>
            </select>
            <p>*fields will appear based on the selected table, hold cmd to select multiple (ctrl on windows)</p>

            <br/>

            <p>To filter the table, select a field you want a specific value for and specify the condition you would like the field to meet:</p>
            <div style="display:flex;">
              <select name='selection-query-attribute' id='selection-query-attribute'>
                <?php echo "<option value='' disabled selected>Select field</option>" . $attributeOptions; ?>
              </select>

              <select name="selection-query-operator" id="selection-query-operator">
                <option value="" disabled selected>Select condition</option>
                <option value="=">is</option>
                <option value="<>">is not</option>
                <option value=">">is greater than</option>
                <option value=">=">is greater than or equal to</option>
                <option value="<">is less than</option>
                <option value="<=">is less than or equal to</option>
              </select>

              <input type="text" id="selection-query-value" name="selection-query-value" placeholder="Enter value">
            </div>

            <?php
              global $selectedTable;
              echo "<input type='hidden' id='selection-table' name='selection-table' value=" . $selectedTable . ">";
            ?>
            <input type="hidden" id="selectionRequest" name="selectionRequest">
            <p><input type="submit" value="Search" name="selection-request"></p>
          </form>

          <?php
            global $selectionErrorMessage;
            global $selectionQueryResult;

            if ($selectionQueryResult) {
              echo "<p style='color:green';>See results below</p>";
            } else if (strlen($selectionErrorMessage) > 0) {
              echo "<p style='color:red;'>" . $selectionErrorMessage . "</p>";
            }
          ?>
        </form>
      </div>
    </div>

    <?php
      global $projectionTableResult;
      global $projectionQueryResult;

      if ($projectionTableResult and $projectionQueryResult) {
        echo printResult($projectionTableResult, $projectionQueryResult);
      }
    ?>

    <?php
      global $selectionErrorMessage;
      global $selectionQueryResult;

      if ($selectionQueryResult) {
        echo printResult("Search Result", $selectionQueryResult);
      }
    ?>

  </body>
</html>
