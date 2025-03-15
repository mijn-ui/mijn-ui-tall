document.addEventListener("alpine:init", () => {
    // Initialize sidebar
    Alpine.store("sidebar", {
        isOpen: JSON.parse(localStorage.getItem("mijnuiSidebarOpen")) ?? true,
        toggle() {
            this.isOpen = !this.isOpen;
            localStorage.setItem("mijnuiSidebarOpen", this.isOpen);
        }
    });

    // Check if the @mijnuiAppearance directive is present
    const mijnuiAppearanceDirective = document.querySelector('script[data-mijnui-appearance]');

    if (!mijnuiAppearanceDirective) {
        localStorage.removeItem("mijnui.appearance");
    }

});
