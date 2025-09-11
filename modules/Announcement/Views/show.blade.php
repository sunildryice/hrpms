@extends('layouts.container')

@section('title', 'View Announcement')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#announcement-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('announcement.index'), 'title' => 'Announcement'],
        ]" />

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Announcement</h6>
                        <h6><i><u>{{$announcement->getAnnouncementNumber()}}</u></i></h6>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <h5>{{$announcement->getTitle()}}</h5>

                        <p>{!!$announcement->getDescription()!!}</p>

                        @isset($announcement->attachment)
                            Attachment : <a class="btn btn-sm btn-outline-primary"
                                            href="{{asset('storage/'.$announcement->attachment)}}"
                                            target="_blank"
                                            rel="tooltip"
                                            title="View attachment">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                        @endisset
                    </div>

                    <div class="mt-4">
                        <hr>
                        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 2px 20px 0 0;">
                            <div style="display: flex; flex-direction: row;">
                                <div style="display: flex; flex-direction: row;">
                                    <div style="display: flex; flex-direction: column;">
                                        <div style="display: flex; flex-direction:row; width: 110px; justify-content: flex-end; margin-bottom: 0px; padding-bottom: 0px;">
                                            <p style="padding: 0px; margin: 0px;">Published on:</p>
                                        </div>
                                        <div style="display: flex; flex-direction:row; width: 110px; justify-content: flex-end; margin-top: 0px; padding-top: 0px;">
                                            <p style="padding: 0px; margin: 0px;">Expires on:</p>
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: row;">
                                    <div style="display: flex; flex-direction: column;">
                                        <div style="display: flex; flex-direction:row; width: 110px; justify-content: center; margin-bottom: 0px; padding-bottom: 0px;">
                                            <p style="padding: 0px; margin: 0px;">{{$announcement->getPublishedDate()}}</p>
                                        </div>
                                        <div style="display: flex; flex-direction:row; width: 110px; justify-content: center; margin-top: 0px; padding-top: 0px;">
                                            <p style="padding: 0px; margin: 0px;">{{$announcement->getExpiryDate()}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div>
                                Publisher: <i>{{$announcement->getCreatorName()}}</i>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <a href={{URL::previous()}} type="button" class="btn btn-sm btn-secondary">Back</a>
        </section>
    </div>
</div>

@stop
