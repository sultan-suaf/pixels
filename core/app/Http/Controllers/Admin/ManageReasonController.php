<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class ManageReasonController extends Controller
{
    public function all()
    {
        $pageTitle = "All Reason";
        $reasons   = Reason::searchable(['title'])->orderBy('title')->paginate(getPaginate());
        return view('admin.reasons', compact('pageTitle', 'reasons'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'title'       => 'required|string|max:255|unique:reasons,title,' . $id,
            'description' => 'required|string'
        ]);

        if ($id) {
            $reason       = Reason::findOrFail($id);
            $notification = 'Reason updated successfully';
        } else {
            $reason       = new Reason();
            $notification = 'Reason added successfully';
        }

        $reason->title       = $request->title;
        $reason->description = $request->description;
        $reason->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
}
