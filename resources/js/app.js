import './bootstrap';

/**
 * Generic drag-to-reorder behaviour for any [data-sortable] container whose
 * direct children carry [data-sortable-item] + data-sortable-id. On drop,
 * POSTs the new ordered id list to data-sortable-url. Containers can nest
 * (e.g. modules containing their own lesson/quiz list) — stopPropagation
 * plus the "dragEl belongs to this container" guard keeps inner drags from
 * also being read as outer reorders.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-sortable]').forEach((container) => {
        let dragEl = null;

        const directItem = (node) => {
            while (node && node.parentElement !== container) node = node.parentElement;
            return node;
        };

        container.addEventListener('dragstart', (event) => {
            const item = directItem(event.target);
            if (! item) return;
            dragEl = item;
            event.dataTransfer.effectAllowed = 'move';
            event.stopPropagation();
        });

        container.addEventListener('dragover', (event) => {
            if (! dragEl) return;
            event.preventDefault();
            event.stopPropagation();

            const target = directItem(event.target);
            if (! target || target === dragEl) return;

            const rect = target.getBoundingClientRect();
            const before = (event.clientY - rect.top) < rect.height / 2;
            container.insertBefore(dragEl, before ? target : target.nextSibling);
        });

        container.addEventListener('drop', (event) => {
            if (! dragEl) return;
            event.preventDefault();
            event.stopPropagation();
        });

        container.addEventListener('dragend', (event) => {
            if (! dragEl) return;
            event.stopPropagation();
            dragEl = null;

            const ids = Array.from(container.children)
                .filter((el) => el.matches('[data-sortable-item]'))
                .map((el) => el.dataset.sortableId);

            const url = container.dataset.sortableUrl;
            if (url) window.axios.post(url, { ids });
        });
    });
});
