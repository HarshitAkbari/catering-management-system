@props(['field', 'label'])

@php
    $isSorted = request('sort_by') === $field;
    $direction = request('sort_order', 'asc');
    $newDirection = $isSorted && $direction === 'asc' ? 'desc' : 'asc';
@endphp

<a href="{{ request()->fullUrlWithQuery(['sort_by' => $field, 'sort_order' => $newDirection]) }}" class="sort-link">
    {{ $label }}
    @if ($isSorted)
        <i class="fa fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
    @else
        <i class="fa fa-sort text-muted"></i>
    @endif
</a>

