<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Sale Receipt') }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #1a202c; line-height: 1.6; } /* Texto base casi negro */
        .container { width: 100%; margin: 0 auto; }

        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #2d3748; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a202c; text-transform: uppercase; letter-spacing: 2px; }
        .receipt-title { font-size: 18px; margin-top: 10px; color: #4a5568; }

        .info-section { width: 100%; margin-bottom: 30px; }
        .info-box { width: 48%; float: left; }
        .info-box.right { float: right; text-align: right; }
        .clearfix { clear: both; }

        h3 { font-size: 14px; color: #2d3748; border-bottom: 1px solid #cbd5e0; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }

        th { background-color: #ebf8ff; font-weight: bold; text-align: left; color: #1a365d; }

        td { color: #2d3748; }

        .totals { width: 40%; float: right; margin-top: 20px; }
        .totals table { border: 1px solid #bee3f8; }

        .totals th { background-color: #ebf8ff; text-align: right; color: #1a365d; }

        .totals td { text-align: right; font-weight: bold; color: #2d3748; }

        .grand-total { font-size: 16px; background-color: #2b6cb0; color: #ffffff !important; }

        .footer { margin-top: 60px; text-align: center; font-size: 10px; color: #718096; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
            <div class="logo">{{ config('app.name', 'Vehicle Platform') }}</div>
            <div class="receipt-title">{{ __('OFFICIAL SALE RECEIPT') }}</div>
            <p>{{ __('Reference') }}: #{{ str_pad($sale->id, 8, '0', STR_PAD_LEFT) }} | {{ __('Date') }}: {{ $sale->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>{{ __('Seller Information') }}</h3>
                <p><strong>{{ __('Name') }}:</strong> {{ $sale->vendedor->nombre_contacto }}</p>
                <p><strong>{{ __('Phone') }}:</strong> {{ $sale->vendedor->telefono }}</p>
                <p><strong>{{ __('Email') }}:</strong> {{ $sale->vendedor->user->email ?? 'N/A' }}</p>
                @if($sale->vendedor->dealership)
                    <p><strong>{{ __('Company') }}:</strong> {{ $sale->vendedor->dealership->nombre_empresa }}</p>
                    <p><strong>{{ __('NIF') }}:</strong> {{ $sale->vendedor->dealership->nif }}</p>
                @endif
            </div>
            <div class="info-box right">
                <h3>{{ __('Buyer Information') }}</h3>
                <p><strong>{{ __('Name') }}:</strong> {{ $sale->comprador->nombre_contacto }}</p>
                <p><strong>{{ __('Phone') }}:</strong> {{ $sale->comprador->telefono }}</p>
                <p><strong>{{ __('Email') }}:</strong> {{ $sale->comprador->user->email ?? 'N/A' }}</p>
            </div>
            <div class="clearfix"></div>
        </div>

        <h3>{{ __('Vehicle Specifications') }}</h3>
        <table>
            <tr>
                <th width="25%">{{ __('Brand') }}</th>
                <td width="25%">{{ $sale->vehiculo->marca->nombre ?? $sale->vehiculo->temp_brand }}</td>
                <th width="25%">{{ __('Model') }}</th>
                <td width="25%">{{ $sale->vehiculo->modelo->nombre ?? $sale->vehiculo->temp_model }}</td>
            </tr>
            <tr>
                <th>{{ __('Year') }}</th>
                <td>{{ $sale->vehiculo->anyo_matri }}</td>
                <th>{{ __('Mileage') }}</th>
                <td>{{ number_format($sale->vehiculo->km) }} km</td>
            </tr>
            <tr>
                <th>{{ __('Color') }}</th>
                <td>{{ $sale->vehiculo->color->nombre ?? $sale->vehiculo->temp_color }}</td>
                <th>{{ __('License Plate') }}</th>
                <td>{{ strtoupper($sale->vehiculo->matricula) }}</td>
            </tr>
            <tr>
                <th>{{ __('Fuel Type') }}</th>
                <td>{{ $sale->vehiculo->combustible->nombre ?? '-' }}</td>
                <th>{{ __('Transmission') }}</th>
                <td>{{ $sale->vehiculo->marcha->tipo ?? '-' }}</td>
            </tr>
        </table>

        <h3>{{ __('Financial Breakdown') }}</h3>

        @php
            $total = $sale->precio;
            $serviceFee = $total * 0.05;
            $tax = $serviceFee * 0.21;
            $grandTotal = $total + $serviceFee + $tax;
        @endphp

        <table>
            <thead>
                <tr>
                    <th>{{ __('Description') }}</th>
                    <th style="text-align: right;">{{ __('Amount') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('Vehicle Price') }} ({{ $sale->vehiculo->title }})</td>
                    <td style="text-align: right;">{{ number_format($total, 2) }} €</td>
                </tr>
                <tr>
                    <td>{{ __('Platform Service Fee') }} (5%)</td>
                    <td style="text-align: right;">{{ number_format($serviceFee, 2) }} €</td>
                </tr>
                <tr>
                    <td>{{ __('VAT') }} (21% {{ __('on Service Fee') }})</td>
                    <td style="text-align: right;">{{ number_format($tax, 2) }} €</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <th>{{ __('Subtotal') }}</th>
                    <td>{{ number_format($total + $serviceFee, 2) }} €</td>
                </tr>
                <tr>
                    <th>{{ __('TOTAL PAID') }}</th>
                    <td class="grand-total">{{ number_format($grandTotal, 2) }} €</td>
                </tr>
            </table>
        </div>
        <div class="clearfix"></div>

        <div class="footer">
            <p>{{ __('This receipt is an official proof of payment generated by the Vehicle Platform.') }}</p>
            <p>{{ __('For any inquiries, please contact support at support@vehicleplatform.com') }}</p>
            <p>{{ config('app.url') }}</p>
        </div>
    </div>
</body>
</html>
