<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Services\BlogService;
use App\Http\Requests\BlogRequest;
use App\Http\Resources\BlogResource;
use App\Http\Requests\PaginateRequest;
use App\Models\Blog;

class BlogController extends AdminController
{
    private BlogService $blogService;

    public function __construct(BlogService $blog)
    {
        parent::__construct();
        $this->blogService = $blog;
        $this->middleware(['permission:settings'])->only('store', 'update', 'destroy', 'show');
    }

    public function index(PaginateRequest $request)
    {
        try {
            return BlogResource::collection($this->blogService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(BlogRequest $request): \Illuminate\Http\Response | BlogResource
    {
        try {
            return new BlogResource($this->blogService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Blog $blog): \Illuminate\Http\Response | BlogResource
    {
        try {
            return new BlogResource($this->blogService->show($blog));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(BlogRequest $request, Blog $blog): \Illuminate\Http\Response | BlogResource
    {
        try {
            return new BlogResource($this->blogService->update($request, $blog));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Blog $blog)
    {
        try {
            $this->blogService->destroy($blog);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
