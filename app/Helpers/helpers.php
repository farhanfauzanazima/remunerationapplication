<?php

if (!function_exists('rupiah')) {
    function rupiah($amount): string
    {
        return 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
    }
}

if (!function_exists('roleLabel')) {
    function roleLabel(string $role): string
    {
        return match($role) {
            'owner' => 'Owner',
            'head'  => 'Kepala Toko',
            'admin' => 'Admin Toko',
            default => ucfirst($role),
        };
    }
}

if (!function_exists('statusBadge')) {
    function statusBadge(string $status): string
    {
        $map = [
            'draft'    => '<span class="badge-custom badge-draft">Draft</span>',
            'sent'     => '<span class="badge-custom badge-sent">Terkirim</span>',
            'open'     => '<span class="badge-custom badge-open">Open</span>',
            'closed'   => '<span class="badge-custom badge-closed">Closed</span>',
            'active'   => '<span class="badge-custom badge-active">Aktif</span>',
            'inactive' => '<span class="badge-custom badge-inactive">Nonaktif</span>',
        ];
        return $map[$status] ?? '<span class="badge-custom">' . ucfirst($status) . '</span>';
    }
}