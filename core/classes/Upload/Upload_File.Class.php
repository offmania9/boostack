<?php
/**
 * Boostack: Upload_File.Class.php
 * ========================================================================
 * Copyright 2014-2017 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Spagnolo Stefano <s.spagnolo@hotmail.it>
 * @version 3.0
 */
//require_once ("extension_mimetypes.inc.php");

class Upload_File
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var null
     */
    private $visual_name;

    /**
     * @var
     */
    private $type;

    /**
     * @var float|int
     */
    private $size;

    /**
     * @var
     */
    private $tmp_name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var
     */
    private $extension;

    /**
     * @var array
     */
    private $image_types = array(
        "image/gif",
        "image/jpeg",
        "image/pjpeg",
        "image/bmp",
        "image/png"
    );

    /**
     * Upload_File constructor.
     * @param $file
     * @param $destination_folder
     * @param bool $exitifexist
     * @param null $target_name
     * @param null $visual_name
     * @throws Exception
     */
    public function __construct($file, $destination_folder, $exitifexist = true, $target_name = null, $visual_name = null)
    {
        if ($file["error"] > 0) {
            throw new Exception("Return Error Code: : ".$file["error"]."<br />");
        } else {
            $info = pathinfo($file["name"]);
            $this->visual_name = $visual_name;
            if(empty($info["extension"])) throw new Exception("File extension not valid");
            $this->extension = $info["extension"];
            $this->name = ($target_name == null) ? $file["name"] : $target_name . "." . $this->extension;
            $this->type = $file["type"];
            $this->size = $file["size"] / 1024;
            $this->tmp_name = $file["tmp_name"];
            $this->path = $destination_folder . $this->name;

            if ($exitifexist && file_exists($this->path))
                throw new Exception("File already exist.");
            if($this->PDFconstraints($file, $info))
                $this->save();

        }
    }

    /**
     * @throws Exception
     */
    public function save(){
        if (move_uploaded_file($this->tmp_name,$this->path))
            chmod($this->path, 0755);
        else
            throw new Exception("Can't MoveUploaded file: " . $this->name);
    }

    // CHECK SU EXTENSION !!!!!
    /**
     *
     * TODO: CHECK REGEX SU FILENAME
     * check extension .php.pdf
     * token a tempo ?
     * check su filename senza caratteri strani
     * owasp per regex filename
     * file senza estensione
     * check alphanum su nome e city
     * lib per vera estensione files
     * string mime_content_type ( string $filename )
     *
     */
    public function PDFconstraints($file, $fileInfo)
    {
        if ($file["type"] !== "application/pdf")
            throw new Exception("File type not valid");
        if ($fileInfo["extension"] !== "pdf")
            throw new Exception("File extension not valid");
        if($file["size"] > Config::get("max_upload_pdf_size"))
            throw new Exception("File is too large");
        if (strlen($file["name"]) >= Config::get("max_upload_namefile_length")) {
            throw new Exception("Filename is too long");
        }
        return true;
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function constraints($file)
    {
        global $MAX_UPLOAD_IMAGE_SIZE, $MAX_UPLOAD_PDF_SIZE, $MAX_UPLOAD_NAMEFILE_LENGTH, $MAX_UPLOAD_GENERALFILE_SIZE, $mime_types;

        if (strlen($file["name"]) >= $MAX_UPLOAD_NAMEFILE_LENGTH) { // # se il nome del file � troppo lungo
            throw new Exception("File Name too long. Rename it and repeat upload. <br />");
        }
        if (in_array($file["type"], $this->image_types)) { // IS IMAGE
            if ($file["size"] > $MAX_UPLOAD_IMAGE_SIZE) // SIZE CHECK
                throw new Exception("File too large. <br />");
            return true;
        }
        /*
         * if ($file["type"] == "application/pdf"){ #
         * if($file["size"] > $MAX_UPLOAD_PDF_SIZE) #SIZE CHECK
         * throw new Exception("File too large. <br />");
         * return true;
         * }
         * if (in_array($file["type"],$mime_types)){ # ANY OTHER FILE IN LIST OF EXTENSIONS
         * if($file["size"] > $MAX_UPLOAD_GENERALFILE_SIZE) #SIZE CHECK
         * throw new Exception("File too large. > $MAX_UPLOAD_GENERALFILE_SIZE<br />");
         * return true;
         * }
         */
        throw new Exception("Unknown file. <br />" . $mime_types["" . $file["type"]] . $file["type"]);
    }

    /**
     * @return bool
     */
    public function remove()
    {
        return unlink($this->path . $this->name);
    }

    /**
     * @param $property_name
     * @return null
     */
    public function __get($property_name)
    {
        if (isset($this->$property_name))
            return ($this->$property_name);
        else
            return (NULL);
    }

    /**
     * @param $property_name
     * @param $val
     */
    public function __set($property_name, $val)
    {
        $this->$property_name = $val;
    }
}
?>