<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0 !important; /* Ensure no default margin */
            padding: 0 !important; /* Ensure no default padding */
            font-size: 10px;
        }
    
        .invoice-box {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 0; /* Remove padding */
            border: 1px solid #eee;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0; /* Ensure no margin */
            padding: 0; /* Ensure no padding */
        }
    
        table td, table th {
            padding-left: 10px;
            padding-right: 10px;
            vertical-align: top;
            border: none;
            text-align: left;
        }

    
        .top {
            line-height: 13px;
        }
    
        .information td {
            padding-top: 20px;
            padding-bottom: 20px;
            line-height: 18px;
            padding-left: 0px !important;
        }
    
        .item{
            padding: 0 10px;
        }

        .item td {
            border-bottom: none;
        }
    
        .total td {
            border-top: none;
            font-weight: bold;
        }
    
        .underline {
            text-decoration: underline;
        }
    
        .wrap-text {
            word-wrap: break-word;
        }

        .body_table tr {
            line-height: 25px;
        }
    
        .heading th {
            background-color: #dbd8d8;
            font-weight: bold;
        }
    
        .amount {
            text-align: right;
            padding-right: 0; /* Remove padding */
        }
    
        .text-right {
            text-align: right;
        }
    
        .text-left {
            text-align: left;
        }
    
        .border-top {
            border-top: 1px solid #b4aaaa;
        }
    
        .spacer {
            height: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        {{-- first page --}}
        <div class="first-page" style="margin: 0; padding: 0;">
            {{-- Top Section --}}
            @php
                $contact_number = null;
                $email = null;
                $address = null;
                $postcode = null;

                foreach ($settings as $setting) {
                    if ($setting->param == 'contact_number') {
                        $contact_number = $setting->value;
                    } elseif ($setting->param == 'email') {
                        $email = $setting->value;
                    } elseif ($setting->param == 'address') {
                        $address = $setting->value;
                    } elseif ($setting->param == 'postcode') {
                        $postcode = $setting->value;
                    }
                }
            @endphp    
            <table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0; border-collapse: collapse;">
                <tr>
                    <td valign="top" style="padding: 0;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0; border-collapse: collapse;">
                            <!-- Logo Row -->
                            <tr>
                                <td style="padding: 0; line-height: 12px; border: none;">
                                    <img src="{{ public_path('images/logo.png') }}" width="70px" style="display: block; margin: 0; padding: 0;">
                                </td>
                            </tr>
                            
                            <!-- Spacer Row -->
                            <tr><td style="height: 15px; padding: 0;"></td></tr>
                    
                            <!-- Contact Info Rows -->
                            @if($contact_number)
                            <tr>
                                <td style="padding: 0; line-height: 13px; border: none;">
                                    <strong>Help Desk:</strong> {{ $contact_number }}
                                </td>
                            </tr>
                            @endif
                    
                            @if($email)
                            <tr>
                                <td style="padding: 0; line-height: 13px; border: none;">
                                    <strong>Email:</strong> {{ $email }}
                                </td>
                            </tr>
                            @endif
                    
                            @if($address)
                            <tr>
                                <td style="padding: 0; line-height: 13px; border: none;">
                                    <strong>Address:</strong> {{ $address }}
                                </td>
                            </tr>
                            @endif
                    
                            @if($postcode)
                            <tr>
                                <td style="padding: 0; line-height: 13px; border: none;">
                                    <strong>Postcode:</strong> {{ $postcode }}
                                </td>
                            </tr>
                            @endif
                        </table>
                    </td>
                
                    <td valign="top" style="padding: 0;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0; border-collapse: collapse;">
                            <tr class="information">
                                <td style="padding: 0; border: none;">
                                    <table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0; padding: 0; border-collapse: collapse;">
                                        <tr><td style="padding: 0;"><strong>Account:</strong> {{ $billing->client->account_number }}</td></tr>
                                        <tr><td style="padding: 0;"><strong>Invoice:</strong> {{ $billing->uuid }}</td></tr>
                                        <tr><td style="padding: 0;"><strong>Dated:</strong> {{ date('M d, Y', strtotime($billing->created_at)) }}</td></tr>
                                        <tr><td style="padding: 0;"><strong>Name:</strong> {{ $billing->client->client_name }}</td></tr>
                                        <tr><td style="padding: 0;"><strong>Email:</strong> {{ $billing->client->client_email }}</td></tr>
                                        <tr><td style="padding: 0;"><strong>Address:</strong> <span class="wrap-text">{{ $billing->client->client_address }}</span></td></tr>
                                        <tr><td style="padding: 0;"><strong>VAT:</strong> {{ getSetting('vat_number', '-') }}</td></tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>               
        
            {{-- Spacer --}}
            <div class="spacer" style="height: 20px;"></div>
    
            {{-- Payable Section --}}
            <table class="body_table" cellpadding="4" cellspacing="0" style="width: 100%; margin: 0; padding: 0; border-collapse: collapse;">
                <tr>
                    <td colspan="6">
                        <h2>Your Phone Bill</h2>
                    </td>
                </tr>
                <tr class="item">
                    <td colspan="5" style="border: 1px solid #b4aaaa;">Call Charges:</td>
                    <td class="amount" style="border: 1px solid #b4aaaa;">{{ $currency.$total_call_bill_amount }}</td>
                </tr>
                <tr class="item">
                    <td colspan="5" style="border: 1px solid #b4aaaa;">Service Charges:</td>
                    <td class="amount" style="border: 1px solid #b4aaaa;">{{ $currency.$total_additional_bill }}</td>
                </tr>
                @if($client->is_enable_fixed_line_services == '1')
                    <tr class="item">
                        <td colspan="5" style="border: 1px solid #b4aaaa;">Fixed Line Service Charges:</td>
                        <td class="amount" style="border: 1px solid #b4aaaa;">{{ $currency.$total_fixedLineService_amount }}</td>
                    </tr>
                @endif
                
                @foreach($billing->tax_billing_details as $tax)
                    <tr class="total">
                        <td colspan="5" style="border: 1px solid #b4aaaa; margin: 0; padding: 0;" class="amount">
                        Net Total:
                        </td>
                        <td class="amount" style="border: 1px solid #b4aaaa; margin: 0; padding: 0;"> {{ $currency.(floatval($total_fixedLineService_amount ?? 0) + floatval($total_call_bill_amount) + floatval($total_additional_bill)) }}
                        </td>
                    </tr>
                    <tr class="item">
                        <td colspan="5" style="border: 1px solid #b4aaaa; margin: 0; margin-top:25px; padding: 0;" class="text-left">{{ $tax['tax_name'] }} @ {{ $tax['tax_type'] == 'percentage' ? $tax['tax_rate'].'%' : $currency.$tax['tax_rate'] }}:
                        </td>
                        <td class="amount" style="border: 1px solid #b4aaaa; margin: 0; margin-top:25px; padding: 0;"> {{ $currency.round($tax['tax_amount'],2) }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="5" class="amount" style="border: 1px solid #b4aaaa;">Total:</td>
                    @if($client->is_enable_fixed_line_services == '1')
                        <td class="amount" style="border: 1px solid #b4aaaa;"> {{ $currency.round($total_tax_amount + $total_call_bill_amount + $total_additional_bill + $total_fixedLineService_amount,2) }}</td>
                    @else
                        <td class="amount" style="border: 1px solid #b4aaaa;"> {{ $currency.round($total_tax_amount + $total_call_bill_amount + $total_additional_bill,2) }}</td>
                    @endif
                </tr>
            </table>

            {{-- Spacer --}}
            <div class="spacer" style="height: 20px;"></div>
        
            {{-- Note Section --}}
            <table cellpadding="4" cellspacing="0" style="width: 100%; margin: 0;">
                <tr>
                    <td colspan="6" style="margin: 0; padding: 0 10px;">Note:- <strong class="underline">{{ $billing->client->notes }}</strong>
                    </td>
                </tr>
            </table>
        
            {{-- Spacer --}}
            <div class="spacer" style="height: 20px;"></div>
            <div class="spacer" style="height: 20px;"></div>
        
            {{-- Announcement Section --}}
            <table cellpadding="4" cellspacing="0" style="width: 100%; margin: 0;">
                <tr>
                    <td colspan="5" style="margin: 0; padding: 0;"><strong>
                            <img src="{{ public_path('images/announcement-svgrepo-com.svg') }}" width="15px" style="vertical-align: middle; margin: 0; padding: 0;"> Announcement:
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="margin: 0; padding: 0;">{{ getSetting('announcement', '') }}</td>
                </tr>
            </table>
        </div>

        {{-- Second Page --}}
        <div class="second-page" style="page-break-before: always;">
            {{-- Call Summary --}}
            <table class="body_table" cellpadding="4" cellspacing="0" style="width: 100%; margin: 0;">
                <tr>
                    <td colspan="6">
                        <h2>Call Summary</h2>
                    </td>
                </tr>
                <tr class="heading">
                    <th colspan="3">Description</th>
                    <th class="amount">No. of Calls</th>
                    <th class="amount">Total (Dur.)</th>
                    <th class="amount">Amount</th>
                </tr>
                @php
                    $total_duration_seconds = 0;
                    $total_count = 0;
                @endphp
                @forelse($groupedBillingDetails as $key => $billingDetail)
                    <tr class="item border-top">
                        <td colspan="3" style="border-top: 1px solid #b4aaaa;">{{ $billingDetail['simplified_to_descriptive'] }}</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ isset($billingDetail['count']) ? $billingDetail['count'] : '-' }}</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ isset($billingDetail['total_duration']) ? secondsToDuration($billingDetail['total_duration']) : '-' }}</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ isset($billingDetail['total_amount']) ? $billingDetail['total_amount'] : '-' }}</td>
                    </tr>
                    @php
                        $total_count += isset($billingDetail['count']) ? $billingDetail['count'] : 0; 
                        $total_duration_seconds += $billingDetail['total_duration'];
                    @endphp
                @empty
                    <tr class="item">
                        <td colspan="6" class="text-center">No summary available</td>
                    </tr>
                @endforelse
                <tr class="total border-top">
                    <td colspan="3" style="border-top: 1px solid #b4aaaa;">Total:</td>
                    <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ $total_count }}</td>
                    <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ secondsToDuration($total_duration_seconds)}}</td>
                    <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ $currency ?? '' }}{{ $total_call_bill_amount ?? 0 }}</td>
                </tr>
            </table>

            {{-- Fixed Line Service Summary --}}
            @if($client->is_enable_fixed_line_services == '1')
                <table class="body_table" cellpadding="4" cellspacing="0" style="width: 100%; margin: 0;">
                    <tr>
                        <td colspan="6">
                            <h2>Fixed Line Service Summary</h2>
                        </td>
                    </tr>
                    <tr class="heading">
                        <th colspan="3">Description</th>
                        <th>No. of Calls</th>
                        <th>Total (Dur.)</th>
                        <th class="amount">Amount</th>
                    </tr>
                    @php
                        $total_fixedLineService_duration_seconds = 0;
                        $total_fixedLineService_count = 0;
                    @endphp
                    @forelse($groupedFixedLineServiceBillingDetails as $key => $fixedLinebillingDetail)
                        <tr class="item border-top">
                            <td colspan="3" style="border-top: 1px solid #b4aaaa;">{{ $fixedLinebillingDetail['simplified_to_descriptive'] }}</td>
                            <td style="border-top: 1px solid #b4aaaa;">{{ isset($fixedLinebillingDetail['count']) ? $fixedLinebillingDetail['count'] : '-' }}</td>
                            <td style="border-top: 1px solid #b4aaaa;">{{ isset($fixedLinebillingDetail['total_duration']) ? secondsToDuration($fixedLinebillingDetail['total_duration']) : '-' }}</td>
                            <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ isset($fixedLinebillingDetail['total_amount']) ? $fixedLinebillingDetail['total_amount'] : '-' }}</td>
                        </tr>
                        @php
                            $total_fixedLineService_count += isset($fixedLinebillingDetail['count']) ? $fixedLinebillingDetail['count'] : 0; 
                            $total_fixedLineService_duration_seconds += $fixedLinebillingDetail['total_duration'];
                        @endphp
                    @empty
                        <tr class="item">
                            <td colspan="6" class="text-center">No summary available</td>
                        </tr>
                    @endforelse
                    <tr class="total border-top">
                        <td colspan="3" style="border-top: 1px solid #b4aaaa;">Total:</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ $total_fixedLineService_count }}</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ secondsToDuration($total_fixedLineService_duration_seconds)}}</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ $currency ?? '' }}{{ $total_fixedLineService_amount ?? 0 }}</td>
                    </tr>
                </table>
            @endif

            {{-- Service Charges --}}
            <table class="body_table" cellpadding="4" cellspacing="0" style="width: 100%; margin: 0;">
                <tr>
                    <td colspan="7">
                        <h2>Service Charges</h2>
                    </td>
                </tr>
                <tr class="heading" style="font-size: 8.5px;">
                    <th width="30%">Item</th>
                    <th width="10%">From Date</th>
                    <th width="10%">To Date</th>
                    <th width="7%">Qty</th>
                    <th width="25%">Description</th>
                    <th width="8%">Freq.</th>
                    <th class="amount" width="10%">Amount</th>
                </tr>
                @forelse($groupedAdditionalBillDetails as $adBills)
                    <tr class="item border-top" style="font-size:7.5px;">
                        <td style="border-top: 1px solid #b4aaaa;">{{ $adBills['item_name'] }}</td>
                        <td style="border-top: 1px solid #b4aaaa;">{{ date('d/m/Y', strtotime($adBills['start_from'])) }}</td>
                        <td style="border-top: 1px solid #b4aaaa;">{{ date('d/m/Y', strtotime($adBills['end_to'])) }}</td>
                        <td class="text-center" style="border-top: 1px solid #b4aaaa;">{{ $adBills['qty'] }}</td>
                        <td style="border-top: 1px solid #b4aaaa;">{{ $adBills['description'] }}</td>
                        <td style="border-top: 1px solid #b4aaaa;">{{ ucwords($adBills['frequency']) }}</td>
                        <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ $currency.$adBills['total_amount'] }}</td>
                    </tr>
                @empty
                    <tr class="item">
                        <td colspan="7" class="text-center">No charges available</td>
                    </tr>
                @endforelse
                <tr class="total border-top">
                    <td colspan="6" style="border-top: 1px solid #b4aaaa;">Total:</td>
                    <td class="amount" style="border-top: 1px solid #b4aaaa;">{{ $currency.$total_additional_bill }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>