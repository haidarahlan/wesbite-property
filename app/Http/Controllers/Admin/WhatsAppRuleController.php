<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqTemplate;
use App\Models\MessageFilterRule;
use Illuminate\Http\Request;

class WhatsAppRuleController extends Controller
{
    /**
     * Tampilkan halaman kelola aturan kata kunci & template auto-reply FAQ.
     */
    public function index()
    {
        $rules = MessageFilterRule::latest()->get();
        $faqTemplates = FaqTemplate::latest()->get();

        return view('admin.whatsapp.rules', compact('rules', 'faqTemplates'));
    }

    /**
     * Tambah aturan kata kunci baru (Tertarik / Tidak Tertarik).
     */
    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:100', 'unique:message_filter_rules,keyword'],
            'classification' => ['required', 'in:Tertarik,Tidak Tertarik'],
            'weight' => ['required', 'integer', 'min:1', 'max:10'],
        ], [
            'keyword.required' => 'Kata kunci wajib diisi.',
            'keyword.unique' => 'Kata kunci ini sudah terdaftar.',
            'classification.required' => 'Klasifikasi wajib dipilih.',
        ]);

        $validated['keyword'] = strtolower(trim($validated['keyword']));
        $validated['is_active'] = true;

        MessageFilterRule::create($validated);

        return back()->with('success', "Aturan kata kunci '{$validated['keyword']}' berhasil ditambahkan!");
    }

    /**
     * Hapus aturan kata kunci.
     */
    public function destroyRule(MessageFilterRule $rule)
    {
        $rule->delete();
        return back()->with('success', "Aturan kata kunci '{$rule->keyword}' berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif aturan kata kunci.
     */
    public function toggleRule(MessageFilterRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);
        $status = $rule->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Aturan '{$rule->keyword}' berhasil {$status}.");
    }

    /**
     * Simpan / Perbarui template balasan FAQ otomatis.
     */
    public function storeFaqTemplate(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'template_message' => ['required', 'string'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['is_default'])) {
            FaqTemplate::query()->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        FaqTemplate::updateOrCreate(
            ['code' => $validated['code']],
            $validated
        );

        return back()->with('success', "Template auto-reply FAQ '{$validated['title']}' berhasil disimpan!");
    }

    /**
     * Hapus template balasan FAQ.
     */
    public function destroyFaqTemplate(FaqTemplate $template)
    {
        $template->delete();
        return back()->with('success', "Template '{$template->title}' berhasil dihapus.");
    }
}
