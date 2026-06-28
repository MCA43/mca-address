(function () {
    'use strict';

    function normalize(value) {
        return String(value || '')
            .toLocaleLowerCase('tr-TR')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function enhanceFilterSelect(root) {
        if (root.dataset.enhanced === '1') {
            return;
        }

        root.dataset.enhanced = '1';

        var native = root.querySelector('.mca-addr-filter-select__native');
        var trigger = root.querySelector('.mca-addr-filter-select__trigger');
        var valueEl = root.querySelector('.mca-addr-filter-select__value');
        var dropdown = root.querySelector('.mca-addr-filter-select__dropdown');
        var search = root.querySelector('.mca-addr-filter-select__search');
        var list = root.querySelector('.mca-addr-filter-select__list');
        var autoSubmit = root.hasAttribute('data-auto-submit');
        var items = [];
        var activeIndex = -1;

        if (!native || !trigger || !valueEl || !dropdown || !search || !list) {
            return;
        }

        function readOptions() {
            items = Array.prototype.map.call(native.options, function (option) {
                return {
                    value: option.value,
                    label: option.textContent.trim(),
                    normalized: normalize(option.textContent.trim()),
                };
            });
        }

        function selectedLabel() {
            var current = native.options[native.selectedIndex];

            return current ? current.textContent.trim() : '';
        }

        function syncTrigger() {
            valueEl.textContent = selectedLabel();
        }

        function closeDropdown() {
            dropdown.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
        }

        function openDropdown() {
            dropdown.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            search.value = '';
            renderList('');
            window.setTimeout(function () {
                search.focus();
            }, 0);
        }

        function toggleDropdown() {
            if (dropdown.hidden) {
                openDropdown();
            } else {
                closeDropdown();
            }
        }

        function choose(value) {
            native.value = value;
            syncTrigger();
            closeDropdown();

            if (autoSubmit) {
                var form = root.closest('form');
                if (form) {
                    form.requestSubmit ? form.requestSubmit() : form.submit();
                }
            }
        }

        function renderList(query) {
            var term = normalize(query);
            list.innerHTML = '';
            activeIndex = -1;

            var matches = items.filter(function (item) {
                if (!term) {
                    return true;
                }

                return item.normalized.indexOf(term) !== -1;
            });

            if (!matches.length) {
                var empty = document.createElement('li');
                empty.className = 'mca-addr-filter-select__empty';
                empty.textContent = (window.McaAddrI18n && window.McaAddrI18n.no_results) || 'Sonuç yok';
                list.appendChild(empty);

                return;
            }

            matches.forEach(function (item, index) {
                var li = document.createElement('li');
                li.className = 'mca-addr-filter-select__option';
                li.setAttribute('role', 'option');
                li.dataset.value = item.value;
                li.textContent = item.label;

                if (String(native.value) === String(item.value)) {
                    li.classList.add('is-selected');
                    li.setAttribute('aria-selected', 'true');
                }

                li.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                });

                li.addEventListener('click', function () {
                    choose(item.value);
                });

                list.appendChild(li);
            });
        }

        function moveActive(direction) {
            var options = list.querySelectorAll('.mca-addr-filter-select__option');

            if (!options.length) {
                return;
            }

            activeIndex += direction;

            if (activeIndex < 0) {
                activeIndex = options.length - 1;
            }

            if (activeIndex >= options.length) {
                activeIndex = 0;
            }

            options.forEach(function (option, index) {
                option.classList.toggle('is-active', index === activeIndex);
            });

            options[activeIndex].scrollIntoView({ block: 'nearest' });
        }

        readOptions();
        syncTrigger();

        trigger.addEventListener('click', function () {
            toggleDropdown();
        });

        search.addEventListener('input', function () {
            renderList(search.value);
        });

        search.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                moveActive(1);
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveActive(-1);
            }

            if (event.key === 'Enter') {
                event.preventDefault();
                var active = list.querySelector('.mca-addr-filter-select__option.is-active')
                    || list.querySelector('.mca-addr-filter-select__option');

                if (active) {
                    choose(active.dataset.value || '');
                }
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeDropdown();
                trigger.focus();
            }
        });

        document.addEventListener('click', function (event) {
            if (!root.contains(event.target)) {
                closeDropdown();
            }
        });
    }

    function initCrudForms() {
        document.querySelectorAll('[data-mca-addr-crud-root]').forEach(function (root) {
            if (root.dataset.enhanced === '1') {
                return;
            }

            root.dataset.enhanced = '1';

            var form = root.querySelector('[data-mca-addr-crud-form]');
            var formCard = root.querySelector('[data-mca-addr-crud-form-card]');
            var formTitle = root.querySelector('[data-mca-addr-crud-title]');
            var formHint = root.querySelector('[data-mca-addr-crud-hint]');
            var formEditingName = root.querySelector('[data-mca-addr-crud-editing]');
            var formMethod = root.querySelector('[data-mca-addr-crud-method]');
            var submitLabel = root.querySelector('[data-mca-addr-crud-submit-label]');
            var cancelBtn = root.querySelector('[data-mca-addr-crud-cancel]');
            var table = root.querySelector('[data-mca-addr-crud-table]');
            var activeRow = null;
            var defaults = captureFormDefaults(form);

            var i18n = {
                newTitle: root.dataset.i18nNew || '',
                editTitle: root.dataset.i18nEdit || '',
                hint: root.dataset.i18nHint || '',
                add: root.dataset.i18nAdd || '',
                save: root.dataset.i18nSave || '',
                cancel: root.dataset.i18nCancel || '',
                editing: root.dataset.i18nEditing || '',
            };

            function setCreateMode() {
                if (!form) {
                    return;
                }

                form.action = root.dataset.storeUrl || form.action;

                if (formMethod) {
                    formMethod.disabled = true;
                }

                if (formTitle) {
                    formTitle.textContent = i18n.newTitle;
                }

                if (formHint) {
                    formHint.textContent = i18n.hint;
                    formHint.hidden = false;
                }

                if (formEditingName) {
                    formEditingName.hidden = true;
                    formEditingName.textContent = '';
                }

                if (submitLabel) {
                    submitLabel.textContent = i18n.add;
                }

                if (cancelBtn) {
                    cancelBtn.hidden = true;
                }

                if (formCard) {
                    formCard.classList.remove('mca-addr-crud-form-card--edit');
                }

                restoreFormDefaults(form, defaults);

                if (activeRow) {
                    activeRow.classList.remove('is-editing');
                    activeRow = null;
                }
            }

            function setEditMode(btn) {
                if (!form || !btn) {
                    return;
                }

                form.action = btn.getAttribute('data-update-url') || form.action;

                if (formMethod) {
                    formMethod.disabled = false;
                }

                applyFieldsFromButton(btn, form);

                var label = btn.getAttribute('data-label') || '';

                if (formTitle) {
                    formTitle.textContent = i18n.editTitle;
                }

                if (formHint) {
                    formHint.hidden = true;
                }

                if (formEditingName) {
                    formEditingName.textContent = i18n.editing.replace('__NAME__', label);
                    formEditingName.hidden = !label;
                }

                if (submitLabel) {
                    submitLabel.textContent = i18n.save;
                }

                if (cancelBtn) {
                    cancelBtn.hidden = false;
                }

                if (formCard) {
                    formCard.classList.add('mca-addr-crud-form-card--edit');
                }

                if (table) {
                    var row = btn.closest('tr');

                    if (activeRow && activeRow !== row) {
                        activeRow.classList.remove('is-editing');
                    }

                    activeRow = row;

                    if (activeRow) {
                        activeRow.classList.add('is-editing');
                    }
                }

                if (formCard && typeof formCard.scrollIntoView === 'function') {
                    formCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }

            root.querySelectorAll('[data-mca-addr-edit-row]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    setEditMode(btn);
                });
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    setCreateMode();
                });
            }
        });
    }

    function captureFormDefaults(form) {
        var defaults = {};

        if (!form) {
            return defaults;
        }

        form.querySelectorAll('[name]').forEach(function (el) {
            if (el.name === '_method') {
                return;
            }

            if (el.type === 'hidden' && form.querySelector('[name="' + el.name + '"][type="checkbox"]')) {
                return;
            }

            if (el.type === 'checkbox') {
                defaults[el.name] = el.checked;

                return;
            }

            defaults[el.name] = el.value;
        });

        return defaults;
    }

    function restoreFormDefaults(form, defaults) {
        if (!form) {
            return;
        }

        Object.keys(defaults).forEach(function (name) {
            var checkbox = form.querySelector('[name="' + name + '"][type="checkbox"]');

            if (checkbox) {
                checkbox.checked = !!defaults[name];

                return;
            }

            var el = form.querySelector('[name="' + name + '"]');

            if (!el) {
                return;
            }

            el.value = defaults[name];
        });
    }

    function applyFieldsFromButton(btn, form) {
        Array.prototype.forEach.call(btn.attributes, function (attr) {
            if (attr.name.indexOf('data-field-') !== 0) {
                return;
            }

            var fieldName = attr.name.slice('data-field-'.length);
            var input = form.querySelector('[name="' + fieldName + '"][type="checkbox"]')
                || form.querySelector('[name="' + fieldName + '"]');

            if (!input) {
                return;
            }

            if (input.type === 'checkbox') {
                input.checked = attr.value === '1' || attr.value === 'true';

                return;
            }

            input.value = attr.value;
        });
    }

    function init() {
        document.querySelectorAll('[data-mca-addr-filter-select]').forEach(enhanceFilterSelect);
        initCrudForms();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
