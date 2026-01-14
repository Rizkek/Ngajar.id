<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriResource\Pages;
use App\Models\Materi;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Materi';
    protected static ?string $navigationGroup = 'Pembelajaran';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Materi')
                    ->schema([
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->options(Kelas::aktif()->pluck('judul', 'kelas_id'))
                            ->searchable()
                            ->required()
                            ->preload(),

                        Forms\Components\TextInput::make('judul')
                            ->label('Judul Materi')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('tipe')
                            ->label('Tipe Materi')
                            ->options([
                                'video' => 'Video',
                                'pdf' => 'PDF',
                                'soal' => 'Soal',
                            ])
                            ->required()
                            ->default('pdf'),

                        Forms\Components\FileUpload::make('file_url')
                            ->label('Upload File')
                            ->directory('materi')
                            ->acceptedFileTypes(['application/pdf', 'video/*'])
                            ->maxSize(102400) // 100MB
                            ->columnSpanFull()
                            ->helperText('Upload file PDF atau Video. Max: 100MB'),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('materi_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Materi')
                    ->sortable()
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('kelas.judul')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('tipe')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'video',
                        'success' => 'pdf',
                        'warning' => 'soal',
                    ])
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
                        'video' => 'Video',
                        'pdf' => 'PDF',
                        'soal' => 'Soal',
                    ]),

                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(Kelas::pluck('judul', 'kelas_id'))
                    ->searchable(),
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
            'index' => Pages\ListMateris::route('/'),
            'create' => Pages\CreateMateri::route('/create'),
            'edit' => Pages\EditMateri::route('/{record}/edit'),
        ];
    }
}
