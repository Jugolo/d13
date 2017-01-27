<?php
// ----------------------------------------------------------------------------------------
// d13_Collection
// @
//
// ----------------------------------------------------------------------------------------

class d13_collection implements IteratorAggregate
{
    private $data = array();

    public

    function __construct($data)
    {
        $this->data = $data;
    }

    public

    function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    public

    function getAll()
    {
        return $this->data;
    }

    public

    function getByKey($key, $field)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key][$field];
        }
        else {
            return NULL;
        }
    }

    function getByID($id, $field="")
    {
        foreach($this->data as $entry) {
            if ($entry['id'] == $id) {
                if ($field = "" || !isset($entry[$field])) {
                    return $entry;
                } else {
                    return $entry[$field];
                }
            }
        }

        return NULL;
    }

    public

    function get($indices)
    {

        if (empty($indices)) {
            return $this->data;
        }

        $array = $this->data;
        if (!is_array($indices)) {
            $indices = array(
                $indices
            );
        }

        foreach($indices as $index) {
            if (isset($array[$index])) {
                $array = $array[$index];
            }
            else {
                return FALSE;
            }
        }

        if ((array)$array === $array) {
            return (array)$array;
        } else if ((string)$array === $array) {
            return (string)$array;
        } else if ((integer)$array === $array) {
            return (integer)$array;
        }

        return $array;

    }

}
