(() => {
  const host = document.querySelector('.wikipress-admin');

  if (!host || ! host.attachShadow || host.shadowRoot) return;

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

  document.querySelectorAll('link[rel="stylesheet"]').forEach((link) => {
    const href = link.href || '';
    if (!href.toLowerCase().includes('wikipress') && !isFontAwesomeAsset(link)) return;

    const shadowLink = link.cloneNode(true);
    shadowRoot.appendChild(shadowLink);
  });

  document.querySelectorAll('style').forEach((style) => {
    if (!isFontAwesomeAsset(style)) return;

    shadowRoot.appendChild(style.cloneNode(true));
  });

  const copyFontAwesomeStyle = (element) => {
    if (shadowRoot.contains(element) || !isFontAwesomeAsset(element)) return;
    shadowRoot.appendChild(element.cloneNode(true));
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
  }).observe(document.head, { childList: true });

  let renderingFontAwesome = false;
  let renderQueued = false;

  const renderFontAwesomeInShadow = () => {
    if (renderingFontAwesome || !window.FontAwesome?.dom?.i2svg) return;

    renderingFontAwesome = true;
    Promise.resolve(window.FontAwesome.dom.i2svg({ node: shadowRoot }))
      .catch(() => {})
      .finally(() => {
        renderingFontAwesome = false;
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
  queueFontAwesomeRender();

  new MutationObserver((mutations) => {
    if (mutations.some((mutation) => Array.from(mutation.addedNodes).some((node) => {
      return node.nodeType === Node.ELEMENT_NODE
        && (node.matches?.('i[class*="fa-"]') || node.querySelector?.('i[class*="fa-"]'));
    }))) {
      queueFontAwesomeRender();
    }
  }).observe(shell, { childList: true, subtree: true });
})();
