<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FakturResource\Pages;
use App\Filament\Resources\FakturResource\RelationManagers;
use App\Models\Faktur;
use App\Models\Stok;
use App\Models\Suplayer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FakturResource extends Resource
{
    protected static ?string $model = Faktur::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Pembelian';

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
                                TextInput::make('nomor_transaksi')->required()->placeholder('Nomor Transaksi')->columnSpan(2),
                                TextInput::make('nomor_kwitansi')->required()->placeholder('Masukan Nomor Kwitansi')->columnSpan(2),
                                DateTimePicker::make('tanggal'),
                                Select::make('suplayer')
                                ->searchable()
                                ->options(Suplayer::all()->pluck('nama_suplayer', 'nama_suplayer')),
                                Select::make('tipe_pembelian')
                                ->searchable()
                                ->options([
                                    'CASH' => 'CASH',
                                    'KREDIT' => 'KREDIT'
                                ]),
                                TextInput::make('ppn')->numeric()->placeholder('Masukan PPN'),
                                TextInput::make('diskon')->numeric()->placeholder('Masukan Diskon'),
                                DateTimePicker::make('jatuh_tempo'),
                                Textarea::make('keterangan')->placeholder('keterangan')->columnSpan(2),
                                FileUpload::make('foto')->required()->columnSpanFull(),
                            ])->columns([
                                'sm' => 4
                            ]),

                        Card::make()
                            ->schema([
                                Placeholder::make('Barang'),
                                Repeater::make('fakturItems')
                                    ->label('Barang Permintaan')
                                    ->relationship()
                                    ->schema([
                                        Select::make('nama_barang')
                                            ->searchable()
                                            ->options(Stok::all()->pluck('nama_barang', 'nama_barang'))
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $harga = Stok::where('nama_barang', $state)->value('harga');
                                                $set('harga_lama', $harga);
                                            }),
                                        TextInput::make('jumlah')
                                            ->required()
                                            ->placeholder('Masukan Jumlah Barang'),
                                        TextInput::make('harga_lama')
                                            ->required()
                                            ->placeholder('Masukan Harga Lama Barang')
                                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),
                                        TextInput::make('harga_baru')
                                            ->required()
                                            ->placeholder('Masukan Harga Baru Barang')
                                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),
                                    ])->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                            $stok = Stok::where('nama_barang', $data['nama_barang'])->first();
                                            Stok::where('nama_barang', $data['nama_barang'])->update([
                                                'stok' => $stok->stok+$data['jumlah'],
                                            ]);

                                            return $data;
                                    })
                                    ->columns(4)
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_transaksi')->searchable()->sortable()->badge(),
                TextColumn::make('suplayer')->searchable()->sortable(),
                TextColumn::make('tanggal')->sortable(),
                TextColumn::make('tipe_pembelian')->badge()->color(fn (string $state): string => match ($state) {
                    'CASH' => 'success',
                    'KREDIT' => 'danger',
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListFakturs::route('/'),
            'create' => Pages\CreateFaktur::route('/create'),
            'view' => Pages\ViewFaktur::route('/{record}'),
            'edit' => Pages\EditFaktur::route('/{record}/edit'),
        ];
    }
}
