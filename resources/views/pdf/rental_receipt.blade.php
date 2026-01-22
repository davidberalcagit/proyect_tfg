<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Rental Receipt') }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #1a202c; line-height: 1.6; }
        .container { width: 100%; margin: 0 auto; }

        /* Header */
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #2d3748; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a202c; text-transform: uppercase; letter-spacing: 2px; }
        .receipt-title { font-size: 18px; margin-top: 10px; color: #4a5568; }

        /* Info Grid */
        .info-section { width: 100%; margin-bottom: 30px; }
        .info-box { width: 48%; float: left; }
        .info-box.right { float: right; text-align: right; }
        .clearfix { clear: both; }

        h3 { font-size: 14px; color: #2d3748; border-bottom: 1px solid #cbd5e0; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }

        /* Encabezados */
        th { background-color: #ebf8ff; font-weight: bold; text-align: left; color: #1a365d; }

        td { color: #2d3748; }

        /* Totales */
        .totals { width: 40%; float: right; margin-top: 20px; }
        .totals table { border: 1px solid #cbd5e0; }

        /* Subtotal */
        .totals th { background-color: #ebf8ff; text-align: right; color: #1a365d; }
        .totals td { text-align: right; font-weight: bold; color: #2d3748; }

        /* Total Final */
        .grand-total { font-size: 16px; background-color: #2d3748; color: #ffffff !important; }

        /* Footer */
        .footer { margin-top: 60px; text-align: center; font-size: 10px; color: #718096; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">

        <!-- Header -->
        <div class="header">
            <div class="logo">{{ config('app.name', 'Vehicle Platform') }}</div>
            <div class="receipt-title">{{ __('OFFICIAL RENTAL RECEIPT') }}</div>
            <p>{{ __('Reference') }}: #R{{ str_pad($rental->id, 8, '0', STR_PAD_LEFT) }} | {{ __('Date') }}: {{ now()->format('d/m/Y') }}</p>
        </div>

        <!-- Partes -->
        <div class="info-section">
            <div class="info-box">
                <h3>{{ __('Lessor Information') }} ({{ __('Owner') }})</h3>
                <p><strong>{{ __('Name') }}:</strong> {{ $rental->car->vendedor->nombre_contacto }}</p>
                <p><strong>{{ __('Phone') }}:</strong> {{ $rental->car->vendedor->telefono }}</p>
                <p><strong>{{ __('Email') }}:</strong> {{ $rental->car->vendedor->user->email ?? 'N/A' }}</p>
                @if($rental->car->vendedor->dealership)
                    <p><strong>{{ __('Company') }}:</strong> {{ $rental->car->vendedor->dealership->nombre_empresa }}</p>
                    <p><strong>{{ __('NIF') }}:</strong> {{ $rental->car->vendedor->dealership->nif }}</p>
                @endif
            </div>
            <div class="info-box right">
                <h3>{{ __('Lessee Information') }} ({{ __('Customer') }})</h3>
                <p><strong>{{ __('Name') }}:</strong> {{ $rental->customer->nombre_contacto }}</p>
                <p><strong>{{ __('Phone') }}:</strong> {{ $rental->customer->telefono }}</p>
                <p><strong>{{ __('Email') }}:</strong> {{ $rental->customer->user->email ?? 'N/A' }}</p>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- Detalles del Vehículo -->
        <h3>{{ __('Vehicle Specifications') }}</h3>
        <table>
            <tr>
                <th width="25%">{{ __('Brand') }}</th>
                <td width="25%">{{ $rental->car->marca->nombre ?? $rental->car->temp_brand }}</td>
                <th width="25%">{{ __('Model') }}</th>
                <td width="25%">{{ $rental->car->modelo->nombre ?? $rental->car->temp_model }}</td>
            </tr>
            <tr>
                <th>{{ __('Year') }}</th>
                <td>{{ $rental->car->anyo_matri }}</td>
                <th>{{ __('License Plate') }}</th>
                <td>{{ strtoupper($rental->car->matricula) }}</td>
            </tr>
            <tr>
                <th>{{ __('Color') }}</th>
                <td>{{ $rental->car->color->nombre ?? $rental->car->temp_color }}</td>
                <th>{{ __('Fuel Type') }}</th>
                <td>{{ $rental->car->combustible->nombre ?? '-' }}</td>
            </tr>
        </table>

        <!-- Detalles del Alquiler -->
        <h3>{{ __('Rental Details') }}</h3>
        <table>
            <tr>
                <th>{{ __('Start Date') }}</th>
                <td>{{ $rental->fecha_inicio->format('d/m/Y') }}</td>
                <th>{{ __('End Date') }}</th>
                <td>{{ $rental->fecha_fin->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>{{ __('Duration') }}</th>
                <td>
                    @php
                        $days = $rental->fecha_inicio->diffInDays($rental->fecha_fin);
                        if ($days == 0) $days = 1;
                    @endphp
                    {{ $days }} {{ __('days') }}
                </td>
                <th>{{ __('Daily Rate') }}</th>
                <td>{{ number_format($rental->car->precio, 2) }} €</td>
            </tr>
        </table>

        <!-- Desglose Económico -->
        <h3>{{ __('Financial Breakdown') }}</h3>

        @php
            $total = $rental->precio_total;
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
                    <td>{{ __('Rental Cost') }} ({{ $days }} {{ __('days') }} x {{ number_format($rental->car->precio, 2) }} €)</td>
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

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('This receipt is an official proof of payment generated by the Vehicle Platform.') }}</p>
            <p>{{ __('For any inquiries, please contact support at support@vehicleplatform.com') }}</p>
            <p>{{ config('app.url') }}</p>
        </div>
    </div>
</body>
</html>
