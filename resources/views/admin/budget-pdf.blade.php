<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Budget Information PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; display: flex; align-items: center; }
        .section { margin-bottom: 18px; }
        .section-title { font-weight: bold; font-size: 16px; margin-bottom: 8px; color: #333; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table th, .info-table td { border: 1px solid #bbb; padding: 6px 10px; }
        .info-table th { background: #f3f3f3; text-align: left; }
        .line-items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .line-items-table th, .line-items-table td { border: 1px solid #bbb; padding: 6px 10px; }
        .line-items-table th { background: #f3f3f3; }
        .footer { margin-top: 30px; text-align: right; font-size: 12px; color: #888; }
        .header-table {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
    <table class="header-table" width="40%">
        <tr>
            <td style="width: 80px; text-align: left;">
                <img src="{{ public_path('assets/logo.png') }}" style="max-height:80px;">
            </td>
            <td style="text-align: center;">
                <h2 style="margin:0;">Paeta Budget Hub</h2>
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
        <div>{{ $budget->justification ?? 'N/A' }}</div>
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
                    <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                    <td>₱{{ number_format($item->total_cost, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="footer">
        <p>Approved and signed by:</p>
        <p>Name: <strong>{{ $budget->approved_by }}</strong></p>
        <img src="{{ $esignature }}" alt="e-signature" width="100" height="100">
    </div>
</body>
</html>
