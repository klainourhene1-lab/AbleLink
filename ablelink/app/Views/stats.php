<?php
use App\Models\SuccessStory;
$m = new SuccessStory();
$status = $m->statusCounts();
$cats = $m->categoryDistribution();
$monthsApproved = $m->monthlyProgression(6, 'approved');
$monthsPending = $m->monthlyProgression(6, 'pending');
$monthsRejected = $m->monthlyProgression(6, 'rejected');
$shares = $m->shareStats();
$mapA = []; foreach ($monthsApproved as $r) { $mapA[$r['ym']] = (int)$r['c']; }
$mapP = []; foreach ($monthsPending as $r) { $mapP[$r['ym']] = (int)$r['c']; }
$mapR = []; foreach ($monthsRejected as $r) { $mapR[$r['ym']] = (int)$r['c']; }
$rows = [];
for ($i = 5; $i >= 0; $i--) {
    $ym = date('Y-m', strtotime('-'.$i.' months'));
    $a = $mapA[$ym] ?? 0; $p = $mapP[$ym] ?? 0; $r = $mapR[$ym] ?? 0;
    $rows[] = ['ym'=>$ym,'a'=>$a,'p'=>$p,'r'=>$r];
}
$maxMonth = 1; foreach ($rows as $row) { $maxMonth = max($maxMonth, $row['a'], $row['p'], $row['r']); }
$tot = (int)($status['total'] ?? (($status['approved']??0)+($status['pending']??0)+($status['rejected']??0)));
$maxTot = max(1, $tot);
$pa = (int)($status['approved'] ?? 0);
$pp = (int)($status['pending'] ?? 0);
$pr = (int)($status['rejected'] ?? 0);
$perA = $tot > 0 ? round($pa * 100 / $tot) : 0;
$perP = $tot > 0 ? round($pp * 100 / $tot) : 0;
$perR = $tot > 0 ? round($pr * 100 / $tot) : 0;
?>
<style>
    :root{ --text:#e2e8f0; --muted:#94a3b8; }
    .stats-hero { position:relative; overflow:hidden; background: radial-gradient(1200px 500px at 10% 30%, rgba(124,58,237,0.12), transparent), linear-gradient(180deg, #0b1220 0%, #100028 100%); }
    .stats-hero:before { content:""; position:absolute; inset:0; background: repeating-linear-gradient(135deg, rgba(167,139,250,0.10) 0px, rgba(167,139,250,0.10) 2px, transparent 2px, transparent 8px); opacity:0.35; pointer-events:none; }
    .stats-hero .inner { padding:80px 0 40px; }
    .hero-frame { background: rgba(76,29,149,0.18); border: 1.5px solid rgba(167,139,250,0.35); border-radius: 18px; padding: 26px 28px; backdrop-filter: blur(3px); box-shadow: 0 10px 24px rgba(124,58,237,0.28); }
    .stats-hero h2 { color:#fff; font-size:40px; font-weight:800; margin-bottom:8px; }
    .stats-hero p { color:#c4b5fd; }
    .stats-hero .accent { width:80px; height:3px; background:#a78bfa; border-radius:3px; margin-top:12px; }
    .kpi-card { background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:14px; padding:16px; display:flex; align-items:center; gap:12px; }
    .kpi-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; }
    .kpi-info { display:flex; flex-direction:column; }
    .kpi-value { font-size:24px; font-weight:800; color:#fff; }
    .kpi-label { font-size:12px; color:var(--muted); }
    .about-card { background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:14px; padding:16px; }
    .bar { height:10px; background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; overflow:hidden; }
    .bar > div { height:100%; }
    .row-line { display:flex; align-items:center; gap:10px; }
    .row-name { flex:0 0 150px; color:#fff; }
    .row-val { width:50px; text-align:right; color:#fff; }
</style>
<section class="stats-hero">
    <div class="container inner">
        <div class="row">
            <div class="col-lg-12">
                <div class="hero-frame">
                    <h2>Statistiques</h2>
                    <p>Vue synthétique des histoires et catégories</p>
                    <div class="accent"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="spad">
    <div class="container">
        
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="about-card">
                    <h4 style="color:#fff; margin-bottom:8px;">Approuvées</h4>
                    <div class="bar"><div style="background:#10b981; width:<?php echo $perA; ?>%"></div></div>
                    <div class="kpi-label" style="margin-top:6px;"><?php echo $pa; ?> / <?php echo $tot; ?> (<?php echo $perA; ?>%)</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="about-card">
                    <h4 style="color:#fff; margin-bottom:8px;">En attente</h4>
                    <div class="bar"><div style="background:#3b82f6; width:<?php echo $perP; ?>%"></div></div>
                    <div class="kpi-label" style="margin-top:6px;"><?php echo $pp; ?> / <?php echo $tot; ?> (<?php echo $perP; ?>%)</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="about-card">
                    <h4 style="color:#fff; margin-bottom:8px;">Rejetées</h4>
                    <div class="bar"><div style="background:#ef4444; width:<?php echo $perR; ?>%"></div></div>
                    <div class="kpi-label" style="margin-top:6px;"><?php echo $pr; ?> / <?php echo $tot; ?> (<?php echo $perR; ?>%)</div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:16px;">
            <div class="col-lg-12">
                <div class="about-card">
                    <h4 style="color:#fff; margin-bottom:8px;">Taux de partage</h4>
                    <div class="bar"><div style="background:#fbbf24; width:<?php echo (int)($shares['rate'] ?? 0); ?>%"></div></div>
                    <div class="kpi-label" style="margin-top:6px;"><?php echo (int)($shares['storiesShared'] ?? 0); ?> / <?php echo (int)($shares['approved'] ?? 0); ?> (<?php echo (int)($shares['rate'] ?? 0); ?>%) — Total partages: <?php echo (int)($shares['totalShares'] ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:16px;">
            <div class="col-lg-12">
                <div class="about-card">
                    <h4 style="color:#fff; margin-bottom:8px;">Répartition par catégorie (en % du total)</h4>
                    <?php if (empty($cats ?? [])): ?><div style="color:var(--muted)">Aucune catégorie détectée.</div><?php endif; ?>
                    <?php foreach (($cats ?? []) as $c): ?>
                        <?php $pc = ($tot > 0) ? round(((int)$c['c']) * 100 / $tot) : 0; ?>
                        <div class="row-line"><span class="row-name"><?php echo htmlspecialchars($c['category'] ?? ''); ?></span><div class="bar" style="flex:1"><div style="background:#a78bfa; width:<?php echo $pc; ?>%"></div></div><span class="row-val"><?php echo $pc; ?>%</span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
