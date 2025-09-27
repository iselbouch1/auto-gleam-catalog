<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Produits';
    protected static ?string $modelLabel = 'Produit';
    protected static ?string $pluralModelLabel = 'Produits';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informations générales')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $context, $state, Forms\Set $set) => 
                                        $context === 'create' ? $set('slug', \Str::slug($state)) : null
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->rules(['alpha_dash']),

                                Forms\Components\Textarea::make('short_description')
                                    ->label('Description courte')
                                    ->rows(3)
                                    ->maxLength(500),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Description détaillée')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('Organisation')
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->label('Catégories')
                                    ->multiple()
                                    ->relationship('categories', 'name')
                                    ->options(Category::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TagsInput::make('tags')
                                    ->label('Tags')
                                    ->suggestions(function () {
                                        return collect([
                                            'LED', 'RGB', 'Premium', 'Sport', 'Racing', 'Cuir', 
                                            'Carbone', 'Magnétique', 'HD', 'Bluetooth', 'USB-C'
                                        ]);
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Statut')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visible')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Coup de cœur')
                                    ->default(false),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Ordre de tri')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Section::make('Spécifications')
                            ->schema([
                                Forms\Components\KeyValue::make('specs')
                                    ->label('Caractéristiques')
                                    ->keyLabel('Propriété')
                                    ->valueLabel('Valeur'),
                            ]),

                        Forms\Components\Section::make('Images')
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                                    ->label('Images')
                                    ->collection('images')
                                    ->multiple()
                                    ->reorderable()
                                    ->image()
                                    ->imageEditor()
                                    ->maxFiles(10)
                                    ->customProperties(function (?array $state, Forms\Get $get): array {
                                        return [
                                            'alt' => $get('name') ?? 'Image produit',
                                        ];
                                    }),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('images')
                    ->label('Image')
                    ->collection('images')
                    ->conversion('thumb')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Catégories')
                    ->badge()
                    ->separator(','),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->color('gray')
                    ->separator(','),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Coup de cœur')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visible'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Coup de cœur'),
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Catégorie')
                    ->relationship('categories', 'name')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_visibility')
                    ->label(fn (Product $record) => $record->is_visible ? 'Masquer' : 'Publier')
                    ->icon(fn (Product $record) => $record->is_visible ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Product $record) => $record->is_visible ? 'warning' : 'success')
                    ->action(function (Product $record) {
                        $record->update(['is_visible' => !$record->is_visible]);
                        \Filament\Notifications\Notification::make()
                            ->title($record->is_visible ? 'Produit publié' : 'Produit masqué')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publier')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_visible' => true]);
                        }),
                    Tables\Actions\BulkAction::make('hide')
                        ->label('Masquer')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each->update(['is_visible' => false]);
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
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