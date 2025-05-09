@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-3">My Tickets</h4>
        @if(!auth()->user()->isAdmin())
        <a href="{{ route('tickets.create') }}" class="btn btn-primary mb-3">Create New Ticket</a>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($tickets))
                @forelse($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td><span class="badge bg-info text-dark">{{ ucfirst($ticket->status) }}</span></td>
                        <td><span class="badge bg-secondary">{{ ucfirst($ticket->priority) }}</span></td>
                        <td>{{ $ticket->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('tickets.assign.form', $ticket->id) }}" class="btn btn-sm btn-warning">Assign</a>
                            @endif
                            @if(auth()->user()->isAdmin() && auth()->id() === $ticket->assigned_to)
                                <a href="{{ route('tickets.status.form', $ticket->id) }}" class="btn btn-sm btn-info">Change Status</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No tickets found.</td>
                    </tr>
                @endforelse
            @else
                <tr>
                    <td colspan="6">No tickets found.</td>
                </tr>
            @endif
            </tbody>
        </table>

        @if(!empty($tickets))
            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
@endsection
