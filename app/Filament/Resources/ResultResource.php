<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Models\Result;
use App\Models\Candidate;
use App\Models\Voter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class ResultResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Results';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Results';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form inputs if needed
            ]);
    }

    public static function table(Table $table): Table
    {
        // Get the total number of voters who participated
        $totalVoters = Voter::where('has_voted', true)->count();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Candidate')
                    ->searchable(),

                Tables\Columns\TextColumn::make('num_votes')
                    ->label('Votes')
                    ->numeric()
                    ->sortable(),

                    Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentage')
                    ->state(function (Candidate $record) {
                        // Get total votes for this position
                        $totalPositionVotes = Candidate::where('position_id', $record->position_id)
                            ->sum('num_votes');

                        if ($totalPositionVotes == 0) return '0.00%';

                        // Calculate percentage of votes within this position
                        return number_format(($record->num_votes / $totalPositionVotes) * 100, 2) . '%';
                    })
                    ->color(function (Candidate $record): ?string {
                        // Get highest vote in this candidate's position
                        $highestVote = Candidate::where('position_id', $record->position_id)
                            ->max('num_votes');

                        // Return 'success' (green) if this candidate has the highest votes
                        return ($record->num_votes == $highestVote && $record->num_votes > 0) ? 'success' : null;
                    })
                    ->sortable(function (Builder $query): Builder {
                        // Custom sorting for the calculated field
                        return $query->orderBy('num_votes');
                    }),

                Tables\Columns\TextColumn::make('position.name')
                    ->label('Position')
                    ->sortable(),
            ])
            ->defaultSort('position.name')
            ->filters([
                // Add filters if needed
            ])
            ->actions([
            ])
            ->bulkActions([
                // Remove bulk actions if not needed for results
            ])
            ->defaultGroup('position.name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
        ];
    }
}
