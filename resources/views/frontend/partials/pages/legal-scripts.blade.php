<script>
(function () {
    'use strict';

    var blocks = document.querySelectorAll('.store-legal-block[id]');
    var links = document.querySelectorAll('.store-legal-nav__link');
    if (!blocks.length || !links.length) {
        return;
    }

    function setActiveLink(id) {
        links.forEach(function (link) {
            var isActive = link.getAttribute('href') === '#' + id;
            link.classList.toggle('is-active', isActive);
        });
    }

    function syncFromScroll() {
        var offset = window.innerHeight * 0.35;
        var currentId = blocks[0].id;

        blocks.forEach(function (block) {
            if (block.getBoundingClientRect().top <= offset) {
                currentId = block.id;
            }
        });

        setActiveLink(currentId);
    }

    links.forEach(function (link) {
        link.addEventListener('click', function () {
            var id = (this.getAttribute('href') || '').replace('#', '');
            if (id) {
                setActiveLink(id);
            }
        });
    });

    window.addEventListener('scroll', syncFromScroll, { passive: true });
    syncFromScroll();
})();
</script>
