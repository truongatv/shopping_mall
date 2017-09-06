@extends('admin.category_master')
@section('category')
    <div class="row">
        <form id="form2" action= "{{ route('add_cate') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="form-group">
                <label>{{ trans('admin.category_parent') }}</label>
                {!! Form::select('category_parent_id', $categoryParent, 0, [
                    'class' => 'form-control',
                ]) !!}
            </div>
            <div class="form-group">
                <label>{{ trans('admin.category_name') }}</label>
                <input class="form-control" name="name" placeholder="{{ trans('admin.please_enter_category_name') }}" />
            </div>
            <button type="submit" class="btn btn-success">{{ trans('admin.category_add') }}</button>
        </form>
        <hr/>
        <table class="table table-striped table-bordered table-hover push-pit" id="dataTables-example">
            <thead>
                <tr align="center">
                    <th style="text-align: center;">{{ trans('admin.id') }}</th>
                    <th style="text-align: center;">{{ trans('admin.name') }}</th>
                    <th style="text-align: center;">{{ trans('admin.category_child') }}</th>
                    <th style="text-align: center;">{{ trans('admin.edit') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category as $category)
                    @if($category->category_parent_id == NULL)
                        <tr class="odd gradeX" align="center">
                            <td>{{$category->category_id}}</td>
                            <td style="text-align: left;">{{ $category->name }}</td>
                            <td><a class="link_order btn btn-success" data-toggle="tooltip" title="List child categories" href="{{ route('cate_list_child', $category->category_id) }}"><i class="fa fa-eye" ></i></a></td>
                            <td class="center"><a href="{{ action('AdminController@getEdit', $category->category_id) }}" class="btn btn-warning" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil fa-fw"></i></a>
                            <a href="{{ action('AdminController@getDelete', $category->category_id) }}" class="btn btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o  fa-fw"></i></a></td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.row -->
@endsection
