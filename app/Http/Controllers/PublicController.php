<?php

namespace App\Http\Controllers;

use App\Models\CompanySection;
use App\Models\Faq;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    /**
     * Halaman Beranda (Home) terhubung penuh ke database CRM & CMS.
     */
    public function home(Request $request)
    {
        // 1. Ambil Pengaturan Web (Cached)
        $settings = SiteSetting::allGrouped();
        $whatsappNumber = $settings['whatsapp_business'] ?? '6281234567890';
        $hideSold = ($settings['hide_sold_units'] ?? '0') === '1';

        // 2. Ambil Section CMS Konten
        $hero = CompanySection::where('section_key', 'hero')->first();
        $about = CompanySection::where('section_key', 'about')->first();
        $visionMission = CompanySection::where('section_key', 'vision_mission')->first();
        $visionMissionData = $visionMission && $visionMission->content ? json_decode($visionMission->content, true) : null;

        $services = Service::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $faqs = Faq::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        // 3. Query Unit Properti dari Database CRM
        $unitsQuery = Unit::with(['photos', 'developer'])->where('is_published', true)->latest();

        // Terapkan pengaturan sembunyikan unit terjual
        if ($hideSold) {
            $unitsQuery->where('status', '!=', 'Terjual');
        }

        // Filter Lokasi
        if ($request->filled('location') && $request->location !== 'all') {
            $loc = $request->location;
            $unitsQuery->where(function ($q) use ($loc) {
                $q->where('city', 'like', "%{$loc}%")
                  ->orWhere('district', 'like', "%{$loc}%")
                  ->orWhere('province', 'like', "%{$loc}%");
            });
        }

        // Filter Tipe Properti
        if ($request->filled('type') && $request->type !== 'all') {
            $unitsQuery->where('type', $request->type);
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $unitsQuery->where('status', $request->status);
        }

        // Filter Rentang Harga
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $unitsQuery->where('price', '<=', (float) $request->max_price);
        }

        $units = $unitsQuery->paginate(6)->withQueryString();

        // Dropdown data lokasi & tipe
        $availableCities = Unit::where('is_published', true)->distinct()->pluck('city');
        $availableTypes = Unit::where('is_published', true)->distinct()->pluck('type');

        return view('user.home', compact(
            'settings',
            'whatsappNumber',
            'hero',
            'about',
            'visionMissionData',
            'services',
            'testimonials',
            'faqs',
            'units',
            'availableCities',
            'availableTypes'
        ));
    }

    /**
     * Halaman Daftar Properti Lengkap dengan Filter.
     */
    public function properties(Request $request)
    {
        $settings = SiteSetting::allGrouped();
        $whatsappNumber = $settings['whatsapp_business'] ?? '6281234567890';
        $hideSold = ($settings['hide_sold_units'] ?? '0') === '1';

        $query = Unit::with(['photos', 'developer'])->where('is_published', true)->latest();

        if ($hideSold) {
            $query->where('status', '!=', 'Terjual');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('district', 'like', "%{$s}%");
            });
        }

        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city', $request->city);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $units = $query->paginate(9)->withQueryString();
        $cities = Unit::where('is_published', true)->distinct()->pluck('city');
        $types = Unit::where('is_published', true)->distinct()->pluck('type');

        return view('user.properties.index', compact('units', 'cities', 'types', 'settings', 'whatsappNumber'));
    }

    /**
     * Halaman Detail Properti (Menggunakan Slug SEO).
     */
    public function propertyDetail(string $slug)
    {
        $unit = Unit::with(['photos', 'developer'])->where('slug', $slug)->firstOrFail();

        $settings = SiteSetting::allGrouped();
        $whatsappNumber = $settings['whatsapp_business'] ?? '6281234567890';

        // Template pesan WhatsApp dinamis
        $template = $settings['wa_template_interest'] ?? 'Halo Wastu Property, saya tertarik dengan unit {kode_unit} ({nama_unit}). Mohon info ketersediaan dan detailnya.';
        $waMessage = str_replace(['{kode_unit}', '{nama_unit}'], [$unit->code, $unit->title], $template);

        // Rekomendasi properti sejenis
        $relatedUnits = Unit::with(['photos', 'developer'])
            ->where('id', '!=', $unit->id)
            ->where('is_published', true)
            ->where('type', $unit->type)
            ->limit(3)
            ->get();

        return view('user.properties.show', compact('unit', 'settings', 'whatsappNumber', 'waMessage', 'relatedUnits'));
    }

    /**
     * Handle formulir minat dari pengunjung website publik.
     * Membuat lead baru, men-generate kode referensi (L-xxxx),
     * mengirim notifikasi ke admin, dan redirect ke WhatsApp Business.
     */
    public function submitInterest(Request $request, string $slug)
    {
        $unit = Unit::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        // Standarisasi nomor HP (62xxx)
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        // Generate Kode Referensi Unik: L-0001
        $count = Lead::withTrashed()->count() + 1;
        $referenceCode = 'L-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $lead = Lead::create([
            'reference_code' => $referenceCode,
            'name' => $validated['name'],
            'phone' => $cleanPhone,
            'unit_id' => $unit->id,
            'source' => 'website',
            'status' => 'Tertarik',
            'notes' => $validated['notes'] ? "Minat Website: " . $validated['notes'] : "Minat langsung dari formulir website.",
        ]);

        // Kirim notifikasi sistem ke Superadmin & Admin
        $admins = \App\Models\User::where('is_active', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewLeadNotification($lead));
        }

        // Teks WA yang rapi menyertakan Kode Referensi
        $settings = SiteSetting::allGrouped();
        $whatsappNumber = $settings['whatsapp_business'] ?? '6281234567890';

        $text = "Halo Wastu Property,\nSaya *{$lead->name}* (Ref: *{$lead->reference_code}*).\nSaya berminat dengan properti *{$unit->title}* ({$unit->code}).\n"
              . ($validated['notes'] ? "Catatan: {$validated['notes']}\n" : "")
              . "Mohon informasi jadwal survei lokasi dan simulasi pembayarannya. Terima kasih.";

        $waUrl = "https://wa.me/{$whatsappNumber}?text=" . rawurlencode($text);

        return redirect()->away($waUrl);
    }

    /**
     * XML Sitemap generator untuk SEO Google.
     */
    public function sitemap()
    {
        $units = Unit::where('is_published', true)->latest()->get();

        return response()->view('user.sitemap', compact('units'))
            ->header('Content-Type', 'text/xml');
    }
}
