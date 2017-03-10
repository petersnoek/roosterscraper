@extends('_layout')

@section('content')

@if(isset($_SESSION['reload']) && $_SESSION['reload'])
    <h1>Reload!</h1>
@endif

{{--
<div class="tooltip">Hover over me
    <span class="tooltiptext">Tooltip text</span>
</div>


<h1>Gevonden lessen: {{ sizeof($lessenContainer->lessen) }}</h1>
<ul>
    @foreach ($lessenContainer->lessen as $les)
        <li>{{ $les->docent }} {{ $les->dag }} {{ $les->lescode }} {{ $les->starttijd }}-{{ $les->eindtijd }}
            {{ $les->GetKlassen() }}  {{ $les->GetLokalen() }}  {{ $les->datum }} </li>
    @endforeach
</ul>


<h1>Planbord</h1>
<p>Klik op de naam van de dag (ma, di, enz) om in- en uit te klappen.</p>
--}}

@foreach($dagen as $dag)
    <h2>{{$dag}}</h2>

<table class="roostertabel">
    <tr>
        <th class="nr">#</th>
        <th class="tijd">van</th>
        <th class="tijd">tot</th>
        @foreach($klassen as $k)
            <th>{!! $k[1] !!}</th>
        @endforeach
        <th class="separator"></th>
        @foreach($docenten as $docent=>$url)
            @if(is_array($url)) <th class="coldocent"><a href="{{ $url[0] }}" >{{ $docent }}</a></th>
            @else <th class="coldocent"><a href="{{ $url }}" >{{ $docent }}</a></th>
            @endif
        @endforeach
        <th class="separator"></th>
        @foreach($lessenContainer->allelokalen as $lok=>$short)
            <th>{{ $short }}</th>
        @endforeach
    </tr>
    @foreach ($tijden as $tijd)
    <tr {{ ($tijd[0]=='p' ? 'class=separator' :'' ) }} >
        <td class="nr">{{ $tijd[0] }}</td>
        <td class="tijd">{{ $tijd[1] }}</td>
        <td class="tijd">{{ $tijd[2] }}</td>
        @foreach($klassen as $k)
            @if(isset($k[2]))
                <td class="{{ $k[2] }}">{{ $k[2] }}</td>
            @else
            <td style="background-color: {{ $k[3] }}" title="{{ $lessenContainer->ZoekDocentEnLes($dag, $tijd[1], $k[0], true) }}" >{!! $lessenContainer->ZoekDocentEnLes($dag, $tijd[1], $k[0]) !!}</td>
            @endif
        @endforeach
        <td class="separator"></td>
        @foreach($docenten as $docent=>$url)
            <td rowspan="" title="{{ $lessenContainer->ZoekLesEnKlas($dag, $tijd[1], $docent, true) }}">{!! $lessenContainer->ZoekLesEnKlas($dag, $tijd[1], $docent) !!}</td>
        @endforeach
        <td class="separator"></td>
        @foreach($lessenContainer->allelokalen as $lok=>$short)
            <td title="{!! $lessenContainer->ZoekLokaalLes($dag, $tijd[1], $lok, true) !!}">{!! $lessenContainer->ZoekLokaalLes($dag, $tijd[1], $lok) !!}</td>
        @endforeach
    </tr>
    @endforeach
</table>
@endforeach

<script>
    $( document ).ready(function() {
        $( "h2" ).click(function( event ) {
            $( ".cel-tweede" ).toggle();
        });
    });
</script>

<table>
    <tr>
        <td>row1.cel1</td>
        <td>row1.cel2</td>
        <td>row1.cel3</td>
    </tr>

    <tr>
        <td>row2.cel1</td>
        <td rowspan="2">row2.cel2</td>
        <td>row2.cel3</td>
    </tr>

    <tr>
        <td>row3.cel1</td>

        <td>row3.cel3</td>
    </tr>
    <tr>
        <td>row4.cel1</td>
        <td>row4.cel2</td>
        <td>row4.cel3</td>
    </tr>

</table>



@endsection

