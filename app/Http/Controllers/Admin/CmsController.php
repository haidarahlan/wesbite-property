<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySection;
use App\Models\Faq;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CmsController extends Controller
{
    /**
     * Dashboard CMS Company Profile.
     */
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        
        $hero = CompanySection::firstOrCreate(
            ['section_key' => 'hero'],
            [
                'title' => 'AGEN PROPERTI INDONESIA',
                'subtitle' => 'Melayani Dengan Hati',
                'content' => 'Agen Properti Profesional, yang saat ini telah menangani pemasaran unit-unit Properti di Jogja, Bantul, Magelang, Jakarta, Malang, Bali dan kota-kota lainnya.',
            ]
        );

        $about = CompanySection::firstOrCreate(
            ['section_key' => 'about'],
            [
                'title' => 'Mitra Akuisisi & Pemasaran Properti Terpercaya Anda',
                'subtitle' => 'TENTANG KAMI',
                'content' => 'PT. Wastu Pratama Wijaya (Wastu Property) adalah perusahaan agen perumahan dan pengembang properti profesional yang berbasis di Sleman, Daerah Istimewa Yogyakarta.',
            ]
        );

        $visionMission = CompanySection::firstOrCreate(
            ['section_key' => 'vision_mission'],
            [
                'title' => 'Visi & Misi Perusahaan',
                'content' => json_encode([
                    'vision' => 'Menjadi agen dan pengembang properti terdepan di Indonesia yang berintegritas dan melayani dengan hati.',
                    'mission' => 'Memberikan pendampingan transaksi properti yang transparan, aman, dan berdaya guna bagi konsumen serta developer mitra.'
                ]),
            ]
        );

        $services = Service::orderBy('sort_order', 'asc')->get();
        $testimonials = Testimonial::orderBy('sort_order', 'asc')->get();
        $faqs = Faq::orderBy('sort_order', 'asc')->get();
        $teams = TeamMember::orderBy('sort_order', 'asc')->get();

        return view('admin.cms.index', compact(
            'settings',
            'hero',
            'about',
            'visionMission',
            'services',
            'testimonials',
            'faqs',
            'teams'
        ));
    }

    /**
     * Simpan Pengaturan Umum (Site Settings: WA Bisnis, Hide Unit Terjual, Medsos, dll).
     */
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Checkbox hide sold units
        $data['hide_sold_units'] = $request->has('hide_sold_units') ? '1' : '0';

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        Cache::flush(); // Invalidate cache

        return back()->with('success', 'Pengaturan website dan WhatsApp bisnis berhasil diperbarui!');
    }

    /**
     * Update Konten Section (Hero, Tentang Kami, Visi Misi).
     */
    public function updateSection(Request $request, string $sectionKey)
    {
        $section = CompanySection::where('section_key', $sectionKey)->firstOrFail();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'image' => 'nullable|image|max:3072',
        ]);

        if ($sectionKey === 'vision_mission') {
            $section->content = json_encode([
                'vision' => $request->input('vision'),
                'mission' => $request->input('mission')
            ]);
        } else {
            $section->title = $validated['title'] ?? $section->title;
            $section->subtitle = $validated['subtitle'] ?? $section->subtitle;
            $section->content = $validated['content'] ?? $section->content;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cms', 'public');
            $section->image_path = $path;
        }

        $section->save();
        Cache::flush();

        return back()->with('success', "Konten {$sectionKey} berhasil diperbarui!");
    }

    /**
     * Layanan (Store & Delete).
     */
    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string',
        ]);

        Service::create($validated);
        Cache::flush();

        return back()->with('success', 'Layanan baru berhasil ditambahkan!');
    }

    public function deleteService(Service $service)
    {
        $service->delete();
        Cache::flush();
        return back()->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Testimoni (Store & Delete).
     */
    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_role' => 'nullable|string|max:255',
            'testimony' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Testimonial::create($validated);
        Cache::flush();

        return back()->with('success', 'Testimoni berhasil ditambahkan!');
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        $testimonial->delete();
        Cache::flush();
        return back()->with('success', 'Testimoni berhasil dihapus.');
    }

    /**
     * FAQ (Store & Delete).
     */
    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string',
        ]);

        Faq::create($validated);
        Cache::flush();

        return back()->with('success', 'FAQ berhasil ditambahkan!');
    }

    public function deleteFaq(Faq $faq)
    {
        $faq->delete();
        Cache::flush();
        return back()->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Tim (Store & Delete).
     */
    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string',
        ]);

        TeamMember::create($validated);
        Cache::flush();

        return back()->with('success', 'Anggota tim berhasil ditambahkan!');
    }

    public function deleteTeam(TeamMember $teamMember)
    {
        $teamMember->delete();
        Cache::flush();
        return back()->with('success', 'Anggota tim berhasil dihapus.');
    }
}
