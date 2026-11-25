<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Validator;

class ReportPeriod
{
    public const TIMEZONE = 'Africa/Cairo';

    public function __construct(
        public readonly CarbonImmutable $from,
        public readonly CarbonImmutable $through,
    ) {}

    public static function fromFilters(array $filters): self
    {
        $today = CarbonImmutable::today(self::TIMEZONE);
        $preset = (int) ($filters['days'] ?? 30);
        $days = in_array($preset, [7, 30, 90], true) ? $preset : 30;

        $validator = Validator::make($filters, [
            'from' => ['required_with:to', 'date_format:Y-m-d'],
            'to' => ['required_with:from', 'date_format:Y-m-d', 'after_or_equal:from', 'before_or_equal:'.$today->toDateString()],
        ]);

        if ($validator->passes() && ! empty($filters['from']) && ! empty($filters['to'])) {
            $from = CarbonImmutable::parse($filters['from'], self::TIMEZONE);
            $through = CarbonImmutable::parse($filters['to'], self::TIMEZONE);

            if ($from->diffInDays($through) < 366) {
                return new self($from, $through);
            }
        }

        return new self($today->subDays($days - 1), $today);
    }

    public function previous(): self
    {
        $days = (int) $this->from->diffInDays($this->through) + 1;

        return new self($this->from->subDays($days), $this->from->subDay());
    }

    public function utcStart(): CarbonImmutable
    {
        return $this->from->utc();
    }

    public function utcEnd(): CarbonImmutable
    {
        return $this->through->addDay()->utc();
    }

    public function query(): array
    {
        return ['from' => $this->from->toDateString(), 'to' => $this->through->toDateString()];
    }

    /** @return list<self> */
    public function chartBuckets(): array
    {
        $days = (int) $this->from->diffInDays($this->through) + 1;
        $step = $days > 31 ? 7 : 1;
        $buckets = [];

        for ($start = $this->from; $start <= $this->through; $start = $start->addDays($step)) {
            $buckets[] = new self($start, $start->addDays($step - 1)->min($this->through));
        }

        return $buckets;
    }
}
