@extends('admin.product_master')
@section('product')
    <div class="row">
        <!-- /.col-lg-12 -->
        @if(session('thongbao1'))
            <div class="alert alert-success">
                {{ session('thongbao1') }}
            </div>
        @endif
        @if(session('thongbao'))
            <div class="alert alert-success">
                {{ session('thongbao') }}
            </div>
        @endif
        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
            <thead>
                <tr align="center">
                    <th>{{ trans('admin.id') }}</th>
                    <th>{{ trans('admin.name') }}</th>
                    <th>{{ trans('admin.unit_price') }}</th>
                    <th>{{ trans('admin.total_quality') }}</th>
                    <th>{{ trans('admin.category') }}</th>
                    <th>{{ trans('admin.shop_product') }}</th>
                    <th>{{ trans('admin.image') }}</th>
                    <th>{{ trans('admin.delete') }}</th>
                    <th>{{ trans('admin.edit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($product as $sp)
                <tr class="odd gradeX" align="center">
                    <td>{{ $sp->product_id }}</td>
                    <td>{{ $sp->name }}</td>
                    <td>${{ number_format($sp->unit_price, 2) }}</td>
                    <td>{{ $sp->total_quanity }}</td>
                    <td>{{ ($sp->category) ? $sp->category->name : ''}} </td>
                    <td>{{ ($sp->shopProduct) ? $sp->shopProduct->shop_product_name : ''}}</td>
                    <td>
                        @if(isset($sp -> images[0]))
                            {{ Html::image(($sp->images[0]->hasImage()) ? '/assets/uploads/' . $sp->images[0]->link : $sp->images[0]->link, trans('title.this-is-image'), [
                            'class' => 'image_product',
                        ]) }}
                        @else
                            <img src="https://parts.ippin.com/resized_images/shops/43/28d4ee6c49a9c0785b2a15b059e17c10.png" alt="" class="img-responsive">
                        @endif
                    </td>
                    <td class="center"><a href="{{ action('AdminController@getDeleteProduct', $sp->product_id) }}" class="btn btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o  fa-fw"></i>{{ trans('admin.delete') }}</a></td>
                    <td class="center"><a href="{{ action('AdminController@getEditProduct', $sp->product_id) }}" class="btn btn-warning" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil fa-fw"></i>{{ trans('admin.edit') }}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.row -->
@endsection
