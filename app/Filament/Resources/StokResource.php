<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StokResource\Pages;
use App\Filament\Resources\StokResource\RelationManagers;
use App\Models\Stok;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StokResource extends Resource
{
    protected static ?string $model = Stok::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Logistik';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                TextInput::make('nama_barang')
                                    ->required()
                                    ->placeholder('Masukan Nama Barang'),
                                Select::make('jenis_barang')
                                    ->required()
                                    ->options([
                                        'Barang Habis Pakai' => 'Barang Habis Pakai',
                                        'Rumah Tangga' => 'Rumah Tangga'
                                    ]),
                                TextInput::make('stok')
                                    ->placeholder('Masukan Jumlah Stok')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('harga')
                                    ->placeholder('Masukan Harga')
                                    ->numeric()
                                    ->required()
                                    ->currencyMask(thousandSeparator: ',',decimalSeparator: '.',precision: 2)
                            ])
                            ->columns(2)
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stok')
                    ->badge(),
                TextColumn::make('harga')
                    ->badge()
                    ->money('Rp. '),
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
            'index' => Pages\ListStoks::route('/'),
            'create' => Pages\CreateStok::route('/create'),
            'view' => Pages\ViewStok::route('/{record}'),
            'edit' => Pages\EditStok::route('/{record}/edit'),
        ];
    }
}
