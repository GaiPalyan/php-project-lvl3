@extends('layouts.app')
@section('content')
<h1 class="mt-5 mb-3">Сайты</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-nowrap">
            <tbody>
                <tr class="thead-light">
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Последняя проверка</th>
                    <th>Код ответа</th>
                </tr>
                <tr>
                    @foreach($domains as $domain)
                    <td>
                        {{$domain->id}}
                    </td>
                    <td>
                        <a href="{{route('domain.show', $domain->id)}}">{{$domain->name}}</a>
                    </td>
                    <td>
                        {{data_get($lastChecks, $domain->id . '.last_check')}}
                        {{data_get($lastChecks, $domain->id . '.status_code')}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
