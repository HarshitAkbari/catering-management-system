<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\EventTime;
use App\Models\OrderType;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Decode events JSON if it's a string
        $eventsData = $this->input('events');
        if (is_string($eventsData)) {
            $decodedEvents = json_decode($eventsData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedEvents)) {
                $this->merge(['events' => $decodedEvents]);
            }
        }
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
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_mobile' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'events' => ['required', 'array', 'min:1'],
            'events.*.event_date' => ['required', 'date'],
            'events.*.event_time_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if (!EventTime::where('id', $value)
                        ->where(function ($q) use ($tenantId) {
                            $q->whereNull('tenant_id')
                              ->orWhere('tenant_id', $tenantId);
                        })
                        ->where('is_active', true)
                        ->exists()) {
                        $fail('The selected event time is invalid.');
                    }
                },
            ],
            'events.*.event_menu' => ['required', 'string', 'max:255'],
            'events.*.guest_count' => ['required', 'integer', 'min:1'],
            'events.*.order_type_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($tenantId) {
                    if ($value && !OrderType::where('id', $value)->where('tenant_id', $tenantId)->where('is_active', true)->exists()) {
                        $fail('The selected order type is invalid.');
                    }
                },
            ],
            'events.*.dish_price' => ['required', 'numeric', 'min:0'],
            'events.*.cost' => ['required', 'numeric', 'min:0'],
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
            'customer_name.required' => 'The customer name is required.',
            'customer_name.string' => 'The customer name must be a string.',
            'customer_name.max' => 'The customer name cannot exceed 255 characters.',
            'customer_email.required' => 'The customer email is required.',
            'customer_email.email' => 'The customer email must be a valid email address.',
            'customer_email.max' => 'The customer email cannot exceed 255 characters.',
            'customer_mobile.required' => 'The customer mobile number is required.',
            'customer_mobile.string' => 'The customer mobile number must be a string.',
            'customer_mobile.max' => 'The customer mobile number cannot exceed 20 characters.',
            'address.required' => 'The address is required.',
            'address.string' => 'The address must be a string.',
            'events.required' => 'At least one event is required.',
            'events.array' => 'Events must be an array.',
            'events.min' => 'At least one event is required.',
            'events.*.event_date.required' => 'The event date is required.',
            'events.*.event_date.date' => 'The event date must be a valid date.',
            'events.*.event_time_id.required' => 'The event time is required.',
            'events.*.event_time_id.integer' => 'The event time must be a valid selection.',
            'events.*.event_menu.required' => 'The event menu is required.',
            'events.*.event_menu.string' => 'The event menu must be a string.',
            'events.*.event_menu.max' => 'The event menu cannot exceed 255 characters.',
            'events.*.guest_count.required' => 'The guest count is required.',
            'events.*.guest_count.integer' => 'The guest count must be an integer.',
            'events.*.guest_count.min' => 'The guest count must be at least 1.',
            'events.*.order_type_id.integer' => 'The order type must be a valid selection.',
            'events.*.dish_price.required' => 'The dish price is required.',
            'events.*.dish_price.numeric' => 'The dish price must be a number.',
            'events.*.dish_price.min' => 'The dish price must be at least 0.',
            'events.*.cost.required' => 'The cost is required.',
            'events.*.cost.numeric' => 'The cost must be a number.',
            'events.*.cost.min' => 'The cost must be at least 0.',
        ];
    }
}

