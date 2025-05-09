@extends('layouts.app')

@section('content')
    <!-- resources/views/tickets/partials/_status_form.blade.php -->
    <h4>Assign Ticket #{{ $ticket->id }} - {{ $ticket->title }}</h4>
    <hr>
    <!-- resources/views/tickets/partials/_assign_form.blade.php -->
    @if(auth()->user()->isAdmin())
        <form action="{{ route('tickets.assign', $ticket->id) }}" method="POST" class="mb-4">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Assign to Admin <span class="text-danger">*</span></label>
                <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                    <option value="">-- Select Admin --</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                            {{ $admin->name }} ({{ $admin->email }})
                        </option>
                    @endforeach
                </select>
                @error('assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-sm btn-warning">Assign</button>
        </form>
    @endif
@endsection
