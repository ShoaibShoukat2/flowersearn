<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardItem;
use App\Models\RewardPageSetting;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Rewards Management';
        $settings = RewardPageSetting::current();
        $items = RewardItem::ordered()->get();

        return view('admin.rewards.index', compact('pageTitle', 'settings', 'items'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'page_title' => 'required|string|max:255',
            'page_subtitle' => 'nullable|string|max:500',
            'tips_title' => 'nullable|string|max:255',
            'tips_content' => 'nullable|string',
        ]);

        $settings = RewardPageSetting::current();
        $settings->update([
            'page_title' => $request->page_title,
            'page_subtitle' => $request->page_subtitle,
            'tips_title' => $request->tips_title,
            'tips_content' => $request->tips_content,
            'show_levels' => $request->boolean('show_levels'),
            'show_on_dashboard' => $request->boolean('show_on_dashboard'),
        ]);

        $notify[] = ['success', 'Rewards page settings updated successfully.'];
        return back()->withNotify($notify);
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        RewardItem::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'badge' => $request->badge,
            'sort_order' => $request->input('sort_order', 0),
            'status' => $request->boolean('status', true),
        ]);

        $notify[] = ['success', 'Reward item added successfully.'];
        return back()->withNotify($notify);
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $item = RewardItem::findOrFail($id);
        $item->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'badge' => $request->badge,
            'sort_order' => $request->input('sort_order', 0),
            'status' => $request->boolean('status'),
        ]);

        $notify[] = ['success', 'Reward item updated successfully.'];
        return back()->withNotify($notify);
    }

    public function deleteItem($id)
    {
        RewardItem::findOrFail($id)->delete();

        $notify[] = ['success', 'Reward item deleted successfully.'];
        return back()->withNotify($notify);
    }

    public function toggleItem($id)
    {
        $item = RewardItem::findOrFail($id);
        $item->status = !$item->status;
        $item->save();

        $notify[] = ['success', 'Reward item status updated successfully.'];
        return back()->withNotify($notify);
    }
}
