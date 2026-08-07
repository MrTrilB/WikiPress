(() => {
  const host = document.querySelector('.wikipress-admin');

  if (!host || ! host.attachShadow || host.shadowRoot) return;

  const shadowRoot = host.attachShadow({ mode: 'open' });
  const shell = document.createElement('div');
  shell.className = 'wikipress-admin';

  const hostStyle = document.createElement('style');
  hostStyle.textContent = ':host { display: block; }';
  shadowRoot.appendChild(hostStyle);

  document.querySelectorAll('link[rel="stylesheet"]').forEach((link) => {
    const href = link.href || '';
    if (!href.toLowerCase().includes('wikipress')) return;

    const shadowLink = link.cloneNode(true);
    shadowRoot.appendChild(shadowLink);
  });

  while (host.firstChild) {
    shell.appendChild(host.firstChild);
  }

  shadowRoot.appendChild(shell);
  window.wikipressShadowRoot = shadowRoot;
})();
