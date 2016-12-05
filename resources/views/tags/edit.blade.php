@extends('main')

@section('title', "| Edit Tag")

@section('content')

  <h1>Edit Tag</h1>

  {{ Form::model($tag, ['route' => ['tags.update', $tag->id], 'method' => "PUT"]) }}
    {{ Form::label('name', "Title:") }}
    {{ Form::text('name', null, ['class' => 'form-control']) }}

    {{ Form::submit('Save Changes', ['class' => 'btn btn-success btn-h1-spacing']) }}
  {{ Form::close() }}
@endsection
