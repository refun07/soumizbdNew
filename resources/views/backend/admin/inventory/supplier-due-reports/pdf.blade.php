<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Supplier Due Report') }}</title>
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

        .supplier-name {
            font-weight: bold;
            max-width: 150px;
            word-wrap: break-word;
        }

        .contact-info {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }

        .contact-info div {
            margin-bottom: 2px;
        }

        .amount {
            text-align: right;
            font-weight: bold;
        }

        .amount.due {
            color: #dc3545;
        }

        .amount.paid {
            color: #28a745;
        }

        .text-center {
            text-align: center;
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

        .stat-value.due-amount {
            color: #dc3545;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .date-info {
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ getSetting('systemName') ?? 'EpikCart' }}</div>
        <h1>{{ translate('Supplier Due Report') }}</h1>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <table>
            <tr>
                <td class="label">{{ translate('Generated on') }}:</td>
                <td>{{ $generatedAt }}</td>
                <td class="label">{{ translate('Total Suppliers with Due') }}:</td>
                <td>{{ $totalSuppliersWithDue ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label">{{ translate('Total Due Amount') }}:</td>
                <td style="color: #dc3545; font-weight: bold;">{{ formatPrice($totalDueAmount ?? 0) }}</td>
                <td class="label">{{ translate('Report Type') }}:</td>
                <td>{{ translate('Suppliers Outstanding Balance') }}</td>
            </tr>
        </table>
    </div>

    <!-- Applied Filters -->
    @php
        $hasFilters = !empty($filters['search']) || !empty($filters['min_due']) || !empty($filters['max_due']) || !empty($filters['sort']);
    @endphp

    @if($hasFilters)
    <div class="filters">
        <h3>{{ translate('Applied Filters') }}:</h3>

        @if(!empty($filters['search']))
            <div class="filter-item">
                <strong>{{ translate('Search') }}:</strong> {{ $filters['search'] }}
            </div>
        @endif

        @if(!empty($filters['min_due']))
            <div class="filter-item">
                <strong>{{ translate('Minimum Due') }}:</strong> {{ formatPrice($filters['min_due']) }}
            </div>
        @endif

        @if(!empty($filters['max_due']))
            <div class="filter-item">
                <strong>{{ translate('Maximum Due') }}:</strong> {{ formatPrice($filters['max_due']) }}
            </div>
        @endif

        @if(!empty($filters['sort']))
            @php
                $sortLabel = match($filters['sort']) {
                    'supplier_name' => translate('Supplier Name'),
                    'total_due' => translate('Total Due'),
                    'total_orders' => translate('Total Orders'),
                    'last_purchase_date' => translate('Last Purchase Date'),
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
    @if($supplierDueData->count() > 0)
    <div class="summary-stats">
        <div class="stat-item">
            <span class="stat-label">{{ translate('Suppliers with Due') }}</span>
            <span class="stat-value">{{ $supplierDueData->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Due Amount') }}</span>
            <span class="stat-value due-amount">{{ formatPrice($supplierDueData->sum('total_due')) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Average Due per Supplier') }}</span>
            <span class="stat-value">{{ formatPrice($supplierDueData->avg('total_due')) }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">{{ translate('Total Orders') }}</span>
            <span class="stat-value">{{ number_format($supplierDueData->sum('total_orders')) }}</span>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    @if($supplierDueData->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">{{ translate('Supplier Name') }}</th>
                <th style="width: 20%;">{{ translate('Contact Info') }}</th>
                <th style="width: 10%;">{{ translate('Orders') }}</th>
                <th style="width: 15%;">{{ translate('Order Amount') }}</th>
                <th style="width: 15%;">{{ translate('Paid Amount') }}</th>
                <th style="width: 15%;">{{ translate('Due Amount') }}</th>
                <th style="width: 15%;">{{ translate('Last Purchase') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplierDueData as $supplier)
            <tr>
                <td>
                    <div class="supplier-name">
                        {{ $supplier->supplier_name }}
                    </div>
                </td>
                <td>
                    <div class="contact-info">
                        @if(!empty($supplier->supplier_email))
                            <div>📧 {{ $supplier->supplier_email }}</div>
                        @endif
                        @if(!empty($supplier->supplier_phone))
                            <div>📞 {{ $supplier->supplier_phone }}</div>
                        @endif
                        @if(empty($supplier->supplier_email) && empty($supplier->supplier_phone))
                            <div>{{ translate('No contact info') }}</div>
                        @endif
                    </div>
                </td>
                <td class="text-center">
                    {{ number_format($supplier->total_orders) }}
                </td>
                <td class="amount">
                    {{ formatPrice($supplier->total_order_amount ?? 0) }}
                </td>
                <td class="amount paid">
                    {{ formatPrice($supplier->total_paid ?? 0) }}
                </td>
                <td class="amount due">
                    {{ formatPrice($supplier->total_due ?? 0) }}
                </td>
                <td class="date-info">
                    @if($supplier->last_purchase_date)
                        {{ \Carbon\Carbon::parse($supplier->last_purchase_date)->format('M d, Y') }}
                    @else
                        {{ translate('N/A') }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="3">{{ translate('TOTAL') }}</td>
                <td class="amount">{{ formatPrice($supplierDueData->sum('total_order_amount')) }}</td>
                <td class="amount paid">{{ formatPrice($supplierDueData->sum('total_paid')) }}</td>
                <td class="amount due">{{ formatPrice($supplierDueData->sum('total_due')) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="no-data">
        {{ translate('No suppliers with due amounts found') }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>{{ translate('This report was generated on') }} {{ $generatedAt }}</p>
        <p>{{ getSetting('systemName') ?? 'EpikCart' }} - {{ translate('Supplier Due Management System') }}</p>
        <p>{{ translate('Note: This report shows all suppliers with outstanding due amounts') }}</p>
    </div>
</body>
</html>