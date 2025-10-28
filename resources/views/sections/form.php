<?php $title = 'Prompt Section'; include __DIR__ . '/../partials/header.php'; ?>

<form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES) ?>">
    <label for="key">Key</label>
    <input type="text" id="key" name="key" value="<?= htmlspecialchars($section['key'] ?? '', ENT_QUOTES) ?>" <?= isset($section['id']) ? 'readonly' : 'required' ?>>
    <?php if (!empty($errors['key'])): ?><div class="error"><?= htmlspecialchars($errors['key'], ENT_QUOTES) ?></div><?php endif; ?>

    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($section['title'] ?? '', ENT_QUOTES) ?>" required>
    <?php if (!empty($errors['title'])): ?><div class="error"><?= htmlspecialchars($errors['title'], ENT_QUOTES) ?></div><?php endif; ?>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($section['description'] ?? '', ENT_QUOTES) ?></textarea>

    <label for="order_index">Order Index</label>
    <input type="number" id="order_index" name="order_index" value="<?= htmlspecialchars($section['order_index'] ?? 0, ENT_QUOTES) ?>" min="0">
    <?php if (!empty($errors['order_index'])): ?><div class="error"><?= htmlspecialchars($errors['order_index'], ENT_QUOTES) ?></div><?php endif; ?>

    <label><input type="checkbox" name="enabled" value="1" <?= (($section['enabled'] ?? 1) == 1) ? 'checked' : '' ?>> Enabled</label>

    <button type="submit">Save Section</button>
    <a href="/sections"><button type="button" class="secondary">Cancel</button></a>
</form>

<?php include __DIR__ . '/../partials/footer.php'; ?>
