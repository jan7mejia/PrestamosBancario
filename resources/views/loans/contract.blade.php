<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato - {{ $loan->client->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12pt; line-height: 1.5; color: black; background: white; }
            @page { margin: 2.5cm; }
        }
        .contract-content { max-width: 800px; margin: 0 auto; background: white; padding: 3rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        h1 { font-size: 1.25rem; font-weight: bold; text-align: center; text-transform: uppercase; margin-bottom: 2rem; }
        p { margin-bottom: 1rem; text-align: justify; }
        .firmas { margin-top: 5rem; display: flex; justify-content: space-around; }
        .firma-box { text-align: center; border-top: 1px solid black; width: 40%; padding-top: 0.5rem; }
    </style>
</head>
<body class="bg-gray-100 py-10">

    <div class="max-w-4xl mx-auto mb-6 flex justify-end no-print">
        <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 px-6 rounded-lg flex items-center shadow-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimir Contrato
        </button>
    </div>

    <div class="contract-content">
        @if($loan->contract_type == 'garantia_prendaria')
            <h1>CONTRATO DE PRÉSTAMO DE DINERO CON GARANTÍA PRENDARIA</h1>
            <p>Conste por el presente documento privado, un Contrato de Préstamo de Dinero con Garantía Prendaria, que suscriben las partes que a continuación se detallan:</p>
            
            <p><strong>PRIMERA.- (LAS PARTES)</strong> Son partes del presente contrato:<br>
            1.1. El señor(a) <strong>[NOMBRE PRESTAMISTA]</strong>, mayor de edad, hábil por derecho, con C.I. No. [CI PRESTAMISTA], en adelante denominado(a) "EL ACREEDOR".<br>
            1.2. El señor(a) <strong>{{ mb_strtoupper($loan->client->name) }}</strong>, mayor de edad, hábil por derecho, con C.I. No. {{ $loan->client->ci }}, domiciliado en {{ $loan->client->address ?? 'su respectivo domicilio' }}, en adelante denominado(a) "EL DEUDOR".</p>
            
            <p><strong>SEGUNDA.- (DEL PRÉSTAMO Y LOS INTERESES)</strong> EL ACREEDOR otorga en calidad de préstamo la suma de <strong>Bs. {{ number_format($loan->amount, 2) }} </strong>, a favor del DEUDOR. Este préstamo devengará un interés pactado del <strong>{{ $loan->interest_rate }}%</strong> por el sistema {{ ucfirst($loan->amortization_system) }}.</p>
            
            <p><strong>TERCERA.- (DEL PLAZO)</strong> El plazo establecido para la cancelación total del capital e intereses es de <strong>{{ $loan->term_months }} meses</strong> computables a partir de la firma del presente documento, finalizando el {{ \Carbon\Carbon::parse($loan->start_date)->addMonths($loan->term_months)->format('d/m/Y') }}.</p>

            <p><strong>CUARTA.- (DE LA GARANTÍA PRENDARIA)</strong> Para garantizar el cumplimiento de la obligación descrita en la cláusula segunda, EL DEUDOR entrega en este acto a favor del ACREEDOR, en calidad de Prenda, el siguiente bien de su exclusiva propiedad: <strong>{{ $loan->guarantee_details ?? 'No especificado' }}</strong>.</p>
            
            <p><strong>QUINTA.- (ACEPTACIÓN)</strong> Ambas partes expresan su plena conformidad con cada una de las cláusulas precedentes, firmando al pie en señal de aceptación.</p>
        
        @elseif($loan->contract_type == 'pacto_rescate')
            <h1>CONTRATO DE VENTA DE BIEN CON PACTO DE RESCATE</h1>
            <p>Conste por el presente documento privado, un Contrato de Venta de Bien con Pacto de Rescate, que suscriben las partes que a continuación se detallan:</p>
            
            <p><strong>PRIMERA.- (LAS PARTES)</strong> Son partes del presente contrato:<br>
            1.1. El señor(a) <strong>{{ mb_strtoupper($loan->client->name) }}</strong>, mayor de edad, hábil por derecho, con C.I. No. {{ $loan->client->ci }}, domiciliado en {{ $loan->client->address ?? 'su respectivo domicilio' }}, en adelante denominado(a) "EL VENDEDOR".<br>
            1.2. El señor(a) <strong>[NOMBRE PRESTAMISTA]</strong>, mayor de edad, hábil por derecho, con C.I. No. [CI PRESTAMISTA], en adelante denominado(a) "EL COMPRADOR".</p>
            
            <p><strong>SEGUNDA.- (DEL OBJETO)</strong> EL VENDEDOR declara ser legítimo propietario de: <strong>{{ $loan->guarantee_details ?? 'No especificado' }}</strong>. Por convenir a sus intereses, transfiere dicho bien a favor del COMPRADOR por la suma acordada de <strong>Bs. {{ number_format($loan->amount, 2) }} </strong>, monto que declara haber recibido en su totalidad.</p>
            
            <p><strong>TERCERA.- (DEL PACTO DE RESCATE)</strong> Las partes acuerdan establecer un Pacto de Rescate sobre el bien objeto del presente contrato por un plazo máximo de <strong>{{ $loan->term_months }} meses</strong>. EL VENDEDOR se reserva el derecho de readquirir (rescatar) el bien transferido devolviendo la suma de Bs. {{ number_format($loan->amount, 2) }} más el pago pactado por concepto de resguardo/interés de <strong>{{ $loan->interest_rate }}%</strong>.</p>
            
            <p><strong>CUARTA.- (VENCIMIENTO)</strong> Si EL VENDEDOR no hiciere uso de su derecho de rescate en el plazo establecido, el bien quedará consolidado de forma definitiva a favor del COMPRADOR, sin necesidad de requerimiento judicial previo.</p>

            <p><strong>QUINTA.- (ACEPTACIÓN)</strong> Ambas partes expresan su plena conformidad con cada una de las cláusulas precedentes, firmando al pie en señal de aceptación.</p>
            
        @elseif($loan->contract_type == 'dacion_pago')
            <h1>DOCUMENTO PRIVADO DE DACIÓN EN PAGO</h1>
            <p>Conste por el presente documento privado, una Dación en Pago, que suscriben las partes que a continuación se detallan:</p>
            
            <p><strong>PRIMERA.- (LAS PARTES)</strong> Son partes del presente contrato:<br>
            1.1. El señor(a) <strong>{{ mb_strtoupper($loan->client->name) }}</strong>, mayor de edad, hábil por derecho, con C.I. No. {{ $loan->client->ci }}, en adelante denominado(a) "EL DEUDOR".<br>
            1.2. El señor(a) <strong>[NOMBRE PRESTAMISTA]</strong>, mayor de edad, hábil por derecho, con C.I. No. [CI PRESTAMISTA], en adelante denominado(a) "EL ACREEDOR".</p>
            
            <p><strong>SEGUNDA.- (DE LA DEUDA ANTERIOR)</strong> EL DEUDOR reconoce de forma expresa y libre adeudar a favor del ACREEDOR la suma de <strong>Bs. {{ number_format($loan->amount, 2) }}</strong>, proveniente de obligaciones contraídas con anterioridad.</p>
            
            <p><strong>TERCERA.- (DE LA DACIÓN EN PAGO)</strong> Ante la imposibilidad de cumplir con el pago en efectivo, EL DEUDOR, de su libre y espontánea voluntad, otorga en calidad de DACIÓN EN PAGO a favor del ACREEDOR el siguiente bien: <strong>{{ $loan->guarantee_details ?? 'No especificado' }}</strong>.</p>
            
            <p><strong>CUARTA.- (EXTINCIÓN DE LA OBLIGACIÓN)</strong> EL ACREEDOR declara aceptar expresamente la presente Dación en Pago, recibiendo el bien descrito a su entera satisfacción, dándose por cancelada y extinguida en su totalidad la deuda referida en la cláusula segunda.</p>

            <p><strong>QUINTA.- (ACEPTACIÓN)</strong> Ambas partes expresan su plena conformidad con cada una de las cláusulas precedentes, firmando al pie en señal de aceptación.</p>
        
        @else
            <h1>CONTRATO DE PRÉSTAMO DE DINERO</h1>
            <p><strong>Nota:</strong> No se ha especificado un tipo de contrato de garantía para este préstamo.</p>
        @endif

        <p style="text-align: right; margin-top: 3rem;">Bolivia, {{ now()->format('d/m/Y') }}</p>

        <div class="firmas">
            <div class="firma-box">
                <br><br>
                <strong>EL DEUDOR</strong><br>
                {{ $loan->client->name }}<br>
                C.I. {{ $loan->client->ci }}
            </div>
            <div class="firma-box">
                <br><br>
                <strong>EL ACREEDOR</strong><br>
                Prestamista
            </div>
        </div>
    </div>
</body>
</html>
