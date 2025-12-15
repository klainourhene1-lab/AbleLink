// Pagination Helper - Call this after loading events into any grid
// This ensures pagination is updated whenever the grid content changes

// Override the original grid update to include pagination
(function () {
    // Store original innerHTML setter
    const originalInnerHTMLDescriptor = Object.getOwnPropertyDescriptor(Element.prototype, 'innerHTML');

    // Define new innerHTML setter that triggers pagination
    Object.defineProperty(Element.prototype, 'innerHTML', {
        set: function (value) {
            // Call original setter
            originalInnerHTMLDescriptor.set.call(this, value);

            // If this is one of our event grids, update pagination
            if (this.id === 'eventsGrid' ||
                this.id === 'eventsToEvaluateGrid' ||
                this.id === 'myEvaluationsGrid') {

                // Wait a bit for DOM to update, then trigger pagination
                setTimeout(() => {
                    if (typeof window.updatePagination === 'function') {
                        window.updatePagination(this.id);
                    }
                }, 100);
            }
        },
        get: originalInnerHTMLDescriptor.get
    });
})();

// Also provide a manual trigger function
window.triggerPaginationUpdate = function (gridId) {
    if (typeof window.updatePagination === 'function') {
        window.updatePagination(gridId);
    }
};

// Trigger pagination on page load for any pre-existing content
window.addEventListener('load', function () {
    setTimeout(() => {
        ['eventsGrid', 'eventsToEvaluateGrid', 'myEvaluationsGrid'].forEach(gridId => {
            const grid = document.getElementById(gridId);
            if (grid && grid.children.length > 0) {
                if (typeof window.updatePagination === 'function') {
                    window.updatePagination(gridId);
                }
            }
        });
    }, 500);
});
