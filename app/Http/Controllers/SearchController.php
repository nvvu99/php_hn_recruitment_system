<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Field;
use Illuminate\Http\Request;
use App\Models\EmployerProfile;

class SearchController extends Controller
{
    public function autocompleteJob(Request $request)
    {
        $data = Field::select('name')
            ->where('name', 'like', "%{$request->terms}%")->get();
        $data = $data->concat(EmployerProfile::select('name')
            ->where('name', 'like', "%{$request->terms}%")->get());
        $data = $data->concat(Job::select('title as name')
            ->where('title', 'like', "%{$request->terms}%")->get());

        return response()->json($data);
    }

    public function searchJobGeneral(Request $request)
    {
        $keyword = $request->keyword;
        $jobs = Job::select('jobs.*')
            ->join('fields', 'fields.id', '=', 'jobs.field_id')
            ->join('employer_profiles', 'employer_profiles.id', '=', 'jobs.employer_profile_id')
            ->where('fields.name', 'like', "%{$keyword}%")
            ->orWhere('employer_profiles.name', 'like', "%{$keyword}%")
            ->orWhere('jobs.title', 'like', "%{$keyword}%")
            ->paginate(config('user.num_pages'))->withQueryString();
        $cntJobs = $jobs->total();

        return view('job.list', [
            'keyword' => $keyword,
            'jobs' => $jobs,
            'cntJobs' => $cntJobs,
        ]);
    }

    public function filterJobs(Request $request)
    {
        $keyword = $request->keyword;
        $types = explode(',', $request->types);

        $jobs = Job::select('jobs.*')
            ->join('fields', 'fields.id', '=', 'jobs.field_id')
            ->join('employer_profiles', 'employer_profiles.id', '=', 'jobs.employer_profile_id')
            ->whereIn('job_type', $types)
            ->where(function ($query) use ($keyword) {
                $query->where('fields.name', 'like', "%{$keyword}%")
                    ->orWhere('employer_profiles.name', 'like', "%{$keyword}%")
                    ->orWhere('jobs.title', 'like', "%{$keyword}%");
            })->paginate(config('user.num_pages'))->withQueryString();
        $cntJobs = $jobs->total();

        return view('job.list', [
            'keyword' => $keyword,
            'jobs' => $jobs,
            'cntJobs' => $cntJobs,
        ]);
    }
}