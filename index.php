<?php namespace x\katex;

function from($value) {
    $chops = [];
    while (false !== ($chop = \strpbrk($value, '$' . "\\"))) {
        if ("" !== ($v = \substr($value, 0, \strlen($value) - \strlen($chop)))) {
            $chops[] = $v;
            $value = \substr($value, \strlen($v));
        }
        if (0 === \strpos($chop, '$') && \preg_match('/^([$]{1,2})(?>\\\$|[^$])+\1/', $chop, $m)) {
            if ('$$' === \substr($m[0], -2)) {
                $chops[] = '<samp class="katex-display">' . \trim(\substr($m[0], 2, -2)) . '</samp>';
            } else {
                $chops[] = '<samp class="katex">' . \trim(\substr($m[0], 1, -1)) . '</samp>';
            }
            $value = \substr($value, \strlen($m[0]));
            continue;
        }
        if (0 === \strpos($chop, "\\(") && \preg_match('/\\\\[(][\s\S]+?\\\\[)]/', $chop, $m)) {
            $chops[] = '<samp class="katex">' . \trim(\substr($m[0], 2, -2)) . '</samp>';
            $value = \substr($value, \strlen($m[0]));
            continue;
        }
        if (0 === \strpos($chop, "\\[") && \preg_match('/\\\\[[][\s\S]+?\\\\[]]/', $chop, $m)) {
            $chops[] = '<samp class="katex-display">' . \trim(\substr($m[0], 2, -2)) . '</samp>';
            $value = \substr($value, \strlen($m[0]));
            continue;
        }
        $chops[] = $chop;
        $value = \substr($value, \strlen($chop));
    }
    if ("" !== $value) {
        $chops[] = $value;
    }
    return \implode("", $chops);
}

function page__content($content) {
    if (!$content || (
        false === \strpos($content, "\\(") &&
        false === \strpos($content, "\\[") &&
        false === \strpos($content, '$')
    )) {
        return $content;
    }
    $x = '(?>\s(?>"[^"]"|\'[^\']\'|[^>])*)?>[\s\S]*?<\/';
    $parts = \preg_split('/(' . \implode('|', [
        '<pre' . $x . 'pre>', // Capture `<pre>…</pre>` before `<code>…</code>`
        '<code' . $x . 'code>',
        '<script' . $x . 'script>',
        '<style' . $x . 'style>',
        '<textarea' . $x . 'textarea>',
        '<(?>"[^"]*"|\'[^\']*\'|[^>])+>'
    ]) . ')/', $content, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
    $content = "";
    foreach ($parts as $part) {
        if ("" === \trim($part)) {
            $content .= $part;
            continue;
        }
        if ('<' === $part[0] && '>' === \substr($part, -1)) {
            $content .= $part;
            continue;
        }
        $content .= from($part);
    }
    return $content;
}

function page__description($description) {
    return \fire(__NAMESPACE__ . "\\page__content", [$description], $this);
}

function page__title($title) {
    return \fire(__NAMESPACE__ . "\\page__content", [$title], $this);
}

$z = \defined("\\TEST") && \TEST ? '.' : '.min.';
\Asset::set(__DIR__ . \D . 'index' . $z . 'css', 10);
\Asset::set(__DIR__ . \D . 'index' . $z . 'js', 10, ['defer' => true]);

\Asset::set(__DIR__ . \D . 'index' . \D . 'copy-tex' . $z . 'js', 10.1, ['defer' => true]);
\Asset::set(__DIR__ . \D . 'index' . \D . 'mhchem' . $z . 'js', 10.1, ['defer' => true]);

$script = <<<JS
!function(e,t){e.addEventListener("DOMContentLoaded",function(){if(!e.katex)return;let n=t.querySelectorAll("samp.katex,samp.katex-display");n.length&&n.forEach(t=>{e.katex.render(t.textContent,t,{displayMode:t.classList.contains("katex-display"),throwOnError:!1}),t.replaceWith(...t.childNodes)})},!1)}(window,document);
JS;

$style = <<<CSS
.katex-display{font-size:150%}
CSS;

\Asset::set($script = 'data:text/js;base64,' . \To::base64($script), 10.2);
\Asset::set($style = 'data:text/css;base64,' . \To::base64($style), 10.2);

\Hook::set('page.content', __NAMESPACE__ . "\\page__content", 2.1);
\Hook::set('page.description', __NAMESPACE__ . "\\page__description", 2.1);
\Hook::set('page.title', __NAMESPACE__ . "\\page__title", 2.1);