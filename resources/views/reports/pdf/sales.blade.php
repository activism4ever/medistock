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
  .card { display:table-cell; width:33%; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:10px 14px; }
  .card .lbl { font-size:9px; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-bottom:3px; }
  .card .val { font-size:17px; font-weight:700; color:#1e3a5f; }
  .card.green .val { color:#16a34a; }
  .card.blue  .val { color:#2563eb; }
  table.main { width:100%; border-collapse:collapse; }
  table.main thead tr { background:#1e3a5f; color:white; }
  table.main thead th { padding:8px 10px; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:.04em; }
  table.main thead th.r { text-align:right; }
  table.main tbody tr:nth-child(even) { background:#f8fafc; }
  table.main tbody td { padding:7px 10px; border-bottom:1px solid #e2e8f0; font-size:10px; }
  table.main tbody td.r { text-align:right; }
  table.main tbody td.mono { font-family:monospace; color:#2563eb; }
  .badge { background:#e0e7ff; color:#3730a3; padding:1px 6px; border-radius:99px; font-size:9px; }
  table.main tfoot tr { background:#1e3a5f; color:white; }
  table.main tfoot td { padding:9px 10px; font-weight:700; }
  table.main tfoot td.r { text-align:right; }
  .footer { margin-top:14px; padding-top:10px; border-top:1px solid #e2e8f0; display:table; width:100%; }
  .footer .l { display:table-cell; font-size:9px; color:#94a3b8; }
  .footer .r { display:table-cell; text-align:right; font-size:9px; color:#94a3b8; }
  .nodata { text-align:center; padding:40px; color:#94a3b8; }
</style>
</head>
<body>

<div class="header">
  <h1>MediStock POS &mdash; Sales Report</h1>
  <p>Hospital Medicine Stock &amp; Point of Sale System</p>
  <div class="meta">
    Period: <strong>{{ $periodLabel }}</strong><br>
    Generated: {{ now()->format('d M Y, H:i') }}<br>
    By: {{ auth()->user()->name }}
  </div>
</div>

<div class="cards">
  <div class="card">
    <div class="lbl">Total Revenue</div>
    <div class="val">NGN{{ number_format($summary['total_amount'],2) }}</div>
  </div>
  <div class="card green">
    <div class="lbl">Total Profit</div>
    <div class="val">NGN{{ number_format($summary['total_profit'],2) }}</div>
  </div>
  <div class="card blue">
    <div class="lbl">Transactions</div>
    <div class="val">{{ number_format($summary['total_count']) }}</div>
  </div>
</div>

@if($sales->count() > 0)
<table class="main">
  <thead>
    <tr>
      <th>#</th>
      <th>Receipt No.</th>
      <th>Patient Name</th>
      <th>Department</th>
      <th>Dispensed By</th>
      <th class="r">Amount (NGN)</th>
      <th class="r">Profit (NGN)</th>
      <th>Date &amp; Time</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sales as $i => $s)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td class="mono">{{ $s->receipt_number }}</td>
      <td>{{ $s->patient_name }}</td>
      <td><span class="badge">{{ $s->department->name }}</span></td>
      <td>{{ $s->soldBy->name }}</td>
      <td class="r"><strong>{{ number_format($s->total_amount,2) }}</strong></td>
      <td class="r" style="color:#16a34a">{{ number_format($s->total_profit,2) }}</td>
      <td>{{ $s->created_at->format('d M Y H:i') }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5" class="r">TOTALS</td>
      <td class="r">NGN{{ number_format($summary['total_amount'],2) }}</td>
      <td class="r">NGN{{ number_format($summary['total_profit'],2) }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>
@else
  <div class="nodata">No sales transactions found for this period.</div>
@endif

<div class="footer">
  <div class="l">MediStock POS &mdash; Confidential</div>
  <div class="r">{{ $sales->count() }} records &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i:s') }}</div>
</div>

</body>
</html>