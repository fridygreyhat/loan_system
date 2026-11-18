<?php
/**
 * Safe file uploads.
 *
 * Never trust the client's filename or extension: the stored file gets a
 * random name and an extension derived from its real (sniffed) MIME type,
 * so nothing executable can land in a web-served directory.
 */

const UPLOAD_IMAGE_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

const UPLOAD_DOCUMENT_TYPES = UPLOAD_IMAGE_TYPES + [
    'application/pdf' => 'pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    'application/msword' => 'doc',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
    'application/vnd.ms-excel' => 'xls',
    'text/plain' => 'txt',
    'text/csv' => 'csv',
];

/**
 * Move one uploaded file into $relativeDir (relative to the app root).
 *
 * @param array  $file         one entry of $_FILES (name, tmp_name, error, size)
 * @param string $relativeDir  e.g. 'uploads/borrowers'
 * @param array  $allowedTypes MIME => extension map
 * @param int    $maxBytes
 * @return string relative path of the stored file, e.g. 'uploads/borrowers/ab12….jpg'
 * @throws RuntimeException with a user-presentable message
 */
function safe_upload(array $file, string $relativeDir, array $allowedTypes = UPLOAD_IMAGE_TYPES, int $maxBytes = 5242880): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new RuntimeException('The file was not uploaded correctly.');
    }
    if (($file['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException(sprintf('The file is too large (maximum %d MB).', $maxBytes / 1048576));
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowedTypes[$mime])) {
        throw new RuntimeException('This file type is not allowed.');
    }

    $relativeDir = trim($relativeDir, '/');
    $absDir = dirname(__DIR__) . '/' . $relativeDir;
    if (!is_dir($absDir) && !mkdir($absDir, 0755, true)) {
        throw new RuntimeException('Upload folder is not writable.');
    }

    $name = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
    if (!move_uploaded_file($file['tmp_name'], "$absDir/$name")) {
        throw new RuntimeException('Could not save the uploaded file.');
    }
    return "$relativeDir/$name";
}
