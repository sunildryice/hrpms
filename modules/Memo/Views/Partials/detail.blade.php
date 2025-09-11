            <div class="row"
                style="display: flex; flex-direction: row; border: 2px solid; border-color: lightgray; box-shadow: 2px 3px lightgray; border-radius: 10px; margin: 0 5px; margin-bottom: 15px;">
                <div class="col-lg-6">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-2">
                                    <strong>Date: </strong> {{ $memo->getMemoDate() }}
                                </div>
                                <div>
                                    <ul class="p-0 m-0 list-unstyled">
                                        <li><span class="fw-bold me-2">Memo No.
                                                :</span><span>{{ $memo->getMemoNumber() }}</span></li>
                                        <li><span class="fw-bold me-2"> To:</span><span>{{ $memo->getTo() }}</span></li>
                                        <li><span class="fw-bold me-2">
                                                From:</span><span>{{ $memo->getCreatedBy() }}</span></li>
                                        <li><span class="fw-bold me-2"> CC:</span><span>{{ $memo->getThrough() }}</span>
                                        </li>
                                        <li><span class="fw-bold me-2"> Subject:</span><span>{{ $memo->subject }}</span>
                                        </li>
                                        @isset($memo->attachment)
                                            <li><span class="fw-bold me-2">
                                                    Attachment:</span><span>
                                                    <a href="{!! asset('storage/' . $memo->attachment) !!}" target="_blank"
                                                        name='attachment_exist' class="fs-5" title="View Attachment">
                                                        <i class="bi bi-file-earmark-medical"></i>
                                                    </a>
                                                </span>
                                            </li>
                                        @endisset
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-body">
                        <div class="mb-3 print-header-info">
                            <ul class="p-0 m-0 list-unstyled">
                                @php
                                    $submittedDate = $memo->logs()->where('status_id', 3)->orderBy('id', 'desc')->first()?->created_at;
                                    $approvedDate = $memo->logs()->where('status_id', 6)->orderBy('id', 'desc')->first()?->created_at;
                                @endphp

                                <li><span class="fw-bold me-2">
                                        Requester:</span><span>{{ $memo->getCreatedBy() }}</span></li>
                                @isset($submittedDate)
                                    <li><span class="fw-bold me-2"> Submitted at:</span><span>{{ $submittedDate }}</span>
                                    </li>
                                @endisset
                                @isset($approvedDate)
                                    <li><span class="fw-bold me-2"> Approver:</span><span>{{ $memo->getTo() }}</span></li>
                                    <li><span class="fw-bold me-2"> Approved at:</span><span>{{ $approvedDate }}</span>
                                    </li>
                                @endisset
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
