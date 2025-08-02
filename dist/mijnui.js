document.addEventListener("alpine:init", () => {
    Alpine.store("sidebar", {
        // Active content state (persisted in localStorage)
        activeContent: localStorage.getItem("mijnuiActiveContent") || null,

        // Sidebar open/close state (persisted in localStorage)
        isOpen: JSON.parse(localStorage.getItem("mijnuiSidebarOpen")) ?? false,


        // Toggle sidebar open/close
        toggle() {
            this.isOpen = !this.isOpen;
            localStorage.setItem("mijnuiSidebarOpen", this.isOpen);
        },

        // Set active content
        setActiveContent(contentId) {
            this.activeContent = contentId;
            localStorage.setItem("mijnuiActiveContent", contentId);

            // Open sidebar if it's closed
            if (!this.isOpen) {
                this.isOpen = true;
                localStorage.setItem("mijnuiSidebarOpen", true);
            }
        }
    });

    //darkmode
    Alpine.data('theme', () => ({
        theme: 'light',
        init() {
            this.theme = localStorage.getItem('theme') || 'light';
            if (this.theme === 'dark') {
                // Add "dark" to all elements EXCEPT those with class "constant"
                console.log(document.querySelectorAll('body *:not(.constant):not(.constant *)'))
                document.querySelectorAll('body *:not(.constant):not(.constant *)').forEach(el => {
                    el.classList.add('dark');
                });

                this.changeThemeIcon();
            }
        },
        switchTheme() {
            document.querySelectorAll('body *:not(.constant):not(.constant *)').forEach(el => {
                el.classList.toggle('dark');
            });

            this.changeThemeIcon();
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', this.theme);
        },
        changeThemeIcon() {
            document.querySelector('.dark-icon')?.classList.toggle('hidden');
            document.querySelector('.light-icon')?.classList.toggle('hidden');
        }
    }));

});

console.log('dom started')
Livewire.on('perPageUpdated', (perPage) => {
    const url = new URL(window.location.href);
    console.log('PerPage updated to:', perPage);
    const params = new URLSearchParams(url.search);
    params.set('perPage', perPage); // Update the query parameter
    window.history.pushState({}, '', url.pathname + '?' + params.toString()); // Update the URL
});
