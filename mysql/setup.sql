DROP DATABASE IF EXISTS student_passwords;
CREATE DATABASE student_passwords;
USE student_passwords;

DROP USER IF EXISTS 'passwords_user'@'localhost';
CREATE USER 'passwords_user'@'localhost';
GRANT ALL PRIVILEGES ON student_passwords.* TO 'passwords_user'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS Users (
    userID INT NOT NULL AUTO_INCREMENT,
    firstName VARCHAR(64),
    lastName VARCHAR(64),
    email VARCHAR(128),
    userName VARCHAR(128),
    PRIMARY KEY (userID)
);

CREATE TABLE IF NOT EXISTS Websites (
    websiteID INT NOT NULL AUTO_INCREMENT,
    siteName VARCHAR(128),
    URL VARCHAR(256),
    PRIMARY KEY (websiteID)
);

CREATE TABLE IF NOT EXISTS Passwords (
    userID INT NOT NULL,
    websiteID INT NOT NULL,
    password VARBINARY(512) NOT NULL,
    comment VARCHAR(256),
    registered_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (userID, websiteID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (websiteID) REFERENCES Websites(websiteID) ON DELETE CASCADE
);

SET block_encryption_mode = 'aes-256-cbc';
SET @key_str = UNHEX(SHA2('365passwordKey', 512));
SET @init_vector = UNHEX('00000000000000000000000000000000');

INSERT INTO Users (firstName, lastName, email, userName) VALUES
('Charles', 'Caroll', 'Chuck123@gmail.com', 'ChucksPalace123'),
('Erick', 'Hayden', 'HaydenEnt456@gmail.com', 'HaydenWorld456'),
('Daniel', 'Bernaldo', 'Dman67@yahoo.com', 'Dman67'),
('Alex','Schultz', 'Aschultz789@yahoo.com', 'AschultzProd789'),
('Luke', 'Valentine', 'LukeV234@gmail.com', 'LV234'),
('Nick', 'Schwartzman', 'Nschwartzman123@gmail.com', 'NickSchwartz77'),
('Roger', 'Smith', 'Rsmitty44@gmail.com', 'Rsmitty44'),
('John', 'Connor', 'Jconn99@outlook.com', 'Jconn99'),
('Gregg', 'Hill', 'Ghill88@outlook.com', 'Ghill88'),
('Calvin', 'Mcphee', 'Cmcphee21@outlook.com', 'Cmcphee21');

INSERT INTO Websites (siteName, URL) VALUES
('Amazon', 'https://amazon.com'),
('Nike', 'http://nike.com'),
('Supreme', 'http://supreme.com'),
('Adidas','http://adidas.com'),
('Converse', 'http://converse.com'),
('Bass Pro Shops', 'http://basspro.com'),
('Uniqlo', 'https://uniqlo.com'),
('Wired', 'http://wired.com'),
('Home Depot', 'http://homedepot.com'),
('Github', 'http://github.com');

INSERT INTO Passwords (userID, websiteID, password, comment)
VALUES
(1, 1, AES_ENCRYPT('PaL@ce!123', @key_str, @init_vector), 'Amazon Shopping Account'),
(2, 6, AES_ENCRYPT('H@yd3rr456', @key_str, @init_vector), 'Bass Pro Shops Membership'),
(2, 9, AES_ENCRYPT('ToolT!m3456', @key_str, @init_vector), 'Home Depot Shopping Account'),
(3, 3, AES_ENCRYPT('Urb@nDam3!', @key_str, @init_vector), 'Supreme Shopping Account'),
(5, 5, AES_ENCRYPT('NV@l3321', @key_str, @init_vector), 'Converse Shopping Account'),
(6, 7, AES_ENCRYPT('T3nn1sG0d', @key_str, @init_vector), 'Uniqlo Shopping Account'),
(8, 2, AES_ENCRYPT('JCr0b0!1', @key_str, @init_vector), 'Nike Shopping Account'),
(8, 8, AES_ENCRYPT('JCT3rm$$', @key_str, @init_vector), 'Wired Magazine Subscription'),
(8, 9, AES_ENCRYPT('JCh@mm3r', @key_str, @init_vector), 'Home Depot Shopping Account'),
(10, 10, AES_ENCRYPT('MCph33Pr0g', @key_str, @init_vector), 'Github Account'),
(7, 7, AES_ENCRYPT('W1mbl3M@st!', @key_str, @init_vector), 'Uniqlo Shopping Account'),
(1, 1, AES_ENCRYPT('Chuck$Wrld!', @key_str, @init_vector), 'Amazon Updated Password')
ON DUPLICATE KEY UPDATE
password = VALUES(password),
comment = VALUES(comment),
registered_on = CURRENT_TIMESTAMP;

SELECT AES_DECRYPT(password, @key_str, @init_vector) AS decrypted_password, comment
FROM Passwords
JOIN Websites ON Passwords.websiteID = Websites.websiteID
WHERE userID = 8 AND Websites.URL = 'http://nike.com';

SELECT AES_DECRYPT(password, @key_str, @init_vector) AS decrypted_password, comment, registered_on
FROM Passwords
JOIN Websites ON Passwords.websiteID = Websites.websiteID
WHERE Websites.URL LIKE 'https%';