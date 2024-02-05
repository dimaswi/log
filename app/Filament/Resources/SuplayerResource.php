<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuplayerResource\Pages;
use App\Filament\Resources\SuplayerResource\RelationManagers;
use App\Models\Suplayer;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuplayerResource extends Resource
{
    protected static ?string $model = Suplayer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Master';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_suplayer')
                ->required()
                ->label('Nama Suplayer')
                ->placeholder('Masukan Nama Suplayer'),
                TextInput::make('handphone')
                ->type('number')
                ->placeholder('Masukan Nomor Handphone')
                ->required(),
                Textarea::make('alamat')
                ->placeholder('Masukan Alamat')
                ->required()
                ->columnSpan(2),
                Textarea::make('keterangan')
                ->required()
                ->placeholder('Masukan Keterangan')
                ->columnSpan(2),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_suplayer')
                ->searchable()
                ->sortable(),
                TextColumn::make('handphone')
                ->searchable(),
                TextColumn::make('alamat')
                ->searchable(),
                TextColumn::make('keterangan')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSuplayers::route('/'),
            'create' => Pages\CreateSuplayer::route('/create'),
            'view' => Pages\ViewSuplayer::route('/{record}'),
            'edit' => Pages\EditSuplayer::route('/{record}/edit'),
        ];
    }
}
