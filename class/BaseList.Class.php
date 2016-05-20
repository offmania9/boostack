<?php

abstract class BaseList implements IteratorAggregate {

    protected $items;

    /**
     * With this method you can iterate the list like an array
     * e.g. foreach($myList as $elem) ...
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    protected function isEmpty() {
        return count($this->items) == 0;
    }

    protected function add($element) {
        $this->items[] = $element;
    }

    protected function exist($key) {
        // TODO
        return true;
    }

    protected function length() {
        return count($this->items);
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