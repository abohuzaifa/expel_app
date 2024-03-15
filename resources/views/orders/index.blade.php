@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid" style="width:100%">
        @if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif
                                <table class="table table-bordered ">
                                  <thead>
                                    <tr>
                                      <th>#</th>
                                      <th>{{trans('lang.order_number')}}</th>
                                      <th>{{trans('lang.customers')}}</th>
                                      <th>{{trans('lang.sellers')}}</th>
                                      <th>{{trans('lang.total_sale')}}</th>
                                      <th>{{trans('lang.action')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      @php
                                        //echo "<pre>";print_r($perPage); exit;
                                        $page = $_GET['page'] ?? 1;
                                        $i = ($page*$perPage)-$perPage;
                                      @endphp
                                    <?php
                                            foreach($orders as $key => $row) {
                                              echo '
                                              <tr>
                                                <th scope="row">'.($key+1).'</th>
                                                <td>'.$row->id.'</td>
                                                <td>'.$row->user->name.'</td>
                                                <td>'.$row->seller->name.'</td>
                                                <td class="fw-bold">'.$row->total.'</td>
                                                <td> <a class="btn btn-info" href="'.route('orders.show',$row->id).'">'.trans('lang.view').'</a>
                                                '. Form::open(['method' => 'DELETE','route' => ['orders.destroy', $row->id],'style'=>'display:inline']) .'
                                                        '. Form::submit(trans('lang.delete'), ['class' => 'btn btn-danger']) .'
                                                    '. Form::close() .'</td>
                                              </tr>';
                                         }
                                     ?>
                                  </tbody>
                                </table>
                                {{ $orders->onEachSide(1)->links('vendor.pagination.default') }}
                              </div>
                              </div>
            </div>
        </div>

@endsection