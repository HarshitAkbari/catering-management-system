@extends('layouts.app')

@section('title', 'Invoice Branding')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Invoice Branding</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('settings.invoice-branding.update') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Invoice Logo URL</label><input type="url" name="invoice_logo" value="{{ old('invoice_logo', $settings['invoice_logo'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">@error('invoice_logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Footer Text</label><textarea name="invoice_footer_text" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('invoice_footer_text', $settings['invoice_footer_text'] ?? '') }}</textarea>@error('invoice_footer_text')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Terms & Conditions</label><textarea name="invoice_terms" rows="5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('invoice_terms', $settings['invoice_terms'] ?? '') }}</textarea>@error('invoice_terms')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('settings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Branding</button>
            </div>
        </form>
    </div>
</div>
@endsection

