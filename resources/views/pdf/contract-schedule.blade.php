<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Shartnoma grafigi</title>
    <style>

        body {
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .contract-info {
            margin-bottom: 20px;
        }
        .tables-container {
            width: 100%;
            /* gap: 20px;  */
            page-break-inside: avoid;
        }
        .table-wrapper {
            width: 40%;
            margin: 1%;
            page-break-inside: avoid;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            /* table-layout: fixed; */
        }
        tbody {
            page-break-inside: avoid;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 10px; /* Matn hajmini kichikroq qilamiz */
        }
        th {
            background-color: #f4f4f4;
            white-space: nowrap;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        @page {
            size: 280mm 280mm; /* 900x600px ga yaqin */
            margin: 10mm;
        }
    </style>
</head>
<body style="font-family: 'DejaVu Sans', sans-serif;transform: scale(0.9); transform-origin: top left;">
    <div class="tables-container" style="display: flex">
        <div class="table-wrapper" style="float: left">
            <div class="header">
                <p style="font-size:8px"><strong>Шартнома № {{ $contract->id }}</strong></p>
                <p style="font-size:8px"><strong>Шартнома санаси: {{ $contract->created_at->format('d.m.Y') }}</strong></p>
                <h2 style="font-size:14px">{{$contract->client->first_name}} {{$contract->client->last_name}} {{$contract->client->patronymic}}</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Тўлов санаси</th>
                        <th>Жами сумма</th>
                        <th>Умумий қарздорлик</th>
                        <th>Умумий тўланган сумма</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->payments as $key=> $payment)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $payment->due_date }}</td>
                            <td>{{ number_format($payment->total_amount,'2','.',' ') }}</td>
                            <td>{{ number_format($payment->total_amount,'2','.',' ') }}</td>
                            <td>0</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="contract-info">
                <p style="font-size:8px"><strong>{{$contract->branch->name}}</strong></p>
                <p style="font-size:8px"><strong>Мурожат учун телефон:</strong> +99890 575 12 34</p>
                <p style="font-size:8px;float: right"><strong>Сана:</strong> {{ \Carbon\Carbon::now()->format('d.m.Y') }}</p>
            </div>

            <div class="footer">
                <div style="font-size: 10px"><strong>Танишдим:</strong> ________________</div>
            </div>
        </div>
        <div class="table-wrapper" style="float: right">
            <div class="header">
                <p style="font-size:8px"><strong>Шартнома № {{ $contract->id }}</strong></p>
                <p style="font-size:8px;"><strong>Шартнома санаси: {{ $contract->created_at->format('d.m.Y') }}</strong></p>
                <h2 style="font-size:14px">{{$contract->client->first_name}} {{$contract->client->last_name}} {{$contract->client->patronymic}}</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Тўлов санаси</th>
                        <th>Жами сумма</th>
                        <th>Умумий қарздорлик</th>
                        <th>Умумий тўланган сумма</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->payments as $key=> $payment)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $payment->due_date }}</td>
                            <td>{{ number_format($payment->total_amount,'2','.',' ') }}</td>
                            <td>{{ number_format($payment->total_amount,'2','.',' ') }}</td>
                            <td>0</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="contract-info">
                <p style="font-size:8px"><strong>{{$contract->branch->name}}</strong></p>
                <p style="font-size:8px"><strong>Мурожат учун телефон:</strong> +99890 575 12 34</p>
                <p style="font-size:8px;float: right"><strong>Сана:</strong> {{ \Carbon\Carbon::now()->format('d.m.Y') }}</p>
            </div>

            <div class="footer">
                <div style="font-size: 10px"><strong>Танишдим:</strong> ________________</div>
            </div>
        </div>
    </div>
</body>
</html>