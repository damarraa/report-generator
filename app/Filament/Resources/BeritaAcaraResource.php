<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeritaAcaraResource\Pages;
use App\Filament\Resources\BeritaAcaraResource\RelationManagers;
use App\Models\BeritaAcara;
use App\Models\Material;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
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
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use App\Forms\Components\MapPicker;
use Filament\Forms\Components\Component;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Support\RawJs;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;

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

                        // Section Gangguan Pada
                        self::gangguanSection()
                            ->hidden(fn(Get $get): bool => !in_array(
                                $get('pekerjaan'),
                                [
                                    'gangguan tanpa tambah kabel',
                                    'gangguan dengan tambah kabel'
                                ]
                            )),

                        // Section Cek Fisik Kabel Gangguan
                        self::cekFisikKabelSection('cek_fisik_kabel_gangguan', 'Cek Fisik Kabel Gangguan')
                            ->hidden(fn(Get $get): bool => !in_array(
                                $get('pekerjaan'),
                                [
                                    'gangguan tanpa tambah kabel',
                                    'gangguan dengan tambah kabel'
                                ]
                            )),

                        // Section Cek Tahanan Isolasi Awal
                        self::cekTahananIsolasiSection('cek_tahanan_isolasi_awal', 'Cek Tahanan Isolasi Awal'),

                        // Section Cek Fisik Kabel Tambahan
                        self::cekFisikKabelSection('cek_fisik_kabel_tambahan', 'Cek Fisik Kabel Tambahan')
                            ->hidden(fn(Get $get): bool => !in_array(
                                $get('pekerjaan'),
                                [
                                    'pasang baru',
                                    'gangguan dengan tambah kabel'
                                ]
                            )),

                        // Section Material
                        self::materialSection(),

                        // Section Cek Tahanan Isolasi Akhir
                        self::cekTahananIsolasiSection('cek_tahanan_isolasi_akhir', 'Cek Tahanan Isolasi Akhir')
                            ->hidden(fn(Get $get): bool => !in_array(
                                $get('pekerjaan'),
                                [
                                    'pasang baru',
                                ]
                            )),

                        // Section Yang Selalu Tampil
                        self::alwaysVisibleSections(),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn() => auth()->id()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             InfolistSection::make('Dokumentasi Foto')
    //                 ->schema([
    //                     RepeatableEntry::make('foto_pengukuran')
    //                         ->label('Foto Pengukuran')
    //                         ->schema([
    //                             ImageEntry::make('self')
    //                                 ->label(false)
    //                                 ->disk('public')
    //                                 ->height(200)
    //                                 ->columnSpanFull(),
    //                         ])->columns(4),

    //                     RepeatableEntry::make('foto_realisasi')
    //                         ->label('Foto Realisasi')
    //                         ->schema([
    //                             ImageEntry::make('self')
    //                                 ->label(false)
    //                                 ->disk('public')
    //                                 ->height(200)
    //                                 ->columnSpanFull(),
    //                         ])->columns(4),
    //                 ])->collapsible(),

    //             // Tambahkan field lain jika ingin ditampilkan di halaman View
    //         ]);
    // }

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
                            'spk',
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

    // Section yang selalu tampil updated
    protected static function alwaysVisibleSections(): Section
    {
        return Section::make('')
            ->disableLabel()
            ->schema([
                // Section Pekerjaan Lain-Lain
                self::pekerjaanLainSection('pekerjaan_lain', 'Pekerjaan Lain-Lain'),

                // Section Jointer Pelaksana
                self::jointerPelaksanaSection(),

                // Section Titik Koordinat
                self::titikKoordinatSection(),

                // Section Pengawasan
                self::pengawasanSection(),

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

                // Section Signature (SUDAH RESPONSIF)
                Section::make('Tanda Tangan Digital')
                    ->schema([
                        // Grid utama yang mengatur layout Pengawas, Pelaksana, dan Kontraktor
                        Grid::make(['default' => 1, 'lg' => 3]) // 1 kolom di mobile, 3 di desktop
                            ->schema([
                                // GRUP 1: PENGAWAS
                                Group::make()
                                    ->schema([
                                        SignaturePad::make('signature_pengawas')
                                            ->label('Tanda Tangan Pengawas')
                                            ->penColor('#000000')
                                            ->backgroundColor('#ffffff'),
                                        TextInput::make('nama_pengawas')
                                            ->label('Nama Pengawas'),
                                    ]),

                                // GRUP 2: PELAKSANA
                                Group::make()
                                    ->schema([
                                        SignaturePad::make('signature_pelaksana')
                                            ->label('Tanda Tangan Pelaksana')
                                            ->penColor('#000000')
                                            ->backgroundColor('#ffffff')
                                            ->required(),
                                        TextInput::make('nama_pelaksana')
                                            ->label('Nama Pelaksana')
                                            ->required(),
                                    ]),

                                // GRUP 3: KONTRAKTOR
                                Group::make()
                                    ->schema([
                                        SignaturePad::make('signature_kontraktor')
                                            ->label('Tanda Tangan Kontraktor')
                                            ->penColor('#000000')
                                            ->backgroundColor('#ffffff'),
                                        TextInput::make('nama_kontraktor')
                                            ->label('Nama Kontraktor'),
                                    ]),
                            ]),
                    ]),
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
        ;
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
                            // ->suffix('/BAPP/PAL')
                            ->default(function () {
                                // Ambil nomor terakhir dari database
                                $lastNumber = \App\Models\BeritaAcara::max('id') ?? 0;
                                $incrementedNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

                                // Format lengkap dengan suffix
                                return "{$incrementedNumber}/BAPP/PAL";
                            })
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),

                        Select::make('spk_id')
                            ->label('No. SPK/SPBJ/PK/WO')
                            ->relationship('spk', 'nomor_spk')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('nomor_spk')
                                    ->label('Nomor SPK/SPBJ/PK/WO')
                                    ->mask(RawJs::make(<<<'JS'
                                    $input.toUpperCase()
                                    JS))
                                    ->mutateDehydratedStateUsing(fn(?string $state): ?string => strtoupper($state))
                                    ->required()
                            ])
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
        ;
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
                                Placeholder::make('jenis_permukaan')
                                    ->content('Jenis Permukaan:')
                                    ->disableLabel()
                                    ->extraAttributes(['class' => 'font-bold']),
                                Radio::make('galian_perbaikan.jenis_permukaan')
                                    ->options([
                                        'aspal' => 'Aspal',
                                        'beton' => 'Beton',
                                        'berm' => 'Berm',
                                        'trotoar' => 'Trotoar'
                                    ])
                                    ->disableLabel()
                                    ->live()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),


                        Group::make()
                            ->schema([
                                Placeholder::make('dimensi_label')
                                    ->content('Dimensi Galian:')
                                    ->disableLabel()
                                    ->extraAttributes(['class' => 'font-bold']),
                                TextInput::make('galian_perbaikan.panjang')
                                    ->label('Panjang (m)')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->inputMode('decimal'),
                                TextInput::make('galian_perbaikan.lebar')
                                    ->label('Lebar (m)')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->inputMode('decimal'),
                                TextInput::make('galian_perbaikan.tinggi')
                                    ->label('Tinggi (m)')
                                    ->placeholder('0.00')
                                    ->numeric()
                                    ->inputMode('decimal'),
                            ])
                            ->columnSpan(1),

                        Group::make()
                            ->schema([
                                TextInput::make('galian_perbaikan.jumlah_galian')
                                    ->label('Jumlah Galian')
                                    ->placeholder('Jumlah Galian')
                                    ->disabled(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
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
        ;
    }

    protected static function materialSection(): Section
    {
        return Section::make('Material')
            ->schema([
                Repeater::make('material')
                    ->schema([
                        Select::make('material_name')
                            ->label('Nama Material')
                            ->options(Material::pluck('material_name', 'material_name'))
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('material_name')->required()
                            ])
                            ->required(),
                        TextInput::make('serial_number'),
                        Select::make('konduktor')
                            ->options([
                                'shearbolt' => 'Shearbolt',
                                'press connector' => 'Press Connector',
                            ])
                    ])
                    ->columns(3)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->maxItems(10)
                    ->disableLabel()
                    ->createItemButtonLabel('Tambah Material')

                    ->cloneable()
                    ->itemLabel(fn(array $state): ?string => $state['nama_material'] ?? null),
            ]);
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
        ;
    }

    protected static function buildTeganganGroup(string $prefix): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make('tegangan')
                    ->content('Tegangan:')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold']),
                Radio::make("{$prefix}.tegangan")
                    ->options([
                        'tegangan_1kv' => '1 kV',
                        'tegangan_7c2kv' => '7,2 kV',
                        'tegangan_17c5kv' => '17,5 kV',
                        'tegangan_24kv' => '24 kV',
                        'tegangan_36kv' => '36 kV'
                    ])
                    ->disableLabel()
                    ->columnSpanFull(),
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
                Radio::make("{$prefix}.jenis_isolasi")
                    ->options([
                        'jenis_isolasi_xlpe' => 'XLPE',
                        'jenis_isolasi_pilc' => 'PILC'
                    ])
                    ->disableLabel()
                    ->columnSpanFull(),
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
                Radio::make("{$prefix}.inti_kabel")
                    ->options([
                        '1c' => '1 C',
                        '3c' => '3 C',
                        'lainnya' => 'Lainnya'
                    ])
                    ->disableLabel()
                    ->live()
                    ->columnSpanFull(),
                TextInput::make("{$prefix}.inti_kabel_lainnya")
                    ->label('Lainnya')
                    ->placeholder('Jumlah inti kabel C')
                    ->hidden(function (Get $get) use ($prefix) {
                        return $get("{$prefix}.inti_kabel") !== 'lainnya';
                    })
                    ->required(function (Get $get) use ($prefix) {
                        return $get("{$prefix}.inti_kabel") === 'lainnya';
                    }),
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
                Radio::make("{$prefix}.ukuran_kabel")
                    ->options([
                        '150' => '150 mm²',
                        '240' => '240 mm²',
                        '300' => '300 mm²',
                        'lainnya' => 'Lainnya'
                    ])
                    ->disableLabel()
                    ->live()
                    ->columnSpanFull(),
                TextInput::make("{$prefix}.ukuran_kabel_lainnya")
                    ->label('Specify')
                    ->placeholder('Ukuran kabel dalam mm²')
                    ->hidden(function (Get $get) use ($prefix) {
                        return $get("{$prefix}.ukuran_kabel") !== 'lainnya';
                    })
                    ->required(function (Get $get) use ($prefix) {
                        return $get("{$prefix}.ukuran_kabel") === 'lainnya';
                    }),
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
            ]);
    }

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
            ]);
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
            ]);
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
            ]);
    }

    // Versi Updated 3
    protected static function titikKoordinatSection(): Section
    {
        return Section::make('Lokasi Pekerjaan')
            ->schema([
                Map::make('titik_koordinat')
                    ->label('Peta Lokasi Pekerjaan')
                    ->required()
                    ->columnSpanFull()
                    // --- PERUBAHAN 1: Hapus ->reactive() karena kita akan handle manual ---
                    ->afterStateUpdated(function ($state, Set $set) {
                        $lat = Arr::get($state, 'lat');
                        $lng = Arr::get($state, 'lng');

                        if ($lat !== null && $lng !== null) {
                            $set('latitude', (float)$lat);
                            $set('longitude', (float)$lng);
                        }
                    })
                    ->helperText('Anda bisa menggeser marker untuk menentukan koordinat.'), // --- PERUBAHAN 2: Helper text disesuaikan ---

                TextInput::make('koordinat_paste')
                    ->label('Paste Koordinat (Latitude, Longitude) Disini')
                    ->placeholder('Contoh: -0.5123, 101.4456')
                    ->live(onBlur: true)
                    ->dehydrated(false) // --- PERUBAHAN 3: Ditambahkan agar tidak disimpan ke database ---
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (blank($state)) return;

                        $parts = explode(',', $state);

                        if (count($parts) === 2) {
                            $lat = trim($parts[0]);
                            $lng = trim($parts[1]);

                            if (is_numeric($lat) && is_numeric($lng)) {
                                $set('latitude', (float)$lat);
                                $set('longitude', (float)$lng);
                                // --- PERUBAHAN 4: State peta diupdate agar data sinkron & marker bergerak ---
                                $set('titik_koordinat', ['lat' => (float)$lat, 'lng' => (float)$lng]);

                                Notification::make()
                                    ->title('Koordinat Berhasil Diproses')
                                    ->success()
                                    // --- PERUBAHAN 5: Teks notifikasi disesuaikan ---
                                    ->body('Latitude, Longitude, dan Peta sudah diperbarui.')
                                    ->send();
                            }
                        }
                    })
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->required()
                            ->live()
                            // --- PERUBAHAN 6: Hook ditambahkan untuk sinkronisasi dari input ke peta ---
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (is_numeric($get('longitude'))) {
                                    $set('titik_koordinat', ['lat' => (float)$state, 'lng' => (float)$get('longitude')]);
                                }
                            }),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->required()
                            ->live()
                            // --- PERUBAHAN 7: Hook ditambahkan untuk sinkronisasi dari input ke peta ---
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                if (is_numeric($get('latitude'))) {
                                    $set('titik_koordinat', ['lat' => (float)$get('latitude'), 'lng' => (float)$state]);
                                }
                            }),
                    ]),

                // --- PERUBAHAN 8: View untuk error 'geolocation' tidak lagi relevan dan bisa dihapus/disembunyikan ---
                // View::make('filament.components.location-error')
                //     ->extraAttributes([ ... ]),
            ])
            ->columns(1);
    }

    // Versi Updated 2
    // protected static function titikKoordinatSection(): Section
    // {
    //     return Section::make('Lokasi Pekerjaan')
    //         ->extraAttributes([
    //             'x-data' => 'geolocation',
    //         ])
    //         ->schema([
    //             Map::make('titik_koordinat')
    //                 ->label('Peta Lokasi Pekerjaan')
    //                 ->required()
    //                 ->columnSpanFull()
    //                 ->live()
    //                 ->afterStateUpdated(function ($state, Set $set) {
    //                     $lat = Arr::get($state, 'lat');
    //                     $lng = Arr::get($state, 'lng');

    //                     if ($lat !== null && $lng !== null) {
    //                         $set('latitude', (float)$lat);
    //                         $set('longitude', (float)$lng);
    //                     }
    //                 })
    //                 ->helperText('Tekan tombol Marker pada sebelah kanan bawah untuk Reload Location'),

    //             TextInput::make('koordinat_paste')
    //                 ->label('Paste Koordinat (Latitude, Longitude) Disini')
    //                 ->placeholder('Contoh: -0.5123, 101.4456')
    //                 ->helperText('Salin dan tempel (paste) koordinat yang sudah ada di sini')
    //                 ->live(onBlur: true)
    //                 ->afterStateUpdated(function (Set $set, ?string $state) {
    //                     if (blank($state)) return;

    //                     $parts = explode(',', $state);

    //                     if (count($parts) === 2) {
    //                         $lat = trim($parts[0]);
    //                         $lng = trim($parts[1]);

    //                         if (is_numeric($lat) && is_numeric($lng)) {
    //                             $set('latitude', (float)$lat);
    //                             $set('longitude', (float)$lng);
    //                             $set('titik_koordinat', ['lat' => (float)$lat, 'lng' => (float)$lng]);
    //                         }
    //                     }
    //                 })
    //                 ->columnSpanFull(),

    //             Grid::make(['default' => 1, 'md' => 2])
    //                 ->schema([
    //                     TextInput::make('latitude')
    //                         ->label('Latitude')
    //                         ->numeric()
    //                         ->required()
    //                         ->live()
    //                         ->afterStateUpdated(function ($state, Set $set, Get $get) {
    //                             if (is_numeric($state) && is_numeric($get('longitude'))) {
    //                                 $set('titik_koordinat', ['lat' => (float)$state, 'lng' => (float)$get('longitude')]);
    //                             }
    //                         }),

    //                     TextInput::make('longitude')
    //                         ->label('Longitude')
    //                         ->numeric()
    //                         ->required()
    //                         ->live()
    //                         ->afterStateUpdated(function ($state, Set $set, Get $get) {
    //                             if (is_numeric($get('latitude')) && is_numeric($state)) {
    //                                 $set('titik_koordinat', ['lat' => (float)$get('latitude'), 'lng' => (float)$state]);
    //                             }
    //                         }),
    //                 ]),

    //             View::make('filament.components.location-error')
    //                 ->extraAttributes([
    //                     'id' => 'location-error',
    //                     'class' => 'text-sm text-red-600 mt-2'
    //                 ]),
    //         ])
    //         ->columns(1);
    // }

    // Versi Updated
    // protected static function titikKoordinatSection(): Section
    // {
    //     return Section::make('Lokasi Pekerjaan')
    //         ->extraAttributes([
    //             'x-data' => 'geolocation', // Inisialisasi Alpine.js di sini
    //         ])
    //         ->schema([
    //             Map::make('titik_koordinat')
    //                 ->label('Peta Lokasi Pekerjaan')
    //                 ->lazy()
    //                 // Default Pekanbaru, Riau, Indonesia
    //                 // Ini akan menjadi lokasi awal jika geolokasi gagal atau ditolak.
    //                 // ->defaultLocation(0.510440, 101.438309)
    //                 ->required()
    //                 ->columnSpanFull()
    //                 ->live()
    //                 // Callback saat nilai MapPicker berubah (misal: marker digeser)
    //                 ->afterStateUpdated(function ($state, Set $set) {
    //                     if (!is_array($state) && !is_object($state)) {
    //                         // Tangani kasus $state mungkin null atau bukan array/objek
    //                         return;
    //                     }

    //                     // Pastikan $state adalah array asosiatif {lat:x, lng:y}
    //                     $lat = $state['lat'] ?? null;
    //                     $lng = $state['lng'] ?? null;

    //                     if ($lat !== null && $lng !== null) {
    //                         $set('latitude', (float)$lat);
    //                         $set('longitude', (float)$lng);
    //                         // Penting: pastikan titik_koordinat selalu format array asosiatif {lat:x, lng:y}
    //                         $set('titik_koordinat', ['lat' => (float)$lat, 'lng' => (float)$lng]);
    //                     }
    //                 })
    //                 // Atribut ekstra untuk JavaScript MapPicker dan penanganan event
    //                 ->extraAttributes([
    //                     'id' => 'map-container',
    //                     'x-data' => '{}', // Ini untuk Alpine.js initialization pada MapPicker
    //                     '@location-updated.window' => '
    //                         if (!window.mapInstance) return;
    //                         mapInstance.setView([$event.detail.lat, $event.detail.lng], 18);

    //                         if (!window.mapMarker) {
    //                             window.mapMarker = L.marker([$event.detail.lat, $event.detail.lng], {
    //                                 draggable: true,
    //                                 autoPan: true,
    //                                 icon: L.divIcon({className: "custom-marker", html: "📍"}) // Marker kustom
    //                             }).addTo(mapInstance);

    //                             window.mapMarker.on("dragend", function(e) {
    //                                 const newPos = e.target.getLatLng();
    //                                 $wire.set("latitude", newPos.lat);
    //                                 $wire.set("longitude", newPos.lng);
    //                                 // Map picker expects [lat, lng] for its internal update
    //                                 // This will trigger afterStateUpdated on the Map field
    //                                 $wire.set("titik_koordinat", [newPos.lat, newPos.lng]);
    //                             });
    //                         } else {
    //                             window.mapMarker.setLatLng([$event.detail.lat, $event.detail.lng]);
    //                         }
    //                         const accuracy = $event.detail.accuracy ? `Akurasi: ~${Math.round($event.detail.accuracy)} meter` : "";
    //                         window.mapMarker.bindPopup(`Lokasi pekerjaan<br>${accuracy}`).openPopup();',
    //                     // Tangani saat inputan lat/lng diubah manual
    //                     '@input-updated.window' => '
    //                         if (!window.mapInstance) return;
    //                         const lat = $event.detail.lat;
    //                         const lng = $event.detail.lng;

    //                         if (!lat || !lng) return;
    //                         mapInstance.setView([lat, lng], 18);

    //                         if (!window.mapMarker) {
    //                             window.mapMarker = L.marker([lat, lng], {
    //                                 draggable: true,
    //                                 autoPan: true,
    //                                 icon: L.divIcon({className: "custom-marker", html: "📍"})
    //                             }).addTo(mapInstance);

    //                             window.mapMarker.on("dragend", function(e) {
    //                                 const newPos = e.target.getLatLng();
    //                                 $wire.set("latitude", newPos.lat);
    //                                 $wire.set("longitude", newPos.lng);
    //                                 $wire.set("titik_koordinat", [newPos.lat, newPos.lng]);
    //                             });
    //                         } else {
    //                            window.mapMarker.setLatLng([lat, lng]);
    //                         }',
    //                 ]),

    //             Grid::make(2)
    //                 ->schema([
    //                     TextInput::make('latitude')
    //                         ->label('Latitude')
    //                         ->numeric()
    //                         ->required()
    //                         ->live()
    //                         // Gunakan $set->dispatch() untuk mengirim event ke frontend dengan Livewire
    //                         ->afterStateUpdated(function ($state, Set $set, Get $get) {
    //                             if (is_numeric($state) && is_numeric($get('longitude'))) {
    //                                 $set('titik_koordinat', [(float)$state, (float)$get('longitude')]);
    //                                 // Dispatch event agar map update marker
    //                                 $set->dispatch('input-updated', ['lat' => (float)$state, 'lng' => (float)$get('longitude')]);
    //                             }
    //                         })
    //                         ->extraAttributes(['id' => 'latitude-field']),

    //                     TextInput::make('longitude')
    //                         ->label('Longitude')
    //                         ->numeric()
    //                         ->required()
    //                         ->live()
    //                         ->afterStateUpdated(function ($state, Set $set, Get $get) {
    //                             if (is_numeric($get('latitude')) && is_numeric($state)) {
    //                                 $set('titik_koordinat', [(float)$get('latitude'), (float)$state]);
    //                                 // Dispatch event agar map update marker
    //                                 $set->dispatch('input-updated', ['lat' => (float)$get('latitude'), 'lng' => (float)$state]);
    //                             }
    //                         })
    //                         ->extraAttributes(['id' => 'longitude-field']),
    //                 ]),

    //             Actions::make([
    //                 Action::make('get_location')
    //                     ->label('Dapatkan Lokasi Saya Sekarang')
    //                     ->icon('heroicon-o-map-pin')
    //                     ->action(function ($livewire) {
    //                         $livewire->dispatch('get-live-location');
    //                     })
    //                     ->extraAttributes([
    //                         'class' => 'cursor-pointer w-full justify-center',
    //                         'x-data' => 'geolocation', // Alpine.js component initialization
    //                         'data-geolocation-button' => true // Custom attribute for JS to find this button
    //                     ]),
    //             ]),

    //             // View untuk menampilkan error lokasi
    //             // Pastikan ada file `resources/views/filament/components/location-error.blade.php`
    //             // yang mungkin hanya berisi `<div x-text="errorMessage"></div>` atau sejenisnya.
    //             View::make('filament.components.location-error')
    //                 ->extraAttributes([
    //                     'id' => 'location-error', // ID untuk ditargetkan oleh JS
    //                     'class' => 'text-sm text-red-600 mt-2'
    //                 ]),
    //         ])
    //         ->columns(1) // Atau Grid::make(1) jika ini hanya satu kolom besar
    //     ;
    // }

    // Versi Original
    // protected static function titikKoordinatSection(): Section
    // {
    //     return Section::make('Lokasi Pekerjaan')
    //         ->schema([
    //             Map::make('titik_koordinat')
    //                 ->label('Peta Lokasi Pekerjaan')
    //                 ->lazy()
    //                 ->defaultLocation(0.510440, 101.438309)
    //                 ->required()
    //                 ->columnSpanFull()
    //                 ->live()
    //                 ->afterStateUpdated(function ($state, $set) {
    //                     if (!is_array($state)) return;

    //                     $lat = $state['lat'] ?? $state[0] ?? null;
    //                     $lng = $state['lng'] ?? $state[1] ?? null;

    //                     if ($lat !== null && $lng !== null) {
    //                         $set('latitude', $lat);
    //                         $set('longitude', $lng);
    //                         $set('titik_koordinat', ['lat' => $lat, 'lng' => $lng]);
    //                     }
    //                 })
    //                 ->extraAttributes([
    //                     'id' => 'map-container',
    //                     'x-data' => '{}',
    //                     '@location-updated.window' => '
    //                                     if (!window.mapInstance) return;
    //                                         mapInstance.setView([$event.detail.lat, $event.detail.lng], 18);

    //                                         if (!window.mapMarker) {
    //                                             window.mapMarker = L.marker([$event.detail.lat, $event.detail.lng], {
    //                                             draggable: true,
    //                                             autoPan: true,
    //                                             icon: L.divIcon({className: "custom-marker", html: "📍"}) // Marker kustom
    //                                         }).addTo(mapInstance);

    //                                         window.mapMarker.on("dragend", function(e) {
    //                                             const newPos = e.target.getLatLng();
    //                                             $wire.set("latitude", newPos.lat);
    //                                             $wire.set("longitude", newPos.lng);
    //                                             $wire.set("titik_koordinat", [newPos.lat, newPos.lng]);
    //                                         });
    //                                     } else {
    //                                         window.mapMarker.setLatLng([$event.detail.lat, $event.detail.lng]);
    //                                     }
    //                                         // Update popup dengan info akurasi jika ada
    //                                     const accuracy = $event.detail.accuracy ? `Akurasi: ~${Math.round($event.detail.accuracy)} meter` : "";
    //                                     window.mapMarker.bindPopup(`Lokasi pekerjaan<br>${accuracy}`).openPopup();',

    //                     // Tangani saat inputan lat/lng diubah manual
    //                     '@input-updated.window' => '
    //                                     if (!window.mapInstance) return;
    //                                     const lat = $event.detail.lat;
    //                                     const lng = $event.detail.lng;

    //                                     if (!lat || !lng) return;
    //                                     mapInstance.setView([lat, lng], 18);

    //                                     if (!window.mapMarker) {
    //                                         window.mapMarker = L.marker([lat, lng], {
    //                                         draggable: true,
    //                                         autoPan: true,
    //                                         icon: L.divIcon({className: "custom-marker", html: "📍"})
    //                                         }).addTo(mapInstance);

    //                                         window.mapMarker.on("dragend", function(e) {
    //                                         const newPos = e.target.getLatLng();
    //                                         $wire.set("latitude", newPos.lat);
    //                                         $wire.set("longitude", newPos.lng);
    //                                         $wire.set("titik_koordinat", [newPos.lat, newPos.lng]);
    //                                     });

    //                                     } else {
    //                                      window.mapMarker.setLatLng([lat, lng]);
    //                                     }',
    //                 ]),

    //             Grid::make(2)
    //                 ->schema([
    //                     TextInput::make('latitude')
    //                         ->label('Latitude')
    //                         ->numeric()
    //                         ->required()
    //                         ->live()
    //                         ->afterStateUpdated(function ($state, $set, $get) {
    //                             if (is_numeric($state) && is_numeric($get('longitude'))) {
    //                                 $set('titik_koordinat', [$state, $get('longitude')]);

    //                                 // Dispatch even agar map update marker
    //                                 echo <<<SCRIPT
    //                                     <script>
    //                                         window.dispatchEvent(new CustomEvent('input-updated', {
    //                                             detail: { lat: {$state}, lng: {$get('longitude')} }
    //                                         }));
    //                                     </script>
    //                                 SCRIPT;
    //                             }
    //                         })
    //                         ->extraAttributes(['id' => 'latitude-field']),

    //                     TextInput::make('longitude')
    //                         ->label('Longitude')
    //                         ->numeric()
    //                         ->required()
    //                         ->live()
    //                         ->afterStateUpdated(function ($state, $set, $get) {
    //                             if (is_numeric($get('latitude')) && is_numeric($state)) {
    //                                 $set('titik_koordinat', [$get('latitude'), $state]);

    //                                 echo <<<SCRIPT
    //                                 <script>
    //                                     window.dispatchEvent(new CustomEvent('input-updated', {
    //                                         detail: { lat: {$get('latitude')}, lng: {$state} }
    //                                     }));
    //                                 </script>
    //                             SCRIPT;
    //                             }
    //                         })
    //                         ->extraAttributes(['id' => 'longitude-field']),
    //                 ]),

    //             Actions::make([
    //                 Action::make('get_location')
    //                     ->label('Dapatkan Lokasi Saya Sekarang')
    //                     ->icon('heroicon-o-map-pin')
    //                     ->action(function ($livewire) {
    //                         $livewire->dispatch('get-live-location');
    //                     })
    //                     ->extraAttributes([
    //                         'class' => 'cursor-pointer w-full justify-center'
    //                     ]),
    //             ]),

    //             View::make('filament.components.location-error')
    //                 ->extraAttributes([
    //                     'id' => 'location-error',
    //                     'class' => 'text-sm text-red-600 mt-2'
    //                 ]),
    //         ])
    //     ;
    // }

    protected static function pengawasanSection(): Section
    {
        // return Section::make('Pengawasan Pekerjaan')
        //     ->schema([
        //         // Tanggal Pemasangan
        //         Grid::make(2)
        //             ->schema([
        //                 DatePicker::make('start_date')
        //                     ->label('Tanggal Mulai')
        //                     ->required(),

        //                 DatePicker::make('end_date')
        //                     ->label('Tanggal Selesai')
        //                     ->required(),
        //             ]),
        //         // Waktu Pemasangan
        //         Grid::make(3)
        //             ->schema([
        //                 Placeholder::make('waktu_pemasangan_label')
        //                     ->content('Waktu Pemasangan:')
        //                     ->disableLabel()
        //                     ->extraAttributes(['class' => 'font-bold'])
        //                     ->columnSpanFull(),

        //                 TimePicker::make('waktu_pemasangan.tiba_di_lokasi')
        //                     ->label('Tiba di Lokasi')
        //                     ->seconds(false),

        //                 TimePicker::make('waktu_pemasangan.mulai_kerja')
        //                     ->label('Mulai Kerja')
        //                     ->seconds(false),

        //                 TimePicker::make('waktu_pemasangan.selesai')
        //                     ->label('Selesai')
        //                     ->seconds(false),
        //             ]),

        //         // Peralatan Kerja
        //         self::pengawasanChecklist(
        //             'peralatan_kerja',
        //             'Peralatan Kerja',
        //             'Catatan Peralatan Kerja'
        //         ),

        //         // Seragam Kerja
        //         self::pengawasanChecklist(
        //             'seragam_kerja',
        //             'Seragam Kerja',
        //             'Catatan Seragam Kerja'
        //         ),

        //         // Peralatan K2
        //         self::pengawasanChecklist(
        //             'peralatan_k2',
        //             'Peralatan K2',
        //             'Catatan Peralatan K2'
        //         ),

        //         // Label Timah
        //         self::pengawasanChecklist(
        //             'label_timah',
        //             'Label Timah/Penang',
        //             'Catatan Label Timah'
        //         ),

        //         // Catatan Pekerjaan
        //         Textarea::make('catatan_pekerjaan')
        //             ->label('Catatan Pekerjaan')
        //             ->placeholder('..........')
        //             ->columnSpanFull()
        //             ->rows(3),
        //     ]);

        return Section::make('Pengawasan Pekerjaan')
            ->schema([
                // Gabungkan semua field tanggal dan waktu dalam satu Grid Responsif
                Grid::make(['default' => 1, 'md' => 3, 'lg' => 5]) // 1 kolom di mobile, 3 di tablet, 5 di desktop
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),

                        TimePicker::make('waktu_pemasangan.tiba_di_lokasi')
                            ->label('Tiba di Lokasi')
                            ->seconds(false),

                        TimePicker::make('waktu_pemasangan.mulai_kerja')
                            ->label('Mulai Kerja')
                            ->seconds(false),

                        DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required(),

                        TimePicker::make('waktu_pemasangan.selesai')
                            ->label('Selesai')
                            ->seconds(false),
                    ]),

                // Komponen lainnya tetap sama karena sudah full-width
                self::pengawasanChecklist(
                    'peralatan_kerja',
                    'Peralatan Kerja',
                    'Catatan Peralatan Kerja'
                ),
                self::pengawasanChecklist(
                    'seragam_kerja',
                    'Seragam Kerja',
                    'Catatan Seragam Kerja'
                ),
                self::pengawasanChecklist(
                    'peralatan_k2',
                    'Peralatan K2',
                    'Catatan Peralatan K2'
                ),
                self::pengawasanChecklist(
                    'label_timah',
                    'Label Timah/Penang',
                    'Catatan Label Timah'
                ),
                Textarea::make('catatan_pekerjaan')
                    ->label('Catatan Pekerjaan')
                    ->placeholder('..........')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    protected static function pengawasanChecklist(string $field, string $title, string $noteLabel): Group
    {
        return Group::make()
            ->schema([
                Placeholder::make("{$field}_label")
                    ->content($title . ':')
                    ->disableLabel()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->columnSpanFull(),

                // Radio button custom
                Fieldset::make()
                    ->schema([
                        Radio::make("{$field}_radio")
                            ->options([
                                'lengkap' => 'Lengkap/Ada',
                                'tidak_lengkap' => 'Tidak Lengkap/Tidak Ada',
                            ])
                            ->default('lengkap')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) use ($field) {
                                $set("{$field}.lengkap", $state === 'lengkap');
                                if ($state === 'lengkap') {
                                    $set("{$field}.catatan", 'Lengkap sesuai persyaratan.');
                                } else {
                                    $set("{$field}.catatan", '');
                                }
                                $set("{$field}.tidak_lengkap", $state === 'tidak_lengkap');
                            })
                            ->disableLabel()
                            ->inline()
                            ->columns(2),
                    ])
                    ->columnSpanFull()
                    ->extraAttributes(['style' => 'padding: 0; border: none;']),

                // Hidden fields untuk menyimpan nilai
                Hidden::make("{$field}.lengkap"),
                Hidden::make("{$field}.tidak_lengkap"),

                Textarea::make("{$field}.catatan")
                    ->label($noteLabel)
                    ->hidden(fn(Get $get) => $get("{$field}_radio") !== 'tidak_lengkap')
                    ->placeholder('.....')
                    ->default(function (Get $get) use ($field) {
                        if ($get("{$field}_radio") === 'lengkap') {
                            return 'Lengkap sesuai persyaratan.';
                        }
                        return null;
                    })
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
            ->previewable(true)
            ->preserveFilenames(false)
            ->disk('public')
            // ->header(fn(Get $get) => view('filament.forms.components.image-previews', ['state' => $get($name)]))
            // ->visible(fn(string $operation): bool => $operation === 'create' || $operation === 'edit')
            ->visibility('public')
            ->getUploadedFileNameForStorageUsing(
                fn(TemporaryUploadedFile $file): string => 'bap/' . Str::random(40) . '.' . $file->getClientOriginalExtension()
            );
        // ->getUploadedFileNameForStorageUsing(
        //     fn(TemporaryUploadedFile $file): string => self::optimizeAndStoreImage($file)
        // );
    }

    private static function optimizeAndStoreImage(TemporaryUploadedFile $file): string
    {
        $path = $file->getRealPath();

        // Optimize image first
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($path);

        // Generate unique filename
        $filename = 'bap/' . md5_file($path) . '.' . $file->getClientOriginalExtension();

        // Store to public disk
        Storage::disk('public')->put($filename, file_get_contents($path));

        return $filename;
    }
}
