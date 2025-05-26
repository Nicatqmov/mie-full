@extends('admin.layouts.master')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow bg-dark text-light">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Entities for Project: {{ $project->name }}</h5>
                    <a href="{{ route('projects.entities.create', $project->id) }}" class="btn btn-success">➕ Add Entity</a>
                    <a href="{{ route('projects.index') }}" class="btn btn-warning">➕ Back to All Projects</a>
                </div>
                <div class="card-body">
                    <table class="table table-dark table-hover text-center">
                        <thead class="bg-secondary">
                            <tr>
                                <th>#</th>
                                <th>Entity Name</th>
                                <th>Table Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entities as $index => $entity)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $entity->name }}</td>
                                <td>{{ $entity->table_name }}</td>
                                <td>{{ $entity->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('projects.entities.fields.index', ['project' => $project->id, 'entity' => $entity->id]) }}" class="btn btn-sm btn-info">📜 View Fields</a>
                                    <a href="{{ route('projects.entities.edit', ['project' => $project->id, 'entity' => $entity->id]) }}" class="btn btn-sm btn-warning">✏ Edit</a>
                                    <form action="{{ route('projects.entities.destroy', ['project' => $project->id, 'entity' => $entity->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">🗑 Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- @if($entities->isEmpty())
                        <p class="text-center text-muted">No entities found. Start by adding a new one!</p>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 for Delete Confirmation -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll(".delete-btn");
        deleteButtons.forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault();
                const form = this.closest("form");
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

@endsection
