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

}

?>