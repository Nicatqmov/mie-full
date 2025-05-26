@extends('admin.layouts.master')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow bg-dark text-light">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📋 All Projects</h5>
                    <a href="{{ route('projects.create') }}" class="btn btn-success">➕ Create New Project</a>
                </div>
                <div class="card-body">
                    <table class="table table-dark table-hover text-center">
                        <thead class="bg-secondary">
                            <tr>
                                <th>#</th>
                                <th>Project Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $index => $project)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('projects.entities.index', $project->id) }}" class="btn btn-sm btn-info">👁 View</a>
                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">✏ Edit</a>
                                    <a target="_blank" href="{{ \App\Filament\Pages\Projects::getUrl(['project' => $project->id]) }}"
                                    class="btn btn-sm btn-primary"> 
                                        <i class="fas fa-cog"></i> Admin Panel
                                    </a>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-btn">🗑 Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($projects->isEmpty())
                        <p class="text-center text-muted">No projects found. Start by creating a new project!</p>
                    @endif
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
