<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->isAdmin() : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unitId = $this->route('unit') ? (is_object($this->route('unit')) ? $this->route('unit')->id : $this->route('unit')) : null;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('units', 'code')->ignore($unitId)],
            'title' => ['required', 'string', 'max:255'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'type' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'land_area' => ['nullable', 'integer', 'min:0'],
            'building_area' => ['nullable', 'integer', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'certificate' => ['nullable', 'string', 'max:50'],
            'electricity' => ['nullable', 'string', 'max:50'],
            'specifications' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:Tersedia,Booking,Terjual'],
            'status_notes' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'valid_until' => ['nullable', 'date'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'primary_photo_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode unit wajib diisi.',
            'code.unique' => 'Kode unit sudah terdaftar.',
            'title.required' => 'Nama/Judul unit wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'city.required' => 'Kota/Kabupaten wajib diisi.',
            'type.required' => 'Tipe properti wajib dipilih.',
            'price.required' => 'Harga properti wajib diisi.',
            'price.numeric' => 'Harga properti harus berupa angka.',
            'status.required' => 'Status unit wajib dipilih.',
            'photos.*.image' => 'File galeri harus berupa gambar.',
            'photos.*.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'photos.*.max' => 'Ukuran setiap gambar maksimal 5 MB.',
        ];
    }
}
