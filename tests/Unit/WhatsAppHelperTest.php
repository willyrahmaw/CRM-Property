<?php

namespace Tests\Unit;

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
        $urlWithText = WhatsAppHelper::buildUrl('081234567890', 'Halo Bapak/Ibu');
        $this->assertEquals('https://wa.me/6281234567890?text=Halo%20Bapak%2FIbu', $urlWithText);

        $urlNoText = WhatsAppHelper::buildUrl('+6281234567890');
        $this->assertEquals('https://wa.me/6281234567890', $urlNoText);

        $urlNullPhone = WhatsAppHelper::buildUrl(null);
        $this->assertNull($urlNullPhone);
    }

    public function test_generates_lead_sales_templates(): void
    {
        $companyId = (string) Str::uuid();
        $company = new Company(['name' => 'Royal Property Group']);
        $company->id = $companyId;

        $sales = new User(['name' => 'Rian Sanjaya']);
        $sales->id = (string) Str::uuid();

        $project = new Project(['name' => 'Grand Harmony']);
        $project->id = (string) Str::uuid();
        $project->company_id = $companyId;

        $lead = new Lead([
            'name' => 'Budi Pratama',
            'phone' => '081298765432',
            'budget' => 1500000000,
        ]);
        $lead->id = (string) Str::uuid();
        $lead->company_id = $companyId;
        $lead->setRelation('company', $company);
        $lead->setRelation('assignedSales', $sales);
        $lead->setRelation('interestedProject', $project);

        $templates = WhatsAppHelper::getLeadTemplates($lead, $sales);

        $this->assertArrayHasKey('catalog_promo', $templates);
        $this->assertArrayHasKey('site_visit_invite', $templates);
        $this->assertArrayHasKey('kpr_simulation', $templates);
        $this->assertArrayHasKey('followup_warm', $templates);

        $this->assertStringContainsString('Budi Pratama', $templates['catalog_promo']['message']);
        $this->assertStringContainsString('Grand Harmony', $templates['catalog_promo']['message']);
        $this->assertStringContainsString('Rian Sanjaya', $templates['catalog_promo']['message']);
        $this->assertStringStartsWith('https://wa.me/6281298765432?text=', $templates['catalog_promo']['url']);

        // Model helper check
        $this->assertEquals('https://wa.me/6281298765432', $lead->getWhatsAppUrl(''));
        $this->assertStringStartsWith('https://wa.me/6281298765432?text=', $lead->getWhatsAppUrl());
        $this->assertCount(4, $lead->getWhatsAppTemplates($sales));
    }

    public function test_generates_customer_sales_templates(): void
    {
        $companyId = (string) Str::uuid();
        $company = new Company(['name' => 'Royal Property Group']);
        $company->id = $companyId;

        $sales = new User(['name' => 'Rian Sanjaya']);
        $sales->id = (string) Str::uuid();

        $customer = new Customer([
            'name' => 'Siti Nurhaliza',
            'phone' => '085712345678',
        ]);
        $customer->id = (string) Str::uuid();
        $customer->company_id = $companyId;
        $customer->setRelation('company', $company);

        $templates = WhatsAppHelper::getCustomerTemplates($customer, $sales);

        $this->assertArrayHasKey('booking_update', $templates);
        $this->assertArrayHasKey('document_reminder', $templates);
        $this->assertStringContainsString('Siti Nurhaliza', $templates['booking_update']['message']);
        $this->assertStringStartsWith('https://wa.me/6285712345678?text=', $templates['booking_update']['url']);

        // Model helper check
        $this->assertEquals('https://wa.me/6285712345678', $customer->getWhatsAppUrl(''));
        $this->assertStringStartsWith('https://wa.me/6285712345678?text=', $customer->getWhatsAppUrl());
        $this->assertCount(2, $customer->getWhatsAppTemplates($sales));
    }
}
