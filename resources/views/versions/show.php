<?php $title = 'Prompt Version Details'; include __DIR__ . '/../partials/header.php'; ?>

<h2><?= htmlspecialchars($version['prompt_name'], ENT_QUOTES) ?> — <?= htmlspecialchars($version['version_label'], ENT_QUOTES) ?></h2>
<p>Status: <strong><?= htmlspecialchars($version['status'], ENT_QUOTES) ?></strong></p>

<div class="stacked">
    <form method="post" action="/versions/<?= $version['id'] ?>/status">
        <label for="status">Update Status</label>
        <select id="status" name="status">
            <?php foreach (App\Repositories\PromptVersionRepository::STATUSES as $status): ?>
                <option value="<?= $status ?>" <?= $status === $version['status'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Update Status</button>
    </form>

    <form method="post" action="/versions/<?= $version['id'] ?>/duplicate">
        <label for="version_label">Duplicate Version</label>
        <input type="text" id="version_label" name="version_label" placeholder="New version label" required>
        <button type="submit" class="secondary">Duplicate</button>
    </form>
</div>

<div class="actions" style="margin: 1rem 0;">
    <a href="/versions/<?= $version['id'] ?>/lines/create"><button>Add Prompt Line</button></a>
    <a href="/versions"><button class="secondary">Back to Versions</button></a>
</div>

<form method="get" action="/versions/<?= $version['id'] ?>" style="max-width:320px;">
    <label for="section">Filter by Section</label>
    <select id="section" name="section">
        <option value="">All sections</option>
        <?php foreach ($sections as $section): ?>
            <option value="<?= $section['id'] ?>" <?= ($selectedSection ?? null) == $section['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($section['title'] ?? $section['key'], ENT_QUOTES) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="secondary">Apply Filter</button>
</form>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
<?php endif; ?>

<?php
$currentSection = null;
foreach ($lines as $line):
    if ($currentSection !== $line['section_id']):
        if ($currentSection !== null):
            echo '</div>';
        endif;
        $currentSection = $line['section_id'];
        ?>
        <div class="section-group">
            <h3><?= htmlspecialchars($line['section_title'] ?? $line['section_key'], ENT_QUOTES) ?></h3>
            <?php if (!empty($line['section_order'])): ?>
                <p style="color:#475569;">Order: <?= (int) $line['section_order'] ?></p>
            <?php endif; ?>
        <?php
    endif;
    ?>
        <div style="border-top:1px solid #e2e8f0; padding-top:0.75rem; margin-top:0.75rem;">
            <p><strong>#<?= (int) $line['order_index'] ?></strong> <?= nl2br(htmlspecialchars($line['content'], ENT_QUOTES)) ?></p>
            <p>Status: <?= ((int) $line['enabled'] === 1) ? 'Enabled' : 'Disabled' ?></p>
            <div class="actions">
                <form method="post" action="/lines/<?= $line['id'] ?>/move">
                    <input type="hidden" name="direction" value="up">
                    <button class="secondary" type="submit">Move Up</button>
                </form>
                <form method="post" action="/lines/<?= $line['id'] ?>/move">
                    <input type="hidden" name="direction" value="down">
                    <button class="secondary" type="submit">Move Down</button>
                </form>
                <a href="/lines/<?= $line['id'] ?>/edit"><button class="secondary" type="button">Edit</button></a>
                <form method="post" action="/lines/<?= $line['id'] ?>/delete" onsubmit="return confirm('Delete this line?');">
                    <button class="secondary" type="submit">Delete</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php if ($currentSection !== null): ?>
    </div>
<?php endif; ?>

<?php if (empty($lines)): ?>
    <p>No prompt lines for this version yet.</p>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
