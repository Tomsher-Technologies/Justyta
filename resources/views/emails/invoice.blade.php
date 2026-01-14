<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
</head>

<body style="margin:0; padding:0px; background-color:#ffffff; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">

    <div style="max-width:900px; margin:0 auto; background-color:#ffffff; padding:10px; border-radius:10px; box-sizing:border-box;">

        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
            <tr>
                <td style="vertical-align:middle;">
                    <img src="{{ public_path('assets/img/logo.png') }}" alt="Justyta Logo" style="max-height:140px;">
                </td>
                <td style="text-align:right; vertical-align:middle;">
                    <div style="font-size:32px; font-weight:bold; color:#07683b;">INVOICE</div>
                    <div style="font-size:14px; line-height:1.6; color:#4b5563;">
                        Invoice No: {{ $invoice->invoice_no }}<br>
                        Invoice Date: {{ $invoice->paid_at->format('d M Y') }}
                    </div>
                </td>
            </tr>
        </table>

        @php
            $lang = 'en';
            $settings = \App\Models\WebsiteSetting::pluck('value', 'key')->toArray();
        @endphp
        <!-- Company & Client -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:30px;">
            <tr>
                <td style="vertical-align:top; font-size:14px; line-height:1.6;">
                    <strong>Issued By</strong><br>
                    {{ env('APP_INVOICE_NAME', 'Justyta') }}<br>
                    {!! nl2br(setting('address', $lang, $settings)) !!}<br>
                    Email: {{ $settings['email'] }}<br>
                    Website: {{ env('APP_INVOICE_SITE_NAME', 'www.justyta.com') }}
                </td>
                <td style="vertical-align:top; text-align:right; font-size:14px; line-height:1.6;">
                    <strong>Billed To</strong><br>
                    {{ $user->name }}<br>
                    {{ $user->email }}
                </td>
            </tr>
        </table>

        <!-- Divider -->
        <div style="height:1px; background:#e5e7eb; margin-bottom:30px;"></div>

        <!-- Services Table -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:40px;">
            <thead>
                <tr>
                    <th style="padding:14px; font-size:14px;background:#f9fafb; border-bottom:2px solid #e5e7eb; text-align:left;">
                        Service Description
                    </th>
                    <th style="padding:14px;font-size:14px; background:#f9fafb; border-bottom:2px solid #e5e7eb; text-align:right;">
                        Qty
                    </th>
                    
                    <th style="padding:14px;font-size:14px; background:#f9fafb; border-bottom:2px solid #e5e7eb; text-align:right;">
                        Amount
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:14px; border-bottom:1px solid #e5e7eb;font-size:14px;">
                        {{ $description }}
                    </td>
                    <td style="padding:14px; text-align:right; border-bottom:1px solid #e5e7eb;font-size:14px;">1</td>
                    <td style="padding:14px; text-align:right; border-bottom:1px solid #e5e7eb;font-size:14px;">AED {{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <table width="200" align="right" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:40px;">
            <tr>
                <td style="padding:10px;font-size:14px;">Subtotal</td>
                <td style="padding:10px; text-align:right;font-size:14px;">AED {{ number_format($invoice->amount, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:10px;font-size:14px;">VAT</td>
                <td style="padding:10px; text-align:right;font-size:14px;">AED {{ number_format($invoice->tax, 2) }}</td>
            </tr>
            <tr>
                <td style="padding:14px; font-weight:bold; border-top:2px solid #1d437e;font-size:14px;">
                    Total Payable
                </td>
                <td style="padding:14px; text-align:right; font-weight:bold; border-top:2px solid #1d437e;font-size:14px;">
                    AED {{ number_format($invoice->total, 2) }}
                </td>
            </tr>
        </table>

        <div style="clear:both;"></div>

        <!-- Legal Notes -->
        <div style="margin-top:50px; font-size:13px; line-height:1.6; color:#4b5563;">
            <strong>Important Notice</strong><br>
            This invoice reflects professional legal services facilitated through the Justyta platform.
            Fees are non-refundable once services are initiated. All services comply with UAE legal
            regulations.
        </div>

    </div>

</body>

</html>
