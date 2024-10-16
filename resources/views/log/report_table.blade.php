<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <title>
        Report Table
    </title>
</head>

<body class="p-5">

    <table id="logReportTable" class="display table table-striped" style="width:100%">
        <thead>
            <tr>

                <th>Date</th>
                <th>Total OTP Sent</th>
                <th>Total Back From OTP Page</th>
                <th>OTP Match</th>
                <th>OTP Fail</th>
                <th>Payment Request</th>
                <th>Payment Success</th>
                <th>Insufficient credit</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($sendData as $item)
                <tr>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['total_otp_sent'] }}</td>
                    <td>{{ $item['total_back_from_otp_page'] }}</td>
                    <td>{{ $item['otp_match'] }}</td>
                    <td>{{ $item['otp_fail'] }}</td>
                    <td>{{ $item['payment_request'] }}</td>
                    <td>{{ $item['payment_success'] }}</td>
                    <td>{{ $item['insufficient_credit'] }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#logReportTable').DataTable({
                dom: 'Bfrtip',
                paging: false,
                searching: false,
                entires: false,
                bInfo : false,
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    className: 'btn btn-success',
                    filename: 'Bd gamers Daily (BDGD)',
                    title: 'Bd gamers Daily (BDGD)',
                }]
            });
        });
    </script>
</body>

</html>
