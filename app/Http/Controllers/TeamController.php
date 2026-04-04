<?php
namespace App\Http\Controllers;
use App\Models\{Employee, Lead, Deal, Task, Commission};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller {
    public function performance() {
        $user = Auth::user();
        $isAdmin = $user->is_admin;
        $employees = $isAdmin ? Employee::with('company')->get() : Employee::where('company_id',$user->company_id)->get();
        $leaderboard = $employees->map(function($emp){
            $emp->leads_count = Lead::where('added_by',$emp->id)->count();
            $emp->deals_count = Deal::where('assigned_to',$emp->id)->count();
            $emp->deals_value = Deal::where('assigned_to',$emp->id)->sum('value');
            $emp->tasks_completed = Task::where('assigned_to',$emp->id)->where('status','completed')->count();
            $emp->tasks_pending = Task::where('assigned_to',$emp->id)->where('status','pending')->count();
            $emp->commissions = Commission::where('employee_id',$emp->id)->sum('commission_amount');
            $emp->converted_leads = Lead::where('added_by',$emp->id)->where('status','converted')->count();
            return $emp;
        })->sortByDesc('deals_value');
        $todayActivities = \App\Models\Activity::whereDate('created_at',today())->with('employee')->latest()->take(20)->get();
        return view('team.performance',compact('leaderboard','todayActivities'));
    }
}
