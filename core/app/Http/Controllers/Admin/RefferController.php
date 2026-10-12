<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reffer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Plan;

class RefferController extends Controller
{
    public function index(){
        $pageTitle = "Reffer Setting";
        $reffers = Reffer::latest()->get();
        $plans = Plan::get();
        return view('admin.reffer.index',compact('reffers', 'pageTitle', 'plans'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:plan_subscribe_commission,ptc_view_commission',
            'level' => 'required',
            'commision' => 'required',
            'plan_id' => 'required'
        ]);


        $refferal = Reffer::where('plan_id', $request->plan_id)->where('type', $request->type)->first();


        if($refferal){
            $notify[] = ['error', 'Already Has a Referral'];

            return redirect()->route('admin.reffer.index')->withNotify($notify);
        }

        Reffer::create([
            'plan_id' => $request->plan_id,
            'type' => $request->type,
            'level' => $request->level,
            'commision' => $request->commision,
        ]);


        $notify[] = ['success', 'Referral Created Successfully'];

        return redirect()->route('admin.reffer.index')->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:plan_subscribe_commission,ptc_view_commission',
            'level' => 'required',
            'commision' => 'required',
            'plan_id' => 'required'
        ]);


        $refferal = Reffer::findOrFail($id);


        $refferal->update([
            'plan_id' => $request->plan_id,
            'type' => $request->type,
            'level' => $request->level,
            'commision' => $request->commision,
        ]);


        $notify[] = ['success', 'Referral updated Successfully'];

        return redirect()->route('admin.reffer.index')->withNotify($notify);
    }

    public function delete($id)
    {
        $refferal = Reffer::findOrFail($id);
        $refferal->delete();

        $notify[] = ['success', 'Referral Deleted Successfully'];
        return redirect()->route('admin.reffer.index')->withNotify($notify);
    }
}
