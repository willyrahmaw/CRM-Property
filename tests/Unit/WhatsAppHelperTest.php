<?php

namespace Tests\Unit;

use App\Enums\Gender;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use App\Support\WhatsAppHelper;
use Illuminate\Support\Str;
use Tests\TestCase;

class WhatsAppHelperTest extends TestCase
{
    public function test_sanitizes_various_phone_number_formats(): void
    {
        // Leading 0
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone('081234567890'));
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone('0812-3456-7890'));
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone(' 0812 3456 7890 '));

        // Leading +62
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone('+6281234567890'));

        // Already 62
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone('6281234567890'));

        // Leading 8 (missing 0 or 62)
        $this->assertEquals('6281234567890', WhatsAppHelper::sanitizePhone('81234567890'));

        // Null and empty strings
        $this->assertNull(WhatsAppHelper::sanitizePhone(null));
        $this->assertNull(WhatsAppHelper::sanitizePhone(''));
        $this->assertNull(WhatsAppHelper::sanitizePhone('   '));
        $this->assertNull(WhatsAppHelper::sanitizePhone('abc-def'));
    }

    public function test_builds_wa_me_url_correctly(): void
    {
        $urlWithText = WhatsAppHelper::buildUrl('081234567890', 'Halo Bapak Budi');
        $this->assertEquals('https://wa.me/6281234567890?text=Halo%20Bapak%20Budi', $urlWithText);

        $urlNoText = WhatsAppHelper::buildUrl('+6281234567890');
        $this->assertEquals('https://wa.me/6281234567890', $urlNoText);

        $urlNullPhone = WhatsAppHelper::buildUrl(null);
        $this->assertNull($urlNullPhone);
    }

    public function test_generates_lead_sales_templates_with_gender_salutation(): void
    {
        $companyId = (string) Str::uuid();
        $company = new Company(['name' => 'Royal Property Group']);
        $company->id = $companyId;

        $sales = new User(['name' => 'Rian Sanjaya']);
        $sales->id = (string) Str::uuid();

        $project = new Project(['name' => 'Grand Harmony']);
        $project->id = (string) Str::uuid();
        $project->company_id = $companyId;

        // 1. Male Lead -> "Bapak Budi Pratama"
        $maleLead = new Lead([
            'name' => 'Budi Pratama',
            'gender' => Gender::MALE,
            'phone' => '081298765432',
            'budget' => 1500000000,
        ]);
        $maleLead->id = (string) Str::uuid();
        $maleLead->company_id = $companyId;
        $maleLead->setRelation('company', $company);
        $maleLead->setRelation('assignedSales', $sales);
        $maleLead->setRelation('interestedProject', $project);

        $maleTemplates = WhatsAppHelper::getLeadTemplates($maleLead, $sales);

        $this->assertEquals('Bapak', $maleLead->salutation);
        $this->assertStringContainsString('Halo Bapak Budi Pratama', $maleTemplates['catalog_promo']['message']);
        $this->assertStringContainsString('Grand Harmony', $maleTemplates['catalog_promo']['message']);
        $this->assertStringContainsString('Rian Sanjaya', $maleTemplates['catalog_promo']['message']);
        $this->assertStringContainsString('Halo%20Bapak%20Budi%20Pratama', $maleLead->getWhatsAppUrl());

        // 2. Female Lead -> "Ibu Siti Maryam"
        $femaleLead = new Lead([
            'name' => 'Siti Maryam',
            'gender' => Gender::FEMALE,
            'phone' => '081311223344',
        ]);
        $femaleLead->id = (string) Str::uuid();
        $femaleLead->company_id = $companyId;
        $femaleLead->setRelation('company', $company);
        $femaleLead->setRelation('assignedSales', $sales);
        $femaleLead->setRelation('interestedProject', $project);

        $femaleTemplates = WhatsAppHelper::getLeadTemplates($femaleLead, $sales);

        $this->assertEquals('Ibu', $femaleLead->salutation);
        $this->assertStringContainsString('Halo Ibu Siti Maryam', $femaleTemplates['catalog_promo']['message']);
        $this->assertStringContainsString('Halo%20Ibu%20Siti%20Maryam', $femaleLead->getWhatsAppUrl());

        // 3. Unspecified Lead -> "Bapak/Ibu"
        $neutralLead = new Lead([
            'name' => 'Alex Santoso',
            'gender' => null,
            'phone' => '081599887766',
        ]);
        $this->assertEquals('Bapak/Ibu', $neutralLead->salutation);
        $this->assertStringContainsString('Halo%20Bapak%2FIbu%20Alex%20Santoso', $neutralLead->getWhatsAppUrl());
    }

    public function test_generates_customer_sales_templates_with_gender_salutation(): void
    {
        $companyId = (string) Str::uuid();
        $company = new Company(['name' => 'Royal Property Group']);
        $company->id = $companyId;

        $sales = new User(['name' => 'Rian Sanjaya']);
        $sales->id = (string) Str::uuid();

        // Female customer
        $customer = new Customer([
            'name' => 'Siti Nurhaliza',
            'gender' => Gender::FEMALE,
            'phone' => '085712345678',
        ]);
        $customer->id = (string) Str::uuid();
        $customer->company_id = $companyId;
        $customer->setRelation('company', $company);

        $templates = WhatsAppHelper::getCustomerTemplates($customer, $sales);

        $this->assertEquals('Ibu', $customer->salutation);
        $this->assertArrayHasKey('booking_update', $templates);
        $this->assertArrayHasKey('document_reminder', $templates);
        $this->assertStringContainsString('Halo Ibu Siti Nurhaliza', $templates['booking_update']['message']);
        $this->assertStringStartsWith('https://wa.me/6285712345678?text=', $templates['booking_update']['url']);
        $this->assertStringContainsString('Halo%20Ibu%20Siti%20Nurhaliza', $customer->getWhatsAppUrl());

        // Male customer
        $maleCustomer = new Customer([
            'name' => 'Ahmad Dahlan',
            'gender' => Gender::MALE,
            'phone' => '081233445566',
        ]);
        $this->assertEquals('Bapak', $maleCustomer->salutation);
        $this->assertStringContainsString('Halo%20Bapak%20Ahmad%20Dahlan', $maleCustomer->getWhatsAppUrl());
    }
}
