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
     * XML Sitemap generator untuk SEO Google.
     */
    public function sitemap()
    {
        $units = Unit::where('is_published', true)->latest()->get();

        return response()->view('user.sitemap', compact('units'))
            ->header('Content-Type', 'text/xml');
    }
}
