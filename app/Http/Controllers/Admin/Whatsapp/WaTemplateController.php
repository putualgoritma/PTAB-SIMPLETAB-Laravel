<?php

namespace App\Http\Controllers\Admin\Whatsapp;

use App\CategoryWa;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyWaTemplateRequest;
use App\Traits\TraitModel;
use App\WaTemplate;
use Illuminate\Http\Request;

class WaTemplateController extends Controller
{
    use TraitModel;

    public function index()
    {
        $WaTemplates = WaTemplate::selectRaw('wa_templates.id as id, wa_templates.name as name, wa_templates.message, category_was.name as category')->join('category_was', 'category_was.id', '=', 'wa_templates.category_wa_id')->get();
        return view('admin.whatsapp.template.index', compact('WaTemplates'));
    }
    public function create()
    {
        $last_code = $this->get_last_code('templateWa');

        $code = acc_code_generate($last_code, 8, 3);

        $categorys = CategoryWa::get();

        return view('admin.whatsapp.template.create', compact('code', 'categorys'));
    }
    public function store(Request $request)
    {
        WaTemplate::create($request->all());
        return redirect()->route('admin.WaTemplate.index');
    }

    public function show($id)
    {
        abort_unless(\Gate::allows('permission_show'), 403);
        $WaTemplate = WaTemplate::selectRaw('wa_templates.id as id, wa_templates.name as name, wa_templates.message, category_was.name as category, wa_templates.code as code')->join('category_was', 'category_was.id', '=', 'wa_templates.category_wa_id')->where('wa_templates.id', $id)->first();
        return view('admin..whatsapp.template.show', compact('WaTemplate'));
    }
    public function edit($id)
    {
        $categorys = CategoryWa::get();
        $WaTemplate = WaTemplate::where('id', $id)->first();
        return view('admin.whatsapp.template.edit', compact('WaTemplate', 'categorys'));
    }
    public function update($id, Request $request)
    {
        $WaTemplate = WaTemplate::where('id', $id)->first();
        $WaTemplate->update($request->all());
        return redirect()->route('admin.WaTemplate.index');
    }
    public function destroy($id)
    {
        $WaTemplate = WaTemplate::where('id', $id)->first();
        $WaTemplate->delete();
        return back();
    }
    public function massDestroy(MassDestroyWaTemplateRequest $request)
    {
        WaTemplate::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}
