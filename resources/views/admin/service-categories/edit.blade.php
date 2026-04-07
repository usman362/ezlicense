@extends('layouts.admin')
@section('title', 'Edit Category')
@section('content')
<div class="container-fluid p-4" style="max-width: 700px;">
    <h1 class="h3 mb-4">Edit Category</h1>
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.service-categories.update', $category) }}">
                @method('PUT')
                @include('admin.service-categories._form')
            </form>
        </div>
    </div>
</div>
@endsection
