document.addEventListener("alpine:init", () => {
    Alpine.store("sidebar", {
        // Active content state (persisted in localStorage)
        activeContent: (() => { try { return localStorage.getItem("mijnuiActiveContent"); } catch { return null; } })(),

        // Sidebar open/close state (persisted in localStorage)
        isOpen: (() => { try { return JSON.parse(localStorage.getItem("mijnuiSidebarOpen")) ?? false; } catch { return false; } })(),


        // Toggle sidebar open/close
        toggle() {
            this.isOpen = !this.isOpen;
            try { localStorage.setItem("mijnuiSidebarOpen", this.isOpen); } catch {}
        },

        // Set active content
        setActiveContent(contentId) {
            this.activeContent = contentId;
            try { localStorage.setItem("mijnuiActiveContent", contentId); } catch {}

            // Open sidebar if it's closed
            if (!this.isOpen) {
                this.isOpen = true;
                try { localStorage.setItem("mijnuiSidebarOpen", true); } catch {}
            }
        }
    });

    //darkmode
    Alpine.data('theme', () => ({
        theme: 'light',
        init() {
            try { this.theme = localStorage.getItem('theme') || 'light'; } catch {}
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
                this.changeThemeIcon();
            }
        },
        switchTheme() {
            document.documentElement.classList.toggle('dark');

            this.changeThemeIcon();
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            try { localStorage.setItem('theme', this.theme); } catch {}
        },
        changeThemeIcon() {
            document.querySelector('.dark-icon')?.classList.toggle('hidden');
            document.querySelector('.light-icon')?.classList.toggle('hidden');
        }
    }));

});

Livewire.on('perPageUpdated', (perPage) => {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    params.set('perPage', perPage);
    window.history.pushState({}, '', url.pathname + '?' + params.toString());
});
