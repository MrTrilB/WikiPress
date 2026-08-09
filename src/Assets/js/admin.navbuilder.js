document.addEventListener('DOMContentLoaded', () => {
	const root = window.wikipressShadowRoot || document;

	root.querySelectorAll('[data-wikipress-menu-builder]').forEach((builder) => {
		builder.addEventListener('click', (event) => {
			const addButton = event.target.closest('[data-wikipress-nav-add]');
			const removeButton = event.target.closest('[data-wikipress-nav-remove]');
			if (addButton) {
				const list = builder.querySelector('[data-wikipress-nav-items]');
				if (!list) return;
				const item = document.createElement('li');
				item.className = 'list-group-item d-flex align-items-center justify-content-between gap-2';
				item.dataset.wikipressNavItem = addButton.dataset.wikipressNavAdd;
				item.innerHTML = `<span>${addButton.dataset.wikipressNavAddLabel || addButton.dataset.wikipressNavAdd}</span><button type="button" class="btn btn-sm btn-outline-danger" data-wikipress-nav-remove aria-label="Remove">&times;</button>`;
				list.appendChild(item);
			}
			if (removeButton) removeButton.closest('[data-wikipress-nav-item]')?.remove();
		});
	});
});
