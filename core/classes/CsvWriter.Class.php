<?php
/**
 * Boostack: CsvWriter.Class.php
 * ========================================================================
 * Copyright 2014-2024 Spagnolo Stefano
 * Licensed under MIT (https://github.com/offmania9/Boostack/blob/master/LICENSE)
 * ========================================================================
 * @author Alessio Debernardi
 * @version 4
 */

class CsvWriter
{

    const DEFAULT_DELIMITER = ",";
    const DEFAULT_LINES_OFFSET = 0;
    const DEFAULT_HEADING_LINE = -1;

    private $filePath = null;
    private $fileInstance = null;
    private $delimiter = self::DEFAULT_DELIMITER;

    public function __construct($file, $delimiter = self::DEFAULT_DELIMITER)
    {
        $path = dirname($file);
        if (!file_exists($path))
            throw new Exception("Directory not found");
        if(!is_writable($path))
            throw new Exception("Directory not writable");
        $this->filePath = $file;
        $this->delimiter = $delimiter;
    }

    private function openFile($path)
    {
        $fileHandler = fopen($path,"w");
        if($fileHandler == false) throw new Exception("Failed to open file");
        return $fileHandler;
    }

    public function writeAll($array)
    {
        $insertedRows = 0;

        foreach ($array as $row){
            if($this->writeRow($row))
               $insertedRows++;
        }
        return $insertedRows;
    }

    public function writeRow($row)
    {
        if($this->fileInstance == null)
            $this->fileInstance = $this->openFile($this->filePath);
        $result = fputcsv($this->fileInstance,$row,$this->delimiter);
        return $result;
    }


}