<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    public function created(Post $post): void
    {
        $slug = $this->generateUniqueSlug($post->title);
        $post->slug = $slug;
        $post->save();
    }
    public function updated(Post $post): void
    {
        if ($post->isDirty('title')) {
            $slug = $this->generateUniqueSlug($post->title);
            $post->slug = $slug;
            $post->save();
        }
    }
    private function generateUniqueSlug($title, $count = 0)
    {
        $slug = Str::slug($title);

        if ($count > 0) {
            $slug .= "-$count";
        }

        if (Post::where('slug', $slug)->exists()) {
            return $this->generateUniqueSlug($title, $count + 1);
        }

        return $slug;
    }

}
