<?php

namespace App\Filament\Pages;

use App\Support\CampaignAttribution;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

class MarketingStudio extends Page
{
    protected static ?string $slug = 'marketing';

    protected static ?string $title = 'أدوات التسويق';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected string $view = 'filament.pages.marketing-studio';

    public array $link = ['destination' => '/', 'utm_source' => '', 'utm_medium' => 'paid_social', 'utm_campaign' => ''];

    public array $budget = ['value' => '', 'cost' => '', 'spend' => '', 'orders' => ''];

    #[Locked]
    public string $campaignUrl = '';

    #[Locked]
    public ?array $budgetResult = null;

    public function getHeading(): ?string
    {
        return null;
    }

    public function buildLink(): void
    {
        $this->campaignUrl = '';
        $this->resetValidation(['link.destination', 'link.utm_source', 'link.utm_medium', 'link.utm_campaign']);
        $rules = ['link.destination' => ['required', 'string', 'max:1000']];
        foreach (CampaignAttribution::FIELDS as $field) {
            $rules['link.'.$field] = ['required', 'string', 'regex:'.CampaignAttribution::PATTERN];
        }

        $data = Validator::make(['link' => $this->link], $rules, [
            'required' => 'الحقل مطلوب.',
            'regex' => 'ابدأ بحرف أو رقم. استخدم حتى 100 حرف أو رقم أو مسافة أو نقطة أو شرطة.',
        ])->validate()['link'];

        $destination = parse_url($data['destination']);
        $path = $destination['path'] ?? '';
        if ($destination === false || array_intersect(['scheme', 'host', 'port', 'user', 'pass'], array_keys($destination))
            || ! str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\')
            || preg_match('/[\x00-\x20]/', $path)) {
            throw ValidationException::withMessages(['link.destination' => 'اكتب مساراً داخل المتجر يبدأ بـ /، مثل /products/alpha.']);
        }

        parse_str($destination['query'] ?? '', $query);
        $query = array_replace($query, CampaignAttribution::fieldsFrom($data));
        $fragment = isset($destination['fragment']) ? '#'.$destination['fragment'] : '';
        $this->campaignUrl = rtrim(config('app.frontend_url'), '/').$path.'?'.http_build_query($query, encoding_type: PHP_QUERY_RFC3986).$fragment;
    }

    public function calculateBudget(): void
    {
        $this->budgetResult = null;
        $this->resetValidation(['budget.value', 'budget.cost', 'budget.spend', 'budget.orders']);
        $data = Validator::make(['budget' => $this->budget], [
            'budget.value' => ['required', 'numeric', 'decimal:0,2', 'gt:0', 'max:100000000'],
            'budget.cost' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:100000000'],
            'budget.spend' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:100000000'],
            'budget.orders' => ['required', 'integer', 'min:1', 'max:1000000'],
        ], [
            'required' => 'الحقل مطلوب.',
            'numeric' => 'أدخل مبلغاً صحيحاً.',
            'decimal' => 'استخدم رقمين بعد العلامة العشرية بحد أقصى.',
            'integer' => 'أدخل عدداً صحيحاً.',
            'gt' => 'أدخل قيمة أكبر من صفر.',
            'min' => 'القيمة أقل من الحد المسموح.',
            'max' => 'القيمة أكبر من الحد المسموح.',
        ])->validate()['budget'];

        $value = (int) round($data['value'] * 100);
        $cost = (int) round($data['cost'] * 100);
        $spend = (int) round($data['spend'] * 100);
        $contribution = $value - $cost;
        $this->budgetResult = [
            'contribution' => $contribution / 100,
            'planned_cpa' => $spend / $data['orders'] / 100,
            'break_even_roas' => $contribution > 0 ? $value / $contribution : null,
            'break_even_orders' => $contribution > 0 ? intdiv($spend + $contribution - 1, $contribution) : null,
            'profit' => ($contribution * $data['orders'] - $spend) / 100,
        ];
    }
}
