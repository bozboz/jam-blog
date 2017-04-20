@extends($layout ?: 'layouts.default')

@section('posts')
    @foreach ($posts as $post)
        <article>
                <header>
                    {{ $post->name }}
                    <br>
                    {{ $post->published_at->format('d/M/Y - H:i') }}
                    {{ $post->author ? "by {$post->author->first_name} {$post->author->last_name}" : '' }}
                </header>
            <a href="/{{ $post->canonical_path }}" title="{{ $post->name }}">Read more &raquo;</a>
        </article>
    @endforeach
    {{ $posts->links() }}
@stop

@section('aside')
    <ul>
    @foreach ($archive as $year)
        <li>
            <a href="/{{ $year->url }}">{{ $year->year }}</a>
            <ul>
                @foreach ($year->months as $month)
                    <li>
                        <a href="/{{ $month->url }}">{{ $month->date->format('M') }}</a>
                        ({{ $month->post_count }})
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
    </ul>
@stop