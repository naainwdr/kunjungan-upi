<?php
$k = \App\Models\Kunjungan::where('nomor_registrasi', 'UPI-20260723-0034')->first();
echo "STATUS: " . ($k ? $k->status : 'NOT FOUND');
