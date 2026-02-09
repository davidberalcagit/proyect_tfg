<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Supervisor Report') }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4a5568; padding-bottom: 15px; }
        h1 { color: #2d3748; margin: 0; font-size: 24px; }
        h2 { color: #4a5568; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-top: 25px; font-size: 16px; }
        h3 { color: #718096; font-size: 14px; margin-bottom: 10px; }

        .stats-container { width: 100%; margin-bottom: 30px; }
        .stat-box {
            float: left;
            width: 22%;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 15px 5px;
            margin-right: 3%;
            text-align: center;
            border-radius: 8px;
        }
        .stat-box:last-child { margin-right: 0; }
        .stat-value { font-size: 24px; font-weight: bold; color: #2b6cb0; margin-bottom: 5px; }
        .stat-label { font-size: 11px; color: #718096; text-transform: uppercase; letter-spacing: 1px; }
        .clearfix { clear: both; }

        .highlights { background-color: #ebf8ff; border: 1px solid #bee3f8; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .highlights ul { list-style: none; padding: 0; margin: 0; }
        .highlights li { margin-bottom: 8px; font-size: 13px; }
        .highlights strong { color: #2c5282; }
        .sub-list { margin-left: 20px; margin-top: 5px; font-size: 12px; color: #4a5568; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
        th { background-color: #edf2f7; font-weight: bold; color: #4a5568; font-size: 11px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f7fafc; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #a0aec0; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Platform General Report') }}</h1>
        <p>{{ __('Generated on') }}: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-container">
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_users'] }}</div>
            <div class="stat-label">{{ __('Total Users') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_cars'] }}</div>
            <div class="stat-label">{{ __('Total Cars') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_sales'] }}</div>
            <div class="stat-label">{{ __('Total Sales') }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $stats['total_rentals'] }}</div>
            <div class="stat-label">{{ __('Total Rentals') }}</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="highlights">
        <h3>{{ __('Highlights') }}</h3>
        <ul>
            <li>
                <strong>{{ __('Users Breakdown') }}:</strong>
                <div class="sub-list">
                    @foreach($stats['users_by_type'] as $type)
                        <span style="margin-right: 15px;">• {{ ucfirst($type->name) }}: <strong>{{ $type->total }}</strong></span>
                    @endforeach
                </div>
            </li>
            <li>
                <strong>{{ __('Best Selling Brand') }}:</strong> {{ $stats['popular_brand'] }}
            </li>
            <li>
                <strong>{{ __('Sales by Seller Type') }}:</strong>
                <div class="sub-list">
                    @foreach($stats['sales_by_type'] as $type)
                        <span style="margin-right: 15px;">• {{ ucfirst($type->nombre) }}: <strong>{{ $type->total }}</strong></span>
                    @endforeach
                </div>
            </li>
        </ul>
    </div>

    <h2>{{ __('Top 5 Sellers') }}</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">{{ __('Seller') }}</th>
                <th style="width: 25%;">{{ __('Total Sales Count') }}</th>
                <th style="width: 25%;">{{ __('Total Revenue') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topSellers as $seller)
                <tr>
                    <td>{{ $seller->nombre_contacto }}</td>
                    <td>{{ $seller->total_sales }}</td>
                    <td>{{ number_format($seller->total_revenue, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>{{ __('Recent Sales') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Vehicle') }}</th>
                <th>{{ __('Seller') }}</th>
                <th>{{ __('Buyer') }}</th>
                <th>{{ __('Price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentSales as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/Y') }}</td>
                    <td>{{ $sale->vehiculo->title }}</td>
                    <td>{{ $sale->vendedor->nombre_contacto }}</td>
                    <td>{{ $sale->comprador->nombre_contacto }}</td>
                    <td>{{ number_format($sale->precio, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>{{ __('Recent Rentals') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Vehicle') }}</th>
                <th>{{ __('Owner') }}</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentRentals as $rental)
                <tr>
                    <td>{{ $rental->fecha_inicio->format('d/m') }} - {{ $rental->fecha_fin->format('d/m/Y') }}</td>
                    <td>{{ $rental->car->title }}</td>
                    <td>{{ $rental->car->vendedor->nombre_contacto }}</td>
                    <td>{{ $rental->customer->nombre_contacto }}</td>
                    <td>{{ number_format($rental->precio_total, 2) }} €</td>
                    <td>{{ $rental->status->nombre }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ __('Confidential report for supervisor use only. Generated by the system.') }}</p>
    </div>
</body>
</html>
