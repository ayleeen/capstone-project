
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll(
        '.menu-filter-pills .btn[data-filter]'
    );

    const sections = document.querySelectorAll(
        '#menuAllView .menu-section'
    );

    function setActiveFilter(filter) {
        filterButtons.forEach(button => {
            button.classList.toggle(
                'active',
                button.dataset.filter === filter
            );
        });

        sections.forEach(section => {
            section.style.display =
                filter === 'all' ||
                section.dataset.section === filter
                    ? ''
                    : 'none';
        });
    }

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            setActiveFilter(this.dataset.filter);
        });
    });

    setActiveFilter('all');
});
