<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Budget Information PDF</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #222; margin: 0; padding: 20px; }
        .header-table { margin-bottom: 20px; width: 100%; }
        .section { margin-bottom: 18px; }
        .section-title { font-weight: bold; font-size: 16px; margin-bottom: 8px; color: #333; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table th, .info-table td { border: 1px solid #bbb; padding: 6px 10px; }
        .info-table th { background: #f3f3f3; text-align: left; }
        
        .line-items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .line-items-table th, .line-items-table td { border: 1px solid #bbb; padding: 6px 10px; }
        .line-items-table th { background: #f3f3f3; }

        /* THE SIGNATURE BOX */
        .footer-table {
            width: 100%;
            margin-top: 60px;
        }

        .sig-container {
            width: 320px;
            text-align: center;
        }

        .approval-text {
            margin-bottom: 10px; 
            color: #555;
            font-size: 12px;
        }

        .signature-wrapper {
            position: relative;
            display: inline-block;
            width: 280px; 
            height: 100px; 
            border-bottom: 1px solid #000;
            text-align: center;
        }

        .printed-name {
            position: absolute;
            bottom: 2px; 
            left: 0;
            right: 0;
            font-size: 13px;
            font-weight: normal; 
            text-transform: uppercase;
            z-index: 1; 
        }

        .signature-img {
            position: absolute;
            bottom: -15px;
            left: 50%;
            margin-left: -100px; /* Centering a 200px wide image */
            width: 200px; 
            height: 100px; 
            object-fit: contain; 
            z-index: 999; 
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 80px; text-align: left;">
                <img src="{{ public_path('assets/logo.png') }}" style="max-height:80px;">
            </td>
            <td style="text-align: center;">
                <h2 style="margin:0;">Paete Budget Hub</h2>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Budget Request</div>
        <table class="info-table">
            <tr><th>Title</th><td>{{ $budget->title }}</td></tr>
            <tr><th>Name of Submitter</th><td>{{ $budget->user->full_name }}</td></tr>
            <tr><th>Due Date</th><td>{{ $budget->submission_date ? $budget->submission_date->format('M d, Y') : '' }}</td></tr>
            <tr><th>Department</th><td>{{ $budget->department->name ?? 'N/A' }}</td></tr>
            <tr><th>Fiscal Year</th><td>{{ $budget->fiscal_year }}</td></tr>
            <tr><th>Budget Category</th><td>{{ $budget->category }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Justification</div>
        <div style="padding: 5px; border: 1px solid #eee; min-height: 40px;">
            {{ $budget->justification ?? 'N/A' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Budget Line Items</div>
        <table class="line-items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budget->lineItems as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>&#8369;{{ number_format($item->unit_cost, 2) }}</td>
                    <td>&#8369;{{ number_format($item->total_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="footer-table">
        <tr>
            <td width="55%"></td>
            <td class="sig-container">
                <div class="approval-text">Approved and signed by:</div>
                
                <div class="signature-wrapper">
                    <span class="printed-name">
                        {{ $budget->approved_by }}
                    </span>

                    @if($esignature)
                        <img src="{{ $esignature }}" class="signature-img">
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>