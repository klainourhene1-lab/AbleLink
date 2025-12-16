<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques pédagogiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{ --bg:#0f172a; --card:#1e293b; --text:#e2e8f0; --muted:#94a3b8; --blue:#3b82f6; --green:#10b981; --red:#ef4444; --purple:#a78bfa; }
        body{ background: linear-gradient(135deg, var(--bg), #1e1b4b); color:var(--text); font-family:'Poppins',sans-serif; }
        .container{ max-width:1100px; margin:40px auto; padding:0 16px; }
        .grid{ display:grid; grid-template-columns: repeat(3,1fr); gap:14px; }
        .card{ background: rgba(30,41,59,0.7); border:1px solid rgba(255,255,255,0.08); border-radius:14px; padding:16px; }
        .kpi{ display:flex; align-items:center; gap:12px; }
        .kpi .dot{ width:12px; height:12px; border-radius:50%; }
        .title{ font-weight:700; margin-bottom:8px; }
        .muted{ color:var(--muted); font-size:12px; }
        .bar{ height:12px; background: rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; overflow:hidden; }
        .bar > div{ height:100%; }
        .bar-blue{ background: var(--blue); }
        .bar-green{ background: var(--green); }
        .bar-red{ background: var(--red); }
        .list{ display:flex; flex-direction:column; gap:10px; }
        .row{ display:flex; align-items:center; gap:10px; }
        .row .name{ flex:0 0 190px; }
        .row .val{ width:60px; text-align:right; }
        @media (max-width:900px){ .grid{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="margin-bottom:14px"><i class="fas fa-chart-bar"></i> Statistiques pédagogiques</h2>
        <div class="grid">
            <div class="card">
                <div class="title">État des histoires</div>
                <div class="list">
                    <?php $tot = (int)($status['total'] ?? (($status['approved']??0)+($status['pending']??0)+($status['rejected']??0))); $max = max(1,$tot); ?>
                    <div class="row"><span class="dot" style="background:var(--green)"></span><span class="name">Approuvées</span><div class="bar"><div class="bar-green" style="width:<?php echo round(($status['approved']??0)/$max*100); ?>%"></div></div><span class="val"><?php echo (int)($status['approved']??0); ?></span></div>
                    <div class="row"><span class="dot" style="background:var(--blue)"></span><span class="name">En attente</span><div class="bar"><div class="bar-blue" style="width:<?php echo round(($status['pending']??0)/$max*100); ?>%"></div></div><span class="val"><?php echo (int)($status['pending']??0); ?></span></div>
                    <div class="row"><span class="dot" style="background:var(--red)"></span><span class="name">Rejetées</span><div class="bar"><div class="bar-red" style="width:<?php echo round(($status['rejected']??0)/$max*100); ?>%"></div></div><span class="val"><?php echo (int)($status['rejected']??0); ?></span></div>
                    <div class="muted">Total: <?php echo $tot; ?></div>
                </div>
            </div>
            <div class="card">
                <div class="title">Répartition par catégorie</div>
                <div class="list">
                    <?php $maxCat = 0; foreach (($cats ?? []) as $c) { $maxCat = max($maxCat, (int)$c['c']); } $maxCat = max(1,$maxCat); ?>
                    <?php if (empty($cats ?? [])): ?><div class="muted">Aucune catégorie détectée.</div><?php endif; ?>
                    <?php foreach (($cats ?? []) as $c): ?>
                        <div class="row">
                            <span class="name"><?php echo htmlspecialchars($c['category'] ?? ''); ?></span>
                            <div class="bar"><div class="bar-blue" style="width:<?php echo round(((int)$c['c'])/$maxCat*100); ?>%"></div></div>
                            <span class="val"><?php echo (int)$c['c']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card">
                <div class="title">Progression mensuelle</div>
                <div class="list">
                    <?php 
                        $maxMonth = 0; $rows = [];
                        $len = max(count($monthsApproved ?? []), count($monthsPending ?? []), count($monthsRejected ?? []));
                        for ($i=0; $i<$len; $i++) {
                            $ym = $monthsApproved[$i]['ym'] ?? ($monthsPending[$i]['ym'] ?? ($monthsRejected[$i]['ym'] ?? ''));
                            $a = (int)($monthsApproved[$i]['c'] ?? 0); $p = (int)($monthsPending[$i]['c'] ?? 0); $r = (int)($monthsRejected[$i]['c'] ?? 0);
                            $maxMonth = max($maxMonth, $a, $p, $r);
                            $rows[] = ['ym'=>$ym,'a'=>$a,'p'=>$p,'r'=>$r];
                        }
                        $maxMonth = max(1,$maxMonth);
                    ?>
                    <?php if (empty($rows)): ?><div class="muted">Pas de données.</div><?php endif; ?>
                    <?php foreach ($rows as $row): ?>
                        <div class="row">
                            <span class="name"><?php echo htmlspecialchars($row['ym']); ?></span>
                            <div class="bar" style="flex:1">
                                <div class="bar-green" style="width:<?php echo round($row['a']/$maxMonth*100); ?>%; height:12px"></div>
                            </div>
                            <div class="bar" style="flex:1">
                                <div class="bar-blue" style="width:<?php echo round($row['p']/$maxMonth*100); ?>%; height:12px"></div>
                            </div>
                            <div class="bar" style="flex:1">
                                <div class="bar-red" style="width:<?php echo round($row['r']/$maxMonth*100); ?>%; height:12px"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="muted" style="margin-top:8px">Légende: vert=approuvées, bleu=en attente, rouge=rejetées</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
