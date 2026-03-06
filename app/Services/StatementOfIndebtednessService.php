<?php

namespace App\Services;

use App\Models\FiscalYear;
use App\Models\StatementOfIndebtedness;
use App\Repositories\StatementOfIndebtednessRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class StatementOfIndebtednessService
{
    public function __construct(
        protected StatementOfIndebtednessRepository $repository
    ) {}

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function create(array $data): StatementOfIndebtedness
    {
        return $this->repository->create($this->normalizePayload($data));
    }

    public function update(StatementOfIndebtedness $record, array $data): StatementOfIndebtedness
    {
        $this->repository->update($record, $this->normalizePayload($data));
        return $record->fresh(['fiscalYear']);
    }

    public function delete(StatementOfIndebtedness $record): bool
    {
        return $this->repository->delete($record);
    }

    public function generateSinglePdf(StatementOfIndebtedness $record)
    {
        $record->load('fiscalYear');

        return PdfService::make()
            ->configure([
                'paper' => 'a4',
                'orientation' => 'portrait',
                'font_size' => 11,
                'margin' => [
                    'top' => 12,
                    'right' => 12,
                    'bottom' => 12,
                    'left' => 12,
                ],
            ])
            ->template('pdf.statement-of-indebtedness', [
                'record' => $record,
                'isPdf' => true,
            ])
            ->inline('statement-of-indebtedness-' . $record->id . '.pdf');
    }

    public function generateFiscalYearPdf(FiscalYear $fiscalYear)
    {
        $records = $this->repository->getByFiscalYear($fiscalYear->id);

        return PdfService::make()
            ->configure([
                'paper' => 'a4',
                'orientation' => 'landscape',
                'font_size' => 10,
                'margin' => [
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                ],
            ])
            ->template('pdf.statement-of-indebtedness-by-fiscal-year', [
                'fiscalYear' => $fiscalYear,
                'records' => $records,
                'isPdf' => true,
            ])
            ->inline('statement-of-indebtedness-fy-' . $fiscalYear->year . '.pdf');
    }

    private function normalizePayload(array $data): array
    {
        $prevPrincipal = (float) ($data['prev_principal'] ?? 0);
        $prevInterest = (float) ($data['prev_interest'] ?? 0);
        $duePrincipal = (float) ($data['due_principal'] ?? 0);
        $dueInterest = (float) ($data['due_interest'] ?? 0);
        $principalAmount = (float) $data['principal_amount'];

        $data['prev_principal'] = $prevPrincipal;
        $data['prev_interest'] = $prevInterest;
        $data['due_principal'] = $duePrincipal;
        $data['due_interest'] = $dueInterest;
        $data['prev_total'] = round($prevPrincipal + $prevInterest, 2);
        $data['due_total'] = round($duePrincipal + $dueInterest, 2);
        $data['balance'] = round($principalAmount - $duePrincipal, 2);

        return $data;
    }
}
