(() => {
  const host = document.querySelector('.wikipress-admin');

  if (!host || !host.attachShadow || host.shadowRoot) return;

  const shadowRoot = host.attachShadow({ mode: 'open' });
  const shell = document.createElement('div');
  shell.className = 'wikipress-admin';

  const hostStyle = document.createElement('style');
  hostStyle.textContent = ':host { display: block; }';
  shadowRoot.appendChild(hostStyle);

  const isFontAwesomeAsset = (element) => {
    const source = `${element.id || ''} ${element.href || ''} ${element.textContent || ''}`.toLowerCase();
    return source.includes('fontawesome')
      || source.includes('font-awesome')
      || source.includes('font awesome')
      || source.includes('.fa-solid')
      || source.includes('.fa-regular')
      || source.includes('.svg-inline--fa')
      || source.includes('--fa-');
  };

  const injectFontAwesomeLink = (link) => {
    if (!link?.href || Array.from(shadowRoot.querySelectorAll('link[rel="stylesheet"]')).some((shadowLink) => shadowLink.href === link.href)) return;

    const shadowLink = document.createElement('link');
    shadowLink.rel = 'stylesheet';
    shadowLink.href = link.href;
    shadowLink.media = link.media || 'all';
    shadowRoot.appendChild(shadowLink);
  };

  const injectFontAwesomeKitCss = () => {
    document.querySelectorAll('link[rel="stylesheet"][href*="kit.fontawesome.com"]').forEach(injectFontAwesomeLink);
  };

  document.querySelectorAll('link[rel="stylesheet"]').forEach((link) => {
    const href = link.href || '';
    if (!href.toLowerCase().includes('wikipress') && !isFontAwesomeAsset(link)) return;

    injectFontAwesomeLink(link);
  });

  document.querySelectorAll('style').forEach((style) => {
    if (!isFontAwesomeAsset(style)) return;

    shadowRoot.appendChild(style.cloneNode(true));
  });

  const copyFontAwesomeStyle = (element) => {
    if (shadowRoot.contains(element) || !isFontAwesomeAsset(element)) return;
    if (element.matches?.('link[rel="stylesheet"]')) {
      injectFontAwesomeLink(element);
      return;
    }

    shadowRoot.appendChild(element.cloneNode(true));
  };

  const copyFontAwesomeRuntimeCss = () => {
    const css = window.FontAwesome?.dom?.css?.();
    if (!css || shadowRoot.querySelector('#wikipress-fontawesome-runtime-css')) return;

    const style = document.createElement('style');
    style.id = 'wikipress-fontawesome-runtime-css';
    style.textContent = css;
    shadowRoot.appendChild(style);
  };

  const isFontAwesomeScript = (element) => {
    const source = `${element.id || ''} ${element.src || ''}`.toLowerCase();
    return source.includes('fontawesome') || source.includes('font-awesome');
  };

  const watchFontAwesomeScript = (script) => {
    if (!isFontAwesomeScript(script)) return;
    script.addEventListener('load', () => queueFontAwesomeRender(), { once: true });
  };

  document.querySelectorAll('script').forEach(watchFontAwesomeScript);

  new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType !== Node.ELEMENT_NODE) return;
        if (node.matches?.('link[rel="stylesheet"], style')) copyFontAwesomeStyle(node);
        if (node.matches?.('script')) watchFontAwesomeScript(node);
      });
    });
  }).observe(document.documentElement, { childList: true, subtree: true });

  let renderingFontAwesome = false;
  let renderQueued = false;

  const renderFontAwesomeInShadow = () => {
    injectFontAwesomeKitCss();
    copyFontAwesomeRuntimeCss();

    if (renderingFontAwesome || !window.FontAwesome?.dom?.i2svg) return;

    renderingFontAwesome = true;
    Promise.resolve(window.FontAwesome.dom.i2svg({ node: shell }))
      .catch(() => {})
      .finally(() => {
        renderingFontAwesome = false;
        copyFontAwesomeRuntimeCss();
      });
  };

  const queueFontAwesomeRender = () => {
    if (renderQueued) return;

    renderQueued = true;
    window.requestAnimationFrame(() => {
      renderQueued = false;
      renderFontAwesomeInShadow();
    });
  };

  while (host.firstChild) {
    shell.appendChild(host.firstChild);
  }

  shadowRoot.appendChild(shell);
  window.wikipressShadowRoot = shadowRoot;

  injectFontAwesomeKitCss();
  copyFontAwesomeRuntimeCss();
  queueFontAwesomeRender();

  document.addEventListener('DOMContentLoaded', queueFontAwesomeRender, { once: true });
  window.addEventListener('load', queueFontAwesomeRender, { once: true });

  new MutationObserver((mutations) => {
    if (mutations.some((mutation) => Array.from(mutation.addedNodes).some((node) => {
      return node.nodeType === Node.ELEMENT_NODE
        && (node.matches?.('i[class*="fa-"]') || node.querySelector?.('i[class*="fa-"]'));
    }))) {
      queueFontAwesomeRender();
    }
  }).observe(shell, { childList: true, subtree: true });
})();