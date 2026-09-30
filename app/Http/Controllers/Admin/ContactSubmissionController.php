<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginateRequest;
use App\Http\Resources\ContactSubmissionResource;
use App\Services\ContactSubmissionService;
use Exception;

class ContactSubmissionController extends AdminController
{
    public ContactSubmissionService $contactSubmissionService;

    public function __construct(ContactSubmissionService $contactSubmissionService)
    {
        parent::__construct();
        $this->contactSubmissionService = $contactSubmissionService;
        $this->middleware(['permission:contact-submissions'])->only('index');
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ContactSubmissionResource::collection($this->contactSubmissionService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
