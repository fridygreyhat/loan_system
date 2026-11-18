<?php
// Image upload endpoint (legacy). Requires login and only accepts real images.
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['fileToUpload'])) {
    http_response_code(400);
    exit('No file uploaded.');
}

try {
    $path = safe_upload($_FILES['fileToUpload'], 'uploads/images', UPLOAD_IMAGE_TYPES, 500000);
    echo 'The file has been uploaded: ' . htmlspecialchars($path);
} catch (RuntimeException $e) {
    http_response_code(422);
    echo 'Sorry, your file was not uploaded. ' . htmlspecialchars($e->getMessage());
}
