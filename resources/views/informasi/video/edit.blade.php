@extends('partial.main')

@section('body')
<div class="container">
    <h1>Edit Video</h1>
    <form action="{{ route('informasi.video.update', $video->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Judul Video</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $video->title }}" required>
        </div>
        <div class="mb-3">
            <label for="url" class="form-label">URL Video</label>
            <input type="url" name="url" id="url" class="form-control" value="{{ $video->url }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" id="description" class="form-control">{{ $video->description }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
