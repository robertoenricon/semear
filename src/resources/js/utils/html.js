const ALLOWED_HTML_TAGS = new Set(['P', 'DIV', 'BR', 'UL', 'OL', 'LI', 'STRONG', 'B', 'EM', 'I', 'U']);
const BLOCKED_HTML_TAGS = new Set(['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'EMBED', 'SVG', 'MATH', 'TEMPLATE']);

export function sanitizeRichTextHtml(html) {
    const parsed = new DOMParser().parseFromString(html || '', 'text/html');
    const clean = document.createElement('div');

    const appendSafe = (source, target) => {
        [...source.childNodes].forEach((child) => {
            if (child.nodeType === Node.TEXT_NODE) {
                target.appendChild(document.createTextNode(child.textContent || ''));
                return;
            }

            if (child.nodeType !== Node.ELEMENT_NODE || BLOCKED_HTML_TAGS.has(child.tagName)) {
                return;
            }

            if (!ALLOWED_HTML_TAGS.has(child.tagName)) {
                appendSafe(child, target);
                return;
            }

            const tagName = child.tagName === 'B'
                ? 'strong'
                : child.tagName === 'I'
                    ? 'em'
                    : child.tagName.toLowerCase();
            const clone = document.createElement(tagName);
            target.appendChild(clone);

            if (child.tagName !== 'BR') {
                appendSafe(child, clone);
            }
        });
    };

    appendSafe(parsed.body, clean);

    return clean.innerHTML;
}
