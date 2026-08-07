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
    return source.includes('fontawesome') || source.includes('font-awesome') || source.includes('.fa-solid') || source.includes('.fa-regular');
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

  while (host.firstChild) {
    shell.appendChild(host.firstChild);
  }

  shadowRoot.appendChild(shell);
  window.wikipressShadowRoot = shadowRoot;
})();
