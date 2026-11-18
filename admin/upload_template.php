<?php
require_once '../helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_default = isset($_POST['is_default']); // bound below as 'true'/'false': PDO sends PHP false as ''
    
    if (empty($name)) {
        header("Location: templates.php?error=Name is required");
        exit;
    }
    
    if (!isset($_FILES['template_file']) || $_FILES['template_file']['error'] !== UPLOAD_ERR_OK) {
        header("Location: templates.php?error=Please upload a valid file");
        exit;
    }
    
    $file = $_FILES['template_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if ($ext !== 'docx') {
        header("Location: templates.php?error=Only DOCX files are allowed");
        exit;
    }
    
    $upload_dir = '../uploads/templates/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = 'template_' . date('YmdHis') . '_' . uniqid() . '.docx';
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        try {
            $placeholders = extractPlaceholders($filepath);
            
            if ($is_default) {
                $con->exec("UPDATE agreement_templates SET is_default = FALSE");
            }
            
            $stmt = $con->prepare("INSERT INTO agreement_templates (name, description, file_path, placeholders, is_default, is_active) VALUES (?, ?, ?, ?, ?, TRUE)");
            $stmt->execute([$name, $description, $filepath, $placeholders, $is_default ? 'true' : 'false']);
            
            header("Location: templates.php?success=1");
            exit;
        } catch (PDOException $e) {
            unlink($filepath);
            header("Location: templates.php?error=" . urlencode($e->getMessage()));
            exit;
        }
    } else {
        header("Location: templates.php?error=Failed to upload file");
        exit;
    }
} else {
    header("Location: templates.php");
    exit;
}

function extractPlaceholders($filepath) {
    $placeholders = [];
    
    $zip = new ZipArchive();
    if ($zip->open($filepath) === TRUE) {
        $content = $zip->getFromName('word/document.xml');
        $zip->close();
        
        if ($content) {
            preg_match_all('/\{([a-z_]+)\}/i', $content, $matches);
            if (!empty($matches[1])) {
                $placeholders = array_unique($matches[1]);
            }
        }
    }
    
    return implode(',', $placeholders);
}
