<h1>Workspace Invitation</h1>

<p>
    You have been invited to join:
    <strong>{{ $invitation->workspace->name }}</strong>
</p>

<p>
    Role:
    <strong>{{ $invitation->role }}</strong>
</p>

<a href="{{ config('app.frontend_url') }}/invitations/{{ $invitation->token }}">
    Accept invitation
</a>