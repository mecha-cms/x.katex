<?php

$z = defined('TEST') && TEST ? '.' : '.min.';

Asset::set(__DIR__ . D . 'index' . $z . 'css', 10);
Asset::set(__DIR__ . D . 'index' . $z . 'js', 10, ['defer' => true]);