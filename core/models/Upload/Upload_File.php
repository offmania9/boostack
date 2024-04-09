<?php
namespace Core\Models\Upload;
use Core\Models\Config;
use Core\Models\Utils\Validator;
/**
 * Boostack: Upload_File.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 5.0
 */

class Upload_File
{


    private $name;

    private $type;

    private $size;

    private $tmp_name;

    private $extension;

    /**
     * Constructor for the Upload_File class.
     *
     * @param array $file The $_FILES array representing the uploaded file.
     * @throws \Exception If an error occurs during file upload.
     */
    public function __construct($file)
    {
        if ($file["error"] != UPLOAD_ERR_OK) {
            throw new \Exception("Error during file upload. Error code: " . $file["error"] . ". Error message: " . $this->errorCodeToMessage($file["error"]));
        }
        $pathInfo = pathinfo($file["name"]);
        $this->type = $file["type"];
        $this->name = $file["name"];
        $this->tmp_name = $file["tmp_name"];
        $this->size = $file["size"];
        $this->extension = isset($pathInfo["extension"]) ? strtolower($pathInfo["extension"]) : null;
    }

    /**
     * Check if the uploaded file meets the constraints.
     *
     * @return bool Returns true if the file meets the constraints, otherwise throws an \Exception.
     * @throws \Exception If the file does not meet the constraints.
     */
    public function constraints()
    {
        if ($this->size > Config::get("max_upload_filesize")) {
            throw new \Exception("File exceeds the maximum size");
        }
        if (strlen($this->name) > Config::get("max_upload_filename_length")) {
            throw new \Exception("Filename is too long");
        }
        if (!in_array($this->type, Config::get("allowed_file_upload_types"))) {
            throw new \Exception("File type is not valid");
        }
        if (!in_array($this->extension, Config::get("allowed_file_upload_extensions"))) {
            throw new \Exception("File extension is not valid");
        }
        if (!Validator::filename($this->name)) {
            throw new \Exception("Filename is not valid");
        }
        return true;
    }

    /**
     * Moves the uploaded file to the specified destination.
     *
     * @param string $path The destination path.
     * @param string $filename The filename.
     * @param int $permission The file permission.
     * @param bool $overwriteIfExist Whether to overwrite the file if it already exists.
     * @throws \Exception If an error occurs during the file move operation.
     */
    public function moveTo($path, $filename, $permission = 0755, $overwriteIfExist = false)
    {
        $destinationFullPath = $path . $filename . "." . $this->extension;
        if (!$overwriteIfExist && file_exists($destinationFullPath)) {
            throw new \Exception("File " . $destinationFullPath . " already exists");
        }
        if (move_uploaded_file($this->tmp_name, $destinationFullPath)) {
            if (is_writable($destinationFullPath)) {
                chmod($destinationFullPath, $permission);
            } else {
                throw new \Exception("File " . $this->name . " is not writable");
            }
        } else {
            throw new \Exception("Can't move uploaded file: " . $this->name);
        }
    }

    /**
     * Converts error code to error message.
     *
     * @param int $code The error code.
     * @return string The error message.
     */
    private function errorCodeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            case UPLOAD_ERR_FORM_SIZE:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            case UPLOAD_ERR_PARTIAL:
                return "The uploaded file was only partially uploaded";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION:
                return "File upload stopped by extension";
            default:
                return "Unknown upload error";
        }
    }
}
