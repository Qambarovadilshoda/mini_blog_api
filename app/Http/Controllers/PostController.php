<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with('user')->paginate(10);
        return response()->json(
            [
                'posts' => PostResource::collection($posts),
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'from' => $posts->firstItem(),
                    'last_page' => $posts->lastPage(),
                    'path' => $posts->path(),
                    'per_page' => $posts->perPage(),
                    'to' => $posts->lastItem(),
                    'total' => $posts->total(),
                ],
            ]
        );
    }

    public function store(StorePostRequest $request)
    {
        $post = new Post();
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->save();

        return response()->json([
            'message' => 'Post created'
        ], 201);
    }

    public function show($id)
    {
        $post = Post::with('user')->findOrFail($id);
        return response()->json([
            'post' => new PostResource($post)
        ]);
    }

    public function update(UpdatePostRequest $request,  $id)
    {
        $post = Post::with('user')->findOrFail($id);
        if(Auth::id() !== $post->user_id){
            return response()->json([
                'message' => 'This is not your post'
            ], 403);
        }
        $post->title = $request->title;
        $post->save();

        return response()->json([
            'post' => new PostResource($post),
            'message' => 'Post updated'
        ]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        if(Auth::id() !== $post->user_id){
            return response()->json([
                'message' => 'This is not your post'
            ], 403);
        }
        $post->delete();
        return response()->json([
            'message' => 'Post deleted'
        ], 204);
    }
    public function search(Request $request)
    {
        $posts = Post::where('title', 'like', "%$request->q%")->paginate(6);
        return response()->json([
            'posts' => PostResource::collection($posts),
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'from' => $posts->firstItem(),
                    'last_page' => $posts->lastPage(),
                    'path' => $posts->path(),
                    'per_page' => $posts->perPage(),
                    'to' => $posts->lastItem(),
                    'total' => $posts->total(),
                ],
        ]);
    }
}
