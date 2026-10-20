<?php
/**
 * BeautySpot professional categories and their specialty tag catalogs.
 * A professional can belong to more than one category (e.g. Makeup + Nails).
 */

function professional_categories(): array
{
    return [
        'hair'   => 'Hairstylist / Barber',
        'makeup' => 'Make-up Artist',
        'nails'  => 'Nail Technician',
    ];
}

function specialty_catalog(): array
{
    return [
        'hair' => [
            'Fades', 'Braids', 'Locs & Dreadlocks', 'Weaves & Extensions',
            'Colour & Balayage', 'Relaxers', 'Natural Hair Care', 'Bridal Hair',
            "Kids' Hair", "Men's Cuts", 'Afro Hair', 'Silk Press',
            'Wigs & Wig Installs', 'Cornrows', 'Hair Treatments',
        ],
        'makeup' => [
            'Bridal Makeup', 'Editorial & Fashion Makeup', 'Special FX Makeup',
            'Airbrush Makeup', 'Glam Makeup', 'Natural / "No-Makeup" Look',
            'Photoshoot Makeup', 'Theatrical & Stage Makeup', 'Prosthetic Makeup',
            'HD Makeup', 'Matric Dance & Prom Makeup', 'Drag Makeup',
            'Editorial Runway Makeup', 'Makeup Lessons',
        ],
        'nails' => [
            'Manicure', 'Pedicure', 'Gel Nails', 'Acrylic Nails', 'Dip Powder',
            'Nail Art', 'French Tips', 'Ombré Nails', 'Nail Extensions',
            'BIAB (Builder in a Bag)', 'Polygel', 'Nail Repair',
        ],
    ];
}

/**
 * Curated, professional-looking accent colors a pro can choose to personalize
 * their public profile page. Kept as a fixed palette (rather than a free color
 * picker) so every profile still looks considered and on-brand.
 */
function accent_color_presets(): array
{
    return [
        'teal'      => ['label' => 'Teal',    'base' => '#3D7A73', 'dark' => '#2A5751'],
        'gold'      => ['label' => 'Gold',     'base' => '#B08A3E', 'dark' => '#8C6C2F'],
        'wine'      => ['label' => 'Wine',     'base' => '#7A3B44', 'dark' => '#5E2C33'],
        'navy'      => ['label' => 'Navy',     'base' => '#2E4057', 'dark' => '#212E3F'],
        'charcoal'  => ['label' => 'Charcoal', 'base' => '#3A3A3A', 'dark' => '#242424'],
        'rose'      => ['label' => 'Rose',     'base' => '#A15C63', 'dark' => '#7D454B'],
    ];
}

function accent_color(?string $key): array
{
    $presets = accent_color_presets();
    return $presets[$key] ?? $presets['teal'];
}

/**
 * Turn a stored comma-separated category string ("hair,makeup") into a display list.
 */
function category_labels(?string $csv): array
{
    if (!$csv) return [];
    $labels = professional_categories();
    $codes = array_filter(array_map('trim', explode(',', $csv)));
    return array_values(array_intersect_key($labels, array_flip($codes)));
}
