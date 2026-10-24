<?php
// =============================================
// WT ASSIGNMENT 1 – Electricity Bill Calculator
// Subject : Web Technology (WT)
// =============================================

$units     = '';
$bill      = null;
$breakdown = [];
$error     = '';
$month     = '';
$name      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = htmlspecialchars(trim($_POST['name']  ?? ''));
    $month = htmlspecialchars(trim($_POST['month'] ?? ''));
    $units = trim($_POST['units'] ?? '');

    if ($units === '' || !is_numeric($units) || (float)$units < 0) {
        $error = 'Please enter a valid non-negative number of units consumed.';
    } else {
        $units = (float)$units;

        // ── Slab Calculation ──────────────────────────────────────────────
        // Slab 1: First  50 units  @ Rs. 3.50 / unit
        // Slab 2: Next  100 units  @ Rs. 4.00 / unit  (51 – 150)
        // Slab 3: Next  100 units  @ Rs. 5.20 / unit  (151 – 250)
        // Slab 4: Above 250 units  @ Rs. 6.50 / unit
        // ─────────────────────────────────────────────────────────────────

        $remaining = $units;
        $bill      = 0;

        $slabs = [
            ['limit' => 50,  'rate' => 3.50, 'label' => 'First 50 units'],
            ['limit' => 100, 'rate' => 4.00, 'label' => 'Next 100 units (51 – 150)'],
            ['limit' => 100, 'rate' => 5.20, 'label' => 'Next 100 units (151 – 250)'],
            ['limit' => INF, 'rate' => 6.50, 'label' => 'Above 250 units'],
        ];

        foreach ($slabs as $slab) {
            if ($remaining <= 0) break;

            $consumed  = min($remaining, $slab['limit']);
            $charge    = $consumed * $slab['rate'];
            $bill     += $charge;
            $remaining -= $consumed;

            $breakdown[] = [
                'label'  => $slab['label'],
                'units'  => $consumed,
                'rate'   => $slab['rate'],
                'charge' => $charge,
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Electricity Bill Calculator – WT Assignment 1. Calculate your monthly electricity bill based on residential slab rates instantly." />
    <title>&#9889; Electricity Bill Calculator | WT Assignment 1</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- Animated background orbs -->
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<div class="bg-orb orb-3"></div>

<div class="page-wrapper">

    <!-- HEADER -->
    <header class="site-header">
        <div class="header-badge">WT Assignment 1</div>
        <div class="header-icon">&#9889;</div>
        <h1 class="header-title">Electricity Bill Calculator</h1>
        <p class="header-subtitle">Instant slab-based bill estimation for residential consumers</p>
    </header>

    <!-- SLAB RATES INFO CARDS -->
    <section class="slab-section" aria-label="Slab Rate Information">
        <h2 class="section-heading">Tariff Slab Rates</h2>
        <div class="slab-grid">
            <div class="slab-card" style="--accent:#6366f1">
                <span class="slab-icon">&#128309;</span>
                <div class="slab-range">0 &ndash; 50 Units</div>
                <div class="slab-rate">&#8377; 3.50<span>/unit</span></div>
                <div class="slab-tag">First Slab</div>
            </div>
            <div class="slab-card" style="--accent:#8b5cf6">
                <span class="slab-icon">&#128995;</span>
                <div class="slab-range">51 &ndash; 150 Units</div>
                <div class="slab-rate">&#8377; 4.00<span>/unit</span></div>
                <div class="slab-tag">Second Slab</div>
            </div>
            <div class="slab-card" style="--accent:#f59e0b">
                <span class="slab-icon">&#128992;</span>
                <div class="slab-range">151 &ndash; 250 Units</div>
                <div class="slab-rate">&#8377; 5.20<span>/unit</span></div>
                <div class="slab-tag">Third Slab</div>
            </div>
            <div class="slab-card" style="--accent:#ec4899">
                <span class="slab-icon">&#128308;</span>
                <div class="slab-range">Above 250 Units</div>
                <div class="slab-rate">&#8377; 6.50<span>/unit</span></div>
                <div class="slab-tag">Fourth Slab</div>
            </div>
        </div>
    </section>

    <!-- MAIN FORM -->
    <main class="main-content">
        <div class="calculator-card">
            <div class="card-header">
                <span class="card-header-icon">&#129518;</span>
                <h2 class="card-title">Enter Your Details</h2>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error" role="alert">
                <span class="alert-icon">&#9888;&#65039;</span>
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form id="billForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#result" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Consumer Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            placeholder="e.g. Aditya Kumar"
                            value="<?= htmlspecialchars($name) ?>"
                        />
                    </div>
                    <div class="form-group">
                        <label for="month" class="form-label">Billing Month</label>
                        <input
                            type="month"
                            id="month"
                            name="month"
                            class="form-input"
                            value="<?= htmlspecialchars($month) ?>"
                        />
                    </div>
                </div>

                <div class="form-group">
                    <label for="units" class="form-label">Units Consumed (kWh) <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <span class="input-prefix">&#9889;</span>
                        <input
                            type="number"
                            id="units"
                            name="units"
                            class="form-input has-prefix"
                            placeholder="e.g. 320"
                            min="0"
                            step="0.01"
                            value="<?= is_numeric($units) ? htmlspecialchars($units) : '' ?>"
                            required
                        />
                        <span class="input-suffix">kWh</span>
                    </div>
                    <!-- Live slider for quick selection -->
                    <input
                        type="range"
                        id="unitsSlider"
                        class="range-slider"
                        min="0"
                        max="500"
                        step="1"
                        value="<?= is_numeric($units) ? (int)$units : 0 ?>"
                        aria-label="Units slider"
                    />
                    <div class="slider-labels">
                        <span>0</span><span>125</span><span>250</span><span>375</span><span>500</span>
                    </div>
                </div>

                <button type="submit" id="calcBtn" class="btn-calculate">
                    <span class="btn-text">Calculate Bill</span>
                    <span class="btn-icon">&#8594;</span>
                </button>
            </form>
        </div>

        <!-- RESULT SECTION -->
        <?php if ($bill !== null && !$error): ?>
        <div id="result" class="result-card animate-in">

            <!-- Bill Summary -->
            <div class="result-header">
                <div class="result-header-left">
                    <div class="result-label">Total Bill Amount</div>
                    <div class="result-amount">
                        &#8377; <?= number_format($bill, 2) ?>
                    </div>
                    <?php if ($name): ?>
                    <div class="result-consumer">Consumer: <strong><?= htmlspecialchars($name) ?></strong></div>
                    <?php endif; ?>
                    <?php if ($month): ?>
                    <div class="result-consumer">
                        Month: <strong><?= date('F Y', strtotime($month . '-01')) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="result-header-right">
                    <div class="result-stat">
                        <div class="stat-value"><?= number_format($units, 2) ?></div>
                        <div class="stat-label">Total Units</div>
                    </div>
                    <div class="result-stat">
                        <div class="stat-value">&#8377;<?= number_format($units > 0 ? ($bill / $units) : 0, 2) ?></div>
                        <div class="stat-label">Avg Rate/Unit</div>
                    </div>
                </div>
            </div>

            <!-- Slab Breakdown Table -->
            <h3 class="breakdown-heading">&#128202; Slab-wise Breakdown</h3>
            <div class="table-wrapper">
                <table class="breakdown-table" aria-label="Slab breakdown table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Slab Description</th>
                            <th>Units</th>
                            <th>Rate (&#8377;/unit)</th>
                            <th>Charge (&#8377;)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($breakdown as $i => $row): ?>
                        <tr class="<?= $row['units'] > 0 ? 'row-active' : 'row-inactive' ?>">
                            <td class="col-num"><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['label']) ?></td>
                            <td class="col-units"><?= number_format($row['units'], 2) ?></td>
                            <td class="col-rate">&#8377; <?= number_format($row['rate'], 2) ?></td>
                            <td class="col-charge">&#8377; <?= number_format($row['charge'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="2"><strong>Total</strong></td>
                            <td class="col-units"><strong><?= number_format($units, 2) ?></strong></td>
                            <td>&mdash;</td>
                            <td class="col-charge total-amount"><strong>&#8377; <?= number_format($bill, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Visual bar chart -->
            <h3 class="breakdown-heading">&#128200; Visual Usage Breakdown</h3>
            <div class="bar-chart" aria-label="Visual bar chart of slab charges">
                <?php foreach ($breakdown as $row):
                    if ($row['units'] <= 0) continue;
                    $pct = $bill > 0 ? round(($row['charge'] / $bill) * 100, 1) : 0;
                ?>
                <div class="bar-row">
                    <div class="bar-label"><?= htmlspecialchars($row['label']) ?></div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:<?= $pct ?>%" aria-valuenow="<?= $pct ?>">
                            <span class="bar-pct"><?= $pct ?>%</span>
                        </div>
                    </div>
                    <div class="bar-amount">&#8377;<?= number_format($row['charge'], 2) ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Energy advice -->
            <div class="advice-box">
                <?php if ($units <= 50): ?>
                    <span class="advice-emoji">&#127807;</span>
                    <div><strong>Excellent!</strong> Your usage is very low. You fall entirely in the cheapest slab.</div>
                <?php elseif ($units <= 150): ?>
                    <span class="advice-emoji">&#9989;</span>
                    <div><strong>Good usage.</strong> You're within the first two slabs. Small savings can keep costs low.</div>
                <?php elseif ($units <= 250): ?>
                    <span class="advice-emoji">&#9888;&#65039;</span>
                    <div><strong>Moderate usage.</strong> Consider switching to energy-efficient appliances to reduce your bill.</div>
                <?php else: ?>
                    <span class="advice-emoji">&#128308;</span>
                    <div><strong>High consumption!</strong> You're paying at the highest slab rate. Energy auditing is recommended.</div>
                <?php endif; ?>
            </div>

            <button onclick="window.print()" class="btn-print" aria-label="Print bill">&#128424;&#65039; Print / Save Bill</button>
        </div>
        <?php endif; ?>
    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <p>WT Assignment 1 &nbsp;|&nbsp; Electricity Bill Calculator &nbsp;|&nbsp; PHP &amp; CSS</p>
        <p class="footer-note">Rates: &#8377;3.50 | &#8377;4.00 | &#8377;5.20 | &#8377;6.50 per unit as per slab policy</p>
    </footer>

</div><!-- /page-wrapper -->

<script src="script.js"></script>
</body>
</html>
