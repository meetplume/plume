@component('mail::message')
# New Comment Waiting for Approval

A new comment has been submitted on your blog and is waiting for your approval.

**Comment Details:**
- **Author:** {{ $comment->author->name }}
- **Post:** {{ $comment->post->title }}
- **Date:** {{ $comment->created_at->format('F j, Y, g:i a') }}

**Comment Content:**
{{ $comment->content }}

@component('mail::button', ['url' => $url])
Review Comment
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
