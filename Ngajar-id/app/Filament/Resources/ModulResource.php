<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModulResource\Pages;
use App\Models\Modul;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModulResource extends Resource
{
    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Modul';
    protected static ?string $navigationGroup = 'Pembelajaran';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Modul')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul Modul')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('tipe')
                            ->label('Tipe')
                            ->options([
                                'gratis' => 'Gratis',
                                'premium' => 'Premium',
                            ])
                            ->required()
                            ->default('gratis')
                            ->reactive(),

                        Forms\Components\TextInput::make('token_harga')
                            ->label('Harga (Token)')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->visible(fn(callable $get) => $get('tipe') === 'premium'),

                        Forms\Components\Select::make('dibuat_oleh')
                            ->label('Dibuat Oleh')
                            ->options(User::pluck('name', 'user_id'))
                            ->searchable()
                            ->preload(),

                        Forms\Components\FileUpload::make('file_url')
                            ->label('Upload File')
                            ->directory('modul')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(51200) // 50MB
                            ->columnSpanFull()
                            ->helperText('Upload file PDF modul. Max: 50MB'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modul_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Modul')
                    ->sortable()
                    ->searchable()
                    ->limit(40),

                Tables\Columns\BadgeColumn::make('tipe')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'gratis',
                        'warning' => 'premium',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('token_harga')
                    ->label('Harga Token')
                    ->sortable()
                    ->default(0),

                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Pembuat')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pembeli_count')
                    ->label('Pembeli')
                    ->counts('pembeli')
                    ->sortable(),

                Tables\Columns\IconColumn::make('file_url')
                    ->label('File')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'gratis' => 'Gratis',
                        'premium' => 'Premium',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListModuls::route('/'),
            'create' => Pages\CreateModul::route('/create'),
            'edit' => Pages\EditModul::route('/{record}/edit'),
        ];
    }
}
