<?php $renderer->wrap('subdir/1root', ['title' => $title]) ?>
<div class="main">
<div class="tags"><?php $renderer->yield('tags') ?></div>
<h1><?= $title ?></h1>
<div><?php $renderer->content() ?></div>
</div>
<div clas="sidebar"><?php $renderer->include('1usersidebar'); ?></div>