<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Product Expiry Report') }}</title>
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
            width: 150px;
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

        .variation-title {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
            font-style: italic;
        }

        .category-name {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }

        .sku {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            background: #f0f0f0;
            padding: 2px 4px;
            border-radius: 3px;
        }

        .expiry-date {
            font-weight: bold;
        }

        .days-until-expiry {
            font-weight: bold;
        }

        .days-until-expiry.expired {
            color: #dc3545;
        }

        .days-until-expiry.expiring-soon {
            color: #ff8c00;
        }

        .days-until-expiry.fresh {
            color: #28a745;
        }

        .stock-quantity {
            text-align: center;
            font-weight: bold;
        }

        .stock-value {
            text-align: right;
            font-weight: bold;
        }

        .status-badge {
            padding: 3px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            min-width: 80px;
        }

        .status-badge.expired {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-badge.expiring-soon {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-badge.fresh {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

        .stat-value.danger {
            color: #dc3545;
        }

        .stat-value.warning {
            color: #ff8c00;
        }

        .stat-value.success {
            color: #28a745;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .supplier-info {
            font-size: 10px;
            color: #666;
        }

        .warehouse-info {
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ getSetting('systemName') ?? 'EpikCart' }}</div>
        <h1>{{ translate('Product Expiry Report') }}</h1>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <table>
            <tr>
                <td class="label">{{ translate('Generated on') }}:</td>
                <td>{{ $generatedAt }}</td>
                <td class="label">{{ translate('Total Products') }}:</td>
                <td>{{ $summary['total_products'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">{{ translate('Expired Products') }}:</td>
                <td style="color: #dc3545; font-weight: bold;">{{ $summary['expired_count'] ?? 0 }}</td>
                <td class="label">{{ translate('Expiring in 7 Days') }}:</td>
                <td style="color: #ff8c00; font-weight: bold;">{{ $summary['expiring_soon_count'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">{{ translate('Total Stock Value') }}:</td>
                <td style="color: #28a745; font-weight: bold;">{{ formatPrice($summary['total_stock_value'] ?? 0) }}</td>
                <td class="label">{{ translate('Report Type') }}:</td>
                <td>{{ translate('Product Expiry Tracking') }}</td>
            </tr>
        </table>
    </div>

    <!-- Applied Filters -->
    @if(count($filters) > 0)
    <div class="filters">
        <h3>{{ translate('Applied Filters') }}:</h3>

        @foreach($filters as $key => $value)
            @if(!empty($value))
                <div class="filter-item">
                    @php
                        $filterLabel = match($key) {
                            'search' => translate('Search'),
                            'status' => translate('Status'),
                            'warehouse_id' => translate('Warehouse'),
                            'supplier_id' => translate('Supplier'),
                            'category_id' => translate('Category'),
                            'sort' => translate('Sort'),
                            default => ucfirst(str_replace('_', ' ', $key))
                        };

                        $displayValue = $value;
                        if($key === 'status') {
                            $displayValue = match($value) {
                                'expired' => translate('Expired'),
                                'expiring-soon' => translate('Expiring Soon'),
                                'fresh' => translate('Fresh'),
                                default => $value
                            };
                        }
                    @endphp
                    <strong>{{ $filterLabel }}:</strong> {{ $displayValue }}
                </div>
            @endif
        @endforeach
    </div>
    @endif

    <!-- Summary Statistics -->
    @if($stocks->count() > 0)
    <div class="summary-stats">
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Products') }}</span>
            <span class="stat-value">{{ $stocks->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Expired Items') }}</span>
            <span class="stat-value danger">{{ $stocks->where('status', 'expired')->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Expiring Soon') }}</span>
            <span class="stat-value warning">{{ $stocks->where('status', 'expiring-soon')->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Stock Value') }}</span>
            <span class="stat-value">{{ formatPrice($stocks->sum('total_value')) }}</span>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    @if($stocks->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">{{ translate('Product Information') }}</th>
                <th style="width: 10%;">{{ translate('SKU') }}</th>
                <th style="width: 12%;">{{ translate('Expiry Date') }}</th>
                <th style="width: 12%;">{{ translate('Days Until Expiry') }}</th>
                <th style="width: 8%;">{{ translate('Stock') }}</th>
                <th style="width: 15%;">{{ translate('Supplier') }}</th>
                <th style="width: 10%;">{{ translate('Warehouse') }}</th>
                <th style="width: 8%;">{{ translate('Stock Value') }}</th>
                <th style="width: 10%;">{{ translate('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr>
                <td>
                    <div class="product-name">
                        {{ $stock['product_name'] }}
                        @if(!empty($stock['variation_title']))
                            <div class="variation-title">{{ $stock['variation_title'] }}</div>
                        @endif
                        @if(!empty($stock['category_name']))
                            <div class="category-name">📁 {{ $stock['category_name'] }}</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="sku">{{ $stock['sku'] ?: 'N/A' }}</div>
                </td>
                <td>
                    <div class="expiry-date">{{ $stock['expiry_date_formatted'] }}</div>
                </td>
                <td>
                    @php
                        $daysClass = $stock['status'] === 'expired' ? 'expired' : ($stock['status'] === 'expiring-soon' ? 'expiring-soon' : 'fresh');
                        $daysText = $stock['days_until_expiry'] < 0
                            ? abs($stock['days_until_expiry']) . ' ' . translate('days ago')
                            : $stock['days_until_expiry'] . ' ' . translate('days');
                    @endphp
                    <div class="days-until-expiry {{ $daysClass }}">{{ $daysText }}</div>
                </td>
                <td class="stock-quantity">
                    {{ number_format($stock['current_stock']) }}
                </td>
                <td>
                    <div class="supplier-info">
                        {{ $stock['supplier_name'] }}
                    </div>
                </td>
                <td>
                    <div class="warehouse-info">
                        {{ $stock['warehouse_name'] }}
                    </div>
                </td>
                <td class="stock-value">
                    {{ formatPrice($stock['total_value']) }}
                </td>
                <td class="text-center">
                    <div class="status-badge {{ $stock['status'] }}">
                        @if($stock['status'] === 'expired')
                            ❌ {{ translate('Expired') }}
                        @elseif($stock['status'] === 'expiring-soon')
                            ⚠️ {{ translate('Expiring Soon') }}
                        @else
                            ✅ {{ translate('Fresh') }}
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="4">{{ translate('TOTAL') }}</td>
                <td class="stock-quantity">{{ number_format($stocks->sum('current_stock')) }}</td>
                <td colspan="2"></td>
                <td class="stock-value">{{ formatPrice($stocks->sum('total_value')) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="no-data">
        {{ translate('No products with expiry dates found matching the current filters') }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>{{ translate('This report was generated on') }} {{ $generatedAt }}</p>
        <p>{{ getSetting('systemName') ?? 'EpikCart' }} - {{ translate('Product Expiry Management System') }}</p>
        <p>{{ translate('Note: This report shows all products with expiry dates and their current status') }}</p>
        @if(!empty($sortBy) && !empty($sortDir))
            <p>{{ translate('Sorted by') }}: {{ $sortBy }} ({{ $sortDir === 'desc' ? translate('Descending') : translate('Ascending') }})</p>
        @endif
    </div>
</body>
</html>