<script>
    'use strict';

    let mediaManager = {};

    $(document).ready(function() {
        // selector classes
        const inputGroupSelector = '.theme-input-group.theme-input--file-upload'
        const filePreviewItemSelector = '.file-preview__item'
        const setFilesBtnSelector = '.set-files'
        const mediaManagerModalSelector = '#media-manager-modal'
        const wrapperSelector = '.media-manager--wrapper'
        const mediaManagerSelector = '.media-manager'
        const mediaManagerBrowserSelector = '.media-manager__browser'
        const mediaManagerUploaderSelector = '.media-manager__uploader'
        const mediaManagerItemSelector = '.media-manager__item'
        const mediaInfoFormSelector = '#media-info-form'
        const uploadTogglerSelector = '.toggle-media-uploader'
        const mediaFiltersSelector = '#media-manager__filters'
        const showMediaInfoSelector = '.show-media-info'
        const mediaManagerFilterTogglerSelector = '.media-manager__filters--toggler'
        // pagination
        const paginatePrevSelector = '.media-manager__pagination--prev'
        const paginateNextSelector = '.media-manager__pagination--next'

        const getMediaLimitPerRow = () => {
            if (window.innerWidth >= 1280) {
                return 9
            } else if (window.innerWidth >= 768) {
                return 7
            } else if (window.innerWidth >= 640) {
                return 5
            } else if (window.innerWidth >= 490) {
                return 4
            } else {
                return 3
            }
        }

        const initialState = {
            data: {
                element: null,
                multiple: false,
                type: "all",
                allFiles: [],
                selectedFiles: [],
                clickedForDelete: null,

                filter: {
                    sort: 'newest',
                    search: '',
                    type: 'all',
                    page: 1,
                },

                meta: {
                    current_page: 1,
                    per_page: 5 * getMediaLimitPerRow() - 1,
                    totalItems: null,
                    totalPages: null,
                }
            },
            selectors: {
                $inputGroup: null,
                $mediaPreview: null,
                $mediaCount: null,
            },
        }

        let debouncedPagination = debounce(async () => {
            await mediaManager.getAllUploads()
            mediaManager.renderAllFiles()
        }, 500);

        mediaManager = {
            ...JSON.parse(JSON.stringify(initialState)), // deep copy of initial state

            markups: {
                uploadMediaMarkup: () => {
                    return `
                        <div
                            class="toggle-media-uploader aspect-square border-2 border-dashed border-secondary bg-secondary/[.02] rounded-md cursor-pointer flex items-center flex-col justify-center">
                            <span class="leading-tight">
                                <i class="fa-sharp fa-light fa-plus text-secondary text-2xl lg:text-5xl"></i>
                            </span>
                            <p>Add Media</p>
                        </div>
                    `;
                },
                mediaMarkup: (file, isSelected = false) => {
                    return `
                        <div class="relative group">
                            <div class="media-manager__item ${isSelected && 'selected'}" tabindex="0" data-media-id="${file.id}">
                                <span class="media-manager__check-mark">
                                    <i class="fas fa-check"></i>
                                </span>
                                <img src="{{ asset('${file.media_file}') }}" alt="" />
                            </div>

                            <div class="option-dropdown absolute top-3 right-3" tabindex="0">
                                <div class="option-dropdown__toggler no-style no-arrow h-8 w-8 rounded bg-white text-secondary-light flex item-center justify-center shadow-theme">
                                    <i class="fa-solid fa-ellipsis-v !text-xl"></i>
                                </div>

                                <div class="option-dropdown__options left-auto right-0 w-[150px] text-xs">
                                    <ul>
                                        <li>
                                            <button class="option-dropdown__option show-media-info text-left" data-media-id="${file.id}">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>Details</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button class="remove-background option-dropdown__option text-left" data-media-id="${file.id}">
                                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                                <span>Remove Background</span>
                                            </button>
                                        </li>
                                        <li>
                                            <button class="option-dropdown__option copy-link text-left" data-media-url="{{ asset('${file.media_file}') }}">
                                                <i class="fas fa-link"></i>
                                                <span>Copy Link</span>
                                            </button>
                                        </li>
                                        <li>
                                            <a href="{{ asset('${file.media_file}') }}" download="{{ asset('${file.media_file}') }}"
                                             class="option-dropdown__option">
                                                <i class="fas fa-download"></i>
                                                <span>Download</span>
                                            </a>
                                        </li>
                                        <li>
                                            <button class="destroy-media option-dropdown__option text-left !text-theme-alert hover:bg-theme-alert/20" data-media-id="${file.id}">
                                                <i class="fa-solid fa-trash-can"></i>
                                                <span>Delete</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `
                },
                loadingMarkup: () => {
                    return `
                        <div class="grow">
                            <svg aria-hidden="true" class="mx-auto mt-16 w-12 h-12 text-muted animate-spin fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                            </svg>
                        </div>
                    `
                },
                removeBackgroudLoadingMarkup: () => {
                    return `
                        <div class="text-center flex flex-col items-center justify-center grow max-w-[520px] mx-auto">
                            <img src="{{ asset('images/icons/remove-bg.png') }}" class="max-h-[150px]" />
                            <h4 class="text-xl font-medium mt-8 mb-3">{{ translate('Magic Happening!') }}</h4>
                            <p>{{ translate('Your AI Assistant is removing the background of the image. This might take a up to a minutes. Please wait to experience the magic.') }}</p>
                        </div>
                    `
                },
                noMediaMarkup: () => {
                    // if no file is found then show this markup
                    return `
                        <div class="grow">
                            <div class="h-full flex flex-col items-center justify-center max-w-[290px] mx-auto mt-10">
                                <img src="{{ asset('images/no-media.png') }}" alt="">
                                <p class="text-lg font-semibold text-center mt-5">No Media File Found!</p>
                                <p class="mt-1 mb-4 text-center">Seems like this filed is empty. Let’s upload some files!</p>

                                <button class="toggle-media-uploader text-primary text-sm mt-2">
                                    <i class="fal fa-upload mr-1"></i>
                                    Upload Files
                                </button>
                            </div>
                        </div>
                    `
                },
                mediaInfoMarkup: function(file) {
                    return `
                        <div class="flex gap-4">
                            <div class="max-w-[150px]">
                                <img src="{{ asset('${file?.media_file}') }}" alt="Image" class="w-40 h-40 object-cover" />
                            </div>
                            <div class="grow text-muted space-y-1">
                                <h5 class="">${file.media_name.replace(/^(.{10}).*(.{8})$/, '$1...$2')}.${file.media_extension}</h5>
                                <p>${mediaManager.mediaSizeStr(file.media_size)}</p>
                                <p>${dateFormatter(file.created_at, 'DD MMM, YYYYY')}</p>
                                <p>
                                    <span class="text-foreground">By</span>
                                    Admin
                                </p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex gap-3 items-center">
                                <div class="whitespace-nowrap">{{ translate('Alternate Text') }}</div>
                                <input type="hidden" name="media_id" value="${file.id}">
                                <x-backend.inputs.text name="alt" placeholder="Type Here..." value="${file.alt_text || ''}" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-backend.inputs.button type="submit" class="w-full justify-center"
                                buttonText="{{ translate('UPDATE') }}" />
                        </div>
                    `
                },
                inputMediaPreviewMarkup: function(file) {
                    return `
                        <div class="file-preview__item group max-w-[120px]">
                            <div class="relative rounded-md overflow-hidden">
                                <img src="{{ asset('${file.media_file}') }}" alt=""
                                    class="mx-auto w-full aspect-video object-cover group-hover:brightness-50 transition-all">

                                <button
                                    type="button"
                                    class="file-preview__item--remove absolute inset-1/2 h-5 w-5 rounded-full -translate-x-1/2 -translate-y-1/2 cursor-pointer bg-background flex items-center justify-center opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all delay-100"
                                    data-media-id="${file.id}"
                                    >
                                    <i class="fal fa-times"></i>
                                </button>
                            </div>
                            <div>
                                <h6 class="break-words text-sm">branding...asd.png</h6>
                                <span class="text-muted text-xs">${mediaManager.mediaSizeStr(file.media_size)}</span>
                            </div>
                        </div>
                    `;
                },
                paginationMarkup: function(meta) {},
            },

            mediaSizeStr: function(media_size) {
                if (media_size < 1024) {
                    return media_size + 'b'
                } else if (media_size < 1024 * 1024) {
                    return (media_size / 1024).toFixed(2) + 'kb'
                } else if (media_size < 1024 * 1024 * 1024) {
                    return (media_size / (1024 * 1024)).toFixed(2) + 'mb'
                } else {
                    return (media_size / (1024 * 1024 * 1024)).toFixed(2) + 'gb'
                }
            },
            paginate: async function(pageNavigation) {
                const {
                    per_page,
                    totalPages,
                } = mediaManager.data.meta;

                const {
                    page
                } = mediaManager.data.filter;

                let pageToGo = page;

                if (pageNavigation === 'next') {
                    if (page === totalPages) {
                        return;
                    }
                    pageToGo += 1;
                } else if (pageNavigation === 'prev') {
                    if (page === 1) {
                        return;
                    }
                    pageToGo -= 1;
                }

                mediaManager.data.filter = {
                    ...mediaManager.data.filter,
                    page: pageToGo,
                }

                debouncedPagination()
            },

            // insert file view html to media manager
            renderAllFiles: function() {
                const mediaFiles = mediaManager.data.allFiles;

                if (!mediaFiles.length) {
                    $(mediaManagerSelector).empty()
                    $(mediaManagerSelector).append(mediaManager.markups.noMediaMarkup())
                    return;
                }

                const selectedFileIds = mediaManager.data.selectedFiles.map(
                    (file) => file.id
                );

                // remove all files from media manager
                $(mediaManagerSelector).empty()
                $(mediaManagerSelector).append(mediaManager.markups.uploadMediaMarkup())
                $(mediaManagerSelector).removeClass('no-media');
                mediaFiles.forEach((file) => {
                    const isSelected = selectedFileIds.includes(file.id);
                    $(mediaManagerSelector).append(mediaManager.markups.mediaMarkup(file,
                        isSelected))
                });
            },

            // get all media fiiles
            getAllUploads: async function(skipLoading = false) {
                try {
                    const {
                        sort,
                        search,
                        type,
                        page,
                    } = mediaManager.data.filter;

                    const {
                        per_page
                    } = mediaManager.data.meta;

                    if (!skipLoading) {
                        $(mediaManagerSelector).addClass('no-media');
                        $(mediaManagerSelector).empty().append(mediaManager.markups.loadingMarkup())
                    }

                    const res = await $.ajax({
                        url: `{{ route('get_uploaded_files') }}`,
                        type: "GET",
                        data: {
                            sort,
                            search,
                            type,
                            page,
                            limit: per_page,
                        },
                    });

                    mediaManager.data.allFiles = res.data;
                    mediaManager.data.meta = {
                        current_page: res.current_page,
                        per_page: res.per_page,
                        totalItems: res.total,
                        totalPages: res.last_page,
                    }
                } catch (error) {
                    console.error('Error loading files:', error);
                }
            },
            // load the files and show in the media manager
            showMedia: async function() {
                await mediaManager.getAllUploads();
                mediaManager.renderAllFiles();
            },

            selectMedia: function(mediaId) {
                const media = mediaManager.data.allFiles.find(
                    (file) => file.id === mediaId
                );

                if (!media) {
                    return;
                }

                mediaManager.data.selectedFiles.push(media);
            },

            getPreviouslySelectedMedia: async function() {
                $('.theme-input--file-upload').each(function() {
                    const $input = $(this).find('input[type="hidden"]')
                    const value = $input.val()
                    if (value === '') {
                        return;
                    }

                    if (value === '') {
                        return;
                    }

                    mediaManager.getMediaByIds(value).then((res) => {
                        // start:this line added by shohan to remove duplicate preview for footable re-initialization
                        $(this).find('.file-preview').empty();
                        // end:this line added by shohan to remove duplicate preview for footable re-initialization

                        res.forEach((file) => {
                            $(this).find('.file-preview').append(
                                mediaManager.markups
                                .inputMediaPreviewMarkup(file)
                            )
                        })
                    })
                })
            },

            removeSelectedMedia: function(mediaId) {
                const media = mediaManager.data.selectedFiles.find(
                    (file) => file.id === mediaId
                );

                if (!media) {
                    return;
                }

                mediaManager.data.selectedFiles = mediaManager.data.selectedFiles.filter(
                    (file) => file.id !== mediaId
                );
            },

            showActiveMedia: function(mediaId) {
                if (!mediaId) {
                    return;
                }

                const media = mediaManager.data.allFiles.find(
                    (file) => file.id === mediaId
                );
                mediaManager.showMediaInfo(media);
            },

            showMediaInfo: function(media) {
                $(mediaInfoFormSelector).empty()
                $(mediaInfoFormSelector).append(
                    mediaManager.markups.mediaInfoMarkup(media)
                )
            },

            setInputValue: function() {
                const selectedFileIds = mediaManager.data.selectedFiles.map(
                    (file) => file.id
                );

                mediaManager.data.element.find('input[type="hidden"]').val(selectedFileIds.join(','))
            },

            // remove an item from input
            removeInputValue: function(fileId) {
                $(inputGroupSelector).on('click', '.file-preview__item--remove', function() {
                    const fileId = +$(this).data('media-id');

                    const input = $(this).parents(inputGroupSelector).find(
                        'input[type="hidden"]')
                    const value = input.val()
                    const newValue = value.split(',').filter((id) => +id !== fileId)
                    input.val(newValue.join(','))

                    $(this).parents(inputGroupSelector).find('.media-count').text(
                        mediaManager.getMediaCountStr(newValue.length)
                    )

                    $(this).parents(filePreviewItemSelector).remove();
                })
            },

            getMediaCountStr: function(files) {
                let mediaCountStr;
                if (!files.length) {
                    mediaCountStr = 'No Selected Item'
                } else if (files.length === 1) {
                    mediaCountStr = '1 media selected'
                } else {
                    mediaCountStr = `${files.length} media selected`
                }
                return mediaCountStr;
            },

            renderInputPreview: function() {
                const selectedFiles = mediaManager.data.selectedFiles;

                mediaManager.selectors.$mediaPreview.empty()
                selectedFiles.forEach((file) => {
                    mediaManager.selectors.$mediaPreview.append(
                        mediaManager.markups.inputMediaPreviewMarkup(file)
                    )
                });

                mediaManager.selectors.$mediaCount.text(mediaManager.getMediaCountStr(selectedFiles))
            },

            toggleMediaUploader: function() {
                $(mediaManagerBrowserSelector).toggleClass('active')
                $(mediaManagerUploaderSelector).toggleClass('active')
            },

            getMediaByIds: async function(ids) {
                return await $.ajax({
                    url: '{{ route('get_file_by_ids') }}',
                    type: "POST",
                    data: {
                        ids: ids
                    },
                    headers: {
                        'X-CSRF-TOKEN': ATE.data.csrf,
                    },
                });
            },

            filterMedia: async function() {
                const $mediaFilter = $(mediaFiltersSelector);

                const filter = async () => {
                    const sort = $mediaFilter.find('select[name="order"]').val()
                    const search = $mediaFilter.find('input[name="media_name"]').val()
                    const type = $mediaFilter.find('select[name="file-type"]').val()

                    mediaManager.data.filter = {
                        ...mediaManager.data.filter,
                        sort,
                        search,
                        type,
                        page: 1,
                    }

                    await mediaManager.getAllUploads(true)
                    mediaManager.renderAllFiles()
                }

                $mediaFilter.on('change', 'select', filter)
                $mediaFilter.on('input', 'input', debounce(filter, 500))
            },

            destroyMedia: async function(mediaId) {
                try {
                    const url = `{{ route('destroy_file', ['id' => ':mediaId']) }}`
                    const res = await $.ajax({
                        url: url.replace(':mediaId', mediaId),
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': ATE.data.csrf,
                        },
                    });

                    mediaManager.data.selectedFiles = mediaManager.data.selectedFiles.filter((file) =>
                        file.id !==
                        mediaId)
                    mediaManager.showMedia()
                    $(mediaInfoFormSelector).empty()
                } catch (error) {
                    console.log(error);
                }
            },

            removeBackground: async function(mediaId) {
                try {

                    $(mediaManagerSelector).addClass('no-media');
                    $(mediaManagerSelector).empty().append(mediaManager.markups
                        .removeBackgroudLoadingMarkup())

                    const url = `{{ route('bg_remove', ['id' => ':mediaId']) }}`
                    const res = await $.ajax({
                        url: url.replace(':mediaId', mediaId),
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': ATE.data.csrf,
                        },
                    });

                    mediaManager.showMedia()
                } catch (error) {
                    console.log(error);
                }
            },

            init: async function(triggeredBy) {
                mediaManager.data.element = $(triggeredBy);
                mediaManager.selectors.$inputGroup = mediaManager.data.element.parents(
                    ".theme-input-group");
                mediaManager.selectors.$mediaPreview = mediaManager.selectors.$inputGroup.find(
                    ".file-preview");
                mediaManager.selectors.$mediaCount = mediaManager.selectors.$inputGroup.find(
                    ".media-count");
                const $input = mediaManager.data.element.find('input[type="hidden"]');

                const multiple = $input.attr('multiple') === 'multiple';
                const type = $input.attr("accept") || "all";
                const value = $input.val();

                if (value) {
                    const res = await mediaManager.getMediaByIds(value);
                    mediaManager.data.selectedFiles = res || [];
                }

                mediaManager.data.multiple = multiple;
                mediaManager.data.type = type;

                mediaManager.showMedia();

                return this;
            },

            reset: function() {
                // deep copy
                mediaManager.data = JSON.parse(JSON.stringify(initialState.data));
                mediaManager.selectors = JSON.parse(JSON.stringify(initialState.selectors));
            }
        }

        // init uppy uploader
        if ($("#uppy-upload-files").length > 0) {
            var uppy = new Uppy.Uppy({
                autoProceed: true,
            });
            uppy.use(Uppy.Dashboard, {
                target: "#uppy-upload-files",
                inline: true,
                showLinkToFileUploadResult: false,
                showProgressDetails: true,
                hideCancelButton: true,
                hidePauseResumeButton: true,
                hideUploadButton: true,
                proudlyDisplayPoweredByUppy: false,
                locale: {
                    strings: {
                        addMoreFiles: ATE.local.add_more_files,
                        addingMoreFiles: ATE.local.adding_more_files,
                        dropPaste: ATE.local.drop_files_here_paste_or + ' %{browse}',
                        browse: ATE.local.browse,
                        uploadComplete: ATE.local.upload_complete,
                        uploadPaused: ATE.local.upload_paused,
                        resumeUpload: ATE.local.resume_upload,
                        pauseUpload: ATE.local.pause_upload,
                        retryUpload: ATE.local.retry_upload,
                        cancelUpload: ATE.local.cancel_upload,
                        xFilesSelected: {
                            0: '%{smart_count} ' + ATE.local.file_selected,
                            1: '%{smart_count} ' + ATE.local.files_selected
                        },
                        uploadingXFiles: {
                            0: ATE.local.uploading + ' %{smart_count} ' + ATE.local.file,
                            1: ATE.local.uploading + ' %{smart_count} ' + ATE.local.files
                        },
                        processingXFiles: {
                            0: ATE.local.processing + ' %{smart_count} ' + ATE.local.file,
                            1: ATE.local.processing + ' %{smart_count} ' + ATE.local.files
                        },
                        uploading: ATE.local.uploading,
                        complete: ATE.local.complete,
                    }
                }
            });
            uppy.use(Uppy.XHRUpload, {
                endpoint: ATE.data.appUrl + "/file-manager/upload",
                fieldName: "media_file",
                formData: true,
                headers: {
                    'X-CSRF-TOKEN': ATE.data.csrf,
                },
            });

            // reload the media manager after upload complete
            // hide uploader and show media manager
            uppy.on("complete", async function(result) {
                const uploadedFiles = result.successful.map(item => {
                    return item.response.body
                })

                if (mediaManager.data.multiple) {
                    mediaManager.data.selectedFiles = [
                        ...mediaManager.data.selectedFiles,
                        ...uploadedFiles,
                    ]
                } else {
                    mediaManager.data.selectedFiles = [uploadedFiles[0]]
                }

                await mediaManager.showMedia();
                mediaManager.toggleMediaUploader();
                uppy.cancelAll()
            });
        }


        // filter the media when the inputs change
        mediaManager.filterMedia()

        $(wrapperSelector).on('click', mediaManagerFilterTogglerSelector, function() {
            $(mediaFiltersSelector).toggleClass('active')
        })

        // click on plus icon in media manager view uploader
        $(wrapperSelector).on('click', uploadTogglerSelector, function() {
            mediaManager.toggleMediaUploader();
        })

        // set image id to input value
        // and close media manager
        $(wrapperSelector).on('click', setFilesBtnSelector, function() {
            mediaManager.data.allFiles = [];
            mediaManager.setInputValue();
            mediaManager.renderInputPreview();
            MicroModal.close('media-manager-modal');
        });

        $(wrapperSelector).on('click', showMediaInfoSelector, function() {
            const mediaId = $(this).data("media-id");
            mediaManager.showActiveMedia(mediaId)
            MicroModal.show('media-info-modal');
        })

        // submit media info form
        $(mediaInfoFormSelector).on('submit', async function(e) {
            e.preventDefault();

            const mediaId = $(this).find('input[name="media_id"]').val()
            const altText = $(this).find('input[name="alt"]').val()

            const res = await $.ajax({
                url: `{{ route('alt_text', ['id' => ':id']) }}`.replace(':id', mediaId),
                type: "POST",
                data: {
                    _token: ATE.data.csrf,
                    alt_text: altText
                },
            });

            MicroModal.close('media-info-modal');
        })

        // select a media on media item click
        // and add active class 
        $(wrapperSelector).on('click', mediaManagerItemSelector, function() {
            const mediaId = $(this).data("media-id");

            if (!mediaManager.data.multiple) {
                mediaManager.data.selectedFiles = []

                $(this)
                    .parent()
                    .find(mediaManagerItemSelector)
                    .each(function() {
                        $(this).removeClass('selected')
                    })
            }

            $(this).toggleClass('selected')

            if ($(this).hasClass('selected')) {
                mediaManager.selectMedia(mediaId);
            } else {
                mediaManager.removeSelectedMedia(mediaId);
            }
            mediaManager.renderAllFiles();
        })

        // add event listener on delete button to delete a media
        $(wrapperSelector).on('click', '.destroy-media', function(e) {
            e.preventDefault();
            const mediaId = $(this).data('media-id');
            mediaManager.destroyMedia(mediaId)
        })

        // add event listener on copy link button
        $(wrapperSelector).on('click', '.copy-link', function(e) {
            e.preventDefault();
            const mediaUrl = $(this).data('media-url');
            navigator.clipboard.writeText(mediaUrl).catch(function(err) {
                console.error('Failed to copy link: ', err);
            });
            // Close the dropdown after copying
            const $dropdown = $(this).closest('.option-dropdown');
            $dropdown.blur();
            // Force blur on all focusable elements inside
            $dropdown.find('button, a, [tabindex]').blur();
            // Remove focus from document
            if (document.activeElement) {
                document.activeElement.blur();
            }
        })

        // add event listener on remove background button
        $(wrapperSelector).on('click', '.remove-background', function(e) {
            e.preventDefault();
            const mediaId = $(this).data('media-id');
            mediaManager.removeBackground(mediaId)
        })

        $(wrapperSelector).on('click', '.destroy-media-bulk', function(e) {
            e.preventDefault();
            const mediaIds = mediaManager.data.selectedFiles.map((file) => file.id)
                .join(',')

            mediaManager.destroyMedia(mediaIds)
        })

        // paginate (next and prev)
        $(paginatePrevSelector).on('click', function() {
            mediaManager.paginate('prev')
        })
        $(paginateNextSelector).on('click', function() {
            mediaManager.paginate('next')
        })

        mediaManager.removeInputValue();
        mediaManager.getPreviouslySelectedMedia();

        // Expose mediaManager globally for React components
        window.mediaManager = mediaManager;

        // Handle "Use Variation Image" button click
        $(document).on('click', '.use-variation-image-btn', async function() {
            const $btn = $(this);

            // Skip if button is disabled
            if ($btn.prop('disabled')) {
                return;
            }

            const variationImageInputName = $btn.data('variation-image-input');
            const combinationImageInputName = $btn.data('combination-image-input');

            // Get the variation image input element
            const $variationImageInput = $(`input[name="${variationImageInputName}"]`);
            const $combinationImageInput = $(`input[name="${combinationImageInputName}"]`);

            if (!$variationImageInput.length || !$combinationImageInput.length) {
                console.error('Could not find input elements');
                return;
            }

            const variationImageId = $variationImageInput.val();

            if (!variationImageId) {
                toastr.error('{{ translate("Please upload a variation image first") }}');
                return;
            }

            // Set the combination image input value
            $combinationImageInput.val(variationImageId);

            // Hide the button since image is now selected
            $btn.addClass('hidden');

            // Get the parent input group for combination image
            const $inputGroup = $combinationImageInput.closest('.theme-input-group');
            const $mediaPreview = $inputGroup.find('.file-preview');
            const $mediaCount = $inputGroup.find('.media-count');

            // Clear existing preview
            $mediaPreview.empty();

            try {
                // Fetch the media file details
                const mediaFiles = await mediaManager.getMediaByIds(variationImageId);

                if (mediaFiles && mediaFiles.length > 0) {
                    // Add preview for each file
                    mediaFiles.forEach((file) => {
                        $mediaPreview.append(
                            mediaManager.markups.inputMediaPreviewMarkup(file)
                        );
                    });

                    // Update the media count
                    const countStr = mediaFiles.length === 1 ? '1 media selected' : `${mediaFiles.length} media selected`;
                    $mediaCount.text(countStr);
                }
            } catch (error) {
                console.error('Error loading preview:', error);
                toastr.error('{{ translate("Failed to load image preview") }}');
            }
        });

        // Show/hide "Use Image" button when image is selected or removed
        $(document).on('click', '.file-preview__item--remove', function() {
            const $removeBtn = $(this);
            const $inputGroup = $removeBtn.closest('.theme-input-group');
            const $combinationImageInput = $inputGroup.find('input[type="hidden"]');
            const combinationImageInputName = $combinationImageInput.attr('name');

            // Find the related "Use Image" button for this input
            const $useImageBtn = $(`.use-variation-image-btn[data-combination-image-input="${combinationImageInputName}"]`);

            // Show the button when image is removed
            $useImageBtn.removeClass('hidden');
        });

        // Also handle when user selects a new image via media manager
        $(document).on('click', '.set-files', function() {
            // Wait for the modal to close and values to be set
            setTimeout(() => {
                $('.theme-input-group').each(function() {
                    const $inputGroup = $(this);
                    const $combinationImageInput = $inputGroup.find('input[type="hidden"]');
                    const combinationImageInputName = $combinationImageInput.attr('name');
                    const imageValue = $combinationImageInput.val();

                    // Find the related "Use Image" button
                    const $useImageBtn = $(`.use-variation-image-btn[data-combination-image-input="${combinationImageInputName}"]`);

                    if (imageValue) {
                        $useImageBtn.addClass('hidden');
                    } else {
                        $useImageBtn.removeClass('hidden');
                    }
                });
            }, 100);
        });

        // Handle file input change (when new file is uploaded)
        $(document).on('change', '.theme-input--file-upload input[type="hidden"]', function() {
            const $input = $(this);
            const inputName = $input.attr('name');
            const imageValue = $input.val();

            // Find the related "Use Image" button
            const $useImageBtn = $(`.use-variation-image-btn[data-combination-image-input="${inputName}"]`);

            if (imageValue) {
                $useImageBtn.addClass('hidden');
            }
        });
    });
</script>
