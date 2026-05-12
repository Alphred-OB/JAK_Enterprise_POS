<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $sale->receipt_number }}</title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 80mm; 
            margin: 0; 
            padding: 10px; 
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 15px; }
        .business-name { font-size: 18px; font-weight: 900; text-transform: uppercase; margin-bottom: 5px; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th { text-align: left; border-bottom: 1px solid #000; padding-bottom: 5px; }
        .items-table td { padding: 5px 0; vertical-align: top; }
        .totals { margin-top: 15px; }
        .totals-row { display: flex; justify-content: space-between; padding: 2px 0; }
        .grand-total { font-size: 16px; border-top: 2px solid #000; padding-top: 5px; margin-top: 5px; }
        .footer { margin-top: 20px; font-size: 10px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body @if(!request('preview')) onload="window.print()" @endif>
    @php $settings = \App\Models\Setting::first(); @endphp
    <div class="header text-center">
        <div class="business-name">{{ $settings->shop_name ?? 'JAK POS' }}</div>
        @if($settings->shop_address)
            <div style="font-size: 10px;">{{ $settings->shop_address }}</div>
        @endif
        @if($settings->shop_phone)
            <div style="font-size: 10px;">{{ $settings->shop_phone }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <div>
        <div><strong>Receipt:</strong> #{{ $sale->receipt_number }}</div>
        <div><strong>Date:</strong> {{ $sale->created_at->format('d/m/Y H:i') }}</div>
        <div><strong>Cashier:</strong> {{ $sale->user->name ?? 'N/A' }}</div>
    </div>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="totals">
        <div class="totals-row">
            <span>Subtotal:</span>
            <span>{{ $settings->currency_symbol ?? 'GH₵' }} {{ number_format($sale->subtotal, 2) }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="totals-row">
            <span>Discount:</span>
            <span>-{{ $settings->currency_symbol ?? 'GH₵' }} {{ number_format($sale->discount, 2) }}</span>
        </div>
        @endif
        <div class="totals-row font-bold grand-total">
            <span>TOTAL:</span>
            <span>{{ $settings->currency_symbol ?? 'GH₵' }} {{ number_format($sale->total, 2) }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="text-center">
        <div><strong>Payment:</strong> {{ strtoupper($sale->payment_method) }}</div>
    </div>

    <div class="footer text-center">
        @if($settings->receipt_footer)
            {!! nl2br(e($settings->receipt_footer)) !!}
        @else
            <div>THANK YOU FOR YOUR BUSINESS</div>
            <div>Items sold are not returnable</div>
        @endif
        <div style="margin-top: 5px; opacity: 0.7;">Software by JAK Solutions</div>
        <div style="margin-top: 10px;">
            *** CUSTOMER COPY ***
        </div>
    </div>

    <button class="no-print" onclick="window.close()" style="position: fixed; top: 10px; right: 10px; padding: 10px; background: #000; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
        Close Window
    </button>
</body>
</html>
