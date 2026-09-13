<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Tests\TestCase;

class RoleAndMenuAuthorizationTest extends TestCase
{
    public function test_sales_agent_cannot_access_executive_reports(): void
    {
        $sales = User::where('role', UserRole::SALES_AGENT)->first();

        $response = $this->actingAs($sales)->get(route('reports.sales'));

        $response->assertStatus(403);
    }

    public function test_sales_agent_cannot_access_user_management(): void
    {
        $sales = User::where('role', UserRole::SALES_AGENT)->first();

        $response = $this->actingAs($sales)->get(route('settings.users.index'));

        $response->assertStatus(403);
    }

    public function test_sales_agent_cannot_access_create_project(): void
    {
        $sales = User::where('role', UserRole::SALES_AGENT)->first();

        $response = $this->actingAs($sales)->get(route('inventory.projects.create'));

        $response->assertStatus(403);
    }

    public function test_sales_manager_can_access_executive_reports_and_users(): void
    {
        $manager = User::where('role', UserRole::SALES_MANAGER)->first();

        $reportResponse = $this->actingAs($manager)->get(route('reports.sales'));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('Laporan Eksekutif & Analitik');

        $usersResponse = $this->actingAs($manager)->get(route('settings.users.index'));
        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('Manajemen Tim Sales');
    }

    public function test_finance_can_access_payments_and_reports(): void
    {
        $finance = User::where('role', UserRole::FINANCE)->first();

        $paymentsResponse = $this->actingAs($finance)->get(route('finance.payments.index'));
        $paymentsResponse->assertStatus(200);

        $reportResponse = $this->actingAs($finance)->get(route('reports.sales'));
        $reportResponse->assertStatus(200);
    }

    public function test_property_admin_can_access_create_unit(): void
    {
        $admin = User::where('role', UserRole::ADMIN_PROPERTY)->first();

        $response = $this->actingAs($admin)->get(route('inventory.units.create'));

        $response->assertStatus(200);
    }

    public function test_sales_agent_menu_visibility(): void
    {
        $sales = User::where('role', UserRole::SALES_AGENT)->first();

        $response = $this->actingAs($sales)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Prospek Saya (Leads)');
        $response->assertSee('Pipeline Penjualan Saya');
        $response->assertSee('Jadwal Survei Lokasi');
        $response->assertSee('Data Konsumen Saya');
        $response->assertSee('Stok Unit Kavling');
        $response->assertSee('Pemesanan Unit (SPP)');
        $response->assertSee('Pengajuan Diskon &amp; Nego', false);
        $response->assertSee('Hak Komisi Saya');

        // Should NOT see financial management, executive reports, or system settings
        $response->assertDontSee('Pembayaran &amp; Kwitansi', false);
        $response->assertDontSee('KPR Management');
        $response->assertDontSee('Laporan Penjualan Eksekutif');
        $response->assertDontSee('User &amp; Hak Akses', false);
        $response->assertDontSee('Pengaturan Website');
    }

    public function test_finance_menu_visibility(): void
    {
        $finance = User::where('role', UserRole::FINANCE)->first();

        $response = $this->actingAs($finance)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Ikhtisar Keuangan');
        $response->assertSee('Verifikasi Pembayaran &amp; Kwitansi', false);
        $response->assertSee('KPR Management &amp; Akad', false);
        $response->assertSee('Pencairan Komisi Sales');
        $response->assertSee('Laporan Realisasi &amp; Arus Kas', false);
        $response->assertSee('Daftar Unit &amp; Pricelist', false);

        // Should NOT see lead pipeline, discount negotiations, or system settings
        $response->assertDontSee('Daftar Prospek (Leads)');
        $response->assertDontSee('Sales Pipeline Kanban');
        $response->assertDontSee('Pengajuan Diskon &amp; Nego', false);
        $response->assertDontSee('Approval Negosiasi &amp; Diskon', false);
        $response->assertDontSee('User &amp; Hak Akses', false);
        $response->assertDontSee('Pengaturan Website');
    }

    public function test_property_admin_menu_visibility(): void
    {
        $admin = User::where('role', UserRole::ADMIN_PROPERTY)->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Ikhtisar Inventori');
        $response->assertSee('Master Data & Inventori');
        $response->assertSee('Kelola Proyek Kawasan');
        $response->assertSee('Kelola Cluster &amp; Tipe', false);
        $response->assertSee('Kelola Unit &amp; Spesifikasi', false);
        $response->assertSee('Master Siteplan Interaktif');
        $response->assertSee('Monitoring Unit Terpesan');

        // Should NOT see CRM leads, payments, commissions, or system settings
        $response->assertDontSee('Daftar Prospek (Leads)');
        $response->assertDontSee('Sales Pipeline Kanban');
        $response->assertDontSee('Pembayaran &amp; Kwitansi', false);
        $response->assertDontSee('KPR Management');
        $response->assertDontSee('Pengajuan Diskon &amp; Nego', false);
        $response->assertDontSee('User &amp; Hak Akses', false);
        $response->assertDontSee('Pengaturan Website');
    }

    public function test_company_owner_menu_visibility(): void
    {
        $owner = User::where('role', UserRole::COMPANY_OWNER)->first();

        $response = $this->actingAs($owner)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Ikhtisar Eksekutif');
        $response->assertSee('Konfigurasi Sistem');
        $response->assertSee('User &amp; Hak Akses', false);
        $response->assertSee('Pengaturan Website');
        $response->assertSee('Laporan Penjualan Eksekutif');
        $response->assertSee('Approval Negosiasi &amp; Diskon', false);
    }

    public function test_sales_manager_menu_visibility(): void
    {
        $manager = User::where('role', UserRole::SALES_MANAGER)->first();

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Ikhtisar Sales Manager');
        $response->assertSee('CRM & Penjualan Tim');
        $response->assertSee('Approval Negosiasi &amp; Diskon', false);
        $response->assertSee('Manajemen Tim Sales');
        $response->assertSee('Laporan Kinerja &amp; Omset Tim', false);

        // Sales Manager should NOT see website settings (exclusive to owner/super admin)
        $response->assertDontSee('Pengaturan Website');
    }

    public function test_buttons_hidden_for_unauthorized_roles(): void
    {
        $sales = User::where('role', UserRole::SALES_AGENT)->first();
        $admin = User::where('role', UserRole::ADMIN_PROPERTY)->first();
        $finance = User::where('role', UserRole::FINANCE)->first();

        // 1. Sales should NOT see 'Tambah Proyek Baru' or 'Tambah Unit Baru'
        $projectsRespSales = $this->actingAs($sales)->get(route('inventory.projects.index'));
        $projectsRespSales->assertDontSee('Tambah Proyek Baru');

        $unitsRespSales = $this->actingAs($sales)->get(route('inventory.units.index'));
        $unitsRespSales->assertDontSee('Tambah Unit Baru');

        // 2. Admin Property CAN see 'Tambah Proyek Baru' and 'Tambah Unit Baru', but NOT 'Buat Booking Baru' or 'Tambah Lead Baru'
        $projectsRespAdmin = $this->actingAs($admin)->get(route('inventory.projects.index'));
        $projectsRespAdmin->assertSee('Tambah Proyek Baru');

        $unitsRespAdmin = $this->actingAs($admin)->get(route('inventory.units.index'));
        $unitsRespAdmin->assertSee('Tambah Unit Baru');

        $dashboardRespAdmin = $this->actingAs($admin)->get(route('dashboard'));
        $dashboardRespAdmin->assertDontSee('Tambah Lead Baru');

        $bookingsRespAdmin = $this->actingAs($admin)->get(route('sales.bookings.index'));
        $bookingsRespAdmin->assertDontSee('Buat Booking Baru');

        // 3. Finance should NOT see 'Buat Booking Baru' or 'Ajukan Negosiasi Baru'
        $bookingsRespFinance = $this->actingAs($finance)->get(route('sales.bookings.index'));
        $bookingsRespFinance->assertDontSee('Buat Booking Baru');

        $negotiationsRespFinance = $this->actingAs($finance)->get(route('sales.negotiations.index'));
        $negotiationsRespFinance->assertDontSee('Ajukan Negosiasi Baru');
    }
}

