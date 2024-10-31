<?php

namespace App\Http\Controllers;

use App\Models\ReactJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReactJobsController extends Controller
{
    //
    public function index()
    {
        $job = ReactJobs::latest()->with('user')->get();
        return response()->json($job);
    }
    //store
    public function store(Request $request)
    {
        try {
            $attribute = $request->validate([
                'title' => ['required', 'max:400'],
                'type' => ['required', 'max:200'],
                'description' => ['required'],
                'salary' => ['required'],
                'location' => ['required'],
                'company_name' => ['required'],
                'company_description' => ['required'],
                'company_email' => ['required', 'email'],
                'company_phone' => ['required'],
            ]);
            $attribute['user_id'] = Auth::id();
            $post = ReactJobs::create($attribute);
            return response()->json([
                'message' => 'job successfully added',
                'jobs' => $post,
            ], 200);
        } catch (ValidationException $e) {
            // Return a custom JSON response for validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(), // Return the validation errors
            ], 422); // HTTP 422 Unprocessable Entity status code
        }
    }


    //show
    public function show(ReactJobs $job)
    {
        return response()->json($job, 200);
    }


    //edit
    public function update(Request $request, ReactJobs $job)
    {
        //authorization
        if(Auth::id() !== $job->user_id )
        {
            return response()->json([
                'message'=>'you are not authorized to edit this',
            ],401);
        }

        $validate = Validator::make($request->all(), [
            'title' => ['required', 'max:400'],
            'type' => ['required', 'max:200'],
            'description' => ['required'],
            'salary' => ['required'],
            'location' => ['required'],
            'company_name' => ['required'],
            'company_description' => ['required'],
            'company_email' => ['required', 'email'],
            'company_phone' => ['required'],
        ]);
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validate->errors(),
            ], 422);
        }

        $attribute = $validate->validated();
        $job->update($attribute);

        return response()->json([
            'message' => 'Job successfully updated',
            'job' => $job,
        ], 200);
    }


    //destroy
    public function destroy(ReactJobs $job)
    {
        if(Auth::id() !== $job->user_id )
        {
            return response()->json([
                'message'=>'you are not authorized to delete this',
            ],401);
        }
        $job->delete();
        return response()->json([
            'message' => 'job deleted successfully',
        ]);
    }
    
}
