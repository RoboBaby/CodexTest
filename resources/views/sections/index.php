<?php $title = 'Prompt Sections'; include __DIR__ . '/../partials/header.php'; ?>

<div class="actions" style="margin-bottom:1rem;">
    <a href="/sections/create"><button>Create Section</button></a>
</div>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<table>
    <thead>
    <tr>
        <th>Key</th>
        <th>Title</th>
        <th>Description</th>
        <th>Order</th>
        <th>Enabled</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($sections as $section): ?>
        <tr>
            <td><?= htmlspecialchars($section['key'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($section['title'] ?? '', ENT_QUOTES) ?></td>
            <td><?= nl2br(htmlspecialchars($section['description'] ?? '', ENT_QUOTES)) ?></td>
            <td><?= (int) $section['order_index'] ?></td>
            <td><?= ((int) $section['enabled'] === 1) ? 'Yes' : 'No' ?></td>
            <td>
                <a href="/sections/<?= $section['id'] ?>/edit"><button class="secondary">Edit</button></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include __DIR__ . '/../partials/footer.php'; ?>
