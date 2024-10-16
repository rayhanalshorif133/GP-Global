<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $data['title'] }}
    </title>
</head>

<body>


    <div class="container">
        <div class="row">
            <div class="col-md-12 py-2">
                <h4 style="font-size:1.5rem; margin-left: auto!important; margin-right: auto!important; padding: 0.5rem 0!important; text-decoration: underline!important; text-align: center!important; ">
                    {{ $data['title'] }}</h4>
                <h5 style="margin-top: 0; margin-bottom: 0.5rem; font-weight: 600; line-height: 1.2;  font-size:1.2rem;">
                    Subscribers and Unsubscribers - Date: {{ $data['date'] }}
                </h5>
                <table style="width: 100%; margin-top: 1rem; border: 1px solid #454545; padding:1rem;">
                    <thead style="border: 1px solid #454545;">
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
                <table>
                    <h5>Charge Log - Date: {{ $data['date'] }}</h5>
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
                                <td class="fw-bolder">{{ $item['keyword'] }}</td>
                                <td>{{ $item['subscount'] }}</td>
                                <td>{{ $item['renewcount'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total</td>
                            <td>{{ $data['chargeLogsData']['totalSubs'] }}</td>
                            <td>{{ $data['chargeLogsData']['totalRenews'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    -->
</body>

</html>
