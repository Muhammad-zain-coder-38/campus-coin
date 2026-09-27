<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';
require_student();
$uid=(int)current_user()['user_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf($_POST['csrf_token']??null);
    $dark=(($_POST['appearance']??'dark')==='dark')?1:0;
    $size=in_array($_POST['font_size']??'', ['small','medium','large'], true)?$_POST['font_size']:'medium';
    db()->prepare('INSERT INTO user_preferences(user_id,dark_mode,font_size,currency_code) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE dark_mode=VALUES(dark_mode),font_size=VALUES(font_size)')
      ->execute([$uid,$dark,$size,DEFAULT_CURRENCY]);
    flash('success','Settings saved.');
    redirect('student/settings.php');
}
$s=db()->prepare('SELECT dark_mode,font_size,currency_code FROM user_preferences WHERE user_id=? LIMIT 1');
$s->execute([$uid]);
$p=$s->fetch();
if(!$p){$p=['dark_mode'=>1,'font_size'=>'medium','currency_code'=>DEFAULT_CURRENCY];}
$pageTitle='Settings';$activePage='settings';require __DIR__.'/../includes/app_header.php';
?>
<section class="page-head">
  <div><span class="eyebrow">ACCESSIBILITY</span><h1>Settings</h1><p>Choose the appearance and reading scale used throughout the authenticated Campus Coin experience.</p></div>
</section>
<section class="panel form-panel settings-panel">
<form class="form" method="post">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="settings-option-group">
  <span class="settings-label">Appearance</span>
  <div class="appearance-options">
    <label class="appearance-option <?=!empty($p['dark_mode'])?'selected':''?>"><input type="radio" name="appearance" value="dark" <?=!empty($p['dark_mode'])?'checked':''?>><span><strong>Dark green</strong><small>Signature Campus Coin presentation theme.</small></span></label>
    <label class="appearance-option <?=empty($p['dark_mode'])?'selected':''?>"><input type="radio" name="appearance" value="light" <?=empty($p['dark_mode'])?'checked':''?>><span><strong>White</strong><small>Light canvas with the same dark-green content system.</small></span></label>
  </div>
</div>
<label>Font size<select class="input" name="font_size"><option value="small" <?=$p['font_size']==='small'?'selected':''?>>Small</option><option value="medium" <?=$p['font_size']==='medium'?'selected':''?>>Medium</option><option value="large" <?=$p['font_size']==='large'?'selected':''?>>Large</option></select></label>
<label>Currency code<input class="input" value="<?=e($p['currency_code']??DEFAULT_CURRENCY)?>" disabled></label>
<div class="settings-preview"><span>Preview</span><strong>Campus Coin keeps your data readable and consistent.</strong><small>Changes apply to the authenticated experience after saving.</small></div>
<button class="btn btn-primary">Save settings</button>
</form>
</section>
<?php require __DIR__.'/../includes/footer.php'; ?>
