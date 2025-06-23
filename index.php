<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Tracker - Rajalakshmi Engineering College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
       /* Import Elegant Fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Lora:wght@400;600&family=Montserrat:wght@300;400;700&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background-color: #121212;
    color: #ffffff;
}

/* Headings and Typography */
h1, h2, h3, h4 {
    font-family: 'Montserrat', sans-serif;
    font-weight: 500;
    letter-spacing: 1px;
    margin-bottom: 30px;
}

h2 {
    color: #3cbeff;
    font-size: 2.5rem;
    margin-bottom: 50px;
}


/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, rgba(0, 85, 104, 0.5), rgba(0, 153, 204, 0.5)), url('rec.jpg') center/cover no-repeat;
    color: white;
    padding: 150px 0;
    text-align: center;
    margin-bottom: 150px;
}

.hero-section h1 {
    font-size: 3rem;
    font-weight: 700;
    text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
    font-family: 'Lora', serif;
}

.hero-section p {
    font-size: 1.3rem;
    font-style: italic;
    font-family: 'Inter', sans-serif;
}

/* Navbar */
.navbar {
    background: linear-gradient(90deg, #222, #333);
    font-family: 'Montserrat', sans-serif;
    font-size: 18px;
}

.nav-link {
    color: #ffffff !important;
    font-weight: 500;
    margin-right: 10px;
}

.nav-link:hover {
    color: #00aaff !important;
}

/* Stats Card */
.stats-card {
    background: #1e1e1e;
    box-shadow: 0px 6px 12px rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 20px;
    transition: transform 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.stats-card:hover {
    transform: translateY(-5px);
}

/* Table */
.table thead {
    background-color: #0055cc;
    color: white;
}

/* Charts */
.chart-container {
    width: 100%;
    max-width: 500px;
    margin: auto;
    font-family: 'Inter', sans-serif;
}

/* Top Recruiters */
.top-recruiters img {
    width: 80px;
    filter: grayscale(20%);
    transition: filter 0.3s ease;
}

.top-recruiters img:hover {
    filter: none;
}

/* Why Choose Us Cards */
.why-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0px 4px 8px rgba(255, 255, 255, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 30px;
}

.why-card:hover {
    transform: scale(1.05);
    box-shadow: 0px 15px 30px rgba(0, 170, 255, 0.5);
}

/* Why Card Icons */
.why-card .icon {
    margin-bottom: 10px;
}

.why-card .icon img {
    width: 50px;
    height: 50px;
    filter: brightness(1.2);
    transition: transform 0.3s ease;
}

.why-card:hover .icon img {
    transform: scale(1.2);
}

.why-card h4 {
    font-size: 1.6rem;
    font-weight: 500;
    color: #00aaff;
    font-family: 'Montserrat', sans-serif;
}

.why-card p {
    font-size: 1rem;
    line-height: 1.6;
    color: #ddd;
    font-family: 'Inter', sans-serif;
}

.why-card .icon i {
    font-size: 40px;
    transition: transform 0.3s ease;
}

.why-card:hover .icon i {
    transform: scale(1.2);
}

.section {
    margin-bottom: 100px !important; /* Increase spacing */
}


    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Placement Tracker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#statistics">Statistics</a></li>
                <li class="nav-item"><a class="nav-link" href="#recruiters">Top Recruiters</a></li>
                <li class="nav-item"><a class="nav-link" href="#why-raj">Why Rajalakshmi?</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Rajalakshmi Engineering College Placements 2025</h1>
        <p>Empowering Careers, Shaping Futures</p>
    </div>
</section>

<!-- Why Rajalakshmi? -->
<section id="why-raj" class="container section">
    <h2 class="text-center mb-5">Why Rajalakshmi?</h2>
    <div class="row">
    <div class="col-md-6">
        <div class="why-card">
            <div class="icon"><i class="fa-solid fa-graduation-cap"></i></div>
            <h4>Quality Input</h4>
            <p>50% of students admitted through merit-based selection.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="why-card">
            <div class="icon"><i class="fa-solid fa-book-open"></i></div>
            <h4>Strong Academics</h4>
            <p>High university ranks, experienced faculty.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="why-card">
            <div class="icon"><i class="fa-solid fa-certificate"></i></div>
            <h4>Value Addition</h4>
            <p>Certifications, guest lectures, industrial visits.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="why-card">
            <div class="icon"><i class="fa-solid fa-users"></i></div>
            <h4>Skill Enhancement</h4>
            <p>Soft skills training for industry readiness.</p>
        </div>
    </div>
</div>
</section>


<!-- Placement Statistics -->
<section id="statistics" class="container my-5 section">
    <h2 class="text-center">Placement Statistics</h2>
    <div class="row text-center">
        <div class="col-md-4"><div class="stats-card"><h3>92%</h3><p>Overall Placement Rate</p></div></div>
        <div class="col-md-4"><div class="stats-card"><h3>₹21 LPA</h3><p>Highest Package</p></div></div>
        <div class="col-md-4"><div class="stats-card"><h3>₹6.5 LPA</h3><p>Average Package</p></div></div>
    </div>
</section>

<!-- Charts -->
<section class="container my-5 section">
    <h2 class="text-center">Placement Trends</h2>
    <div class="chart-container">
        <canvas id="placementChart"></canvas>
    </div>
</section>

<section class="container my-5 section">
    <h2 class="text-center">Department-wise Placement Statistics</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <canvas id="placementPieChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <canvas id="placementBarChart"></canvas>
            </div>
        </div>
    </div>
</section>

<!-- Top Recruiters -->
<section id="recruiters" class="container my-5 section">
    <h2 class="text-center">Top Recruiters</h2>
    <div class="row text-center top-recruiters">
        <div class="col-md-2"><img src="./images/amazon.png" alt="Amazon"></div>
        <div class="col-md-2"><img src="./images/tcs.png" alt="TCS"></div>
        <div class="col-md-2"><img src="./images/infosys.png" alt="Infosys"></div>
        <div class="col-md-2"><img src="./images/wipro.png" alt="Wipro"></div>
        <div class="col-md-2"><img src="./images/cognizant.png" alt="Cognizant"></div>
        <div class="col-md-2"><img src="./images/accenture.png" alt="Accenture"></div>
    </div>
    <div class="row text-center top-recruiters mt-3">
        <div class="col-md-2"><img src="./images/hcl.png" alt="HCL"></div>
        <div class="col-md-2"><img src="./images/capgemini.png" alt="Capgemini"></div>
        <div class="col-md-2"><img src="./images/deloitte.png" alt="Deloitte"></div>
        <div class="col-md-2"><img src="./images/zoho.png" alt="Zoho"></div>
        <div class="col-md-2"><img src="./images/oracle.png" alt="Oracle"></div>
        <div class="col-md-2"><img src="./images/mindtree.png" alt="Mindtree"></div>
    </div>
</section>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const placementChart = new Chart(document.getElementById('placementChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['2018', '2019', '2020', '2021', '2022', '2023'],
                datasets: [{
                    label: 'Placement Offers',
                    data: [1246, 1319, 1508, 2058, 1640, 1570],
                    borderColor: '#6A89CC',
                    backgroundColor: 'rgba(106, 137, 204, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff' // White text color
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#ffffff' // White text color
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)' // Light grid lines
                        }
                    },
                    y: {
                        ticks: {
                            color: '#ffffff' // White text color
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                }
            }
        });

        const pastelColors = ['#A8DADC', '#F4A261', '#E9C46A', '#8ECAE6'];

        const placementPieChart = new Chart(document.getElementById('placementPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['CSE', 'ECE', 'Mechanical', 'IT'],
                datasets: [{
                    data: [95, 90, 85, 92],
                    backgroundColor: pastelColors
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff' // White text color
                        }
                    }
                }
            }
        });

        const placementBarChart = new Chart(document.getElementById('placementBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['CSE', 'ECE', 'Mechanical', 'IT'],
                datasets: [{
                    label: 'Placement %',
                    data: [95, 90, 85, 92],
                    backgroundColor: pastelColors,
                    borderColor: ['#457B9D', '#E76F51', '#F4A261', '#2A9D8F'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff' // White text color
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#ffffff' // White text color
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#ffffff' // White text color
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)'
                        }
                    }
                }
            }
        });
    });
</script>

</body>
</html>
