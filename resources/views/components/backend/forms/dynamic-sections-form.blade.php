@php
    $langKey = $langKey ?? 'en-US';
    $sectionsData = json_decode(getSetting('homepage_dynamic_sections'), true) ?: ['sections' => []];
    $sections = $sectionsData['sections'] ?? [];
@endphp

<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Dynamic Sections') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            {{-- Add New Section Form --}}
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Section Name --}}
                <div class="mb-4">
                    <h4 class="font-semibold mb-3">{{ translate('Add New Section') }}</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label fw-bold">{{ translate('Section Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="new_section_name" class="form-control"
                                placeholder="e.g., Hero Slider" required>
                        </div>
                        <div>
                            <label class="form-label fw-bold">{{ translate('Section Type') }} <span class="text-danger">*</span></label>
                            <select name="new_section_type" class="form-select" required>
                                <option value="slider" selected>{{ translate('Slider') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-3">
                        <button type="submit" name="action" value="add_section"
                            class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>{{ translate('Add Section') }}
                        </button>
                    </div>
                </div>
            </form>

            <hr class="my-6 border-gray-200">

            {{-- Existing Sections --}}
            @if(count($sections) > 0)
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-lg">{{ translate('Existing Sections') }}</h4>
                        <span class="text-sm text-muted">{{ count($sections) }} {{ translate('section') }}{{ count($sections) > 1 ? 's' : '' }}</span>
                    </div>

                    <div class="grid gap-3">
                        @foreach($sections as $index => $section)
                            <div class="group border border-gray-200 dark:border-gray-700 rounded-xl p-5 bg-white dark:bg-gray-800 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                                        <i class="fas fa-images text-xl"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $section['name'] ?? 'Unnamed Section' }}</h5>
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <i class="fas fa-tag text-xs mr-1"></i>{{ ucfirst($section['type'] ?? '') }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($section['enabled'] ?? false) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ ($section['enabled'] ?? false) ? 'bg-green-500' : 'bg-gray-400' }} mr-1.5"></span>
                                                {{ ($section['enabled'] ?? false) ? translate('Enabled') : translate('Disabled') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons Below --}}
                                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    {{-- Enable/Disable Toggle --}}
                                    <form action="{{ route('admin.general-settings.update') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="section_index" value="{{ $index }}">
                                        <input type="hidden" name="action" value="update_section">
                                        <x-backend.inputs.checkbox
                                            toggler="true"
                                            name="section_enabled_{{ $index }}"
                                            value="1"
                                            :isChecked="($section['enabled'] ?? false)"
                                            onchange="this.form.submit()"
                                        />
                                    </form>
                                    {{-- Edit Button --}}
                                    <button type="button" class="w-10 h-10 flex items-center justify-center btn btn-sm btn-primary shadow-md hover:shadow-lg rounded-lg transition-all"
                                        onclick="openEditModal({{ $index }}, '{{ $section['type'] ?? '' }}')"
                                        title="{{ translate('Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- Delete Button --}}
                                    <form action="{{ route('admin.general-settings.update') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="section_index" value="{{ $index }}">
                                        <button type="submit" name="action" value="delete_section"
                                            class="w-10 h-10 flex items-center justify-center btn btn-sm btn-outline-danger shadow-md hover:shadow-lg rounded-lg transition-all"
                                            onclick="return confirm('{{ translate('Are you sure you want to delete this section?') }}')"
                                            title="{{ translate('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Edit Modal - Custom Modal --}}
            <div id="editSectionModal" class="fixed inset-0 z-[39] hidden bg-black bg-opacity-60 transition-opacity duration-300">
                <form id="editSectionForm" class="space-y-4">
                    <div class="h-screen flex items-center justify-center p-4">
                        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl w-full max-w-4xl shadow-2xl max-h-[95vh] overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white" id="editSectionModalLabel">{{ translate('Edit Section') }}</h2>
                                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto p-6">
                                <input type="hidden" name="section_index" id="editSectionIndex">
                                <input type="hidden" name="action" value="update_section">

                                <div id="modalSectionContent" class="space-y-6">
                                    {{-- Dynamic content will be loaded here --}}
                                </div>
                            </div>

                            <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <div class="flex justify-end space-x-3">
                                    <x-backend.inputs.button
                                        type="button"
                                        variant="secondary"
                                        buttonText="Cancel"
                                        onclick="closeEditModal()"
                                    />
                                    <x-backend.inputs.button
                                        type="submit"
                                        variant="primary"
                                        buttonText="Save Changes"
                                        onclick="submitEditModal(event)"
                                    >
                                        <i class="fas fa-save mr-2"></i>
                                    </x-backend.inputs.button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <style>
                /* Ensure media manager modal appears above edit modal */
                .micromodal-slide[aria-hidden="false"] {
                    z-index: 9999 !important;
                }
            </style>

            <script>
                const sectionsData = @json($sections);
                let imageCounter = 0;

                function openEditModal(index, type) {
                    const section = sectionsData[index];

                    document.getElementById('editSectionIndex').value = index;
                    document.getElementById('editSectionModalLabel').textContent = '{{ translate('Edit Section') }}: ' + (section.name || 'Unnamed Section');

                    let content = '';

                    if (type === 'slider') {
                        const images = section.config && section.config.images ? section.config.images : [];
                        const title = section.config && section.config.title ? section.config.title : '';
                        const subtitle = section.config && section.config.subtitle ? section.config.subtitle : '';
                        imageCounter = images.length;

                        content = `
                            <div class="space-y-6">
                                {{-- Section Title and Subtitle --}}
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                    <h6 class="font-semibold text-lg mb-3 flex items-center">
                                        <i class="fas fa-heading mr-3 text-gray-700 dark:text-gray-300"></i>
                                        {{ translate('Section Content') }}
                                    </h6>
                                    <div class="space-y-4">
                                        <div>
                                            <x-backend.inputs.text
                                                name="slider_title_${index}"
                                                value="${title}"
                                                placeholder="e.g., Featured Collection"
                                                :labelInline="false"
                                            />
                                        </div>
                                        <div>
                                            <x-backend.inputs.text
                                                name="slider_subtitle_${index}"
                                                value="${subtitle}"
                                                placeholder="e.g., Check out our amazing products"
                                                :labelInline="false"
                                            />
                                        </div>
                                    </div>
                                </div>

                                {{-- Slider Images --}}
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                    <h6 class="font-semibold text-lg mb-3 flex items-center">
                                        <i class="fas fa-images mr-3 text-gray-700 dark:text-gray-300"></i>
                                        {{ translate('Slider Images') }}
                                    </h6>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ translate('Upload images for your slider. You can add multiple images.') }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ translate('Recommended: 1920x600 pixels') }}</p>
                                </div>

                                <div id="slider-images-wrapper" class="space-y-4">
                                    ${images.map((img, imgIndex) => `
                                        <div class="slider-image-item border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 hover:border-gray-400 transition-colors">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ translate('Image') }} ${imgIndex + 1}</span>
                                                <button type="button" onclick="removeImage(this)" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="theme-input-group theme-input--file-upload !block no-label">
                                                <div class="theme-input-wrapper">
                                                    <div class="flex">
                                                        <div class="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0" tabindex="0"
                                                            data-micromodal-trigger="media-manager-modal">
                                                            <div class="px-6 py-3 bg-primary text-white rounded-md rounded-e-none">{{ translate('BROWSE') }}</div>
                                                            <span class="media-count py-2 flex items-center text-muted">${img.url ? '{{ translate('1 file selected') }}' : '{{ translate('No Selected Item') }}'}</span>
                                                            <input type="hidden" name="slider_image_urls[${index}][]" class="hidden" value="${img.url || ''}" accept="image/*"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>

                                <button type="button" onclick="addImageField(${index})"
                                    class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:border-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all font-medium">
                                    <i class="fas fa-plus-circle mr-2"></i>{{ translate('Add More Images') }}
                                </button>
                            </div>
                        `;
                    }

                    document.getElementById('modalSectionContent').innerHTML = content;
                    document.getElementById('editSectionModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    // Register new triggers with MicroModal without re-initializing
                    if (typeof MicroModal !== 'undefined') {
                        // Just register the new triggers, don't re-init everything
                        const newTriggers = document.querySelectorAll('#modalSectionContent [data-micromodal-trigger="media-manager-modal"]');
                        newTriggers.forEach(trigger => {
                            trigger.addEventListener('click', function(e) {
                                e.preventDefault();
                                MicroModal.show('media-manager-modal', {
                                    onShow: (modal, triggeredBy) => {
                                        if (modal.id === 'media-manager-modal' && typeof mediaManager !== 'undefined') {
                                            mediaManager.init(triggeredBy);
                                        }
                                    }
                                });
                            });
                        });
                    }
                }

                function addImageField(index) {
                    imageCounter++;
                    const wrapper = document.getElementById('slider-images-wrapper');
                    const newField = document.createElement('div');
                    newField.className = 'slider-image-item border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 hover:border-gray-400 transition-colors';
                    newField.innerHTML = `
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ translate('Image') }} ${imageCounter}</span>
                            <button type="button" onclick="removeImage(this)" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="theme-input-group theme-input--file-upload !block no-label">
                            <div class="theme-input-wrapper">
                                <div class="flex">
                                    <div class="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0" tabindex="0"
                                        data-micromodal-trigger="media-manager-modal">
                                        <div class="px-6 py-3 bg-primary text-white rounded-md rounded-e-none">{{ translate('BROWSE') }}</div>
                                        <span class="media-count py-2 flex items-center text-muted">{{ translate('No Selected Item') }}</span>
                                        <input type="hidden" name="slider_image_urls[${index}][]" class="hidden" value="" accept="image/*"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    wrapper.appendChild(newField);

                    // Register new trigger with MicroModal
                    const newTrigger = newField.querySelector('[data-micromodal-trigger="media-manager-modal"]');
                    if (newTrigger && typeof MicroModal !== 'undefined') {
                        newTrigger.addEventListener('click', function(e) {
                            e.preventDefault();
                            MicroModal.show('media-manager-modal', {
                                onShow: (modal, triggeredBy) => {
                                    if (modal.id === 'media-manager-modal' && typeof mediaManager !== 'undefined') {
                                        mediaManager.init(triggeredBy);
                                    }
                                }
                            });
                        });
                    }
                }

                function removeImage(button) {
                    const imageItem = button.closest('.slider-image-item');
                    imageItem.remove();

                    // Re-number the remaining images
                    const items = document.querySelectorAll('.slider-image-item');
                    items.forEach((item, index) => {
                        const label = item.querySelector('.text-sm.font-medium');
                        if (label) {
                            label.textContent = '{{ translate('Image') }} ' + (index + 1);
                        }
                    });
                }

                function closeEditModal() {
                    document.getElementById('editSectionModal').classList.add('hidden');
                    document.body.style.overflow = '';
                }

                function submitEditModal(event) {
                    event.preventDefault();

                    // Get the form element
                    const form = document.getElementById('editSectionForm');

                    // Create a new form element to submit
                    const submitForm = document.createElement('form');
                    submitForm.method = 'POST';
                    submitForm.action = '{{ route('admin.general-settings.update') }}';
                    submitForm.enctype = 'multipart/form-data';

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    submitForm.appendChild(csrfToken);

                    // Add action
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'update_section';
                    submitForm.appendChild(actionInput);

                    // Add section index
                    const indexInput = document.createElement('input');
                    indexInput.type = 'hidden';
                    indexInput.name = 'section_index';
                    indexInput.value = document.getElementById('editSectionIndex').value;
                    submitForm.appendChild(indexInput);

                    // Copy all inputs from the modal form
                    const modalInputs = form.querySelectorAll('input, textarea, select');
                    modalInputs.forEach(input => {
                        // Include all inputs except submit buttons
                        if (input.type !== 'submit' && input.name) {
                            // Skip empty hidden inputs, but include slider_image_urls even if hidden
                            if (input.type === 'hidden' && !input.value && !input.name.includes('slider_image_urls')) {
                                return; // Skip this input
                            }
                            const clonedInput = input.cloneNode(true);
                            submitForm.appendChild(clonedInput);
                        }
                    });

                    // Submit the form
                    document.body.appendChild(submitForm);
                    submitForm.submit();
                }

                // Close modal when clicking outside
                document.getElementById('editSectionModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeEditModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        const modal = document.getElementById('editSectionModal');
                        if (!modal.classList.contains('hidden')) {
                            closeEditModal();
                        }
                    }
                });
            </script>

            @if(count($sections) == 0)
                <div class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shadow-lg">
                        <i class="fas fa-layer-group text-3xl text-gray-700 dark:text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ translate('No sections created yet') }}</h5>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ translate('Add your first section to get started') }}</p>
                    <div class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ translate('Use the form above to create a new section') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
