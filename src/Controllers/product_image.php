<?php
// Динамически генерируемые SVG-плейсхолдеры для товаров
$id = $params[0] ?? 'default';

// Палитра для разных категорий товаров
$themes = [
    // ноутбуки
    'mbp14'      => ['#9ca3af','#374151','laptop'],
    'rog'        => ['#ff3b30','#1f0c0c','laptop'],
    'thinkpad'   => ['#1f2937','#000','laptop'],
    'matebook'   => ['#3b82f6','#1e3a8a','laptop'],
    'msi'        => ['#dc2626','#1c1917','laptop'],
    // телефоны
    'iphone'     => ['#f5e8d6','#a16207','phone'],
    'galaxy'     => ['#1e293b','#0f172a','phone'],
    'xiaomi'     => ['#ea580c','#9a3412','phone'],
    'pixel'      => ['#86efac','#166534','phone'],
    'oneplus'    => ['#16a34a','#14532d','phone'],
    // часы
    'aw_ultra'   => ['#fb923c','#7c2d12','watch'],
    'aw_s10'     => ['#94a3b8','#1e293b','watch'],
    'gw7'        => ['#3b82f6','#1e3a8a','watch'],
    'fenix'      => ['#22c55e','#14532d','watch'],
    'xwatch'     => ['#8b5cf6','#4c1d95','watch'],
    // мыши
    'mxmaster'   => ['#52525b','#18181b','mouse'],
    'deathadder' => ['#10b981','#022c22','mouse'],
    'gpro'       => ['#fff','#e5e7eb','mouse'],
    'magicmouse' => ['#fff','#d1d5db','mouse'],
    // ПК
    'pc_gaming'  => ['#7c3aed','#3b0764','tower'],
    'pc_pro'     => ['#06b6d4','#164e63','tower'],
    'macmini'    => ['#9ca3af','#4b5563','tower'],
    'pc_office'  => ['#64748b','#1e293b','tower'],
    // микрофоны
    'sm7b'       => ['#1c1917','#000','mic'],
    'rode'       => ['#fff','#9ca3af','mic'],
    'quadcast'   => ['#ec4899','#831843','mic'],
    // наушники
    'sony_xm5'   => ['#1c1917','#000','headphones'],
    'airpods'    => ['#fff','#e5e7eb','headphones'],
    'bose'       => ['#3f3f46','#18181b','headphones'],
    'momentum'   => ['#1e293b','#020617','headphones'],
    'marshall'   => ['#000','#1f2937','headphones'],
    // пылесосы
    'dyson'      => ['#9333ea','#4c1d95','vacuum'],
    'robot_xm'   => ['#fff','#9ca3af','robot'],
    'roomba'     => ['#374151','#000','robot'],
    'bespoke'    => ['#dbeafe','#3b82f6','vacuum'],
    // мониторы
    'lg_oled'    => ['#7c3aed','#1e1b4b','monitor'],
    'dell'       => ['#1f2937','#000','monitor'],
    'odyssey'    => ['#fbbf24','#7c2d12','monitor'],
    // клавиатуры
    'keychron'   => ['#52525b','#27272a','keyboard'],
    'mxkeys'     => ['#a3a3a3','#525252','keyboard'],
    'huntsman'   => ['#16a34a','#14532d','keyboard'],
    'magickb'    => ['#fff','#d4d4d8','keyboard'],
];

$theme = $themes[$id] ?? ['#a78bfa', '#5b21b6', 'box'];
[$primary, $secondary, $kind] = $theme;

header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=31536000');

$shapes = [
    'laptop' => '<rect x="60" y="120" width="280" height="160" rx="12" fill="' . $primary . '"/><rect x="80" y="140" width="240" height="120" rx="4" fill="#0a0a0c"/><rect x="40" y="280" width="320" height="14" rx="3" fill="' . $secondary . '"/><rect x="170" y="280" width="60" height="6" rx="2" fill="' . $primary . '" opacity="0.6"/>',
    'phone' => '<rect x="140" y="60" width="120" height="280" rx="22" fill="' . $primary . '"/><rect x="148" y="70" width="104" height="260" rx="16" fill="#0a0a0c"/><rect x="180" y="78" width="40" height="6" rx="3" fill="' . $secondary . '"/><circle cx="200" cy="318" r="3" fill="' . $secondary . '" opacity="0.5"/>',
    'watch' => '<rect x="160" y="80" width="80" height="40" rx="6" fill="' . $secondary . '"/><rect x="160" y="280" width="80" height="40" rx="6" fill="' . $secondary . '"/><rect x="130" y="120" width="140" height="160" rx="32" fill="' . $primary . '"/><rect x="146" y="140" width="108" height="120" rx="20" fill="#0a0a0c"/>',
    'mouse' => '<path d="M 130 130 Q 200 80 270 130 L 280 230 Q 280 290 200 290 Q 120 290 120 230 Z" fill="' . $primary . '"/><path d="M 200 130 L 200 200" stroke="' . $secondary . '" stroke-width="2"/><circle cx="200" cy="170" r="6" fill="' . $secondary . '"/>',
    'tower' => '<rect x="120" y="60" width="160" height="280" rx="12" fill="' . $primary . '"/><rect x="140" y="80" width="120" height="180" rx="6" fill="#0a0a0c"/><rect x="160" y="100" width="80" height="6" rx="3" fill="' . $secondary . '" opacity="0.7"/><rect x="160" y="115" width="60" height="4" rx="2" fill="' . $secondary . '" opacity="0.5"/><rect x="160" y="130" width="70" height="4" rx="2" fill="' . $secondary . '" opacity="0.5"/><circle cx="200" cy="290" r="14" fill="' . $secondary . '"/>',
    'mic' => '<rect x="180" y="60" width="40" height="120" rx="20" fill="' . $primary . '"/><rect x="190" y="180" width="20" height="80" fill="' . $secondary . '"/><rect x="150" y="260" width="100" height="14" rx="6" fill="' . $secondary . '"/><path d="M 150 130 Q 150 200 200 200 Q 250 200 250 130" stroke="' . $primary . '" stroke-width="4" fill="none"/>',
    'headphones' => '<path d="M 80 200 Q 80 100 200 100 Q 320 100 320 200" stroke="' . $primary . '" stroke-width="14" fill="none"/><rect x="60" y="190" width="60" height="100" rx="22" fill="' . $primary . '"/><rect x="280" y="190" width="60" height="100" rx="22" fill="' . $primary . '"/><rect x="76" y="210" width="28" height="60" rx="10" fill="' . $secondary . '"/><rect x="296" y="210" width="28" height="60" rx="10" fill="' . $secondary . '"/>',
    'vacuum' => '<rect x="80" y="200" width="180" height="120" rx="14" fill="' . $primary . '"/><circle cx="170" cy="260" r="10" fill="' . $secondary . '"/><rect x="270" y="60" width="20" height="200" rx="8" fill="' . $secondary . '"/><rect x="220" y="50" width="120" height="30" rx="14" fill="' . $primary . '"/>',
    'robot' => '<circle cx="200" cy="200" r="120" fill="' . $primary . '"/><circle cx="200" cy="200" r="90" fill="' . $secondary . '" opacity="0.3"/><circle cx="200" cy="200" r="20" fill="' . $secondary . '"/><circle cx="160" cy="160" r="6" fill="#fff" opacity="0.8"/>',
    'monitor' => '<rect x="50" y="80" width="300" height="180" rx="10" fill="' . $secondary . '"/><rect x="62" y="92" width="276" height="156" rx="4" fill="' . $primary . '"/><rect x="180" y="260" width="40" height="40" fill="' . $secondary . '"/><rect x="140" y="295" width="120" height="10" rx="4" fill="' . $secondary . '"/>',
    'keyboard' => '<rect x="50" y="140" width="300" height="120" rx="14" fill="' . $primary . '"/>' . implode('', array_map(function($i) use ($secondary) {
        $cols = ($i < 12) ? 12 : (($i < 24) ? 12 : 6);
        $row = ($i < 12) ? 0 : (($i < 24) ? 1 : 2);
        $col = $i % 12;
        $x = 64 + $col * 23;
        $y = 156 + $row * 28;
        return '<rect x="' . $x . '" y="' . $y . '" width="19" height="22" rx="4" fill="' . $secondary . '"/>';
    }, range(0, 29))),
    'box' => '<rect x="80" y="80" width="240" height="240" rx="20" fill="' . $primary . '"/><rect x="100" y="100" width="200" height="200" rx="10" fill="' . $secondary . '"/>',
];

$shape = $shapes[$kind] ?? $shapes['box'];

echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#f8fafc" stop-opacity="0"/>
      <stop offset="100%" stop-color="$primary" stop-opacity="0.08"/>
    </linearGradient>
  </defs>
  <rect width="400" height="400" fill="url(#bg)"/>
  $shape
</svg>
SVG;
