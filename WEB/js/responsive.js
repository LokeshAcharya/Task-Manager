document.addEventListener('DOMContentLoaded', () => {
    // Function to handle responsive behavior
    const handleResponsive = () => {
        const screenWidth = window.innerWidth;

        // Navbar adjustments
        const navbar = document.querySelector('.navbar');
        if (screenWidth < 768) {
            navbar.style.flexDirection = 'column';
            navbar.style.alignItems = 'flex-start';
        } else {
            navbar.style.flexDirection = 'row';
            navbar.style.alignItems = 'center';
        }

        // Hero section adjustments
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            if (screenWidth < 768) {
                heroSection.style.flexDirection = 'column';
                heroSection.style.textAlign = 'center';
            } else {
                heroSection.style.flexDirection = 'row';
                heroSection.style.textAlign = 'left';
            }
        }

        // Tables adjustments
        const tables = document.querySelectorAll('table');
        tables.forEach(table => {
            if (screenWidth < 768) {
                table.style.fontSize = '0.9rem';
                table.style.overflowX = 'auto';
            } else {
                table.style.fontSize = '1rem';
                table.style.overflowX = 'unset';
            }
        });

        // Forms adjustments
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            if (screenWidth < 768) {
                form.style.maxWidth = '100%';
            } else {
                form.style.maxWidth = '400px';
            }
        });
    };

    // Initial call to set responsiveness
    handleResponsive();

    // Reapply responsiveness on window resize
    window.addEventListener('resize', handleResponsive);
});