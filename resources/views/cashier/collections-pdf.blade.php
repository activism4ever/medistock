<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: sans-serif; font-size: 11px; color: #1f2937; }
  .header { background:#1e3a5f; color:white; padding:18px 24px; margin-bottom:18px; }
  .header h1 { font-size:18px; font-weight:700; }
  .header p  { font-size:10px; opacity:0.8; margin-top:2px; }
  .meta { float:right; text-align:right; margin-top:-36px; font-size:10px; opacity:0.9; }
  .cards { width:100%; margin-bottom:18px; border-collapse:separate; border-spacing:8px; display:table; }
  .card { display:table-cell; width:25%; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; }
  .card .lbl { font-size:9px; color:#64748b; text-transform:uppercase; margin-bottom:3px; }
  .card .val { font-size:16px; font-weight:700; color:#1e3a5f; }
  .card.green .val { color:#16a34a; }
  table.main { width:100%; border-collapse:collapse; }
  table.main thead tr { background:#1e3a5f; color:white; }
  table.main thead th { padding:8px 10px; text-align:left; font-size:9px; text-transform:uppercase; }
  table.main thead th.r { text-align:right; }
  table.main tbody tr:nth-child(even) { background:#f8fafc; }
  table.main tbody td { padding:7px 10px; border-bottom:1px solid #e2e8f0; font-size:10px; }
  table.main tbody td.r { text-align:right; }
  table.main tfoot tr { background:#1e3a5f; color:white; }
  table.main tfoot td { padding:9px 10px; font-weight:700; }
  table.main tfoot td.r { text-align:right; }
  .badge { background:#e0e7ff; color:#3730a3; padding:1px 6px; border-radius:99px; font-size:9px; }
  .badge.green { background:#dcfce7; color:#166534; }
  .footer { margin-top:14px; padding-top:10px; border-top:1px solid #e2e8f0; display:table; width:100%; }
  .footer .l { display:table-cell; font-size:9px; color:#94a3b8; }
  .footer .r { display:table-cell; text-align:right; font-size:9px; color:#94a3b8; }
</style>
</head>
<body>
<div class="header">
  <h1>MediStock POS — Collections Report</h1>
  <p>Cashier: {{ $user->name }}</p>
  <div class="meta">
    Period: <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong><br>
    Generated: {{ now()->format('d M Y, H:i') }}
  </div>
</div>

<div class="cards">
  <div class="card">
    <div class="lbl">Total Collected</div>
    <div class="val">NGN {{ number_format($summary['total_amount'],2) }}</div>
  </div>
  <div class="card">
    <div class="lbl">Normal Sales</div>
    <div class="val">NGN {{ number_format($summary['normal_amount'],2) }}</div>
    <div style="font-size:9px;color:#64748b">{{ $summary['normal_count'] }} invoices</div>
  </div>
  <div class="card green">
    <div class="lbl">Insurance Co-payments</div>
    <div class="val">NGN {{ number_format($summary['insurance_amount'],2) }}</div>
    <div style="font-size:9px;color:#64748b">{{ $summary['insurance_count'] }} invoices</div>
  </div>
  <div class="card">
    <div class="lbl">Total Invoices</div>
    <div class="val">{{ $summary['total_count'] }}</div>
  </div>
</div>

<table class="main">
  <thead>
    <tr>
      <th>#</th>
      <th>Invoice No.</th>
      <th>Patient</th>
      <th>Type</th>
      <th>Drawer</th>
      <th class="r">Total (NGN)</th>
      <th class="r">Collected (NGN)</th>
      <th>Paid At</th>
    </tr>
  </thead>
  <tbody>
    @foreach($invoices as $i => $inv)
    <tr>
      <td>{{ $i+1 }}</td>
      <td style="font-family:monospace">{{ $inv->invoice_number }}</td>
      <td>{{ $inv->patient_name }}</td>
      <td>
        @if($inv->isInsurance())
        <span class="badge green">{{ $inv->insuranceScheme?->name }}</span>
        @else
        <span class="badge">Normal</span>
        @endif
      </td>
      <td>{{ $inv->drawer_number ? 'Drawer '.$inv->drawer_number : '—' }}</td>
      <td class="r">{{ number_format($inv->total_amount,2) }}</td>
      <td class="r"><strong>{{ number_format($inv->isInsurance() ? $inv->copayment_amount : $inv->total_amount, 2) }}</strong></td>
      <td>{{ $inv->paid_at?->format('d M Y H:i') }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="6" class="r">TOTALS</td>
      <td class="r">NGN {{ number_format($summary['total_amount'],2) }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>

<div class="footer">
  <div class="l">MediStock POS — Confidential</div>
  <div class="r">{{ $invoices->count() }} records | {{ now()->format('d/m/Y H:i:s') }}</div>
</div>
</body>
</html>