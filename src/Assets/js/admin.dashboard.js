document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('[data-wikipress-count]').forEach((element) => {
		element.classList.add('wikipress-count-ready');
	});
});
