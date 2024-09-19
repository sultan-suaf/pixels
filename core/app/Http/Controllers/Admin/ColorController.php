<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function all()
    {
        $pageTitle = "All Colors";
        $colors    = Color::searchable(['name'])->orderBy('name')->paginate(getPaginate());
        return view('admin.color.all', compact('pageTitle', 'colors'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'       => 'required|string|max:40|unique:colors,name,' . $id,
            'color_code' => 'required|max:6|unique:colors,color_code,' . $id
        ]);

        if ($id) {
            $color        = Color::findOrFail($id);
            $notification = "Color updated successfully";
        } else {
            $color        = new Color();
            $notification = "Color added successfully";
        }

        $color->name       = $request->name;
        $color->color_code = $request->color_code;
        $color->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        $color = Color::findOrFail($id);
        $color->delete();
        $notify[] = ['success', 'Color deleted successfully'];
        return back()->withNotify($notify);
    }
}
