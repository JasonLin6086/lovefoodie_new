@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => $welcome,
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p align='center'>Thanks for signing up!</p>

        <p align='center'>Please click the button below to confirm your email.</p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
        	'title' => 'Click me to confirm',
        	'link' => $confirm_url
    ])

@stop
