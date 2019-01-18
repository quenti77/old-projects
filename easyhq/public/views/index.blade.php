@extends('base')
@section('title', $title)

@section('content')
    {!! \EasyHQ\Translate::getContent('home') !!}

    {!! \EasyHQ\Translate::getContent('footer') !!}
@endsection
