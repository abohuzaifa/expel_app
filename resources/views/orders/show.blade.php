@extends('layouts.app')


@section('content')
<!DOCTYPE html>
<html lang="en">

<!-- <head> -->
    <!-- <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <!-- <title>Order Detail</title> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        main {
            max-width: 100%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        section {
            /* margin-bottom: 20px; */
            margin:30px;
        }

        h2 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }
    </style>
<!-- </head> -->

<!-- <body> -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <header>
                <h1>{{trans('lang.order_detail')}}</h1>
            </header>

            <main>
                <section>
                    <h2>{{trans('lang.order_summary')}}</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{trans('lang.order_no')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->id}}</p>
                            <p><strong>{{trans('lang.date_time')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{formatDateTimeToEnglish($order->created_at)}}</p>
                            <p><strong>{{trans('lang.pickup_date_time')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->pickup_date_time != null ? formatDateTimeToEnglish($order->pickup_date_time) : trans('lang.nill')}}</p>
                            <p><strong>{{trans('lang.status')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ($order->order_status == 0 ? '<span class="badge bg-warning">'.trans('lang.pending').'</span>' : ($order->order_status == 1 ? '<span class="badge bg-success">'.trans('lang.complete').'</span>' : ($order->order_status == 2 ? '<span class="badge bg-primary">'.trans('lang.processing').'</span>' : '<span class="badge bg-danger">'.trans('lang.cancelled').'</span>'))) ?></p>
                            <p><strong>{{trans('lang.payment_method')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->payment->name}}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{trans('lang.tax')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->tax}}</p>
                            <p><strong>{{trans('lang.discount')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->discount}}</p>
                            <p><strong>{{trans('lang.due')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->due}}</p>
                            <p><strong>{{trans('lang.paid')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->paid}}</p>
                            <p><strong>{{trans('lang.total')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$order->total}}</p>
                        </div>
                    </div>
                    
                </section>

                <section>
                    <h2>{{trans('lang.buyer_information')}}</h2>
                    <p><strong>{{trans('lang.id')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->user->id}}</p>
                    <p><strong>{{trans('lang.name')}}:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->user->name}}</p>
                    <p><strong>{{trans('lang.address')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->user->street_address}}</p>
                    <p><strong>{{trans('lang.phone')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->user->mobile}}</p>
                    <p><strong>{{trans('lang.email')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->user->email}}</p>
                </section>

                <section>
                    <h2>{{trans('lang.seller_information')}}</h2>
                    <p><strong>{{trans('lang.id')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->seller->id}}</p>
                    <p><strong>{{trans('lang.name')}}:</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->seller->name}}</p>
                    <p><strong>{{trans('lang.address')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->seller->street_address}}</p>
                    <p><strong>{{trans('lang.phone')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->seller->mobile}}</p>
                    <p><strong>{{trans('lang.email')}}:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$order->seller->email}}</p>
                </section>

                <section>
                    <h2>{{trans('lang.order_items')}}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{trans('lang.product')}}</th>
                                <th>{{trans('lang.price')}}</th>
                                <th>{{trans('lang.quantity')}}</th>
                                <th>tax</th>
                                <th>{{trans('lang.discount')}}</th>
                                <th>{{trans('lang.net_total')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order->orderItems as $key => $row) {
                                echo '
                                <tr>
                                    <td>'.($key+1).'</td>
                                    <td>'.$row->product->p_name.'</td>
                                    <td>'.$row->product->price.'</td>
                                    <td>'.$row->item_quantity.'</td>
                                    <td>'.$row->item_tax.'</td>
                                    <td>'.$row->item_discount.'</td>
                                    <td>'.$row->item_total.'</td>
                                </tr>
                                ';
                            } ?>
                            
                        </tbody>
                    </table>
                </section>

                <!-- <button>Contact Customer Support</button> -->
            </main>
        </div>
    </section>
</div>
<!-- </body>

</html> -->

@endsection