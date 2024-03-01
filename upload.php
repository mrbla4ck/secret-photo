<?php
$db = new mysqli('localhost', 'shahriar_image', '&GqgS&UTw58h', 'shahriar_image');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo']) && isset($_POST['viewLimit']) && isset($_POST['timer'])) {
    $targetDirectory = "uploads/";
    $filename = time() . '_' . basename($_FILES['photo']['name']);
    $targetFile = $targetDirectory . $filename;
    $viewLimit = $_POST['viewLimit'];
    $timer = $_POST['timer'];
    $uniqueId = bin2hex(random_bytes(5));

    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $stmt = $db->prepare("INSERT INTO photos (file_path, view_limit, unique_id, timer) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $targetFile, $viewLimit, $uniqueId, $timer);
        $stmt->execute();

        $shareableLink = "https://" . $_SERVER['HTTP_HOST'] . "/view.php?id=$uniqueId";
        $message = "File uploaded successfully. Shareable link: <input id='shareableLink' value='$shareableLink' readonly><button onclick='copyLink()'>Copy</button>";
    } else {
        $message = "Sorry, there was an error uploading your file.";
    }
} else {
    $message = "Invalid request.";
}

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Upload Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .message {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            display: inline-block;
            margin: auto;
        }
        input#shareableLink {
            margin-top: 10px;
        }
    </style>
    <script>
        function copyLink() {
            /* Get the text field */
            var copyText = document.getElementById('shareableLink');

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            /* Copy the text inside the text field */
            navigator.clipboard.writeText(copyText.value);
            
            /* Alert the copied text */
            alert('Copied: ' + copyText.value);
        }
    </script>
</head>
<body>
    <div class='message'>$message</div>
</body>
</html>";
?>
