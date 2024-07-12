<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mt-5">Hello, {{ Auth::user()->name }}</h2>
            <form action="{{ url('/logout') }}" method="post" class="mt-5">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            
            <div class="card mt-3">
                <div class="card-body">
                <form id="createPostForm" action="{{ route('posts.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="body">Body</label>
                        <textarea name="body" id="body" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
                </div>
            </div>
            

            
            
            <h3 class="mt-5">Posts</h3>
            @foreach($posts as $post)
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $post->title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">by: {{ $post->user->name }}</h6>
                        <p class="card-text">{{ $post->body }}</p>
                        @if($post->user_id == Auth::id())
                            <form action="{{ route('posts.destroy', $post->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @endif
                        <a href="{{ route('comments.create', $post->id) }}" class="btn btn-secondary btn-sm">Add Comment</a>
                    </div>
                </div>
            @endforeach

            
            
        </div>
    </div>
</div>
<script>
    document.getElementById('createPostForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const form = event.target;

        fetch(form.action, {
            method: form.method,
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                response.json().then(data => {
                    alert('Failed to create post: ' + data.message);
                });
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Failed to create post.');
        });
    });
</script>
</body>
</html>
