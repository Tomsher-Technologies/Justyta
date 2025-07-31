@extends('layouts.admin_default', ['title' => 'Contacts'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">Contacts</h4>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table4  bg-white mb-20">

                            <form method="GET" action="{{ route('user-contacts.feedback') }}" autocomplete="off">
                                <div class="row mb-2">
                                    <div class="col-md-4 input-group  mt-2 mb-1">
                                        <input type="text" class="form-control ih-small ip-gray radius-xs b-deep px-15 form-control-default date-range-picker" name="daterange" placeholder="From Date - To Date" value="{{ request('daterange') }}">
                                    </div>

                                    <div class="col-md-3 mb-1 mt-2 d-flex flex-wrap align-items-end">
                                        <button class="btn btn-primary btn-sm " type="submit">Filter</button>
                                        <a href="{{ route('user-contacts.feedback') }}"
                                            class="btn btn-secondary btn-square btn-sm ml-2">Reset</a>

                                        @can('export_contacts')
                                            <a href="{{ route('user-contacts.export', request()->query()) }}"
                                                class="btn btn-warning btn-sm ml-2">
                                                Export
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-basic mb-0">
                                    <thead class="userDatatable-header">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="">Contact Info</th>
                                            {{-- <th class="text-center">Email</th>
                                            <th class="text-center">Phone</th> --}}
                                            <th class="text-center w-25">Subject</th>
                                            <th class="text-center w-40">Message</th>
                                            <th class="text-center w-10">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse($contacts as $key => $con)
                                            
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <strong>Name :</strong> {{ $con->name ?? ''}}<br>
                                                    <strong>Email :</strong> {{ $con->email }}<br>
                                                    <strong>Phone :</strong> {{ $con->phone }}
                                                </td>
                                                {{-- <td class="text-center">
                                                    {{ $con->email }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $con->phone }}
                                                </td> --}}

                                                <td>
                                                    {{ $con->subject }}
                                                </td>

                                                <td>
                                                    {{ $con->message }}
                                                </td>

                                                <td class="text-center">{{ date('d, M Y h:i A', strtotime($con->created_at)) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="aiz-pagination mt-4">
                                    {{ $contacts->appends(request()->input())->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('style')
   
@endsection

@section('script_first')

@endsection


@section('script')
    <script type="text/javascript">
      
    </script>
@endsection
