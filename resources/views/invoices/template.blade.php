<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->prefix }}-{{ $invoice->number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            line-height: 1.5;
            color: #333;
            background: #fff;
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm 20mm;
            background: #fff;
        }
        
        /* Header with Logo and Invoice Info */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .logo {
            max-width: 150px;
            height: auto;
        }
        
        .logo img {
            width: 100%;
            height: auto;
        }
        
        .invoice-header-info {
            text-align: right;
            font-size: 18px;
            line-height: 1.6;
        }
        
        .invoice-header-info strong {
            font-weight: 600;
        }
        
        /* Company Info Section */
        .company-section {
            width: 100%;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .company-info, .client-info {
            width: 48%;
            font-size: 15px;
            line-height: 1.6;
            vertical-align: top;
        }
        
        .company-info {
            float: left;
        }
        
        .client-info {
            float: right;
            text-align: right;
        }
        
        .company-info p, .client-info p {
            margin-bottom: 2px;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 18px;
        }
        
        .items-table th {
            background: none;
            border-bottom: 2px solid #ddd;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Totals Section */
        .totals-section {
            margin-top: 20px;
            text-align: right;
        }
        
        .totals-table {
            margin-left: auto;
            width: 375px;
            font-size: 18px;
        }
        
        .totals-table td {
            padding: 4px 0;
            text-align: right;
        }
        
        .totals-table .label {
            text-align: right;
            padding-right: 20px;
            font-weight: 600;
        }
        
        .total-final {
            font-size: 30px;
            font-weight: bold;
            margin-top: 20px;
            text-align: right;
        }
        
        /* Footer with Bank Info */
        .footer {
            margin-top: 40px;
            font-size: 18px;
            line-height: 1.6;
        }
        
        .footer p {
            margin-bottom: 4px;
        }
        
        /* Utility Classes */
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: 600;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ public_path('images/logo.png') }}" alt="cloudstudio Logo" style="width: 150px; height: auto;">
            </div>
            <div class="invoice-header-info">
                <p><strong>Número de factura:</strong> {{ $invoice->prefix }}-{{ $invoice->number }}</p>
                <p><strong>Fecha de factura:</strong> {{ $invoice->date_issued->format('d-m-Y') }}</p>
            </div>
        </div>

        <!-- Company and Client Info -->
        <div class="company-section clearfix">
            <div class="company-info">
                @php
                    $settings = App\Models\Setting::first();
                @endphp
                <p><strong>{{ $settings->company_name ?? $invoice->organization->name }}</strong></p>
                @if($settings && $settings->vat_number)
                    <p>CIF: {{ $settings->vat_number }}</p>
                @endif
                @if($settings && $settings->company_address)
                    <p>{{ $settings->company_address }}</p>
                @endif
                @if($settings && $settings->company_phone)
                    <p>Tel: {{ $settings->company_phone }}</p>
                @endif
                @if($settings && $settings->company_website)
                    <p>{{ $settings->company_website }}</p>
                @endif
                @if($settings && $settings->company_email)
                    <p>{{ $settings->company_email }}</p>
                @endif
            </div>

            <div class="client-info">
                <p><strong>{{ $invoice->client->company_name }}</strong></p>
                @if($invoice->client->tax_id)
                    <p>CIF: {{ $invoice->client->tax_id }}</p>
                @endif
                @if($invoice->client->address)
                    <p>{{ $invoice->client->address }}</p>
                @endif
                @if($invoice->client->zip_code && $invoice->client->city)
                    <p>{{ $invoice->client->zip_code }} - {{ $invoice->client->city }}{{ $invoice->client->state ? ' ' . $invoice->client->state : '' }}{{ $invoice->client->country ? ' (' . $invoice->client->country . ')' : '' }}</p>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Concepto</th>
                    <th class="text-right">Precio</th>
                    <th class="text-center">IVA</th>
                    <th class="text-center">IRPF</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    @php
                        $currencyService = app(\App\Services\Currency\CurrencyService::class);
                        $unitPrice = $currencyService->fromBigInt($item->unit_price);
                        $taxRate = $item->tax_rate;
                        $currency = $invoice->client->currency ?? 'EUR';
                        $symbol = $currency === 'EUR' ? '&euro;' : ($currency === 'USD' ? '$' : '&pound;');
                        
                        // Calcular IRPF desde el item
                        $irpfRate = $item->irpf_rate ?? 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ number_format($unitPrice, 2, ',', '.') }}&nbsp;{!! $symbol !!}</td>
                        <td class="text-center">{{ number_format($taxRate, 0) }}%</td>
                        <td class="text-center">{{ number_format($irpfRate, 0) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #666; font-style: italic;">
                            No hay elementos en esta factura
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            @php
                $currencyService = app(\App\Services\Currency\CurrencyService::class);
                $currency = $invoice->client->currency ?? 'EUR';
                $symbol = $currency === 'EUR' ? '&euro;' : ($currency === 'USD' ? '$' : '&pound;');
                
                // Calcular totales
                $baseImponible = 0;
                $totalIVA = 0;
                $totalIRPF = 0;
                
                foreach($invoice->items as $item) {
                    $unitPrice = $currencyService->fromBigInt($item->unit_price);
                    $lineTotal = $unitPrice * $item->quantity;
                    $baseImponible += $lineTotal;
                    
                    // IVA
                    $totalIVA += $lineTotal * ($item->tax_rate / 100);
                    
                    // IRPF - usar el IRPF del item
                    $itemIrpfRate = $item->irpf_rate ?? 0;
                    $totalIRPF += $lineTotal * ($itemIrpfRate / 100);
                }
                
                $totalFactura = $baseImponible + $totalIVA - $totalIRPF;
            @endphp
            
            <table class="totals-table">
                <tr>
                    <td class="label">Base imponible:</td>
                    <td>{{ number_format($baseImponible, 2, ',', '.') }}&nbsp;{!! $symbol !!}</td>
                </tr>
                @if($totalIVA > 0)
                <tr>
                    <td class="label">Base {{ number_format($invoice->items->first()->tax_rate ?? 21, 0) }}% de IVA:</td>
                    <td>{{ number_format($totalIVA, 2, ',', '.') }}&nbsp;{!! $symbol !!}</td>
                </tr>
                @endif
                @if($totalIRPF > 0)
                <tr>
                    <td class="label">Base {{ number_format($invoice->items->first()->irpf_rate ?? 0, 0) }}% IRPF:</td>
                    <td>- {{ number_format($totalIRPF, 2, ',', '.') }}&nbsp;{!! $symbol !!}</td>
                </tr>
                @endif
                @if($totalIVA > 0)
                <tr>
                    <td class="label">Total IVA:</td>
                    <td>{{ number_format($totalIVA, 2, ',', '.') }}&nbsp;{!! $symbol !!}</td>
                </tr>
                @endif
                @if($totalIRPF > 0)
                <tr>
                    <td class="label">Total Retención:</td>
                    <td>- {{ number_format($totalIRPF, 2, ',', '.') }}&nbsp;{!! $symbol !!}</td>
                </tr>
                @endif
            </table>
            
            <div class="total-final">
                Total {{ number_format($totalFactura, 2, ',', '.') }}&nbsp;{!! $symbol !!}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            @if($settings && $settings->company_name)
                <p>El importe de la factura se abonará en la siguiente cuenta :</p>
                <p>ES10 0182 7710 4402 0165 6751</p>
                <p>BIC/SWIFT: BBVAESMMXXX</p>
            @endif
        </div>
    </div>
</body>
</html>