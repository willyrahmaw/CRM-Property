<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case COMPANY_OWNER = 'company_owner';
    case SALES_MANAGER = 'sales_manager';
    case TEAM_LEADER = 'team_leader';
    case SALES_AGENT = 'sales_agent';
    case FINANCE = 'finance';
    case ADMIN_PROPERTY = 'admin_property';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::COMPANY_OWNER => 'Company Owner',
            self::SALES_MANAGER => 'Sales Manager',
            self::TEAM_LEADER => 'Team Leader',
            self::SALES_AGENT => 'Sales / Agent',
            self::FINANCE => 'Finance',
            self::ADMIN_PROPERTY => 'Admin Property',
        };
    }

    public function isManagerial(): bool
    {
        return in_array($this, [self::SUPER_ADMIN, self::COMPANY_OWNER, self::SALES_MANAGER]);
    }

    public function isSales(): bool
    {
        return in_array($this, [self::SALES_AGENT, self::TEAM_LEADER]);
    }
}
