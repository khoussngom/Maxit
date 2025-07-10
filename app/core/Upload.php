<?php

namespace App\Core;

class Upload
{
    public static function save($file, $destinationDir, $allowedTypes = ['image/jpeg', 'image/png'], $maxSize = 2097152)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Erreur lors de l\'upload.'];
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Type de fichier non autorisé.'];
        }

        if ($file['size'] > $maxSize) {
            return ['error' => 'Fichier trop volumineux.'];
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $destination = rtrim($destinationDir, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['error' => 'Impossible de sauvegarder le fichier.'];
        }

        return ['success' => true, 'filename' => $filename];
    }
}