@extends('layout.containerform')

@section('title', 'Add Office')

@section('footer_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebar li').removeClass('active');
            $('#sidebar a').removeClass('active');
            $('#sidebar').find('#configuration').addClass('active');
            $('#sidebar').find('#office').addClass('active');
            $("#currency_types").select2();

            $('#officeAddForm').find('select.select_search').select2();

            $('#officeAddForm').formValidation({
                framework: 'bootstrap',
                excluded: ':disabled',
                icon: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    office_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'The office type is required'
                            },
                        }
                    },
                    office_name: {
                        validators: {
                            notEmpty: {
                                message: 'The office name is required.'
                            }
                        }
                    },
                    location: {
                        validators: {
                            notEmpty: {
                                message: 'The location is required.'
                            }
                        }
                    },
                    email_address: {
                        validators: {
                            emailAddress: {message: 'The value is not a valid email address'},
                            stringLength: {max: 512, message: 'Cannot exceed 512 characters'},
                        }
                    },
                    phone_number: {
                        validators: {
                            regexp: {
                                regexp: /^([0-9\(\)\/\+ \-]*)$/,
                                message: 'The value is not a valid phone number'
                            }
                        }
                    },
                    fax_number: {
                        validators: {
                            regexp: {
                                regexp: /^([0-9\(\)\/\+ \-]*)$/,
                                message: 'The value is not a valid fax number'
                            }
                        }
                    },
                    logo: {
                        validators: {
                            file: {
                                extension: 'jpg,jpeg,png',
                                message: 'The selected file is not valid.'
                            }
                        }
                    },
                }
            });
        });
    </script>
@endsection
@section('dynamicdata')

    <div class="row">
        <div class="col-md-12">

            <div data-collapsed="0" class="panel">

                <header class="panel-heading">
                    Add Office
                </header>

                <div class="panel-body">

                    @include('layout.alert')

                    <form id="officeAddForm" action="{{ route('configuration.office.store') }}" method="post" enctype="multipart/form-data">
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Office Type</label>
                            <select name="office_type_id" class="form-control">
                                <option value="">Select Office Type</option>
                                @foreach($officeTypes as $id=>$office_type)
                                    <option value="{{ $id }}">{{ $office_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Parent Office</label>
                            <select name="parent_id" class="form-control select_search parent_id">
                                <option value="0">Parent Itself</option>
                                @foreach($offices as $id=>$office)
                                    <option value="{{ $id }}">{{ $office }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Office Name *</label>
                            <input type="text" name="office_name" class="form-control office_name"
                                   value="{!! old('office_name') !!}"/>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Email Address</label>
                            <input type="email" name="email_address" class="form-control email_address"
                                   value="{!! old('email_address') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Location *</label>
                            <input type="text" name="location" class="form-control location"
                                   value="{!! old('location') !!}"/>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control phone_number"
                                   value="{!! old('phone_number') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Fax Number</label>
                            <input type="text" name="fax_number" class="form-control fax_number"
                                   value="{!! old('fax_number') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Procurement Form Prefix *</label>
                            <input type="text" name="purchase_request_prefix"
                                   class="form-control purchase_request_prefix"
                                   value="{!! old('purchase_request_prefix') !!}"/>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Sourcing Prefix *</label>
                            <input type="text" name="sourcing_prefix" class="form-control sourcing_prefix"
                                   value="{!! old('sourcing_prefix') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Contract Prefix *</label>
                            <input type="text" name="contract_prefix" class="form-control contract_prefix"
                                   value="{!! old('contract_prefix') !!}"/>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Purchase Order Prefix *</label>
                            <input type="text" name="purchase_order_prefix" class="form-control purchase_order_prefix"
                                   value="{!! old('purchase_order_prefix') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">GSRN Prefix *</label>
                            <input type="text" name="grn_prefix" class="form-control grn_prefix"
                                   value="{!! old('grn_prefix') !!}"/>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">BPA Prefix </label>
                            <input type="text" name="bpa_prefix" class="form-control bpa_prefix"
                                   value="{!! old('bpa_prefix') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">TRF Prefix *</label>
                            <input type="text" name="trf_prefix" class="form-control trf_prefix"
                                   value="{!! old('trf_prefix') !!}"/>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">VRF Prefix </label>
                            <input type="text" name="vrf_prefix" class="form-control vrf_prefix"
                                   value="{!! old('vrf_prefix') !!}"/>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Leave Prefix </label>
                            <input type="text" name="leave_prefix" class="form-control leave_prefix"
                                   value="{!! old('leave_prefix') !!}"/>
                        </div>
                        <div class="clearfix"></div>


                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Currency Types *</label>
                            <select name="currency_types[]" id="currency_types" style="width:300px"  multiple class="populate">
                                @foreach($currencyTypes as $currencyType)
                                    <option value="{{ $currencyType->id }}">{{ $currencyType->heading }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 col-xs-11">
                            <label for="">Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Deactive</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>

                        <div class="form-group col-md-12 col-xs-11">
                            <label for="attachment">Logo</label>

                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <img src="{{ asset('storage/noimage.jpg') }}" alt="">
                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail"
                                     style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div>
                                                   <span class="btn btn-white btn-file">
                                                   <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                                   <span class="fileupload-exists"><i
                                                               class="fa fa-undo"></i> Change</span>
                                                   <input type="file" name="logo" class="default"/>
                                                   </span>
                                </div>
                            </div>
                            <span class="label label-danger">NOTE!</span>
                            <span>Valid file extensions are jpg,jpeg and png.</span>
                            <span>Logo size of 120*125 looks better.</span>
                        </div>
                        <div class="clearfix"></div>

                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-info">Submit</button>
                    </form>

                </div>

            </div>

        </div>
    </div>
@stop
