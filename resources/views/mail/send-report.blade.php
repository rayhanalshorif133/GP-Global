<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ $data['title'] }}
    </title>
    <style>
        /* Reset some default styles */
        body,
        p,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #111111 !important;
        }

        h2 {
            font-size: 1.2rem;
            color: #111111 !important;
        }

        /* Styles for the email container */
        .email-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #111111 !important;
        }

        /* Styles for the header */
        .header {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            color: #111111 !important;
        }

        /* Styles for the main content */
        .content {
            padding: 20px 0;
            color: #111111 !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            color: #111111 !important;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Styles for the footer */
        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
            color: #111111 !important;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $data['title'] }}</h1>
        </div>
        <div class="content">
            <h2>Subscribers and Unsubscribers - Date: {{ $data['date'] }},</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Keyword</th>
                        <th>Subscribers</th>
                        <th>Unsubscribers</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['subsAndUnsubsData']['reports'] as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td class="fw-bolder">{{ $item['keyword'] }}</td>
                            <td>{{ $item['subscount'] }}</td>
                            <td>{{ $item['unsubscount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: center!important;">Total</td>
                        <td>{{ $data['subsAndUnsubsData']['totalSubs'] }}</td>
                        <td>{{ $data['subsAndUnsubsData']['totalUnsubs'] }}</td>
                    </tr>
                </tfoot>
            </table>
            <h2 style="margin-top:2rem;">Charge Log - Date: {{ $data['date'] }},</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Keyword</th>
                        <th>Subscribers</th>
                        <th>Renews</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['chargeLogsData']['reports'] as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item['keyword'] }}</td>
                            <td>{{ $item['subscount'] }}</td>
                            <td>{{ $item['renewcount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: center!important;">Total</td>
                        <td>{{ $data['chargeLogsData']['totalSubs'] }}</td>
                        <td>{{ $data['chargeLogsData']['totalRenews'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="footer">
            <p>Thank you for reading our email. For more information, visit our <a
                    href="http://gpglobal.b2mwap.com">website</a>.</p>
        </div>
    </div>
</body>

</html>
