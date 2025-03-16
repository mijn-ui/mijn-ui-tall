document.addEventListener("alpine:init", () => {
    Alpine.store("sidebar", {
        isOpen: JSON.parse(localStorage.getItem("mijnuiSidebarOpen")) ?? true,
        toggle() {
            this.isOpen = !this.isOpen;
            localStorage.setItem("mijnuiSidebarOpen", this.isOpen);
        }
    });
});
