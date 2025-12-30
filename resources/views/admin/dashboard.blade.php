@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('css')
@endsection

@section('content')
    <div class="card widget">
        <div class="card-header">
            <h5 class="card-title">Activity Overview</h5>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <div class="display-5">
                            <i class="bi bi-truck text-secondary"></i>
                        </div>
                        <h5 class="my-3">Petrol Balance</h5>
                        <div class="text-muted">{{ number_format($petrolBalance) ?? 0 }}</div>
                        <div class="progress mt-3" style="height: 5px">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 25%"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <div class="display-5">
                            <i class="bi bi-receipt text-warning"></i>
                        </div>
                        <h5 class="my-3">Diesel Balance</h5>
                        <div class="text-muted">{{ number_format($dieselBalance) ?? 0 }}</div>
                        <div class="progress mt-3" style="height: 5px">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 67%"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <div class="display-5">
                            <i class="bi bi-bar-chart text-info"></i>
                        </div>
                        <h5 class="my-3">Reported</h5>
                        <div class="text-muted">50 Support New Cases</div>
                        <div class="progress mt-3" style="height: 5px">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0">
                    <div class="card-body text-center">
                        <div class="display-5">
                            <i class="bi bi-cursor text-success"></i>
                        </div>
                        <h5 class="my-3">Arrived</h5>
                        <div class="text-muted">34 Upgraded Boxed</div>
                        <div class="progress mt-3" style="height: 5px">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 55%"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ asset('admin/dist/js/popper.min.js') }}"  crossorigin="anonymous"></script>
<script src="{{ asset('admin/dist/js/bootstrap.min.js') }}" crossorigin="anonymous"></script>
@endsection
