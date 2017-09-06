@extends('admin.user_master')
@section('user')
    <!-- /.col-lg-12 -->
    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
        <thead>
            <tr align="center">
                <th>{{ trans('admin.name') }}</th>
                <th>{{ trans('admin.email') }}</th>
                <th>{{ trans('admin.phone') }}</th>
                <th>{{ trans('admin.customer') }}</th>
                @if(Auth::user()->role == 1)
                    <th>{{ trans('admin.setup') }}</th>
                @endif
                <th>{{ trans('admin.delete') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user as $user)
            <tr class="odd gradeX" align="center">
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                @if($user->role == 1 || $user->role == 2)
                    <td class="center">
                        <button class="btn btn-warning" data-toggle="tooltip" title="Admin">{{ trans('admin.admin') }}</button>
                    </td>
                @else
                    <td class="center">
                        {{ trans('admin.user') }}
                    </td>
                @endif
                @if(Auth::user()->role == 1)
                    <td><button class="btn btn-success" data-toggle="tooltip" title="Setup Admin"><a  class="doing" href="{{ action('AdminController@getSetupUser', $user->id) }}">Setup<i class="fa fa-pencil fa-fw"></i></a></button></td>
                @endif
                <td class="center"><a href="{{ action('AdminController@getDeleteUser', $user->id) }}" class="btn btn-danger" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o  fa-fw"></i>{{ trans('admin.delete') }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- /.row -->
@endsection
