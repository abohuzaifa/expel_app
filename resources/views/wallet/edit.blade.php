@extends('layouts.app')


@section('content')
<div class="pagetitle">
    <h1>{{trans('lang.create_user')}}</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
        <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
        <li class="breadcrumb-item active">{{trans('lang.elements')}}</li>
      </ol>
    </nav>
  </div>
<section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"></h5>
                        <a class="btn btn-primary" href="{{ route('wallet.index') }}"> {{trans('lang.back')}}</a>
       


                        @if ($message = Session::get('error'))
                        <div class="alert alert-danger">
                        <p class="msg">{{ $message }}</p>
                        </div>
                        @endif
                        <div class="alert alert-danger d-none">
                        <p class="msg"></p>
                        </div>



{!! Form::model($wallet, ['enctype'=>'multipart/form-data','method' => 'PATCH','route' => ['wallet.update', $wallet->id]]) !!}
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-3" style="text-align: right;">
                <div class="form-group">
                    <input style="height: 15px; width:15px;" type="checkbox"  name="recharge" id="recharge" value="1" checked onchange="check_box1()">
                    <strong>{{trans('lang.recharge')}}</strong>
                </div>
            </div>

            <div class="col-3" style="text-align: right;">
                <div class="form-group">
                    <input style="height: 15px; width:15px;" type="checkbox"  name="withdraw" id="withdraw" value="1" onchange="check_box2()">
                    <strong>{{trans('lang.withdraw')}}</strong>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <strong>{{trans('lang.amount')}}:</strong>
                <input type="number" id="amount" name="amount" class="form-control" value="0" required onkeyup="calculate();">
            </div>
        </div>
        <div class="col-4 d-none">
            <div class="form-group">
                <label for="">{{trans('lang.wallet_amount')}}</label>
                <input type="text" id="wallet_amount" readonly class="form-control" value="{{$wallet->amount}}">
            </div>
        </div>
    </div>
    

    <div class="col-xs-12 col-sm-12 col-md-12 text-center"><br>
        <button type="submit" class="btn btn-primary submit">{{trans('lang.submit')}}</button>
    </div>
</div>
{!! Form::close() !!}


        </div>
    </div>
</div>
</div>
    </section>

@endsection
<script>
      function calculate()
      {
        var amount = $("#amount").val();
        var wallet_amount = $("#wallet_amount").val();
        if(amount > wallet_amount)
        {
            if ($("#withdraw").prop('checked')) {
                $('.msg').html('You cannot enter amount more then wallet amount.');
                $(".alert-danger").removeClass("d-none");
                $(".submit").attr("disabeld", "disabled");
                $("#amount").val(0);
            } else {
                $('.msg').html(``);
                $(".alert-danger").addClass("d-none");
                $(".submit").removeAttr("disabeld", "disabled");
            }
        } else {
            $('.msg').html(``);
            $(".alert-danger").addClass("d-none");
            $(".submit").removeAttr("disabeld", "disabled");
        }
        
      }
      function check_box1()
      {
        if ($("#recharge").prop('checked', true)) {
          $('#withdraw').not(this).prop('checked', false);
          $(".col-4").addClass('d-none');
          $("#amount").val(0);
        }
      }
      function check_box2()
      {
        if ($("#withdraw").prop('checked', true)) {
          $('#recharge').not(this).prop('checked', false);
          $(".col-4").removeClass('d-none');
          $("#amount").val(0);
        }
      }
     
    // });
  </script>