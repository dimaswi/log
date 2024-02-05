<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanResource\Pages;
use App\Filament\Resources\PermintaanResource\RelationManagers;
use App\Models\Permintaan;
use App\Models\PermintaanItems;
use App\Models\Stok;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermintaanResource extends Resource
{
    protected static ?string $model = Permintaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Logistik';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('nama_unit', auth()->user()->unit)->count();
    }

    public static function form(Form $form): Form
    {
        $data = Permintaan::orderBy('id', 'desc')->first();

        if (empty($data)) {
            $nomor = 1;
        } else if (Carbon::now('Asia/Jakarta')->format('Y') != Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal)->year) {
            $nomor = 1;
        } else if (Carbon::now('Asia/Jakarta')->format('m') != Carbon::createFromFormat('Y-m-d H:i:s', $data->tanggal)->month) {
            $nomor = 1;
        } else {
            $nomor = $data->nomor + 1;
        }

        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                TextInput::make('nomor_permintaan')
                                    ->default('SP/' . $nomor . '/' . Carbon::now('Asia/Jakarta')->format('m') . '/' . Carbon::now('Asia/Jakarta')->format('Y'))
                                    ->readOnly(),
                                TextInput::make('nama_unit')
                                    ->default(auth()->user()->unit)
                                    ->readOnly(),
                                DateTimePicker::make('tanggal')
                                    ->default(Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')),
                                Hidden::make('nomor')
                                    ->default($nomor),
                                Hidden::make('status')
                                    ->default('Proses'),
                            ])->columns([
                                'sm' => 3
                            ]),

                        Card::make()
                            ->schema([
                                Placeholder::make('Barang'),
                                Repeater::make('permintaanItems')
                                    ->label('Barang Permintaan')
                                    ->relationship()
                                    ->schema([
                                        Select::make('nama_barang')
                                            ->searchable()
                                            ->options(Stok::all()->pluck('nama_barang', 'nama_barang'))
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $harga = Stok::where('nama_barang', $state)->value('harga');
                                                $set('harga', $harga);
                                            }),
                                        TextInput::make('jumlah')
                                            ->required()
                                            ->placeholder('Masukan Jumlah Barang'),
                                        TextInput::make('harga')
                                            ->required()
                                            ->placeholder('Masukan Harga Barang')
                                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),
                                    ])->columns(3)
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // dd(auth()->user());
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {

                if (auth()->user()->hasRole('Logistik')) {
                    $data = $query->orderBy('id', 'desc');
                } else {
                    $data = $query->where('nama_unit', auth()->user()->unit);
                }

                return $data;

            })
            ->columns([
                TextColumn::make('nomor_permintaan')->searchable()->sortable()->badge(),
                TextColumn::make('nama_unit')->searchable()->sortable(),
                TextColumn::make('tanggal')->searchable()->sortable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'Proses' => 'warning',
                    'Setuju' => 'success',
                    'Tolak' => 'danger',
                })->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Status')
                    ->visible(function (): bool {
                        $visible = auth()->user()->hasRole('Logistik');
                        return $visible;
                    })
                    ->form([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Proses' => 'Proses',
                                'Setuju' => 'Setuju',
                                'Tolak' => 'Tolak',
                            ])
                            ->required(),
                    ])
                    ->action(function (Permintaan $record, array $data): void {

                        if ($data['status'] == 'Setuju') {

                            $data_permintaan = PermintaanItems::where('permintaan_id', $record->id)->get();

                            foreach ($data_permintaan as $key => $value) {
                                $stok = Stok::where('nama_barang', $value->nama_barang)->first();
                                Stok::where('nama_barang', $value->nama_barang)->update([
                                    'stok' => $stok->stok - $value->jumlah
                                ]);
                            };

                            Permintaan::where('id', $record->id)->update([
                                'status' => $data['status']
                            ]);
                        }
                    }),
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
            'index' => Pages\ListPermintaans::route('/'),
            'create' => Pages\CreatePermintaan::route('/create'),
            'edit' => Pages\EditPermintaan::route('/{record}/edit'),
            'view' => Pages\ViewPermintaan::route('/{record}'),
        ];
    }
}
