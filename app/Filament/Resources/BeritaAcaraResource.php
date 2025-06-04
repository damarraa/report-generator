<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeritaAcaraResource\Pages;
use App\Filament\Resources\BeritaAcaraResource\RelationManagers;
use App\Models\BeritaAcara;
use Barryvdh\DomPDF\Facade\Pdf;
use Doctrine\DBAL\Schema\Schema;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;
use Ramsey\Uuid\Type\Decimal;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class BeritaAcaraResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return 'Formulir BAPP';
    }
    protected static ?string $model = BeritaAcara::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Petugas']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['Admin', 'Petugas']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        // Section Informasi Pekerjaan
                        self::informasiPekerjaanSection(),
                        // Section Informasi Utama
                        self::informasiUtamaSection(),
                        // Section Galian & Perbaikan
                        self::galianPerbaikanSection(),
                        // Section Pasang Baru
                        self::pasangBaruSections(),
                        // Section Gangguan Tanpa/Dengan Tambah Kabel
                        self::gangguanKabelSections(),
                        // Section Yang Selalu Tampil
                        self::alwaysVisibleSections(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn() => auth()->id()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_bap')
                    ->label('No. BAP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('penyulang.penyulang_gardu')
                    ->label('Penyulang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lokasi_pekerjaan')
                    ->label('Lokasi')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->lokasi_pekerjaan),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Pada Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer')
                    ->relationship('customer', 'customer_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('penyulang')
                    ->relationship('penyulang', 'penyulang_gardu')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPDF')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        $record->load([
                            'customer',
                            'penyulang',
                            'jointer',
                            'user'
                        ]);
                        $filename = Str::slug($record->nomor_bap) . '.pdf';

                        return response()->streamDownload(
                            function () use ($record) {
                                echo Pdf::loadView('pdf.bapp', [
                                    'record' => $record
                                ])->stream();
                            },
                            $filename
                        );
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBeritaAcaras::route('/'),
            'create' => Pages\CreateBeritaAcara::route('/create'),
            'edit' => Pages\EditBeritaAcara::route('/{record}/edit'),
        ];
    }

    // Section Pasang Baru
    protected static function pasangBaruSections(): Section
    {
        return Section::make('Informasi Tambahan')
            ->schema([
                // Section Cek Tahanan Isolasi Awal
                self::cekTahananIsolasiSection('cek_tahanan_isolasi_awal', 'Cek Tahanan Isolasi Awal')
                    ->visible(fn(Get $get): bool => in_array(
                        $get('pekerjaan'),
                        [
                            'pasang baru',
                            'gangguan tanpa tambah kabel',
                            'gangguan dengan tambah kabel'
                        ]
                    )),

                // Section Cek Fisik Kabel Tambahan
                self::cekFisikKabelSection('cek_fisik_kabel_tambahan', 'Cek Fisik Kabel Tambahan / Baru')
                    ->visible(fn(Get $get): bool => in_array(
                        $get('pekerjaan'),
                        [
                            'pasang baru',
                            'gangguan dengan tambah kabel'
                        ]
                    )),

                // Section Material
                self::materialSection()
                    ->visible(fn(Get $get): bool => in_array(
                        $get('pekerjaan'),
                        [
                            'pasang baru',
                            'gangguan tanpa tambah kabel',
                            'gangguan dengan tambah kabel'
                        ]
                    )),

                // Section Cek Tahanan Isolasi Akhir
                self::cekTahananIsolasiSection('cek_tahanan_isolasi_akhir', 'Cek Tahanan Isolasi Akhir')
                    ->visible(fn(Get $get): bool => $get('pekerjaan') === 'pasang baru'),
            ]);
    }

    // Section Gangguan Tanpa Tambah Kabel
    protected static function gangguanKabelSections(): Section
    {
        return Section::make('Informasi Tambahan')
            ->schema([
                // Section Gangguan Pada
                self::gangguanSection()
                    ->visible(fn(Get $get): bool => in_array(
                        $get('pekerjaan'),
                        [
                            'gangguan tanpa tambah kabel',
                            'gangguan dengan tambah kabel'
                        ]
                    )),

                // Section Cek Fisik Kabel Gangguan
                self::cekFisikKabelSection('cek_fisik_kabel_gangguan', 'Cek Fisik Kabel Gangguan')
                    ->visible(fn(Get $get): bool => in_array(
                        $get('pekerjaan'),
                        [
                            'gangguan tanpa tambah kabel',
                            'gangguan dengan tambah kabel'
                        ]
                    )),
            ]);
    }

    // Section yang selalu tampil
    protected static function alwaysVisibleSections(): Section
    {
        return Section::make('Informasi Tambahan')
            ->schema([
                // Section Pekerjaan Lain-Lain
                self::pekerjaanLainSection('pekerjaan_lain', 'Pekerjaan Lain-Lain'),

                // Section Jointer Pelaksana
                self::jointerPelaksanaSection(),

                // Section Titik Koordinat
                self::titikKoordinatSection(),

                // Section Pengawasan
                self::pengawasanSection(),

                // Catatan Pekerjaan
                Textarea::make('catatan_pekerjaan')
                    ->label('Catatan Pekerjaan')
                    ->placeholder('..........')
                    ->columnSpanFull()
                    ->rows(3),

                // Section Dokumentasi Foto
                Section::make('Dokumentasi Foto')
                    ->schema([
                        self::fotoUploadField(
                            'foto_pengukuran',
                            'Foto Pengukuran (Maks 6 Foto)',
                            6
                        ),

                        self::fotoUploadField(
                            'foto_realisasi',
                            'Foto Realisasi (Maks 8 Foto)',
                            8
                        ),
                    ]),

                // Section Signature
                Section::make('Tanda Tangan Digital')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                SignaturePad::make('signature_pengawas')
                                    ->label('Tanda Tangan Pengawas')
                                    ->penColor('#000000')
                                    ->backgroundColor('#fafafa')
                                    ->required()
                                    ->columnSpan(1),

                                SignaturePad::make('signature_pelaksana')
                                    ->label('Tanda Tangan Pelaksana')
                                    ->penColor('#000000')
                                    ->backgroundColor('#fafafa')
                                    ->required()
                                    ->columnSpan(1),

                                SignaturePad::make('signature_kontraktor')
                                    ->label('Tanda Tangan Kontraktor')
                                    ->penColor('#000000')
                                    ->backgroundColor('#fafafa')
                                    ->required()
                                    ->columnSpan(1)
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('nama_pengawas')
                                    ->label('Nama Pengawas')
                                    ->required(),

                                TextInput::make('nama_pelaksana')
                                    ->label('Nama Pelaksana')
                                    ->required(),

                                TextInput::make('nama_kontraktor')
                                    ->label('Nama Kontraktor')
                                    ->required(),
                            ])
                    ])
                    ->collapsible(),
            ]);
    }

    // Section Informasi Pekerjaan
    protected static function informasiPekerjaanSection(): Section
    {
        return Section::make('Informasi Pekerjaan')
            ->schema([
                Grid::make(2)
                    ->schema([
                        // Jenis Pekerjaan
                        Select::make('jenis_pekerjaan')
                            ->options([
                                'jointing' => 'Jointing',
                                'terminating' => 'Terminating'
                            ])
                            ->label('Jenis Pekerjaan')
                            ->live()
                            ->required(),

                        // Pekerjaan
                        Select::make('pekerjaan')
                            ->options([
                                'pasang baru' => 'Pasang Baru',
                                'gangguan tanpa tambah kabel' => 'Gangguan Tanpa Tambah Kabel',
                                'gangguan dengan tambah kabel' => 'Gangguan Dengan Tambah Kabel'
                            ])
                            ->label('Pekerjaan Saat Ini')
                            ->live()
                            ->required(),
                    ]),
            ])
            ->collapsible();
    }
    // Section Informasi Utama
    protected static function informasiUtamaSection(): Section
    {
        return Section::make('Informasi Utama')
            ->schema([
                Grid::make(3)
                    ->schema([
                        TextInput::make('nomor_bap')
                            ->label('No. Berita Acara Pemasangan')
                            ->placeholder('No. BAPP')
                            ->required()
                            ->suffix('/BAPP/PAL')
                            ->mutateDehydratedStateUsing(function (?string $state): ?string {
                                // Cek input
                                if (blank($state)) return null;

                                // Gabungkan state dengan format
                                return "{$state}/BAPP/PAL";
                            })
                            ->columnSpan(1),

                        TextInput::make('no_spk')
                            ->label('No. SPK/SPBJ/PK/WO')
                            ->placeholder('No. SPK/SPBJ/PK/WO')
                            ->required()
                            ->columnSpan(2),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'customer_name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('customer_name')
                                    ->required()
                            ])
                            ->required(),

                        Select::make('penyulang_id')
                            ->label('Penyulang')
                            ->relationship('penyulang', 'penyulang_gardu')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('penyulang_gardu')
                                    ->required()
                            ])
                            ->required(),
                    ]),

                Grid::make(3)
                    ->schema([
                        TextInput::make('pelaksana_gali')
                            ->label('Pelaksana Gali/Kontraktor')
                            ->placeholder('Pelaksana Gali/Kontraktor')
                            ->default('PT Prisan Artha Lestari')
                            ->required(),

                        TextInput::make('arah_gardu')
                            ->label('Arah Gardu')
                            ->placeholder('Arah Gardu')
                            ->required(),

                        TextInput::make('lokasi_pekerjaan')
                            ->label('Lokasi Pekerjaan')
                            ->placeholder('Lokasi Pekerjaan')
                            ->required(),
                    ]),
            ])
            ->collapsible();
    }

    // Section Class
    protected static function galianPerbaikanSection(): Section
    {
        return Section::make('Galian & Perbaikan')
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Checkbox::make('galian_perbaikan.aspal')->label('Aspal'),
                                Checkbox::make('galian_perbaikan.beton')->label('Beton'),
                                TextInput::make('galian_perbaikan.lebar')
                                    ->label('Lebar (m)')
                                    ->placeholder('meter')
                                    ->numeric()
                                    ->inputMode('decimal'),
                            ]),

                        Group::make()
                            ->schema([
                                Checkbox::make('galian_perbaikan.berm')->label('Berm'),
                                Checkbox::make('galian_perbaikan.trotoar')->label('Trotoar'),
                                TextInput::make('galian_perbaikan.tinggi')
                                    ->label('Tinggi (m)')
                                    ->placeholder('meter')
                                    ->numeric()
                                    ->inputMode('decimal'),
                            ]),

                        Group::make()
                            ->schema([
                                TextInput::make('galian_perbaikan.jumlah_galian')
                                    ->label('Jumlah Galian')
                                    ->placeholder('Jumlah Galian')
                                    ->disabled(),
                                TextInput::make('galian_perbaikan.panjang')
                                    ->label('Panjang (m)')
                                    ->placeholder('meter')
                                    ->numeric()
                                    ->inputMode('decimal'),
                            ]),
                    ]),
            ])
            ->collapsible();
    }

    protected static function gangguanSection(): Section
    {
        return Section::make('Gangguan Pada')
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()->schema([
                            Checkbox::make('gangguan.kabel')->label('Kabel'),
                        ])->columnSpan(1),
                        Group::make()->schema([
                            Checkbox::make('gangguan.sambungan')->label('Sambungan'),
                        ])->columnSpan(1),
                        Group::make()->schema([
                            TextInput::make('gangguan.merk')->label('Merk')->placeholder('merk'),
                        ])->columnSpan(1),
                    ]),
            ])
            ->collapsible();
    }

    protected static function materialSection(): Section
    {
        return Section::make('Material')
            ->schema([
                Repeater::make('material')
                    ->schema([
                        TextInput::make('nama_material')
                            ->label('Nama Material')
                            ->placeholder('..........'),

                        TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->placeholder('..........'),

                        TextInput::make('konduktor')
                            ->label('Konduktor')
                            ->placeholder('..........'),
                    ])
                    ->columns(3)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->maxItems(10)
                    ->disableLabel()
                    ->createItemButtonLabel('Tambah Material')
                    ->collapsible()
                    ->cloneable()
                    ->itemLabel(fn(array $state): ?string => $state['nama_material'] ?? null),
            ])
            ->collapsible();
    }

    protected static function cekFisikKabelSection(string $prefix, string $title): Section
    {
        return Section::make($title)
            ->schema([
                Grid::make(4)
                    ->schema([
                        self::buildTeganganGroup($prefix),
                        self::buildJenisIsolasiGroup($prefix),
                        self::buildIntiKabelGroup($prefix),
                        self::buildUkuranKabelGroup($prefix),
                    ]),
            ])
            ->collapsible();
    }

    protected static function buildTeganganGroup(string $prefix): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make('tegangan')
                    ->content('Tegangan:')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold']),
                Checkbox::make("{$prefix}.tegangan_1kv")->label('1 Kv'),
                Checkbox::make("{$prefix}.tegangan_7c2kv")->label('7,2 Kv'),
                Checkbox::make("{$prefix}.tegangan_17c5kv")->label('17,5 Kv'),
                Checkbox::make("{$prefix}.tegangan_24kv")->label('24 Kv'),
                Checkbox::make("{$prefix}.tegangan_36kv")->label('36 Kv'),
            ]);
    }

    protected static function buildJenisIsolasiGroup(string $prefix): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make('jenis_isolasi')
                    ->content('Jenis Isolasi:')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold']),
                Checkbox::make("{$prefix}.jenis_isolasi_xlpe")->label('XLPE'),
                Checkbox::make("{$prefix}.jenis_isolasi_pilc")->label('PILC'),
            ]);
    }

    protected static function buildIntiKabelGroup(string $prefix): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make('inti_kabel')
                    ->content('Inti Kabel:')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold']),
                Checkbox::make("{$prefix}.inti_kabel_1c")->label('1 C'),
                Checkbox::make("{$prefix}.inti_kabel_3c")->label('3 C'),
                TextInput::make("{$prefix}.inti_kabel")
                    ->label('Lainnya')
                    ->placeholder('C'),
            ]);
    }

    protected static function buildUkuranKabelGroup(string $prefix): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make('ukuran_kabel')
                    ->content('Ukuran Kabel:')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold']),
                Checkbox::make("{$prefix}.ukuran_kabel_150")->label('150 mm²'),
                Checkbox::make("{$prefix}.ukuran_kabel_240")->label('240 mm²'),
                Checkbox::make("{$prefix}.ukuran_kabel_300")->label('300 mm²'),
                TextInput::make("{$prefix}.ukuran_kabel")
                    ->label('Lainnya')
                    ->placeholder('mm²'),
            ]);
    }

    protected static function cekTahananIsolasiSection(string $prefix, string $title): Section
    {
        return Section::make($title)
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.r")->label('R')->numeric()->placeholder('MΩ'),
                            ]),
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.s")->label('S')->numeric()->placeholder('MΩ'),
                            ]),
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.t")->label('T')->numeric()->placeholder('MΩ'),
                            ]),
                    ]),
            ])->collapsible();
    }

    // protected static function cekFisikKabelTambahanSection(string $prefix, string $title): Section
    // {
    //     return Section::make($title)
    //         ->schema([
    //             Grid::make(4)
    //                 ->schema([
    //                     Group::make()
    //                         ->schema([
    //                             Placeholder::make('tegangan')
    //                                 ->content('Tegangan:')
    //                                 ->disableLabel()
    //                                 ->extraAttributes(['class' => 'font-bold']),
    //                             Checkbox::make("{$prefix}.tegangan_1kv")->label('1 Kv'),
    //                             Checkbox::make("{$prefix}.tegangan_7c2kv")->label('7,2 Kv'),
    //                             Checkbox::make("{$prefix}.tegangan_17c5kv")->label('17,5 Kv'),
    //                             Checkbox::make("{$prefix}.tegangan_24kv")->label('24 Kv'),
    //                             Checkbox::make("{$prefix}.tegangan_36kv")->label('36 Kv'),
    //                         ]),

    //                     Group::make()
    //                         ->schema([
    //                             Placeholder::make('jenis_isolasi')
    //                                 ->content('Jenis Isolasi:')
    //                                 ->disableLabel()
    //                                 ->extraAttributes(['class' => 'font-bold']),
    //                             Checkbox::make("{$prefix}.jenis_isolasi_xlpe")->label('XLPE'),
    //                             Checkbox::make("{$prefix}.jenis_isolasi_pilc")->label('PILC'),
    //                         ]),

    //                     Group::make()
    //                         ->schema([
    //                             Placeholder::make('inti_kabel')
    //                                 ->content('Inti Kabel:')
    //                                 ->disableLabel()
    //                                 ->extraAttributes(['class' => 'font-bold']),
    //                             Checkbox::make("{$prefix}.inti_kabel_1c")->label('1 C'),
    //                             Checkbox::make("{$prefix}.inti_kabel_3c")->label('3 C'),
    //                             TextInput::make("{$prefix}.inti_kabel")
    //                                 ->label('Lainnya')
    //                                 ->placeholder('C'),
    //                         ]),

    //                     Group::make()
    //                         ->schema([
    //                             Placeholder::make('ukuran_kabel')
    //                                 ->content('Ukuran Kabel:')
    //                                 ->disableLabel()
    //                                 ->extraAttributes(['class' => 'font-bold']),
    //                             Checkbox::make("{$prefix}.ukuran_kabel_150")->label('150 mm²'),
    //                             Checkbox::make("{$prefix}.ukuran_kabel_240")->label('240 mm²'),
    //                             Checkbox::make("{$prefix}.ukuran_kabel_300")->label('300 mm²'),
    //                             TextInput::make("{$prefix}.ukuran_kabel")
    //                                 ->label('Lainnya')
    //                                 ->placeholder('mm²'),
    //                         ]),
    //                 ]),
    //         ])
    //         ->collapsible();
    // }

    protected static function cekTahananIsolasiAkhirSection(string $prefix, string $title): Section
    {
        return Section::make($title)
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.r")->label('R')->numeric()->placeholder('MΩ'),
                            ]),
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.s")->label('S')->numeric()->placeholder('MΩ'),
                            ]),
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.t")->label('T')->numeric()->placeholder('MΩ'),
                            ]),
                    ]),
            ])->collapsible();
    }

    protected static function pekerjaanLainSection(string $prefix, string $title): Section
    {
        return Section::make($title)
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Checkbox::make("{$prefix}.pengaspalan")->label('Pengaspalan'),
                            ]),
                        Group::make()
                            ->schema([
                                Checkbox::make("{$prefix}.cor_beton")->label('Cor Beton'),
                            ]),
                        Group::make()
                            ->schema([
                                Checkbox::make("{$prefix}.urug")->label('Urug'),
                            ]),
                        Group::make()
                            ->schema([
                                Checkbox::make("{$prefix}.sewa_stamper")->label('Sewa Stamper'),
                            ]),
                        Group::make()
                            ->schema([
                                TextInput::make("{$prefix}.gelar_kabel")
                                    ->label('Gelar Kabel')
                                    ->numeric()
                                    ->placeholder('m')
                                    ->inputMode('decimal'),
                            ]),
                    ]),
            ])->collapsible();
    }

    protected static function jointerPelaksanaSection(): Section
    {
        return Section::make('Jointer Pelaksana')
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('leader_id')
                                    ->label('Leader')
                                    ->relationship('leader', 'nama_jointer')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disableOptionWhen(function (string $value, \Filament\Forms\Get $get) {
                                        return in_array($value, [$get('jointer_id'), $get('helper_id')]);
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nama_jointer')
                                            ->required()
                                    ])
                                    ->required(),
                            ]),
                        Group::make()
                            ->schema([
                                Select::make('jointer_id')
                                    ->label('Jointer')
                                    ->relationship('jointer', 'nama_jointer')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disableOptionWhen(function (string $value, \Filament\Forms\Get $get) {
                                        return in_array($value, [$get('leader_id'), $get('helper_id')]);
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nama_jointer')
                                            ->required()
                                    ])
                                    ->required(),
                            ]),
                        Group::make()
                            ->schema([
                                Select::make('helper_id')
                                    ->label('Helper')
                                    ->relationship('helper', 'nama_jointer')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->disableOptionWhen(function (string $value, \Filament\Forms\Get $get) {
                                        return in_array($value, [$get('leader_id'), $get('jointer_id')]);
                                    })
                                    ->createOptionForm([
                                        TextInput::make('nama_jointer')
                                            ->required()
                                    ])
                                    ->required(),
                            ]),
                    ]),
            ])->collapsible();
    }

    protected static function titikKoordinatSection(): Section
    {
        return Section::make('Lokasi Pekerjaan')
            ->schema([
                Map::make('titik_koordinat')
                    ->label('Peta Lokasi Pekerjaan')
                    ->defaultLocation(0.510440, 101.438309)
                    ->required()
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if (!is_array($state)) return;

                        $lat = $state['lat'] ?? $state[0] ?? null;
                        $lng = $state['lng'] ?? $state[1] ?? null;

                        if ($lat !== null && $lng !== null) {
                            $set('latitude', $lat);
                            $set('longitude', $lng);
                            $set('titik_koordinat', ['lat' => $lat, 'lng' => $lng]); // Format konsisten
                        }
                    })
                    ->extraAttributes([
                        'id' => 'map-container',
                        'x-data' => '{}',
                        '@location-updated.window' => '
                                        if (!window.mapInstance) return;
                                            mapInstance.setView([$event.detail.lat, $event.detail.lng], 18); // Zoom lebih dekat
                                            if (!window.mapMarker) {
                                                window.mapMarker = L.marker([$event.detail.lat, $event.detail.lng], {
                                                draggable: true,
                                                autoPan: true,
                                                icon: L.divIcon({className: "custom-marker", html: "📍"}) // Marker kustom
                                            }).addTo(mapInstance);
                                            
                                            window.mapMarker.on("dragend", function(e) {
                                                const newPos = e.target.getLatLng();
                                                $wire.set("latitude", newPos.lat);
                                                $wire.set("longitude", newPos.lng);
                                                $wire.set("titik_koordinat", [newPos.lat, newPos.lng]);
                                            });
                                        } else {
                                            window.mapMarker.setLatLng([$event.detail.lat, $event.detail.lng]);
                                        }
                                            // Update popup dengan info akurasi jika ada
                                        const accuracy = $event.detail.accuracy ? `Akurasi: ~${Math.round($event.detail.accuracy)} meter` : "";
                                        window.mapMarker.bindPopup(`Lokasi pekerjaan<br>${accuracy}`).openPopup();'
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (is_numeric($state) && is_numeric($get('longitude'))) {
                                    $set('titik_koordinat', [$state, $get('longitude')]);
                                }
                            })
                            ->extraAttributes(['id' => 'latitude-field']),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (is_numeric($get('latitude')) && is_numeric($state)) {
                                    $set('titik_koordinat', [$get('latitude'), $state]);
                                }
                            })
                            ->extraAttributes(['id' => 'longitude-field']),
                    ]),

                Actions::make([
                    Action::make('get_location')
                        ->label('Dapatkan Lokasi Saya Sekarang')
                        ->icon('heroicon-o-map-pin')
                        ->action(function ($livewire) {
                            $livewire->dispatch('get-live-location');
                        })
                        ->extraAttributes([
                            'class' => 'cursor-pointer w-full justify-center'
                        ]),
                ]),

                View::make('filament.components.location-error')
                    ->extraAttributes([
                        'id' => 'location-error',
                        'class' => 'text-sm text-red-600 mt-2'
                    ]),
            ])
            ->collapsible();
    }

    protected static function pengawasanSection(): Section
    {
        return Section::make('Pengawasan Pekerjaan')
            ->schema([
                // Waktu Pemasangan
                Grid::make(3)
                    ->schema([
                        Placeholder::make('waktu_pemasangan_label')
                            ->content('Waktu Pemasangan:')
                            ->disableLabel()
                            ->extraAttributes(['class' => 'font-bold'])
                            ->columnSpanFull(),

                        TimePicker::make('waktu_pemasangan.tiba_di_lokasi')
                            ->label('Tiba di Lokasi')
                            ->seconds(false),

                        TimePicker::make('waktu_pemasangan.mulai_kerja')
                            ->label('Mulai Kerja')
                            ->seconds(false),

                        TimePicker::make('waktu_pemasangan.selesai')
                            ->label('Selesai')
                            ->seconds(false),
                    ]),

                // Peralatan Kerja
                self::pengawasanChecklist(
                    'peralatan_kerja',
                    'Peralatan Kerja',
                    'Catatan Peralatan Kerja'
                ),

                // Seragam Kerja
                self::pengawasanChecklist(
                    'seragam_kerja',
                    'Seragam Kerja',
                    'Catatan Seragam Kerja'
                ),

                // Peralatan K2
                self::pengawasanChecklist(
                    'peralatan_k2',
                    'Peralatan K2',
                    'Catatan Peralatan K2'
                ),

                // Label Timah
                self::pengawasanChecklist(
                    'label_timah',
                    'Label Timah/Penang',
                    'Catatan Label Timah'
                ),
            ])
            ->collapsible();
    }

    protected static function pengawasanChecklist(string $field, string $title, string $noteLabel): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make("{$field}_label")
                    ->content($title . ':')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold']),

                Checkbox::make("{$field}.lengkap")
                    ->label('Lengkap/Ada')
                    ->inline(),

                Checkbox::make("{$field}.tidak_lengkap")
                    ->label('Tidak Lengkap/Tidak Ada')
                    ->inline(),

                Textarea::make("{$field}.catatan")
                    ->label($noteLabel)
                    ->placeholder('.....')
                    ->columnSpanFull(),
            ])
            ->columnSpan(1);
    }

    protected static function fotoUploadField(string $name, string $label, int $maxFiles): Field
    {
        return FileUpload::make($name)
            ->label($label)
            ->multiple()
            ->maxFiles($maxFiles)
            ->directory('bap')
            ->image()
            ->imagePreviewHeight('150')
            ->openable()
            ->downloadable()
            ->reorderable()
            ->appendFiles()
            ->lazy()
            ->previewable()
            ->preserveFilenames();
    }
}
