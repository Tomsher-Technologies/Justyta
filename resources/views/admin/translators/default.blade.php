@extends('layouts.admin_default', ['title' => 'Default Translator'])

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-4">
        <div class="col-lg-12 mx-auto">
            <div class="card card-default">
                <div class="card-header">
                    <h4>Set Default Translator</h4>
                </div>
                <div class="card-body ">
                    <div class=" sticky-table-container">
                        <table class="table table-bordered sticky-table">
                            <thead>
                                <tr>
                                    <th>From Language</th>
                                    <th>To Language</th>
                                    <th>Eligible Translators</th>
                                    <th>Current Default</th>
                                    <th width="35%">Set Default</th>
                                </tr>
                            </thead>
                            <tbody style="color:#000;">
                                @foreach ($combinations as $combo)
                                    <tr>
                                        <td>{{ $combo->from->name }}</td>
                                        <td>{{ $combo->to->name }}</td>
                                        <td>
                                            @foreach ($combo->eligible_translators as $t)
                                                <div>{{ $t->name }}</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($combo->current_default)
                                                <strong>{{ $combo->current_default->translator->name }}</strong>
                                                <br><small>Assigned at: {{ date('d,M Y h:i A', strtotime($combo->current_default->assigned_at)) }}</small>
                                            @else
                                                <em class="text-danger">None</em>
                                            @endif
                                        </td>
                                        <td >
                                            <form method="POST" class="row" action="{{ route('translators.set-default') }}">
                                                <div class="col-md-7">
                                                    @csrf
                                                    <input type="hidden" name="from_language_id" value="{{ $combo->from->id }}">
                                                    <input type="hidden" name="to_language_id" value="{{ $combo->to->id }}">
                                                    <select name="translator_id" class="form-control mb-1 select2" required>
                                                        <option value="">Select Translator</option>
                                                        @foreach ($combo->eligible_translators as $t)
                                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4 d-flex">
                                                    <button type="submit" class="btn btn-xs btn-secondary">Assign</button>

                                                    <a href="{{ route('default-translators.history', [$combo->from->id, $combo->to->id]) }}" class="btn btn-xs btn-outline-secondary ml-1"  title="View Assignment History">
                                                        <i class="fas fa-history" style="margin-right: 0px;"></i>
                                                    </a>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
    <style>

        .sticky-table td {
            padding: 0.35rem;
            border: 1px solid #d6d6d8;
        }
        .sticky-table-container {
            max-height: 600px;
            overflow-y: auto;
        }

        .sticky-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #07683b; /* same as .thead-dark */
            color: white;
            border: 1px solid #dee2e6 !important;
            box-shadow: inset 0 -1px 0 #dee2e6;
        }

        
        .sticky-table th,.sticky-table td {
            vertical-align: middle !important;
            text-align: center;
        }

    </style>
@endsection