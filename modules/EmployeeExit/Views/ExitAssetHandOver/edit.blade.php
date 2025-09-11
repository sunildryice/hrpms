@extends('layouts.container')

@section('title', 'Edit Asset HandOver')

@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
            // $('#goodRequestAssetsTable').DataTable({});
            let handoverCount = @json($handoverCount);
            $('#assetTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('profile.assets.index') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                    {data: 'asset_number', name: 'asset_number'},
                    {data: 'item_name', name: 'item_name'},
                    {data: 'remarks', name: 'remarks'},
                    {data: 'status', name: 'status'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'sticky-col'
                    },
                ]
            });

            $('#exitHandOverNoteEditForm').on('submit',(e) => {
              e.preventDefault();
              let btnVal = 'save';
              const submitButton = $('[name="btn"]:focus').data('submit');
              if(submitButton == 'submit'){
                   btnVal = 'submit';
                   console.log(handoverCount)
                    if(handoverCount != 0){
                        toastr.warning("Please Handover All Assets before Submitting", 'Warning', {
                            timeOut: 2000
                        });
                        return;
                    }
              }
              $("<input />").attr("type", "hidden")
                    .attr("name", "btn")
                    .attr("value", btnVal)
                    .appendTo('#exitHandOverNoteEditForm');
              e.currentTarget.submit();
            })
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                                           class="text-decoration-none text-dark">Home</a></li>
                            {{--                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a></li>--}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>
        <section class="registration">

            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                        <ul class="m-0 list-unstyled">
                            <li class="nav-item"><a
                                    href="@if($authUser->can('update', $exitHandOverNote))
                                            {{route('exit.employee.handover.note.edit')}}
                                          @else
                                            {{route('exit.employee.handover.note.show')}}
                                          @endif" class="nav-link text-decoration-none"><i
                                    class="nav-icon bi-info-circle"></i> Handover Note</a></li>
                            <li class="nav-item"><a href="#" class="nav-link active text-decoration-none"><i
                                        class="nav-icon bi-people"></i> Asset Handover</a></li>
                            <li class="nav-item"><a
                                            href= "@if ($authUser->can('update', $exitInterview)) {{ route('exit.employee.interview.edit') }} @else
                                            {{ route('exit.employee.interview.show') }} @endif"
                                            class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i> Exit
                                            interview</a>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <form action="{{ route('exit.employee.handover.asset.update', $exitAssetHandover->employee_id) }}"
                            id="exitHandOverNoteEditForm" method="post"
                            enctype="multipart/form-data" autocomplete="off">
                            @method('PUT')
                            @csrf
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="assetTable">
                                    <thead>
                                    <tr>
                                        <th>{{ __('label.sn') }}</th>
                                        <th>{{ __('label.asset-number') }}</th>
                                        <th>{{ __('label.item-name') }}</th>
                                        <th>{{ __('label.remarks') }}</th>
                                        <th>{{__('label.status')}}</th>
                                        <th>{{ __('label.action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                
                            </div>
                            <div class="row mt-3">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="approver_id" class="form-label required-label">Send To</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    @php $selectedApproverId = old('approver_id') ?: $exitAssetHandover->approver_id; @endphp
                                    <select name="approver_id" class="select2 form-control
                                        @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                                        <option value="">Select an approver</option>
                                        @foreach($approvers as $approver)
                                            <option
                                                value="{{ $approver->id }}" {{$approver->id == $selectedApproverId ? "selected":""}}>{{ $approver->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('approver_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="approver_id">
                                                {!! $errors->first('approver_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm" data-submit="save">Update
                            </button>
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm" data-submit="submit">
                                Submit
                            </button>
                            <a href="{!! route('advance.requests.index') !!}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
            </div>

        </section>
@stop
