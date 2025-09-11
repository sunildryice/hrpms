@extends('layouts.container')

@section('title', 'Memo')

@section('page_css')
    <style>
        /*.ck-content table {
            table-layout: fixed;
        }

        .ck-content table td,
        .ck-content table th {
            width: calc(100% / 10);
            max-width: 3000px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .ck-content {
            overflow-x: auto;
        }*/
    </style>
@endsection

@section('page_js')
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#memo-menu').addClass('active');
        });

        ClassicEditor
            .create(document.querySelector('#details-desc'))
            .catch(error => {
                console.error(error);
            });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('memoEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    memo_to: {
                        validators: {
                            notEmpty: {
                                message: 'Memo To field is required.',
                            },
                        },
                    },
                    // memo_through: {
                    //     selector: '#memothrough',
                    //     row: '.col-lg-9',
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Memo Through field is required.',
                    //         },
                    //     },
                    // },
                    // memo_from: {
                    //     selector: '#memofrom',
                    //     row: '.col-lg-9',
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Memo From is required.',
                    //         },
                    //     },
                    // },
                    memo_date: {
                        validators: {
                            notEmpty: {
                                message: 'The memo date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    subject: {
                        validators: {
                            notEmpty: {
                                message: 'Subject field is required.',
                            },
                        },
                    },
                    // description: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Brief description field is required.',
                    //         },
                    //     },
                    // },
                    // enclosure: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Enclosure field is required.',
                    //         },
                    //     },
                    // },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
                                message: 'The selected file is not valid type or must not be greater than 2 MB.',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).find('[name="memo_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('memo_date');
            });
            $(form).on('change', '[name="memo_to"]', function(e) {
                fv.revalidateField('memo_to');
            }).on('change', '[name="description"]', function(e) {
                fv.revalidateField('description');
            });
            // .on('change', '#memothrough', function(e) {
            //     fv.revalidateField('memo_through');
            // });
            // .on('change', '#memofrom', function(e) {
            //     fv.revalidateField('memo_from');
            // });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 page-header border-bottom">
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                    class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('memo.index') }}" class="text-decoration-none">Memo
                                    List</a></li>
                            <li class="breadcrumb-item" aria-current="page">Edit Memo</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">Edit Memo</h4>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Edit Memo</h3>
                        </div>
                        <form action="{!! route('memo.update', $memo->id) !!}" method="post" enctype="multipart/form-data"
                            id="memoEditForm" autocomplete="off">
                            <div class="card-body">
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="memto" class="form-label required-label">To
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select
                                            class="form-control select2 @if ($errors->has('memo_to')) is-invalid @endif"
                                            name="memo_to" id="memto">
                                            <option value="">Select To</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    @if ($user->id == $selectedMemoTo[0]) selected @endif>
                                                    {{ $user->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('memo_to'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="memo_to">
                                                    {!! $errors->first('memo_to') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="memothrough" class="m-0">Through
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select
                                            class="form-select select2 @if ($errors->has('memo_through')) is-invalid @endif"
                                            name="memo_through[]" id="memothrough">
                                            <option value="">Select Through</option>
                                            @foreach ($supervisors as $supervisor)
                                                <option value="{{ $supervisor->id }}"
                                                    @if (in_array($supervisor->id, $selectedMemoThrough)) selected @endif>
                                                    {{ $supervisor->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('memo_through'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="memo_through">
                                                    {!! $errors->first('memo_through') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {{-- <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="memofrom" class="form-label required-label">From
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select class="form-select select2 @if ($errors->has('memo_from')) is-invalid @endif" name="memo_from[]" id="memofrom">
                                            <option value="">Select From</option>
                                            @foreach ($employees as $employee)
                                                @if ($employee->user)
                                                    <option value="{{ $employee->id }}"
                                                        @if (in_array($employee->id, $selectedMemoFrom)) selected @endif>
                                                        {{ $employee->getFullName() }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('memo_from'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="memo_from">
                                                    {!! $errors->first('memo_from') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div> --}}
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="atvcde" class="form-label required-label">Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="input-group has-validation">
                                            {{-- <div class="input-group-append">
                                                <span class="input-group-text required-label">Start</span>
                                            </div> --}}
                                            <input type="text"
                                                class="form-control  @if ($errors->has('memo_date')) is-invalid @endif"
                                                name="memo_date" value="{{ date('Y-m-d', strtotime($memo->memo_date)) }}"
                                                autofocus="" readonly>
                                        </div>
                                        @if ($errors->has('memo_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="memo_date">
                                                    {!! $errors->first('memo_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="prblmdsc" class="form-label required-label">Subject
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="subject"
                                            class="form-control  @if ($errors->has('subject')) is-invalid @endif"
                                            value="{{ $memo->subject }}">
                                        @if ($errors->has('subject'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="subject">
                                                    {!! $errors->first('subject') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="details-desc" class="m-0">Brief Descriptions
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea name="description" id="details-desc" cols="30" rows="6"
                                            class="form-control
                                            @if ($errors->has('description')) is-invalid @endif"
                                            id="editor">
@if ($memo->description)
{{ $memo->description }}
@endif
</textarea>
                                        @if ($errors->has('description'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="description">
                                                    {!! $errors->first('description') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="details-desc" class="m-0">Enclosure
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="enclosure"
                                            class="form-control @if ($errors->has('enclosure')) is-invalid @endif"
                                            value="{{ $memo->enclosure }}">
                                        @if ($errors->has('enclosure'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="enclosure">
                                                    {!! $errors->first('enclosure') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="fileattch" class="m-0">Attach File(s)</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="gap-3 d-flex">
                                            <input type="file"
                                                class="form-control  @if ($errors->has('attachment')) is-invalid @endif"
                                                id="fileattch" name="attachment">
                                            <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                                            @if ($attachment)
                                                <div class="media">
                                                    <a href="{!! asset('storage/' . $memo->attachment) !!}" target="_blank"
                                                        name='attachment_exist' class="fs-5" title="View Attachment">
                                                        <i class="bi bi-file-earmark-medical"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        @if ($errors->has('attachment'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="attachment">
                                                    {!! $errors->first('attachment') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}

                            </div>
                            <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                                <button type="submit" class="btn btn-primary btn-sm" name="btn"
                                    value="save">Save</button>&emsp;
                                <button type="submit" class="btn btn-success btn-sm" name="btn"
                                    value="submit">Submit</button>&emsp;
                                <a href="{!! route('memo.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                    @if ($memo->returnLog()->exists())
                        <div class="card">
                            <div class="card-header text-danger">
                                Return Remarks
                            </div>
                            <div class="card-body">
                                {{ $memo->returnLog->log_remarks }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@stop
