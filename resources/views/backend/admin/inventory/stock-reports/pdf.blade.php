<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Stock Report') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header .company-name {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .report-info {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .report-info table {
            width: 100%;
            margin: 0;
        }

        .report-info td {
            padding: 5px 0;
            vertical-align: top;
        }

        .report-info .label {
            font-weight: bold;
            width: 120px;
        }

        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 5px;
        }

        .filters h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .product-name {
            font-weight: bold;
            max-width: 200px;
            word-wrap: break-word;
        }

        .variation-info {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
            font-style: italic;
        }

        .stock-amount {
            text-align: center;
            font-weight: bold;
        }

        .stock-value {
            text-align: right;
            font-weight: bold;
        }

        .low-stock {
            color: #dc3545;
        }

        .low-stock-icon {
            color: #ffc107;
            margin-left: 5px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .summary-stats {
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }

        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        .stat-label {
            display: block;
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 14px;
            color: #333;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ getSetting('systemName') ?? 'EpikCart' }}</div>
        <h1>{{ translate('Stock Report') }}</h1>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <table>
            <tr>
                <td class="label">{{ translate('Generated on') }}:</td>
                <td>{{ $generatedAt }}</td>
                <td class="label">{{ translate('Total Products') }}:</td>
                <td>{{ $stockData->count() }}</td>
            </tr>
        </table>
    </div>

    <!-- Applied Filters -->
    @php
        $hasFilters = !empty($filters['search']) || !empty($filters['categoryId']) || !empty($filters['brandId']) || !empty($filters['warehouseId']) || !empty($filters['sort']);
    @endphp

    @if($hasFilters)
    <div class="filters">
        <h3>{{ translate('Applied Filters') }}:</h3>

        @if(!empty($filters['search']))
            <div class="filter-item">
                <strong>{{ translate('Search') }}:</strong> {{ $filters['search'] }}
            </div>
        @endif

        @if(!empty($filters['categoryId']))
            @php
                $category = $categories->find($filters['categoryId']);
            @endphp
            <div class="filter-item">
                <strong>{{ translate('Category') }}:</strong> {{ $category ? $category->collectTranslation('name') : 'ID: ' . $filters['categoryId'] }}
            </div>
        @endif

        @if(!empty($filters['brandId']))
            @php
                $brand = $brands->find($filters['brandId']);
            @endphp
            <div class="filter-item">
                <strong>{{ translate('Brand') }}:</strong> {{ $brand ? $brand->collectTranslation('name') : 'ID: ' . $filters['brandId'] }}
            </div>
        @endif

        @if(!empty($filters['warehouseId']))
            @php
                $warehouse = $warehouses->find($filters['warehouseId']);
            @endphp
            <div class="filter-item">
                <strong>{{ translate('Warehouse') }}:</strong> {{ $warehouse ? $warehouse->name : 'ID: ' . $filters['warehouseId'] }}
            </div>
        @endif

        @if(!empty($filters['sort']))
            @php
                $sortLabel = match($filters['sort']) {
                    'product_name' => translate('Product Name'),
                    'stock_amount' => translate('Stock Amount'),
                    'stock_value' => translate('Stock Value'),
                    default => $filters['sort']
                };
                $direction = ($filters['sort_dir'] ?? 'asc') === 'desc' ? translate('Descending') : translate('Ascending');
            @endphp
            <div class="filter-item">
                <strong>{{ translate('Sort') }}:</strong> {{ $sortLabel }} ({{ $direction }})
            </div>
        @endif
    </div>
    @endif

    <!-- Summary Statistics -->
    @if($stockData->count() > 0)
    <div class="summary-stats">
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Products') }}</span>
            <span class="stat-value">{{ $stockData->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Stock') }}</span>
            <span class="stat-value">{{ number_format($stockData->sum('total_stock')) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Value') }}</span>
            <span class="stat-value">{{ formatPrice($stockData->sum('total_stock_value')) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Low Stock Items') }}</span>
            <span class="stat-value">{{ $stockData->where('total_stock', '<=', function($item) { return $item->alert_qty; })->count() }}</span>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    @if($stockData->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">{{ translate('Product Name') }}</th>
                <th style="width: 20%;">{{ translate('Stock Amount') }}</th>
                <th style="width: 20%;">{{ translate('Stock Value') }}</th>
                <th style="width: 20%;">{{ translate('Variations') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockData as $product)
            <tr>
                <td>
                    <div class="product-name">
                        {{ $product->collectTranslation('name') }}
                    </div>
                </td>
                <td class="stock-amount {{ ($product->total_stock ?? 0) <= $product->alert_qty ? 'low-stock' : '' }}">
                    {{ number_format($product->total_stock ?? 0) }}
                    @if(($product->total_stock ?? 0) <= $product->alert_qty)
                        <span class="low-stock-icon">⚠</span>
                    @endif
                </td>
                <td class="stock-value">
                    {{ formatPrice($product->total_stock_value ?? 0) }}
                </td>
                <td>
                    @if(!empty($product->variation_stocks))
                        <div class="variation-info">
                            @foreach($product->variation_stocks as $variationName => $stockQty)
                                <div>{{ $variationName }}: {{ $stockQty }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="variation-info">{{ translate('No variations') }}</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        {{ translate('No stock data found') }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>{{ translate('This report was generated on') }} {{ $generatedAt }}</p>
        <p>{{ getSetting('systemName') ?? 'EpikCart' }} - {{ translate('Stock Management System') }}</p>
    </div>
</body>
</html>