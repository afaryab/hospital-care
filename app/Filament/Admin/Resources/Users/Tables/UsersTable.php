<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_img_path')
                    ->label('Avatar')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl('data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50"><circle fill="#e5e7eb" cx="25" cy="25" r="25"/><text x="25" y="30" text-anchor="middle" fill="white" font-size="16" font-family="Arial, sans-serif">👤</text></svg>'))
                    ->toggleable(),
                    
                TextColumn::make('name')
                    ->label('User Details')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => view('filament.tables.user-details', [
                        'user' => $record
                    ]))
                    ->html(),
                    
                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Blocked')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        1, true => 'success',
                        0, false => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('login_info')
                    ->label('Login Activity')
                    ->formatStateUsing(fn ($record) => view('filament.tables.login-activity', [
                        'record' => $record
                    ]))
                    ->html()
                    ->toggleable(),
                    
                TextColumn::make('security')
                    ->label('Security')
                    ->formatStateUsing(fn ($record) => view('filament.tables.user-security', [
                        'record' => $record
                    ]))
                    ->html()
                    ->toggleable(),
                    
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Account Status')
                    ->placeholder('All users')
                    ->trueLabel('Active users')
                    ->falseLabel('Blocked users'),
                    
                TernaryFilter::make('email_verified_at')
                    ->label('Email Verification')
                    ->placeholder('All users')
                    ->trueLabel('Verified emails')
                    ->falseLabel('Unverified emails')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                    
                TernaryFilter::make('two_factor_confirmed_at')
                    ->label('Two Factor Auth')
                    ->placeholder('All users')
                    ->trueLabel('2FA Enabled')
                    ->falseLabel('2FA Disabled')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('two_factor_confirmed_at'),
                        false: fn (Builder $query) => $query->whereNull('two_factor_confirmed_at'),
                    ),
            ])
            ->actions([
                Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->is_active ? 'Block User' : 'Activate User')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => ($record->is_active ? 'Block' : 'Activate') . ' User: ' . $record->name)
                    ->modalDescription(fn ($record) => $record->is_active 
                        ? 'Are you sure you want to block this user? They will not be able to log in.'
                        : 'Are you sure you want to activate this user? They will be able to log in.'
                    )
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active]))
                    ->successNotificationTitle(fn ($record) => 'User has been ' . ($record->is_active ? 'activated' : 'blocked')),
                    
                Actions\ViewAction::make()
                    ->color('info'),
                    
                Actions\EditAction::make()
                    ->color('warning'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\Action::make('activate_users')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Activate Selected Users')
                        ->modalDescription('Are you sure you want to activate all selected users?')
                        ->accessSelectedRecords()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => true])))
                        ->successNotificationTitle('Selected users have been activated'),
                        
                    Actions\Action::make('block_users')
                        ->label('Block Selected')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Block Selected Users')
                        ->modalDescription('Are you sure you want to block all selected users? They will not be able to log in.')
                        ->accessSelectedRecords()
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['is_active' => false])))
                        ->successNotificationTitle('Selected users have been blocked'),
                        
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
