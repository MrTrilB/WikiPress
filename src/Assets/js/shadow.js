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

    // --- Utility: detect FA assets ---
    const isFontAwesomeAsset = (element) => {
        const source = `${element.id || ''} ${element.href || ''} ${element.textContent || ''}`.toLowerCase();
        return (
            source.includes('fontawesome') ||
            source.includes('font-awesome') ||
            source.includes('font awesome') ||
            source.includes('.fa-solid') ||
            source.includes('.fa-regular') ||
            source.includes('.svg-inline--fa') ||
            source.includes('--fa-')
        );
    };

    // --- Inject CSS into shadow (deduped) ---
    const injectCss = (href, media = 'all') => {
        if (!href) return;
        if ([...shadowRoot.querySelectorAll('link[rel="stylesheet"]')].some(l => l.href === href)) return;

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.media = media;
        shadowRoot.appendChild(link);
    };

    // --- Inject FA Kit CSS ---
    const injectFontAwesomeKitCss = () => {
        document.querySelectorAll('link[rel="stylesheet"][href*="kit.fontawesome.com"]').forEach(link => {
            injectCss(link.href, link.media);
        });
    };

    // --- Inject WikiPress + WP Admin CSS (same behaviour as original) ---
    document.querySelectorAll('link[rel="stylesheet"]').forEach((link) => {
        const href = link.href || '';
        if (!href.toLowerCase().includes('wikipress') && !isFontAwesomeAsset(link)) return;
        injectCss(href, link.media);
    });

    // --- Inject FA <style> blocks (deduped) ---
    document.querySelectorAll('style').forEach((style) => {
        if (!isFontAwesomeAsset(style)) return;

        const clone = style.cloneNode(true);
        if (![...shadowRoot.querySelectorAll('style')].some(s => s.textContent === clone.textContent)) {
            shadowRoot.appendChild(clone);
        }
    });

    // --- Inject FA runtime CSS once ---
    const injectRuntimeCss = () => {
        const css = window.FontAwesome?.dom?.css?.();
        if (!css) return;

        const existing = shadowRoot.querySelector('#wikipress-fontawesome-runtime-css');
        if (existing && existing.textContent === css) return;

        const style = existing || document.createElement('style');
        style.id = 'wikipress-fontawesome-runtime-css';
        style.textContent = css;

        if (!existing) shadowRoot.appendChild(style);
    };

    // --- Render FA icons inside shadow ---
    let queued = false;
    const renderIcons = () => {
        if (queued) return;
        queued = true;

        requestAnimationFrame(() => {
            queued = false;

            injectFontAwesomeKitCss();
            injectRuntimeCss();

            if (window.FontAwesome?.dom?.i2svg) {
                window.FontAwesome.dom.i2svg({ node: shell });
                injectRuntimeCss();
            }
        });
    };

    // --- Move WP content into shadow ---
    while (host.firstChild) {
        shell.appendChild(host.firstChild);
    }
    shadowRoot.appendChild(shell);

    window.wikipressShadowRoot = shadowRoot;

    // Initial FA load
    injectFontAwesomeKitCss();
    injectRuntimeCss();
    renderIcons();

    // --- Watch only the WikiPress shell (not entire WP admin) ---
    new MutationObserver((mutations) => {
        const needsRender = mutations.some(m =>
            [...m.addedNodes].some(n =>
                n.nodeType === 1 &&
                (n.matches?.('i[class*="fa-"]') || n.querySelector?.('i[class*="fa-"]'))
            )
        );

        if (needsRender) renderIcons();
    }).observe(shell, { childList: true, subtree: true });

})();
