<?php

function ProcessAndSaveImage($image)
{
    $result = [
        'path' => "",
        'status' => false,
        'error' => ""
    ];

    // Handle file upload
    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $image['tmp_name'];
        $fileName = $image['name'];
        $fileSize = $image['size'];
        $fileType = $image['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../../uploads/';
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $result['path'] = $dest_path;
                $result['status'] = true;
                return $result;
            } else {
                $result['error'] = 'There was an error moving the uploaded file.';
                return $result;
            }
        } else {
            $result['error'] = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            return $result;
        }

        return $result;
    }

    $result['error'] = 'There is some error in the file upload. Please check the following error.<br>';
    $result['error'] .= 'Error:' . $image['error'];

    return $result;
}
