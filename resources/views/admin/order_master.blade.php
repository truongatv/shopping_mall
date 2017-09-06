@extends('layouts.master')
@section('text-center')
    <section class="site-section site-section-light site-section-top themed-background-dark">
        <div class="container text-center">
            <h1 class="animation-slideDown"><strong>{{ trans('admin.explore_over') }}</strong></h1>
        </div>
    </section>
@endsection
@section('content')
    <section class="site-content site-section site-section2">
        <div id="wrapper">
            <!-- Navigation -->
            <div class="noti"><span class="notifi">{{ trans('admin.notification') }}</span> : <a href="#">{{ trans('admin.thongbao') }}</a></div>
            <nav class="navbar navbar-default navbar-static-top navbar2" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">{{ trans('admin.toggle_navigation') }}</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- <a class="navbar-brand" href="index.html">{{ trans('admin.admin_area') }} - <span class="admin_name">{{ Auth::user()->name }}</span></a> -->
                </div>
                <body class="news">

                <!-- /.navbar-header -->
                <!-- /.navbar-top-links -->
                <div class="navbar-default sidebar_admin" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                                <!-- /input-group -->
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-dashboard fa-fw"></i>{{ trans('admin.dashboard') }}</a>
                            </li>
                            <li>
                                <a href="{{ action('AdminController@getList') }}"><i class="fa fa-bar-chart-o fa-fw"></i>{{ trans('admin.category') }}<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="{{ action('AdminController@getList') }}">{{ trans('admin.list_category') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ action('AdminController@getAdd') }}">{{ trans('admin.add_category') }}</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="{{ action('AdminController@getProductList') }}"><i class="fa fa-cube fa-fw"></i>{{ trans('admin.product') }}<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="{{ action('AdminController@getProductList') }}">{{ trans('admin.list_product') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ action('AdminController@getAddProduct') }}">{{ trans('admin.add_product') }}</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="{{ action('AdminController@getUserList') }}"><i class="fa fa-users fa-fw"></i>{{ trans('admin.user') }}<span class="fa arrow"></span></a>
                            </li>
                            <li>
                                <a href="{{ action('AdminController@getOrderList') }}"><i class="fa fa-users fa-fw"></i>{{ trans('admin.order') }}<span class="fa arrow"></span></a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header header2">{{ trans('admin.order') }}
                                <small>{{ trans('admin.list') }}</small>
                            </h1>
                        </div>
                        <div class="col-lg-12">
                            <div class="nav1">
                              <ul>
                                <li class="home"><a href="{{ action('AdminController@getOrderList') }}">All Order</a></li>
                                <li class="contact"><a href="{{ action('AdminController@getOrderListToday') }}">Order Today</a></li>
                                <li class="about"><a href="{{ action('AdminController@getOrderListDoing') }}">Order Doing</a></li>
                                <li class="contact"><a href="{{ action('AdminController@getOrderListDone') }}">Order Done</a></li>
                              </ul>
                            </div>
                        </div>
                        <!-- /.col-lg-12 -->
                        @if(session('thongbao1'))
                            <div class="alert alert-success">
                                {{ session('thongbao1') }}
                            </div>
                        @endif
                        @yield('order')
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->
        </div>
    </section>
@endsection
