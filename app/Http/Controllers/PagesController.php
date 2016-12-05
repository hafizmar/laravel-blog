<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Post; // letak use namespace model a.k.a table kalau nak guna sesebuah model/table
use Mail;
use Session;

class PagesController extends Controller {

  #process variable data or params
  #talk to the model
  #receive from the model
  #compile or process data from the model if needed
  #pass the data to the correct view

  public function getIndex()
  {
    $posts = Post::orderBy('created_at', 'desc')->limit(4)->get();
    return view('pages.welcome')->withPosts($posts);
  }

  public function getAbout()
  {
    $first            = 'Hafiz';
    $last             = 'Mar';
    $full             = $first . " " . $last;
    $email            = 'hafizmar@hotmail.co.uk';
    $data['email']    = $email;
    $data['fullname'] = $full;

    // return view('pages.about')->withFullname($full)->withEmail($email);
    return view('pages.about')->withData($data);
  }

  public function getContact()
  {
    return view('pages.contact');
  }

  public function postContact(Request $request)
  {
    $this->validate($request, [
      'email' => 'required|email',
      'subject' => 'min:3',
      'message' => 'min:10'
    ]);

    $data = [
      'email' => $request->email,
      'subject' => $request->subject,
      'bodyMessage' => $request->message
    ];

    Mail::send('emails.contact', $data, function($message) use ($data){
      $message->from($data['email']);
      $message->to('hafizmar@hotmail.co.uk');
      $message->subject($data['subject']);
    });

    Session::flash('success', 'Your Email was sent!!');
    return redirect()->route('/');
  }

}
