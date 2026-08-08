(() => {
    const host = document.querySelector('.wikipress-admin');
    if (!host || !host.attachShadow || host.shadowRoot) return;

    const shadowRoot = host.attachShadow({ mode: 'open' });
    const shell = document.createElement('div');
    shell.className = 'wikipress-admin';

    // Basic host styling
    const hostStyle = document.createElement('style');
    hostStyle.textContent = ':host { display: block; }';
    shadowRoot.appendChild(hostStyle);

    // ------------------------------------------------------------
    // 1. COLLECT ALL CSS/JS FROM YOUR WP HANDLES
    // ------------------------------------------------------------

    const WIKIPRESS_HANDLES = [
        'wikipress-bootstrap',
        'wikipress-bootstrapsearch',
        'wikipress-admin-ui',
        'wikipress-shadow',
        'font-awesome-official'
    ];

    const collectedCss = [];
    const collectedJs = [];

    // WP outputs <link> and <script> tags with handle names in id=""
    document.querySelectorAll('link[rel="stylesheet"], script').forEach(el => {
        const id = el.id || '';
        const handle = id.replace(/^wp-(style|script)-/, '');

        if (WIKIPRESS_HANDLES.includes(handle)) {
            if (el.tagName === 'LINK') collectedCss.push(el.href);
            if (el.tagName === 'SCRIPT') collectedJs.push(el.src);
        }
    });

    // ------------------------------------------------------------
    // 2. INJECT CSS INTO SHADOW ROOT (DEDUPED)
    // ------------------------------------------------------------

    const injectCss = (href) => {
        if (!href) return;
        if ([...shadowRoot.querySelectorAll('link[rel="stylesheet"]')].some(l => l.href === href)) return;

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        shadowRoot.appendChild(link);
    };

    collectedCss.forEach(injectCss);

    // ------------------------------------------------------------
    // 3. INJECT JS INTO SHADOW ROOT (DEDUPED)
    // ------------------------------------------------------------

    const injectJs = (src) => {
        if (!src) return;
        if ([...shadowRoot.querySelectorAll('script')].some(s => s.src === src)) return;

        const script = document.createElement('script');
        script.src = src;
        script.defer = true;
        shadowRoot.appendChild(script);
    };

    collectedJs.forEach(injectJs);

    // ------------------------------------------------------------
    // 4. MOVE WP CONTENT INTO SHADOW
    // ------------------------------------------------------------

    while (host.firstChild) {
        shell.appendChild(host.firstChild);
    }
    shadowRoot.appendChild(shell);

    // ------------------------------------------------------------
    // 5. FONT AWESOME KIT + RUNTIME CSS + SVG RENDERING
    // ------------------------------------------------------------

    const injectRuntimeCss = () => {
        const css = window.FontAwesome?.dom?.css?.();
        if (!css) return;

        const existing = shadowRoot.querySelector('#fa-runtime');
        if (existing && existing.textContent === css) return;

        const style = existing || document.createElement('style');
        style.id = 'fa-runtime';
        style.textContent = css;

        if (!existing) shadowRoot.appendChild(style);
    };

    let queued = false;
    const renderIcons = () => {
        if (queued) return;
        queued = true;

        requestAnimationFrame(() => {
            queued = false;

            injectRuntimeCss();

            if (window.FontAwesome?.dom?.i2svg) {
                window.FontAwesome.dom.i2svg({ node: shell });
                injectRuntimeCss();
            }
        });
    };

    // Initial render
    renderIcons();

    // Re-render only when icons appear inside the shadow
    new MutationObserver((mutations) => {
        const needsRender = mutations.some(m =>
            [...m.addedNodes].some(n =>
                n.nodeType === 1 &&
                (n.matches?.('i[class*="fa-"]') || n.querySelector?.('i[class*="fa-"]'))
            )
        );

        if (needsRender) renderIcons();
    }).observe(shell, { childList: true, subtree: true });

    window.wikipressShadowRoot = shadowRoot;
})();
