<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <h2>INVOICE</h2>
                            </td>
                            <td>
                                Invoice #: {{ $invoice->invoice_number }}<br>
                                Created: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}<br>
                                Status: {{ ucfirst($invoice->payment_status ?? 'Unpaid') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                {{ config('app.name', 'Digital Rakshak') }}<br>
                                admin@digitalrakshak.com
                            </td>
                            <td>
                                Bill To:<br>
                                {{ $client->company_name ?? $client->name ?? 'Client' }}<br>
                                {{ $client->email ?? '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td class="text-center">Quantity</td>
                <td class="text-center">Unit Price</td>
                <td class="text-right">Total Price</td>
            </tr>

            @foreach ($items as $index => $item)
            <tr class="item {{ $loop->last ? 'last' : '' }}">
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">₹{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach

            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right">Subtotal:</td>
                <td class="text-right">₹{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right">Tax ({{ $invoice->tax_percentage ?? 0 }}%):</td>
                <td class="text-right">₹{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount_amount > 0)
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right">Discount:</td>
                <td class="text-right">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right">Total:</td>
                <td class="text-right">₹{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right">Amount Due:</td>
                <td class="text-right">₹{{ number_format($invoice->amount_due ?? $invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
