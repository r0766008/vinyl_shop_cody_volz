@extends('layouts.template')

@section('title', 'Shop')

@section('main')
    @foreach($genres as $genre)
        <h2>{{ $genre->name }}</h2>
        <ul>
        @foreach($genre->records as $record)
            @if ($genre->id == $record->genre_id)
                    <li><a href="shop/{{ $record->id }}">{{ $record->artist }} - {{ $record->title }}</a> | Price: € {{ $record->price }} | Stock: {{ $record->stock }} </li>
            @endif
        @endforeach
        </ul>
    @endforeach
@endsection
