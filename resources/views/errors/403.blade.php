@extends('errors.layout')

@section('code', '403')
@section('title', 'دسترسی غیرمجاز')
@section('message'){{ preg_match('/\p{Arabic}/u', $exception->getMessage()) ? $exception->getMessage() : 'شما اجازه دسترسی به این صفحه را ندارید.' }}@endsection
