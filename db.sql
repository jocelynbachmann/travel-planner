CREATE TABLE Person (
   ID CHAR(16),
   uname CHAR(32) NOT NULL,
   email CHAR(255) UNIQUE NOT NULL,
   PRIMARY KEY(ID));

CREATE TABLE TouristGroup (
   ID CHAR(16),
   title CHAR(32),
   PRIMARY KEY(ID));

CREATE TABLE Member (
    userID CHAR(16),
    groupID CHAR(16),
    PRIMARY KEY(userID, groupID),
    FOREIGN KEY(userID) REFERENCES Person(ID)
        ON DELETE CASCADE,
    FOREIGN KEY(groupID) REFERENCES TouristGroup(ID)
        ON DELETE CASCADE);

CREATE TABLE Guide (
   ID CHAR(16),
   title CHAR(32),
   authorID CHAR(16) NOT NULL,
   PRIMARY KEY(ID),
   FOREIGN KEY(authorID) REFERENCES Person(ID)
       ON DELETE CASCADE);

CREATE TABLE Itinerary (
   ID CHAR(16),
   title CHAR(32),
   PRIMARY KEY(ID));


CREATE TABLE GroupPlans (
    itineraryID CHAR(16),
    groupID CHAR(16) NOT NULL,
    maxParticipants INTEGER,
    PRIMARY KEY(itineraryID),
    FOREIGN KEY(itineraryID) REFERENCES Itinerary(ID)
        ON DELETE CASCADE,
    FOREIGN KEY(groupID) REFERENCES TouristGroup(ID)
        ON DELETE CASCADE);


CREATE TABLE UserPlans (
    itineraryID CHAR(16),
    userID CHAR(16) NOT NULL,
    PRIMARY KEY(itineraryID),
    FOREIGN KEY(itineraryID) REFERENCES Itinerary(ID)
        ON DELETE CASCADE,
    FOREIGN KEY(userID) REFERENCES Person(ID)
        ON DELETE CASCADE);

CREATE TABLE City (
    country CHAR(32),
    region CHAR(32),
    cityName CHAR(32),
    PRIMARY KEY(country, region, cityName));


CREATE TABLE Language (
    country CHAR(32),
    region CHAR(32),
    localLanguage CHAR(16) NOT NULL,
    PRIMARY KEY(country, region));


CREATE TABLE Currency (
    country CHAR(32),
    currency CHAR(16) NOT NULL,
    PRIMARY KEY(country));


CREATE TABLE Transport (
    ID CHAR(16),
    transportType CHAR(16),
    PRIMARY KEY(ID));


CREATE TABLE Location (
    address CHAR(128),
    country CHAR(32),
    cityName CHAR(32),
    region CHAR(32),
    locationName CHAR(32),
    opening TIMESTAMP,
    closing TIMESTAMP,
    PRIMARY KEY(address),
    FOREIGN KEY(country, cityName, region) REFERENCES City(country, cityName, region)
    ON DELETE CASCADE);


CREATE TABLE Event (
                       eventName CHAR(64),
                       startsAt TIMESTAMP,
                       address CHAR(128) REFERENCES Location(address),
                       admissionPrice FLOAT,
                       PRIMARY KEY(eventName, startsAt, address));


CREATE TABLE Attraction (
    address CHAR(128) REFERENCES Location(address),
    admissionPrice FLOAT,
    attractionType CHAR(16),
    PRIMARY KEY(address));


CREATE TABLE Dining (
    address CHAR(128) REFERENCES Location(address),
    cuisine CHAR(16),
    PRIMARY KEY(address));


CREATE TABLE Hospitality (
    address CHAR(128) REFERENCES Location(address),
    rate FLOAT,
    PRIMARY KEY(address));


CREATE TABLE About (
    guideID CHAR(16),
    address CHAR(128) REFERENCES Location(address),
    description VARCHAR2(1000),
    PRIMARY KEY(guideID, address),
    FOREIGN KEY(guideID) REFERENCES Guide(ID)
    ON DELETE CASCADE);

CREATE TABLE TravelsBetween(
    itineraryID CHAR(16),
    departure TIMESTAMP,
    arrival TIMESTAMP,
    transportID CHAR(16) NOT NULL,
    toAddress CHAR(128) NOT NULL REFERENCES Location(address),
    fromAddress CHAR(128) NOT NULL REFERENCES Location(address),
    PRIMARY KEY (itineraryID, departure),
    FOREIGN KEY (itineraryID) REFERENCES Itinerary(ID)--,
    ON DELETE CASCADE,
    FOREIGN KEY (transportID) REFERENCES Transport(ID)
    ON DELETE CASCADE);

INSERT INTO Person(ID, uname, email) VALUES ('0000000000000001', 'Benjamin Raine', 'beepbeep@sheep.com');
INSERT INTO Person(ID, uname, email) VALUES ('0000000000000002', 'Autumn Steininger', 'auti@notaconvertable.ca');
INSERT INTO Person(ID, uname, email) VALUES ('0000000000000003', 'Fern Brady', 'bernardisapuppy@taskmaster.com');
INSERT INTO Person(ID, uname, email) VALUES ('0000000000000004', 'Nayimathun', 'nayimathun@cometspoopdragons.eu');
INSERT INTO Person(ID, uname, email) VALUES ('0000000000000005', 'Chelsea Baek', 'albaektross@birb.com');

INSERT INTO TouristGroup(ID, title) VALUES ('0000000000000001', 'This is a Group');
INSERT INTO TouristGroup(ID, title) VALUES ('0000000000000002', 'Don’t Call Me Fall');
INSERT INTO TouristGroup(ID, title) VALUES ('0000000000000003', 'Taskmaster Reunion');
INSERT INTO TouristGroup(ID, title) VALUES ('0000000000000004', 'Seiiki Dragons');
INSERT INTO TouristGroup(ID, title) VALUES ('0000000000000005', 'Streaming Buds');

INSERT INTO Member(userID ,groupID) VALUES ('0000000000000001', '0000000000000002');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000001', '0000000000000003');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000001', '0000000000000004');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000001', '0000000000000005');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000002', '0000000000000001');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000002', '0000000000000002');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000003', '0000000000000001');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000003', '0000000000000003');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000004', '0000000000000001');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000004', '0000000000000004');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000005', '0000000000000001');
INSERT INTO Member(userID ,groupID) VALUES ('0000000000000005', '0000000000000005');

INSERT INTO Guide(ID, title, authorID) VALUES ('0000000000000001', 'Ben’s Guide to the World', '0000000000000001');
INSERT INTO Guide(ID, title, authorID) VALUES ('0000000000000002', 'Ben’s Guide to Winnipeg', '0000000000000001');
INSERT INTO Guide(ID, title, authorID) VALUES ('0000000000000003', 'Ben’s Guide to Vancouver', '0000000000000001');
INSERT INTO Guide(ID, title, authorID) VALUES ('0000000000000004', 'Best Spots for Dragon Naps', '0000000000000004');
INSERT INTO Guide(ID, title, authorID) VALUES ('0000000000000005', 'Montreal without French', '0000000000000005');

INSERT INTO Itinerary (ID, title) VALUES ('0000000000000001', 'Doing Group Things');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000002', 'RSVPed to a Penguin Marriage');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000003', 'Reunion Schedule');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000004', 'A Day of Fallen Night');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000005', 'Where to Watch Glow');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000006', 'Graduation Trip');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000007', 'Chaperone Overseas');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000008', 'Avoiding the Abyss');
INSERT INTO Itinerary (ID, title) VALUES ('0000000000000009', 'Visiting Vancouver');
INSERT INTO Itinerary (ID, title) VALUES ('000000000000000a', 'Scotland Tour');
INSERT INTO Itinerary (ID, title) VALUES ('000000000000000b', 'Snugs and Hugs');
INSERT INTO Itinerary (ID, title) VALUES ('000000000000000c', 'Life');

INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('0000000000000001', '0000000000000001', 5);
INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('0000000000000002', '0000000000000002', 2);
INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('0000000000000003', '0000000000000003', 8);
INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('0000000000000004', '0000000000000004', 2);
INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('0000000000000005', '0000000000000005', 4);
INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('000000000000000b', '0000000000000005', 2);
INSERT INTO GroupPlans(itineraryID, groupID, maxParticipants) VALUES ('000000000000000c', '0000000000000001', 9);

INSERT INTO UserPlans (itineraryID, userID) VALUES ('0000000000000006', '0000000000000001');
INSERT INTO UserPlans (itineraryID, userID) VALUES ('0000000000000007', '0000000000000002');
INSERT INTO UserPlans (itineraryID, userID) VALUES ('0000000000000008', '0000000000000004');
INSERT INTO UserPlans (itineraryID, userID) VALUES ('0000000000000009', '0000000000000005');
INSERT INTO UserPlans (itineraryID, userID) VALUES ('000000000000000a', '0000000000000003');

INSERT INTO Language(country, region, localLanguage) VALUES ('Canada', 'Manitoba', 'English');
INSERT INTO Language(country, region, localLanguage) VALUES ('Canada', 'Quebec', 'French');
INSERT INTO Language(country, region, localLanguage) VALUES ('UK', 'Scotland', 'English');
INSERT INTO Language(country, region, localLanguage) VALUES ('Canada', 'British Columbia', 'English');
INSERT INTO Language(country, region, localLanguage) VALUES ('Japan', 'Kanto', 'Japanese');
INSERT INTO Language(country, region, localLanguage) VALUES ('Mexico', 'Distrito Federal', 'Spanish');
INSERT INTO Language(country, region, localLanguage) VALUES ('Seiiki', 'Orisima', 'Seiikiese');


INSERT INTO Currency(country, currency)  VALUES ('Canada', 'CAD');
INSERT INTO Currency(country, currency)  VALUES ('Japan', 'JPY');
INSERT INTO Currency(country, currency)  VALUES ('UK', 'GBP');
INSERT INTO Currency(country, currency)  VALUES ('Mexico', 'MXN');
INSERT INTO Currency(country, currency)  VALUES ('Seiiki', 'Dragons');

INSERT INTO City (country, region, cityName) VALUES ('Canada', 'Manitoba', 'Winnipeg');
INSERT INTO City (country, region, cityName) VALUES ('Canada', 'Quebec', 'Montreal');
INSERT INTO City (country, region, cityName) VALUES ('UK', 'Scotland', 'Edinburgh');
INSERT INTO City (country, region, cityName) VALUES ('Canada', 'British Columbia', 'Vancouver');
INSERT INTO City (country, region, cityName) VALUES ('Japan', 'Kanto', 'Tokyo');
INSERT INTO City (country, region, cityName) VALUES ('Mexico', 'Distrito Federal', 'Mexico City');
INSERT INTO City (country, region, cityName) VALUES('Seiiki', 'Orisima', 'Ginura');

INSERT INTO Transport(ID, transportType) VALUES ('A296', 'Airplane');
INSERT INTO Transport(ID, transportType) VALUES ('49 Metrotown', 'Bus');
INSERT INTO Transport(ID, transportType) VALUES ('Expo Line', 'Train');
INSERT INTO Transport(ID, transportType) VALUES ('A227', 'Airplane');
INSERT INTO Transport(ID, transportType) VALUES ('Tiger Eye Fleet', 'Boat');

INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('1473 Main St, R2W 3V9', 'Canada', 'Winnipeg', 'Manitoba', 'Santa Lucia', to_timestamp('2000-01-01 11:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('1721 Kenaston Blvd, R3Y 1V5', 'Canada', 'Winnipeg', 'Manitoba', 'Mongo’s Grill', to_timestamp('2000-01-01 11:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 21:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('2520 Portage Ave., R3J 3T6', 'Canada', 'Winnipeg', 'Manitoba', 'Holiday Inn', to_timestamp('2000-01-01 00:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('85 Israel Asper Way,  R3C 0L5', 'Canada', 'Winnipeg', 'Manitoba', 'Museum of Human Rights', to_timestamp('2000-01-01 10:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 17:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('55 Pavilion Crescent, R3P 2N6', 'Canada', 'Winnipeg', 'Manitoba', 'Assiniboine Park', to_timestamp('2000-01-01 9:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 17:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('300 Portage Ave, R3C 5S4', 'Canada', 'Winnipeg', 'Manitoba', 'MTS Centre', to_timestamp('2000-01-01 9:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 17:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('900 Georgia St W, V6C 2W6', 'Canada', 'Vancouver', 'British Columbia','The Fairmont', to_timestamp('2000-01-01 00:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('655 Burrard St, V6C 2R7', 'Canada', 'Vancouver', 'British Columbia', 'Hyatt Regency', to_timestamp('2000-01-01 00:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('845 Avison Way, V6G 3E2', 'Canada', 'Vancouver', 'British Columbia', 'Vancouver Aquarium', to_timestamp('2000-01-01 10:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 17:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('202-755 Burrard St, V6Z 1X6', 'Canada', 'Vancouver', 'British Columbia', 'Shabusen Yakiniku House', to_timestamp('2000-01-01 11:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 22:30:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('4445 NW Marine Dr, V6R 1B7', 'Canada', 'Vancouver', 'British Columbia', 'Locarno Beach', to_timestamp('2000-01-01 00:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('1445 E 41st Ave, V5P 1J8', 'Canada', 'Vancouver', 'British Columbia', 'East Side Craft House', to_timestamp('2000-01-01 11:30:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('1829 Quebec St, V5T 2Z3', 'Canada', 'Vancouver', 'British Columbia', 'Earnest Ice Cream', to_timestamp('2000-01-01 12:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 22:00:00', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('1253 Johnston St, V6H 3R9', 'Canada', 'Vancouver', 'British Columbia', 'Granville Island Hotel', to_timestamp('2000-01-01 00:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));
INSERT INTO Location(address, country, cityName, region, locationName, opening, closing) VALUES ('1301 Rue Rachel E, H2J 2K1', 'Canada', 'Montreal', 'Quebec', 'Auberge de la Fontaine', to_timestamp('2000-01-01 00:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS'));

INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Winnipeg Jets vs Las Vegas Golden Knights', to_timestamp('2023-11-01 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '300 Portage Ave, R3C 5S4', 99.50);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Winnipeg Jets vs Toronto Maple Leaves', to_timestamp('2023-11-02 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '300 Portage Ave, R3C 5S4', 97.50);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Winnipeg Jets vs Boston Bruins', to_timestamp('2023-11-03 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '300 Portage Ave, R3C 5S4', 99.50);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Winnipeg Jets vs Tampa Bay Lightning', to_timestamp('2023-11-04 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '300 Portage Ave, R3C 5S4', 96.50);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Winnipeg Jets vs St. Louis Blues', to_timestamp('2023-11-05 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '300 Portage Ave, R3C 5S4', 98.50);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Red Fish! Blue Fish!', to_timestamp('2023-11-03 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '845 Avison Way, V6G 3E2', 20.50);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Old Fish! New Fish!', to_timestamp('2023-11-04 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '845 Avison Way, V6G 3E2', 18.00);
INSERT INTO Event(eventName, startsAt, address, admissionPrice) VALUES ('Ritzy Party', to_timestamp('2023-11-05 19:00:00', 'YYYY-MM-DD HH24:MI:SS'), '900 Georgia St W, V6C 2W6', 250.00);


INSERT INTO Attraction(address, admissionPrice, attractionType) VALUES ('845 Avison Way, V6G 3E2',  22.50, 'Aquarium');
INSERT INTO Attraction(address, admissionPrice, attractionType) VALUES ('85 Israel Asper Way,  R3C 0L5', 25.00, 'Museum');
INSERT INTO Attraction(address, admissionPrice, attractionType) VALUES ('55 Pavilion Crescent, R3P 2N6', 0.00, 'Park');
INSERT INTO Attraction(address, admissionPrice, attractionType) VALUES ('300 Portage Ave, R3C 5S4', 0.00, 'Stadium');
INSERT INTO Attraction(address, admissionPrice, attractionType) VALUES ('4445 NW Marine Dr, V6R 1B7', 0.00, 'Beach') ;

INSERT INTO Hospitality (address, rate) VALUES ('2520 Portage Ave., R3J 3T6', 190.00);
INSERT INTO Hospitality (address, rate) VALUES ('900 Georgia St W, V6C 2W6', 560.00);
INSERT INTO Hospitality (address, rate) VALUES ('655 Burrard St, V6C 2R7', 500.00);
INSERT INTO Hospitality (address, rate) VALUES ('1301 Rue Rachel E, H2J 2K1', 240.00);
INSERT INTO Hospitality (address, rate) VALUES ('1253 Johnston St, V6H 3R9', 290.00);


INSERT INTO Dining (address, cuisine) VALUES ('1473 Main St, R2W 3V9', 'Pizza');
INSERT INTO Dining (address, cuisine) VALUES ('1721 Kenaston Blvd, R3Y 1V5', 'Mongolian');
INSERT INTO Dining (address, cuisine) VALUES ('202-755 Burrard St, V6Z 1X6', 'Japanese');
INSERT INTO Dining (address, cuisine) VALUES ('1445 E 41st Ave, V5P 1J8', 'Pub');
INSERT INTO Dining (address, cuisine) VALUES ('1829 Quebec St, V5T 2Z3', 'Ice Cream');

--- FOR SET DIVISION QUERY ---
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '1473 Main St, R2W 3V9', 'Best pizza in Winnipeg!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '1721 Kenaston Blvd, R3Y 1V5', 'Make your own stir fry is a really great concept well executed, should be more common!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '2520 Portage Ave., R3J 3T6', 'I went to a birthday party here when I was 6, who has a birthday party at a hotel?');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '85 Israel Asper Way,  R3C 0L5', 'I went when it first opened, to be honest I was underwhelmed but awareness is important anyway.');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '55 Pavilion Crescent, R3P 2N6',  'Near my childhood home! Absolutely gorgeous place.');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '300 Portage Ave, R3C 5S4', 'Pretty nice place! I don`t enjoy watching sport much though, I`d rather play!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '900 Georgia St W, V6C 2W6', 'My co-op had it`s AGM here, real ritzy.');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '655 Burrard St, V6C 2R7',  'My co-op also book some rooms here when they ran out of space.');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '845 Avison Way, V6G 3E2',  'Fishes :)');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '202-755 Burrard St, V6Z 1X6',  'Had a late night work visit to welcome someone new');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '4445 NW Marine Dr, V6R 1B7',  'Had a date here, the date was meh but the beach was lovely!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '1445 E 41st Ave, V5P 1J8',  'Fun trivia and delicious poutine!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '1829 Quebec St, V5T 2Z3', 'They had carrot cake ice cream the first time I went! Brought me back to my childhood :)');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '1253 Johnston St, V6H 3R9', 'I don`t go to many hotels, this one probably exists' );
INSERT INTO About (guideID, address, description) VALUES ('0000000000000001', '1301 Rue Rachel E, H2J 2K1', 'I don`t go to many hotels, this one probably exists, but in Montreal.');
------------------------------
INSERT INTO About (guideID, address, description) VALUES ('0000000000000002', '1721 Kenaston Blvd, R3Y 1V5', 'Delectable!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000003', '202-755 Burrard St, V6Z 1X6', 'Delicious!');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000004', '4445 NW Marine Dr, V6R 1B7', 'BEACH');
INSERT INTO About (guideID, address, description) VALUES ('0000000000000005', '1301 Rue Rachel E, H2J 2K1', 'Location-tastic!');

INSERT INTO TravelsBetween (itineraryID, departure, arrival, transportID, toAddress, fromAddress) VALUES ('0000000000000005', to_timestamp('2000-01-01 6:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 13:00:00', 'YYYY-MM-DD HH24:MI:SS'), 'A227', '4445 NW Marine Dr, V6R 1B7', '1301 Rue Rachel E, H2J 2K1');
INSERT INTO TravelsBetween (itineraryID, departure, arrival, transportID, toAddress, fromAddress) VALUES ('0000000000000006', to_timestamp('2000-01-01 10:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 10:30:00', 'YYYY-MM-DD HH24:MI:SS'), '49 Metrotown', '4445 NW Marine Dr, V6R 1B7', '900 Georgia St W, V6C 2W6');
INSERT INTO TravelsBetween (itineraryID, departure, arrival, transportID, toAddress, fromAddress) VALUES ('0000000000000006', to_timestamp('2000-01-01 11:00:0', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 11:30:00', 'YYYY-MM-DD HH24:MI:SS'), '49 Metrotown',  '900 Georgia St W, V6C 2W6', '4445 NW Marine Dr, V6R 1B7');
INSERT INTO TravelsBetween (itineraryID, departure, arrival, transportID, toAddress, fromAddress) VALUES ('0000000000000001', to_timestamp('2000-01-01 9:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 13:00:00', 'YYYY-MM-DD HH24:MI:SS'), 'A296', '4445 NW Marine Dr, V6R 1B7', '1721 Kenaston Blvd, R3Y 1V5');
INSERT INTO TravelsBetween (itineraryID, departure, arrival, transportID, toAddress, fromAddress) VALUES ('0000000000000009', to_timestamp('2000-01-01 12:00:00', 'YYYY-MM-DD HH24:MI:SS'), to_timestamp('2000-01-01 12:20:00', 'YYYY-MM-DD HH24:MI:SS'), 'Expo Line', '1829 Quebec St, V5T 2Z3', '1253 Johnston St, V6H 3R9');
