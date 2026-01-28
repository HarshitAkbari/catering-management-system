<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tenantId = auth()->user()->tenant_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('staff_roles')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }
}
