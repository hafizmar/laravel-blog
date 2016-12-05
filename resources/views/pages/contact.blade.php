
@extends('main')

@section('title', '| Contact')

@section('content')
  <div class="row">
    <h1>Contact me</h1>
    <hr>
    <form action="{{ url('contact') }}" method="post">
      {{ csrf_field() }}
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" name="email" class="form-control">
        </div>

        <div class="form-group">
          <label for="subject">Subject:</label>
          <input type="text" name="subject" class="form-control">
        </div>

        <div class="form-group">
          <label for="message">Email:</label>
          <textarea name="message" class="form-control" rows="8" cols="40" placeholder="Type your message here..."></textarea>
        </div>

        <input type="submit" value="Send Message" class="btn btn-success">
    </form>
  </div>
@endsection
