@extends('admin.order_master')
@section('order')
    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
        <thead>
            <tr align="center">
                <th>{{ trans('admin.date') }}</th>
                <th>{{ trans('admin.customer') }}</th>
                <th>{{ trans('admin.product') }}</th>
                <th>{{ trans('admin.total_price') }}</th>
                <th>{{ trans('admin.order_detail') }}</th>
                <th>{{ trans('admin.note') }}</th>
                <th>{{ trans('admin.status') }}</th>
                <th>{{ trans('admin.delete') }}</th>
            </tr>
        </thead>
        <tbody>

            @foreach($order as $order)
                @if($order->total_price != 0)
                <tr class="odd gradeX" align="center">
                    <td class="center">{{ $order->created_at }}</td>
                    <td class="center">{{ $order->user->name }}</td>
                    <td class="center">
                        @if(count($order->order_details) == 1)
                            {{ $order->order_details[0]->product->name}}
                        @endif
                        @if(count($order->order_details) > 1)
                            {{ $order->order_details[0]->product->name }} ... and {{ count($order->order_details) - 1 }} products other
                        @endif
                    </td>
                    <td class="center">{{ $order->total_price }}</td>
                    <td class="center"><a class="link_order btn btn-warning" data-toggle="tooltip" title="Order Detail" href="{{ route('orderdetail_list', $order->order_id) }}">Order Detail</a></td>
                    <td class="center">{{ $order->content }}</td>
                    @if($order->status == 1)
                        <td class="center">
                            <button class="btn btn-success" data-toggle="tooltip" title="Done">{{ trans('admin.done') }}</button>
                        </td>
                    @else
                        <td class="center">
                            <button class="btn btn-danger" data-toggle="tooltip" title="Doing"><a  class="doing" href="{{ action('AdminController@getEditOrder', $order->order_id) }}">{{ trans('admin.doing') }}</a></button>
                        </td>
                    @endif
                    <td class="center"><a href="{{ action('AdminController@getDeleteOrder', $order->order_id) }}" class="btn btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o  fa-fw"></i>{{ trans('admin.delete') }}</a></td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endsection
