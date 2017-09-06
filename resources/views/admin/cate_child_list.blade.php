@extends('admin.category_master')
@section('category')
<div class="row">
    <!-- /.col-lg-12 -->
    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
        <thead>
            <tr align="center">
                <th style="text-align: center;">{{ trans('admin.id') }}</th>
                <th style="text-align: center;">{{ trans('admin.name') }}</th>
                <th style="text-align: center;">{{ trans('admin.edit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($category as $cate)
            <tr class="odd gradeX" align="center">
                <td>{{ $cate->category_id }}</td>
                <td style="text-align: left;">{{ $cate->name }}</td>
                <td class="center">
                <a href="{{ action('AdminController@getEdit', $cate->category_id) }}" class="btn btn-warning" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil fa-fw"></i></a>
                <a href="{{ action('AdminController@getDelete', $cate->category_id) }}" class="btn btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o  fa-fw"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- /.row -->

@endsection


