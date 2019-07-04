<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'label' => 'required|unique:topics',
        ];

        foreach($this->request->get('label') as $key => $val)
        {
            $rules['label.'.$key] = 'required|unique:topics|min:4';
        }
        return $rules;
    }
}
