@extends('layouts.app')

@section('content')
    <!-- resources/views/tickets/partials/_status_form.blade.php -->
    <h4>Update Status for Ticket #{{ $ticket->id }} - {{ $ticket->title }}</h4>
    <hr>
    @if(auth()->user()->isAdmin())
        <form action="{{ route('tickets.status.update', $ticket->id) }}" method="POST" class="mb-4">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Update Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-sm btn-info">Update Status</button>
        </form>
    @endif

@endsection
