<?php

namespace App\Http\Requests;

use App\WaTemplate;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyWaTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return abort_if(Gate::denies('role_delete'), 403, '403 Forbidden') ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:wa_templates,id',
        ];
    }
}
