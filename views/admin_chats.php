<?php
$threads = Chat::threadsForAdmin();
$activeUid = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$activeSid = isset($_GET['session_id']) ? (string)$_GET['session_id'] : null;
$activeMsgs = [];
$activeName = '';
if ($activeUid || $activeSid) {
    $activeMsgs = Chat::threadMessages($activeUid, $activeSid);
    Chat::markRead($activeUid, $activeSid);
    foreach ($threads as $t) {
        if (($activeUid && (int)$t['user_id'] === $activeUid) ||
            ($activeSid && $t['session_id'] === $activeSid)) {
            $activeName = $t['name'];
            break;
        }
    }
}
?>
<h1 class="admin-h1">Чаты поддержки</h1>
<div class="admin-chats">
    <aside class="admin-chats-list">
        <div class="admin-chats-list-title">Диалоги (<?= count($threads) ?>)</div>
        <?php if (!$threads): ?>
            <div class="admin-chats-empty">Пока нет сообщений</div>
        <?php endif; ?>
        <?php foreach ($threads as $t): ?>
            <?php
            $href = '/admin/chats?';
            if ($t['user_id']) $href .= 'user_id=' . (int)$t['user_id'];
            else $href .= 'session_id=' . urlencode((string)$t['session_id']);
            $isActive = (($activeUid && (int)$t['user_id'] === $activeUid) ||
                         ($activeSid && $t['session_id'] === $activeSid));
            ?>
            <a href="<?= e($href) ?>" class="admin-chat-item <?= $isActive ? 'active' : '' ?>">
                <div class="admin-chat-item-head">
                    <span class="admin-chat-item-name"><?= e($t['name']) ?></span>
                    <?php if ($t['unread'] > 0): ?>
                        <span class="admin-badge"><?= (int)$t['unread'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="admin-chat-item-last">
                    <?= e(mb_substr($t['last_text'], 0, 60)) ?>
                </div>
            </a>
        <?php endforeach; ?>
    </aside>
    <section class="admin-chat-view">
        <?php if (!$activeUid && !$activeSid): ?>
            <div class="admin-chats-empty">Выберите диалог слева</div>
        <?php else: ?>
            <div class="admin-chat-view-head">
                <strong><?= e($activeName) ?: 'Диалог' ?></strong>
            </div>
            <div class="admin-chat-view-body">
                <?php foreach ($activeMsgs as $m): ?>
                    <div class="msg msg-<?= e($m['author']) ?>">
                        <div class="msg-author">
                            <?php
                            $authorLabel = ['user' => 'Пользователь', 'bot' => 'Бот', 'operator' => 'Оператор'][$m['author']] ?? $m['author'];
                            echo e($authorLabel);
                            ?>
                            <span class="msg-time"><?= e($m['created_at']) ?></span>
                        </div>
                        <div class="msg-text"><?= nl2br(e($m['text'])) ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$activeMsgs): ?>
                    <div class="admin-chats-empty">Сообщений нет</div>
                <?php endif; ?>
            </div>
            <form method="post" action="/admin/chats" class="admin-chat-reply">
                <?php if ($activeUid): ?>
                    <input type="hidden" name="user_id" value="<?= (int)$activeUid ?>">
                <?php else: ?>
                    <input type="hidden" name="session_id" value="<?= e((string)$activeSid) ?>">
                <?php endif; ?>
                <textarea name="text" rows="2" required placeholder="Ответ оператора..."></textarea>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        <?php endif; ?>
    </section>
</div>
