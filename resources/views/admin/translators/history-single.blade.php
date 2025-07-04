@extends('layouts.admin_default', ['title' => 'Default Translator'])

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-4">
        <div class="col-lg-12 mx-auto">
            <div class="card card-default">
                <div class="card-header">
                    <h4>Assignment History: {{ $fromLang->name }} → {{ $toLang->name }}</h4>
                     <a href="{{ route('translators.default') }}" class="btn btn-sm btn-primary">← Back</a>

                </div>
                <div class="card-body table4  ">
                    <div class="table-responsive">
                        <table class="table table-bordered table-basic">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>#</th>
                                    <th>Translator</th>
                                    <th>Assigned By</th>
                                    <th>Assigned At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($histories as $i => $history)
                                    <tr>
                                        <td>{{ $histories->firstItem() + $i }}</td>
                                        <td>{{ $history->translator->name }}</td>
                                        <td>{{ $history->assignedBy->name ?? 'System' }}</td>
                                        <td>{{ date('d,M Y h:i A', strtotime($history->assigned_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4">No assignment history found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="aiz-pagination mt-4">
                        {{ $histories->appends(request()->input())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection