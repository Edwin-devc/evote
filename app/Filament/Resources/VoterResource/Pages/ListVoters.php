<?php

namespace App\Filament\Resources\VoterResource\Pages;

use App\Filament\Resources\VoterResource;
use App\Models\Voter;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListVoters extends ListRecords
{
    protected static string $resource = VoterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importVoters')
                ->label('Import voters')
                ->form([
                    FileUpload::make('file')
                        ->label('CSV file')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->disk('local')
                        ->directory('imports')
                        ->visibility('private'),
                ])
                ->action(function (array $data): void {
                    $this->importVotersFromCsv($data['file']);
                }),
        ];
    }

    private function importVotersFromCsv(string $path): void
    {
        $disk = Storage::disk('local');
        $fullPath = $disk->path($path);

        $handle = fopen($fullPath, 'rb');
        if ($handle === false) {
            return;
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            $disk->delete($path);
            return;
        }

        $delimiter = $this->detectDelimiter($firstLine);
        $firstRow = $this->trimRow(str_getcsv($firstLine, $delimiter));

        if (!$this->isHeaderRow($firstRow)) {
            $this->storeVoterRow($firstRow);
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $this->storeVoterRow($this->trimRow($row));
        }

        fclose($handle);
        $disk->delete($path);
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [',', "\t", ';'];
        $counts = [];

        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($line, $delimiter);
        }

        arsort($counts);
        $best = array_key_first($counts);

        return ($counts[$best] ?? 0) > 0 ? $best : ',';
    }

    private function isHeaderRow(array $row): bool
    {
        $row = array_map(static fn ($value) => strtolower(trim((string) $value)), $row);

        return ($row[0] ?? '') === 'name'
            && ($row[1] ?? '') === 'email'
            && ($row[2] ?? '') === 'student_number'
            && ($row[3] ?? '') === 'access_code';
    }

    private function trimRow(array $row): array
    {
        return array_map(static fn ($value) => trim((string) $value), $row);
    }

    private function storeVoterRow(array $row): void
    {
        if (count(array_filter($row, static fn ($value) => $value !== '')) === 0) {
            return;
        }

        [$name, $email, $studentNumber, $accessCode] = array_pad($row, 4, '');

        if ($name === '' || $email === '' || $studentNumber === '' || $accessCode === '') {
            return;
        }

        Voter::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'student_number' => $studentNumber,
                'access_code' => $accessCode,
            ],
        );
    }
}
