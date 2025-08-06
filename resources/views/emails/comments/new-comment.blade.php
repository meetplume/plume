@component('mail::message')
# {{ __('New Comment Waiting for Approval') }}

{{ __('A new comment has been submitted on your blog and is waiting for your approval.') }}

**{{ __('Comment Details:') }}**
- **{{ __('Author:') }}** {{ $comment->author->name }}
- **{{ __('Post:') }}** {{ $comment->post->title }}
- **{{ __('Date:') }}** {{ $comment->created_at->format('F j, Y, g:i a') }}

**{{ __('Comment Content:') }}**
{{ $comment->content }}

@component('mail::button', ['url' => $url])
{{ __('Review Comment') }}
@endcomponent

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent
