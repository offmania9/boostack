<?php

abstract class BaseList implements IteratorAggregate, JsonSerializable {

    protected $items;

    /**
     * With this method you can iterate the list like an array
     * e.g. foreach($myList as $elem) ...
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    public function size() {
        return count($this->items);
    }

    protected function isEmpty() {
        return count($this->items) == 0;
    }

    protected function add($element) {
        $this->items[] = $element;
    }

    /**
     * This method is used when json_encode() is called
     * It expose "items" to the json_encode() function
     */
    public function jsonSerialize() {
        return $this->items;
    }

    protected function exist($key) {
        // TODO
        return true;
    }

    protected function remove($key, $shift = true) {
        // TODO
        return true;
    }

    protected function get($key) {
        return $this->items[$key];
    }

    protected function find($field,$value) {
        // TODO
        return true;
    }


}

?>