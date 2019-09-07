<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            color: #8b8b8b;
            font-family: 'Open Sans', 'HelveticaNeue', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        ul {
            list-style: none;
            padding-left: 15px;
        }

        .two-column {
            width: 50%;
        }

        .default-padding {
            padding: 15px;
        }

        .right {
            float: right
        }

        .left {
            float: left
        }

        .address-bar {
            height: 250px;
            width: 100%;
        }

        .title-bar {
            color: #fff;
            background-color: #456480;
            width: 100%;
        }

        .customer-bar {
            color: #456480;
            margin-top: 10px;
            width: 100%;
            height: 150px;
        }

        .customer-bar-header {
            height: 25px;
            border-bottom: 1px solid #456480;
        }

        .customer-address {}

        .customer-instruction {}

        .item-bar-header {
            height: 18px;
            color: #fff;
            background-color: #456480;
            width: 100%;
        }

        .item-bar {
            height: 18px;
            width: 100%;
            border-bottom: 1px solid #999;
        }

        .col1,
        .col2,
        .col3,
        .col4 {
            float: left;
        }

        .col1 {
            width: 15%
        }

        .col2 {
            width: 40%;
        }

        .col3,
        .col4 {
            width: 20%;
        }

        .footer {
            text-align: center;
            height: 0px;
            position: absolute;
            bottom: 15px;
            width: 100%;
            color: #456480;
        }

        .footer .top {
            padding-bottom: 5px;
        }

        .footer .bottom {
            border-top: 1px solid #456480;
        }
    </style>
</head>


<body>
    <div class="container">
        <div class="address-bar">
            <div class="two-column left">
                <ul>
                    <li>Opslag magazijn</li>
                    <li>Keulschevaart 15 B</li>
                    <li>3621 MX Breukelen</li>
                    <li>&nbsp;</li>
                    <li>Tel: 0346 25 15 70</li>
                    <li>&nbsp; </li>
                    <li>KVK: 30230366</li>
                    <li>BTW: NL 818490780B01</li>
                    <li>&nbsp; </li>
                    <li>info@opslagmagazijn.nl</li>
                    <li>www.opslagmagazijn.nl</li>
                </ul>
            </div>
            <div class="two-column right">
                <img class="right" src="./logo.png" width="175"></img>
            </div>
        </div>
        <div class="title-bar default-padding">
            {{ $invoice->ref }} <span class="right">Periode {{ \Carbon\Carbon::parse($invoice->start_date)->isoFormat('LL') }} tot {{  \Carbon\Carbon::parse($invoice->end_date)->isoFormat('LL') }}</span>
            {{ $invoice->customer->name }}
        </div>
        <div class="customer-bar">
            <div class="customer-bar-header default-padding">
                <div class="left two-column">
                    Factuuradres
                </div>
                <div class="right two-column">
                    Instructies
                </div>
            </div>
            <div class="left two-column">
                <ul>
                    <li>{{ $invoice->customer->name }}</li>
                    <li>{{ $invoice->customer->street_addr }} {{ $invoice->customer->street_number }}</li>
                    <li>{{ $invoice->customer->postal_code }} {{ $invoice->customer->city }}</li>
                </ul>
            </div>
            <div class="right two-column default-padding">
                {!! $invoice->note !!}
            </div>
        </div>
        <div class="item-bar-header default-padding">
            <div class="col1">Aantal</div>
            <div class="col2">Beschrijving</div>
            <div class="col3">Prijs per eenheid</div>
            <div class="col4">Totaal</div>
        </div>
        @foreach($invoice->contract->units as $unit)
        <div class="item-bar default-padding">
            <div class="col1">1</div>
            <div class="col2">{{ $unit->name }} ({{ $unit->size }}m3)</div>
            <div class="col3">€{{ $unit->pivot->price }}</div>
            <div class="col4">€{{ $unit->pivot->price }}</div>
        </div>
        @endforeach
        <div class="item-bar default-padding">
            <div class="col1" style="border-bottom: 0px !important;"></div>
            <div class="col2" style="border-bottom: 0px !important;"></div>
            @if($invoice->customer->kvk)
                <div class="col3">21% BTW</div>
            @else
                <div class="col3">Vrij van BTW</div>
            @endif
            <div class="col4"></div>
        </div>
        <div class="item-bar default-padding">
            <div class="col1"></div>
            <div class="col2"></div>
            <div class="col3">Totaal</div>
            @if($invoice->customer->kvk)
            <div class="col4">€{{ $total['price_vat'] }}</div>
            @else
            <div class="col4">€{{ $total['price'] }}</div>
            @endif
        </div>
        <div class="footer">

            <div class="bottom">
                Rekeningnummer Rabobank: NL35 RABO 0168 4940 51 t.n.v. Opslag magazijn
            </div>
        </div>
    </div>

</body>

</html>