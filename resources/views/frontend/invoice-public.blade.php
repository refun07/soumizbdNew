<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Invoice') }} - {{ $invoiceCode }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            color: #333;
        }

        .page-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            min-height: 100vh;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .header-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .invoice-code {
            font-size: 18px;
            opacity: 0.9;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
        }

        .content {
            padding: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #e2e8f0;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .product-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f7fafc;
        }

        .product-image.placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #edf2f7;
            color: #a0aec0;
            font-size: 40px;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .footer {
            text-align: center;
            padding: 30px;
            background: #f7fafc;
            border-top: 1px solid #e2e8f0;
        }

        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .footer-text {
            color: #718096;
            font-size: 14px;
        }

        .product-count {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .content {
                padding: 20px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }

            .product-image {
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <h1>{{ translate('Invoice Verification') }}</h1>
            <div class="invoice-code">{{ $invoiceCode }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="section-title">
                <i class="fa-solid fa-box-open"></i>
                {{ translate('Products in this Invoice') }}
                <span class="product-count">{{ $products->count() }}</span>
            </div>

            <div class="products-grid">
                @forelse($products as $product)
                    <div class="product-card">
                        @if($product['image'])
                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="product-image">
                        @else
                            <div class="product-image placeholder">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                        <div class="product-info">
                            <div class="product-name">{{ $product['name'] }}</div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #718096;">
                        <i class="fa-solid fa-box-open" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>{{ translate('No products found in this invoice.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">
                {{ getSetting('systemName') ?? 'EpikCart' }}
            </div>
            <div class="footer-text">
                {{ translate('Thank you for your business!') }}
            </div>
        </div>
    </div>
</body>
</html>
