<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FaceRegistration;
use App\Models\FaceDescriptor;
use App\Models\AuditLog;
use App\Helpers\FileUploader;
use App\Helpers\Auth;
use App\Core\Database;
use Exception;
use PDO;

class FaceService {
    private static function ensureSchemaUpToDate(PDO $pdo): void {
        static $checked = false;
        if ($checked) return;

        try {
            // Check face_registrations columns
            $cols = $pdo->query("SHOW COLUMNS FROM face_registrations")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('is_active', $cols, true)) {
                $pdo->exec("ALTER TABLE face_registrations ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1");
            }
            if (!in_array('preview_image', $cols, true)) {
                $pdo->exec("ALTER TABLE face_registrations ADD COLUMN preview_image VARCHAR(255) NULL");
            }
            if (!in_array('registered_by_user_id', $cols, true)) {
                if (in_array('created_by_user_id', $cols, true)) {
                    $pdo->exec("ALTER TABLE face_registrations CHANGE COLUMN created_by_user_id registered_by_user_id INT NULL");
                } else {
                    $pdo->exec("ALTER TABLE face_registrations ADD COLUMN registered_by_user_id INT NULL");
                }
            }

            // Check face_descriptors columns
            $descCols = $pdo->query("SHOW COLUMNS FROM face_descriptors")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('brightness', $descCols, true)) {
                $pdo->exec("ALTER TABLE face_descriptors ADD COLUMN brightness FLOAT NULL");
            }
            if (!in_array('sharpness', $descCols, true)) {
                $pdo->exec("ALTER TABLE face_descriptors ADD COLUMN sharpness FLOAT NULL");
            }
            if (!in_array('face_size', $descCols, true)) {
                $pdo->exec("ALTER TABLE face_descriptors ADD COLUMN face_size FLOAT NULL");
            }
            $checked = true;
        } catch (\Throwable $e) {
            error_log("Schema auto-upgrade warning: " . $e->getMessage());
        }
    }

    public static function registerFace(
        int $studentId,
        array $descriptors,
        ?string $previewBase64 = null,
        ?int $registeredByUserId = null
    ): array {
        $student = Student::find($studentId);
        if (!$student) {
            throw new Exception("Student not found.");
        }

        if (empty($descriptors)) {
            throw new Exception("At least one facial descriptor sample is required.");
        }

        $pdo = Database::getInstance();
        self::ensureSchemaUpToDate($pdo);
        $pdo->beginTransaction();

        try {
            $isReRegistration = ((int)$student['face_registered'] === 1);
            $newVersion = (int)$student['registration_version'] + 1;

            // Deactivate previous active registrations
            $deactStmt = $pdo->prepare("UPDATE face_registrations SET is_active = 0 WHERE student_id = ?");
            $deactStmt->execute([$studentId]);

            // Save preview image if provided
            $previewPath = $student['preview_photo'];
            if (!empty($previewBase64)) {
                $saved = FileUploader::saveBase64Image($previewBase64, 'faces');
                if ($saved) {
                    $previewPath = $saved;
                }
            }

            // Create new Face Registration record
            $regStmt = $pdo->prepare("
                INSERT INTO face_registrations (student_id, version, sample_count, preview_image, registered_by_user_id, is_active)
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $regStmt->execute([
                $studentId,
                $newVersion,
                count($descriptors),
                $previewPath,
                $registeredByUserId ?: Auth::id()
            ]);
            $registrationId = (int)$pdo->lastInsertId();

            // Insert each sample descriptor
            $descStmt = $pdo->prepare("
                INSERT INTO face_descriptors (student_id, registration_id, sample_index, descriptor, brightness, sharpness, face_size)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($descriptors as $idx => $sample) {
                $descriptorData = is_string($sample['descriptor']) 
                    ? $sample['descriptor'] 
                    : json_encode($sample['descriptor'] ?? $sample);

                $brightness = isset($sample['brightness']) ? (float)$sample['brightness'] : null;
                $sharpness  = isset($sample['sharpness']) ? (float)$sample['sharpness'] : null;
                $faceSize   = isset($sample['face_size']) ? (float)$sample['face_size'] : null;

                $descStmt->execute([
                    $studentId,
                    $registrationId,
                    $idx + 1,
                    $descriptorData,
                    $brightness,
                    $sharpness,
                    $faceSize
                ]);
            }

            // Update student face registration status & version
            $stuStmt = $pdo->prepare("
                UPDATE students 
                SET face_registered = 1, registration_version = ?, preview_photo = ? 
                WHERE id = ?
            ");
            $stuStmt->execute([$newVersion, $previewPath, $studentId]);

            $action = $isReRegistration ? 'FACE_REREGISTERED' : 'FACE_REGISTERED';
            AuditLog::log($action, $registeredByUserId ?: Auth::id(), $studentId, null, $newVersion, [
                'registration_id' => $registrationId,
                'sample_count'    => count($descriptors),
                'preview_image'   => $previewPath
            ]);

            $pdo->commit();

            return [
                'success'         => true,
                'version'         => $newVersion,
                'registration_id' => $registrationId,
                'message'         => $isReRegistration 
                    ? "Face successfully re-registered (Version {$newVersion})." 
                    : "Face successfully registered (Version {$newVersion})."
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
