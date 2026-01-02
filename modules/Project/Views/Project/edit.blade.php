@extends('layouts.container')

@section('title', 'Edit Project')

@section('page_css')
    <style>
        #deliverables-table th,
        #deliverables-table td {
            border-color: #dee2e6;
        }

        .deliverable-row .btn {
            padding-inline: .35rem;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            // Highlight Project nav; rely on global datepicker init
            $('#navbarVerticalMenu').find('#project-index').addClass('active');

            $('select[name="members[]"]').select2({
                placeholder: "Select Members",
                width: '100%',
                closeOnSelect: false,
            });

            // Persist side-tab selection like Employee edit
            var queryTab = @json(request()->query('tab'));
            var selectedTab = queryTab ?? localStorage.getItem('project-edit-tab') ?? 'projectInformation';
            if ($("[data-tag='" + selectedTab + "']").length == 0 || $('#' + selectedTab).length == 0) {
                selectedTab = 'projectInformation';
            }

            // Tab click handler
            $('.step-item').on('click', function(e) {
                e.preventDefault();
                selectedTab = $(this).data('tag');
                localStorage.setItem('project-edit-tab', selectedTab);
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + selectedTab).addClass('active').removeClass('hide');
            });

            // Initialize current tab
            $(function() {
                $("[data-tag='" + selectedTab + "']").addClass('active');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + selectedTab).addClass('active').removeClass('hide');
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('project.index') }}" class="text-decoration-none text-dark">
                                Project
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">
                    Edit Project
                </h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="rounded shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                    <ul class="m-0 list-unstyled">
                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="projectInformation">
                                <i class="nav-icon bi-info-circle"></i> Project Information
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="projectMembers">
                                <i class="nav-icon bi-people"></i> Members
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="projectActivities">
                                <i class="nav-icon bi-activity"></i> Activities
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link step-item text-decoration-none" data-tag="projectExtensions">
                                <i class="nav-icon bi-calendar-event"></i> Extensions
                            </a>
                        </li>
                    </ul>

                </div>
            </div>
            <div class="col-lg-9">
                <div class="c-tabs-content active" id="projectInformation">
                    @include('Project::ProjectInformation.edit')
                </div>
                <div class="c-tabs-content" id="projectMembers">
                    @include('Project::Members.edit')
                </div>
                <div class="c-tabs-content" id="projectActivities">
                    @include('Project::Activities.edit')
                </div>
                <div class="c-tabs-content" id="projectExtensions">
                    <!-- Extensions content goes here -->
                </div>
            </div>
        </div>
    </section>
@endsection
