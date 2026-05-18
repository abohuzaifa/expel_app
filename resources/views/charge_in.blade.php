
    
    @extends('layouts.app')
    

    @section('content')
    @php
        $isPaid = isset($status) && (int) $status === 1;
        $message = $message ?? ($isPaid ? trans('lang.success') : trans('lang.failed'));
    @endphp
    <style>
          body {
            text-align: center;
            padding: 40px 0;
            background: #EBF0F5;
          }
          .charge-wrap {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
          }
            h1 {
              color: #1f2937;
              font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
              font-weight: 900;
              font-size: 40px;
              margin-bottom: 10px;
            }
            p {
              color: #404F5E;
              font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
              font-size:20px;
              margin: 0;
            }
          .status-icon {
            color: #ffffff;
            font-size: 96px;
            line-height: 200px;
            margin-left:-15px;
          }
          .card {
            background: white;
            padding: 48px 56px;
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
            display: inline-block;
            margin: 0 auto;
            min-width: 320px;
          }
          .status-circle {
            border-radius: 200px;
            height: 200px;
            width: 200px;
            margin: 0 auto;
          }
          .status-circle.success {
            background: #16a34a;
          }
          .status-circle.failed {
            background: #dc2626;
          }
          .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 700;
            margin-top: 18px;
          }
          .status-badge.success {
            background: #dcfce7;
            color: #166534;
          }
          .status-badge.failed {
            background: #fee2e2;
            color: #991b1b;
          }
        </style>
          <div class="charge-wrap">
            <div class="card">
              <div class="status-circle {{ $isPaid ? 'success' : 'failed' }}">
                <i class="status-icon">{{ $isPaid ? '✓' : '✕' }}</i>
              </div>
              <h1>{{ $isPaid ? trans('lang.success') : trans('lang.failed') }}</h1>
              <p>{{ $message }}</p>
              <div class="status-badge {{ $isPaid ? 'success' : 'failed' }}">
                {{ $isPaid ? 'Payment made' : 'Payment not made' }}
              </div>
            </div>
          </div>
     
          @endsection