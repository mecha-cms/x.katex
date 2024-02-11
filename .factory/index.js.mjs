import katex from 'katex';
import renderMathInElement from './_/auto-render.mjs';

import './_/mhchem.mjs';

katex.__render = source => {
    renderMathInElement(source, {
        delimiters: [
            {left: '$$', right: '$$', display: true},
            {left: '$', right: '$', display: false},
            {left: '\\(', right: '\\)', display: false},
            {left: '\\[', right: '\\]', display: true}
        ],
        throwOnError: false
    });
};

window.addEventListener('DOMContentLoaded', () => {
    katex.__render(document.body);
}, false);

import './_/copy-tex.js';

export default katex;