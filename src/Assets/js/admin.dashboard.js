document.addEventListener('DOMContentLoaded', () => {
	const root = window.wikipressShadowRoot || document;
	root.querySelectorAll('[data-wikipress-count]').forEach((element) => {
		element.classList.add('wikipress-count-ready');
	});
});
