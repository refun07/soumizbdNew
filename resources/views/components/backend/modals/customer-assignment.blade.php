<div class="modal micromodal-slide" id="customer-assignment-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container lg:min-w-[600px] overflow-auto" role="dialog" aria-modal="true" aria-labelledby="customer-assignment-modal-title">
        <form action="{{ route('admin.customer-logs.store') }}" method="POST">
            @csrf
            <input id="customer-assignment-modal-method" type="hidden" name="_method" value="">
            <header class="modal__header">
                <h2 class="modal__title">
                    {{ translate('Manage') }}: <span class="customer-name"></span>
                </h2>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>
            <main class="modal__content" id="customer-assignment-modal-content"> 
                <input type="hidden" name="customer_id" value="">

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-6">
                        <x-backend.inputs.datepicker label="Reorder Reminder" :labelInline="false" name="reorder_reminder_date"
                            placeholder="Reorder Reminder" value="" :isRequired="false"  :htmlDateInput="true" />
                    </div>
                    <div class="col-span-12 md:col-span-6">
                        <x-backend.inputs.datepicker label="General follow-up" :labelInline="false" name="next_follow_up_date"
                            placeholder="General follow-up" value="" :isRequired="false" :htmlDateInput="true" />
                    </div>

                    <div class="col-span-12">
                        <label class="theme-input-label pt-0">{{ translate('Assigned To') }}</label>
                        <div class="theme-input-wrapper">
                            <select class="theme-input h-auto p-3" name="assigned_to">
                                <option value="">{{ translate('Select User') }}</option>
                                @php
                                    $users = App\Models\User::staffs()->get();
                                @endphp
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-span-12">
                        <x-backend.inputs.textarea label="Status Notes" :labelInline="false" name="status_note"
                            placeholder="" value="" :isRequired="false" />
                    </div>
                    
                    <hr class="col-span-12" />

                    <div class="col-span-12 text-lg">
                        {{ translate("Add New Activity Logs") }}
                    </div>
                     
                    <div class="col-span-12 activity-logs-wrapper">
                        <div class="grid grid-cols-12 gap-4 mb-2 activity-log-item">
                            <div class="col-span-4">
                                <label class="theme-input-label pt-0">{{ translate('Type') }}</label>
                                <div class="theme-input-wrapper">
                                    <select class="theme-input h-auto p-3" name="types[]">
                                        <option value="note">{{ translate('Note') }}</option> 
                                        <option value="call_recording">{{ translate('Call Recording') }}</option> 
                                        <option value="whatsapp_messaging">{{ translate('WhatsApp Messaging') }}</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-span-8">
                                <x-backend.inputs.textarea label="Details" :labelInline="false" name="details[]"
                                    placeholder="Type few words..." :isRequired="false" />
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 text-end">
                        <a href="javascript:void(0);" class="bg-primary text-white rounded-md p-2" data-toggle="add-more"
                            data-content='<div class="activity-log-item grid grid-cols-12 gap-4 mb-2">
                                        <div class="col-span-4">
                                            <label class="theme-input-label pt-0">{{ translate('Type') }}</label>
                                            <div class="theme-input-wrapper">
                                                <select class="theme-input h-auto p-3" name="types[]">
                                                    <option value="note">{{ translate('Note') }}</option> 
                                                    <option value="call_recording">{{ translate('Call Recording') }}</option> 
                                                    <option value="whatsapp_messaging">{{ translate('WhatsApp Messaging') }}</option> 
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-span-8">
                                            <x-backend.inputs.textarea label="Details" :labelInline="false" name="details[]"  :trashBtn="true" trashParent=".activity-log-item"
                                                placeholder="Type few words..." :isRequired="false" />
                                        </div>
                                    </div>'
                            data-target=".activity-logs-wrapper">
                            {{ translate('Add Log') }} 
                        </a>
                    </div> 
                </div> 
            </main>
            <footer class="modal__footer flex justify-end">
                <button type="button" class="modal__btn me-3" data-micromodal-close
                    aria-label="Close this dialog window">{{ translate('Cancel') }}</button>
                <button type="submit"
                    class="modal__btn modal__btn-success button button--primary">{{ translate('Save Changes') }}</button> 
            </footer>
        </form>
    </div>
</div>
