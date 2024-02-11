<?php

Hook::set('_', function ($_) {
    $folder = dirname(__DIR__);
    $z = defined('TEST') && TEST ? '.' : '.min.';
    foreach ([
        $folder . D . 'index' . $z . 'css',
        $folder . D . 'index' . $z . 'js'
    ] as $key) {
        if (0 === $key) {
            continue;
        }
        if (!isset($_['asset'][$key])) {
            continue;
        }
        // Do not prevent KaTeX from being loaded into the Panel
        $_['asset'][$key]['id'] = false;
        $_['asset'][$key]['skip'] = false;
    }
    return $_;
}, 0);