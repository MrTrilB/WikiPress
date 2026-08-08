(() => {
    const host = document.querySelector('.wikipress-admin');
    if (!host || !host.attachShadow || host.shadowRoot) return;

    const shadowRoot = host.attachShadow({ mode: 'open' });
    const shell = document.createElement('div');
    shell.className = 'wikipress-admin';

    const hostStyle = document.createElement('style');
    hostStyle.textContent = ':host { display: block; }';
    shadowRoot.appendChild(hostStyle);

    const handles = new Set([
        'wikipress-bootstrap',
        'wikipress-bootstrapsearch',
        'wikipress-admin-ui',
        'font-awesome-official',
        'font-awesome-official-v4shim'
    ]);

    const getHandle = (element) => {
        const id = element.id || '';
        return id.replace(/-(css|js)$/, '').replace(/^wp-(style|script)-/, '');
    };

    const isFontAwesomeAsset = (element) => {
        const source = `${element.id || ''} ${element.href || ''} ${element.src || ''} ${element.textContent || ''}`.toLowerCase();
        return source.includes('fontawesome') || source.includes('font-awesome') || source.includes('kit.fontawesome.com');
    };

    const injectCss = (element) => {
        if (!element.href || (!handles.has(getHandle(element)) && !isFontAwesomeAsset(element))) return;
        if ([...shadowRoot.querySelectorAll('link[rel="stylesheet"]')].some((link) => link.href === element.href)) return;

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = element.href;
        link.media = element.media || 'all';
        shadowRoot.appendChild(link);
    };

    document.querySelectorAll('link[rel="stylesheet"]').forEach(injectCss);

    while (host.firstChild) {
        shell.appendChild(host.firstChild);
    }
    shadowRoot.appendChild(shell);
    window.wikipressShadowRoot = shadowRoot;

    const injectRuntimeCss = () => {
        const css = window.FontAwesome?.dom?.css?.();
        if (!css) return;

        const existing = shadowRoot.querySelector('#fa-runtime');
        if (existing?.textContent === css) return;

        const style = existing || document.createElement('style');
        style.id = 'fa-runtime';
        style.textContent = css;
        if (!existing) shadowRoot.appendChild(style);
    };

    let renderQueued = false;
    let rendering = false;
    const renderIcons = () => {
        if (renderQueued || rendering) return;
        renderQueued = true;

        requestAnimationFrame(() => {
            renderQueued = false;
            injectRuntimeCss();
            if (!window.FontAwesome?.dom?.i2svg) return;

            rendering = true;
            Promise.resolve(window.FontAwesome.dom.i2svg({ node: shell }))
                .catch(() => {})
                .finally(() => {
                    rendering = false;
                    injectRuntimeCss();
                });
        });
    };

    renderIcons();
    document.addEventListener('DOMContentLoaded', renderIcons, { once: true });
    window.addEventListener('load', renderIcons, { once: true });

    new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType !== Node.ELEMENT_NODE) return;
                if (node.matches?.('link[rel="stylesheet"]')) injectCss(node);
                if (node.matches?.('script') && isFontAwesomeAsset(node)) node.addEventListener('load', renderIcons, { once: true });
            });
        });
    }).observe(document.documentElement, { childList: true, subtree: true });

    new MutationObserver((mutations) => {
        if (mutations.some((mutation) => [...mutation.addedNodes].some((node) => {
            return node.nodeType === Node.ELEMENT_NODE
                && (node.matches?.('i[class*="fa-"]') || node.querySelector?.('i[class*="fa-"]'));
        }))) {
            renderIcons();
        }
    }).observe(shell, { childList: true, subtree: true });
})();
