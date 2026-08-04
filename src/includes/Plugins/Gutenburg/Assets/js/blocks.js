(function (wp) {
    'use strict';

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, RangeControl, SelectControl, TextControl } = wp.components;
    const { __ } = wp.i18n;

    const controls = (name, attributes, setAttributes) => {
        const fields = [];
        if (name === 'wikipress/wiki-list') {
            fields.push(el(RangeControl, { label: __('Posts per page', 'wikipress'), value: attributes.perPage, min: 1, max: 50, onChange: (perPage) => setAttributes({ perPage }) }));
            fields.push(el(SelectControl, { label: __('Order by', 'wikipress'), value: attributes.orderby, options: [{ label: __('Date', 'wikipress'), value: 'date' }, { label: __('Title', 'wikipress'), value: 'title' }], onChange: (orderby) => setAttributes({ orderby }) }));
            fields.push(el(SelectControl, { label: __('Order', 'wikipress'), value: attributes.order, options: [{ label: 'DESC', value: 'DESC' }, { label: 'ASC', value: 'ASC' }], onChange: (order) => setAttributes({ order }) }));
        }
        if (name === 'wikipress/wiki-related') {
            fields.push(el(RangeControl, { label: __('Related posts', 'wikipress'), value: attributes.limit, min: 1, max: 12, onChange: (limit) => setAttributes({ limit }) }));
        }
        return fields.length ? el(InspectorControls, {}, el(PanelBody, { title: __('Block settings', 'wikipress'), initialOpen: true }, fields)) : null;
    };

    const register = (name, title, icon, attributes, description) => registerBlockType(name, {
        title,
        icon,
        category: 'widgets',
        description,
        attributes,
        edit: ({ attributes: current, setAttributes }) => el(Fragment, {}, controls(name, current, setAttributes), el('div', useBlockProps({ className: 'wikipress-block-placeholder' }), el('strong', {}, title), el('p', {}, __('This WikiPress block renders on the frontend.', 'wikipress')))),
        save: () => null,
    });

    register('wikipress/wiki-breadcrumbs', __('Wiki Breadcrumbs', 'wikipress'), 'admin-links', {}, __('Displays the current Wiki breadcrumb trail.', 'wikipress'));
    register('wikipress/wiki-list', __('Wiki List', 'wikipress'), 'list-view', { perPage: { type: 'number', default: 10 }, orderby: { type: 'string', default: 'date' }, order: { type: 'string', default: 'DESC' } }, __('Displays a list of Wiki posts.', 'wikipress'));
    register('wikipress/wiki-reading-time', __('Wiki Reading Time', 'wikipress'), 'clock', {}, __('Displays the estimated reading time for the current Wiki.', 'wikipress'));
    register('wikipress/wiki-related', __('Wiki Related', 'wikipress'), 'admin-post', { limit: { type: 'number', default: 5 } }, __('Displays related Wiki posts.', 'wikipress'));
    register('wikipress/wiki-toc', __('Wiki Table of Contents', 'wikipress'), 'menu', {}, __('Displays headings from the current Wiki.', 'wikipress'));
    register('wikipress/wiki-search-modal', __('Wiki Search Modal', 'wikipress'), 'search', {}, __('Displays a Wiki search modal.', 'wikipress'));
}(window.wp));
