<?php

namespace App\Helpers;

use Exception;

class FileUploader {
    private static array $mimeMap = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png'
    ];

    public static function upload(
        array $file,
        string $subDir = 'leaves',
        array $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'],
        int $maxSizeBytes = 5242880 // 5MB
    ): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid file upload parameters.");
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file was uploaded.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("Uploaded file exceeds maximum permitted file size.");
            default:
                throw new Exception("An unknown error occurred during file upload.");
        }

        if ($file['size'] > $maxSizeBytes) {
            throw new Exception("File size cannot exceed " . round($maxSizeBytes / (1024 * 1024), 1) . "MB.");
        }

        $originalName = basename($file['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            throw new Exception("Disallowed file format. Allowed types: " . implode(', ', $allowedExtensions));
        }

        // Verify real MIME type using finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file['tmp_name']);

        $validMimes = array_map(fn($e) => self::$mimeMap[$e] ?? '', $allowedExtensions);
        if (!in_array($realMime, $validMimes, true)) {
            throw new Exception("Invalid file content MIME type ({$realMime}).");
        }

        // Generate safe randomized filename
        $safeFileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $targetDirectory = dirname(__DIR__, 2) . '/public/uploads/' . trim($subDir, '/');

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $destination = $targetDirectory . '/' . $safeFileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to move uploaded file to destination directory.");
        }

        return [
            'filename'      => $safeFileName,
            'relative_path' => 'uploads/' . trim($subDir, '/') . '/' . $safeFileName,
            'original_name' => $originalName,
            'mime_type'     => $realMime,
            'size'          => $file['size']
        ];
    }

    public static function saveBase64Image(string $base64Data, string $subDir = 'faces'): ?string {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, jpeg

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                $type = 'jpg';
            }

            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }

            $safeFileName = bin2hex(random_bytes(16)) . '.' . ($type === 'jpeg' ? 'jpg' : $type);
            $targetDirectory = dirname(__DIR__, 2) . '/public/uploads/' . trim($subDir, '/');

            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            file_put_contents($targetDirectory . '/' . $safeFileName, $data);
            return 'uploads/' . trim($subDir, '/') . '/' . $safeFileName;
        }

        return null;
    }
}
