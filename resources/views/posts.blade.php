<!DOCTYPE html>
<html lang="en">

<head>
    <title>My blog</title>
    <link rel="stylesheet" href="/app.css" />
</head>

<body>
    @foreach ($posts as $post)
        <article class="{{ $loop->even ? 'mb-6' : 'foobar' }}">
            <h1>
                <a href="posts/{{ $post->slug }}">
                    {{ $post->title }}
                </a>
            </h1>
            <div>
                {{ $post->excerpt }}
            </div>
        </article>
    @endforeach
</body>

</html>