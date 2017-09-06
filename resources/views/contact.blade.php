@extends('layouts.master')
@section('text-center')
    <section class="site-section site-section-light site-section-top themed-background-dark">
        <div class="container text-center">
            <h1 class="animation-slideDown"><strong></strong></h1>
        </div>
    </section>
@endsection
@section('content')
    <section class="header1">
        <h1 id="h1 contact" >Contact us</h1>
    </section>
    <div id="form">
        <div class="fish" id="fish"></div>
        <div class="fish" id="fish2"></div>
        <form id="waterform" action="{!! url('contact') !!}" method="post">
            <input class="input" type="hidden" name="_token" value="{!! csrf_token() !!}" />
            <div class="formgroup" id="name-form">
                <label class="label" for="name">Your name*</label>
                @if(auth()->check())
                    <input class="input" type="text" id="name" name="name" value="{{ Auth::user()->name }}"/>
                @else
                    <input class="input" type="text" id="name" name="name"/>
                @endif
            </div>
            <div class="formgroup" id="email-form">
                <label class="label" for="email">Your e-mail*</label>
                @if(auth()->check())
                    <input class="input" type="email" id="email" name="email" value="{{ Auth::user()->email }}"/>
                @else
                <input class="input" type="email" id="email" name="email"/>
                @endif
            </div>
            <div class="formgroup" id="message-form">
                <label class="label" for="message">Your message</label>
                <textarea class="textarea" id="message" name="messsage_notification"></textarea>
            </div>
            <input class="input" type="submit" value="Send your message!" />
        </form>
    </div>
@endsection
