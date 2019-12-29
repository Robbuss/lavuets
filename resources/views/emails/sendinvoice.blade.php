@extends('emails.layout')

@section('title')
    Titel van booking compleet mail
@endsection

@section('body')
    <p>
    Beste klant,
    </p>

    <p>
    In de bijlage zit uw nieuwe factuur.
    </p>

    <p>
    @if($payment_method === 'incasso')
    Het totaalbedrag wordt binnen enkele dagen automatisch van het bij ons bekende rekening nummer afgeschreven.
    @else
    Wij verzoeken u vriendelijk om het totaalbedrag binnen 8 dagen aan ons over te maken.
    @endif
    </p>
@endsection

@section('footer')
    Met vriendelijke groet,<br><br>
    <strong>Vincent van Montfoort</strong>
@endsection

