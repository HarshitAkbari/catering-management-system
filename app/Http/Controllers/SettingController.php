<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EventType;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
        
        return view('settings.index', compact('tenant'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'nullable|array',
        ]);

        if (isset($validated['settings'])) {
            foreach ($validated['settings'] as $key => $value) {
                Setting::setValue($key, $value);
            }
        }

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }

    public function companyProfile()
    {
        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
        
        return view('settings.company-profile', compact('tenant'));
    }

    public function updateCompanyProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo_url' => 'nullable|url|max:255',
        ]);

        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
        $tenant->update($validated);

        return redirect()->route('settings.company-profile')->with('success', 'Company profile updated successfully!');
    }

    public function invoiceBranding()
    {
        $settings = [
            'invoice_logo' => Setting::getValue('invoice_logo'),
            'invoice_footer_text' => Setting::getValue('invoice_footer_text'),
            'invoice_terms' => Setting::getValue('invoice_terms'),
        ];

        return view('settings.invoice-branding', compact('settings'));
    }

    public function updateInvoiceBranding(Request $request)
    {
        $validated = $request->validate([
            'invoice_logo' => 'nullable|url|max:255',
            'invoice_footer_text' => 'nullable|string',
            'invoice_terms' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('settings.invoice-branding')->with('success', 'Invoice branding updated successfully!');
    }

    public function eventTypes()
    {
        $eventTypes = EventType::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('display_order')
            ->get();

        return view('settings.event-types', compact('eventTypes'));
    }

    public function storeEventType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        EventType::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('settings.event-types')->with('success', 'Event type created successfully!');
    }

    public function updateEventType(Request $request, EventType $eventType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $eventType->update($validated);

        return redirect()->route('settings.event-types')->with('success', 'Event type updated successfully!');
    }

    public function destroyEventType(EventType $eventType)
    {
        $eventType->delete();
        return redirect()->route('settings.event-types')->with('success', 'Event type deleted successfully!');
    }

    public function notifications()
    {
        $settings = [
            'sms_enabled' => Setting::getValue('sms_enabled', false),
            'email_enabled' => Setting::getValue('email_enabled', false),
            'low_stock_alert' => Setting::getValue('low_stock_alert', true),
            'payment_reminder' => Setting::getValue('payment_reminder', true),
            'maintenance_reminder' => Setting::getValue('maintenance_reminder', true),
        ];

        return view('settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'sms_enabled' => 'nullable|boolean',
            'email_enabled' => 'nullable|boolean',
            'low_stock_alert' => 'nullable|boolean',
            'payment_reminder' => 'nullable|boolean',
            'maintenance_reminder' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value ?? false, 'boolean');
        }

        return redirect()->route('settings.notifications')->with('success', 'Notification settings updated successfully!');
    }
}

