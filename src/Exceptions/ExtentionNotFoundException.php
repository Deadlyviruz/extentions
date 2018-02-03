<?php

namespace Deadlyviruz\Extentions\Exceptions;


class ExtentionNotFoundException extends \Exception {

    /**
     * ExtentionNotFoundException constructor.
     */
    public function __construct( $slug ) {
        parent::__construct('Extetion with slug name [' . $slug . '] not found');
    }
}