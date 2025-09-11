@extends('layouts.container')

@section('title', 'Get Employee Code From Employee Name')

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">
        <form action="{{route('employee.code.check')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div>
                <input class="form-control mb-2" type="file" name="test_file" id="test_file">
                <button type="submit">Import</button>
            </div>
        </form>
    </div>
</div>
@stop
