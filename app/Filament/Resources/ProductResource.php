<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                            Forms\Components\TextInput::make('slug')
                            // i'll make it unique in product model in slug column
                                ->unique(Product::class, 'slug', ignoreRecord: true)
                                ->required()
                                ->disabled()
                                // dehydrated method ensures that even though the input is disabled (non-editable), its value will still be the part of submitted data.
                                ->dehydrated()
                                ->maxLength(255),
                            Forms\Components\MarkdownEditor::make('description')
                                ->required()
                                ->columnSpanFull()
                                // whenever i attach any file in markdown editor then it will uploaded in products directory
                                ->fileAttachmentsDirectory('products'),
                        ])->columns(2),
                    Section::make('Images')
                        ->schema([
                            Forms\Components\FileUpload::make('images')
                                ->image()
                                ->multiple()
                                ->directory('products')
                                ->maxFiles(5)
                                ->reorderable(),
                        ]),
                ])->columnSpan(2),
                Group::make()->schema([
                    Section::make('Price')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('INR'),
                        ]),
                    Section::make('Associations')
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->required()
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('brand_id')
                                ->required()
                                ->relationship('brand', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                    Section::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->required()
                                ->default(true),
                            Forms\Components\Toggle::make('in_stock')
                                ->required()
                                ->default(true),
                            Forms\Components\Toggle::make('is_featured')
                                ->required(),
                            Forms\Components\Toggle::make('on_sale')
                                ->required(),
                        ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Category')
                    ->label('Filter by category')
                    ->relationship('category', 'name'),
                SelectFilter::make('Brand')
                    ->label('Filter by brand')
                    ->relationship('brand', 'name'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
