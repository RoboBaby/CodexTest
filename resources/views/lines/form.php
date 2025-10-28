<?php $title = 'Prompt Line'; include __DIR__ . '/../partials/header.php'; ?>

<h2><?= htmlspecialchars($version['prompt_name'], ENT_QUOTES) ?> — <?= htmlspecialchars($version['version_label'], ENT_QUOTES) ?></h2>

<form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES) ?>">
    <label for="section_id">Section</label>
    <select id="section_id" name="section_id" required>
        <option value="">Select a section</option>
        <?php foreach ($sections as $section): ?>
            <option value="<?= $section['id'] ?>" <?= ((int) ($line['section_id'] ?? 0) === (int) $section['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($section['title'] ?? $section['key'], ENT_QUOTES) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($errors['section_id'])): ?><div class="error"><?= htmlspecialchars($errors['section_id'], ENT_QUOTES) ?></div><?php endif; ?>

    <label for="order_index">Order Index (optional)</label>
    <input type="number" id="order_index" name="order_index" value="<?= htmlspecialchars($line['order_index'] ?? '', ENT_QUOTES) ?>" min="0">
    <?php if (!empty($errors['order_index'])): ?><div class="error"><?= htmlspecialchars($errors['order_index'], ENT_QUOTES) ?></div><?php endif; ?>

    <label for="content">Content</label>
    <textarea id="content" name="content" rows="6" required><?= htmlspecialchars($line['content'] ?? '', ENT_QUOTES) ?></textarea>
    <?php if (!empty($errors['content'])): ?><div class="error"><?= htmlspecialchars($errors['content'], ENT_QUOTES) ?></div><?php endif; ?>

    <label><input type="checkbox" name="enabled" value="1" <?= (($line['enabled'] ?? 1) == 1) ? 'checked' : '' ?>> Enabled</label>

    <button type="submit">Save Line</button>
    <a href="/versions/<?= $version['id'] ?>"><button type="button" class="secondary">Cancel</button></a>
</form>

<?php include __DIR__ . '/../partials/footer.php'; ?>
