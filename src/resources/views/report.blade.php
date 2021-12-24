@extends('layout.corp.master')
<!-- The correy layout must be found -->

@section('breadcrumbs')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor m-b-0 m-t-0">Panic Reports</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">Panic</li>
        </ol>
    </div>

    @endsection

    @section('content')
    <div class='col-lg-12'>
        <div class='card card-outline-info'>
            <div class='card-header'>
                <h4 class='m-b-0 text-white'>Filter</h4>
            </div>
            <div class='card-block'>
                <form action='' method='get'>
                    <div class='form-body'>
                        <div class='row'>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='control-label'>Date</label>
                                    <input type='calendar' class='form-control' name='date' value='dd-mm-yyyy' />
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='control-label'>Status</label>
                                    <select class='form-control' name='status'>
                                        <option value=''>All</option>
                                        <option value='0'>Pending</option>
                                        <option value='1' }>Approved</option>
                                        <option value='2' }>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='control-label'>&nbsp;</label><br />
                                    <button type='submit' class='btn btn-success'>Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <th>ledger_id</th>
            <th>request_id</th>
            <th>admin_id</th>
            <th>History</th>
        </thead>
        <tbody>
            @if ($panics->count() == 0)
            <tr>
                <td colspan="5">No panics to display.</td>
            </tr>
            @endif

            @foreach ($panics as $panic)
            <tr>
                <td>{{ $panic->ledger_id }}</td>
                <td>{{ $panic->request_id }}</td>
                <td>{{ $panic->admin_id }}</td>
                <td>${{ $panic->history}}</td>
                <td>
                    <form style="display:inline-block" action="" method="POST">
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-sm btn-danger"> Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {!! $panics->appends(Request::except('page'))->render() !!}

    <p>
        Displaying {{$panics->count()}} of {{ $panics->total() }} alerts.
    </p>

    @endsection
