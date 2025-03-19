document.addEventListener("alpine:init", () => {
    Alpine.store("sidebar", {
        isOpen: JSON.parse(localStorage.getItem("mijnuiSidebarOpen")) ?? true,
        toggle() {
            this.isOpen = !this.isOpen;
            localStorage.setItem("mijnuiSidebarOpen", this.isOpen);
        }
    });
});

console.log('dom started')
Livewire.on('perPageUpdated', (perPage) => {
    const url = new URL(window.location.href);
    console.log('PerPage updated to:', perPage);
    const params = new URLSearchParams(url.search);
    params.set('perPage', perPage); // Update the query parameter
    window.history.pushState({}, '', url.pathname + '?' + params.toString()); // Update the URL
});
