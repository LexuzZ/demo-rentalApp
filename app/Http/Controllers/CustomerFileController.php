<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerFileController extends Controller
{
    public function downloadKtp(Customer $customer)
    {
        if (!$customer->identity_file) {
            abort(404, 'File KTP tidak tersedia.');
        }

        // Path sudah lengkap karena di DB ada "identity_docs/xxx.png"
        $path = $customer->identity_file;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, "File KTP tidak ditemukan: $path");
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'KTP-' . preg_replace('/\s+/', '_', $customer->nama) . '.' . $ext;

        return Storage::disk('public')->download($path, $filename);
    }

    public function downloadSim(Customer $customer)
    {
        if (!$customer->lisence_file) {
            abort(404, 'File SIM tidak tersedia.');
        }

        $path = $customer->lisence_file;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, "File SIM tidak ditemukan: $path");
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'SIM-' . preg_replace('/\s+/', '_', $customer->nama) . '.' . $ext;

        return Storage::disk('public')->download($path, $filename);
    }
}
