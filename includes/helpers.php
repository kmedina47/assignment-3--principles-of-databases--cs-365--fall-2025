<?php
require_once 'config.php';

function connect() {
    try {
        return new PDO(
            "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8",
            DBUSER,
            DBPASS
        );
    } catch (PDOException $e) {
        echo '<p>Database connection error: ' . $e->getMessage() . '</p>';
        exit;
    }
}

function searchPasswords($pattern) {
    try {
        $db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pattern = "%$pattern%";

        $stmt = $db->prepare("
            SELECT Users.firstName, Users.lastName, Users.email, Users.userName,
                   Websites.siteName, Websites.URL,
                   Passwords.comment, Passwords.registered_on
            FROM Passwords
            JOIN Users ON Passwords.userID = Users.userID
            JOIN Websites ON Passwords.websiteID = Websites.websiteID
            WHERE Users.firstName LIKE :pattern
               OR Users.lastName LIKE :pattern
               OR Users.email LIKE :pattern
               OR Users.userName LIKE :pattern
               OR Websites.siteName LIKE :pattern
               OR Websites.URL LIKE :pattern
               OR Passwords.comment LIKE :pattern
        ");

        $stmt->execute(['pattern' => $pattern]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = "<table>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Site Name</th>
                            <th>URL</th>
                            <th>Comment</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>";

        if (!$results) {
            $html .= "<tr><td colspan='8' id='error'>Nothing found.</td></tr>";
        } else {
            foreach ($results as $row) {
                $html .= "<tr>
                            <td>" . htmlspecialchars($row['firstName'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['lastName'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['email'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['userName'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['siteName'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['URL'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['comment'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['registered_on'] ?? '') . "</td>
                          </tr>";
            }
        }

        $html .= "</tbody></table>";

        return $html;

    } catch (PDOException $e) {
        return "<p id='error'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

function insertPassword($siteName, $URL, $email, $userName, $password, $comment) {
    try {
        $db = connect();

        $stmt = $db->prepare("
            INSERT INTO Users (userName, email)
            VALUES (:userName, :email)
            ON DUPLICATE KEY UPDATE userID=LAST_INSERT_ID(userID)
        ");
        $stmt->execute([
            'userName' => $userName,
            'email' => $email
        ]);
        $userID = $db->lastInsertId();

        $stmt = $db->prepare("
            INSERT INTO Websites (siteName, URL)
            VALUES (:siteName, :URL)
            ON DUPLICATE KEY UPDATE websiteID=LAST_INSERT_ID(websiteID)
        ");
        $stmt->execute([
            'siteName' => $siteName,
            'URL' => $URL
        ]);
        $websiteID = $db->lastInsertId();

        $stmt = $db->prepare("
            INSERT INTO Passwords (userID, websiteID, password, comment)
            VALUES (:userID, :websiteID, AES_ENCRYPT(:password, UNHEX(SHA2('365passwordKey', 512)), UNHEX('00000000000000000000000000000000')), :comment)
        ");
        $stmt->execute([
            'userID' => $userID,
            'websiteID' => $websiteID,
            'password' => $password,
            'comment' => $comment
        ]);

    } catch (PDOException $e) {
        echo "<p>Insert error: " . $e->getMessage() . "</p>";
        exit;
    }
}

function updatePassword($userID, $websiteID, $newPassword) {
    try {
        $db = connect();

        $stmt = $db->prepare("
            UPDATE Passwords
            SET password = AES_ENCRYPT(:newPassword, UNHEX(SHA2('365passwordKey', 512)), UNHEX('00000000000000000000000000000000'))
            WHERE userID = :userID AND websiteID = :websiteID
        ");

        $stmt->execute([
            'newPassword' => $newPassword,
            'userID' => $userID,
            'websiteID' => $websiteID
        ]);

    } catch (PDOException $e) {
        echo "<p>Update error: " . $e->getMessage() . "</p>";
        exit;
    }
}

function deletePassword($userID, $websiteID) {
    try {
        $db = connect();

        $stmt = $db->prepare("
            DELETE FROM Passwords
            WHERE userID = :userID AND websiteID = :websiteID
        ");

        $stmt->execute([
            'userID' => $userID,
            'websiteID' => $websiteID
        ]);

    } catch (PDOException $e) {
        echo "<p>Delete error: " . $e->getMessage() . "</p>";
        exit;
    }
}
?>
