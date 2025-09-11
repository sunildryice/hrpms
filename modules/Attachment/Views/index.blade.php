@push('scripts')
<script>
    // Start - Attachment Scripts Section
    var attachmentTable = $('#attachmentTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{!! route('attachments.index', http_build_query(['modelType' => $modelType, 'modelId' => $modelId])) !!}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'link',
                    name: 'link',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#attachmentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                attachmentTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-attachment-create-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('attachmentCreateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf,xlsx,docx',
                                    type: 'image/jpeg,image/png,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                        attachment_link: {
                            validators: {
                                uri: {
                                    protocol: 'http, https, ftp',
                                    message: 'Please enter a valid link.'
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    let form = document.getElementById('attachmentCreateForm');
                    let data = new FormData(form);
                    let url  = form.getAttribute('action');

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        attachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        $(document).on('click', '.open-attachment-edit-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('attachmentEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf,xlsx,docx',
                                    type: 'image/jpeg,image/png,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                        attachment_link: {
                            validators: {
                                uri: {
                                    protocol: 'http, https, ftp',
                                    message: 'Please enter a valid link.'
                                }
                            }
                        }
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    let form = document.getElementById('attachmentEditForm');
                    let data = new FormData(form);
                    let url  = form.getAttribute('action');

                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        attachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        // End - Attachment Scripts Section
</script>
@endpush


    <div class="card">
        <div class="card-header fw-bold">
            <div class="d-flex align-items-center justify-content-between">
                <span>Attachments</span>
                <button data-toggle="modal"
                    class="btn btn-primary btn-sm open-attachment-create-modal-form"
                    href="{{ route('attachments.create', http_build_query(['modelType' => $modelType, 'modelId' => $modelId])) }}"><i
                        class="bi-plus"></i> Add Attachment
                </button>
                {{-- <button data-toggle="modal"
                    class="btn btn-primary btn-sm open-attachment-create-modal-form"
                    href="{{ route('attachments.create', [$modelType, $modelId]) }}"><i
                        class="bi-plus"></i> Add Attachment
                </button> --}}
            </div>
        </div>
        <div class="container-fluid-s">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="attachmentTable">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col" style="width: 150px">Attachment</th>
                                <th scope="col" style="width: 150px">Link</th>
                                <th scope="col" style="width: 150px">{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
