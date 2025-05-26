@extends('admin.layouts.master')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow bg-dark text-light">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Fields for Entity: {{ $entity->name }} (Project: {{ $project->name }})</h5>
                    <a href="{{ route('projects.entities.fields.create', ['project' => $project->id, 'entity' => $entity->id]) }}" class="btn btn-success">Add Field</a>
                    <a href="{{ route('projects.entities.index', $project->id) }}" class="btn btn-warning">➕ Back to {{$entity->name}}</a>

                </div>
                <div class="card-body">
                    <table class="table table-dark table-hover text-center">
                        <thead class="bg-secondary">
                            <tr>
                                <th>#</th>
                                <th>Field Name</th>
                                <th>Type</th>
                                <th>Validation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fields as $index => $field)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $field->name }}</td>
                                <td>{{ $field->dataType->name }}</td>
                                <td>{{ $field->validation ?? 'None' }}</td>
                                <td>
                                    <a href="{{ route('projects.entities.fields.edit', ['project' => $project->id, 'entity' => $entity->id, 'field' => $field->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('projects.entities.fields.destroy', ['project' => $project->id, 'entity' => $entity->id, 'field' => $field->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($fields->isEmpty())
                        <p class="text-center text-muted">No fields found. Add a new one!</p>
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
