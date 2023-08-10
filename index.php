<?php

function dump($data, $die=true){
    echo '<pre>';
    var_dump($data);
    echo '</pre>';

    if($die) die();
}

function validateAltText($altText) {
    if (empty($altText)) {
        return "Alternate text cannot be empty.";
    }
    return "";
}

function validateFileType($fileType) {
    $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
    if (!in_array($fileType, $allowedTypes)) {
        return "Invalid file type. It must be a JPEG, PNG, or GIF image.";
    }
    return "";
}

function validateFileSize($fileSize, $maxSize) {
    if ($fileSize > $maxSize) {
        return "File size exceeds the allowed limit.";
    }
    return "";
}

function generateDateCode() {
    return date('dmY');
}

function uploadFile($file) {
    $uploadDir = 'uploads/';
    $fileName = generateDateCode() . '_' . uniqid() . '_' . $file['name'];
    // $fileName = $file['name'];
    $filePath = $uploadDir . $fileName;

    if ( !is_dir( $uploadDir ) ) {
        mkdir( $uploadDir );
    }

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $filePath;
    } else {
        return false;
    }
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tazkiya', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $altText = $_POST['alt_text'];
    $image = $_FILES['image'];

    $altTextError = validateAltText($altText);
    $fileTypeError = validateFileType($image['type']);
    
    $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
    $fileSizeError = validateFileSize($image['size'], $maxFileSize);

    if (empty($altTextError) && empty($fileTypeError) && empty($fileSizeError)) {
        $uploadedFilePath = uploadFile($image);

        if ($uploadedFilePath) {
            $insertQuery = "INSERT INTO images (path, alt_text) VALUES (?, ?)";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute([$uploadedFilePath, $altText]);

            header('Content-Type: application/json');
            http_response_code(201);
            echo json_encode([
                'message' => 'Upload successful.',
                'image_path' => $uploadedFilePath,
                'alt_text' => $altText
            ]);
            exit();
        } else {
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode(['error' => 'Upload failed.']);
            exit();
        }
    } else {
        header('Content-Type: application/json');
        http_response_code(422);
        echo json_encode([
            'error' => 'Validation failed.',
            'alt_text_error' => $altTextError,
            'file_type_error' => $fileTypeError,
            'file_size_error' => $fileSizeError
        ]);
        exit();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Image Upload Form</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <label for="image">Choose Image:</label>
        <input type="file" name="image" accept="image/*" required>
        <br>
        <label for="alt_text">Alternate Text:</label>
        <input type="text" name="alt_text" required>
        <br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
