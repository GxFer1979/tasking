<?php

namespace App\Http\Requests\Admin\DetailTask;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreDetailTask extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.detail-task.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'task' => ['required'],
            'state' => ['required'],
            'date_begin' => ['required', 'date'],
            'date_end' => ['required', 'date'],
            'user' => ['required'],
            'advance' => ['', ''],

        ];
    }

    /**
    * Modify input data
    *
    * @return array
    */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();

        //Add your code for manipulation with request data here

        return $sanitized;
    }

    public function getStateId()
    {
        return $this->get('state')['id'];
    }

    public function getTaskId()
    {
        return $this->get('task')['id'];
    }

    public function getUserId()
    {
        return $this->get('user')['id'];
    }
}
