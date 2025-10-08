<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageResource\Pages;
use App\Filament\Resources\ImageResource\RelationManagers;
use App\Models\Image;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImageResource extends Resource
{
    protected static ?string $model = Image::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo'; // Icon ảnh

    protected static ?string $recordTitleAttribute = 'filename'; // Dùng 'filename' để xác định bản ghi

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('filename')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('path')->label('Image'), // Hiển thị ảnh từ cột 'path'
                Tables\Columns\ImageColumn::make('image_url')->label('Image'),
                Tables\Columns\TextColumn::make('category')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(50), // Giới hạn độ dài mô tả
                Tables\Columns\TextColumn::make('user.name')->label('Uploaded by')->sortable(), // Hiển thị tên user
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                // Có thể thêm bộ lọc nếu cần, ví dụ:
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'nature' => 'Nature',
                        'people' => 'People',
                        // Thêm các category khác nếu biết trước
                    ]),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(), // Hành động xóa từng bản ghi
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(), // Xóa nhiều bản ghi
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
            'index' => Pages\ListImages::route('/'),

        ];
    }
}
