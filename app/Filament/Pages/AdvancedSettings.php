<?php

namespace App\Filament\Pages;

use App\Enums\SiteSettings;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use App\Support\AvailableLanguages;
use Filament\Support\Icons\Heroicon;
use Rawilk\Settings\Support\Context;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Filament\Concerns\HandlesSettingsForm;

class AdvancedSettings extends Page implements HasForms
{
    use InteractsWithForms, HandlesSettingsForm;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Advanced';
    protected static ?string $slug = 'settings/advanced';
    protected static ?string $title = 'Advanced Settings';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.settings';

    protected function getSettings(): array
    {
        return [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            SiteSettings::QUEUE_CONNECTION,
        ];
    }

    protected function getSuccessMessage(): string
    {
        return __('Advanced settings saved!');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Queue Configuration')
                    ->heading(__('Queue Configuration'))
                    ->description(__('Configure how background jobs are processed.'))
                    ->icon(Heroicon::OutlinedQueueList)
                    ->aside()
                    ->schema([
                        Select::make(SiteSettings::QUEUE_CONNECTION->value)
                            ->label(__('Queue Connection'))
                            ->options([
                                'sync' => 'Sync (Process immediately)',
                                'database' => 'Database',
                                'redis' => 'Redis',
                                'sqs' => 'Amazon SQS',
                                'beanstalkd' => 'Beanstalkd',
                            ])
                            ->required()
                            ->helperText(__('Sync processes jobs immediately. Other options may require additional configuration.')),
                    ]),

                Section::make('Mail Configuration')
                    ->heading(__('Mail Configuration'))
                    ->description(__('You can configure the mail settings for your blog.'))
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->aside()
                    ->schema([
                        Select::make('mail_mailer')
                            ->label('Mail Driver')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'mailgun' => 'Mailgun',
                                'ses' => 'Amazon SES',
                                'postmark' => 'Postmark',
                                'log' => 'Log',
                                'array' => 'Array',
                            ])
                            ->required(),
                        TextInput::make('mail_host')
                            ->label('Mail Host'),
                        TextInput::make('mail_port')
                            ->label('Mail Port')
                            ->numeric(),
                        TextInput::make('mail_username')
                            ->extraInputAttributes(['autocomplete' => "new-text"])
                            ->label('Mail Username'),
                        TextInput::make('mail_password')
                            ->extraInputAttributes(['autocomplete' => "new-password"])
                            ->label('Mail Password')
                            ->revealable()
                            ->password(),
                        Select::make('mail_encryption')
                            ->label('Mail Encryption')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                '' => 'None',
                            ]),
                        TextInput::make('mail_from_address')
                            ->extraInputAttributes(['autocomplete' => "new-text"])
                            ->label('From Address')
                            ->email(),
                        TextInput::make('mail_from_name')
                            ->extraInputAttributes(['autocomplete' => "new-text"])
                            ->label('From Name'),

                        Section::make('Test Email')
                            ->heading(__('Test Email Configuration'))
                            ->description(__('Send a test email to verify your configuration.'))
                            ->schema([
                                TextInput::make('test_email')
                                    ->label(__('Test Email Address'))
                                    ->email(),
                                Action::make('sendTestEmail')
                                    ->label(__('Send Test Email'))
                                    ->button()
                                    ->color('primary')
                                    ->action(function () {
                                        $state = $this->form->getState();
                                        if (!$state['test_email']) {
                                            Notification::make()->danger()->title(__('Please enter a test email address.'))->send();
                                            return;
                                        }
                                        $this->sendTestEmail($state['test_email']);
                                    })
                            ])
                            ->columns(1)
                            ->collapsible(),
                    ]),

            ])->statePath('data');
    }

    public function sendTestEmail(string $testEmailAddress): void
    {
        try {
            $state = $this->form->getState();

            // Configure mail settings for this test
            config([
                'mail.mailer' => $state['mail_mailer'] ?? config('mail.mailer'),
                'mail.host' => $state['mail_host'] ?? config('mail.host'),
                'mail.port' => $state['mail_port'] ?? config('mail.port'),
                'mail.username' => $state['mail_username'] ?? config('mail.username'),
                'mail.password' => $state['mail_password'] ?? config('mail.password'),
                'mail.encryption' => $state['mail_encryption'] ?? config('mail.encryption'),
                'mail.from.address' => $state['mail_from_address'] ?? config('mail.from.address'),
                'mail.from.name' => $state['mail_from_name'] ?? config('mail.from.name'),
            ]);

            // Send test email
            Mail::raw('This is a test email from your blog to verify mail configuration.', function (Message $message) use ($testEmailAddress) {
                $message->to($testEmailAddress)
                    ->subject('Test Email from ' . config('app.name'));
            });

            Notification::make()
                ->success()
                ->title(__('Test email sent!'))
                ->body(__('A test email has been sent to ') . $testEmailAddress)
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('Failed to send test email'))
                ->body($e->getMessage())
                ->send();
        }
    }
}
