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

    // Move WP content into shadow
    while (host.firstChild) {
        shell.appendChild(host.firstChild);
    }
    shadowRoot.appendChild(shell);

    // --- Load Font Awesome Kit CSS once ---
    const kitLink = document.querySelector('link[href*="kit.fontawesome.com"]');
    if (kitLink) {
        const clone = document.createElement('link');
        clone.rel = 'stylesheet';
        clone.href = kitLink.href;
        shadowRoot.appendChild(clone);
    }

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
