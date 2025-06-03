@extends('layouts.admin_default', ['title' => 'Default Translator'])

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-4">
        <div class="col-lg-10 mx-auto">
            <div class="card card-default">
                <div class="card-header">
                    <h4>Set Default Translator</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('translators.set-default') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="translator_id">Select Translator</label>
                            <select name="translator_id" id="translator_id" class="form-control select2" data-placeholder="Select Translator">
                                <option value="">-- Select --</option>
                                @foreach ($translators as $translator)
                                    <option value="{{ $translator->id }}" >
                                        {{ $translator->name }}
                                    </option>
                                    {{-- {{ $translator->is_default ? 'selected' : '' }} --}}
                                @endforeach
                            </select>
                            @error('translator_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group d-flex flex-wrap align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm">Set as Default</button>
                            <a href="{{ route('translators.default') }}" class="btn btn-secondary btn-sm ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-default mt-4">
                <div class="card-header">
                    <h4>Default Translator History</h4>
                </div>
                <div class="card-body table4  table-responsive">
                    <table class="table table-bordered table-basic mb-0">
                        <thead>
                            <tr class="userDatatable-header">
                                <th>#</th>
                                <th>Translator</th>
                                <th class="text-center">Started At</th>
                                <th class="text-center">Ended At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $key => $history)
                                <tr>
                                    <td class="text-center">
                                        {{ $key + 1 + ($histories->currentPage() - 1) * $histories->perPage() }}
                                    </td>
                                    <td>{{ $history->translator->name }}</td>
                                    <td class="text-center">{{ $history->started_at ? \Carbon\Carbon::parse($history->started_at)->format('d M Y, h:i A') : '-' }}</td>
                                    <td class="text-center">{!! $history->ended_at ? \Carbon\Carbon::parse($history->ended_at)->format('d M Y, h:i A') : '<span class="badge badge-primary rounded-pill ml-4">Ongoing</span>' !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="4">No history available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="aiz-pagination mt-4">
                        {{ $histories->appends(request()->input())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
