<?php

namespace App\Services;

use App\Models\FiscalYear;
use App\Models\StatementOfStatutoryObligation;
use App\Repositories\StatementOfStatutoryObligationRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StatementOfStatutoryObligationService
{
    public function __construct(
        protected StatementOfStatutoryObligationRepository $repository
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

    public function create(array $data): StatementOfStatutoryObligation
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

    public function update(StatementOfStatutoryObligation $record, array $data): StatementOfStatutoryObligation
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

    public function delete(StatementOfStatutoryObligation $record): bool
    {
        return $this->repository->delete($record);
    }

    public function loadFull(StatementOfStatutoryObligation $record): StatementOfStatutoryObligation
    {
        return $this->repository->loadFull($record);
    }

    public function generateSinglePdf(StatementOfStatutoryObligation $record)
    {
        $record = $this->repository->loadFull($record);

        return PdfService::make()
            ->configure(['paper' => 'a4', 'orientation' => 'portrait', 'font_size' => 11])
            ->template('pdf.statement-of-statutory-obligation', ['record' => $record])
            ->inline('statement-of-statutory-obligation-' . $record->id . '.pdf');
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
            ->template('pdf.statement-of-statutory-obligation-by-fiscal-year', [
                'fiscalYear' => $fiscalYear,
                'records' => $records,
            ])
            ->inline('statement-of-statutory-obligation-fy-' . $fiscalYear->year . '.pdf');
    }

    private function syncChildren(StatementOfStatutoryObligation $record, array $categories, array $items): void
    {
        $categoryMap = [];

        foreach ($categories as $index => $category) {
            $categoryMap[$index] = $record->categories()->create([
                'category_number' => $category['category_number'],
                'category_name' => $category['category_name'],
                'sort_order' => $index,
            ]);
        }

        foreach ($items as $index => $item) {
            $ref = (int) ($item['category_ref'] ?? -1);
            if (!isset($categoryMap[$ref])) {
                continue;
            }

            $categoryMap[$ref]->items()->create([
                'code' => $item['code'] ?? null,
                'description' => $item['description'],
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
