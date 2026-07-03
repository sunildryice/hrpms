@extends('layouts.container')

@section('title', 'Edit Research Uptake & Communication')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#research-communication-index').addClass('active');

            let platformIndex = {{ $researchCommunication->platforms->count() }};

            function toggleSections() {
                var val = $('#type_of_publication').val();
                if (val === 'Journal') {
                    $('#publicationDetailsSection').show();
                    $('#socialMediaPostSection').hide();
                    try { fv.enableValidator('publication_title'); } catch(e) {}
                    try { fv.enableValidator('date_of_publication'); } catch(e) {}
                    try { fv.disableValidator('type_of_post'); } catch(e) {}
                    try { fv.disableValidator('date_posted'); } catch(e) {}
                    try { fv.disableValidator('post_title'); } catch(e) {}
                } else if (val === 'Other') {
                    $('#publicationDetailsSection').hide();
                    $('#socialMediaPostSection').show();
                    try { fv.disableValidator('publication_title'); } catch(e) {}
                    try { fv.disableValidator('date_of_publication'); } catch(e) {}
                    try { fv.enableValidator('type_of_post'); } catch(e) {}
                    try { fv.enableValidator('date_posted'); } catch(e) {}
                    try { fv.enableValidator('post_title'); } catch(e) {}
                } else {
                    $('#publicationDetailsSection').hide();
                    $('#socialMediaPostSection').hide();
                    try { fv.disableValidator('publication_title'); } catch(e) {}
                    try { fv.disableValidator('date_of_publication'); } catch(e) {}
                    try { fv.disableValidator('type_of_post'); } catch(e) {}
                    try { fv.disableValidator('date_posted'); } catch(e) {}
                    try { fv.disableValidator('post_title'); } catch(e) {}
                }
            }

            const form = document.getElementById('researchCommunicationUpdateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    type_of_publication: {
                        validators: {
                            notEmpty: {
                                message: 'The type of publication is required.'
                            }
                        }
                    },
                    publication_title: {
                        validators: {
                            notEmpty: {
                                message: 'The publication title is required.'
                            }
                        }
                    },
                    date_of_publication: {
                        validators: {
                            notEmpty: {
                                message: 'The date of publication is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
                            }
                        }
                    },
                    type_of_post: {
                        validators: {
                            notEmpty: {
                                message: 'The type of post is required.'
                            }
                        }
                    },
                    date_posted: {
                        validators: {
                            notEmpty: {
                                message: 'The date posted is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
                            }
                        }
                    },
                    post_title: {
                        validators: {
                            notEmpty: {
                                message: 'The post title is required.'
                            }
                        }
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
                        validating: 'bi bi-arrow-repeat'
                    }),
                }
            });

            $('#addPlatformBtn').on('click', function() {
                let platform = $('#platform_select').val();
                if (!platform) {
                    toastr.error('Please select a platform.', 'Error');
                    return;
                }

                let existing = $('#platformsTableBody').find('input[name$="[platform]"]').map(function() {
                    return $(this).val();
                }).get();

                if (existing.includes(platform)) {
                    toastr.error('Platform "' + platform + '" already added.', 'Error');
                    return;
                }

                let row = '<tr>';
                row += '<td>' + platform + '<input type="hidden" name="platforms[' + platformIndex + '][platform]" value="' + platform + '"></td>';
                row += '<td><input type="number" class="form-control form-control-sm" name="platforms[' + platformIndex + '][views]" value="0" min="0"></td>';
                row += '<td><input type="number" class="form-control form-control-sm" name="platforms[' + platformIndex + '][link_clicks]" value="0" min="0"></td>';
                row += '<td><input type="number" class="form-control form-control-sm" name="platforms[' + platformIndex + '][reactions]" value="0" min="0"></td>';
                row += '<td><input type="number" class="form-control form-control-sm" name="platforms[' + platformIndex + '][shares]" value="0" min="0"></td>';
                row += '<td><input type="number" class="form-control form-control-sm" name="platforms[' + platformIndex + '][comments]" value="0" min="0"></td>';
                row += '<td><button type="button" class="btn btn-danger btn-sm remove-platform"><i class="bi-trash"></i></button></td>';
                row += '</tr>';

                $('#platformsTableBody').append(row);
                platformIndex++;

                $('#platform_select').val('').trigger('change');
            });

            $(document).on('click', '.remove-platform', function() {
                $(this).closest('tr').remove();
            });

            $('[name="date_of_publication"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function (e) {
                fv.revalidateField('date_of_publication');
            });

            $('[name="date_posted"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function (e) {
                fv.revalidateField('date_posted');
            });

            toggleSections();

            $('#type_of_publication').on('change', toggleSections);

        });

    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a href="{{route('research-communication.index')}}" class="text-decoration-none">Research Uptake & Communication</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('research-communication.update', $researchCommunication->id)}}" method="POST" id="researchCommunicationUpdateForm">
                @csrf
                @method('put')
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="type_of_publication">Type of Publication</label>
                                <select class="form-select select2" name="type_of_publication" id="type_of_publication">
                                    <option value="">Select Type of Publication</option>
                                    <option value="Journal" {{$researchCommunication->type_of_publication == 'Journal' ? 'selected' : ''}}>Research Article</option>
                                    <option value="Other" {{$researchCommunication->type_of_publication == 'Other' ? 'selected' : ''}}>Other Posts</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3" id="publicationDetailsSection">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">Publication Details</h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="publication_title">Publication Title</label>
                                <input class="form-control" type="text" name="publication_title" id="publication_title" value="{{$researchCommunication->publication_title}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="date_of_publication">Date of Publication</label>
                                <input class="form-control" type="text" name="date_of_publication" id="date_of_publication" value="{{$researchCommunication->date_of_publication?->format('Y-m-d')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="herdi_members_involved">HERDI Members Involved</label>
                                <select class="form-select select2" name="herdi_members_involved[]" id="herdi_members_involved" multiple>
                                    <option value="">Select Members</option>
                                    @foreach($employees as $employee)
                                        <option value="{{$employee->id}}" {{in_array($employee->id, $researchCommunication->herdi_members_involved ?? []) ? 'selected' : ''}}>{{$employee->full_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="journal_paper_name">Journal/Paper Name</label>
                                <input class="form-control" type="text" name="journal_paper_name" id="journal_paper_name" value="{{$researchCommunication->journal_paper_name}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="publication_url">Publication URL</label>
                                <input class="form-control" type="text" name="publication_url" id="publication_url" value="{{$researchCommunication->publication_url}}">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card" id="socialMediaPostSection">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">Social Media Post Details</h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="type_of_post">Type of Post</label>
                                <select class="form-select select2" name="type_of_post" id="type_of_post">
                                    <option value="">Select Type of Post</option>
                                    @foreach($postTypes as $type)
                                        <option value="{{$type->value}}" {{$researchCommunication->type_of_post == $type->value ? 'selected' : ''}}>{{$type->label()}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="date_posted">Date Posted</label>
                                <input class="form-control" type="text" name="date_posted" id="date_posted" value="{{$researchCommunication->date_posted?->format('Y-m-d')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label required-label" for="post_title">Post Title</label>
                                <input class="form-control" type="text" name="post_title" id="post_title" value="{{$researchCommunication->post_title}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="project_id">Project</label>
                                <select class="form-select select2" name="project_id" id="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{$researchCommunication->project_id == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-2">Platform Performance</h6>
                        <div class="row mb-2 align-items-end">
                            <div class="col-lg-3">
                                <label class="form-label" for="platform_select">Add Platform</label>
                                <select class="form-select form-select-sm select2" id="platform_select">
                                    <option value="">Select Platform</option>
                                    @foreach(['Bluesky', 'Facebook', 'LinkedIn', 'Twitter', 'Website', 'X', 'Youtube'] as $platform)
                                        <option value="{{$platform}}">{{$platform}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-9">
                                <button type="button" class="btn btn-sm btn-primary mt-4" id="addPlatformBtn"><i class="bi-plus"></i> Add</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Platform</th>
                                        <th>Views</th>
                                        <th>Link Clicks</th>
                                        <th>Reactions</th>
                                        <th>Shares</th>
                                        <th>Comments</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="platformsTableBody">
                                    @foreach($researchCommunication->platforms as $i => $p)
                                    <tr>
                                        <td>{{$p->platform}}<input type="hidden" name="platforms[{{$i}}][platform]" value="{{$p->platform}}"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="platforms[{{$i}}][views]" value="{{$p->views}}" min="0"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="platforms[{{$i}}][link_clicks]" value="{{$p->link_clicks}}" min="0"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="platforms[{{$i}}][reactions]" value="{{$p->reactions}}" min="0"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="platforms[{{$i}}][shares]" value="{{$p->shares}}" min="0"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="platforms[{{$i}}][comments]" value="{{$p->comments}}" min="0"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-platform"><i class="bi-trash"></i></button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    <a href="{{route('research-communication.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
