<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Réservation Officielle</title>

<style>
body{
    font-family: DejaVu Sans, sans-serif;
    margin:40px;
    position:relative;
    color:#222;
}

/* IMAGE BACKGROUND */
.bg-image{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    opacity:0.10; زيدناها
    z-index:-2;
}

.logo-watermark{
    position:fixed;
    top:20%;
    left:60%;
    border-radius:25px ;
    width:100px;
    height:100px;
    
    /* opacity:0.1; باينة ولكن ما تغطيش */
    
    z-index:-1;
}

.header{
    text-align:center;
    border-bottom:3px solid #1e3c72;
    padding-bottom:10px;
    margin-bottom:30px;
}

.header h1{
    font-size:30px;
    color:#1e3c72;
    margin:0;
}

.reservation-id{
    font-size:14px;
    color:#777;
}

.details{
    border:1px solid #ddd;
    border-radius:15px;
    padding:25px;
    background:#fdfdfd;
}

.details p{
    margin:10px 0;
    font-size:16px;
}

table{
    width:100%;
    margin-top:15px;
    border-collapse:collapse;
}

table th{
    background:#1e3c72;
    color:#fff;
    padding:10px;
    text-align:left;
}

table td{
    padding:10px;
    border:1px solid #ddd;
}

.footer{
    margin-top:60px;
    font-size:13px;
    text-align:center;
    color:#666;
    border-top:1px solid #ccc;
    padding-top:10px;
}

.security{
    margin-top:20px;
    font-size:12px;
    text-align:center;
    color:#999;
}
</style>
</head>

<body>

<!-- Background Image -->
<img src="{{ public_path('images/t1.jpeg') }}" class="bg-image">

<!-- Logo Watermark -->

<div class="header">
    <h1>CONFIRMATION OFFICIELLE DE RÉSERVATION</h1>
    <div class="reservation-id">
        Référence: #RES-{{ $reservation->id }}-{{ date('Y') }}
    </div>
</div>

<div class="details">
<img src="{{ public_path('images/m2.jpeg') }}" class="logo-watermark">

<p><strong>Nom du Client:</strong> {{ $reservation->user->name }}</p>
<p><strong>Terrain:</strong>  Tawrirt</p>
<p><strong>Adresse:</strong> Mellab</p>

<p><strong>Date:</strong> 
{{ \Carbon\Carbon::parse($reservation->timeSlot->date)->format('d/m/Y') }}
</p>

<p><strong>Heure:</strong> 
{{ \Carbon\Carbon::parse($reservation->timeSlot->start_time)->format('H:i') }}
 -
{{ \Carbon\Carbon::parse($reservation->timeSlot->end_time)->format('H:i') }}
</p>

<p><strong>Montant Payé:</strong> {{ $reservation->total_price }} MAD</p>

<p>
<strong>Statut:</strong> 

@if($reservation->status == 'approved')
    <span style="color:#16a34a; font-weight:bold; font-size:18px;">
        ✅ VALIDÉ
    </span>
@else
    <span style="color:#dc2626; font-weight:bold; font-size:18px;">
        ❌ NON VALIDÉ
    </span>
@endif

</p>
<table>
<tr>
<th>Date de Création</th>
<td>{{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y H:i') }}</td>
</tr>

<tr>
<th>Code Sécurité</th>
<td>{{ md5($reservation->id . $reservation->user->name) }}</td>
</tr>
</table>

</div>

<div class="footer">
Terrain Tawrirt - Document Officiel <br>
Ce document est généré automatiquement et protégé contre la falsification.
</div>

<div class="security">
Toute modification de ce document invalide sa validité légale.
</div>

</body>
</html>