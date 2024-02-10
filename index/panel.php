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
        $_['asset'][$key]['skip'] = false;
        $_['asset'][$key]['stack'] = 20;
    }
    $script = <<<JS
(function (w, d) {
    function onChange() {
        if (!w.katex) return;
        let nodes = d.querySelectorAll('samp.katex,samp.katex-display');
        nodes.length && nodes.forEach(node => {
            w.katex.render(node.textContent, node, {
                displayMode: node.classList.contains('katex-display'),
                throwOnError: false
            });
            node.replaceWith(...node.childNodes);
        });
    }
    if (w._) {
        w._.on('change', onChange);
        w._.on('set', onChange);
    }
})(window, document);
JS;
    $style = <<<CSS
.katex-display {
  font-size: 150%;
}
CSS;
    $_['asset']['katex:script'] = [
        'link' => 'data:text/js;base64,' . To::base64($script),
        'stack' => 20.1
    ];
    $_['asset']['katex:style'] = [
        'link' => 'data:text/css;base64,' . To::base64($style),
        'stack' => 20.1
    ];
    return $_;
}, 0);