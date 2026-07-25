<h2>Password Reset</h2>

<p>
    Hello <?= htmlspecialchars($displayName) ?>,
</p>

<p>
    Your StudyConnect password has been reset by an administrator.
</p>

<p>
    Your temporary password is:
</p>

<p style="font-size:20px; font-weight:bold;">
    <?= htmlspecialchars($temporaryPassword) ?>
</p>

<p>
    Please sign in using this temporary password and change your password immediately when prompted.
</p>

<p>
    If you were not expecting this password reset, please contact your StudyConnect administrator.
</p>