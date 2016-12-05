<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Post;
use Session;
use App\Category;
use App\Tag;
use Purifier;
use Image;
use Storage;

class PostController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //masukkan semua data dalam variable untuk tujuan viewing
        // create a variable and store all the blog posts in it from the database
        //$posts = Post::all();
        $posts = Post::orderBy('id' ,'desc')->paginate(5);
        // return a view and pass in the above variable
        return view('posts.index')->withPosts($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        //direct user ke create form
        return view('posts.create')->withCategories($categories)->withTags($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   //die and dump - terus dump all data dan stop excute code di bawahnya
        //dd($request);

        //insert data into database
        //validate the data
        $this->validate($request, array(
          'title'           => 'required|max:255',
          'slug'            => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
          'category_id'     => 'required|integer',
          'body'            => 'required',
          'featured_image'  => 'sometimes|image'

        ));

        //store in the database
        $post              = new Post;
        $post->title       = $request->title;
        $post->slug        = $request->slug;
        $post->category_id = $request->category_id;
        $post->body        = Purifier::clean($request->body);

        //save our image

        if ($request->hasFile('featured_image'))
        {
          $image = $request->file('featured_image');
          $filename = time() . '.' . $image->getClientOriginalExtension();
          $location = public_path('images/' . $filename); //$location = storage_path('\app\posts\')
          Image::make($image)->resize(800, 400)->save($location);

          $post->image = $filename;
        }

        $post->save();

        $post->tags()->sync($request->tags, false);//false is for dont want to overwrite the data

        Session::flash('success', 'The blog post was successfully save!');

        //redirect to another page
        return redirect()->route('posts.show', $post->id);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   //tujuan untuk tengok details tentang data
        $post = Post::find($id);
        return view('posts.show')->withPost($post); //->with('post', '$post') | ->with('key', 'data')
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   //tujuan untuk direct ke edit view dengan segala data
        // find the post in the database and save as a variable
        $post = Post::find($id);
        $categories = Category::all();
        $cats = array();
        foreach ($categories as $category) {
          $cats[$category->id] = $category->name;
        }

        $tags = Tag::all();
        $tags2= array();
        foreach ($tags as $tag) {
          $tags2[$tag->id] = $tag->name;
        }
        //return view and pass in the var we previously created
        return view('posts.edit')->withPost($post)->withCategories($cats)->withTags($tags2);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   //fetch input dari form dan update data
        // validate the data
        $post = Post::find($id);

        $this->validate($request, [
          'title' => 'required|max:255',
          'slug' => "required|alpha_dash|min:5|max:255|unique:posts,slug,$id",
          'category_id' => 'required|integer',
          'body' => 'required',
          'featured_image' => 'image',
          ]);

        // save the data to tge database
        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->slug = $request->input('slug');
        $post->category_id = $request->input('category_id');
        $post->body = Purifier::clean($request->input('body'));

        if($request->hasFile('featured_image')){
          //ADD NEW PHOTO
          $image = $request->file('featured_image');
          $filename = time() . '.' . $image->getClientOriginalExtension();
          $location = public_path('images/' . $filename); //$location = storage_path('\app\posts\')
          Image::make($image)->resize(800, 400)->save($location);
          $oldFilename = $post->image;
          //UPDATE THE DATABASE
          $post->image = $filename;
          //DELETE THE OLD PHOTO
          Storage::delete($oldFilename);

        }
        $post->save();

        if (isset($request->tags)) { //jika ada request->tags
          $post->tags()->sync($request->tags);
        } else {
          $post->tags()->sync(array()); //save empty array()
        }

        // set flash data with success message
        Session::flash('success', 'This post was succesfully saved.');

        // redirect with flash data to posts.show
        return redirect()->route('posts.show', $post->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   //delete data
        $post = Post::find($id);
        $post->tags()->detach();
        Storage::delete($post->image);
        $post->delete();
        Session::flash('success', 'The post was successfully deleted.');
        return redirect()->route('posts.index');
    }
}
