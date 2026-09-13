<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;

class WhatsAppHelper
{
    /**
     * Sanitize phone number to international WhatsApp format (e.g. 6281234567890).
     */
    public static function sanitizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        // Strip non-digit characters
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // If starts with '08', change leading '0' to '62' (Indonesian mobile standard)
        if (str_starts_with($digits, '08')) {
            return '62' . substr($digits, 1);
        }

        // If starts with '0', change leading '0' to '62'
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        // If starts with '8' without leading zero, prepend '62'
        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    /**
     * Build WhatsApp direct link with optional prefilled message.
     */
    public static function buildUrl(?string $phone, string $message = ''): ?string
    {
        $sanitized = self::sanitizePhone($phone);

        if (! $sanitized) {
            return null;
        }

        if ($message !== '') {
            return 'https://wa.me/' . $sanitized . '?text=' . rawurlencode($message);
        }

        return 'https://wa.me/' . $sanitized;
    }

    /**
     * Generate ready-to-use property sales templates for a Lead.
     *
     * @return array<int, array{key: string, title: string, icon: string, description: string, message: string, url: string}>
     */
    public static function getLeadTemplates(Lead $lead, ?User $sales = null): array
    {
        $salesName = $sales?->name ?? $lead->assignedSales?->name ?? 'Tim Konsultan Properti';
        $companyName = $lead->company?->name ?? 'PROPFlow Developer';
        $projectName = $lead->interestedProject?->name ?? 'kawasan hunian kami';
        $customerName = trim($lead->name);

        $templates = [
            'catalog_promo' => [
                'key' => 'catalog_promo',
                'title' => 'Kirim E-Brosur & Promo',
                'icon' => 'fa-solid fa-file-pdf',
                'description' => 'Sapaan awal, pengenalan diri sales, dan pengiriman e-katalog properti',
                'message' => "Halo Bapak/Ibu {$customerName}, terima kasih atas ketertarikannya pada {$projectName}.\n\nPerkenalkan saya {$salesName} dari {$companyName}. Berikut kami kirimkan rangkuman spesifikasi unit dan penawaran promo terbaru. Apakah ada tipe kaveling atau rumah tertentu yang ingin Bapak/Ibu ketahui lebih lanjut?",
            ],
            'site_visit_invite' => [
                'key' => 'site_visit_invite',
                'title' => 'Undangan Survei Lokasi',
                'icon' => 'fa-solid fa-map-location-dot',
                'description' => 'Mengajak calon pembeli melihat langsung rumah contoh / kaveling siap bangun',
                'message' => "Halo Bapak/Ibu {$customerName}, bagaimana kabarnya hari ini?\n\nKami mengundang Bapak/Ibu untuk melihat langsung rumah contoh dan suasana kawasan di {$projectName} akhir pekan ini. Kami siap mendampingi kunjungan lokasi Bapak/Ibu.\n\nKira-kira hari dan jam berapa waktu luang yang paling nyaman bagi Bapak/Ibu?",
            ],
            'kpr_simulation' => [
                'key' => 'kpr_simulation',
                'title' => 'Simulasi Cicilan KPR & DP',
                'icon' => 'fa-solid fa-calculator',
                'description' => 'Menawarkan simulasi estimasi angsuran bank & promo subsidi DP',
                'message' => "Halo Bapak/Ibu {$customerName}, menindaklanjuti ketertarikan Bapak/Ibu pada {$projectName}.\n\nSaat ini sedang ada program spesial subsidi DP dan bunga KPR ringan dari bank rekanan. Apakah berkenan jika kami bantu buatkan estimasi simulasi angsuran per bulannya sesuai budget Bapak/Ibu?",
            ],
            'followup_warm' => [
                'key' => 'followup_warm',
                'title' => 'Follow-Up Progres Minat',
                'icon' => 'fa-solid fa-comments',
                'description' => 'Menanyakan kelanjutan rencana pembelian hunian dan kebutuhan info tambahan',
                'message' => "Halo Bapak/Ibu {$customerName}, izin menyapa kembali terkait rencana kepemilikan unit di {$projectName}.\n\nApakah ada informasi denah, spesifikasi bangunan, atau skema pembayaran yang ingin didiskusikan lebih lanjut dengan tim kami? Kami siap membantu.",
            ],
        ];

        foreach ($templates as $key => $tpl) {
            $templates[$key]['url'] = self::buildUrl($lead->phone, $tpl['message']) ?? '#';
        }

        return $templates;
    }

    /**
     * Generate ready-to-use property templates for an existing Customer.
     *
     * @return array<int, array{key: string, title: string, icon: string, description: string, message: string, url: string}>
     */
    public static function getCustomerTemplates(Customer $customer, ?User $sales = null): array
    {
        $salesName = $sales?->name ?? 'Tim Layanan Pelanggan';
        $companyName = $customer->company?->name ?? 'PROPFlow Developer';
        $customerName = trim($customer->name);

        $templates = [
            'booking_update' => [
                'key' => 'booking_update',
                'title' => 'Konfirmasi Pemesanan (Booking)',
                'icon' => 'fa-solid fa-receipt',
                'description' => 'Informasi berkas pemesanan unit dan tahapan administrasi berikutnya',
                'message' => "Halo Bapak/Ibu {$customerName}, terima kasih atas pemesanan unit properti di {$companyName}.\n\nSurat Pemesanan Properti (SPP) Anda sedang dalam pemrosesan administrasi. Saya {$salesName} siap mendampingi proses kelengkapan berkas selanjutnya. Silakan hubungi kami jika ada pertanyaan.",
            ],
            'document_reminder' => [
                'key' => 'document_reminder',
                'title' => 'Kelengkapan Berkas KPR',
                'icon' => 'fa-solid fa-folder-open',
                'description' => 'Pengingat berkas KTP, NPWP, Slip Gaji, dan rekening koran',
                'message' => "Halo Bapak/Ibu {$customerName}, salam hangat dari {$companyName}.\n\nUntuk percepatan pengajuan KPR ke bank, kami mengingatkan kelengkapan berkas (KTP, NPWP, KK, Slip Gaji 3 bulan terakhir). Berkas dapat dikirimkan secara digital kepada kami. Terima kasih.",
            ],
        ];

        foreach ($templates as $key => $tpl) {
            $templates[$key]['url'] = self::buildUrl($customer->phone, $tpl['message']) ?? '#';
        }

        return $templates;
    }
}
