<?php

namespace App\Http\Controllers\Admin;

use App\Post;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends HomeController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return view('admin.posts.index', compact('posts'));
    }

    /*
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validatePost($request);

        $form_data = $request->all();
        $post = new Post();
        $post->fill($form_data);

        // $slug = Str::slug($post->title);
        // $slug_base = $slug;
        // $counter = 1;
        //     $existingPost = Post::where('slug', $slug)->first();
        //     while($existingPost){
        //         $slug = $slug_base . '_' . $counter;
        //         $existingPost = Post::where('slug', $slug)->first();
        //         $counter++;
        //     }

        $slug = $this->getSlug($post->title);
        $post->slug = $slug;
        $post->save();

            return redirect()->route('admin.posts.show', $post->id);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view ('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {

        $this->validatePost($request);
        $form_data = $request->all();
        if($post->title != $form_data['title']){
            $slug= $this->getSlug($form_data['title']);
            $form_data['slug']=$slug;

        }

        $post->update($form_data);
        return redirect()->route('admin.posts.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index');
    }

    private function getSlug($title){
        $slug = Str::slug($title);
        $slug_base = $slug;

        $existingPost = Post::where('slug', $slug)->first();
        $counter = 1;
        while($existingPost){
            $slug = $slug_base . '_' . $counter;
            $counter++;
            $existingPost = Post::where('slug', $slug)->first();
        }
        return $slug;
    }

    private function validatePost(Request $request){
        $request->validate([
            'title' => 'required|min:5|max:255',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id'
        ], [
            'required' => ':attribute is mandatory',
            'min' => ':attribute should be at least :min chars',
            'max' => ':attribute should have max length of :max chars',
            'category_id.exists' => 'Category doesn\'t exists anymore :('
        ]);
    }

}




