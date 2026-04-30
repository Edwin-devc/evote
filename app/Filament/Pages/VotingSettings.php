<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class VotingSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Voting Settings';

    protected static ?string $title = 'Voting Settings';

    protected static string $view = 'filament.pages.voting-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') === true;
    }

    public function mount(): void
    {
        $this->form->fill([
            'voting_open' => $this->getVotingOpen(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('voting_open')
                    ->label('Voting is open')
                    ->helperText('Disable to stop voter logins and ballot submissions.')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $votingOpen = (bool) ($data['voting_open'] ?? false);

        cache()->forever('voting_open', $votingOpen);

        Notification::make()
            ->title('Voting settings updated')
            ->success()
            ->send();
    }

    private function getVotingOpen(): bool
    {
        return (bool) cache()->get('voting_open', config('voting.open'));
    }
}
