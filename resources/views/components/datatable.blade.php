@props([
    'tableId' => null,
    'pageLength' => 15,
    'lengthMenu' => null,
    'nonOrderableColumns' => [],
    'customOptions' => null,
])

@php
    // Laravel automatically converts kebab-case (table-id) to camelCase (tableId)
    if (!$tableId) {
        throw new \Exception('DataTable component requires a table-id attribute');
    }

    // Default length menu if not provided
    if ($lengthMenu === null) {
        $lengthMenu = [[10, 15, 25, 50, -1], [10, 15, 25, 50, "All"]];
    }

    // Build columnDefs for non-orderable columns
    $columnDefs = [];
    if (!empty($nonOrderableColumns)) {
        foreach ($nonOrderableColumns as $columnIndex) {
            $columnDefs[] = ['orderable' => false, 'targets' => (int)$columnIndex];
        }
    }

    // Default language configuration
    $defaultLanguage = [
        'search' => 'Search:',
        'lengthMenu' => 'Show _MENU_ entries',
        'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
        'infoEmpty' => 'Showing 0 to 0 of 0 entries',
        'infoFiltered' => '(filtered from _MAX_ total entries)',
        'paginate' => [
            'next' => '<i class="bi bi-chevron-right" aria-hidden="true"></i>',
            'previous' => '<i class="bi bi-chevron-left" aria-hidden="true"></i>'
        ]
    ];

    // Build DataTable options
    $dataTableOptions = [
        'pageLength' => (int)$pageLength,
        'lengthMenu' => $lengthMenu,
        'language' => $defaultLanguage,
    ];

    // Add columnDefs if there are any
    if (!empty($columnDefs)) {
        $dataTableOptions['columnDefs'] = $columnDefs;
    }

    // Merge custom options if provided
    if ($customOptions !== null) {
        if (is_string($customOptions)) {
            $customOptions = json_decode($customOptions, true);
        }
        if (is_array($customOptions)) {
            $dataTableOptions = array_merge($dataTableOptions, $customOptions);
        }
    }

    // Convert to JSON for JavaScript
    $optionsJson = json_encode($dataTableOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

<script>
    $(document).ready(function() {
        $('#{{ $tableId }}').DataTable({!! $optionsJson !!});
    });
</script>

