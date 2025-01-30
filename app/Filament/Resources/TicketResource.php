<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create New Ticket')
                    ->schema([
                        TextInput::make('ticket_number')
                            ->required(),
                        Select::make('category')
                            ->options([
                                'psb' => 'PSB',
                                'ggn' => 'GGN'
                            ]),
                        Select::make('subcategory')
                            ->options([
                                'node_b' => 'NODE-B',
                                'olo' => 'OLO'
                            ]),
                        TextArea::make('description')
                        ->label('Description'),
                        TextInput::make('status')
                            ->disabled()
                            ->placeholder('assigned'),
                        Select::make('assigned_to')
                            ->options(User::all()->mapWithKeys(function ($user) {
                                return [$user->nik => "{$user->nik} - {$user->name}"];
                            }))
                            ->label('Assigned To')
                            ->required()
                            ->placeholder('Select User'),
                        FileUpload::make('evident_image')  // Menambahkan field upload gambar
                            ->label('Evident')
                            ->disk('public')  // Pastikan disk yang digunakan sesuai
                            ->directory('evident')  // Folder untuk menyimpan gambar
                            ->preserveFilenames()  // Menjaga nama file asli
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Menentukan query berdasarkan peran pengguna
        $query = Ticket::query();

        // Jika pengguna adalah helpdesk atau super_admin, ambil semua tiket
        if ($user->hasRole('helpdesk') || $user->hasRole('super_admin')) {
            // Tidak ada filter, ambil semua tiket
        } else {
            // Jika bukan, hanya ambil tiket yang ditugaskan kepada pengguna
            $query->where('assigned_to', $user->nik);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket Number')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Category')
                    ->searchable(),
                TextColumn::make('subcategory')
                    ->label('Subcategory')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable(),
                TextColumn::make('assigned_to')
                    ->label('Assigned To')
                    ->searchable()
                    ->getStateUsing(function (Ticket $record) {
                        return $record->user ? "{$record->user->nik} - {$record->user->name}" : 'Unassigned';
                    }),
                TextColumn::make('description')
                -> label('Description'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->searchable(),
                ImageColumn::make('evident_image')  // Menggunakan ImageColumn untuk menampilkan gambar
                    ->label('Evident Image')
                    ->square(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update'),
                ...(Auth::user()->hasRole('teknisi') ? [] : [Tables\Actions\DeleteAction::make()]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTickets::route('/'),
        ];
    }
}
