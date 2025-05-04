<?php

namespace App\Http\Controllers;

use App\Events\ProjectApproved;
use App\Http\Controllers\Controller;
use App\Mail\ProjectStatusUpdatedMail;
use App\Mail\ProjectSubmittedMail;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    public function create()
    {

        return view('projects.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $destinationPath = public_path('projects');
        
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $filename);
        
            $filePath = 'projects/' . $filename; 
        }

        $project =  Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'user_id' => auth()->user()->id,
            'status' => 'pending'
        ]);
        $adminUsers = User::role('admin')->first(); // Using Spatie roles
            Mail::to($adminUsers->email)->queue(new ProjectSubmittedMail($project));
        
        return response()->json(['message' => 'Project submitted successfully!']);
    }

    public function dashboard()
    {
        if (currentUserRole() == "admin") {
            $total = Project::count() ?: 0;
            $data = [
                'total' => $total,
                'pending' => Project::where('status', 'pending')->count(),
                'approved' => Project::where('status', 'approved')->count(),
                'rejected' => Project::where('status', 'rejected')->count(),
            ];

            foreach (['pending', 'approved', 'rejected'] as $key) {
                $devide=$total==0?1:$total;
                $data[$key . '_percent'] = round(($data[$key] /$devide) * 100);
            }

            $projects = Project::with(['user', 'histories.user'])->latest()->paginate(10);
        } else {
            $total = Project::where('user_id', auth()->id())->count() ?: 1;
            $data = [
                'total' => $total,
                'pending' => Project::where('status', 'pending')->where('user_id', auth()->id())->count(),
                'approved' => Project::where('status', 'approved')->where('user_id', auth()->id())->count(),
                'rejected' => Project::where('status', 'rejected')->where('user_id', auth()->id())->count(),
            ];

            foreach (['pending', 'approved', 'rejected'] as $key) {
                $divide=$total==0?1:$total;
                $data[$key . '_percent'] = round(($data[$key] / $divide) * 100);
            }

            $projects = Project::with(['user', 'histories.user'])
                ->where('user_id', auth()->id())
                ->latest()
                ->paginate(10);
        }

        return view('admin.dashboard', compact('data', 'projects'));
    }


    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected',
        ]);
    
        $project = Project::findOrFail($request->project_id);
    
        if ($request->status === 'approved') {
            try {
                $result = DB::select(
                    "CALL sp_approve_project(?, ?)",
                    [$project->id, auth()->id()]
                );
    
                if ($result[0]->status !== 'success') {
                    throw new \Exception('Approval failed');
                }
    
                event(new ProjectApproved(
                    $project->refresh(), 
                    auth()->user()
                ));
    
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Approval failed: ' . $e->getMessage()
                ], 500);
            }
        } else {
            $project->status = $request->status;
            $project->save();
        }
    
        $history = ProjectStatusHistory::create([
            'project_id' => $project->id,
            'status' => $request->status,
            'reason' => $request->rejection_reason ?? null,
            'user_id' => auth()->id(),
        ]);
    
        // Send notification email
        $userMail = User::where('id', $project->user_id)
                       ->select("email")
                       ->first();
    
        Mail::to($userMail->email)->queue(
            new ProjectStatusUpdatedMail($project, $history->status, $history->reason)
        );
    
        return response()->json([
            'message' => 'Status updated successfully',
            'new_status' => $project->status
        ]);
    }
    public function filterProjects(Request $request)
    {
        $user = auth()->user();
    
        $query = Project::with('user');
    
        if (currentUserRole() !=="admin" ) {
            $query->where('user_id', $user->id);
        }
    
        // Apply filters
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        if ($request->filled('submitter') && currentUserRole()=="admin") {
            // Only allow submitter filter if admin
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->submitter . '%');
            });
        }
    
        // Fetch results
        $projects = $query->orderBy('created_at', 'desc')->paginate(10);
    
        return response()->json([
            'html' => view('admin.partials.project_table', compact('projects'))->render()
        ]);
    }
    public function edit(Project $project)
    {
        return response()->json($project);
    }
    
    public function update(Request $request, Project $project)
{
    $project->update($request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string'
    ]));
    
    return response()->json(['message' => 'Project updated successfully']);
}

public function destroy(Project $project)
{
    $project->delete();
    return response()->json(['message' => 'Project deleted successfully']);
}
    
}
