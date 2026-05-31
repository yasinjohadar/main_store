(function () {
    'use strict';

    function updateGroupCount(groupKey) {
        var boxes = document.querySelectorAll('[data-perm-group="' + groupKey + '"][name^="permissions"]');
        var checked = 0;
        boxes.forEach(function (cb) {
            if (cb.checked) {
                checked++;
            }
        });
        var badge = document.querySelector('[data-group-count="' + groupKey + '"]');
        if (badge) {
            badge.textContent = checked + '/' + boxes.length;
        }
        var toggle = document.querySelector('[data-perm-group-toggle="' + groupKey + '"]');
        if (toggle) {
            toggle.checked = checked === boxes.length && boxes.length > 0;
            toggle.indeterminate = checked > 0 && checked < boxes.length;
        }
    }

    function updateAllGroups() {
        document.querySelectorAll('[data-perm-group-toggle]').forEach(function (toggle) {
            updateGroupCount(toggle.getAttribute('data-perm-group-toggle'));
        });
    }

    function initIndeterminate() {
        document.querySelectorAll('[data-perm-group-toggle][data-indeterminate]').forEach(function (toggle) {
            toggle.indeterminate = true;
        });
    }

    function bindEvents(root) {
        root.addEventListener('change', function (e) {
            var groupToggle = e.target.matches('[data-perm-group-toggle]') ? e.target : null;
            if (groupToggle) {
                var groupKey = groupToggle.getAttribute('data-perm-group-toggle');
                document.querySelectorAll('[data-perm-group="' + groupKey + '"][name^="permissions"]').forEach(function (cb) {
                    cb.checked = groupToggle.checked;
                });
                groupToggle.indeterminate = false;
                updateGroupCount(groupKey);
                return;
            }

            if (e.target.matches('[name^="permissions"]')) {
                updateGroupCount(e.target.getAttribute('data-perm-group'));
            }
        });

        var selectAll = root.querySelector('[data-perm-select-all]');
        if (selectAll) {
            selectAll.addEventListener('click', function () {
                root.querySelectorAll('[name^="permissions"]').forEach(function (cb) {
                    cb.checked = true;
                });
                updateAllGroups();
            });
        }

        var clearAll = root.querySelector('[data-perm-clear-all]');
        if (clearAll) {
            clearAll.addEventListener('click', function () {
                root.querySelectorAll('[name^="permissions"]').forEach(function (cb) {
                    cb.checked = false;
                });
                updateAllGroups();
            });
        }
    }

    function init() {
        document.querySelectorAll('.role-permissions').forEach(function (root) {
            initIndeterminate();
            updateAllGroups();
            bindEvents(root);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
