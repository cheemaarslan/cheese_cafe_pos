<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Enums\Status;
use App\Models\Blog;
use App\Services\BlogService;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Http\Requests\PaginateRequest;

class BlogController extends Controller
{
    private BlogService $blogService;

    public function __construct(BlogService $blog)
    {
        $this->blogService = $blog;
    }

    public function index(PaginateRequest $request)
    {
        try {
            // Add status filter to only show active blogs
            $request->merge(['status' => Status::ACTIVE]);
            return BlogResource::collection($this->blogService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Blog $blog)
    {
        try {
            // Check if blog is active
            if ($blog->status != Status::ACTIVE) {
                return response(['status' => false, 'message' => 'Blog not found'], 404);
            }
            return new BlogResource($this->blogService->show($blog));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
