@extends('layouts.container')

@section('title', 'Travel Claim')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#travel-claims-menu').addClass('active');

            const claimForm = document.getElementById('travelClaimEditForm');
            const fv = FormValidation.formValidation(claimForm, {
                fields: {
                    advance_amount: {
                        validators: {
                            numeric: {
                                message: 'The advance amount should be number.',
                            },
                            between: {
                                inclusive: true,
                                min: 0,
                                max: 99999999,
                                message: 'The value must be between 0 to 99999999',
                            },
                        },
                    },
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'The reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is requried',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(claimForm).on('change', '[name="reviewer_id"]', function (e) {
                fv.revalidateField('reviewer_id');
            }).on('change', '[name="advance_amount"]', function (e) {
                advanceAmount = parseFloat($(this).closest('form').find('[name="advance_amount"]').val());
                totalAmount = parseFloat($(this).closest('form').find('#total_amount').text());
                $(this).closest('form').find('[name="refundable_amount"]').val(totalAmount - advanceAmount);
            });

            // Validate agree checkbox on submit
            const agreeCheckbox = document.querySelector('input[name="agree"]');
            fv.on('core.form.valid', function () {
                const clickedButton = document.activeElement;

                if (clickedButton && clickedButton.name === 'btn' && clickedButton.value === 'submit') {
                    if (!agreeCheckbox.checked) {
                        toastr.error('You must certify the declaration before submitting the claim.',
                            'Required');
                        agreeCheckbox.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        agreeCheckbox.focus();
                        const box = agreeCheckbox.closest('.form-check');
                        box.classList.add('border', 'border-danger', 'border-3', 'rounded');
                        setTimeout(() => box.classList.remove('border', 'border-danger', 'border-3',
                            'rounded'), 1000);
                        return false;
                    }
                }
                claimForm.submit();
            });

            function updateClaimTotals(response) {
                const claim = response.travelClaim;

                $('#total_expense_amount').text(claim.total_expense_amount || '0.00');
                $('#total_local_travel_amount').text(claim.total_local_travel_amount || '0.00');
                $('#total_itinerary_amount').text(claim.total_itinerary_amount || '0.00');
                $('#total_amount').text(claim.total_amount || '0.00');
                $('[name="refundable_amount"]').val(claim.refundable_amount || '0.00');
            }

            var expenseTable = $('#expenseTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('travel.claims.expenses.index', $travelClaim->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                    {
                        data: 'expense_date',
                        name: 'expense_date'
                    },
                    {
                        data: 'expense_description',
                        name: 'expense_description'
                    },
                    {
                        data: 'expense_amount',
                        name: 'expense_amount'
                    },
                    {
                        data: 'invoice_bill_number',
                        name: 'invoice_bill_number'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            var claimLocalTravelTable = $('#claimLocalTravelTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('travel.claims.local.travel.index', $travelClaim->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                    data: 'activity',
                    name: 'activity'
                }, {
                    data: 'travel_date',
                    name: 'travel_date'
                },
                    {
                        data: 'purpose',
                        name: 'purpose'
                    },
                    {
                        data: 'departure_place',
                        name: 'departure_place'
                    },
                    {
                        data: 'arrival_place',
                        name: 'arrival_place'
                    },
                    {
                        data: 'travel_fare',
                        name: 'travel_fare'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            var itineraryTable = $('#itineraryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('travel.claims.dsa.index', $travelClaim->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [
                    {
                        data: 'departure_date',
                        name: 'departure_date'
                    },
                    {
                        data: 'breakfast',
                        name: 'breakfast'
                    },
                    {
                        data: 'lunch',
                        name: 'lunch'
                    },
                    {
                        data: 'dinner',
                        name: 'dinner'
                    },
                    {
                        data: 'incident_cost',
                        name: 'incident_cost'
                    },
                    {
                        data: 'total_dsa',
                        name: 'total_dsa'
                    },
                    {
                        data: 'lodging_expense',
                        name: 'lodging_expense'
                    },
                    {
                        data: 'other_expense',
                        name: 'other_expense'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#expenseTable').on('click', '.delete-record', function (e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    updateClaimTotals(response);
                    expenseTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-expense-modal-form', function (e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                    const expenseForm = document.getElementById('travelExpenseForm');
                    $(expenseForm).find(".select2").each(function () {
                        $(this)
                            .wrap("<div class=\"position-relative\"></div>")
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: '100%',
                                dropdownAutoWidth: true
                            });
                    });

                    const fv = FormValidation.formValidation(expenseForm, {
                        fields: {
                            activity_code_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Activity is required',
                                    },
                                },
                            },
                            expense_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The expense date is required',
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The value is not a valid date',
                                    },
                                },
                            },
                            // description: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Description is required',
                            //         },
                            //     },
                            // },
                            // invoice_bill_number: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Invoice / Bill number is required',
                            //         },
                            //     },
                            // },
                            expense_amount: {
                                validators: {
                                    notEmpty: {
                                        message: 'The expense amount is required',
                                    },
                                    numeric: {
                                        message: 'The expense amount should be number.',
                                    },
                                    between: {
                                        inclusive: true,
                                        min: 1,
                                        max: 99999999,
                                        message: 'The value must be between 1 to 99999999',
                                    },
                                },
                            },
                            attachment: {
                                validators: {
                                    file: {
                                        extension: 'jpeg,jpg,png,pdf',
                                        type: 'image/jpeg,image/png,application/pdf',
                                        maxSize: '5097152',
                                        message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                    },
                                },
                            },
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
                    }).on('core.form.valid', function (event) {
                        $url = fv.form.action;
                        $form = fv.form;
                        var formData = new FormData();
                        $('#travelExpenseForm input, #travelExpenseForm select, #travelExpenseForm textarea')
                            .each(
                                function (index) {
                                    var input = $(this);
                                    formData.append(input.attr('name'), input.val());
                                });
                        var attachmentFiles = expenseForm.querySelector(
                            '[name="attachment"]').files;
                        if (attachmentFiles.length > 0) {
                            formData.append('attachment', attachmentFiles[0]);
                        }

                        var successCallback = function (response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            updateClaimTotals(response);
                            expenseTable.ajax.reload();
                        }
                        ajaxSubmitFormData($url, 'POST', formData, successCallback);
                    });

                    $(expenseForm).find('[name="expense_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        startDate: '{!! $travelClaim->travelRequest->departure_date->format('Y-m-d') !!}',
                        endDate: '{!! $travelClaim->travelRequest->return_date->format('Y-m-d') !!}',
                        zIndex: 2048,
                    }).on('change', function (e) {
                        fv.revalidateField('expense_date');
                    });

                    $(expenseForm).on('change', '[name="activity_code_id"]', function (e) {
                        fv.revalidateField('activity_code_id');
                    });
                });
            });

            $('#claimLocalTravelTable').on('click', '.delete-record', function (e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    updateClaimTotals(response);
                    claimLocalTravelTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-local-travel-modal-form', function (e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                    const claimLocalTravelForm = document.getElementById('claimLocalTravelForm');
                    $(claimLocalTravelForm).find(".select2").each(function () {
                        $(this)
                            .wrap("<div class=\"position-relative\"></div>")
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: '100%',
                                dropdownAutoWidth: true
                            });
                    });

                    const fv = FormValidation.formValidation(claimLocalTravelForm, {
                        fields: {
                            activity_code_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Activity is required',
                                    },
                                },
                            },
                            travel_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'The Travel date is required',
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'The value is not a valid date',
                                    },
                                },
                            },
                            purpose: {
                                validators: {
                                    notEmpty: {
                                        message: 'Purpose is required',
                                    },
                                },
                            },
                            travel_fare: {
                                validators: {
                                    notEmpty: {
                                        message: 'The travel fare is required',
                                    },
                                    numeric: {
                                        message: 'The travel fare should be number.',
                                    },
                                    between: {
                                        inclusive: true,
                                        min: 1,
                                        max: 99999999,
                                        message: 'The value must be between 1 to 99999999',
                                    },
                                },
                            },
                            departure_place: {
                                validators: {
                                    notEmpty: {
                                        message: 'The Departure Place is required'
                                    }
                                }
                            },
                            arrival_place: {
                                validators: {
                                    notEmpty: {
                                        message: 'The Arrival Place is required'
                                    }
                                }
                            },
                            attachment: {
                                validators: {
                                    file: {
                                        extension: 'jpeg,jpg,png,pdf',
                                        type: 'image/jpeg,image/png,application/pdf',
                                        maxSize: '2097152',
                                        message: 'The selected file is not valid file or must not be greater than 2 MB.',
                                    },
                                },
                            },
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
                    }).on('core.form.valid', function () {
                        const $url = fv.form.action;
                        const formData = new FormData(claimLocalTravelForm);

                        const successCallback = function (response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message || 'Saved successfully');
                            updateClaimTotals(response);
                            claimLocalTravelTable.ajax.reload();
                        };

                        ajaxSubmitFormData($url, 'POST', formData, successCallback);
                    });

                    $(claimLocalTravelForm).find('[name="travel_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        startDate: '{!! $travelClaim->travelRequest->departure_date->format('Y-m-d') !!}',
                        endDate: '{!! $travelClaim->travelRequest->return_date->format('Y-m-d') !!}',
                        zIndex: 2048,
                    }).on('change', function (e) {
                        fv.revalidateField('travel_date');
                    });
                });
            });

            $('#itineraryTable').on('click', '.delete-record', function (e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    updateClaimTotals(response);
                    itineraryTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $(document).on('click', '.open-itinerary-modal-form', function (e) {
                e.preventDefault();
                $('#claimItineraryModal').find('.modal-content').html('');
                $('#claimItineraryModal').modal('show').find('.modal-content').load($(this).attr('href'),
                    function () {
                        const claimItineraryForm = document.getElementById('claimItineraryForm');

                        $(claimItineraryForm).find(".select2").each(function () {
                            $(this)
                                .wrap("<div class=\"position-relative\"></div>")
                                .select2({
                                    dropdownParent: $(this).parent(),
                                    width: '100%',
                                    dropdownAutoWidth: true
                                });
                        });

                        const fv = FormValidation.formValidation(claimItineraryForm, {
                            fields: {
                                // activity_code_id: {
                                //     validators: {
                                //         notEmpty: {
                                //             message: 'Activity is required',
                                //         },
                                //     },
                                // },
                                // activities: {
                                //     validators: {
                                //         notEmpty: {
                                //             message: 'Activities is required'
                                //         }
                                //     }
                                // },
                                departure_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Departure date is required',
                                        },
                                        date: {
                                            format: 'YYYY-MM-DD',
                                            message: 'The value is not a valid date',
                                        },
                                    },
                                },
                                arrival_date: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Arrival date is required',
                                        },
                                        date: {
                                            format: 'YYYY-MM-DD',
                                            message: 'The value is not a valid date',
                                        },
                                    },
                                },
                                departure_place: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Departure Place is required'
                                        }
                                    }
                                },
                                arrival_place: {
                                    validators: {
                                        notEmpty: {
                                            message: 'The Arrival Place is required'
                                        }
                                    }
                                },
                                attachment: {
                                    validators: {
                                        file: {
                                            extension: 'jpeg,jpg,png,pdf',
                                            type: 'image/jpeg,image/jpg,image/png,application/pdf',
                                            maxSize: 5097152,
                                            message: 'File must be jpeg, jpg, png or pdf and less than 5MB'
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
                                    validating: 'bi bi-arrow-repeat'
                                }),
                            },
                        }).on('core.form.valid', function () {
                            const $url = fv.form.action;
                            const formData = new FormData(claimItineraryForm);

                            const successCallback = function (response) {
                                $('#claimItineraryModal').modal('hide');
                                toastr.success(response.message || 'Saved successfully');
                                updateClaimTotals(response);
                                itineraryTable.ajax.reload();
                            };

                            ajaxSubmitFormData($url, 'POST', formData, successCallback);
                        });

                        const departurePicker = $(claimItineraryForm.querySelector(
                            '[name="departure_date"]'))
                            .datepicker({
                                language: 'en-GB',
                                autoHide: true,
                                format: 'yyyy-mm-dd',
                                startDate: '{{ $travelRequest->departure_date->format('Y-m-d') }}',
                                endDate: '{{ $travelRequest->return_date->format('Y-m-d') }}',
                                zIndex: 2048,
                            }).on('change pick.datepicker', updateCalculations);

                        const arrivalPicker = $(claimItineraryForm.querySelector(
                            '[name="arrival_date"]'))
                            .datepicker({
                                language: 'en-GB',
                                autoHide: true,
                                format: 'yyyy-mm-dd',
                                startDate: '{{ $travelRequest->departure_date->format('Y-m-d') }}',
                                endDate: '{{ $travelRequest->return_date->format('Y-m-d') }}',
                                zIndex: 2048,
                            }).on('change pick.datepicker', updateCalculations);

                        function updateCalculations() {
                            const $form = $(claimItineraryForm);

                            const breakfast = parseFloat($form.find('[name="breakfast"]').val()) || 0;
                            const lunch = parseFloat($form.find('[name="lunch"]').val()) || 0;
                            const dinner = parseFloat($form.find('[name="dinner"]').val()) || 0;
                            const incidental = parseFloat($form.find('[name="incident_cost"]').val()) ||
                                0;

                            const totalDsa = breakfast + lunch + dinner + incidental;
                            $form.find('[name="total_dsa"]').val(totalDsa.toFixed(2));

                            const lodging = parseFloat($form.find('[name="lodging_expense"]').val()) ||
                                0;
                            const other = parseFloat($form.find('[name="other_expense"]').val()) || 0;
                            const totalAmount = totalDsa + lodging + other;

                            $form.find('[name="total_amount"]').val(totalAmount.toFixed(2));
                        }

                        $(claimItineraryForm).on('change keyup',
                            'input[name="breakfast"], input[name="lunch"], input[name="dinner"], ' +
                            'input[name="incident_cost"], input[name="lodging_expense"], input[name="other_expense"]',
                            updateCalculations
                        );

                        setTimeout(updateCalculations, 300);
                    });
            });
            // TADA Quick Fill JavaScript
            const applyQuickFillBtn = document.getElementById('applyQuickFillBtn');
            const resetDefaultsBtn = document.getElementById('resetDefaultsBtn');

            if (applyQuickFillBtn) {
                applyQuickFillBtn.addEventListener('click', function() {
                    const breakfast = parseFloat(document.getElementById('quickBreakfast').value) || 0;
                    const lunch = parseFloat(document.getElementById('quickLunch').value) || 0;
                    const dinner = parseFloat(document.getElementById('quickDinner').value) || 0;
                    const incidental = parseFloat(document.getElementById('quickIncidental').value) || 0;
                    const lodging = parseFloat(document.getElementById('quickLodging').value) || 0;
                    const other = parseFloat(document.getElementById('quickOther').value) || 0;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will update all TADA claim rows with these rates.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Apply to all!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            applyQuickFillBtn.disabled = true;
                            applyQuickFillBtn.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-1"></span> Applying...';

                            $.ajax({
                                url: "{{ route('travel.claims.dsa.bulk-update', $travelClaim->id) }}",
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    breakfast: breakfast,
                                    lunch: lunch,
                                    dinner: dinner,
                                    incident_cost: incidental,
                                    lodging_expense: lodging,
                                    other_expense: other
                                },
                                success: function(response) {
                                    toastr.success(response.message ||
                                        'Rates updated successfully');
                                    updateClaimTotals(response);
                                    itineraryTable.ajax.reload();
                                },
                                error: function(xhr) {
                                    const msg = xhr.responseJSON?.message ||
                                        'Failed to update rates';
                                    toastr.error(msg);
                                    console.error(xhr);
                                },
                                complete: function() {
                                    applyQuickFillBtn.disabled = false;
                                    applyQuickFillBtn.innerHTML =
                                        '<i class="bi bi-check-all me-1"></i> Apply to All Rows';
                                }
                            });
                        }
                    });
                });
            }

            if (resetDefaultsBtn) {
                resetDefaultsBtn.addEventListener('click', function() {
                    // document.getElementById('quickBreakfast').value =
                    //     {{ config('constant.DSA_BREAKFAST_RATE', 400) }};
                    // document.getElementById('quickLunch').value =
                    //     {{ config('constant.DSA_LUNCH_RATE', 500) }};
                    // document.getElementById('quickDinner').value =
                    //     {{ config('constant.DSA_DINNER_RATE', 600) }};
                    // document.getElementById('quickIncidental').value =
                    //     {{ config('constant.DSA_INCIDENTAL_RATE', 300) }};
                    document.getElementById('quickBreakfast').value = 0;
                    document.getElementById('quickLunch').value = 0;
                    document.getElementById('quickDinner').value = 0;
                    document.getElementById('quickIncidental').value = 0;
                    document.getElementById('quickLodging').value = 0;
                    document.getElementById('quickOther').value = 0;

                    Swal.fire({
                        title: 'Reset?',
                        // text: "This will reset all rows back to standard rates (Breakfast: {{ config('constant.DSA_BREAKFAST_RATE', 400) }}, Lunch: {{ config('constant.DSA_LUNCH_RATE', 500) }}, Dinner: {{ config('constant.DSA_DINNER_RATE', 600) }}, Incidental: {{ config('constant.DSA_INCIDENTAL_RATE', 300) }}, Lodging: 0, Other: 0).",
                        text: "This will reset all rows back to zero rates.",
                        // icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0d6efd',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Reset all!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetDefaultsBtn.disabled = true;
                            resetDefaultsBtn.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-1"></span> Resetting...';

                            $.ajax({
                                url: "{{ route('travel.claims.dsa.bulk-update', $travelClaim->id) }}",
                                method: 'POST',
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    // breakfast: {{ config('constant.DSA_BREAKFAST_RATE', 400) }},
                                    // lunch: {{ config('constant.DSA_LUNCH_RATE', 500) }},
                                    // dinner: {{ config('constant.DSA_DINNER_RATE', 600) }},
                                    // incident_cost: {{ config('constant.DSA_INCIDENTAL_RATE', 300) }},
                                    breakfast: 0,
                                    lunch: 0,
                                    dinner: 0,
                                    incident_cost: 0,
                                    lodging_expense: 0,
                                    other_expense: 0
                                },
                                success: function(response) {
                                    toastr.success(response.message ||
                                        'Rates reset to defaults successfully');
                                    updateClaimTotals(response);
                                    itineraryTable.ajax.reload();
                                },
                                error: function(xhr) {
                                    const msg = xhr.responseJSON?.message ||
                                        'Failed to reset rates';
                                    toastr.error(msg);
                                    console.error(xhr);
                                },
                                complete: function() {
                                    resetDefaultsBtn.disabled = false;
                                    resetDefaultsBtn.innerHTML =
                                        '<i class="bi bi-arrow-counterclockwise me-1"></i> Reset';
                                }
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('travel.claims.index') }}" class="text-decoration-none text-dark">Travel
                                Claims
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Request Details
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
                @if ($travelClaim->returnLog()->exists())
                    <div class="card">
                        <div class="card-header fw-bold text-danger">
                            Return Remarks
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 list-unstyled list-py-2 text-dark">

                                <li class="position-relative">
                                    <div class="gap-2 d-flex align-items-start">
                                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> {{ $travelClaim->returnLog->log_remarks }}</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-9">
                <form action="{{ route('travel.claims.update', $travelClaim->id) }}" id="travelClaimEditForm"
                      method="post"
                      enctype="multipart/form-data" autocomplete="off">

                    <div class="card">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            <span> TADA Claim</span>
                        </div>
                        <div class="container-fluid-s">
                            @if ($authUser->can('update', $travelClaim))
                                <div class="card bg-light border-light mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2 text-primary fw-bold">TADA Claim Quick Fill</h6>
                                        <p class="text-muted small mb-3">Pre-populate or reset Breakfast, Lunch, Dinner,
                                            Incidental, Lodging, and Other expenses across all itinerary days at once.</p>
                                        <div class="row g-2 align-items-end" id="tadaQuickFillSection">
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Breakfast</label>
                                                <input type="number" id="quickBreakfast"
                                                    class="form-control form-control-sm"
                                                    value="{{ config('constant.DSA_BREAKFAST_RATE', 400) }}"
                                                    min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Lunch</label>
                                                <input type="number" id="quickLunch" class="form-control form-control-sm"
                                                    value="{{ config('constant.DSA_LUNCH_RATE', 500) }}" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Dinner</label>
                                                <input type="number" id="quickDinner" class="form-control form-control-sm"
                                                    value="{{ config('constant.DSA_DINNER_RATE', 600) }}" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Incidental</label>
                                                <input type="number" id="quickIncidental"
                                                    class="form-control form-control-sm"
                                                    value="{{ config('constant.DSA_INCIDENTAL_RATE', 300) }}"
                                                    min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Lodging Expense</label>
                                                <input type="number" id="quickLodging" class="form-control form-control-sm"
                                                    value="0" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Other Expense</label>
                                                <input type="number" id="quickOther" class="form-control form-control-sm"
                                                    value="0" min="0">
                                            </div>
                                            <div class="col-12 mt-3 d-flex gap-2">
                                                <button type="button" id="applyQuickFillBtn"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="bi bi-check-all me-1"></i> Apply to All
                                                </button>
                                                <button type="button" id="resetDefaultsBtn"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="itineraryTable">
                                            <thead class="thead-light">
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">Breakfast</th>
                                                <th scope="col">Lunch</th>
                                                <th scope="col">Dinner</th>
                                                <th scope="col">Incidental</th>
                                                <th scope="col">Total DSA</th>
                                                <th scope="col">Lodging Expense</th>
                                                <th scope="col">Other Expense</th>
                                                <th scope="col">Total Amount</th>
                                                <th scope="col">{{ __('label.remarks') }}</th>
                                                <th scope="col">{{ __('label.attachment') }}</th>
                                                <th scope="col">{{ __('label.action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            <span> Local Travel Claim</span>
                            @if ($authUser->can('update', $travelClaim))
                                <button data-toggle="modal"
                                        class="m-2 btn btn-primary btn-sm text-capitalize open-local-travel-modal-form"
                                        href="{!! route('travel.claims.local.travel.create', $travelClaim->id) !!}"><i
                                        class="bi-plus"></i> New Local Travel Claim
                                </button>
                            @endif
                        </div>
                        <div class="container-fluid-s">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="claimLocalTravelTable">
                                            <thead class="thead-light">
                                            <tr>
                                                <th scope="col" rowspan="2">{{ __('label.activity') }}</th>
                                                <th scope="col" rowspan="2">{{ __('label.date') }}</th>
                                                <th scope="col" rowspan="2">{{ __('label.purpose') }}</th>
                                                <th scope="col" colspan="2" class="text-center">
                                                    {{ __('label.destination') }}
                                                </th>
                                                <th scope="col" rowspan="2">Total fare</th>
                                                <th scope="col" rowspan="2">{{ __('label.remarks') }}</th>
                                                <th scope="col" rowspan="2">{{ __('label.attachment') }}</th>
                                                <th style="width: 150px" rowspan="2">{{ __('label.action') }}</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">{{ __('label.from') }}</th>
                                                <th scope="col">{{ __('label.to') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            <span> {{ __('label.other-expense') }}</span>
                            @if ($authUser->can('update', $travelClaim))
                                <button data-toggle="modal"
                                        class="m-2 btn btn-primary btn-sm text-capitalize open-expense-modal-form"
                                        href="{!! route('travel.claims.expenses.create', $travelClaim->id) !!}"><i
                                        class="bi-plus"></i> New Expense
                                </button>
                            @endif
                        </div>
                        <div class="container-fluid-s">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="expenseTable">
                                            <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('label.activity') }}</th>
                                                {{-- <th scope="col">{{ __('label.donor') }}</th> --}}
                                                <th scope="col">{{ __('label.date') }}</th>
                                                <th scope="col">{{ __('label.description') }}</th>
                                                <th scope="col">{{ __('label.amount') }}</th>
                                                <th scope="col">{{ __('label.invoice-bill-number') }}</th>
                                                {{-- <th scope="col">Charging Office</th> --}}
                                                <th scope="col">{{ __('label.attachment') }}</th>
                                                <th style="width: 150px">{{ __('label.action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="3">{{ __('label.sub-total') }}</td>
                                                <td colspan="4" id="total_expense_amount">
                                                    {{ number_format($travelClaim->total_expense_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Total Local Travel</td>
                                                <td colspan="4" id="total_local_travel_amount">
                                                    {{ number_format($travelClaim->localTravels->sum('travel_fare'), 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Total TADA</td>
                                                <td colspan="4" id="total_itinerary_amount">
                                                    {{ number_format($travelClaim->total_itinerary_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">{{ __('label.grand-total') }}</td>
                                                <td colspan="4" id="total_amount">
                                                    {{ number_format($travelClaim->total_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">{{ __('label.advance-amount') }}
                                                </td>
                                                <td colspan="4">
                                                    {{ number_format($travelClaim->advance_amount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    {{ __('label.refundable-reimbursable-amount') }}
                                                </td>
                                                <td colspan="4">
                                                    <input readonly class="form-control" name="refundable_amount"
                                                           value="{{ $travelClaim->refundable_amount }}"/>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header fw-bold">Process</div>
                        <div class="card-body">
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="Fdname" class="form-label required-label">
                                            {{ __('label.approval') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class=" form-switch">
                                        @php $selectedReviewerId = old('reviewer_id') ?: $travelClaim->reviewer_id; @endphp
                                        <select name="approver_id" class="select2 form-control
                                        @if ($errors->has('reviewer_id')) is-invalid @endif" data-width="100%">
                                            @if ($approvers->count() !== 1)
                                                <option value="">Select an Approver</option>
                                            @endif
                                            @foreach ($approvers as $approver)
                                                <option
                                                    value="{{ $approver->id }}" @selected($approver->id == (old('approver_id') ?: $travelClaim->approver_id))>
                                                    {{ $approver->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="Fdname" class="form-label required-label">
                                            Send To
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class=" form-switch">
                                        @php $selectedReviewerId = old('reviewer_id') ?: $travelClaim->reviewer_id; @endphp
                                        <select name="reviewer_id" class="select2 form-control
                                        @if ($errors->has('reviewer_id')) is-invalid @endif" data-width="100%">
                                            <option value="">Select a Verifier</option>
                                            @foreach ($reviewers as $reviewer)
                                                <option
                                                    value="{{ $reviewer->id }}" {{ $reviewer->id == $selectedReviewerId ? 'selected' : '' }}>
                                                    {{ $reviewer->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-2">
                                    <div class=" form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="flexSwitchCheckChecked" name="agree"
                                               @if ($travelClaim->agree_at) checked
                                            @endif>
                                        <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="Fdname" class="m-0">
                                            I certify that the following information is correct and per the approved
                                            Travel
                                            authorization. I authorize HERDi to treat this as the final claim and I will
                                            repay any travel allowances to which I am not entitled. If office provides
                                            breakfast, lunch, dinner or accommodation, this must be deducted from claim,
                                            i.e. % change should be 100%-deducted %
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gap-2 justify-content-end d-flex">
                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                        </button>
                        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                            Submit
                        </button>
                        <a href="{!! route('travel.claims.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                    {!! method_field('PUT') !!}
                    {!! csrf_field() !!}
                </form>
            </div>
        </div>
    </section>

    <div class="modal fade" id="claimItineraryModal" data-bs-backdrop="static" data-bs-keyboard="false"
         aria-labelledby="claimItineraryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
@stop
