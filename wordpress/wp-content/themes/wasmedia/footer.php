<?php

$timberContext = $GLOBALS['timberContext'];

if ( ! isset( $timberContext ) ) {
    throw new \Exception( 'Timber context not set in footer.' );
}