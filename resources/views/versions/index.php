<?php $title = 'Prompt Versions'; include __DIR__ . '/../partials/header.php'; ?>

<div class="actions" style="margin-bottom:1rem;">
    <a href="/versions/create"><button>Create Version</button></a>
</div>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Label</th>
        <th>Status</th>
        <th>Updated</th>
        <th>Notes</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($versions as $version): ?>
        <tr>
            <td><?= htmlspecialchars($version['prompt_name'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($version['version_label'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($version['status'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($version['updated_at'] ?? '', ENT_QUOTES) ?></td>
            <td><?= nl2br(htmlspecialchars($version['notes'] ?? '', ENT_QUOTES)) ?></td>
            <td>
                <div class="actions">
                    <a href="/versions/<?= $version['id'] ?>"><button>View</button></a>
                    <a href="/versions/<?= $version['id'] ?>/edit"><button class="secondary">Edit</button></a>
                    <form method="post" action="/versions/<?= $version['id'] ?>/delete" onsubmit="return confirm('Delete this version?');">
                        <button class="secondary" type="submit">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../partials/footer.php'; ?>
