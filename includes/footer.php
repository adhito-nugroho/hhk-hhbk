    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="max-w-7xl mx-auto py-4 px-6">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    &copy; <?php echo date('Y'); ?> Sistem Informasi Pengelolaan HHK dan HHBK. All rights reserved.
                </div>
                <div class="text-sm text-gray-500">
                    Version 1.0.0
                </div>
            </div>
        </div>
    </footer>
</div>
</div>

<!-- JavaScript for sidebar toggle and user menu -->
<script>
    // Sidebar toggle functionality
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.flex-1');
        sidebar.classList.toggle('-translate-x-full');
        // Optional: Adjust main content width if sidebar is overlaid
        // mainContent.classList.toggle('md:ml-64');
    });

    document.getElementById('sidebar-close').addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
    });

    // User menu dropdown functionality
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenuDropdown = document.getElementById('user-menu-dropdown');

    if (userMenuButton) {
        userMenuButton.addEventListener('click', function() {
            userMenuDropdown.classList.toggle('hidden');
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
                userMenuDropdown.classList.add('hidden');
            }
        });
    }

    // Dropdown for Master Data in Sidebar
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const arrow = document.getElementById(dropdownId.replace('Dropdown', 'Arrow'));
        dropdown.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Initialize Select2 for all select elements
    $(document).ready(function() {
        $('select').select2();
    });

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        
        if (window.innerWidth < 768 && // Only on mobile
            !sidebar.contains(event.target) && 
            !sidebarToggle.contains(event.target) &&
            !sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>
</body>
</html>
