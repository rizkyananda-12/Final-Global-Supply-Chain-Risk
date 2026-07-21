<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Global Supply Chain Intelligence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #map { height: 350px; width: 100%; border-radius: 12px; border: 1px solid #ddd; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .news-item { transition: all 0.2s ease-in-out; }
        .news-item:hover { background-color: #f8f9fa; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm">
        <div>
            <h2 class="fw-bold text-dark mb-0">Dashboard - Global Supply Chain Risk Platform</h2>
            <small class="text-muted">Multi-API Intelligent Decision Support System</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <select id="countrySelect" class="form-select form-select-lg fw-bold text-primary border-primary"></select>
            <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger fw-bold">Logout</button>
            </form>
        </div>
    </div>
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="m-0 font-weight-bold text-dark">⭐ Favorite Monitoring List</h5>
                        <button type="button" class="btn btn-sm btn-warning font-weight-bold ms-2" onclick="addActiveCountryToFavorite()">
                            + Tambah Negara Ini
                        </button>
                    </div>
                    <small class="text-muted">Dipantau secara Waktu Nyata</small>
                </div>
                <div class="card-body">
                    <div class="row" id="favorite-list-container">
                        <div class="col-12 text-center text-muted py-3">
                            Memuat data favorit...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center mb-3">
        <span class="badge bg-secondary px-3 py-2 rounded-pill">
            Role: {{ auth()->check() ? ucfirst(auth()->user()->role ?? 'user') : 'Guest' }}
        </span>

        @if(auth()->check() && (auth()->user()->role ?? '') === 'admin')
            <a href="{{ route('admin.manage') }}" class="btn btn-warning btn-sm fw-bold ms-2 rounded-pill">
                ⚙️ Kelola Data Master
            </a>
        @endif
    </div>
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
                    <div class="col-md bg-light p-3 rounded-3 mx-1">
                        <small class="text-muted d-block">Populasi</small>
                        <strong id="txtPopulation" class="fs-4 text-dark">--</strong>
                    </div>
                    <div class="col bg-light p-3 rounded-3 mx-1 text-nowrap">
                        <small class="text-muted d-block">Kondisi Cuaca</small>
                        <strong id="txtWeather" class="fs-4 text-dark">--</strong>
                    </div>
                    <div class="p-3 bg-light rounded-3 text-center w-100 mt-2">
                        <small class="text-muted d-block mb-1">Pelabuhan Aktif</small>
                        <div id="active-ports-count" class="fw-bold text-dark fs-5">-</div>
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
                <h5 class="fw-bold mb-3 text-secondary">Risk Component Breakdown</h5>
                <canvas id="chartRisk" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card p-4 bg-white">
                <h5 class="fw-bold mb-3 text-secondary">⚖️ Country Comparison Engine</h5>
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">Negara Pertama</label>
                        <select id="compareCountry1" class="form-select fw-bold"></select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">Negara Kedua</label>
                        <select id="compareCountry2" class="form-select fw-bold"></select>
                    </div>
                    <div class="col-md-2">
                        <button id="btnCompare" class="btn btn-primary w-100 fw-bold">Bandingkan</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Parameter Indikator</th>
                                <th id="th-country-1">Negara 1</th>
                                <th id="th-country-2">Negara 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start fw-bold">Skor Risiko Total</td>
                                <td id="val-risk-1">-</td>
                                <td id="val-risk-2">-</td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold">Jumlah Pelabuhan Aktif</td>
                                <td id="val-ports-1">-</td>
                                <td id="val-ports-2">-</td>
                            </tr>
                            <tr>
                                <td class="text-start fw-bold">Estimasi Delay Logistik</td>
                                <td id="val-delay-1">-</td>
                                <td id="val-delay-2">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
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
    <div class="row">
        <div class="col-12">
            <div class="card p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-secondary mb-0">📰 Latest Supply Chain Intelligence News</h5>
                    <small class="text-muted">Real-time monitored updates</small>
                </div>
                <div id="news-container" class="list-group list-group-flush">
                    <div class="text-center text-muted py-3">Memuat berita terkait...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let favoriteCountries = JSON.parse(localStorage.getItem('favorite_countries')) || ['ID', 'SG', 'KR'];
    function getRiskBadge(score, status) {
        let bgClass = 'bg-success';
        if (score >= 65 || status === 'HIGH' || status === 'High Risk') {
            bgClass = 'bg-danger';
        } else if (score >= 35 || status === 'MODERATE' || status === 'Medium Risk') {
            bgClass = 'bg-warning text-dark';
        }
        return `<span class="badge ${bgClass} p-2">${score} ${status ? '(' + status + ')' : ''}</span>`;
    }

    function addActiveCountryToFavorite() {
        let activeIso = null;
        const possibleInputs = ['countrySelect', 'country-select', 'select-country', 'country', 'iso', 'country_code'];
        
        for (let id of possibleInputs) {
            let elem = document.getElementById(id);
            if (elem && elem.value) {
                activeIso = elem.value;
                break;
            }
        }
        
        if (!activeIso && typeof currentCountryIso !== 'undefined') activeIso = currentCountryIso;
        if (!activeIso && typeof selectedCountry !== 'undefined') activeIso = selectedCountry;
        if (!activeIso) activeIso = prompt("Masukkan Kode ISO 2 Negara (contoh: SG, US, JP, KR, DE):", "SG");

        if (activeIso) {
            toggleFavorite(activeIso);
        }
    }

    function toggleFavorite(iso) {
        if (!iso) return;
        iso = iso.toUpperCase();
        
        const index = favoriteCountries.indexOf(iso);
        if (index > -1) {
            favoriteCountries.splice(index, 1);
            alert(`Negara ${iso} dihapus dari daftar favorit.`);
        } else {
            favoriteCountries.push(iso);
            alert(`Negara ${iso} berhasil ditambahkan ke daftar favorit!`);
        }
        localStorage.setItem('favorite_countries', JSON.stringify(favoriteCountries));
        loadFavoritesUI();
    }

    function loadFavoritesUI() {
        const container = document.getElementById('favorite-list-container');
        if (!container) return;

        if (favoriteCountries.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center text-muted py-4">
                    Belum ada negara favorit yang dipantau.
                </div>`;
            return;
        }
        fetchRealFavoritesData(favoriteCountries);
    }
    async function fetchRealFavoritesData(isoArray) {
        const container = document.getElementById('favorite-list-container');
        if (!container) return;

        container.innerHTML = '<div class="col-12 text-center text-muted py-3">Memuat data asli favorit...</div>';

        try {
            const promises = isoArray.map(async (iso) => {
                const [resRisk, resPorts] = await Promise.all([
                    fetch(`/api/risk?iso=${iso}`).then(r => r.ok ? r.json() : null),
                    fetch(`/api/ports?iso=${iso}`).then(r => r.ok ? r.json() : [])
                ]);

                const totalPorts = Array.isArray(resPorts) ? resPorts.length : 0;
                const riskScore = resRisk ? resRisk.total_risk_score : '--';
                const status = resRisk ? resRisk.status : 'UNKNOWN';
                const currency = resRisk ? resRisk.currency : 'USD';

                return {
                    iso: iso,
                    name: getCountryNameByIso(iso),
                    currency: currency,
                    active_ports: `${totalPorts} Pelabuhan`,
                    risk_score: riskScore,
                    status: status
                };
            });

            const realFavorites = await Promise.all(promises);
            renderFavoritesCards(realFavorites);
        } catch (error) {
            console.error("Gagal mengambil data asli favorit:", error);
        }
    }

    function getCountryNameByIso(iso) {
        const names = {
            'ID': 'Indonesia', 'SG': 'Singapura', 'KR': 'Korea Selatan', 
            'US': 'Amerika Serikat', 'DE': 'Jerman', 'CN': 'China', 'JP': 'Jepang',
            'AU': 'Australia', 'GB': 'Inggris', 'CA': 'Kanada', 'IN': 'India'
        };
        return names[iso] || `Negara (${iso})`;
    }

    function renderFavoritesCards(favorites) {
        const container = document.getElementById('favorite-list-container');
        if (!container) return;

        let html = '';
        favorites.forEach(item => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 font-weight-bold text-primary">${item.name} (${item.iso})</h6>
                                <small class="text-muted">${item.active_ports} | ${item.currency}</small>
                                <div class="mt-2">
                                    Skor Risiko: ${getRiskBadge(item.risk_score, item.status)}
                                </div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="toggleFavorite('${item.iso}')" title="Hapus">
                                    ✖
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }
    var map = L.map('map').setView([-2.5, 118], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var markerGroup = L.layerGroup().addTo(map);

    var ctxRisk = document.getElementById('chartRisk').getContext('2d');
    var chartRiskInstance;

    $(document).ready(function() {
        loadFavoritesUI();

        $.get("{{ url('/api/countries') }}", function(countries) {
            $('#countrySelect, #compareCountry1, #compareCountry2').empty();

            if (countries && countries.length > 0) {
                countries.forEach(c => {
                    $('#countrySelect').append(`<option value="${c.iso2}">${c.name}</option>`);
                    $('#compareCountry1').append(`<option value="${c.iso2}">${c.name}</option>`);
                    $('#compareCountry2').append(`<option value="${c.iso2}">${c.name}</option>`);
                });

                $('#compareCountry1').val(countries[0].iso2);
                $('#compareCountry2').val(countries.length > 1 ? countries[1].iso2 : countries[0].iso2);
                
                var initialIso = $('#countrySelect').val();
                fetchIntelligenceData(initialIso);
                fetchPortData(initialIso);
                fetchNewsData(initialIso);
                loadComparisonData();
            }
        });

        $('#countrySelect').change(function() {
            var selectedIso = $(this).val();
            fetchIntelligenceData(selectedIso);
            fetchPortData(selectedIso);
            fetchNewsData(selectedIso);
        });

        $('#btnCompare').click(loadComparisonData);
        $('#compareCountry1, #compareCountry2').change(loadComparisonData);
    });

    function loadComparisonData() {
        let c1 = $('#compareCountry1').val();
        let c2 = $('#compareCountry2').val();

        if (!c1 || !c2) return;

        $.get("{{ url('/api/compare-countries') }}", { country1: c1, country2: c2 }, function(response) {
            let data = response.comparison;
            if (data && data.length === 2) {
                $('#th-country-1').text(`${data[0].name} (${data[0].iso})`);
                $('#th-country-2').text(`${data[1].name} (${data[1].iso})`);

                $('#val-risk-1').html(getRiskBadge(data[0].risk_score));
                $('#val-risk-2').html(getRiskBadge(data[1].risk_score));

                $('#val-ports-1').text(`${data[0].active_ports} Pelabuhan`);
                $('#val-ports-2').text(`${data[1].active_ports} Pelabuhan`);

                $('#val-delay-1').text(data[0].avg_delay_days);
                $('#val-delay-2').text(data[1].avg_delay_days);
            }
        }).fail(function(xhr, status, error) {
            console.error("Gagal memuat API perbandingan:", error);
        });
    }

    function fetchPortData(isoCode) {
        $.get("{{ url('/api/ports') }}?iso=" + isoCode, function(ports) {
            let targetElement = $('#active-ports-count');
            let totalPorts = Array.isArray(ports) ? ports.length : 0;
            
            if (totalPorts > 0) {
                let portNames = ports.map(p => p.port_name || p.name).join(', ');
                targetElement.html(`<span class="badge bg-primary fs-6 mb-1">${ports.length} Pelabuhan</span><br><small class="text-secondary">${portNames}</small>`);
            } else {
                targetElement.html('<span class="text-muted">Tidak Ada Pelabuhan</span>');
            }

            markerGroup.clearLayers();
            if (ports && ports.length > 0) {
                ports.forEach(p => {
                    let lat = p.latitude || p.lat;
                    let lon = p.longitude || p.lon;
                    let portName = p.port_name || p.name;

                    if (lat && lon) {
                        L.marker([lat, lon]).addTo(markerGroup)
                         .bindPopup(`<b>${portName}</b><br>Logistics Hub Info.`);
                    }
                });

                let firstLat = ports[0].latitude || ports[0].lat;
                let firstLon = ports[0].longitude || ports[0].lon;
                if (firstLat && firstLon) {
                    map.setView([firstLat, firstLon], 5);
                }
            }
        }).fail(function() {
            $('#active-ports-count').text('0 Pelabuhan');
            markerGroup.clearLayers();
        });
    }

    function fetchNewsData(isoCode) {
        let newsContainer = $('#news-container');
        newsContainer.html('<div class="text-center text-muted py-3">Memuat berita terbaru dari API...</div>');

        $.get("{{ url('/api/news') }}?iso=" + isoCode, function(response) {
            let articles = response.articles || [];

            if (articles.length > 0) {
                let html = '';
                articles.forEach(article => {
                    let sent = (article.sentiment || '').toLowerCase();
                    let sentimentBadge = '<span class="badge bg-warning text-dark">Neutral</span>';
                    if (sent === 'positive') sentimentBadge = '<span class="badge bg-success">Positive</span>';
                    else if (sent === 'negative') sentimentBadge = '<span class="badge bg-danger">Negative</span>';

                    html += `
                        <div class="list-group-item news-item py-3 px-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold text-dark">
                                    <a href="${article.url}" target="_blank" class="text-decoration-none text-dark">${article.title}</a>
                                </h6>
                                ${sentimentBadge}
                            </div>
                            <p class="text-muted small mb-1">${article.description}</p>
                            <small class="text-secondary">Sumber: ${article.source} • ${article.published_at}</small>
                        </div>`;
                });
                newsContainer.html(html);
            } else {
                newsContainer.html('<div class="text-center text-muted py-3">Tidak ada berita tersedia untuk wilayah ini.</div>');
            }
        }).fail(function() {
            newsContainer.html('<div class="text-center text-danger py-3">Gagal terhubung ke API berita.</div>');
        });
    }

    function fetchIntelligenceData(isoCode) {
        $.get("{{ url('/api/risk') }}?iso=" + isoCode, function(data) {
            $('#txtTotalScore').text(data.total_risk_score);
            $('#badgeStatus').text(data.status);

            var card = $('#riskCard');
            card.removeClass('bg-success bg-warning bg-danger bg-dark');
            if (data.status === "Low Risk") card.addClass('bg-success');
            else if (data.status === "Medium Risk") card.addClass('bg-warning text-dark');
            else card.addClass('bg-danger');

            $('#txtCurrency').text(data.currency);
            $('#txtInflation').text(data.metrics.current_inflation);
            $('#txtWind').text(data.metrics.current_windspeed);
            $('#txtGdp').text('$' + (data.metrics.gdp / 1e12).toFixed(2) + ' T');

            var pop = data.metrics.population;
            if (pop >= 1e9) $('#txtPopulation').text((pop / 1e9).toFixed(1) + ' M');
            else if (pop >= 1e6) $('#txtPopulation').text((pop / 1e6).toFixed(0) + ' Jt');
            else $('#txtPopulation').text(pop ? pop.toLocaleString() : '--');

            var coordinates = {
                'ID': {lat: -6.10, lon: 106.89}, 'DE': {lat: 53.54, lon: 9.93},
                'CN': {lat: 31.22, lon: 121.48}, 'AU': {lat: -33.86, lon: 151.21},
                'JP': {lat: 35.62, lon: 139.79}, 'US': {lat: 33.74, lon: -118.26},
                'SG': {lat: 1.26, lon: 103.81},  'GB': {lat: 51.96, lon: 1.31},
                'CA': {lat: 49.29, lon: -123.11}, 'KR': {lat: 35.10, lon: 129.04},
                'IN': {lat: 18.95, lon: 72.95}
            };

            var pos = coordinates[isoCode] || {lat: 0, lon: 0};

            $.get(`https://api.open-meteo.com/v1/forecast?latitude=${pos.lat}&longitude=${pos.lon}&current=weather_code,wind_speed_10m,temperature_2m`, function(weatherData) {
                if (weatherData && weatherData.current) {
                    var code = weatherData.current.weather_code;
                    var wind = weatherData.current.wind_speed_10m;
                    var temp = weatherData.current.temperature_2m;
                   
                    var weatherText = 'Unknown';
                    if (code === 0) weatherText = 'Cerah';
                    else if ([1, 2, 3].includes(code)) weatherText = 'Berawan';
                    else if ([45, 48].includes(code)) weatherText = 'Berkabut';
                    else if ([51, 53, 55, 61, 63, 65].includes(code)) weatherText = 'Hujan';
                    else if ([71, 73, 75, 77, 85, 86].includes(code)) weatherText = 'Salju';
                    else weatherText = 'Badai';

                    $('#txtWind').text(wind + ' km/h');
                    $('#txtWeather').text(weatherText + ' (' + Math.round(temp) + '°C)');
                }
            }).fail(function() {
                $('#txtWind').text('--');
                $('#txtWeather').text('--');
            });
                
            $('#progressPos').css('width', data.sentiment_analysis.positive).text('Pos: ' + data.sentiment_analysis.positive);
            $('#progressNeu').css('width', data.sentiment_analysis.neutral).text('Neu: ' + data.sentiment_analysis.neutral);
            $('#progressNeg').css('width', data.sentiment_analysis.negative).text('Neg: ' + data.sentiment_analysis.negative);

            if (chartRiskInstance) chartRiskInstance.destroy();
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
    }  
</script>
</body>
</html>