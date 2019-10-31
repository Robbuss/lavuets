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
            padding-left: 0px;
        }

        .title {
            font-size: 1.2em;
            font-weight: 800;
        }

        .subtitle {
            font-size: 0.9em;
            font-weight: 800;
            display: inline;
            margin-bottom: 10px;
        }

        .two-column {
            width: 50%;
        }

        .three-column {
            width: 33.3%;
        }

        .default-padding {
            padding: 15px;
        }

        .padding-x {
            padding-left: 15px;
            padding-right: 15px;
        }

        .padding-y {
            padding-top: 15px;
            padding-bottom: 15px;
        }

        .right {
            float: right
        }

        .left {
            float: left
        }

        .address-bar {
            height:255px;
            width: 100%;
        }

        .content-bar {
            min-height: 15px;
            width: 100%;
        }

        .title-bar {
            color: #fff;
            background-color: #456480;
            width: 100%;
        }

        .tenant-bar {
            color: #456480;
            margin-top: 10px;
            width: 100%;
            height: 150px;
        }

        .tenant-bar-header {
            height: 25px;
            border-bottom: 1px solid #456480;
        }

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
                <h4 class="title">Huurcontract opslagruimte Breukelen</h4>
                <ul>
                    <li>Opslag magazijn</li>
                    <li>Keulschevaart 15 B</li>
                    <li>3621 MX Breukelen</li>
                    <li>Tel: 0346 25 15 70</li>
                    <li>info@opslagmagazijn.nl</li>
                    <li>www.opslagmagazijn.nl</li>
                    <li>&nbsp;</li>
                    <li>Hierna te noemen “Verhuurder”,</li>
                    <li>Vertegenwoordigd door de heer: N. van Leusden </li>
                </ul>
            </div>
            <div class="two-column left">
                <img class="right" src="./logo.png" width="125"></img>
            </div>
        </div>
        <div class="content-bar">
            <p>Huurder en Verhuurder zijn op {{ $contract->created_at->isoFormat('LL') }} het volgende overeen gekomen: </p>
            <h3 class="subtitle">Artikel 1 Gehuurde, bestemming en gebruik:</h3>
            <p>
                Deze huurovereenkomst heeft betrekking op de huur van een opslagruimte per {{ \Carbon\Carbon::parse($contract->start_date)->isoFormat('LL') }} op de Keulschevaart 15 B te Breukelen, bekend als<br>
                UNIT NUMMER(s):
                @foreach($contract->units as $unit)
                {{ $unit->id }} : {{ $unit->name }},
                @endforeach
                <br>
                Het gehuurde mag uitsluitend worden gebruikt als ruimte voor opslag.<br>Er mogen geen werkplaats activiteiten plaats vinden.
                Het is huurder niet toegestaan zonder voorafgaande schriftelijke toestemming van verhuurder een andere bestemming aan het gehuurde te geven dan omschreven in 1.2 van de Algemene Voorwaarden.</br>
            </p>
            <h3 class="subtitle">Artikel 2. Betalingsverplichting en betaalperiode</h3>
            <ul>
                <li>De huurprijs voor unit nummer(s): {{ $units['units'] }}. bedraagt in totaal per kalendermaand: €{{ $units['price'] }} @if(!$tenant->kvk)(vrij van B.T.W.)@endif</li>
                <li>De huurprijs is door Huurder aan Verhuurder in één bedrag bij vooruitbetaling verschuldigd en dient voor de eerste dag van de desbetreffende kalendermaand volledig te zijn voldaan.</li>
                <li>De huurprijs kan jaarlijks worden aangepast in overeenstemming met de Algemene Voorwaarden.</li>
                <li>Verhuurder behoudt zich het recht voor om de toegangspas/dongel te deactiveren bij achterstallige betaling. Zodra de betaling is ontvangen zal de toegangspas/dongel binnen één werkdag weer geactiveerd worden.</li>
            </ul>
            <h3 class="subtitle">Artikel 3. Algemene voorwaarden</h3>
            <p>
                Op deze huurovereenkomst zijn van toepassing de Algemene Voorwaarden Opslag magazijn Breukelen, hierna te noemen, de “Algemene Voorwaarden”.
                Huurder verklaart de Algemene Voorwaarden te hebben ontvangen en deze te zullen naleven.
            </p>
            <ul>
                <li>Huurder {{ $tenant->name }}</li>
                <li>Adres: {{$tenant->street_addr}} {{ $tenant->street_number }}, {{ $tenant->postal_code }} te {{ $tenant->city }}</li>
                @if($tenant->phone)
                <li>Telefoonnummer: {{ $tenant->phone }}</li>
                @endif
                @if($tenant->iban)
                <li>Bankrekeningnummer: {{ $tenant->iban }}</li>
                @endif
                <li>Heeft digitaal ondertekend op {{ $contract->created_at->isoFormat('LL') }} via www.opslagmazijn.nl</li>
            </ul>
            <p>
            </p>
        </div>
    </div>

</body>

</html>