<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Management</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Password Manager</h1>
    </header>

    <?php
    require_once "includes/config.php";
    require_once "includes/helpers.php";

    $resultMessage = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST['clear_results'])) {
            $resultMessage = "";
        }

        if (isset($_POST['search'])) {
        $pattern = $_POST['search'];
        if ($pattern === "") {
            $resultMessage = "<p id='error'>Search query empty. Please try again.</p>";
        } else {
            $resultMessage = searchPasswords($pattern);
        }
    }

        if (isset($_POST['insert'])) {
            $siteName = $_POST['siteName'];
            $URL = $_POST['URL'];
            $email = $_POST['email'];
            $userName = $_POST['userName'];
            $password = $_POST['password'];
            $comment = $_POST['comment'];
            if ($siteName === "" || $URL === "" || $password === ""
            || $email === "" || $userName === "") {
                $resultMessage = "<p id='error'>siteName, URL, email, userName, and password are required for insertion.</p>";
            } else {
                insertPassword($siteName, $URL, $email, $userName, $password, $comment);
                $resultMessage = "<p>Insertion successful!</p>";
            }
        }

        if (isset($_POST['update'])) {
            $userID = $_POST['userID'];
            $websiteID = $_POST['websiteID'];
            $newPassword = $_POST['newPassword'];
            if ($userID === "" || $websiteID === "" || $newPassword === "") {
                $resultMessage = "<p id='error'>User ID, Website ID, and new Password are required for update.</p>";
            } else {
                updatePassword($userID, $websiteID, $newPassword);
                $resultMessage = "<p>Update successful!</p>";
            }
        }

        if (isset($_POST['delete'])) {
            $userID = $_POST['userID'];
            $websiteID = $_POST['websiteID'];
            if ($userID === "" || $websiteID === "") {
                $resultMessage = "<p id='error'>User ID and Website ID are required for deletion.</p>";
            } else {
                deletePassword($userID, $websiteID);
                $resultMessage = "<p>Deletion successful!</p>";
            }
        }
    }
    ?>

    <div id="results">
        <?php echo $resultMessage; ?>
    </div>

    <form method="post">
        <input type="submit" name="clear_results" value="Clear Results">
    </form>

    <form method="post">
        <h2>Search Passwords</h2>
        <input type="text" name="search" placeholder="Search by user, site, URL, etc.">
        <input type="submit" value="Search">
    </form>

    <form method="post">
        <h2>Insert New Password</h2>
        <input type="text" name="siteName" placeholder="Website" required>
        <input type="text" name="URL" placeholder="URL" required>
        <input type="text" name="email" placeholder="Email Address" required>
        <input type="text" name="userName" placeholder="Username" required>
        <input type="text" name="password" placeholder="Password" required>
        <textarea name="comment" placeholder="Comment"></textarea>
        <input type="submit" name="insert" value="Insert">
    </form>

    <form method="post">
        <h2>Update Password</h2>
        <input type="number" name="userID" placeholder="User ID" required>
        <input type="number" name="websiteID" placeholder="Website ID" required>
        <input type="text" name="newPassword" placeholder="New Password" required>
        <input type="submit" name="update" value="Update">
    </form>

    <form method="post">
        <h2>Delete Password</h2>
        <input type="number" name="userID" placeholder="User ID" required>
        <input type="number" name="websiteID" placeholder="Website ID" required>
        <input type="submit" name="delete" value="Delete">
    </form>
</body>
</html>
