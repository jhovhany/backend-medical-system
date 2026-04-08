<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription #{{ $prescription->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; line-height: 1.5; }
        .page { padding: 30px 40px; }

        /* Header */
        .header { border-bottom: 3px solid #1a73e8; padding-bottom: 16px; margin-bottom: 20px; display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .clinic-name { font-size: 20px; font-weight: bold; color: #1a73e8; }
        .clinic-sub  { font-size: 10px; color: #666; }
        .rx-badge { font-size: 36px; font-weight: bold; color: #1a73e8; line-height: 1; }

        /* Section titles */
        .section-title { background: #1a73e8; color: #fff; padding: 4px 10px; font-size: 11px; font-weight: bold;
                         text-transform: uppercase; margin: 16px 0 8px; letter-spacing: 0.5px; }

        /* Info grid */
        .info-grid { display: table; width: 100%; }
        .info-col   { display: table-cell; width: 50%; padding: 2px 0; }
        .info-label { font-weight: bold; color: #555; min-width: 110px; display: inline-block; }

        /* Medications table */
        table.meds { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.meds th { background: #e8f0fe; color: #1a73e8; padding: 6px 8px; text-align: left;
                        font-size: 10px; text-transform: uppercase; border: 1px solid #c5d4f8; }
        table.meds td { padding: 6px 8px; border: 1px solid #ddd; vertical-align: top; }
        table.meds tr:nth-child(even) td { background: #f8faff; }

        /* Instructions box */
        .instructions-box { border: 1px dashed #1a73e8; padding: 10px; background: #f0f7ff;
                            margin-top: 4px; border-radius: 4px; }

        /* Footer */
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 14px; display: table; width: 100%; }
        .signature { display: table-cell; width: 50%; }
        .validity  { display: table-cell; width: 50%; text-align: right; color: #555; font-size: 11px; }
        .sig-line  { border-top: 1px solid #333; width: 200px; margin-top: 40px; padding-top: 4px; font-size: 10px; }
        .watermark { text-align: center; margin-top: 20px; font-size: 9px; color: #bbb; }
    </style>
</head>
<body>
<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div class="header-left">
            <div class="clinic-name">Medical System</div>
            <div class="clinic-sub">Electronic Medical Prescription</div>
        </div>
        <div class="header-right">
            <div class="rx-badge">Rx</div>
        </div>
    </div>

    <!-- PRESCRIPTION META -->
    <div class="section-title">Prescription Information</div>
    <div class="info-grid">
        <div class="info-col">
            <span class="info-label">Prescription #:</span> {{ $prescription->id }}<br>
            <span class="info-label">Issue Date:</span> {{ $prescription->created_at->format('F j, Y') }}<br>
            @if($prescription->valid_until)
            <span class="info-label">Valid Until:</span> {{ $prescription->valid_until->format('F j, Y') }}<br>
            @endif
        </div>
        <div class="info-col">
            <span class="info-label">Consultation #:</span> {{ $prescription->consultation_id }}<br>
            <span class="info-label">Issued By:</span> {{ $prescription->issuedBy->name }}<br>
            @if($prescription->issuedBy->specialty)
            <span class="info-label">Specialty:</span> {{ $prescription->issuedBy->specialty }}<br>
            @endif
        </div>
    </div>

    <!-- PATIENT INFO -->
    @php $patient = $prescription->consultation->medicalRecord->patient; @endphp
    <div class="section-title">Patient Information</div>
    <div class="info-grid">
        <div class="info-col">
            <span class="info-label">Full Name:</span> {{ $patient->full_name }}<br>
            <span class="info-label">Date of Birth:</span> {{ $patient->date_of_birth->format('F j, Y') }}<br>
            <span class="info-label">Gender:</span> {{ ucfirst($patient->gender) }}<br>
        </div>
        <div class="info-col">
            @if($patient->blood_type)
            <span class="info-label">Blood Type:</span> {{ $patient->blood_type }}<br>
            @endif
            @if($patient->allergies)
            <span class="info-label">Allergies:</span> {{ $patient->allergies }}<br>
            @endif
            <span class="info-label">Record #:</span> {{ $prescription->consultation->medicalRecord->record_number }}<br>
        </div>
    </div>

    <!-- DIAGNOSIS -->
    @if($prescription->consultation->diagnosis)
    <div class="section-title">Diagnosis</div>
    <p>{{ $prescription->consultation->diagnosis }}</p>
    @endif

    <!-- MEDICATIONS -->
    <div class="section-title">Prescribed Medications</div>
    <table class="meds">
        <thead>
            <tr>
                <th>#</th>
                <th>Medication</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prescription->medications as $index => $med)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $med['name'] }}</strong></td>
                <td>{{ $med['dosage'] }}</td>
                <td>{{ $med['frequency'] }}</td>
                <td>{{ $med['duration'] }}</td>
                <td>{{ $med['notes'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- INSTRUCTIONS -->
    @if($prescription->instructions)
    <div class="section-title">Instructions</div>
    <div class="instructions-box">{{ $prescription->instructions }}</div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <div class="signature">
            <div class="sig-line">
                {{ $prescription->issuedBy->name }}<br>
                @if($prescription->issuedBy->specialty){{ $prescription->issuedBy->specialty }}@endif
            </div>
        </div>
        <div class="validity">
            Generated: {{ now()->format('F j, Y H:i') }}<br>
            @if($prescription->valid_until)
                Expires: {{ $prescription->valid_until->format('F j, Y') }}
            @else
                No expiry date set
            @endif
        </div>
    </div>

    <div class="watermark">
        This is an electronically generated prescription from Medical System API — {{ now()->format('Y') }}
    </div>

</div>
</body>
</html>
