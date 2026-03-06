<?php

namespace App\Services;

use App\Models\FiscalYear;
use App\Models\StatementOfFundingSource;
use App\Repositories\StatementOfFundingSourceRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StatementOfFundingSourceService
{
    public function __construct(
        protected StatementOfFundingSourceRepository $repository
    ) {}

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getFiscalYears()
    {
        return FiscalYear::orderBy('year', 'desc')->get();
    }

    public function getCurrentFiscalYear(): ?FiscalYear
    {
        return FiscalYear::where('is_current', true)->first();
    }

    public function create(array $data): StatementOfFundingSource
    {
        return DB::transaction(function () use ($data) {
            $categories = array_values($data['categories'] ?? []);
            $items = array_values($data['items'] ?? []);
            unset($data['categories'], $data['items']);

            $record = $this->repository->create($data);
            $this->syncChildren($record, $categories, $items);

            return $this->repository->loadFull($record);
        });
    }

    public function update(StatementOfFundingSource $record, array $data): StatementOfFundingSource
    {
        return DB::transaction(function () use ($record, $data) {
            $categories = array_values($data['categories'] ?? []);
            $items = array_values($data['items'] ?? []);
            unset($data['categories'], $data['items']);

            $this->repository->update($record, $data);
            $record->categories()->delete();
            $this->syncChildren($record, $categories, $items);

            return $this->repository->loadFull($record->fresh());
        });
    }

    public function delete(StatementOfFundingSource $record): bool
    {
        return $this->repository->delete($record);
    }

    public function loadFull(StatementOfFundingSource $record): StatementOfFundingSource
    {
        return $this->repository->loadFull($record);
    }

    public function generateSinglePdf(StatementOfFundingSource $record)
    {
        $record = $this->repository->loadFull($record);

        return PdfService::make()
            ->configure([
                'paper' => 'a4',
                'orientation' => 'portrait',
                'font_size' => 11,
            ])
            ->template('pdf.statement-of-funding-source', [
                'record' => $record,
            ])
            ->inline('statement-of-funding-source-' . $record->id . '.pdf');
    }

    public function generateFiscalYearPdf(FiscalYear $fiscalYear)
    {
        $records = $this->repository->getByFiscalYear($fiscalYear->id);

        return PdfService::make()
            ->configure([
                'paper' => 'a4',
                'orientation' => 'landscape',
                'font_size' => 10,
                'margin' => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10],
            ])
            ->template('pdf.statement-of-funding-source-by-fiscal-year', [
                'fiscalYear' => $fiscalYear,
                'records' => $records,
            ])
            ->inline('statement-of-funding-source-fy-' . $fiscalYear->year . '.pdf');
    }

    private function syncChildren(StatementOfFundingSource $record, array $categories, array $items): void
    {
        $categoryMap = [];

        foreach ($categories as $index => $category) {
            $categoryMap[$index] = $record->categories()->create([
                'category_number' => $category['category_number'],
                'category_name' => $category['category_name'],
                'amount' => $this->nullableNumber($category['amount'] ?? null),
                'sort_order' => $index,
            ]);
        }

        foreach ($items as $index => $item) {
            $ref = (int) ($item['category_ref'] ?? -1);
            if (!isset($categoryMap[$ref])) {
                continue;
            }

            $categoryMap[$ref]->items()->create([
                'particulars' => $item['particulars'],
                'account_classification' => $item['account_classification'] ?? null,
                'amount' => $this->nullableNumber($item['amount'] ?? null),
                'sort_order' => $index,
            ]);
        }
    }

    private function nullableNumber($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }
}
