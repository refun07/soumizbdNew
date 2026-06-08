const { autoUpdate, computePosition, flip, offset, shift } = window.FloatingUIDOM;

$(function () {
    // Customer Create Form Submission via AJAX
    $(document).on('click', '.customer-create-form-modal button[type="submit"]', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const submitBtn = $(this);
        const errorDiv = form.find('#customer-create-error');
        const originalBtnText = submitBtn.html();

        // Hide error, show loading
        errorDiv.addClass('hidden').empty();
        submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                MicroModal.close('customer-create-modal');
                window.location.reload();
            },
            error: function (xhr) {
                let errorMessage = 'Something went wrong';

                try {
                    const response = xhr.responseJSON || {};

                    if (response.message) {
                        errorMessage = response.message;
                    } else if (response.errors && typeof response.errors === 'object') {
                        const firstError = Object.values(response.errors)[0];
                        if (Array.isArray(firstError) && firstError.length > 0) {
                            errorMessage = firstError[0];
                        } else if (typeof firstError === 'string') {
                            errorMessage = firstError;
                        }
                    }
                } catch (e) {
                    errorMessage = xhr.statusText || 'Something went wrong';
                }

                errorDiv.text(errorMessage).removeClass('hidden');
            },
            complete: function () {
                submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    const dropdownOpenClass = 'option-dropdown--open';

    const getDropdownElements = (dropdownEl) => ({
        referenceEl: dropdownEl.querySelector('.option-dropdown__toggler'),
        floatingEl: dropdownEl.querySelector('.option-dropdown__options'),
    });

    const getDropdownPlacement = (dropdownEl, floatingEl) => {
        const placement = dropdownEl.dataset.placement || floatingEl.dataset.placement;
        if (placement) return placement;

        if (floatingEl.classList.contains('option-dropdown__options--top') || floatingEl.classList.contains('bottom-full')) {
            return 'top-start';
        }

        return 'bottom-start';
    };

    const updateFloatingUIDropdown = (referenceEl, floatingEl, placement) => {
        floatingEl.style.width = `${referenceEl.offsetWidth}px`;
        return computePosition(referenceEl, floatingEl, {
            placement,
            strategy: 'fixed',
            middleware: [offset(6), flip({ rootBoundary: 'viewport' }), shift({ padding: 5, rootBoundary: 'viewport' })],
        }).then(({ x, y, strategy }) => {
            Object.assign(floatingEl.style, {
                position: strategy,
                left: `${x}px`,
                top: `${y}px`,
                right: 'auto',
                bottom: 'auto',
                transform: 'translate(0, 0)',
            });
        });
    };

    const closeOptionDropdown = (dropdownEl) => {
        if (!dropdownEl) return;

        dropdownEl._optionDropdownRequest = null;
        dropdownEl.classList.remove(dropdownOpenClass);

        if (dropdownEl._floatingUICleanup) {
            dropdownEl._floatingUICleanup();
            dropdownEl._floatingUICleanup = null;
        }
    };

    const closeOptionDropdowns = (exceptDropdownEl = null) => {
        document.querySelectorAll('.option-dropdown').forEach((dropdownEl) => {
            if (dropdownEl !== exceptDropdownEl) {
                closeOptionDropdown(dropdownEl);
            }
        });
    };

    const openOptionDropdown = (dropdownEl) => {
        const { referenceEl, floatingEl } = getDropdownElements(dropdownEl);
        if (!referenceEl || !floatingEl) return;

        const placement = getDropdownPlacement(dropdownEl, floatingEl);

        closeOptionDropdowns(dropdownEl);

        const requestId = Symbol('optionDropdownOpen');
        dropdownEl._optionDropdownRequest = requestId;

        updateFloatingUIDropdown(referenceEl, floatingEl, placement).then(() => {
            if (dropdownEl._optionDropdownRequest !== requestId) return;
            if (!document.body.contains(dropdownEl)) return;

            dropdownEl.classList.add(dropdownOpenClass);

            if (dropdownEl._floatingUICleanup) {
                dropdownEl._floatingUICleanup();
            }

            dropdownEl._floatingUICleanup = autoUpdate(referenceEl, floatingEl, () => {
                updateFloatingUIDropdown(referenceEl, floatingEl, placement);
            });
        });
    };

    $(document).on('click', '.option-dropdown__toggler', function (e) {
        e.preventDefault();

        const dropdownEl = this.closest('.option-dropdown');
        if (!dropdownEl) return;

        if (dropdownEl.classList.contains(dropdownOpenClass)) {
            closeOptionDropdown(dropdownEl);
        } else {
            openOptionDropdown(dropdownEl);
        }
    });

    $(document).on('keydown', '.option-dropdown', function (e) {
        const dropdownEl = this;

        if (e.key === 'Escape') {
            closeOptionDropdown(dropdownEl);
            const { referenceEl } = getDropdownElements(dropdownEl);
            if (referenceEl) {
                referenceEl.focus();
            }
            return;
        }

        if ((e.key === 'Enter' || e.key === ' ') && (e.target === dropdownEl || e.target.closest('.option-dropdown__toggler'))) {
            e.preventDefault();

            if (dropdownEl.classList.contains(dropdownOpenClass)) {
                closeOptionDropdown(dropdownEl);
            } else {
                openOptionDropdown(dropdownEl);
            }
        }
    });

    $(document).on('click', '.option-dropdown__option', function () {
        closeOptionDropdown(this.closest('.option-dropdown'));
    });

    $(document).on('click', function (e) {
        if (!e.target.closest('.option-dropdown')) {
            closeOptionDropdowns();
        }
    });

    $(document).on('focusin', function (e) {
        const focusedDropdownEl = e.target.closest('.option-dropdown');
        closeOptionDropdowns(focusedDropdownEl);
    });

    // count down
    $('.count-down-time').each(function (_idx, item) {
        let countDownTime = $(item).data('endsAt');

        const counter = $(item).countdown(
            new Date(countDownTime),
            function (event) {
                $(this).html(
                    event.strftime(`
                    <div class="flex items-center gap-[2px]">
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Dd
                        </div>
                        <span class="text-orange-500 font-bold">:</span>
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Hh
                        </div>
                        <span class="text-orange-500 font-bold">:</span>
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Mm
                        </div>
                        <span class="text-orange-500 font-bold">:</span>
                        <div class="h-9 w-9 flex items-center justify-center bg-red-500 text-white">
                            %Ss
                        </div>
                    </div>
                `),
                );
            },
        );
    });

    // language flag select2
    $('.flag-select2').select2({
        selectionCssClass: 'input-select2',
        templateResult: countryCodeFlag,
        templateSelection: countryCodeFlag,
        escapeMarkup: function (m) {
            return m;
        },
    });

    function countryCodeFlag(state) {
        var flagName = $(state.element).data('flag');
        if (!flagName) return state.text;
        return `<div class="flex items-center"><img class='me-2 h-3' src='${flagName}' /> ${state.text}</div>`;
    }

    // modal toggler
    $('.modal-toggler').on('click', function () {
        const target = $(this).data('target');
        $(target).toggleClass('hidden');
        $(this).toggleClass('active');
    });

    // toggle sidebar
    $('.sidebar-toggler').on('click', function () {
        if (window.innerWidth < 1024) {
            $('.sidebar').toggleClass('active');
            $('.overlay').toggleClass('active');
        } else {
            if ($('.sidebar').hasClass('in-active')) {
                $('.sidebar').animate({ width: '300px' }, 250);
            } else {
                $('.sidebar').animate({ width: '90px' }, 250);
            }

            $('.sidebar').toggleClass('in-active');
        }
    });
    $('.overlay').on('click', function () {
        if (window.innerWidth < 1024) {
            $('.sidebar').removeClass('active');
        } else {
            $('.sidebar').addClass('in-active');
        }
        $('.overlay').removeClass('active');
    });

    // toggle sidebar sub-menu
    $('.has-submenu .sidebar__item').on('click', function (e) {
        e.preventDefault();
        $(this).parent().toggleClass('expanded');
    });

    // hide sidebar when screen is too small
    const autoCloseSidebar = () => {
        if ($(window).width() < 1024) {
            $('.sidebar').addClass('sidebar--mobile');
        } else {
            $('.sidebar').removeClass('sidebar--mobile');
        }
    };

    window.addEventListener('resize', function () {
        autoCloseSidebar();
    });
    autoCloseSidebar();

    // date picker - skip elements with data-skip-datepicker-init attribute
    $(
        '.date-picker.date-picker--range input:not([data-skip-datepicker-init])',
    ).daterangepicker();
    $(
        '.date-picker.date-picker--single input:not([data-skip-datepicker-init])',
    ).daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: true,
        drops: 'auto',
        autoApply: true,
        locale: {
            format: 'DD-MM-YYYY',
        },
    });

    const rangeSliders = document.querySelectorAll('.range-slider');
    rangeSliders.forEach((rangeSliderEl) => {
        const minInputEl = rangeSliderEl.querySelector('.range-slider__min');
        const maxInputEl = rangeSliderEl.querySelector('.range-slider__max');

        // initialize a noUiSlider for each
        const rangeSlider = noUiSlider.create(
            rangeSliderEl.querySelector('.range-slider__slider'),
            {
                range: {
                    min: 0,
                    max: 100,
                },
                tooltips: true,
                step: 1,
                connect: true,
                start: [30, 70],
            },
        );

        rangeSlider.on('update', function (values) {
            minInputEl.value = Number(values[0]);
            maxInputEl.value = Number(values[1]);
        });

        minInputEl.addEventListener('change', function () {
            rangeSlider.set([this.value, null]);
        });
        maxInputEl.addEventListener('change', function () {
            rangeSlider.set([null, this.value]);
        });
    });

    // counter
    $('.counter .counter--increment').on('click', function () {
        var $input = $(this).parent().find('.counter--value');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
    $('.counter .counter--decrement').on('click', function () {
        var $input = $(this).parent().find('.counter--value');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });

    // general toggle function
    $('.toggler').on('click', function () {
        const target = $(this).data('target');
        $(target).toggleClass('hidden flex');
        $(this).toggleClass('active');
    });

    // toggle mobile search popup
    $('.toggle-mobile-search').on('click', function () {
        $('.mobile-search-box').toggleClass('active');
        $(this).find('.fa-regular').toggleClass('fa-search fa-times');
    });

    // paginationForm
    $('.paginationForm').on('change', function () {
        $(this).closest('form').submit();
    });

    // filter select
    $('.filterOnSelect').on('change', function () {
        $(this).closest('form').submit();
    });

    // filter select - preserves existing query parameters
    $('.filterOnSelect').on('change', function () {
        let value = $(this).val();
        let name = $(this).data('name');
        let url = new URL(window.location.href);
        if (value) {
            url.searchParams.set(name, value);
        } else {
            url.searchParams.delete(name);
        }
        window.location.href = url.toString();
    });

    // search input - handle enter key press
    $('.search-form input[name="search"]').on('keydown', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            let searchValue = $(this).val();
            let url = new URL(window.location.href);
            if (searchValue) {
                url.searchParams.set('search', searchValue);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        }
    });

    $('.jodit-editor').each(function (idx, item) {
        const editor = Jodit.make(item, {
            height: 300,
            width: '100%',
            toolbar: true,
            toolbarAdaptive: false,
            toolbarSticky: true,
            buttons: [
                'source', '|',
                'bold', 'strikethrough', 'underline', 'italic', '|',
                'ul', 'ol', '|',
                'outdent', 'indent', '|',
                'font', 'fontsize', 'brush', 'paragraph', '|',
                'image', 'link', 'file', 'table', '|',
                'align', 'undo', 'redo', '|',
                'hr', 'eraser', 'copyformat', '|',
                'fullsize', 'print'
            ],
            // Add custom button for browsing gallery
            extraButtons: [
                {
                    tooltip: 'Browse Gallery',
                    iconURL: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiPjxyZWN0IHg9IjMiIHk9IjMiIHdpZHRoPSIxOCIgaGVpZ2h0PSIxOCIgcng9IjIiIHJ5PSIyIi8+PGNpcmNsZSBjeD0iOC41IiBjeT0iOC41IiByPSIxLjUiLz48cG9seWxpbmUgcG9pbnRzPSIyMSAxNSAxNiAxMCA1IDIxIi8+PC9zdmc+',
                    exec: function(editor) {
                        // Open media manager modal for browsing
                        if (window.mediaManager) {
                            // Reset media manager
                            window.mediaManager.reset();

                            // Initialize media manager
                            window.mediaManager.init($(item).closest('.theme-input-group'))
                                .then(() => {
                                    // Show the media manager modal
                                    MicroModal.show('media-manager-modal');

                                    // Set up callback for file selection
                                    const setFilesBtn = document.querySelector('.set-files');
                                    if (setFilesBtn) {
                                        const originalOnClick = setFilesBtn.onclick;
                                        setFilesBtn.onclick = function() {
                                            const selectedFiles = window.mediaManager.data.selectedFiles;
                                            if (selectedFiles.length > 0) {
                                                // Insert each selected file
                                                selectedFiles.forEach(file => {
                                                    const fileUrl = window.location.origin + '/' + file.media_file;
                                                    const fileName = file.media_name + '.' + file.media_extension;
                                                    const fileType = file.media_type;

                                                    if (fileType === 'image') {
                                                        editor.selection.insertHTML(`<img src="${fileUrl}" alt="${file.alt_text || fileName}" />`);
                                                    } else {
                                                        editor.selection.insertHTML(`<a href="${fileUrl}" target="_blank">${fileName}</a>`);
                                                    }
                                                });

                                                // Close modal
                                                MicroModal.close('media-manager-modal');

                                                // Reset media manager
                                                window.mediaManager.reset();
                                            }
                                            // Restore original onclick
                                            if (originalOnClick) {
                                                setFilesBtn.onclick = originalOnClick;
                                            }
                                        };
                                    }
                                });
                        } else {
                            alert('Media Manager not initialized');
                        }
                    }
                }
            ],
            // File browser and upload configuration
            filebrowser: {
                // Custom upload handler for direct file uploads
                upload: {
                    url: window.location.origin + '/file-manager/upload', // Your upload endpoint
                    format: 'json',
                    headers: {
                        'X-CSRF-TOKEN': window.ATE?.data?.csrf || ''
                    },
                    filesVariableName: function (i) {
                        return 'media_file';
                    },
                    isSuccess: function (resp) {
                        return resp.status === 200;
                    },
                    getMsgError: function (resp) {
                        return resp.message || 'Upload failed';
                    },
                    process: function (resp) {
                        if (resp.status === 200 && resp.result) {
                            const file = resp.result;
                            const fileUrl = window.location.origin + '/' + file.media_file;
                            const fileName = file.media_name + '.' + file.media_extension;

                            // Insert the uploaded file into editor
                            if (file.media_type === 'image') {
                                editor.selection.insertHTML(`<img src="${fileUrl}" alt="${file.alt_text || fileName}" />`);
                            } else {
                                editor.selection.insertHTML(`<a href="${fileUrl}" target="_blank">${fileName}</a>`);
                            }

                            return {
                                files: [file],
                                path: [file.media_file],
                                baseurl: window.location.origin + '/',
                                error: 0,
                                msg: 'Upload successful'
                            };
                        }
                        return {
                            files: [],
                            error: resp.message || 'Upload failed',
                            msg: resp.message || 'Upload failed'
                        };
                    }
                },
            },
            controls: {
                ul: {
                    popup: function(editor, current, controlTag, close) {
                        const container = editor.create.fromHTML('<div class="jodit-ui jodit-toolbar__box"></div>');

                        const options = [
                            { icon: '•', style: 'disc', name: 'Disc' },
                            { icon: '◦', style: 'circle', name: 'Circle' },
                            { icon: '▪', style: 'square', name: 'Square' }
                        ];

                        options.forEach(opt => {
                            const button = editor.create.fromHTML(
                                '<button class="jodit-toolbar__button" title="' + opt.name + '">' +
                                '<span class="jodit-toolbar__button-icon">' + opt.icon + '</span>' +
                                '</button>'
                            );
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                editor.execCommand('insertUnorderedList');
                                const ul = editor.selection.closest(editor.editor, 'ul');
                                if (ul) {
                                    ul.style.setProperty('list-style-type', opt.style);
                                }
                                close();
                            });
                            container.appendChild(button);
                        });

                        return container;
                    }
                },
                ol: {
                    popup: function(editor, current, controlTag, close) {
                        const container = editor.create.fromHTML('<div class="jodit-ui jodit-toolbar__box"></div>');

                        const options = [
                            { icon: '1.', style: 'decimal', name: 'Number' },
                            { icon: 'a.', style: 'lower-alpha', name: 'Lowercase' },
                            { icon: 'A.', style: 'upper-alpha', name: 'Uppercase' }
                        ];

                        options.forEach(opt => {
                            const button = editor.create.fromHTML(
                                '<button class="jodit-toolbar__button" title="' + opt.name + '">' +
                                '<span class="jodit-toolbar__button-icon">' + opt.icon + '</span>' +
                                '</button>'
                            );
                            button.addEventListener('click', function(e) {
                                e.preventDefault();
                                editor.execCommand('insertOrderedList');
                                const ol = editor.selection.closest(editor.editor, 'ol');
                                if (ol) {
                                    ol.style.setProperty('list-style-type', opt.style);
                                }
                                close();
                            });
                            container.appendChild(button);
                        });

                        return container;
                    }
                }
            }
        });
        editor.setEditorValue($(item).val());
    });
});
