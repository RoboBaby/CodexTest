<?php $title = 'Prompt Version'; include __DIR__ . '/../partials/header.php'; ?>

<form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES) ?>">
    <div class="stacked">
        <div>
            <label for="prompt_name">Prompt Name</label>
            <input type="text" id="prompt_name" name="prompt_name" value="<?= htmlspecialchars($version['prompt_name'] ?? '', ENT_QUOTES) ?>" required>
            <?php if (!empty($errors['prompt_name'])): ?><div class="error"><?= htmlspecialchars($errors['prompt_name'], ENT_QUOTES) ?></div><?php endif; ?>
        </div>
        <div>
            <label for="version_label">Version Label</label>
            <input type="text" id="version_label" name="version_label" value="<?= htmlspecialchars($version['version_label'] ?? '', ENT_QUOTES) ?>" required>
            <?php if (!empty($errors['version_label'])): ?><div class="error"><?= htmlspecialchars($errors['version_label'], ENT_QUOTES) ?></div><?php endif; ?>
        </div>
        <div>
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= $status ?>" <?= (($version['status'] ?? '') === $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['status'])): ?><div class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES) ?></div><?php endif; ?>
        </div>
    </div>
    <div>
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="4"><?= htmlspecialchars($version['notes'] ?? '', ENT_QUOTES) ?></textarea>
    </div>
    <button type="submit">Save Version</button>
    <a href="/versions"><button type="button" class="secondary">Cancel</button></a>
</form>

<?php include __DIR__ . '/../partials/footer.php'; ?>
