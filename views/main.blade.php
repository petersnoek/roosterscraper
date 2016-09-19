@extends('_layout')

@section('content')
<h1>Gevonden roosters:</h1>

    <ul>
    @foreach ($docenten as $d)
        <li><a href="{{$d['roosterurl']}}">{{$d['docent']}}</a></li>
    @endforeach
    </ul>

<h1>Gevonden lessen:</h1>
<ul>
    @foreach ($lessenContainer->lessen as $les)
        <li>{{ $les->docent }} {{ $les->dag }} {{ $les->lescode }} {{ $les->starttijd }}-{{ $les->eindtijd }}
            {{ $les->GetKlassen() }}  {{ $les->GetLokalen() }} </li>
    @endforeach
</ul>

<h1>Planbord</h1>

@foreach($dagen as $dag)
<h2>{{$dag}}</h2>
<table class="roostertabel">
    <tr>
        <th class="nr">#</th>
        <th class="tijd">van</th>
        <th class="tijd">tot</th>
        @foreach($klassen as $k)
        <th>{{ $k[1] }}</th>
        @endforeach
        <th class="separator"></th>
        @foreach($docenten as $d)
            <th>{{ $d['docent'] }}</th>
        @endforeach
        <th class="separator"></th>
        @foreach($lessenContainer->allelokalen as $lok=>$short)
            <th>{{ $short }}</th>
        @endforeach
    </tr>
    @foreach ($tijden as $tijd)
    <tr>
        <td class="nr">{{ $tijd[0] }}</td>
        <td class="tijd">{{ $tijd[1] }}</td>
        <td class="tijd">{{ $tijd[2] }}</td>
        @foreach($klassen as $k)
            @if(isset($k[2]))
                <td class="{{ $k[2] }}">{{ $k[2] }}</td>
            @else
            <td>{{ $lessenContainer->ZoekDocent($dag, $tijd[1], $k[0])  }}</td>
            @endif
        @endforeach
        <td class="separator"></td>
        @foreach($docenten as $d)
            <td>{{ $lessenContainer->ZoekLes($dag, $tijd[1], $d['docent']) }}</td>
        @endforeach
        <td class="separator"></td>
        @foreach($lessenContainer->allelokalen as $lok=>$short)
            <td>{{ $lessenContainer->ZoekLokaalLes($dag, $tijd[1], $lok) }}</td>
        @endforeach
    </tr>
    @endforeach
</table>
@endforeach

@endsection

