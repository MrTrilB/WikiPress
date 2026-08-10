(() => {
    const host = document.querySelector('.wikipress-admin');
    if (!host || !host.attachShadow || host.shadowRoot) return;

    const shadowRoot = host.attachShadow({ mode: 'open' });
    const shell = document.createElement('div');
    shell.className = 'wikipress-admin';

    const hostStyle = document.createElement('style');
    hostStyle.textContent = ':host { display: block; }';
    shadowRoot.appendChild(hostStyle);

    const getHandle = (element) => {
        const id = element.id || '';
        return id.replace(/-(css|js)$/, '').replace(/^wp-(style|script)-/, '');
    };

    const isFontAwesomeAsset = (element) => {
        const source = `${element.id || ''} ${element.href || ''} ${element.src || ''} ${element.textContent || ''}`.toLowerCase();
        return source.includes('fontawesome') || source.includes('font-awesome') || source.includes('kit.fontawesome.com');
    };

    const getAssetPromise = (element) => element.__wikipressShadowPromise || Promise.resolve();

    const injectFontAwesomeKit = () => {
        const url = window.wikipressFontAwesomeKit?.url;
        if (!url || [...shadowRoot.querySelectorAll('script[data-wikipress-fontawesome-kit]')].some((script) => script.src === url)) return Promise.resolve();

        const script = document.createElement('script');
        script.src = url;
        script.async = false;
        script.dataset.wikipressFontawesomeKit = 'true';
        script.__wikipressShadowPromise = new Promise((resolve) => {
            script.addEventListener('load', resolve, { once: true });
            script.addEventListener('error', resolve, { once: true });
        });
        shadowRoot.appendChild(script);
        return script.__wikipressShadowPromise;
    };

    const injectCss = (element) => {
        const handle = getHandle(element);
        if (!element.href || (!handle.startsWith('wikipress-') && !isFontAwesomeAsset(element))) return Promise.resolve();
        const existing = [...shadowRoot.querySelectorAll('link[rel="stylesheet"]')].find((link) => link.href === element.href);
        if (existing) return getAssetPromise(existing);

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = element.href;
        link.media = element.media || 'all';
        link.__wikipressShadowPromise = new Promise((resolve) => {
            link.addEventListener('load', resolve, { once: true });
            link.addEventListener('error', resolve, { once: true });
        });
        shadowRoot.appendChild(link);
        return link.__wikipressShadowPromise;
    };

    const injectScriptMirror = (element) => {
        const handle = getHandle(element);
        if (!element.src || !handle.startsWith('wikipress-') || isFontAwesomeAsset(element)) return Promise.resolve();
        const existing = [...shadowRoot.querySelectorAll('script[data-wikipress-shadow-asset]')].find((script) => script.src === element.src);
        if (existing) return getAssetPromise(existing);

        const script = document.createElement('script');
        script.src = element.src;
        script.async = false;
        script.dataset.wikipressShadowAsset = 'true';
        script.__wikipressShadowPromise = new Promise((resolve) => {
            script.addEventListener('load', resolve, { once: true });
            script.addEventListener('error', resolve, { once: true });
        });
        shadowRoot.appendChild(script);
        return script.__wikipressShadowPromise;
    };

    const isPriorityAsset = (element) => {
        const handle = getHandle(element);
        return handle === 'wikipress-bootstrap' || handle === 'wikipress-bootstrap-select';
    };

    const loadInitialAssets = async () => {
        await injectFontAwesomeKit();

        const styles = [...document.querySelectorAll('link[rel="stylesheet"]')];
        const scripts = [...document.querySelectorAll('script[src]')];
        const priorityStyles = styles.filter(isPriorityAsset);
        const priorityScripts = scripts.filter(isPriorityAsset);
        const otherStyles = styles.filter((element) => !isPriorityAsset(element));
        const otherScripts = scripts.filter((element) => !isPriorityAsset(element));

        await Promise.all(priorityStyles.map(injectCss));
        for (const script of priorityScripts) {
            await injectScriptMirror(script);
        }

        otherStyles.forEach(injectCss);
        for (const script of otherScripts) {
            await injectScriptMirror(script);
        }
    };

    while (host.firstChild) {
        shell.appendChild(host.firstChild);
    }
    shadowRoot.appendChild(shell);
    window.wikipressShadowRoot = shadowRoot;
    const initialAssetsReady = loadInitialAssets();

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
                if (node.matches?.('script[src]')) {
                    initialAssetsReady.then(() => injectScriptMirror(node));
                    if (isFontAwesomeAsset(node)) node.addEventListener('load', renderIcons, { once: true });
                }
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
