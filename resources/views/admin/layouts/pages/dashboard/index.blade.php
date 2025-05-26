@extends('admin.layouts.master')

@section('content')
@if (session('auth_token'))
    <script>
        const token = "{{ session('auth_token') }}";
        console.log('Auth token:', token);
    </script>
@else 
    <script>
        console.log('No auth token found');
    </script>
@endif
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New Project
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Projects -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Projects
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Projects
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Recent Activity
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recentProjects->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Your Projects</h6>
        </div>
        <div class="card-body">
            @if($projects->isEmpty())
                <p class="text-muted">You don't have any projects yet. Create one to get started!</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('projects.create', $project) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
        </div>
        <div class="card-body">
            @if($recentProjects->isEmpty())
                <p class="text-muted">No recent activity.</p>
            @else
                <ul class="list-group list-group-flush">
                    @foreach($recentProjects as $project)
                    <li class="list-group-item">
                        <i class="fas fa-fw fa-project-diagram text-primary"></i>
                        Created project <strong>{{ $project->name }}</strong> on {{ $project->created_at->format('M d, Y') }}.
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection