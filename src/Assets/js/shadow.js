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

    // --- Inject required CSS files ---
    const injectCss = (href) => {
        if (!href) return;
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        shadowRoot.appendChild(link);
    };

    // 1. Your plugin CSS
    injectCss(wikipressSettingsTabs?.adminCssUrl);

    // 2. WordPress admin base styles
    document.querySelectorAll('link[href*="/wp-admin/"], link[href*="dashicons"]').forEach(link => {
        injectCss(link.href);
    });

    // 3. Bootstrap CSS (if used)
    const bootstrapCss = document.querySelector('link[href*="bootstrap"]');
    if (bootstrapCss) injectCss(bootstrapCss.href);

    // 4. Font Awesome Kit CSS
    const kitLink = document.querySelector('link[href*="kit.fontawesome.com"]');
    if (kitLink) injectCss(kitLink.href);

    // Move WP content into shadow
    while (host.firstChild) {
        shell.appendChild(host.firstChild);
    }
    shadowRoot.appendChild(shell);

    // --- Load FA runtime CSS once ---
    const injectRuntimeCss = () => {
        const css = window.FontAwesome?.dom?.css?.();
        if (!css || shadowRoot.querySelector('#fa-runtime')) return;

        const style = document.createElement('style');
        style.id = 'fa-runtime';
        style.textContent = css;
        shadowRoot.appendChild(style);
    };

    // --- Render icons inside shadow ---
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

    // Re-render only when icons are added inside the shadow
    new MutationObserver((mutations) => {
        if (mutations.some(m =>
            [...m.addedNodes].some(n =>
                n.nodeType === 1 &&
                (n.matches?.('i[class*="fa-"]') || n.querySelector?.('i[class*="fa-"]'))
            )
        )) {
            renderIcons();
        }
    }).observe(shell, { childList: true, subtree: true });

    window.wikipressShadowRoot = shadowRoot;
})();
