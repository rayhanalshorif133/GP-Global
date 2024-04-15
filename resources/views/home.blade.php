@extends('layouts.app', ['title' => 'Dashboard'])
@section('breadcrumb')
    <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
@endsection


@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @foreach ($dashboardLogs as $item)
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box {{ $item['color'] }}">
                            <div class="inner">
                                <h3>{{ $item['count'] }}</h3>
                                <p> {{ $item['title'] }} </p>
                            </div>
                            <div class="icon">
                                <i class="ion {{ $item['icon'] }}"></i>
                            </div>
                            <a href="{{$item['route']}}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
@endsection
