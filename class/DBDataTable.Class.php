<?php
class DataTable
{
    private $data;
    private $count;
    private $index;

    // Creates a Datatable object from a result array.
    // Expects result array to be in the format:
    //      array(array(field1Column => value1, "field 1 name" => value1 ...
    //      Example:
    //          array(array(0 => "Documento", "last_name" => "Documento", ...
    function __construct($result)
    {
        $this->data = $result;
        $this->count = count($result);
        $this->index = -1;
    }

    // Returns an array of field values of the row pointed to by index.
    function Row($index)
    {
        if ($index < 0 || $index >= $this->count)
            throw new Exception("Row index ($index) is out of bounds");
        return $this->data[$index];
    }

    // Returns the number of rows in the Datatable.
    function Count()
    {
        return $this->count;
    }

    // Returns true if the row pointer is at the location
    // before the first record (beginning-of-file).
    function BOF()
    {
        return $this->index == -1;
    }

    // Returns true if the row pointer is at the location
    // after the last record (end-of-file).
    function EOF()
    {
        return $this->index == $this->count;
    }

    // Moves the row pointer to the first row.
    function MoveFirst()
    {
        if ($this->count)
        {
            $this->index = 0;
            return true;
        }
        else
        {
            $this->index = -1;
            return false;
        }
    }

    // Moves the row pointer to the next row.
    function MoveNext()
    {
        if ($this->index < $this->count - 1)
        {
            $this->index++;
            return true;
        }
        else
        {
            $this->index = $this->count;
            return false;
        }
    }

    // Moves the row pointer to the previous row.
    function MovePrevious()
    {
        if ($this->index > 0)
        {
            $this->index--;
            return true;
        }
        else
        {
            $this->index = -1;
            return false;
        }
    }

    // Moves the row pointer to the last row.
    function MoveLast()
    {
        if ($this->count)
        {
            $this->index = $this->count - 1;
            return true;
        }
        else
        {
            $this->index = 0;
            return false;
        }
    }

    // Accessor method: allows field values to be accessed as properties
    // using field names. For example:
    //      $datatable->Price
    //      $dt->last_name
    function __get($member)
    {
        if ($this->index < 0 || $this->index >= $this->count)
            throw new Exception("Row index ($this->index) is out of bounds");

        $row = $this->data[$this->index];
        if (!array_key_exists($member, $row))
            throw new Exception("Column ($member) does not exist");
        else
            return $row[$member];
    }

    // Overloaded method Field() returns field value
    // 1st form: Field(columnIndex)
    // 2nd form: Field(fieldName)
    // 3rd form: Field(rowIndex, columnIndex)
    // 4th form: Field(rowIndex, fieldName)
    function __call($method, $param)
    {
        if ($method == "Field")
        {
            switch (count($param))
            {
            case 0:
                throw new Exception("Method Field() does not accept 0 arguments");
            case 1:
                $index = $this->index;
                $col = $param[0];
                break;
            default:
                $index = $param[0];
                $col = $param[1];
                break;
            }

            $row = $this->Row($index);
            if (!array_key_exists($col, $row))
                throw new Exception("Column ($col) does not exist");
            else
                return $row[$col];
        }
    }
};

?>