<?php

namespace App\Services;

use Exception;
use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BlogRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class BlogService
{
    protected $blogFilter = [
        'title',
        'slug',
        'description',
        'status',
        'author',
        'category'
    ];

    protected $exceptFilter = [
        'excepts'
    ];

    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Blog::with('media')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->blogFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function store(BlogRequest $request)
    {
        try {
            $data = $request->validated() + ['slug' => Str::slug($request->title)];
            // Set author name from authenticated user if available
            if (Auth::check()) {
                $data['author'] = Auth::user()->name ?? Auth::user()->email ?? null;
            }
            $blog = Blog::create($data);
            if ($request->image) {
                $blog->addMediaFromRequest('image')->toMediaCollection('blog');
            }
            return $blog;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function update(BlogRequest $request, Blog $blog): Blog
    {
        try {
            $blog->update($request->validated() + ['slug' => Str::slug($request->title)]);
            if ($request->image) {
                $blog->clearMediaCollection('blog');
                $blog->addMediaFromRequest('image')->toMediaCollection('blog');
            }
            return $blog;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function destroy(Blog $blog)
    {
        try {
            $blog->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function show(Blog $blog): Blog
    {
        try {
            return $blog;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
