<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Supply Chain Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #map { height: 350px; width: 100%; border-radius: 12px; border: 1px solid #ddd; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container py-5">
    <!-- NAVBAR ATAS -->
    <div class="d-flex justify-content-between align-items-center mb-5 bg-white p-4 rounded-4 shadow-sm">
        <div>
            <h2 class="fw-bold text-dark mb-0">Global Supply Chain Risk Platform</h2>
            <small class="text-muted">Multi-API Intelligent Decision Support System</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Dropdown Pilihan Negara -->
            <select id="countrySelect" class="form-select form-select-lg fw-bold text-primary border-primary">
                <!-- Data diisi via AJAX -->
            </select>

            <!-- FORM LOGOUT DI LETAKKAN DI SINI (HTML SEBENARNYA) -->
            <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger fw-bold">Logout</button>
            </form>
        </div>
    </div>

    <!-- MAIN DASHBOARD -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div id="riskCard" class="card text-white bg-dark h-100 p-4 text-center d-flex flex-column justify-content-center">
                <h6 class="text-uppercase tracking-wider text-white-50">Total Risk Engine Score</h6>
                <h1 id="txtTotalScore" class="display-2 fw-bold my-2">--</h1>
                <span id="badgeStatus" class="badge bg-light text-dark fs-6 py-2 px-3 align-self-center rounded-pill fw-bold">ANALYZING...</span>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100 p-4">
                <h5 class="fw-bold mb-4 text-secondary">Global Country Indicators & Weather</h5>
                <div class="row text-center g-3">
                    <div class="col-6 col-md-3 bg-light p-3 rounded-3">
                        <small class="text-muted d-block">Mata Uang</small>
                        <strong id="txtCurrency" class="fs-4 text-dark">--</strong>
                    </div>
                    <div class="col-6 col-md-3 bg-light p-3 rounded-3">
                        <small class="text-muted d-block">Laju Inflasi</small>
                        <strong id="txtInflation" class="fs-4 text-dark">--</strong>
                    </div>
                    <div class="col-6 col-md-3 bg-light p-3 rounded-3">
                        <small class="text-muted d-block">Kecepatan Angin</small>
                        <strong id="txtWind" class="fs-4 text-dark">--</strong>
                    </div>
                    <div class="col-6 col-md-3 bg-light p-3 rounded-3">
                        <small class="text-muted d-block">Estimasi PDB (GDP)</small>
                        <strong id="txtGdp" class="fs-5 text-dark">--</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-secondary">Geospatial Port Positioning</h5>
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-3 text-secondary">Risk Component breakdown</h5>
                <canvas id="chartRisk" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card p-4 bg-white">
                <h5 class="fw-bold mb-3 text-secondary">AI News Sentiment Analysis Index</h5>
                <div class="progress" style="height: 30px; border-radius: 8px;">
                    <div id="progressPos" class="progress-bar bg-success fw-bold" role="progressbar" style="width: 0%">Positive</div>
                    <div id="progressNeu" class="progress-bar bg-warning text-dark fw-bold" role="progressbar" style="width: 0%">Neutral</div>
                    <div id="progressNeg" class="progress-bar bg-danger fw-bold" role="progressbar" style="width: 0%">Negative</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([-2.5, 118], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var markerGroup = L.layerGroup().addTo(map);

    var ctxRisk = document.getElementById('chartRisk').getContext('2d');
    var chartRiskInstance;

    $(document).ready(function() {
        $.get("{{ url('/api/countries') }}", function(countries) {
            $('#countrySelect').empty(); // Kosongkan placeholder sebelum mengisi data baru
            countries.forEach(c => {
                $('#countrySelect').append(`<option value="${c.iso2}">${c.name}</option>`);
            });
            if(countries.length > 0) {
                fetchIntelligenceData($('#countrySelect').val());
            }
        });

        $('#countrySelect').change(function() {
            fetchIntelligenceData($(this).val());
        });
    });

    function fetchIntelligenceData(isoCode) {
        $.get("{{ url('/api/risk') }}?iso=" + isoCode, function(data) {
            $('#txtTotalScore').text(data.total_risk_score);
            $('#badgeStatus').text(data.status);

            var card = $('#riskCard');
            card.removeClass('bg-success bg-warning bg-danger bg-dark');
            if(data.status === "Low Risk") card.addClass('bg-success');
            else if(data.status === "Medium Risk") card.addClass('bg-warning text-dark');
            else card.addClass('bg-danger');

            $('#txtCurrency').text(data.currency);
            $('#txtInflation').text(data.metrics.current_inflation);
            $('#txtWind').text(data.metrics.current_windspeed);
            $('#txtGdp').text('$' + (data.metrics.gdp / 1e12).toFixed(2) + ' T');

            $('#progressPos').css('width', data.sentiment_analysis.positive).text('Pos: ' + data.sentiment_analysis.positive);
            $('#progressNeu').css('width', data.sentiment_analysis.neutral).text('Neu: ' + data.sentiment_analysis.neutral);
            $('#progressNeg').css('width', data.sentiment_analysis.negative).text('Neg: ' + data.sentiment_analysis.negative);

            if(chartRiskInstance) chartRiskInstance.destroy();
            chartRiskInstance = new Chart(ctxRisk, {
                type: 'radar',
                data: {
                    labels: ['Weather', 'Inflation', 'Currency', 'News Sentiment'],
                    datasets: [{
                        label: 'Risk Indice (0-100)',
                        data: [data.components_score.weather, data.components_score.inflation, data.components_score.currency, data.components_score.news],
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2
                    }]
                },
                options: { scales: { r: { min: 0, max: 100 } } }
            });
        });

        $.get("{{ url('/api/ports') }}?iso=" + isoCode, function(ports) {
            markerGroup.clearLayers();
            if(ports && ports.length > 0) {
                ports.forEach(p => {
                    L.marker([p.lat, p.lon]).addTo(markerGroup)
                     .bindPopup(`<b>${p.name}</b><br>Logistics Hub Info.`);
                });
                map.setView([ports[0].lat, ports[0].lon], 5);
            }
        });
    }
</script>
</body>
</html>