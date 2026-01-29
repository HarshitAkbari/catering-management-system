<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $customer = $this->route('customer');
        $customerId = $customer ? $customer->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($customerId),
            ],
            'secondary_mobile' => ['nullable', 'string', 'max:20'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($customerId),
            ],
            'address' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The customer name is required.',
            'name.string' => 'The customer name must be a string.',
            'name.max' => 'The customer name cannot exceed 255 characters.',
            'mobile.required' => 'The mobile number is required.',
            'mobile.string' => 'The mobile number must be a string.',
            'mobile.max' => 'The mobile number cannot exceed 20 characters.',
            'mobile.unique' => 'A customer with this mobile number already exists.',
            'secondary_mobile.string' => 'The secondary mobile number must be a string.',
            'secondary_mobile.max' => 'The secondary mobile number cannot exceed 20 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email cannot exceed 255 characters.',
            'email.unique' => 'A customer with this email already exists.',
            'address.string' => 'The address must be a string.',
        ];
    }
}

