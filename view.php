<?php
// Establish database connection
$db = new mysqli('localhost', 'shahriar_image', '&GqgS&UTw58h', 'shahriar_image');

// Check if the unique ID is set
if (isset($_GET['id'])) {
    $uniqueId = $_GET['id'];
    // Prepare SQL statement to select the photo with the given unique ID
    $stmt = $db->prepare("SELECT * FROM photos WHERE unique_id = ?");
    $stmt->bind_param("s", $uniqueId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if the photo exists and the current views are less than the view limit
    if ($row && $row['current_views'] < $row['view_limit']) {
        // Increment the current views count
        $newViewCount = $row['current_views'] + 1;
        $updateStmt = $db->prepare("UPDATE photos SET current_views = ? WHERE unique_id = ?");
        $updateStmt->bind_param("is", $newViewCount, $uniqueId);
        $updateStmt->execute();

        // Serve the HTML content
        header('Content-Type: text/html');
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>View Image</title>
    <style>
        /* Prevent text and image selection */
        body {
            -webkit-user-select: none; /* Safari */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* IE10+/Edge */
            user-select: none; /* Standard */
        }
        img {
            pointer-events: none; /* Prevent dragging images */
        }
    </style>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', event => event.preventDefault());

        // JavaScript to refresh the page after a specified duration
        setTimeout(function() {
            // Redirect to an expired message page or any other logic you wish to implement
            window.location = 'expired.php';
        }, " . (int)$row['timer'] . " * 1000); // Convert timer to milliseconds
    </script>
</head>
<body>
    <img src='" . htmlspecialchars($row['file_path']) . "' alt='Image' style='max-width:100%; display:block; margin: 0 auto;'>
    <p></p>
</body>
</html>";
    } else {
        // If view limit is reached or photo not found, serve a 404 error
        http_response_code(404);
        echo "Photo not found or view limit reached.";
    }
} else {
    echo "Invalid request.";
}
?>
